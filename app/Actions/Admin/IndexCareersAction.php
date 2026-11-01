<?php

namespace App\Actions\Admin;

use App\Http\Requests\Admin\IndexAdminRequest;
use App\Models\Career;

class IndexCareersAction
{
    public function execute(IndexAdminRequest $request): array
    {
        $search = trim((string) $request->validated('search', ''));

        return [
            'careers' => Career::when($search !== '', fn ($query) => $query
                ->whereRaw('LOWER(title) LIKE ?', ['%'.mb_strtolower($search).'%']))
                ->orderBy('title')->paginate(15)->withQueryString(),
            'filters' => ['search' => $search],
        ];
    }
}
