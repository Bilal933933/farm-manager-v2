# Task 004: بناء Register - إنشاء الشركة + 3 أدوار + المدير

## الهدف
بناء دالة التسجيل `POST /api/auth/register` بهيكلة `Actions` منفصلة لمنع التضخم: `CreateCompanyWithRolesAction` ينشئ الشركة وأدوارها، و `RegisterAction` ينسق وينشئ المدير.

## القرارات المرتبطة
- 1A Self-service, 2A أدوار ثابتة, 3B نظامنا الخاص, 4B Sanctum SPA, 5A Super Admin نظام فقط

## الملفات المطلوبة
- `microservices-demo/services/auth-service/app/Actions/Company/CreateCompanyWithRolesAction.php` (جديد)
- `microservices-demo/services/auth-service/app/Actions/Auth/RegisterAction.php` (جديد)
- `microservices-demo/services/auth-service/app/Http/Requests/RegisterRequest.php` (جديد)
- `microservices-demo/services/auth-service/app/Http/Controllers/AuthController.php` (تعديل - إضافة register فقط)
- `microservices-demo/services/auth-service/routes/api.php` (تعديل - إضافة route)

## خطوات التنفيذ

### 1. CreateCompanyWithRolesAction
```php
// app/Actions/Company/CreateCompanyWithRolesAction.php
namespace App\Actions\Company;
use App\Models\Company; use App\Models\Role; use App\Models\Permission; use Illuminate\Support\Str;

class CreateCompanyWithRolesAction {
  public function execute(string $name, ?string $slug = null): Company {
    return \DB::transaction(function() use ($name, $slug) {
      $company = Company::create([
        'name' => $name,
        'slug' => $slug ?: Str::slug($name) . '-' . Str::random(4),
        'plan' => 'trial',
        'trial_ends_at' => now()->addDays(14),
        'is_active' => true,
      ]);
      // 3 أدوار ثابتة - نفس منطق RoleSeeder
      $perms = Permission::all()->keyBy('name');
      $manager = Role::create(['company_id'=>$company->id, 'name'=>'مدير', 'slug'=>'manager', 'is_system'=>true]);
      $manager->permissions()->sync($perms->pluck('id'));

      $accountant = Role::create(['company_id'=>$company->id, 'name'=>'محاسب', 'slug'=>'accountant', 'is_system'=>true]);
      $accountant->permissions()->sync($perms->whereIn('name', ['finance.view','finance.create','finance.approve','companies.view','users.view'])->pluck('id'));

      $worker = Role::create(['company_id'=>$company->id, 'name'=>'عامل', 'slug'=>'worker', 'is_system'=>true]);
      $worker->permissions()->sync($perms->whereIn('name', ['lands.view','inventory.view','procurement.view'])->pluck('id'));

      return $company->load('roles');
    });
  }
}
```

### 2. RegisterRequest
```php
// app/Http/Requests/RegisterRequest.php
public function rules(): array {
  return [
    'company_name' => ['required','string','min:3','max:255'],
    'company_slug' => ['nullable','string','regex:/^[a-z0-9-]+$/','unique:companies,slug'],
    'admin_name' => ['required','string','min:3','max:255'],
    'admin_email' => ['required','email','unique:users,email'],
    'admin_password' => ['required','string','min:8','regex:/[A-Za-z]/','regex:/[0-9]/'],
    'admin_phone' => ['nullable','string','regex:/^(05[0-9]{8}|\+9665[0-9]{8})$/'],
  ];
}
```

### 3. RegisterAction
```php
// app/Actions/Auth/RegisterAction.php
namespace App\Actions\Auth;
use App\Actions\Company\CreateCompanyWithRolesAction;
use App\Models\User; use Illuminate\Support\Facades\Hash;

class RegisterAction {
  public function __construct(private CreateCompanyWithRolesAction $createCompany) {}
  public function execute(array $data): array {
    return \DB::transaction(function() use ($data) {
      $company = $this->createCompany->execute($data['company_name'], $data['company_slug'] ?? null);
      $managerRole = $company->roles()->where('slug','manager')->first();
      $user = User::create([
        'company_id' => $company->id,
        'role_id' => $managerRole->id,
        'name' => $data['admin_name'],
        'email' => $data['admin_email'],
        'password' => Hash::make($data['admin_password']),
        'phone' => $data['admin_phone'] ?? null,
        'is_active' => true,
      ]);
      // Sanctum SPA: سيُنشئ Cookie عبر login لاحقاً، هنا نرجع البيانات فقط
      return ['company'=>$company, 'user'=>$user, 'role'=>$managerRole];
    });
  }
}
```

### 4. AuthController::register
```php
// app/Http/Controllers/AuthController.php
public function register(RegisterRequest $request, RegisterAction $action) {
  $result = $action->execute($request->validated());
  // تسجيل دخول تلقائي بعد التسجيل للـ SPA
  \Auth::login($result['user']);
  $request->session()->regenerate();
  return response()->json(['success'=>true, 'data'=>[
    'company'=>['id'=>$result['company']->id, 'name'=>$result['company']->name, 'slug'=>$result['company']->slug],
    'user'=>['id'=>$result['user']->id, 'name'=>$result['user']->name, 'email'=>$result['user']->email],
    'role'=>'مدير'
  ]], 201);
}
```

### 5. Route
```php
// routes/api.php
Route::post('/auth/register', [AuthController::class, 'register']);
```

## معايير القبول
- [ ] `php artisan test` أو `curl -X POST http://127.0.0.1:8001/api/auth/register -H "Content-Type: application/json" -d '{"company_name":"مزرعة النور","admin_name":"أحمد","admin_email":"ah@test.com","admin_password":"Pass1234"}'` ينشئ شركة + 3 أدوار + مستخدم
- [ ] `Company::where('name','مزرعة النور')->first()->roles()->count() === 3`
- [ ] `User::where('email','ah@test.com')->first()->role->slug === 'manager'`
- [ ] لا منطق DB في Controller - كله في Actions

## ملاحظات
- استخدم `sys.stdout.reconfigure(encoding='utf-8')` إذا كتبت سكربت بايثون
- احترم `BATCH_SIZE = 1` و `Herd PHP` من `global-conventions.md`
- لا تثبت Spatie، استخدم نظامنا الخاص
