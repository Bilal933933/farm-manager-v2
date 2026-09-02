<?php

namespace App\Actions\Auth;

use App\Actions\Company\CreateCompanyWithRolesAction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RegisterAction
{
    public function __construct(private CreateCompanyWithRolesAction $createCompanyAction) {}

    public function execute(array $data): array
    {
        $result = DB::transaction(function () use ($data) {
            $company = $this->createCompanyAction->execute($data['company_name'], $data['company_slug'] ?? null);
            $managerRole = $company->roles()->where('slug', 'manager')->firstOrFail();

            $user = User::create([
                'company_id' => $company->id,
                'role_id' => $managerRole->id,
                'name' => $data['admin_name'],
                'email' => $data['admin_email'],
                'password' => Hash::make($data['admin_password']),
                'phone' => $data['admin_phone'] ?? null,
                'is_active' => true,
            ]);

            return [
                'company' => $company,
                'user' => $user,
                'role' => $managerRole,
            ];
        });

        $token = $result['user']->createToken('auth')->plainTextToken;

        return [
            'company' => $result['company'],
            'user' => $result['user'],
            'role' => $result['role'],
            'token' => $token,
        ];
    }
}
