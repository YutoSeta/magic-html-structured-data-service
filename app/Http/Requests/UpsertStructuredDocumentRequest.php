<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;

final class UpsertStructuredDocumentRequest extends ContractRequest
{
    /** @return array<string,array<mixed>|string> */
    public function rules(): array
    {
        return [
            'contract_version' => ['required', 'in:1.0'],
            'name' => ['required', 'string', 'min:1', 'max:255'],
            'kind' => ['sometimes', 'string', 'regex:/^[a-z][a-z0-9_-]{0,49}$/'],
            'schema' => ['present', 'array'],
            'value' => ['present'],
            'metadata' => ['sometimes', 'array'],
        ];
    }

    /** @return array<int,callable(Validator):void> */
    public function after(): array
    {
        return [
            ...parent::after(),
            function (Validator $validator): void {
                $document = json_decode($this->getContent());
                if (! is_object($document?->schema ?? null)) {
                    $validator->errors()->add('schema', 'The schema must be a JSON object.');
                }
                if (strlen((string) json_encode($this->input('schema'))) > 50000) {
                    $validator->errors()->add('schema', 'The schema may not exceed 50 KB.');
                }
                if (strlen((string) json_encode($this->input('value'))) > 1000000) {
                    $validator->errors()->add('value', 'The encoded value may not exceed 1 MB.');
                }
                if (strlen((string) json_encode($this->input('metadata', []))) > 50000) {
                    $validator->errors()->add('metadata', 'The metadata may not exceed 50 KB.');
                }
            },
        ];
    }
}
