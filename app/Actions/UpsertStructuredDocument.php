<?php

namespace App\Actions;

use App\Exceptions\InvalidResourceSchemaException;
use App\Models\StructuredDocument;
use Illuminate\Support\Facades\DB;
use Opis\JsonSchema\Errors\ErrorFormatter;
use Opis\JsonSchema\Validator;
use stdClass;
use Throwable;

final class UpsertStructuredDocument
{
    /** @param array<string,mixed> $attributes */
    public function execute(string $projectId, string $documentKey, array $attributes, stdClass $schema): StructuredDocument
    {
        $this->validateValue($attributes['value'], $schema);
        unset($attributes['contract_version']);
        $attributes['kind'] ??= 'generic';
        $attributes['metadata'] ??= [];

        return DB::transaction(function () use ($projectId, $documentKey, $attributes): StructuredDocument {
            $record = StructuredDocument::query()
                ->where('project_id', $projectId)
                ->where('document_key', $documentKey)
                ->lockForUpdate()
                ->first();

            if ($record === null) {
                return StructuredDocument::query()->create([
                    ...$attributes,
                    'project_id' => $projectId,
                    'document_key' => $documentKey,
                    'version' => 1,
                ]);
            }

            $record->update([...$attributes, 'version' => $record->version + 1]);

            return $record->refresh();
        });
    }

    private function validateValue(mixed $value, stdClass $schema): void
    {
        try {
            $data = json_decode(json_encode($value, JSON_THROW_ON_ERROR), false, 512, JSON_THROW_ON_ERROR);
            $result = (new Validator(null, 10, false))->validate($data, $schema);
        } catch (Throwable $exception) {
            throw new InvalidResourceSchemaException(['schema' => [$exception->getMessage()]]);
        }

        if ($result->isValid()) {
            return;
        }

        $error = $result->error();
        throw new InvalidResourceSchemaException(
            $error === null ? ['value' => ['Schema validation failed.']] : (new ErrorFormatter)->format($error),
        );
    }
}
