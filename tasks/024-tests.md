# Task 024: Tests - Middleware + Land

## الهدف
بناء اختبارات Feature لخدمة الأراضي.

## الملفات
- `microservices-demo/services/land-service/tests/TestCase.php` (تعديل - إضافة serviceHeaders)
- `microservices-demo/services/land-service/tests/Feature/Auth/MiddlewareTest.php` (جديد)
- `microservices-demo/services/land-service/tests/Feature/Land/CreateLandTest.php` (جديد)
- `microservices-demo/services/land-service/tests/Feature/Land/ListLandsTest.php` (جديد)

## خطوات

### TestCase
```php
protected function serviceHeaders(string $companyId='comp-1', array $perms=[]): array {
  return [
    'X-Service-Token'=>config('app.service_token'),
    'X-Company-Id'=>$companyId,
    'X-User-Id'=>'user-1',
    'X-Permissions'=>implode(',', $perms),
  ];
}
```

### MiddlewareTest
- بدون token -> 401
- token خاطئ -> 401
- token صحيح -> 200

### CreateLandTest
- بدون create_lands -> 403
- بيانات ناقصة -> 422
- بيانات صحيحة -> 201 + company_id محفوظ

## معايير القبول
- [ ] `php artisan test` يمر 5 اختبارات
