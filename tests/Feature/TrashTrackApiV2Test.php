<?php

namespace Tests\Feature;

use App\Models\Report;
use App\Models\Trash;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrashTrackApiV2Test extends TestCase
{
    use RefreshDatabase;

    public function test_v2_group_routes_require_authentication(): void
    {
        $this->getJson('/api/v2/profile')->assertUnauthorized();
        $this->getJson('/api/v2/trash')->assertUnauthorized();
        $this->postJson('/api/v2/reports', [])->assertUnauthorized();
    }

    public function test_v2_trash_index_show_and_create_return_consistent_data(): void
    {
        $user = User::factory()->create();
        Trash::factory()->count(2)->create();

        $indexResponse = $this->actingWithToken($user)->getJson('/api/v2/trash');

        $indexResponse->assertOk()
            ->assertJsonStructure(['data' => [['id', 'name', 'category', 'weight']]])
            ->assertJsonCount(2, 'data');

        $trash = Trash::first();

        $showResponse = $this->actingWithToken($user)->getJson("/api/v2/trash/{$trash->id}");

        $showResponse->assertOk()
            ->assertJsonStructure(['data' => ['id', 'name', 'category', 'weight']])
            ->assertJsonPath('data.id', $trash->id);

        $createResponse = $this->actingWithToken($user)->postJson('/api/v2/trash', [
            'name' => 'Paper Waste',
            'category' => 'Organik',
            'weight' => 1.2,
        ]);

        $createResponse->assertCreated()
            ->assertJsonPath('message', 'Trash created successfully')
            ->assertJsonStructure(['message', 'data' => ['id', 'name', 'category', 'weight']]);
    }

    public function test_v2_trash_show_returns_not_found_for_missing_item(): void
    {
        $user = User::factory()->create();

        $response = $this->actingWithToken($user)->getJson('/api/v2/trash/9999');

        $response->assertNotFound()
            ->assertJsonPath('message', 'Trash not found');
    }

    public function test_v2_trash_create_validation_errors_are_returned(): void
    {
        $user = User::factory()->create();

        $response = $this->actingWithToken($user)->postJson('/api/v2/trash', [
            'name' => 'Paper Waste',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['category', 'weight']);
    }

    public function test_v2_report_index_and_show_return_simplified_data(): void
    {
        $user = User::factory()->create();
        Trash::factory()->count(2)->create();

        $report = Report::factory()->withTrash()->create([
            'user_id' => $user->id,
            'title' => 'V2 Simplified Report',
            'description' => 'Report for V2 testing',
            'status' => 'pending',
        ]);

        $indexResponse = $this->actingWithToken($user)->getJson('/api/v2/reports');

        $indexResponse->assertOk()
            ->assertJsonStructure(['data' => [['id', 'title', 'reported_at', 'reporter_name', 'description', 'trash_names', 'image']]])
            ->assertJsonPath('data.0.title', 'V2 Simplified Report');

        $showResponse = $this->actingWithToken($user)->getJson("/api/v2/reports/{$report->id}");

        $showResponse->assertOk()
            ->assertJsonStructure(['data' => ['id', 'title', 'reported_at', 'reporter_name', 'description', 'trash_names', 'image']])
            ->assertJsonPath('data.title', 'V2 Simplified Report');
    }

    public function test_v2_report_show_returns_not_found_when_missing(): void
    {
        $user = User::factory()->create();

        $response = $this->actingWithToken($user)->getJson('/api/v2/reports/9999');

        $response->assertNotFound()
            ->assertJsonPath('message', 'Report not found');
    }

    public function test_v2_report_create_update_status_and_delete(): void
    {
        $user = User::factory()->create();

        $createResponse = $this->actingWithToken($user)->postJson('/api/v2/reports', [
            'title' => 'V2 Create Test',
            'description' => 'Test V2 report creation',
        ]);

        $createResponse->assertCreated()
            ->assertJsonPath('message', 'Report created successfully')
            ->assertJsonStructure(['message', 'data' => ['id', 'title', 'reported_at', 'reporter_name', 'description', 'trash_names', 'image']]);

        $reportId = $createResponse->json('data.id');

        $this->actingWithToken($user)->putJson("/api/v2/reports/{$reportId}", [
            'title' => 'V2 Updated Title',
        ])->assertOk()
            ->assertJsonPath('message', 'Report updated successfully');

        $this->actingWithToken($user)->putJson("/api/v2/reports/{$reportId}/status", [
            'status' => 'in_progress',
        ])->assertOk()
            ->assertJsonPath('message', 'Report status updated successfully');

        $this->actingWithToken($user)->deleteJson("/api/v2/reports/{$reportId}")
            ->assertOk()
            ->assertJsonPath('message', 'Report deleted successfully');
    }

    public function test_v2_report_search_filter_and_pagination(): void
    {
        $user = User::factory()->create();
        Trash::factory()->count(3)->create();

        Report::factory()->withTrash()->createMany([
            [
                'user_id' => $user->id,
                'title' => 'Park Cleanup',
                'description' => 'Clean up event completed',
                'status' => 'completed',
            ],
            [
                'user_id' => $user->id,
                'title' => 'Illegal Dumping',
                'description' => 'Garbage left behind in the alley',
                'status' => 'pending',
            ],
        ]);

        $searchResponse = $this->actingWithToken($user)->postJson('/api/v2/reports/search', [
            'query' => 'dumping',
        ]);

        $searchResponse->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Illegal Dumping');

        $filterResponse = $this->actingWithToken($user)->postJson('/api/v2/reports/filter', [
            'status' => 'completed',
        ]);

        $filterResponse->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.status', 'completed');

        $paginationResponse = $this->actingWithToken($user)->getJson('/api/v2/reports/paginated?per_page=1');

        $paginationResponse->assertOk()
            ->assertJsonStructure([
                'data',
                'pagination' => ['total', 'per_page', 'current_page', 'last_page', 'from', 'to'],
            ])
            ->assertJsonCount(1, 'data');
    }

    public function test_v2_report_trash_names_endpoint_returns_report_trash_list(): void
    {
        $user = User::factory()->create();
        Trash::factory()->count(2)->create();

        $report = Report::factory()->withTrash()->create([
            'user_id' => $user->id,
            'title' => 'Report with Trash Names',
            'description' => 'Detailed trash list should appear',
        ]);

        $response = $this->actingWithToken($user)->getJson("/api/v2/reports/{$report->id}/trash");

        $response->assertOk()
            ->assertJsonStructure(['data' => ['report_id', 'trash_names']])
            ->assertJsonPath('data.report_id', $report->id);
    }
}
