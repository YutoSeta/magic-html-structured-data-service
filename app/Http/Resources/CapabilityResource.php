<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class CapabilityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => 'magic-html-structured-data-service',
            'tier' => 1,
            'contract_version' => '1.0',
            'documentation' => url('/api/__verify'),
            'health' => url('/up'),
            'operations' => [
                'GET /api/v1/projects/{project}/documents',
                'GET /api/v1/projects/{project}/documents/{document}',
                'PUT /api/v1/projects/{project}/documents/{document}',
                'DELETE /api/v1/projects/{project}/documents/{document}',
            ],
        ];
    }
}
