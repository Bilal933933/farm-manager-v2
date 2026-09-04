# Task 029: Inventory - Migrations 4 جداول

## الهدف
إنشاء 4 جداول أساسية لخدمة المخزون.

## الملفات
- `microservices-demo/services/inventory-service/database/migrations/2026_09_06_100000_create_products_table.php` (جديد)
- `microservices-demo/services/inventory-service/database/migrations/2026_09_06_100001_create_warehouses_table.php` (جديد)
- `microservices-demo/services/inventory-service/database/migrations/2026_09_06_100002_create_lots_table.php` (جديد)
- `microservices-demo/services/inventory-service/database/migrations/2026_09_06_100003_create_inventory_movements_table.php` (جديد)

## خطوات

### products
```php
Schema::create('products', function (Blueprint $t) {
  $t->uuid('id')->primary()->default(DB::raw('gen_random_uuid()'));
  $t->uuid('company_id')->index();
  $t->string('name',255);
  $t->text('description')->nullable();
  $t->string('unit',20);
  $t->string('category',100)->nullable();
  $t->enum('status',['active','inactive'])->default('active');
  $t->timestamps(); $t->softDeletes();
  $t->unique(['company_id','name']);
});
```

### warehouses - مخزن واحد افتراضي
```php
Schema::create('warehouses', function (Blueprint $t) {
  $t->uuid('id')->primary()->default(DB::raw('gen_random_uuid()'));
  $t->uuid('company_id')->index();
  $t->string('name',255);
  $t->text('location')->nullable();
  $t->enum('status',['active','inactive'])->default('active');
  $t->timestamps(); $t->softDeletes();
  $t->unique(['company_id','name']);
});
```

### lots
```php
Schema::create('lots', function (Blueprint $t) {
  $t->uuid('id')->primary()->default(DB::raw('gen_random_uuid()'));
  $t->uuid('company_id')->index();
  $t->foreignUuid('product_id')->constrained('products');
  $t->foreignUuid('warehouse_id')->constrained('warehouses');
  $t->enum('source_type',['harvest','purchase','adjustment']);
  $t->uuid('source_id')->nullable()->index();
  $t->uuid('season_id')->nullable();
  $t->decimal('quantity',12,2);
  $t->decimal('reserved_quantity',12,2)->default(0);
  $t->string('unit',20);
  $t->decimal('cost_per_unit',12,2)->nullable();
  $t->date('harvest_date')->nullable();
  $t->enum('status',['available','reserved','sold_out','expired'])->default('available');
  $t->timestamps(); $t->softDeletes();
});
```

### inventory_movements
```php
Schema::create('inventory_movements', function (Blueprint $t) {
  $t->uuid('id')->primary()->default(DB::raw('gen_random_uuid()'));
  $t->uuid('company_id')->index();
  $t->foreignUuid('lot_id')->constrained('lots');
  $t->enum('type',['harvest_in','purchase_in','sale_out','adjustment_in','adjustment_out']);
  $t->decimal('quantity',12,2);
  $t->decimal('unit_price',12,2)->nullable();
  $t->date('date');
  $t->timestamps(); $t->softDeletes();
});
```

## معايير القبول
- [ ] `php artisan migrate` ينشئ 4 جداول
- [ ] `Product::create` ينجح
