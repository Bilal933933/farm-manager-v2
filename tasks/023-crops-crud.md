# Task 023: Crops - CRUD

## الهدف
بناء CRUD للمحاصيل المؤقتة.

## الملفات
- `microservices-demo/services/land-service/app/Http/Requests/Crop/StoreCropRequest.php` (جديد)
- `microservices-demo/services/land-service/app/Actions/Crop/CreateCropAction.php` (جديد)
- `microservices-demo/services/land-service/app/Http/Controllers/Api/CropController.php` (جديد)
- `microservices-demo/services/land-service/app/Http/Resources/CropResource.php` (جديد)
- `microservices-demo/services/land-service/routes/api.php` (تعديل)

## خطوات

### StoreCropRequest
```php
public function authorize(): bool { return $this->hasPermission($this,'create_crops'); }
public function rules(): array {
  return ['name'=>['required','string','max:100','unique:crops,name'], 'description'=>['nullable','string'], 'unit'=>['nullable','string','max:20']];
}
```

### CreateCropAction
```php
public function execute(array $data): Crop { return Crop::create($data); }
```

### CropController
```php
public function index(Request $r, ListCropsAction $a) { return CropResource::collection($a->execute()); }
public function store(StoreCropRequest $r, CreateCropAction $a) { return (new CropResource($a->execute($r->validated())))->response()->setStatusCode(201); }
```

### Routes
```php
Route::get('/crops', [CropController::class,'index']);
Route::post('/crops', [CropController::class,'store']);
Route::get('/crops/{crop}', [CropController::class,'show']);
Route::put('/crops/{crop}', [CropController::class,'update']);
Route::delete('/crops/{crop}', [CropController::class,'destroy']);
```

## معايير القبول
- [ ] `POST /crops {name:مانجو}` ينشئ محصول
- [ ] `POST /crops {name:أرز}` مكرر يفشل unique
