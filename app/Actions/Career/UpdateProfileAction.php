<?php

namespace App\Actions\Career;

use App\Http\Requests\Career\UpdateProfileRequest;
use App\Models\StudentProfile;

class UpdateProfileAction
{
    public function execute(UpdateProfileRequest $request): void
    {
        $profile = StudentProfile::firstOrNew(['user_id' => $request->user()->id]);
        $profile->user_id = $request->user()->id;
        $profile->fill($request->validated());
        $profile->save();
    }
}
