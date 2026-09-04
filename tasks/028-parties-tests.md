# Task 028: Parties - اختبارات

## الهدف
اختبارات Feature لخدمة الأطراف.

## الملفات
- `microservices-demo/services/parties-service/tests/Feature/Party/CreatePartyTest.php` (جديد)
- `microservices-demo/services/parties-service/tests/Feature/Party/UniqueTest.php` (جديد - company_id+name/phone unique)
- `microservices-demo/services/parties-service/tests/Feature/PartyRole/AddRoleTest.php` (جديد)

## خطوات

### CreatePartyTest
- بدون create_parties -> 403
- name مكرر داخل نفس company -> 422
- phone مكرر -> 422
- بيانات صحيحة -> 201 + company_id محفوظ

### AddRoleTest
- بدون party_id صحيح -> 404
- role مكرر لنفس party -> 422 unique
- role صحيح -> 201

## معايير القبول
- [ ] `php artisan test` يمر 5 اختبارات
