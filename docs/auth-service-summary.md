# ملخص خدمة المصادقة - Auth Service
### مشروع بصيرة الزراعية - Farm Manager v2

> **التاريخ:** 31 أغسطس 2026  
> **الخدمة:** `services/auth-service` (المنفذ :8001)  
> **الغرض:** إدارة الشركات (Multi-tenant)، المستخدمين، الأدوار والصلاحيات، والمصادقة عبر Sanctum  
> **قاعدة البيانات:** SQLite محلياً / PostgreSQL 15 عبر Docker (`farm_erp`)

---

## 1. نظرة عامة

خدمة المصادقة هي **بوابة الدخول** للنظام. كل شركة زراعية هي مستأجر مستقل (`company_id`)، وكل مستخدم ينتمي لشركة ودور واحد. التوكن `Sanctum` يحمل `company_id` ويُستخدم من قبل الخدمات الأخرى (`Land`, `Inventory`) للعزل.

**التدفق:**
```
زائر -> POST /api/auth/register {company_name, admin_name, email, password}
      -> إنشاء Company + Role "مدير" + User + Token
      -> {company, user, token, role}
```

---

## 2. قاعدة البيانات - 6 Migrations

| # | الملف | الجدول | الوصف |
|---|-------|--------|-------|
| 1 | `100000_create_companies_table` | `companies` | الشركات المستأجرة |
| 2 | `100001_create_roles_table` | `roles` | أدوار كل شركة |
| 3 | `100002_create_permissions_table` | `permissions` | الأذونات العامة |
| 4 | `100003_create_role_permission_table` | `role_permission` | ربط الأدوار بالأذونات M:M |
| 5 | `100004_update_users_table` | `users` | تحويل المستخدم لـ Multi-tenant |
| 6 | `100005_create_personal_access_tokens_table` | `personal_access_tokens` | توكنات Sanctum |

### 2.1 companies
| العمود | النوع | الوصف |
|--------|-------|-------|
| `id` | BIGSERIAL PK | معرف الشركة |
| `name` | VARCHAR(255) | اسم الشركة (الريان الزراعية) |
| `slug` | VARCHAR(100) UNIQUE | للـ subdomain: `alrayan.basira.com` |
| `license_number` | VARCHAR(100) | رقم الترخيص الزراعي |
| `phone/email/address` | VARCHAR/TEXT | بيانات التواصل |
| `plan` | VARCHAR(50) | `trial/basic/pro` |
| `trial_ends_at` | TIMESTAMP | نهاية التجربة (14 يوم) |
| `settings` | JSONB | إعدادات مرنة (اللغة، العملة) |
| `is_active` | BOOLEAN | هل الشركة نشطة؟ |
| **فهارس** | `INDEX(slug), INDEX(is_active)` | سرعة البحث |

### 2.2 roles
| العمود | النوع | الوصف |
|--------|-------|-------|
| `id` | PK | |
| `company_id` | FK -> companies (RESTRICT) | الشركة المالكة - RESTRICT يمنع حذف دور به مستخدمون |
| `name/slug` | VARCHAR(100) | اسم الدور / معرفه (`manager`) |
| `description` | TEXT | وصف الدور |
| `is_system` | BOOLEAN | دور نظامي لا يُحذف |
| **فهارس** | `UNIQUE(company_id, slug), INDEX(company_id)` | نفس الدور في شركات مختلفة مسموح |

### 2.3 permissions
| العمود | النوع | الوصف |
|--------|-------|-------|
| `name` | VARCHAR(100) UNIQUE | `lands.view`, `finance.approve` |
| `group_name` | VARCHAR(50) | المجموعة: `lands, inventory, finance, users` |
| `guard_name` | VARCHAR(50) | `web` (لـ spatie) |
| **فهارس** | `INDEX(group_name)` | |

### 2.4 role_permission
| العمود | النوع | الوصف |
|--------|-------|-------|
| `role_id` | FK -> roles CASCADE | |
| `permission_id` | FK -> permissions CASCADE | |
| **PK** | `(role_id, permission_id)` | |

### 2.5 users (بعد التحديث)
| العمود | النوع | الوصف |
|--------|-------|-------|
| `company_id` | FK nullable CASCADE | `null` = Super Admin (مالك النظام) |
| `role_id` | FK nullable SET NULL | دور واحد فقط (أبسط لـ ERP) |
| `phone/avatar` | VARCHAR | |
| `is_active` | BOOLEAN | تعطيل بدل الحذف |
| `last_login_at` | TIMESTAMP | تتبع الدخول |
| `deleted_at` | TIMESTAMP | SoftDelete |
| `email_verified_at/remember_token` | | يتوقعها Laravel |
| **فهارس** | `UNIQUE(company_id, email), INDEX(company_id, role_id)` | البريد مكرر بين الشركات مسموح |

