<?php

namespace App\Http\Controllers;

use App\Models\JobOffer;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JobOfferController extends Controller
{
    public function index(Request $request): View
    {
        $jobs = JobOffer::query()
            ->active()
            ->when($request->filled('search'), function ($query) use ($request): void {
                $search = $request->string('search');

                $query->where(function ($query) use ($search): void {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('company', 'like', "%{$search}%")
                        ->orWhere('summary', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('location'), fn ($query) => $query->where('location', 'like', '%'.$request->string('location').'%'))
            ->orderByRaw('CASE WHEN deadline IS NULL THEN 1 ELSE 0 END')
            ->orderBy('deadline')
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('jobs.index', [
            'jobs' => $jobs,
            'jobCount' => JobOffer::query()->active()->count(),
        ]);
    }

    public function show(JobOffer $jobOffer): View
    {
        abort_unless($jobOffer->status === 'active', 404);

        $relatedJobs = JobOffer::query()
            ->active()
            ->whereKeyNot($jobOffer->id)
            ->when($jobOffer->location, fn ($query) => $query->where('location', $jobOffer->location))
            ->latest()
            ->limit(3)
            ->get();

        if ($relatedJobs->isEmpty()) {
            $relatedJobs = JobOffer::query()
                ->active()
                ->whereKeyNot($jobOffer->id)
                ->latest()
                ->limit(3)
                ->get();
        }

        return view('jobs.show', [
            'job' => $jobOffer,
            'relatedJobs' => $relatedJobs,
        ]);
    }
}
