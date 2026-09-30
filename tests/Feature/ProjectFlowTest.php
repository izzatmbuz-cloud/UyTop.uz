<?php

namespace Tests\Feature;

use App\Models\District;
use App\Models\Project;
use App\Models\Request as RequestModel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ProjectFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_approved_project_detail_is_public(): void
    {
        $project = $this->project();

        $this->get(route('projects.show', $project))->assertOk();
    }

    public function test_user_can_send_consultation_to_assigned_manager(): void
    {
        $project = $this->project();
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('projects.requests.store', $project), [
            'name' => 'Talaba',
            'phone' => '+998 90 123 45 67',
            'message' => 'Ikki xonali kvartira haqida ma’lumot kerak.',
            'idempotency_key' => (string) Str::uuid(),
        ])->assertRedirect(route('account.requests'));

        $created = RequestModel::first();
        $this->assertSame($project->manager_user_id, $created->recipient_id);
        $this->assertSame($project->id, $created->project_id);
        $this->assertNull($created->listing_id);
        $this->assertDatabaseHas('request_events', ['request_id' => $created->id, 'to_status' => 'new']);
    }

    public function test_consultation_is_rejected_when_project_has_no_manager(): void
    {
        $project = $this->project();
        $project->update(['manager_user_id' => null]);
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('projects.requests.store', $project), [
            'name' => 'Talaba',
            'phone' => '+998 90 123 45 67',
            'idempotency_key' => (string) Str::uuid(),
        ])->assertConflict();

        $this->assertDatabaseCount('requests', 0);
    }

    public function test_unapproved_project_is_not_public(): void
    {
        $project = $this->project();
        $project->update(['moderation_status' => 'pending']);

        $this->get(route('projects.show', $project))->assertNotFound();
    }

    private function project(): Project
    {
        $manager = User::factory()->create(['role' => 'developer']);
        $district = District::create(['name_uz' => 'Andijon shahri', 'active' => true]);

        return Project::create([
            'manager_user_id' => $manager->id,
            'district_id' => $district->id,
            'name' => 'Navbahor Residence',
            'developer_name' => 'UyTop Development',
            'description' => 'Yangi turar joy majmuasi.',
            'stage' => 'building',
            'moderation_status' => 'approved',
        ]);
    }
}
