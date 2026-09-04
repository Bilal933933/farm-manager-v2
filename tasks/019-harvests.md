# Task 019: Harvests - CRUD هرمي

## الهدف
بناء CRUD الحصاد هرمي POST/GET /seasons/{season}/harvests + مباشر /harvests/{harvest}.

## الملفات
- `microservices-demo/services/land-service/app/Http/Requests/Harvest/StoreHarvestRequest.php` (جديد)
- `microservices-demo/services/land-service/app/Http/Requests/Harvest/UpdateHarvestRequest.php` (جديد)
- `microservices-demo/services/land-service/app/Actions/Harvest/CreateHarvestAction.php` (جديد)
- `microservices-demo/services/land-service/app/Actions/Harvest/ListHarvestsAction.php` (جديد)
- `microservices-demo/services/land-service/app/Http/Controllers/Api/HarvestController.php` (جديد)
- `microservices-demo/services/land-service/app/Http/Resources/HarvestResource.php` (جديد)
- `microservices-demo/services/land-service/routes/api.php` (تعديل هرمي)

## خطوات

### StoreHarvestRequest
```php
public function authorize(): bool { return $this->hasPermission($this,'create_harvests'); }
public function rules(): array {
  return [
    'product_id'=>['nullable','uuid'],
    'date'=>['required','date','before_or_equal:today'],
    'total_quantity'=>['required','numeric','min:0.01'],
    'unit'=>['required','string','max:20'],
    'notes'=>['nullable','string'],
  ];
}
```

### CreateHarvestAction
```php
public function execute(Season $season, array $data): Harvest {
  return Harvest::create([...$data, 'season_id'=>$season->id, 'company_id'=>$season->company_id]);
}
```

### HarvestController هرمي
```php
public function store(StoreHarvestRequest $r, Season $season, CreateHarvestAction $a) {
  if ((string)$season->company_id !== (string)$this->getCompanyId($r)) abort(403);
  return (new HarvestResource($a->execute($season, $r->validated())))->response()->setStatusCode(201);
}
```

### Routes
```php
Route::prefix('seasons')->group(function(){
  Route::get('/{season}/harvests', [HarvestController::class,'index']);
  Route::post('/{season}/harvests', [HarvestController::class,'store']);
});
Route::get('/harvests/{harvest}', [HarvestController::class,'show']);
```

## معايير القبول
- [ ] `POST /seasons/{season}/harvests {total_quantity:100, unit:kg}` ينجح
- [ ] `company_id` يؤخذ من Season
