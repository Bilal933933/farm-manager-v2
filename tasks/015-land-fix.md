# Task 015: Land - توحيد Request/Resource مع Migration + باقي CRUD

## المشكلة
StoreLandRequest و LandResource تستخدم حقول area_hectares/soil_type/location بينما lands migration تستخدم area/area_unit/description/slug.

## الهدف
توحيد الحقول وإضافة Update/Delete/Show.

## الملفات
- `microservices-demo/services/land-service/app/Http/Requests/Land/StoreLandRequest.php` (تعديل rules)
- `microservices-demo/services/land-service/app/Http/Requests/Land/UpdateLandRequest.php` (جديد)
- `microservices-demo/services/land-service/app/Http/Resources/LandResource.php` (تعديل)
- `microservices-demo/services/land-service/app/Actions/Land/UpdateLandAction.php` (جديد)
- `microservices-demo/services/land-service/app/Actions/Land/DeleteLandAction.php` (جديد)
- `microservices-demo/services/land-service/app/Http/Controllers/Api/LandController.php` (إضافة show/update/destroy)
- `microservices-demo/services/land-service/routes/api.php` (إضافة routes)

## خطوات

### 1. StoreLandRequest rules لتطابق lands
```php
public function rules(): array {
  return [
    'name'=>['required','string','min:3','max:255'],
    'slug'=>['nullable','string','regex:/^[a-z0-9-]+$/','unique:lands,slug'],
    'area'=>['required','numeric','min:0.1'],
    'area_unit'=>['nullable','string','in:hectare,acre,dunum'],
    'description'=>['nullable','string'],
    'map_coordinates'=>['nullable','json'],
    'ownership_type'=>['nullable','string','in:owned,rented,share'],
    'status'=>['nullable','string','in:active,inactive'],
  ];
}
public function authorize(): bool { return $this->hasPermission($this, 'create_lands'); }
```

### 2. LandResource لتطابق lands
```php
public function toArray($request): array {
  return [
    'id'=>$this->id, 'company_id'=>$this->company_id,
    'name'=>$this->name, 'slug'=>$this->slug,
    'area'=>$this->area, 'area_unit'=>$this->area_unit,
    'description'=>$this->description, 'status'=>$this->status,
  ];
}
```

### 3. Update/Delete Actions
```php
// UpdateLandAction: execute(Land $land, array $data): Land { $land->update($data); return $land->fresh(); }
// DeleteLandAction: execute(Land $land): void { $land->delete(); }
```

### 4. LandController إضافة
```php
public function show(Request $r, Land $land) { // يتحقق company_id
  if ((string)$land->company_id !== (string)$this->getCompanyId($r)) abort(403);
  return new LandResource($land);
}
public function update(UpdateLandRequest $r, Land $land, UpdateLandAction $a) { ... }
public function destroy(Request $r, Land $land, DeleteLandAction $a) { ... }
```

### 5. Routes
```php
Route::middleware(['verify.service.token', SetRequestContext::class])->group(function(){
  Route::get('/lands', [LandController::class,'index']);
  Route::post('/lands', [LandController::class,'store']);
  Route::get('/lands/{land}', [LandController::class,'show']);
  Route::put('/lands/{land}', [LandController::class,'update']);
  Route::delete('/lands/{land}', [LandController::class,'destroy']);
});
```

## معايير القبول
- [ ] `POST /lands {name, area, area_unit}` ينشئ land بنجاح
- [ ] `GET /lands/{id}` لشركة أخرى يرجع 403
- [ ] `LandResource` يرجع slug, area, area_unit
