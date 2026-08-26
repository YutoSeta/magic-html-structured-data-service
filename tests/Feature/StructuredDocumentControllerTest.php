<?php

namespace Tests\Feature;

use App\Models\StructuredDocument;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

final class StructuredDocumentControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config()->set('structured_data.service_token', 'test-token');
    }

    public function test_document_crud_is_project_scoped_and_versioned(): void
    {
        $this->withToken('test-token')->putJson('/api/v1/projects/project-one/documents/brief', $this->payload('First'))
            ->assertOk()->assertJsonPath('version', 1)->assertJsonPath('value.title', 'First');
        $this->withToken('test-token')->putJson('/api/v1/projects/project-one/documents/brief', $this->payload('Second'))
            ->assertOk()->assertJsonPath('version', 2);
        $this->withToken('test-token')->putJson('/api/v1/projects/project-one/documents/brief', $this->payload('Second'))
            ->assertOk()->assertJsonPath('version', 2);
        $this->withToken('test-token')->getJson('/api/v1/projects/project-one/documents/brief')
            ->assertOk()->assertJsonPath('value.title', 'Second');
        $this->withToken('test-token')->getJson('/api/v1/projects/project-two/documents/brief')->assertNotFound();
        $this->withToken('test-token')->deleteJson('/api/v1/projects/project-one/documents/brief')->assertNoContent();
        $this->assertDatabaseEmpty('structured_documents');
    }

    public function test_schema_validation_and_authentication_are_enforced(): void
    {
        $this->putJson('/api/v1/projects/project-one/documents/brief', $this->payload('First'))->assertUnauthorized();
        $payload = $this->payload('First');
        $payload['value']['title'] = 42;
        $this->withToken('test-token')->putJson('/api/v1/projects/project-one/documents/brief', $payload)
            ->assertUnprocessable()->assertJsonPath('type', 'invalid_document_value');
        $this->assertDatabaseEmpty('structured_documents');
    }

    public function test_list_can_filter_by_kind(): void
    {
        StructuredDocument::factory()->create(['project_id' => 'project-one', 'kind' => 'interview']);
        StructuredDocument::factory()->create(['project_id' => 'project-one', 'kind' => 'design']);
        StructuredDocument::factory()->create(['project_id' => 'project-two', 'kind' => 'interview']);
        $this->withToken('test-token')->getJson('/api/v1/projects/project-one/documents?kind=interview')
            ->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.kind', 'interview');
    }

    /** @return array<string,mixed> */
    private function payload(string $title): array
    {
        return [
            'contract_version' => '1.0',
            'name' => 'Project brief',
            'kind' => 'interview',
            'schema' => [
                'type' => 'object',
                'required' => ['title'],
                'additionalProperties' => false,
                'properties' => ['title' => ['type' => 'string']],
            ],
            'value' => ['title' => $title],
            'metadata' => ['source' => 'test'],
        ];
    }
}
