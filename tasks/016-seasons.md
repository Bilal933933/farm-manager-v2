# Task 016: Seasons - CRUD هرمي

## الهدف
بناء CRUD المواسم بنمط هرمي POST/GET /lands/{land}/seasons + مباشر /seasons/{season}.

## الملفات
- `microservices-demo/services/land-service/app/Http/Requests/Season/StoreSeasonRequest.php` (جديد)
- `microservices-demo/services/land-service/app/Http/Requests/Season/UpdateSeasonRequest.php` (جديد)
- `microservices-demo/services/land-service/app/Actions/Season/CreateSeasonAction.php` (جديد)
- `microservices-demo/services/land-service/app/Actions/Season/ListSeasonsAction.php` (جديد)
- `microservices-demo/services/land-service/app/Http/Controllers/Api/SeasonController.php` (جديد)
- `microservices-demo/services/land-service/app/Http/Resources/SeasonResource.php` (جديد)
- `microservices-demo/services/land-service/routes/api.php` (تعديل - إضافة routes هرمية)

## خطوات

### 1. StoreSeasonRequest
```php
namespace App\Http\Requests\Season;
use App\Traits\ExtractsRequestContext; use Illuminate\Foundation\Http\FormRequest;
class StoreSeasonRequest extends FormRequest {
  use ExtractsRequestContext;
  public function authorize(): bool { return $this->hasPermission($this, 'create_seasons'); }
  public function rules(): array {
    return [
      'name'=>['required','string','min:3','max:150'],
      'crop_type'=>['required','string'],
      'product_id'=>['nullable','string'], // من inventory لاحقاً
      'start_date'=>['required','date'],
      'end_date'=>['nullable','date','after:start_date'],
      'status'=>['nullable','string'],
    ];
  }
}
```

### 2. CreateSeasonAction
```php
namespace App\Actions\Season;
use App\Models\Season; use App\Models\Land;
class CreateSeasonAction {
  public function execute(Land $land, array $data): Season {
    return Season::create([...$data, 'land_id'=>$land->id, 'company_id'=>$land->company_id]);
  }
}
```

### 3. SeasonController (هرمي + مباشر)
```php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller; use App\Traits\ExtractsRequestContext;
use App\Models\{Land, Season}; use App\Http\Requests\Season\StoreSeasonRequest;
use App\Actions\Season\{CreateSeasonAction, ListSeasonsAction}; use App\Http\Resources\SeasonResource;
class SeasonController extends Controller {
  use ExtractsRequestContext;
  public function index(Request $r, Land $land, ListSeasonsAction $a) {
    if ((string)$land->company_id !== (string)$this->getCompanyId($r)) abort(403);
    return SeasonResource::collection($a->execute($land));
  }
  public function store(StoreSeasonRequest $r, Land $land, CreateSeasonAction $a) {
    if ((string)$land->company_id !== (string)$this->getCompanyId($r)) abort(403);
    $season = $a->execute($land, $r->validated());
    return (new SeasonResource($season))->response()->setStatusCode(201);
  }
  public function show(Request $r, Season $season) {
    if ((string)$season->company_id !== (string)$this->getCompanyId($r)) abort(403);
    return new SeasonResource($season->load('land'));
  }
}
```

### 4. Routes هرمية + مباشرة
```php
Route::middleware(['verify.service.token', SetRequestContext::class])->group(function(){
  Route::prefix('lands')->group(function(){
    Route::get('/{land}/seasons', [SeasonController::class,'index']);
    Route::post('/{land}/seasons', [SeasonController::class,'store']);
  });
  Route::get('/seasons/{season}', [SeasonController::class,'show']);
  Route::put('/seasons/{season}', [SeasonController::class,'update']);
  Route::delete('/seasons/{season}', [SeasonController::class,'destroy']);
});
```

## معايير القبول
- [ ] `POST /lands/{land}/seasons` لأرض شركة أخرى يرجع 403
- [ ] `GET /lands/{land}/seasons` يرجع مواسم الأرض فقط
- [ ] `company_id` لا يُرسل في Body - يؤخذ من Land
