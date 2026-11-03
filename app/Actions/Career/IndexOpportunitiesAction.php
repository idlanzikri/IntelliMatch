<?php

namespace App\Actions\Career;

use App\Http\Requests\Career\ViewCareerRequest;
use App\Models\Application;
use App\Models\Institution;
use App\Models\StudentProfile;
use App\Models\Vacancy;
use App\Services\ApplicantMatch;
use App\Services\LocationDistance;
use Illuminate\Validation\Rule;

class IndexOpportunitiesAction
{
    public function execute(ViewCareerRequest $request): array
    {
        $origins = [];
        $institution = $request->user()->institution_id ? Institution::find($request->user()->institution_id) : null;
        if ($institution?->latitude !== null && $institution?->longitude !== null) {
            $origins['institution'] = ['key' => 'institution', 'label' => 'My institution: '.$institution->name, 'latitude' => $institution->latitude, 'longitude' => $institution->longitude];
        }
        foreach (config('demo_cities') as $state => $cities) {
            foreach ($cities as $index => [$city, $latitude, $longitude]) {
                $key = $state.'-'.$index;
                $origins[$key] = ['key' => $key, 'label' => $city.' ('.ucwords($state).')', 'latitude' => $latitude, 'longitude' => $longitude];
            }
        }
        $filters = $request->validate(['origin' => ['nullable', Rule::in(['none', ...array_keys($origins)])], 'pin' => ['nullable', 'integer', 'min:1'], 'vacancy' => ['nullable', 'integer', 'min:1'], 'search' => ['nullable', 'string', 'max:180'], 'location' => ['nullable', 'string', 'max:180'], 'type' => ['nullable', 'in:full-time,part-time,contract,internship'], 'arrangement' => ['nullable', 'in:on-site,hybrid,remote']]);
        $originKey = $filters['origin'] ?? (isset($origins['institution']) ? 'institution' : 'none');
        $origin = $origins[$originKey] ?? null;
        $profile = $request->user()->role === 'student' ? StudentProfile::where('user_id', $request->user()->id)->first() : null;
        $matcher = app(ApplicantMatch::class);
        $query = Vacancy::open()->with('company:id,name,industry');
        if ($id = $filters['vacancy'] ?? null) {
            $query->whereKey($id);
        }
        if ($search = $filters['search'] ?? '') {
            $query->where(fn ($q) => $q->whereLike('title', '%'.$search.'%')->orWhereLike('description', '%'.$search.'%')->orWhereHas('company', fn ($c) => $c->whereLike('name', '%'.$search.'%')));
        }
        if ($location = $filters['location'] ?? '') {
            $query->whereLike('location', '%'.$location.'%');
        }
        foreach (['type', 'arrangement'] as $field) {
            if ($value = $filters[$field] ?? '') {
                $query->where($field, $value);
            }
        }

        $mapQuery = (clone $query)->where('arrangement', '!=', 'remote')->whereNotNull('latitude')->whereNotNull('longitude');
        $markers = $mapQuery->get(['id', 'title', 'latitude', 'longitude'])->map(fn ($v) => ['id' => $v->id, 'name' => $v->title, 'latitude' => $v->latitude, 'longitude' => $v->longitude]);
        if ($id = $filters['pin'] ?? null) {
            $pin = $markers->firstWhere('id', $id);
            if ($pin) {
                $query->where('arrangement', '!=', 'remote')->where('latitude', $pin['latitude'])->where('longitude', $pin['longitude']);
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        $vacancies = $query->latest()->paginate(10)->withQueryString();
        $vacancies->through(function ($vacancy) use ($profile, $matcher, $origin) {
            $vacancy->setAttribute('match', $profile ? $matcher->score($profile, $vacancy) : null);
            $vacancy->setAttribute('distance_km', $origin && $vacancy->arrangement !== 'remote' && $vacancy->latitude !== null && $vacancy->longitude !== null
                ? LocationDistance::kilometres($origin['latitude'], $origin['longitude'], $vacancy->latitude, $vacancy->longitude) : null);

            return $vacancy;
        });

        return ['distanceOrigins' => array_values($origins), 'distanceOrigin' => $originKey, 'hasCareerProfile' => $profile !== null, 'markers' => $markers, 'vacancies' => $vacancies, 'filters' => $filters, 'applied' => Application::where('user_id', $request->user()->id)->pluck('vacancy_id')];
    }
}
