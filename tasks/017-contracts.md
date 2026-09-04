# Task 017: Contracts - CRUD هرمي + منطق مالي

## الهدف
بناء CRUD العقود هرمي POST/GET /lands/{land}/contracts + مباشر /contracts/{contract} مع قاعدة revenue_share vs financial_value.

## الملفات
- `microservices-demo/services/land-service/app/Http/Requests/Contract/StoreContractRequest.php` (جديد)
- `microservices-demo/services/land-service/app/Http/Requests/Contract/UpdateContractRequest.php` (جديد)
- `microservices-demo/services/land-service/app/Actions/Contract/CreateContractAction.php` (جديد)
- `microservices-demo/services/land-service/app/Actions/Contract/ListContractsAction.php` (جديد)
- `microservices-demo/services/land-service/app/Http/Controllers/Api/ContractController.php` (جديد)
- `microservices-demo/services/land-service/app/Http/Resources/ContractResource.php` (جديد)
- `microservices-demo/services/land-service/routes/api.php` (تعديل)

## خطوات

### StoreContractRequest - منطق مالي
```php
public function authorize(): bool { return $this->hasPermission($this, 'create_contracts'); }
public function rules(): array {
  return [
    'contract_type'=>['required', Rule::enum(ContractType::class)],
    'counterparty_party_id'=>['required','uuid'],
    'owner_party_id'=>['nullable','uuid'],
    'financial_value'=>['required_if:contract_type,rent_in,rent_out,management','nullable','numeric','min:0'],
    'revenue_share_percentage'=>['required_if:contract_type,sharecropping','nullable','numeric','between:0,100'],
    'start_date'=>['required','date'], 'end_date'=>['nullable','date','after:start_date'],
    'status'=>['nullable', Rule::enum(ContractStatus::class)],
  ];
}
```

### CreateContractAction
```php
public function execute(Land $land, array $data): Contract {
  return Contract::create([...$data, 'land_id'=>$land->id, 'company_id'=>$land->company_id]);
}
```

### ContractController هرمي
```php
public function store(StoreContractRequest $r, Land $land, CreateContractAction $a) {
  if ((string)$land->company_id !== (string)$this->getCompanyId($r)) abort(403);
  return (new ContractResource($a->execute($land, $r->validated())))->response()->setStatusCode(201);
}
```

### Routes
```php
Route::prefix('lands')->group(function(){
  Route::get('/{land}/contracts', [ContractController::class,'index']);
  Route::post('/{land}/contracts', [ContractController::class,'store']);
});
Route::get('/contracts/{contract}', [ContractController::class,'show']);
Route::put('/contracts/{contract}', [ContractController::class,'update']);
Route::delete('/contracts/{contract}', [ContractController::class,'destroy']);
```

## معايير القبول
- [ ] `POST /lands/{land}/contracts {contract_type:sharecropping, revenue_share_percentage:30}` ينجح
- [ ] `POST ... {contract_type:rent_in}` بدون financial_value يفشل 422
- [ ] `company_id` يؤخذ من Land
