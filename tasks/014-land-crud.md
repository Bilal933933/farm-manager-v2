# Task 014: Land Service - Actions + Controller + Resources

## الهدف
بناء CRUD للأراضي بنمط Controller->Action->Resource مع TenantGuard company_id.

## الملفات
- `microservices-demo/services/land-service/app/Actions/Land/CreateLandAction.php` (جديد)
- `microservices-demo/services/land-service/app/Actions/Land/ListLandsAction.php` (جديد)
- `microservices-demo/services/land-service/app/Http/Controllers/Api/LandController.php` (جديد)
- `microservices-demo/services/land-service/app/Http/Resources/LandResource.php` (جديد)
- `microservices-demo/services/land-service/routes/api.php` (تعديل - إضافة routes)

## خطوات

### 1. CreateLandAction
```php
namespace App\Actions\Land;
use App\Models\Land;
class CreateLandAction {
  public function execute(array $data, int $companyId): Land {
    return Land::create([...$data, 'company_id'=>$companyId]);
  }
}
```

### 2. ListLandsAction
```php
namespace App\Actions\Land;
use App\Models\Land;
class ListLandsAction {
  public function execute(int $companyId) {
    return Land::where('company_id', $companyId)->latest()->get();
  }
}
```

### 3. LandResource
```php
namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;
class LandResource extends JsonResource {
  public function toArray($request): array {
    return ['id'=>$this->id, 'name'=>$this->name, 'area_hectares'=>$this->area_hectares, 'company_id'=>$this->company_id];
  }
}
```

### 4. LandController
```php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller; use App\Traits\ExtractsRequestContext;
use App\Actions\Land\{CreateLandAction, ListLandsAction};
use App\Http\Requests\Land\StoreLandRequest; use App\Http\Resources\LandResource;
class LandController extends Controller {
  use ExtractsRequestContext;
  public function index(Request $r, ListLandsAction $a) {
    $lands = $a->execute($this->getCompanyId($r));
    return LandResource::collection($lands);
  }
  public function store(StoreLandRequest $r, CreateLandAction $a) {
    $land = $a->execute($r->validated(), $this->getCompanyId($r));
    return (new LandResource($land))->response()->setStatusCode(201);
  }
}
```

### 5. Routes
```php
Route::middleware(['verify.service.token', SetRequestContext::class])->group(function(){
  Route::get('/lands', [LandController::class, 'index']);
  Route::post('/lands', [LandController::class, 'store']);
});
```

## معايير القبول
- [ ] `GET /lands` مع Headers صحيح يرجع أراضي company_id فقط
- [ ] `POST /lands` بدون create_lands يرجع 403
