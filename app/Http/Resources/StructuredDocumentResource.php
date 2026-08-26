<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class StructuredDocumentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'contract_version' => '1.0',
            'id' => $this->id,
            'project_id' => $this->project_id,
            'document_key' => $this->document_key,
            'name' => $this->name,
            'kind' => $this->kind,
            'schema' => $this->schema,
            'value' => $this->value,
            'metadata' => $this->metadata,
            'version' => $this->version,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
