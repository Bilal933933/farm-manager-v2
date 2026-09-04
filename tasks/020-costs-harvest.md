# Task 020: Costs - إضافة harvest_id

## الهدف
إضافة `harvest_id` لجدول costs لربط تكاليف الحصاد بالحصاد نفسه.

## الملفات
- `microservices-demo/services/land-service/database/migrations/2026_09_04_100006_add_harvest_id_to_costs_table.php` (جديد)
- `microservices-demo/services/land-service/app/Models/Cost.php` (تعديل - علاقة harvest)
- `microservices-demo/services/land-service/app/Http/Requests/Cost/StoreCostRequest.php` (تعديل - إضافة harvest_id)
- `microservices-demo/services/land-service/app/Http/Requests/Cost/UpdateCostRequest.php` (تعديل)

## خطوات

### Migration
```php
Schema::table('costs', function (Blueprint $t) {
  $t->foreignUuid('harvest_id')->nullable()->constrained('harvests')->nullOnDelete();
  $t->index('harvest_id');
});
```

### Cost Model
```php
public function harvest(): BelongsTo { return $this->belongsTo(Harvest::class); }
protected $fillable = [..., 'harvest_id'];
```

### StoreCostRequest - إضافة harvest_id
```php
public function rules(): array {
  return [
    ...,
    'harvest_id'=>['nullable','uuid','exists:harvests,id'],
  ];
}
```

## معايير القبول
- [ ] `costs` به عمود `harvest_id` FK nullable
- [ ] `POST /seasons/{season}/costs {harvest_id}` ينجح إذا Harvest تابع لنفس Season
