# Task 011: تثبيت Sanctum Token - إرجاع {token:"1|..."} + POST verify

## القرار
Sanctum Token (A) - `Authorization: Bearer 1|...` وليس SPA Cookie. المراجع محق للـ Microservices.

## الهدف
تحويل `AuthController` ليرجع token وإصلاح verify ليكون POST مع Body.

## الملفات
- `microservices-demo/services/auth-service/app/Actions/Auth/RegisterAction.php` (تعديل - إنشاء token)
- `microservices-demo/services/auth-service/app/Actions/Auth/LoginAction.php` (تعديل - إنشاء token)
- `microservices-demo/services/auth-service/app/Actions/Auth/VerifyAction.php` (تعديل - يبحث بـ token)
- `microservices-demo/services/auth-service/app/Http/Controllers/AuthController.php` (تعديل - يرجع token)
- `microservices-demo/services/auth-service/app/Http/Resources/VerifyResource.php` (تعديل - يدعم POST)
- `microservices-demo/services/auth-service/routes/api.php` (تعديل - GET->POST verify)
- `README.md` (تعديل - مثال استجابة JWT->1|...)

## خطوات

### 1. RegisterAction - إنشاء token
```php
public function execute(array $data): array {
  $result = DB::transaction(...); // company + user + role
  $token = $result['user']->createToken('auth')->plainTextToken; // 1|...
  return ['company'=>$result['company'], 'user'=>$result['user'], 'role'=>$result['role'], 'token'=>$token];
}
```

### 2. LoginAction - إنشاء token
```php
public function execute(string $email, string $password): array {
  $user = User::...->first(); // checks
  $user->update(['last_login_at'=>now()]);
  $token = $user->createToken('auth')->plainTextToken;
  return ['user'=>$user->load('company','role.permissions'), 'token'=>$token];
}
```

### 3. VerifyAction - يبحث بـ token
```php
public function execute(string $token): ?User {
  $pat = PersonalAccessToken::findToken($token);
  if (!$pat || $pat->expires_at?->isPast()) return null;
  return $pat->tokenable->load(['company','role.permissions']);
}
```

### 4. AuthController
```php
public function register(RegisterRequest $r, RegisterAction $a) {
  $result = $a->execute($r->validated());
  return response()->json(['success'=>true, 'data'=>[
    'company'=> new CompanyResource($result['company']),
    'user'=> new UserResource($result['user']),
    'role'=> $result['role']->name,
    'token'=> $result['token'], // 1|...
  ]], 201);
}
public function login(LoginRequest $r, LoginAction $a) {
  $result = $a->execute($r->input('email'), $r->input('password'));
  return response()->json(['success'=>true, 'data'=>[
    'user'=> new UserResource($result['user']),
    'company'=> $result['user']->company ? new CompanyResource($result['user']->company) : null,
    'role'=> $result['user']->role?->name,
    'token'=> $result['token'],
  ]]);
}
public function verify(Request $r, VerifyAction $a) {
  $r->validate(['token'=>'required|string']);
  $user = $a->execute($r->input('token'));
  if (!$user) return response()->json(['valid'=>false], 401);
  return new VerifyResource($user);
}
```

### 5. Routes
```php
Route::post('/auth/verify', [AuthController::class, 'verify']); // بدون auth middleware - يتحقق بالـ token في Body
```

### 6. README - مثال
```json
{
  "success": true,
  "data": { "token": "1|AbCdEf...", "user": {"id":1, "name":"خالد"} }
}
```

## معايير القبول
- [ ] `POST /auth/register` يرجع `201 {token:"1|..."}`
- [ ] `POST /auth/login` يرجع `{token:"1|..."}`
- [ ] `POST /auth/verify {token:"1|..."}` يرجع `{valid:true, user_id, company_id}`
- [ ] `POST /auth/verify {token:"invalid"}` يرجع `401 {valid:false}`
- [ ] README لا يحتوي `eyJ0eXAiOiJKV1Qi` بل `1|...`
