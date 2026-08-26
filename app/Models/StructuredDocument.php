<?php

namespace App\Models;

use Database\Factories\StructuredDocumentFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class StructuredDocument extends Model
{
    /** @use HasFactory<StructuredDocumentFactory> */
    use HasFactory;

    use HasUuids;

    /** @var list<string> */
    protected $fillable = [
        'project_id',
        'document_key',
        'name',
        'kind',
        'schema',
        'value',
        'metadata',
        'version',
    ];

    /** @return array<string,string> */
    protected function casts(): array
    {
        return [
            'schema' => 'array',
            'value' => 'json',
            'metadata' => 'array',
            'version' => 'integer',
        ];
    }
}
