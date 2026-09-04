# Task 021: Sales - CRUD مع حقول مالية إضافية

## الهدف
بناء CRUD المبيعات هرمي POST/GET /seasons/{season}/sales + مباشر /sales/{sale} مع discount, tax, delivery.

## الملفات
- `microservices-demo/services/land-service/database/migrations/2026_09_04_100005_create_sales_table.php` (تعديل - إضافة الحقول الجديدة)
- `microservices-demo/services/land-service/app/Models/Sale.php` (تعديل)
- `microservices-demo/services/land-service/app/Http/Requests/Sale/StoreSaleRequest.php` (جديد)
- `microservices-demo/services/land-service/app/Actions/Sale/CreateSaleAction.php` (جديد)
- `microservices-demo/services/land-service/app/Http/Controllers/Api/SaleController.php` (جديد)
- `microservices-demo/services/land-service/app/Http/Resources/SaleResource.php` (جديد)
- `microservices-demo/services/land-service/routes/api.php` (تعديل)

## خطوات

### Sale Migration - الحقول الجديدة
```php
$table->decimal('discount_amount',12,2)->nullable();
$table->decimal('tax_amount',12,2)->nullable();
$table->decimal('delivery_cost',12,2)->nullable();
$table->string('currency',10)->default('EGP');
$table->string('buyer_name',255)->nullable();
$table->enum('payment_method',['cash','bank_transfer','check','credit','installment'])->nullable();
$table->date('due_date')->nullable();
$table->enum('payment_status',['paid','pending','partially_paid'])->default('pending');
```

### StoreSaleRequest
```php
public function authorize(): bool { return $this->hasPermission($this,'create_sales'); }
public function rules(): array {
  return [
    'harvest_id'=>['nullable','uuid','exists:harvests,id'],
    'buyer_party_id'=>['required','uuid'],
    'quantity'=>['required','numeric','min:0.01'],
    'unit'=>['required','string'],
    'unit_price'=>['required','numeric','min:0'],
    'discount_amount'=>['nullable','numeric','min:0'],
    'tax_amount'=>['nullable','numeric','min:0'],
    'delivery_cost'=>['nullable','numeric','min:0'],
    'currency'=>['nullable','string','max:10'],
    'date'=>['required','date'],
  ];
}
```

### CreateSaleAction - حساب total_price
```php
public function execute(Season $season, array $data): Sale {
  $total = $data['quantity']*$data['unit_price'] - ($data['discount_amount']??0) + ($data['tax_amount']??0) + ($data['delivery_cost']??0);
  return Sale::create([...$data, 'season_id'=>$season->id, 'company_id'=>$season->company_id, 'total_price'=>$total]);
}
```

## معايير القبول
- [ ] `POST /seasons/{season}/sales {quantity:100, unit_price:10, discount:50}` يحسب total 950
- [ ] `buyer_party_id` مطلوب
