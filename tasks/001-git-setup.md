# Task 001: تهيئة Git ورفع المشروع إلى GitHub

## الهدف
إنشاء مستودع git محلي لمشروع `farm-manager-v2`، حفظ كل ما تم إنشاؤه بكوميت مفصل، ورفعه إلى `https://github.com/Bilal933933/farm-manager-v2.git`، ثم كتابة ملخص.

## الملفات المعنية
- `E:\heard\farm-manager-v2\.gitignore` (حالياً يحتوي `.aider*` فقط - يحتاج تحديث)
- `E:\heard\farm-manager-v2\README.md`
- `E:\heard\farm-manager-v2\CONVENTIONS.md`
- `E:\heard\farm-manager-v2\.aider.conf.yml`
- `E:\heard\farm-manager-v2\microservices-demo/` (كامل: docker-compose.yml, api-gateway, services/*, frontend)
- `E:\heard\farm-manager-v2\tasks/` (هذا المجلد)

## خطوات التنفيذ (نفذها بالترتيب)
1. **تحديث .gitignore** ليستبعد الملفات غير الضرورية:
   ```
   .aider*
   node_modules/
   vendor/
   .env
   storage/logs/*
   !storage/logs/.gitkeep
   .freebuff/
   *.log
   ```

2. **فحص الحالة**:
   ```powershell
   git status --short
   git log --oneline -3
   git remote -v
   ```

3. **إضافة كل الملفات وعمل كوميت مفصل**:
   ```powershell
   git add .
   git commit -m "feat: initial microservices setup

   - Auth Service: 6 migrations (companies, roles, permissions, role_permission, users multitenant, sanctum)
   - Models: Company, Role, Permission, User (SoftDeletes, HasApiTokens)
   - Factories: Company, Role, Permission, User (forCompany, superAdmin)
   - Seeders: CompanySeeder, PermissionSeeder (28 perms), RoleSeeder (3 roles), UserSeeder
   - Frontend: Next.js 16 + Tailwind v4 + shadcn (button, card, input, table) + zustand, tanstack-query
   - Infra: docker-compose.yml (postgres:15 + 4 Laravel services), .env for sqlite local / postgres docker
   - Tooling: Herd PHP 8.4.22, Composer, .aider.conf.yml (gemini-3-flash-preview), CONVENTIONS.md
   - Services running: 8000 gateway, 8001 auth, 8002 land, 8003 inventory, 3000 frontend
   "
   ```

4. **ربط GitHub ورفع**:
   ```powershell
   git branch -M main
   git remote add origin https://github.com/Bilal933933/farm-manager-v2.git
   git push -u origin main
   ```
   > إذا طلب توكن، استخدم GitHub PAT. إذا كان origin موجود مسبقاً: `git remote set-url origin ...`

5. **التحقق**:
   ```powershell
   git log --oneline -1
   git remote -v
   ```

## معايير القبول
- [ ] `.gitignore` محدث
- [ ] `git status` نظيف بعد الكوميت
- [ ] `git log` يظهر كوميت واحد مفصل
- [ ] `git remote -v` يظهر `origin -> Bilal933933/farm-manager-v2.git`
- [ ] `git push` نجح ويظهر المشروع على GitHub

## ملاحظات
- لا تنشئ ملفات جديدة خارج المطلوب
- استخدم `SEARCH/REPLACE` عند تعديل `.gitignore`
- اعرض `git status` قبل وبعد الكوميت
