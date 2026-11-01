<?php

namespace Database\Seeders;

use App\Models\User;
use RuntimeException;

class SeedAccount
{
    public static function save(string $email, string $name, string $role, ?string $password): User
    {
        $user = User::firstOrNew(['email' => $email]);
        if (! $user->exists) {
            if (! $password) {
                throw new RuntimeException('Configure the seed password before creating '.$email.'. A seed password is required.');
            }
            $user->name = $name;

        }
        if ($password) {
            $user->password = $password;
        }
        $user->role = $role;
        $user->is_admin = in_array($role, ['superadmin', 'admin'], true);
        $user->email_verified_at ??= now();
        $user->save();

        return $user;
    }
}
