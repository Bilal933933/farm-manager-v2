# Task 025: توسيع الاختبارات - تغطية كل الأنواع

## الهدف
توسيع Feature Tests لتغطي كل الأنواع والاحتمالات في كل جدول، بإنشاء ملفات منفصلة لكل نوع.

## الملفات (جديدة - كل نوع ملف)
- `tests/Feature/Contract/RentInTest.php` (rent_in + financial_value)
- `tests/Feature/Contract/SharecroppingTest.php` (sharecropping + revenue_share)
- `tests/Feature/Cost/InventoryCostTest.php` (product_id + quantity*unit_price)
- `tests/Feature/Cost/OperationalCostTest.php` (بدون product_id + amount)
- `tests/Feature/Cost/HarvestCostTest.php` (مع harvest_id)
- `tests/Feature/Sale/CashSaleTest.php` (cash, paid)
- `tests/Feature/Sale/InstallmentSaleTest.php` (installment + due_date + partially_paid)
- `tests/Feature/Sale/DiscountSaleTest.php` (discount/tax/delivery حساب total)
- `tests/Feature/Land/OwnershipTypeTest.php` (owned, rented, share)
- `tests/Feature/Season/CropSeasonTest.php` (exists:crops)
- `tests/Feature/Harvest/UnitTest.php` (kg, ton, sack)
- `tests/Feature/Crop/UniqueNameTest.php` (unique name)

## خطوات لكل ملف
- بدون X-Service-Token -> 401
- بدون صلاحية -> 403
- شركة أخرى -> 403/404
- بيانات صحيحة -> 201 + تحقق DB
- بيانات ناقصة -> 422

## معايير القبول
- [ ] كل جدول له 2-3 ملفات اختبار حسب أنواعه
- [ ] `php artisan test` يمر 30+ اختبار
