# Task 013: Land Service - Middleware + Trait + Request

## الهدف
بناء طبقة المصادقة في land-service: VerifyServiceToken + SetRequestContext + Trait + StoreLandRequest حسب الهيكل النهائي.

## الملفات
- `microservices-demo/services/land-service/app/Http/Middleware/VerifyServiceToken.php` (جديد)
- `microservices-demo/services/land-service/app/Http/Middleware/SetRequestContext.php` (جديد)
- `microservices-demo/services/land-service/app/Traits/ExtractsRequestContext.php` (جديد)
- `microservices-demo/services/land-service/app/Http/Requests/Land/StoreLandRequest.php` (جديد)
- `microservices-demo/services/land-service/bootstrap/app.php` (تعديل - تسجيل Middleware)
- `microservices-demo/services/land-service/.env` + `.env.example` (تأكد SERVICE_TOKEN)

## خطوات

### 1. VerifyServiceToken
```php
namespace App\Http\Middleware;
use Closure; use Illuminate\Http\Request;
class VerifyServiceToken {
  public function handle(Request $request, Closure $next) {
    $token = $request->header('X-Service-Token');
    if (!$token || $token !== config('app.service_token')) {
      return response()->json(['message'=>'Unauthorized service'], 401);
    }
    return $next($request);
  }
}
```

### 2. SetRequestContext
```php
namespace App\Http\Middleware;
use Closure; use Illuminate\Http\Request;
class SetRequestContext {
  public function handle(Request $request, Closure $next) {
    $request->attributes->set('user_id', $request->header('X-User-Id'));
    $request->attributes->set('company_id', $request->header('X-Company-Id'));
    $perms = $request->header('X-Permissions', '');
    $request->attributes->set('permissions', $perms ? explode(',', $perms) : []);
    return $next($request);
  }
}
```

### 3. Trait
```php
namespace App\Traits;
use Illuminate\Http\Request;
trait ExtractsRequestContext {
  protected function getCompanyId(Request $request): ?int { return $request->attributes->get('company_id') ? (int)$request->attributes->get('company_id') : null; }
  protected function getUserId(Request $request): ?int { return $request->attributes->get('user_id') ? (int)$request->attributes->get('user_id') : null; }
  protected function hasPermission(Request $request, string $perm): bool { return in_array($perm, $request->attributes->get('permissions', [])); }
}
```

### 4. StoreLandRequest
```php
namespace App\Http\Requests\Land;
use Illuminate\Foundation\Http\FormRequest; use App\Traits\ExtractsRequestContext;
class StoreLandRequest extends FormRequest {
  use ExtractsRequestContext;
  public function authorize(): bool { return $this->hasPermission($this, 'create_lands'); }
  public function rules(): array {
    return [
      'name'=>['required','string','min:3','max:255'],
      'location'=>['nullable','string','max:500'],
      'area_hectares'=>['required','numeric','min:0.1'],
      'soil_type'=>['nullable','string'],
    ];
  }
}
```

### 5. bootstrap/app.php - تسجيل
```php
->withMiddleware(function (Middleware $m) {
  $m->alias(['verify.service.token'=>\App\Http\Middleware\VerifyServiceToken::class]);
})
// في routes/api.php: Route::middleware(['verify.service.token', SetRequestContext::class])->group(...)
```

## معايير القبول
- [ ] `POST /api/lands` بدون `X-Service-Token` يرجع 401
- [ ] `POST /api/lands` مع `X-Service-Token` صحيح + `X-Permissions` بدون `create_lands` يرجع 403 من authorize
- [ ] `company_id` لا يُطلب في Body - يؤخذ من Header
