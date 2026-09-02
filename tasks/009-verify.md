# Task 009: بناء Verify - التحقق للخدمات الأخرى

## الهدف
بناء `GET /api/auth/verify` للخدمات الأخرى (Land, Inventory) للتحقق من الجلسة/التوكن وإرجاع `user_id, company_id, role, permissions`. داخلي، محمي بـ `auth`.

## الملفات المطلوبة
- `microservices-demo/services/auth-service/app/Actions/Auth/VerifyAction.php` (جديد)
- `microservices-demo/services/auth-service/app/Http/Resources/VerifyResource.php` (جديد)
- `microservices-demo/services/auth-service/app/Http/Controllers/AuthController.php` (تعديل - إضافة verify)
- `microservices-demo/services/auth-service/routes/api.php` (تعديل - إضافة route)

## خطوات التنفيذ

### 1. VerifyAction
```php
// app/Actions/Auth/VerifyAction.php
namespace App\Actions\Auth;
use App\Models\User;
class VerifyAction {
  public function execute(User $user): User {
    return $user->load(['company','role.permissions']);
  }
}
```

### 2. VerifyResource
```php
// app/Http/Resources/VerifyResource.php
namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;
class VerifyResource extends JsonResource {
  public function toArray($request): array {
    return [
      'valid' => true,
      'user_id' => $this->resource->id,
      'company_id' => $this->resource->company_id,
      'role' => $this->resource->role?->slug,
      'permissions' => $this->resource->role?->permissions->pluck('name') ?? [],
    ];
  }
}
```

### 3. AuthController::verify
```php
// app/Http/Controllers/AuthController.php - إضافة
use App\Actions\Auth\VerifyAction; use App\Http\Resources\VerifyResource;

public function verify(Request $request, VerifyAction $action) {
  $user = $action->execute($request->user());
  return (new VerifyResource($user))->response();
}
```

### 4. Route
```php
// routes/api.php - إضافة
Route::middleware('auth')->get('/auth/verify', [AuthController::class, 'verify']);
```

## معايير القبول
- [ ] `GET /auth/verify` بدون auth يرجع `401`
- [ ] `GET /auth/verify` بعد login يرجع `200 {valid:true, user_id:1, company_id:1, role:"manager", permissions:["lands.view",...]}`
- [ ] Super Admin يرجع `company_id: null`
- [ ] لا منطق في Controller

## ملاحظات
- هذا endpoint يُستدعى server-to-server من الخدمات الأخرى عبر `X-Service-Token` لاحقاً
- احترم CONVENTIONS.md:28
