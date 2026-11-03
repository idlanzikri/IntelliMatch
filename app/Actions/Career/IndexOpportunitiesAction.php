<?php

namespace App\Actions\Career;

use App\Http\Requests\Career\ViewCareerRequest;
use App\Models\Application;
use App\Models\Vacancy;

class IndexOpportunitiesAction
{
    public function execute(ViewCareerRequest $request): array
    {
        $filters = $request->validate(['vacancy' => ['nullable', 'integer', 'min:1'], 'search' => ['nullable', 'string', 'max:180'], 'location' => ['nullable', 'string', 'max:180'], 'type' => ['nullable', 'in:full-time,part-time,contract,internship'], 'arrangement' => ['nullable', 'in:on-site,hybrid,remote']]);
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

        return ['markers' => $markers, 'vacancies' => $query->latest()->paginate(10)->withQueryString(), 'filters' => $filters, 'applied' => Application::where('user_id', $request->user()->id)->pluck('vacancy_id')];
    }
}
