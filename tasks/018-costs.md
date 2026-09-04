# Task 018: Costs - Mock Inventory + تسعير ذكي

## الهدف
بناء CRUD التكاليف مع تكامل وهمي بالمخزون: اقتراح سعر + حساب amount شرطي.

## الملفات
- `microservices-demo/services/land-service/app/Services/InventoryService.php` (جديد Mock)
- `microservices-demo/services/land-service/app/Http/Requests/Cost/StoreCostRequest.php` (جديد)
- `microservices-demo/services/land-service/app/Http/Requests/Cost/UpdateCostRequest.php` (جديد)
- `microservices-demo/services/land-service/app/Actions/Cost/CreateCostAction.php` (جديد)
- `microservices-demo/services/land-service/app/Actions/Cost/ListCostsAction.php` (جديد)
- `microservices-demo/services/land-service/app/Http/Controllers/Api/CostController.php` (جديد)
- `microservices-demo/services/land-service/app/Http/Resources/CostResource.php` (جديد)
- `microservices-demo/services/land-service/routes/api.php` (تعديل هرمي)

## خطوات

### 1. InventoryService Mock
```php
namespace App\Services;
class InventoryService { public function getLastPrice(string $productId): float { return 10.00; } }
```

### 2. StoreCostRequest - شرطي
```php
public function rules(): array {
  return [
    'cost_type'=>['required', Rule::enum(CostType::class)],
    'product_id'=>['nullable','uuid'],
    'quantity'=>['required_if:product_id,!=,null','nullable','numeric','min:0.01'],
    'unit_price'=>['required_if:product_id,!=,null','nullable','numeric','min:0'],
    'amount'=>['required_if:product_id,null','nullable','numeric','min:0'],
    'date'=>['required','date'],
  ];
}
public function authorize(): bool { return $this->hasPermission($this,'create_costs'); }
```

### 3. CreateCostAction - حساب amount + Mock
```php
public function __construct(private InventoryService $inventory) {}
public function execute(Season $season, array $data): Cost {
  if (!empty($data['product_id'])) {
    $suggested = $this->inventory->getLastPrice($data['product_id']); // Mock
    $data['unit_price'] = $data['unit_price'] ?? $suggested;
    $data['amount'] = $data['quantity'] * $data['unit_price'];
  }
  return Cost::create([...$data, 'season_id'=>$season->id, 'company_id'=>$season->company_id]);
}
```

### 4. CostController هرمي
```php
Route::prefix('seasons')->group(function(){
  Route::get('/{season}/costs', [CostController::class,'index']);
  Route::post('/{season}/costs', [CostController::class,'store']);
});
Route::get('/costs/{cost}', [CostController::class,'show']);
```

## معايير القبول
- [ ] `POST /seasons/{season}/costs {product_id, quantity:10, unit_price:12}` يحسب amount 120
- [ ] `POST ... {cost_type:labor, amount:5000}` بدون product_id ينجح
- [ ] `Mock` يرجع 10.00 إذا لم يرسل unit_price