### 2.6 personal_access_tokens (Sanctum)
| العمود | النوع | الوصف |
|--------|-------|-------|
| `tokenable_type/id` | morph | `App\Models\User` + معرفه |
| `name/token` | VARCHAR | اسم التوكن / القيمة المشفرة |
| `abilities` | TEXT | الصلاحيات المخزنة |
| `last_used_at/expires_at` | TIMESTAMP | |

**ERD:**
```
companies 1--M roles 1--M users
            | M:M permissions
            +-- role_permission
       users 1--M personal_access_tokens
```

---

## 3. النماذج (Models)

| النموذج | الحقول | العلاقات | دوال مساعدة |
|---------|--------|----------|-------------|
| `Company` | `fillable: name, slug, license_number...` `casts: settings=>array, is_active=>bool` | `hasMany(users), hasMany(roles)` | - |
| `Role` | `fillable: company_id, name, slug...` `casts: is_system=>bool` | `belongsTo(company), hasMany(users), belongsToMany(permissions)` | - |
| `Permission` | `fillable: name, group_name...` | `belongsToMany(roles)` | - |
| `User` | `fillable: company_id, role_id, name, email...` `Hidden: password` `SoftDeletes, HasApiTokens` | `belongsTo(company), belongsTo(role)` | `hasPermission($name)` تتحقق عبر `role->permissions` |

---

## 4. المصانع (Factories)

| المصنع | الحقول المولدة | الحالات |
|--------|----------------|---------|
| `CompanyFactory` | `name, slug, license_number, phone, email, plan, trial_ends_at, settings` | `inactive()` |
| `RoleFactory` | `company_id, name, slug, description, is_system` | `system(), forCompany($company)` |
| `PermissionFactory` | `name, group_name, guard_name, description` | - |
| `UserFactory` | `name, email, phone, password, avatar, is_active` | `inactive(), superAdmin(), forCompany($company, $role)` |

**مثال:**
```php
Company::factory()->count(5)->create();
$company = Company::first();
User::factory()->forCompany($company)->create();
```

---

## 5. البذور (Seeders)

| Seeder | ماذا ينشئ | التفاصيل |
|--------|-----------|----------|
| `CompanySeeder` | 1 شركة | `الريان الزراعية (alrayan)` - trial 14 يوم |
| `PermissionSeeder` | 28 إذن | موزعة: `lands(8), inventory(5), procurement(3), finance(4), users(6), companies(2)` |
| `RoleSeeder` | 3 أدوار لـ alrayan | `مدير (28 إذن - الكل)`, `محاسب (5 إذن مالية)`, `عامل (3 إذن عرض)` |
| `UserSeeder` | 2 مستخدم | `super@basira.test` (Super Admin بدون شركة), `k@alrayan.test` (خالد المدير - manager)` |
| `DatabaseSeeder` | منسق | يستدعي الأربعة بالترتيب |

---

## 6. البيانات الحالية (حية)

```
php artisan tinker
companies: 6 (الريان + 5 شركات اختبارية)
roles: 3 (مدير، محاسب، عامل - لـ alrayan)
permissions: 28
users: 3 (Super Admin + خالد + rudy.kuhic@example.net)
personal_access_tokens: 0 (ينشأ عند login)
```

**اختبار سريع:**
```bash
curl -X POST http://127.0.0.1:8001/api/login -H "Content-Type: application/json" -d '{"email":"k@alrayan.test","password":"password"}'
```

---

## 7. ما التالي

| الخطوة | المطلوب |
|--------|---------|
| Controllers | `AuthController (register, login, verify, me, logout, refresh)`, `CompanyController`, `UserController`, `RoleController` |
| Middleware | `TenantGuard` - يقرأ التوكن من `Authorization: Bearer` ويضيف `company_id` للـ Request |
| Requests | `RegisterRequest, LoginRequest (validation)` |
| API | `POST /api/auth/register, /login, /verify, /me` + `GET /api/users, /roles` |

---

> **الخلاصة للإدارة:** خدمة المصادقة جاهزة على مستوى قاعدة البيانات والنماذج والبيانات الاختبارية. العزل بين الشركات محقق عبر `company_id` في كل جدول، والأدوار والصلاحيات مرنة وقابلة للتوسع. الخطوة القادمة هي بناء واجهات الـ API.
