# Parties Service

Microservice مسؤول عن إدارة الأطراف (Suppliers, Farmers, Lessors, Owners) في نظام Farm Manager.

## الميزات الرئيسية

- ✅ **Full CRUD Operations** مع validation محسّن
- ✅ **API V1 Versioning** مع backwards compatibility
- ✅ **Advanced Filtering & Search** مع caching
- ✅ **Activity Logging** (Audit Trail)
- ✅ **Bulk Operations** للعمليات الجماعية
- ✅ **Database Optimization** مع strategic indexes
- ✅ **Error Handling** الشامل
- ✅ **Soft Deletes** للـ Parties و Roles
- ✅ **Pagination** المتقدمة

## البدء السريع

### التثبيت

```bash
cd microservices-demo/services/parties-service
composer install
php artisan migrate
php artisan db:seed
```

### تشغيل الخوادم

```bash
php artisan serve --port=8001
```

### الاختبارات

```bash
# جميع الاختبارات
php artisan test --compact

# Tests محددة
php artisan test tests/Feature/Api/V1/PartyApiTest.php

# Formatting
vendor/bin/pint --dirty --format agent
```

## الـ API Endpoints

### Parties
- `GET /api/v1/parties` - List with filters
- `POST /api/v1/parties` - Create
- `GET /api/v1/parties/{id}` - Show
- `PUT /api/v1/parties/{id}` - Update
- `DELETE /api/v1/parties/{id}` - Soft delete
- `DELETE /api/v1/parties/bulk/delete` - Bulk delete

### Party Roles
- `GET /api/v1/parties/{party}/roles` - List roles
- `POST /api/v1/parties/{party}/roles` - Add role
- `DELETE /api/v1/parties/{party}/roles/{role}` - Remove role

### Activity Logs
- `GET /api/v1/activity-logs` - List logs
- `GET /api/v1/parties/{id}/activity-logs` - Party logs

## الأمثلة

### البحث والتصفية

```bash
# البحث بـ keyword
GET /api/v1/parties?search=John

# التصفية بـ status
GET /api/v1/parties?status=active

# التصفية بـ role
GET /api/v1/parties?role=supplier

# الترتيب
GET /api/v1/parties?sort_by=name&sort_order=asc

# الـ Pagination
GET /api/v1/parties?per_page=20&page=2
```

### إنشاء طرف

```bash
POST /api/v1/parties
Content-Type: application/json

{
  "name": "Farm Supplies Co",
  "email": "info@farm.com",
  "phone": "1234567890",
  "status": "active"
}
```

### حذف جماعي

```bash
DELETE /api/v1/parties/bulk/delete
Content-Type: application/json

{
  "ids": ["id1", "id2", "id3"]
}
```

## الهيكل

```
app/
├── Actions/         # Business logic
├── Models/          # Eloquent models
├── Services/        # Helper services
├── Http/
│   ├── Controllers/ # API controllers
│   └── Resources/   # API resources
├── Observers/       # Event observers
└── Exceptions/      # Custom exceptions

database/
├── migrations/      # Database migrations
├── factories/       # Model factories
└── seeders/         # Database seeders

tests/
├── Feature/         # Feature tests
├── Unit/            # Unit tests
└── TestCase.php     # Test base class
```

## معلومات إضافية

- 📖 **Documentation**: راجع `API.md` لـ complete API docs
- 🎯 **Improvements**: راجع `IMPROVEMENTS.md` للـ details الكاملة للـ improvements
- 📝 **Conventions**: راجع `CONVENTIONS.md` لـ coding standards

## الاختبارات

### الحالة الحالية
- ✅ 76+ tests passing
- ✅ Factory pattern setup
- ✅ Comprehensive model tests
- ⚠️ Feature/Integration tests need middleware setup

### تشغيل tests مفصلة

```bash
# جميع tests
php artisan test --compact

# Feature tests فقط
php artisan test tests/Feature/

# Unit tests فقط
php artisan test tests/Unit/ --compact

# Test محدد
php artisan test --filter="test_search_parties"
```

## التطوير المستقبلي

- [ ] GraphQL API
- [ ] Advanced export (CSV, Excel)
- [ ] Webhooks support
- [ ] Batch async processing
- [ ] Rate limiting
- [ ] Full-text search
- [ ] Geolocation filtering

## المساهمة

يرجى اتباع `CONVENTIONS.md` عند المساهمة في المشروع.

## الترخيص

هذا المشروع مرخص تحت الـ MIT License

