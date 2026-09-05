# Factories Documentation

This directory contains factory definitions for generating test data.

## Party Factory

The `PartyFactory` provides various states and methods for creating Party instances:

### Basic Usage

```php
use App\Models\Party;

// Create a basic party
$party = Party::factory()->create();

// Create multiple parties
$parties = Party::factory()->count(5)->create();
```

### Available States

#### Status States
- `active()` - Creates an active party
- `inactive()` - Creates an inactive party

```php
$activeParty = Party::factory()->active()->create();
$inactiveParty = Party::factory()->inactive()->create();
```

#### Company Association
- `forCompany(string $companyId)` - Associates party with specific company

```php
$party = Party::factory()->forCompany('company-123')->create();
```

#### Field States
- `withoutEmail()` - Creates party without email
- `withoutAddress()` - Creates party without address
- `withNotes(?string $notes)` - Creates party with specific notes
- `minimal()` - Creates party with only required fields (name, phone)

```php
$minimalParty = Party::factory()->minimal()->create();
$partyWithNotes = Party::factory()->withNotes('Custom notes')->create();
```

### Creating Parties with Roles

```php
use App\Models\PartyRole;

// Create party with multiple roles
$party = Party::factory()
    ->has(PartyRole::factory()->count(3), 'roles')
    ->create();
```

## Party Role Factory

The `PartyRoleFactory` provides methods for creating PartyRole instances:

### Basic Usage

```php
use App\Models\PartyRole;
use App\Models\Party;

// Create a role for an existing party
$role = PartyRole::factory()
    ->forParty($party)
    ->create();
```

### Available Role Types

Each role type has a dedicated state method:

```php
$supplier = PartyRole::factory()->supplier()->create();
$farmer = PartyRole::factory()->farmer()->create();
$owner = PartyRole::factory()->owner()->create();
$tenant = PartyRole::factory()->tenant()->create();
$buyer = PartyRole::factory()->buyer()->create();
$lessor = PartyRole::factory()->lessor()->create();
$contractor = PartyRole::factory()->contractor()->create();
```

### Notes Management

```php
// Create role with custom notes
$role = PartyRole::factory()->withNotes('Custom role notes')->create();

// Create role without notes
$role = PartyRole::factory()->withoutNotes()->create();
```

## Best Practices

1. **Use States for Clarity**: Always use state methods like `active()` or `supplier()` instead of passing raw values
2. **Combine States**: States can be chained for complex scenarios
3. **Avoid Unique Constraint Violations**: When creating multiple roles for the same party, ensure role types are unique

### Good Examples

```php
// Good: Clear and specific
$party = Party::factory()
    ->forCompany('company-1')
    ->active()
    ->withNotes('Important client')
    ->create();

// Good: Multiple roles with unique types
$party = Party::factory()->create();
$party->roles()->createMany([
    ['role' => PartyRoleType::Supplier],
    ['role' => PartyRoleType::Farmer],
]);
```

### Bad Examples

```php
// Bad: Hardcoded values
$party = Party::factory()->create(['status' => 'active']);

// Bad: Risk of duplicate roles
Party::factory()
    ->has(PartyRole::factory()->count(5), 'roles') // May create duplicate roles!
    ->create();
```
