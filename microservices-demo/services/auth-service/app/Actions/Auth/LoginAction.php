<?php

namespace App\Actions\Auth;

use App\Http\Resources\AuthResource;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class LoginAction
{
    /**
     * @throws ValidationException
     * @throws HttpException
     */
    public function execute(string $email, string $password): AuthResource
    {
        $user = User::with(['company', 'role.permissions'])
            ->where('email', $email)
            ->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['بيانات الدخول غير صحيحة'],
            ]);
        }

        if (! $user->is_active) {
            throw new HttpException(403, 'الحساب معطل');
        }

        if ($user->company && ! $user->company->is_active) {
            throw new HttpException(403, 'الشركة معطلة');
        }

        if (
            $user->company
            && $user->company->trial_ends_at
            && $user->company->trial_ends_at->isPast()
            && $user->company->plan === 'trial'
        ) {
            throw new HttpException(403, 'انتهت فترة التجربة');
        }

        $user->update(['last_login_at' => now()]);

        return new AuthResource([
            'company' => $user->company,
            'user' => $user,
            'role' => $user->role,
        ]);
    }
}
