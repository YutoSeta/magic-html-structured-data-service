<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\UpsertStructuredDocument;
use App\Exceptions\InvalidResourceSchemaException;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpsertStructuredDocumentRequest;
use App\Http\Resources\StructuredDocumentResource;
use App\Models\StructuredDocument;
use App\Support\Problem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use JsonException;

final class StructuredDocumentController extends Controller
{
    public function index(Request $request, string $project): AnonymousResourceCollection
    {
        $documents = StructuredDocument::query()
            ->where('project_id', $project)
            ->when(
                $request->string('kind')->isNotEmpty(),
                fn ($query) => $query->where('kind', $request->string('kind')->toString()),
            )
            ->orderBy('document_key')
            ->paginate(min(100, max(1, $request->integer('per_page', 25))));

        return StructuredDocumentResource::collection($documents);
    }

    public function show(Request $request, string $project, string $document): StructuredDocumentResource|JsonResponse
    {
        $record = $this->find($project, $document);

        return $record === null
            ? Problem::response($request, 404, 'structured_document_not_found', 'The structured document was not found.')
            : new StructuredDocumentResource($record);
    }

    public function update(
        UpsertStructuredDocumentRequest $request,
        string $project,
        string $document,
        UpsertStructuredDocument $upsert,
    ): JsonResponse {
        try {
            $payload = json_decode($request->getContent(), false, 512, JSON_THROW_ON_ERROR);
            $record = $upsert->execute($project, $document, $request->validated(), $payload->schema);
        } catch (InvalidResourceSchemaException $exception) {
            return Problem::response($request, 422, 'invalid_document_value', $exception->getMessage(), $exception->errors);
        } catch (JsonException) {
            return Problem::response($request, 422, 'invalid_json', 'The request body is not valid JSON.');
        }

        return (new StructuredDocumentResource($record))->response()->setStatusCode(200);
    }

    public function destroy(Request $request, string $project, string $document): JsonResponse
    {
        $record = $this->find($project, $document);
        if ($record === null) {
            return Problem::response($request, 404, 'structured_document_not_found', 'The structured document was not found.');
        }

        $record->delete();

        return response()->json(status: 204);
    }

    private function find(string $project, string $document): ?StructuredDocument
    {
        return StructuredDocument::query()
            ->where('project_id', $project)
            ->where('document_key', $document)
            ->first();
    }
}
