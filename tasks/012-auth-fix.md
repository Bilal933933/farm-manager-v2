# Task 012: إصلاح ثغرة المصادقة - sanctum guard + إزالة statefulApi

## السبب
هجين SPA->Token ناقص: بقي `statefulApi()` و `guard web` و `middleware('auth')` فـ `logout/me` تفشل مع `Bearer 1|...` (401).

## الهدف
توحيد المصادقة إلى `Sanctum Token` فقط: `auth:sanctum` + حذف `statefulApi` + حذف منطق `session`.

## الملفات
- `microservices-demo/services/auth-service/bootstrap/app.php` (إزالة statefulApi)
- `microservices-demo/services/auth-service/config/auth.php` (إضافة sanctum guard)
- `microservices-demo/services/auth-service/routes/api.php` (auth->auth:sanctum)
- `microservices-demo/services/auth-service/app/Actions/Auth/LogoutAction.php` (حذف session -> delete token)

## خطوات

### 1. bootstrap/app.php
```php
// قبل:
->withMiddleware(fn(Middleware $m) => $m->statefulApi())
// بعد: إزالة السطر تماماً
->withMiddleware(function (Middleware $m) {})
```

### 2. config/auth.php
```php
'defaults' => ['guard' => 'web', 'passwords' => 'users'],
'guards' => [
  'web' => ['driver'=>'session','provider'=>'users'],
  'sanctum' => ['driver'=>'sanctum','provider'=>'users'], // إضافة
],
```

### 3. routes/api.php
```php
// قبل:
Route::middleware('auth')->post('/auth/logout', ...);
Route::middleware('auth')->get('/auth/me', ...);
// بعد:
Route::middleware('auth:sanctum')->post('/auth/logout', ...);
Route::middleware('auth:sanctum')->get('/auth/me', ...);
// يبقى: Route::post('/auth/verify', ...) بدون auth (يتحقق بالـ Body token)
```

### 4. LogoutAction
```php
// قبل:
Auth::guard('web')->logout(); $request->session()->invalidate();
// بعد:
public function execute(Request $request): void {
  $request->user()->currentAccessToken()->delete();
}
```

## معايير القبول
- [ ] `grep -r statefulApi bootstrap/` فارغ
- [ ] `config/auth.php` يحتوي `sanctum => driver sanctum`
- [ ] `routes/api.php` يحتوي `auth:sanctum` لـ logout/me
- [ ] `POST /auth/login -> {token:"1|..."}` ثم `GET /auth/me -H "Authorization: Bearer 1|..."` يرجع 200 وليس 401
- [ ] `POST /auth/logout -H "Authorization: Bearer 1|..."` يحذف التوكن و `GET /me` بعده يرجع 401

## ملاحظات
- لا تغير register/login/verify - تعمل أصلاً
- احترم CONVENTIONS.md:28 Controller->Action
