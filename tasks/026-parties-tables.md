# Task 026: Parties - الجداول

## الهدف
إنشاء جداول parties + party_roles.

## الملفات
- `microservices-demo/services/parties-service/database/migrations/2026_09_05_100000_create_parties_table.php` (جديد)
- `microservices-demo/services/parties-service/database/migrations/2026_09_05_100001_create_party_roles_table.php` (جديد)
- `microservices-demo/services/parties-service/app/Models/Party.php` (جديد)
- `microservices-demo/services/parties-service/app/Models/PartyRole.php` (جديد)

## خطوات

### parties
```php
Schema::create('parties', function (Blueprint $t) {
  $t->uuid('id')->primary()->default(DB::raw('gen_random_uuid()'));
  $t->uuid('company_id')->index();
  $t->string('name',255);
  $t->string('phone',50);
  $t->string('email',255)->nullable();
  $t->text('address')->nullable();
  $t->text('notes')->nullable();
  $t->enum('status',['active','inactive'])->default('active');
  $t->timestamps(); $t->softDeletes();
  $t->unique(['company_id','name']);
  $t->unique(['company_id','phone']);
});
```

### party_roles
```php
Schema::create('party_roles', function (Blueprint $t) {
  $t->uuid('id')->primary()->default(DB::raw('gen_random_uuid()'));
  $t->foreignUuid('party_id')->constrained('parties')->cascadeOnDelete();
  $t->enum('role',['supplier','farmer','owner','tenant','buyer','lessor','contractor']);
  $t->text('notes')->nullable();
  $t->timestamps();
  $t->unique(['party_id','role']);
});
```

### Models
- Party: hasMany PartyRole, fillable company_id, name, phone...
- PartyRole: belongsTo Party

## معايير القبول
- [ ] `php artisan migrate` ينشئ الجدولين
- [ ] `Party::create` مع company_id ينجح
