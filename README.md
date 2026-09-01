# 🌾 بصيرة الزراعية — Farm Manager v2

> **نظام ERP زراعي مصغر** لإدارة الأراضي، المواسم، المخزون، المشتريات، والمحاسبة.
> 
> **معمارية:** Microservices (خدمات مصغرة) | **الحالة:** قيد التطوير | **الإصدار:** 0.1.0

---

## 📋 نظرة عامة

**بصيرة الزراعية** هو نظام إدارة مزارع داخلي (Internal Farm ERP) يستخدمه صاحب الشركة الزراعية وموظفوه لإدارة النشاط الزراعي والمحاسبي معًا.

### ما يفعله

| الوحدة | الوظيفة |
|--------|---------|
| 🏢 **الشركات** | تسجيل الشركات الزراعية (Multi-tenant) |
| 🔐 **المصادقة** | تسجيل دخول، أدوار، صلاحيات |
| 🌾 **الأراضي** | إدارة الأراضي والمواسم الزراعية |
| 👥 **الأطراف** | مزارعين، موردين، عمال، سلف |
| 📦 **المخزون** | منتجات، مستودعات، حركات |
| 🛒 **المشتريات** | أوامر شراء، فواتير |
| 💰 **المحاسبة** | دفتر أستاذ، قيود تلقائية |
| 📊 **التقارير** | Dashboard، Charts، PDF/Excel |

---

## 🏛️ المعمارية

```
┌─────────────────────────────────────────────┐
│              Next.js Frontend                │
│         (واجهة مستقلة / Independent UI)     │
│         :3000                               │
└─────────────────────────────────────────────┘
                    │
                    ▼ REST API
┌─────────────────────────────────────────────┐
│           API Gateway (Laravel)              │
│         :8000                               │
│    🔐 JWT │ 📝 Rate Limit │ 🔄 Routing       │
└─────────────────────────────────────────────┘
                    │
    ┌───────────────┼───────────────┐
    ▼               ▼               ▼
┌────────┐   ┌────────┐   ┌────────┐
│ Auth   │   │ Land   │   │Invntry │
│Service │   │Service │   │Service │
│:8001   │   │:8002   │   │:8003   │
└────────┘   └────────┘   └────────┘
    │               │               │
    └───────────────┼───────────────┘
                    ▼
         ┌─────────────────┐
         │   PostgreSQL    │
         │  (قاعدة واحدة   │
         │   + Schemas)    │
         └─────────────────┘
```

### الخدمات (Services)

| الخدمة | المنفذ | قاعدة البيانات | الوصف |
|--------|--------|----------------|-------|
| **API Gateway** | `:8000` | — | توجيه الطلبات، المصادقة، Rate Limiting |
| **Auth Service** | `:8001` | `auth` schema | المستخدمين، الأدوار، الشركات |
| **Land Service** | `:8002` | `land` schema | الأراضي، المواسم، العقود |
| **Inventory Service** | `:8003` | `inventory` schema | المنتجات، المخزون، الحركات |

---

## 🚀 التشغيل

### المتطلبات

- Docker & Docker Compose
- Node.js 20+ (للـ Frontend)
- Composer (للـ Backend)

### 1. استنساخ المستودع

```bash
git clone https://github.com/Bilal933933/microservices-demo.git
cd microservices-demo
```

### 2. تشغيل الخدمات

```bash
# بناء وتشغيل كل الخدمات
docker-compose up --build

# أو في الخلفية
docker-compose up -d --build
```

### 3. إعداد قواعد البيانات

```bash
# Auth Service
docker-compose exec auth-service php artisan migrate --seed

# Land Service
docker-compose exec land-service php artisan migrate --seed

# Inventory Service
docker-compose exec inventory-service php artisan migrate --seed
```

### 4. تشغيل الواجهة

```bash
cd frontend
npm install
npm run dev
```

### 5. الوصول

| الخدمة | الرابط |
|--------|--------|
| API Gateway | http://localhost:8000 |
| Auth Service | http://localhost:8001 |
| Land Service | http://localhost:8002 |
| Inventory Service | http://localhost:8003 |
| Frontend | http://localhost:3000 |

---

## 📡 API Endpoints

### Auth Service (`:8001`)

| الطريقة | المسار | الوصف |
|---------|--------|-------|
| `POST` | `/api/register` | تسجيل مستخدم جديد |
| `POST` | `/api/login` | تسجيل الدخول |
| `GET` | `/api/me` | بيانات المستخدم الحالي |
| `POST` | `/api/logout` | تسجيل الخروج |

**مثال: تسجيل دخول**
```bash
curl -X POST http://localhost:8001/api/login   -H "Content-Type: application/json"   -d '{"email": "admin@example.com", "password": "password"}'
```

