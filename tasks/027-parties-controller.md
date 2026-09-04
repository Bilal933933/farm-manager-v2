# Task 027: Parties - Controller + Actions + Resources

## الهدف
بناء CRUD + إدارة أدوار Party.

## الملفات
- `microservices-demo/services/parties-service/app/Http/Requests/Party/StorePartyRequest.php` (جديد)
- `microservices-demo/services/parties-service/app/Actions/Party/CreatePartyAction.php` (جديد)
- `microservices-demo/services/parties-service/app/Http/Controllers/Api/PartyController.php` (جديد)
- `microservices-demo/services/parties-service/app/Http/Resources/PartyResource.php` (جديد)
- `microservices-demo/services/parties-service/app/Http/Controllers/Api/PartyRoleController.php` (جديد)
- `microservices-demo/services/parties-service/routes/api.php` (تعديل)

## خطوات

### StorePartyRequest
```php
public function authorize(): bool { return $this->hasPermission($this,'create_parties'); }
public function rules(): array {
  return ['name'=>['required','string','max:255'], 'phone'=>['required','string','max:50'], 'email'=>['nullable','email'], 'address'=>['nullable','string']];
}
```

### CreatePartyAction
```php
public function execute(array $data, string $companyId): Party { return Party::create([...$data, 'company_id'=>$companyId]); }
```

### PartyController
```php
public function index(Request $r, ListPartiesAction $a) { return PartyResource::collection($a->execute($this->getCompanyId($r))); }
public function store(StorePartyRequest $r, CreatePartyAction $a) { return (new PartyResource($a->execute($r->validated(), $this->getCompanyId($r))))->response()->setStatusCode(201); }
```

### PartyRoleController
```php
public function store(Request $r, Party $party, CreatePartyRoleAction $a) { // يتحقق company_id
  return $a->execute($party, $r->input('role'));
}
```

### Routes
```php
Route::middleware(['verify.service.token', SetRequestContext::class])->group(function(){
  Route::apiResource('parties', PartyController::class);
  Route::get('/parties/{party}/roles', [PartyRoleController::class,'index']);
  Route::post('/parties/{party}/roles', [PartyRoleController::class,'store']);
  Route::delete('/parties/{party}/roles/{role}', [PartyRoleController::class,'destroy']);
});
```

## معايير القبول
- [ ] `POST /parties {name, phone}` ينشئ طرف
- [ ] `POST /parties/{party}/roles {role:supplier}` يضيف دور
