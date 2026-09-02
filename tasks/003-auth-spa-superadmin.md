# Task 003: تحويل Auth Service إلى Sanctum SPA + تحديد Super Admin للنظام فقط

## الهدف
تحويل المصادقة من `Sanctum Bearer Token` إلى `Sanctum SPA (Cookie + CSRF)` وتحديد صلاحيات `Super Admin` لتكون `system.*` فقط (إدارة النظام) وليس `companies.*`.

## القرارات المعتمدة (من النقاش)
- 1A Self-service, 2A أدوار ثابتة, 3B نظامنا الخاص, 4B Sanctum SPA, 5A Super Admin للنظام فقط

## الملفات المعنية
- `microservices-demo/services/auth-service/.env` و `.env.example`
- `microservices-demo/services/auth-service/config/sanctum.php`
- `microservices-demo/services/auth-service/config/cors.php`
- `microservices-demo/services/auth-service/config/session.php`
- `microservices-demo/services/auth-service/bootstrap/app.php` (middleware)
- `microservices-demo/services/auth-service/app/Models/User.php`
- `microservices-demo/services/auth-service/database/seeders/PermissionSeeder.php`
- `microservices-demo/services/auth-service/database/seeders/UserSeeder.php`
- `microservices-demo/frontend/.env.local` (للـ withCredentials)

## خطوات التنفيذ
1. **تحديث .env** في `auth-service`:
   ```
   SANCTUM_STATEFUL_DOMAINS=localhost:3000,127.0.0.1:3000
   SESSION_DRIVER=cookie
   SESSION_DOMAIN=localhost
   SESSION_SECURE_COOKIE=false
   FRONTEND_URL=http://localhost:3000
   ```

2. **تحديث config/cors.php**:
   ```php
   'supports_credentials' => true,
   'allowed_origins' => [env('FRONTEND_URL', 'http://localhost:3000')],
   ```

3. **تحديث config/sanctum.php**: تأكد من `stateful` يحتوي `localhost:3000`

4. **إضافة Middleware** في `bootstrap/app.php`:
   ```php
   ->withMiddleware(function (Middleware $middleware) {
       $middleware->statefulApi();
   })
   ```

5. **تحديث PermissionSeeder**: أضف إذنين جديدين:
   ```php
   ['name' => 'system.view', 'group_name' => 'system', 'description' => 'عرض إحصائيات النظام'],
   ['name' => 'system.settings', 'group_name' => 'system', 'description' => 'إدارة إعدادات النظام'],
   ```

6. **تحديث UserSeeder**: أعطِ `super@basira.test` دوراً جديداً `super_admin` بصلاحيات `system.*` فقط (لا `companies.*`, لا `lands.*`)

7. **إنشاء Role جديد `super_admin`** في `RoleSeeder` أو `UserSeeder`:
   ```php
   $superRole = Role::firstOrCreate(['company_id' => null, 'slug' => 'super_admin'], [...]);
   $superRole->permissions()->sync(Permission::whereIn('name', ['system.view','system.settings'])->pluck('id'));
   User::where('email','super@basira.test')->update(['role_id' => $superRole->id]);
   ```

8. **تحديث frontend/.env.local**: أضف `NEXT_PUBLIC_WITH_CREDENTIALS=true` وتأكد أن `fetch` يستخدم `credentials: 'include'`

## معايير القبول
- [ ] `php artisan config:show sanctum` يظهر `stateful => localhost:3000`
- [ ] `POST /api/login` يضع `Cookie: laravel_session` بدل `token`
- [ ] `GET /api/user` يعمل مع `Cookie` بدون `Authorization: Bearer`
- [ ] `super@basira.test` يملك فقط `system.view, system.settings` (تحقق عبر `php artisan tinker -> $u->role->permissions`)
- [ ] `k@alrayan.test` لا يزال يملك `lands.*` ولا يملك `system.*`
- [ ] `php artisan migrate:fresh --seed` ينجح بدون أخطاء

## ملاحظات
- لا تحذف جدول `personal_access_tokens` - اتركه للـ API Mobile مستقبلاً
- لا تثبت `spatie/laravel-permission`
- احترم `company_id nullable` للـ Super Admin
