# Parties Service API Documentation

## Base URL
```
/api/v1
```

## Authentication
All endpoints require:
- `X-Service-Token`: Service authentication token
- `X-Company-Id`: Company identifier
- `X-User-Id`: User identifier
- `X-Permissions`: Comma-separated permissions (e.g., "create_parties,update_parties")

## Endpoints

### Parties

#### List Parties
```
GET /parties?search=name&status=active&sort_by=name&sort_order=asc&per_page=15
```

Response:
```json
{
  "data": [
    {
      "id": "uuid",
      "company_id": "company-id",
      "name": "Party Name",
      "phone": "01011111111",
      "email": "party@example.com",
      "address": "Street Address",
      "notes": "Notes",
      "status": "active",
      "full_contact": "Party Name | 01011111111 | party@example.com",
      "roles": [],
      "roles_count": 0,
      "created_at": "2026-09-05T10:00:00Z",
      "updated_at": "2026-09-05T10:00:00Z"
    }
  ],
  "links": { "first": "...", "last": "...", "prev": null, "next": "..." },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 1,
    "path": "...",
    "per_page": 15,
    "to": 1,
    "total": 1
  }
}
```

#### Create Party
```
POST /parties
Content-Type: application/json
```

Request:
```json
{
  "name": "Party Name",
  "phone": "01011111111",
  "email": "party@example.com",
  "address": "Street Address",
  "notes": "Optional notes"
}
```

Response: `201 Created`

#### Get Party
```
GET /parties/{id}
```

#### Update Party
```
PUT /parties/{id}
Content-Type: application/json
```

Request: (all fields optional)
```json
{
  "name": "Updated Name",
  "phone": "01022222222",
  "email": "newemail@example.com",
  "status": "inactive",
  "notes": "Updated notes"
}
```

#### Delete Party
```
DELETE /parties/{id}
```

Response: `204 No Content`

#### Bulk Delete Parties
```
DELETE /parties/bulk/delete
Content-Type: application/json
```

Request:
```json
{
  "party_ids": ["uuid1", "uuid2", "uuid3"]
}
```

Response:
```json
{
  "message": "Bulk delete completed",
  "data": {
    "deleted": 2,
    "failed": 1
  }
}
```

### Party Roles

#### List Party Roles
```
GET /parties/{party_id}/roles
```

Response:
```json
[
  {
    "id": "uuid",
    "party_id": "party-uuid",
    "role": "supplier",
    "notes": "Notes",
    "created_at": "2026-09-05T10:00:00Z",
    "updated_at": "2026-09-05T10:00:00Z"
  }
]
```

#### Add Party Role
```
POST /parties/{party_id}/roles
Content-Type: application/json
```

Request:
```json
{
  "role": "supplier",
  "notes": "Optional notes"
}
```

Response: `201 Created`

#### Delete Party Role
```
DELETE /parties/{party_id}/roles/{role_id}
```

Response: `204 No Content`

## Query Parameters

### List Parties Filters

- `search`: Search by name, phone, or email
- `status`: Filter by status (active, inactive)
- `role`: Filter by party role type
- `sort_by`: Sort by field (name, created_at, updated_at). Default: created_at
- `sort_order`: Sort order (asc, desc). Default: desc
- `per_page`: Items per page. Default: 15. Max: 100

## Status Codes

- `200 OK`: Successful GET/PUT
- `201 Created`: Successful POST
- `204 No Content`: Successful DELETE
- `400 Bad Request`: Invalid request
- `401 Unauthorized`: Missing/invalid authentication
- `403 Forbidden`: Insufficient permissions
- `404 Not Found`: Resource not found
- `422 Unprocessable Entity`: Validation error
- `500 Internal Server Error`: Server error

## Party Roles

Available roles:
- `supplier`
- `farmer`
- `owner`
- `tenant`
- `buyer`
- `lessor`
- `contractor`

## Status Values

- `active`
- `inactive`

## Error Response Format

```json
{
  "success": false,
  "message": "Error message",
  "errors": {
    "field_name": ["Error detail"]
  }
}
```
