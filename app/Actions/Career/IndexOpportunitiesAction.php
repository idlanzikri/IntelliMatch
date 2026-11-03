<?php

namespace App\Actions\Career;

use App\Http\Requests\Career\ViewCareerRequest;
use App\Models\Application;
use App\Models\Vacancy;

class IndexOpportunitiesAction
{
    public function execute(ViewCareerRequest $request): array
    {
        $filters = $request->validate(['search' => ['nullable', 'string', 'max:180'], 'location' => ['nullable', 'string', 'max:180'], 'type' => ['nullable', 'in:full-time,part-time,contract,internship'], 'arrangement' => ['nullable', 'in:on-site,hybrid,remote']]);
        $query = Vacancy::open()->with('company:id,name,industry');
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

        return ['vacancies' => $query->latest()->paginate(10)->withQueryString(), 'filters' => $filters, 'applied' => Application::where('user_id', $request->user()->id)->pluck('vacancy_id')];
    }
}
