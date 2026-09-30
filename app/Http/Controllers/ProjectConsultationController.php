<?php

namespace App\Http\Controllers;

use App\Enums\ModerationStatus;
use App\Enums\RequestStatus;
use App\Models\Project;
use App\Models\Request as RequestModel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjectConsultationController extends Controller
{
    public function store(Request $request, Project $project): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30', 'regex:/^[0-9+()\-\s]{7,30}$/'],
            'message' => ['nullable', 'string', 'max:1000'],
            'idempotency_key' => ['required', 'uuid'],
        ]);

        DB::transaction(function () use ($request, $project, $validated) {
            $lockedProject = Project::query()->lockForUpdate()->with('manager')->findOrFail($project->id);
            abort_unless($lockedProject->moderation_status === ModerationStatus::APPROVED, 404);
            abort_unless($lockedProject->manager !== null, 409, 'Loyiha vakili hali tayinlanmagan.');
            abort_if($lockedProject->manager_user_id === $request->user()->id, 422, 'O‘z loyihangizga murojaat yubora olmaysiz.');

            if (RequestModel::where('idempotency_key', $validated['idempotency_key'])->exists()) {
                return;
            }

            $existing = RequestModel::query()
                ->where('requester_id', $request->user()->id)
                ->where('project_id', $lockedProject->id)
                ->whereIn('status', ['new', 'alternative_proposed', 'accepted'])
                ->lockForUpdate()
                ->first();
            abort_if($existing !== null, 409, 'Bu loyiha bo‘yicha faol murojaatingiz mavjud.');

            $created = RequestModel::create([
                ...$validated,
                'requester_id' => $request->user()->id,
                'recipient_id' => $lockedProject->manager_user_id,
                'project_id' => $lockedProject->id,
                'status' => RequestStatus::NEW,
            ]);
            $created->events()->create([
                'actor_id' => $request->user()->id,
                'from_status' => 'created',
                'to_status' => RequestStatus::NEW->value,
            ]);
        });

        return redirect()->route('account.requests')->with('success', 'Maslahat uchun murojaat yuborildi.');
    }
}
