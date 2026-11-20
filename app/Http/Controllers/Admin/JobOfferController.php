<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobOffer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class JobOfferController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorizeAdmin($request);

        $jobs = JobOffer::query()
            ->when($request->filled('search'), function ($query) use ($request): void {
                $search = $request->string('search');

                $query->where(function ($query) use ($search): void {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('company', 'like', "%{$search}%")
                        ->orWhere('location', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('admin.jobs.index', [
            'jobs' => $jobs,
        ]);
    }

    public function create(Request $request): View
    {
        $this->authorizeAdmin($request);

        return view('admin.jobs.form', [
            'job' => new JobOffer(['status' => 'active']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeAdmin($request);

        $data = $this->validatedData($request);
        $data['slug'] = $this->uniqueSlug($data['title']);
        $data['is_featured'] = $request->boolean('is_featured');

        JobOffer::query()->create($data);

        return redirect()
            ->route('admin.jobs.index')
            ->with('status', 'Offre d\'emploi ajoutee.');
    }

    public function edit(Request $request, JobOffer $jobOffer): View
    {
        $this->authorizeAdmin($request);

        return view('admin.jobs.form', [
            'job' => $jobOffer,
        ]);
    }

    public function update(Request $request, JobOffer $jobOffer): RedirectResponse
    {
        $this->authorizeAdmin($request);

        $data = $this->validatedData($request);
        $data['is_featured'] = $request->boolean('is_featured');

        if ($jobOffer->title !== $data['title']) {
            $data['slug'] = $this->uniqueSlug($data['title'], $jobOffer->id);
        }

        $jobOffer->update($data);

        return redirect()
            ->route('admin.jobs.index')
            ->with('status', 'Offre d\'emploi mise a jour.');
    }

    public function destroy(Request $request, JobOffer $jobOffer): RedirectResponse
    {
        $this->authorizeAdmin($request);

        $jobOffer->delete();

        return redirect()
            ->route('admin.jobs.index')
            ->with('status', 'Offre d\'emploi supprimee.');
    }

    private function authorizeAdmin(Request $request): void
    {
        abort_unless($request->user()?->is_admin, 403);
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedData(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'contract_type' => ['nullable', 'string', 'max:255'],
            'deadline' => ['nullable', 'date'],
            'summary' => ['required', 'string', 'max:2000'],
            'details' => ['nullable', 'string'],
            'profile' => ['nullable', 'string'],
            'apply_email' => ['nullable', 'email', 'max:255'],
            'apply_url' => ['nullable', 'url', 'max:2000'],
            'status' => ['required', 'in:active,draft,closed'],
        ]);
    }

    private function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($title) ?: 'offre-emploi';
        $slug = $baseSlug;
        $counter = 2;

        while (
            JobOffer::query()
                ->where('slug', $slug)
                ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
                ->exists()
        ) {
            $slug = $baseSlug.'-'.$counter;
            $counter++;
        }

        return $slug;
    }
}
