# Task 010: إصلاح التوثيق - JWT->Sanctum SPA + .env.example

## الهدف
إصلاح 4 ملاحظات الوكيل التوثيقية المتبقية: توحيد المصادقة إلى Sanctum SPA وتوضيح register وإضافة .env.example.

## الملفات المطلوبة
- `README.md` (تعديل - JWT->Sanctum SPA)
- `microservices-demo/services/auth-service/.env.example` (جديد)
- `microservices-demo/services/auth-service/.env` (تحقق موجود)
- `frontend/.env.example` (اختياري - إضافة إذا غير موجود)

## خطوات التنفيذ

### 1. README.md - إصلاح 3 مواضع
- ابحث عن `JWT` و `Sanctum — المصادقة (JWT)` وغيّر إلى `Laravel Sanctum SPA (Cookie + CSRF)`
- في قسم API Gateway: غيّر `🔐 JWT` إلى `🔐 Sanctum SPA (Cookie)`
- في جدول Endpoints: غيّر `POST /api/register (غامض)` إلى `POST /api/auth/register — تسجيل شركة جديدة + مديرها (Self-service)` وأضف `POST /api/users — إضافة موظف (لاحقاً)`
- أضف سطر `GET /api/auth/verify — تحقق للخدمات الأخرى (Land, Inventory)` في جدول Endpoints

### 2. .env.example للـ Auth Service
```env
APP_NAME="Auth Service"
APP_ENV=local
APP_KEY=base64:GENERATE_WITH_php_artisan_key:generate
APP_DEBUG=true
APP_URL=http://localhost:8001

DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite

SANCTUM_STATEFUL_DOMAINS=localhost:3000,127.0.0.1:3000
SESSION_DRIVER=cookie
SESSION_DOMAIN=localhost
SESSION_SECURE_COOKIE=false
FRONTEND_URL=http://localhost:3000

CORS_ALLOWED_ORIGINS=http://localhost:3000
```

### 3. التحقق
- `README.md` لا يحتوي كلمة `JWT` إطلاقاً `grep -i jwt README.md` فارغ
- `.env.example` موجود ويحتوي `SANCTUM_STATEFUL_DOMAINS, SESSION_DRIVER`

## معايير القبول
- [ ] `grep -i jwt README.md` لا يرجع نتائج
- [ ] `README` يذكر `Sanctum SPA (Cookie + CSRF)` و `POST /api/auth/register — تسجيل شركة`
- [ ] `GET /api/auth/verify` مذكور في README
- [ ] `.env.example` موجود في `services/auth-service/`

## ملاحظات
- لا تغير docker-compose.yml (موجود أصلاً في microservices-demo/)
- لا تغير كود AuthService - توثيق فقط
