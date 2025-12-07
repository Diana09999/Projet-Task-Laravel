<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProjectApiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guest_cannot_access_project(): void
    {
        $response = $this->getJson('/api/projects');

        $response->assertStatus(401);
    }

    /** @test */
    public function authenticated_user_can_list_projects()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        Project::factory()->count(3)->for($user)->create();

        $response = $this->postJson('/api/projects/search', []);

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    /** @test */
    public function guest_cannot_create_project()
    {
        $response = $this->postJson('/api/projects/mutate', [
            'create' => [
                'title' => 'projet interdit',
                'description' => 'test',
            ]
        ]);

        $response->assertStatus(401);
    }

    /** @test */
    public function authenticated_user_can_create_project()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $payload = [
            'create' => [
                'title' => 'nouveau projet',
                'description' => 'test',
                'user_id' => $user->id,
            ]
        ];

        $response = $this->postJson('/api/projects/mutate', $payload);

        $response->assertStatus(200)
            ->assertJsonFragment(['title' => 'nouveau projet']);

        $this->assertDatabaseHas('projects', [
            'title' => 'nouveau projet',
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function user_cannot_view_another_users_project()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $project = Project::factory()->for($otherUser)->create();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/projects/search', [
            'where' => [
                'id' => $project->id
            ]
        ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function authenticated_user_can_update_their_project()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $project = Project::factory()->for($user)->create();

        $payload = [
            'update' => [
                [
                    'id' => $project->id,
                    'title' => 'titre mis a jour',
                    'description' => 'nouvelle description',
                ],
            ],
        ];

        $response = $this->putJson('/api/projects/mutate', $payload);

        $response->assertStatus(200);

        $this->assertDatabaseHas('projects', [
            'id'    => $project->id,
            'title' => 'titre mis a jour',
        ]);
    }

    /** @test */
    public function authenticated_user_can_delete_their_project()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $project = Project::factory()->for($user)->create();

        $response = $this->deleteJson("/api/projects", [
            'ids' => [$project->id]
        ]);

        $response->assertStatus(204);

        $this->assertDatabaseMissing('projects', [
            'id' => $project->id,
        ]);
    }
}
