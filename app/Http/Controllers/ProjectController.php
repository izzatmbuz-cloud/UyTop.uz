<?php

namespace App\Http\Controllers;

use App\Enums\ModerationStatus;
use App\Models\Project;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Projects', [
            'projects' => Project::with(['district', 'media' => fn ($query) => $query->orderBy('sort_order')])
                ->withCount(['listings' => fn ($query) => $query->where('moderation_status', 'approved')->where('availability_status', 'available')->whereNull('archived_at')])
                ->where('moderation_status', ModerationStatus::APPROVED->value)
                ->latest()
                ->get(),
        ]);
    }

    public function show(Project $project): Response
    {
        abort_unless($project->moderation_status === ModerationStatus::APPROVED, 404);

        $project->load([
            'district',
            'media' => fn ($query) => $query->orderBy('sort_order'),
            'listings' => fn ($query) => $query
                ->with(['district', 'media'])
                ->where('deal_type', 'sale')
                ->where('moderation_status', 'approved')
                ->where('availability_status', 'available')
                ->whereNull('archived_at')
                ->orderBy('price'),
        ]);

        return Inertia::render('ProjectDetail', [
            'project' => $project,
            'consultationAvailable' => $project->manager_user_id !== null,
        ]);
    }
}
