# Task 007: بناء Logout - تسجيل الخروج (SPA)

## الهدف
بناء `POST /api/auth/logout` بنمط `Controller->Action`: يحذف الجلسة/الكوكي للمستخدم الحالي `Sanctum SPA`.

## الملفات المطلوبة
- `microservices-demo/services/auth-service/app/Actions/Auth/LogoutAction.php` (جديد)
- `microservices-demo/services/auth-service/app/Http/Controllers/AuthController.php` (تعديل - إضافة logout)
- `microservices-demo/services/auth-service/routes/api.php` (تعديل - إضافة route مع auth)

## خطوات التنفيذ

### 1. LogoutAction
```php
// app/Actions/Auth/LogoutAction.php
namespace App\Actions\Auth;
use Illuminate\Http\Request;

class LogoutAction {
  public function execute(Request $request): void {
    // للـ SPA: تسجيل خروج + حذف الجلسة
    \Auth::guard('web')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
  }
}
```

### 2. AuthController::logout
```php
// app/Http/Controllers/AuthController.php - إضافة
use App\Actions\Auth\LogoutAction; use Illuminate\Http\Request;

public function logout(Request $request, LogoutAction $action) {
  $action->execute($request);
  return response()->json(['success'=>true, 'message'=>'تم تسجيل الخروج']);
}
```

### 3. Route (محمي)
```php
// routes/api.php - إضافة
Route::middleware('auth')->post('/auth/logout', [AuthController::class, 'logout']);
```

## معايير القبول
- [ ] `POST /auth/logout` بدون تسجيل دخول يرجع `401`
- [ ] `POST /auth/logout` بعد `login` يرجع `200` ويحذف `Cookie: laravel_session`
- [ ] `GET /auth/me` بعد logout يرجع `401`
- [ ] لا منطق في Controller - فقط `Action->execute()`

## ملاحظات
- لا تحذف `personal_access_tokens` هنا (للـ SPA نستخدم session فقط)
- احترم `CONVENTIONS.md:28` - Controller يستدعي Action فقط
- استخدم `auth` middleware وليس `auth:sanctum` لأننا في SPA
