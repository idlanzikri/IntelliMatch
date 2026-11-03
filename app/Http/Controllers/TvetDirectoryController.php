<?php

namespace App\Http\Controllers;

use App\Models\Agency;
use App\Models\Institution;
use Illuminate\Http\Request;

class TvetDirectoryController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate(['search' => 'nullable|string|max:180', 'agency' => 'nullable|integer|exists:agencies,id', 'state' => 'nullable|string|max:100']);
        $query = Institution::whereNotNull('agency_id')->with('agency:id,name')->withCount('courses');
        if ($search = $filters['search'] ?? '') {
            $query->where(fn ($q) => $q->whereLike('name', '%'.$search.'%')->orWhereHas('courses', fn ($c) => $c->whereLike('name', '%'.$search.'%')));
        }
        if ($agency = $filters['agency'] ?? null) {
            $query->where('agency_id', $agency);
        }
        if ($state = $filters['state'] ?? '') {
            $query->where('state', $state);
        }
        $markers = (clone $query)->whereNotNull('latitude')->whereNotNull('longitude')->get(['id', 'name', 'latitude', 'longitude']);

        return inertia('student/Training', ['institutions' => $query->orderBy('name')->paginate(12)->withQueryString(), 'markers' => $markers, 'agencies' => Agency::orderBy('name')->get(['id', 'name']), 'states' => Institution::whereNotNull('state')->distinct()->orderBy('state')->pluck('state'), 'filters' => $filters, 'canManage' => (bool) $request->user()->is_admin]);
    }

    public function show(Institution $institution)
    {
        abort_if($institution->agency_id === null, 404);

        return inertia('student/TrainingDetail', ['institution' => $institution->load(['agency', 'courses' => fn ($q) => $q->orderBy('name')])]);
    }

    public function location(Request $request, Institution $institution)
    {
        abort_unless($request->user()->is_admin, 403);
        $institution->update($request->validate(['latitude' => 'required|numeric|between:-90,90', 'longitude' => 'required|numeric|between:-180,180']));
        $institution->forceFill(['location_is_approximate' => false])->save();

        return back();
    }
}