**الاستجابة:**
```json
{
  "user": {
    "id": 1,
    "name": "مدير النظام",
    "email": "admin@example.com",
    "company_id": 1
  },
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."
}
```

### Land Service (`:8002`)

| الطريقة | المسار | الوصف |
|---------|--------|-------|
| `GET` | `/api/lands` | قائمة الأراضي |
| `POST` | `/api/lands` | إنشاء أرض جديدة |
| `GET` | `/api/lands/{id}` | تفاصيل أرض |
| `PUT` | `/api/lands/{id}` | تحديث أرض |
| `DELETE` | `/api/lands/{id}` | حذف أرض |
| `GET` | `/api/lands/{id}/seasons` | مواسم الأرض |
| `POST` | `/api/seasons` | إنشاء موسم |

**مثال: إنشاء أرض**
```bash
curl -X POST http://localhost:8000/api/lands   -H "Authorization: Bearer {token}"   -H "Content-Type: application/json"   -d '{
    "name": "الأرض الجنوبية",
    "location": "الرياض - الجنوب",
    "area_hectares": 50,
    "soil_type": "طيني"
  }'
```

---

## 🗂️ هيكل المشروع

```
microservices-demo/
├── docker-compose.yml          ← تشغيل كل الخدمات
├── .env                        ← المتغيرات البيئية
│
├── api-gateway/                ← بوابة API
│   ├── app/
│   │   └── Http/
│   │       └── Controllers/
│   │           └── GatewayController.php
│   ├── routes/
│   │   └── api.php
│   └── Dockerfile
│
├── services/
│   ├── auth-service/           ← خدمة المصادقة
│   │   ├── app/
│   │   │   ├── Http/
│   │   │   │   └── Controllers/
│   │   │   │       └── AuthController.php
│   │   │   └── Models/
│   │   │       └── User.php
│   │   ├── database/
│   │   │   └── migrations/
│   │   └── Dockerfile
│   │
│   ├── land-service/           ← خدمة الأراضي
│   │   ├── app/
│   │   │   ├── Http/
│   │   │   │   └── Controllers/
│   │   │   │       └── LandController.php
│   │   │   └── Models/
│   │   │       └── Land.php
│   │   ├── database/
│   │   │   └── migrations/
│   │   └── Dockerfile
│   │
│   └── inventory-service/      ← خدمة المخزون
│       ├── app/
│       │   └── ...
│       └── Dockerfile
│
└── frontend/                   ← واجهة Next.js
    ├── app/
    ├── components/
    ├── lib/
    └── Dockerfile
```

---

## 🛠️ التقنيات

### Backend

| التقنية | الاستخدام |
|---------|-----------|
| **Laravel 11** | إطار العمل |
| **PostgreSQL 15** | قاعدة البيانات |
| **Laravel Sanctum** | المصادقة (JWT) |
| **Docker** | الحاويات |

### Frontend

| التقنية | الاستخدام |
|---------|-----------|
| **Next.js 15** | إطار العمل |
| **React 19** | مكتبة الواجهة |
| **TypeScript** | النوعية |
| **Tailwind CSS** | التصميم |
| **shadcn/ui** | المكونات |
| **TanStack Query** | إدارة البيانات |

---

## 🧪 الاختبارات

```bash
# Auth Service
docker-compose exec auth-service php artisan test

# Land Service
docker-compose exec land-service php artisan test

# Frontend
cd frontend
npm test
```

---

## 📊 خارطة الطريق

- [x] إنشاء هيكل المشروع
- [x] Auth Service (تسجيل دخول/خروج)
- [x] API Gateway (توجيه + مصادقة)
- [x] Docker Compose
- [ ] Land Service (CRUD كامل)
- [ ] Inventory Service
- [ ] Procurement Service
- [ ] Finance Service (دفتر أستاذ)
- [ ] Reporting Service (Dashboard)
- [ ] Frontend كامل
- [ ] Tests
- [ ] README + فيديو توضيحي

---

## 🤝 المساهمة

1. انسخ المستودع (Fork)
2. أنشئ فرعاً (`git checkout -b feature/xyz`)
3. ارتكب التغييرات (`git commit -am 'إضافة xyz'`)
4. ادفع (`git push origin feature/xyz`)
5. افتح Pull Request

---

## 📝 الترخيص

MIT License — استخدمه كما تشاء.

---

## 👤 المطور

**بلال** — مطور ويب متخصص في أنظمة ERP الزراعية

- GitHub: [@Bilal933933](https://github.com/Bilal933933)
- LinkedIn: [رابط]
- البريد: [email@example.com]

---

> **"ليس مجرد تطبيق CRUD، بل نظام ERP زراعي مصغر يركز على إدارة النشاط الزراعي والمحاسبي معًا."**
