<?php

namespace Tests\Feature;

use App\Models\Report;
use App\Models\Trash;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrashTrackApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_successfully(): void
    {
        $payload = [
            'name' => 'Trash Test User',
            'email' => 'trashtrack@example.com',
            'password' => 'password',
        ];

        $response = $this->postJson('/api/register', $payload);

        $response->assertCreated()
            ->assertJsonStructure(['message', 'user' => ['id', 'name', 'email']])
            ->assertJsonPath('user.email', 'trashtrack@example.com');

        $this->assertDatabaseHas('users', [
            'email' => 'trashtrack@example.com',
            'name' => 'Trash Test User',
        ]);
    }

    public function test_register_validation_errors_are_returned(): void
    {
        $response = $this->postJson('/api/register', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertOk()
            ->assertJsonStructure(['message', 'token', 'user' => ['id', 'name', 'email']])
            ->assertJsonPath('user.email', $user->email);
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        User::factory()->create();

        $response = $this->postJson('/api/login', [
            'email' => 'wrong@example.com',
            'password' => 'invalid-password',
        ]);

        $response->assertUnauthorized()
            ->assertJsonPath('message', 'Invalid email or password');
    }

    public function test_authenticated_user_can_view_profile(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'api')->getJson('/api/v1/profile');

        $response->assertOk()
            ->assertJsonStructure(['user' => ['id', 'name', 'email']])
            ->assertJsonPath('user.email', $user->email);
    }

    public function test_user_can_logout_and_token_is_invalidated(): void
    {
        $user = User::factory()->create();

        $loginResponse = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $token = $loginResponse->json('token');

        $logoutResponse = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/v1/logout');

        $logoutResponse->assertOk()
            ->assertJsonPath('message', 'User logged out successfully');

        $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/profile')
            ->assertUnauthorized();
    }

    public function test_v1_group_routes_require_authentication(): void
    {
        $this->getJson('/api/v1/profile')->assertUnauthorized();
        $this->getJson('/api/v1/trash')->assertUnauthorized();
        $this->postJson('/api/v1/reports', [])->assertUnauthorized();
    }

    public function test_authenticated_user_can_list_and_create_trash(): void
    {
        $user = User::factory()->create();
        Trash::factory()->count(2)->create();

        $listResponse = $this->actingAs($user, 'api')->getJson('/api/v1/trash');

        $listResponse->assertOk()
            ->assertJsonCount(2)
            ->assertJsonStructure([
                ['id', 'name', 'category', 'weight', 'created_at', 'updated_at'],
            ]);

        $payload = [
            'name' => 'Plastic Bottle',
            'category' => 'Anorganik',
            'weight' => 2.5,
        ];

        $createResponse = $this->actingAs($user, 'api')->postJson('/api/v1/trash', $payload);

        $createResponse->assertCreated()
            ->assertJsonPath('name', 'Plastic Bottle')
            ->assertJsonPath('category', 'Anorganik')
            ->assertJsonPath('weight', 2.5);

        $this->assertDatabaseHas('trashes', [
            'name' => 'Plastic Bottle',
            'category' => 'Anorganik',
        ]);
    }

    public function test_authenticated_user_can_perform_report_crud_search_and_filter(): void
    {
        $user = User::factory()->create();

        $createResponse = $this->actingAs($user, 'api')->postJson('/api/v1/reports', [
            'title' => 'Illegal dumping near park',
            'description' => 'A pile of waste is left near the community park.',
            'status' => 'pending',
        ]);

        $createResponse->assertCreated()
            ->assertJsonPath('report.title', 'Illegal dumping near park')
            ->assertJsonPath('report.status', 'pending');

        $reportId = $createResponse->json('report.id');

        $indexResponse = $this->actingAs($user, 'api')->getJson('/api/v1/reports');

        $indexResponse->assertOk()
            ->assertJsonStructure([['id', 'user_id', 'title', 'description', 'status', 'created_at', 'updated_at']]);

        $showResponse = $this->actingAs($user, 'api')->getJson("/api/v1/reports/{$reportId}");

        $showResponse->assertOk()
            ->assertJsonPath('id', $reportId)
            ->assertJsonPath('title', 'Illegal dumping near park');

        $this->actingAs($user, 'api')->putJson("/api/v1/reports/{$reportId}", [
            'title' => 'Illegal dumping fixed',
            'description' => 'Update report description after cleanup.',
        ])->assertOk()
            ->assertJsonPath('message', 'Report updated successfully');

        $this->actingAs($user, 'api')->putJson("/api/v1/reports/{$reportId}/status", [
            'status' => 'in_progress',
        ])->assertOk()
            ->assertJsonPath('message', 'Report status updated successfully');

        $searchResponse = $this->actingAs($user, 'api')->postJson('/api/v1/reports/search', [
            'query' => 'dumping',
        ]);

        $searchResponse->assertOk()
            ->assertJsonCount(1, 'results')
            ->assertJsonPath('results.0.id', $reportId);

        $filterResponse = $this->actingAs($user, 'api')->postJson('/api/v1/reports/filter', [
            'status' => 'in_progress',
        ]);

        $filterResponse->assertOk()
            ->assertJsonCount(1, 'reports')
            ->assertJsonPath('reports.0.id', $reportId);

        $this->actingAs($user, 'api')->deleteJson("/api/v1/reports/{$reportId}")
            ->assertOk()
            ->assertJsonPath('message', 'Report deleted successfully');
    }

    public function test_report_endpoints_return_404_for_nonexistent_ids(): void
    {
        $user = User::factory()->create();
        $missingId = 9999;

        $this->actingAs($user, 'api')->getJson("/api/v1/reports/{$missingId}")
            ->assertNotFound()
            ->assertJsonPath('message', 'Report not found');

        $this->actingAs($user, 'api')->putJson("/api/v1/reports/{$missingId}", [
            'title' => 'No report',
        ])->assertNotFound()
            ->assertJsonPath('message', 'Report not found');

        $this->actingAs($user, 'api')->putJson("/api/v1/reports/{$missingId}/status", [
            'status' => 'completed',
        ])->assertNotFound()
            ->assertJsonPath('message', 'Report not found');

        $this->actingAs($user, 'api')->deleteJson("/api/v1/reports/{$missingId}")
            ->assertNotFound()
            ->assertJsonPath('message', 'Report not found');
    }
}
