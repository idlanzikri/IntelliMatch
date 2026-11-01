<?php

namespace App\Actions\Admin;

use App\Http\Requests\Admin\SaveCareerRequest;
use App\Models\Career;

class SaveCareerAction
{
    public function execute(SaveCareerRequest $request, ?Career $career = null): Career
    {
        $career ??= new Career;
        $data = $request->validated();
        $data['skills'] = array_map('trim', $data['skills']);
        $career->fill($data)->save();

        return $career;
    }
}
