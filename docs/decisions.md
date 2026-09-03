# قرارات معمارية - Auth Service
### بصيرة الزراعية - Farm Manager v2
**التاريخ:** 31 أغسطس 2026

## القرارات الخمسة المعتمدة

| # | الموضوع | القرار | التفاصيل |
|---|---------|--------|----------|
| 1 | **التسجيل** | **A - Self-service** | الزائر يسجل شركته بنفسه عبر `POST /api/auth/register {company_name, admin_name, email, password}`. يتطلب `plan=trial` و `trial_ends_at +14 يوم` وبوابة دفع لاحقاً. |
| 2 | **الأدوار** | **A - ثابتة** | 3 أدوار فقط: `مدير (كل الصلاحيات)`, `محاسب (مالية)`, `عامل (عرض)`. لا `RoleController`، الأدوار في `RoleSeeder` فقط. |
| 3 | **الصلاحيات** | **B - نظامنا الخاص** | لا `spatie/laravel-permission`. نستخدم `permissions` + `role_permission` + `User::hasPermission()` يدوي. أخف وأنسب لـ 3 أدوار. |
| 4 | **التوكن** | **Hybrid Proxy - Cookie→Gateway Proxy→Bearer** | `Frontend→Gateway: Cookie + CSRF` و `Gateway Proxy: يقرأ Cookie → يستخرج Token 1\|... → يرسل Bearer للـ Services`. **الحالة الحالية:** `Token` صرف `1\|...` (011/012) لأن `Gateway` لم يُبنى بعد - الـ `Hybrid` سيُطبق في مرحلة `api-gateway`. |
| 5 | **Super Admin** | **A - للنظام فقط** | `super@basira.test` (`company_id=null`) يملك فقط `system.view, system.settings` ولا يملك `companies.*` أو `lands.*`. يدير النظام وليس الشركات. |

## الأثر على الكود الحالي

| القرار | ما سيتغير | ما سيبقى |
|--------|-----------|----------|
| 1A | إبقاء `companies.slug, plan, trial_ends_at` | - |
| 2A | لا إنشاء `RoleController` | `RoleSeeder` ينشئ 3 أدوار ثابتة |
| 3B | لا تثبيت `spatie` | `PermissionSeeder` بـ 28 إذن + `hasPermission()` |
| 4B | تعديل `config/sanctum.php, cors.php, .env` + إضافة `statefulApi()` | جدول `personal_access_tokens` يبقى للـ Mobile |
| 5A | إضافة دور `super_admin` بصلاحيات `system.*` فقط | `company_id nullable` يبقى |

## ما التالي
- تنفيذ `Task 003` لتحويل Sanctum إلى SPA
- بناء `AuthController` و `TenantGuard` حسب هذه القرارات
