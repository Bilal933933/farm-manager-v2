# Task 022: Crops - جدول محاصيل مؤقت

## الهدف
إنشاء جدول crops لتوفير product_id وصفي للموسم/حصاد/مبيعات حتى بناء Inventory Service.

## الملفات
- `microservices-demo/services/land-service/database/migrations/2026_09_04_100006_create_crops_table.php` (جديد)
- `microservices-demo/services/land-service/app/Models/Crop.php` (جديد)
- `microservices-demo/services/land-service/database/seeders/CropSeeder.php` (جديد)
- `microservices-demo/services/land-service/app/Http/Requests/Season/StoreSeasonRequest.php` (تعديل - exists:crops)
- `microservices-demo/services/land-service/app/Http/Requests/Harvest/StoreHarvestRequest.php` (تعديل)
- `microservices-demo/services/land-service/app/Http/Requests/Sale/StoreSaleRequest.php` (تعديل)

## خطوات

### Migration
```php
Schema::create('crops', function (Blueprint $t) {
  $t->uuid('id')->primary()->default(DB::raw('gen_random_uuid()'));
  $t->string('name',100)->unique();
  $t->text('description')->nullable();
  $t->string('unit',20)->nullable();
  $t->timestamps(); $t->softDeletes();
});
```

### Model
```php
class Crop extends Model { use HasFactory, SoftDeletes; protected $keyType='string'; public $incrementing=false;
  protected $fillable=['name','description','unit'];
}
```

### Seeder - 12 محصول
أرز, قمح, ذرة, شعير, قطن, طماطم, بطاطس, خيار, فلفل, برتقال, عنب, موز

### Requests تعديل
```php
// StoreSeasonRequest
'product_id'=>['required','uuid','exists:crops,id']
// StoreHarvestRequest/StoreSaleRequest
'product_id'=>['nullable','uuid','exists:crops,id']
```

## معايير القبول
- [ ] `php artisan migrate` ينشئ crops
- [ ] `Crop::count() == 12` بعد seed
- [ ] `POST /lands/{land}/seasons {product_id: uuid crops}` ينجح و uuid عشوائي يفشل exists
