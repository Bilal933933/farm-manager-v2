# Task 005: بناء Login - تسجيل الدخول + فحص الحالة

## الهدف
بناء `POST /api/auth/login` بنمط Actions: يتحقق من البريد/كلمة السر و `is_active` للمستخدم والشركة و `trial_ends_at` ثم ينشئ جلسة SPA.

## الملفات المطلوبة
- `microservices-demo/services/auth-service/app/Http/Requests/LoginRequest.php` (جديد)
- `microservices-demo/services/auth-service/app/Actions/Auth/LoginAction.php` (جديد)
- `microservices-demo/services/auth-service/app/Http/Controllers/AuthController.php` (تعديل - إضافة login)
- `microservices-demo/services/auth-service/routes/api.php` (تعديل - إضافة route)

## خطوات التنفيذ

### 1. LoginRequest
```php
// app/Http/Requests/LoginRequest.php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class LoginRequest extends FormRequest {
  public function authorize(): bool { return true; }
  public function rules(): array {
    return ['email'=>['required','email'], 'password'=>['required','string']];
  }
  public function messages(): array {
    return ['email.required'=>'البريد مطلوب','password.required'=>'كلمة المرور مطلوبة'];
  }
}
```

### 2. LoginAction
```php
// app/Actions/Auth/LoginAction.php
namespace App\Actions\Auth;
use App\Models\User; use Illuminate\Support\Facades\Hash; use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class LoginAction {
  public function execute(string $email, string $password): User {
    $user = User::with(['company','role.permissions'])->where('email', $email)->first();
    if (!$user || !Hash::check($password, $user->password)) {
      throw ValidationException::withMessages(['email'=>['بيانات الدخول غير صحيحة']]);
    }
    if (!$user->is_active) {
      throw new HttpException(403, 'الحساب معطل');
    }
    if ($user->company && !$user->company->is_active) {
      throw new HttpException(403, 'الشركة معطلة');
    }
    if ($user->company && $user->company->trial_ends_at && $user->company->trial_ends_at->isPast() && $user->company->plan === 'trial') {
      throw new HttpException(403, 'انتهت فترة التجربة');
    }
    $user->update(['last_login_at'=>now()]);
    return $user;
  }
}
```

### 3. AuthController::login
```php
// app/Http/Controllers/AuthController.php - إضافة
use App\Actions\Auth\LoginAction; use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;

public function login(LoginRequest $request, LoginAction $action) {
  $user = $action->execute($request->input('email'), $request->input('password'));
  Auth::login($user);
  $request->session()->regenerate();
  return response()->json(['success'=>true, 'data'=>[
    'user'=>['id'=>$user->id,'name'=>$user->name,'email'=>$user->email,'company_id'=>$user->company_id],
    'company'=> $user->company ? ['id'=>$user->company->id,'name'=>$user->company->name,'plan'=>$user->company->plan] : null,
    'role'=> $user->role?->name,
  ]]);
}
```

### 4. Route
```php
// routes/api.php - إضافة
Route::post('/auth/login', [AuthController::class, 'login']);
```

## معايير القبول
- [ ] `POST /auth/login {email:"k@alrayan.test", password:"password"}` يرجع `200 + Cookie: laravel_session` و `last_login_at` يتحدث
- [ ] `POST /auth/login {email:"wrong@test.com"}` يرجع `422 "بيانات الدخول غير صحيحة"` (لا يكشف وجود البريد)
- [ ] مستخدم `is_active=false` يرجع `403 "الحساب معطل"`
- [ ] شركة `trial_ends_at` منتهية ترجع `403 "انتهت فترة التجربة"`
- [ ] لا منطق DB في Controller

## ملاحظات
- استخدم `Hash::check` وليس `Auth::attempt` مباشرة ليتمكن Action من فحص `company`
- لا تنس `with('company','role.permissions')` للأداء
