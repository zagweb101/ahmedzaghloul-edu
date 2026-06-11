# النشر على GitHub و Hostinger

دليل نشر منصة **بيت المصور** (Laravel 12 + MySQL).

## 1) رفع المشروع إلى GitHub

### التهيئة الأولى (مرة واحدة)

```bash
git init
git add .
git commit -m "Initial platform release"
git branch -M main
git remote add origin https://github.com/zagweb101/ahmedzaghloul-edu.git
git push -u origin main
```

### ملاحظات أمان

- لا ترفع `.env` أبدًا (مضاف في `.gitignore`)
- ارفع `.env.example` فقط كمرجع
- لا ترفع `vendor/` أو `node_modules/`
- بعد كل Push على `main`، GitHub Actions يشغّل الاختبارات تلقائيًا (`.github/workflows/tests.yml`)

### تحديثات لاحقة

```bash
git add .
git commit -m "وصف التحديث"
git push origin main
```

---

## 2) استنساخ المشروع على Hostinger (الطريقة السريعة)

من Terminal في hPanel:

```bash
chmod +x deploy/hostinger-first-install.sh
./deploy/hostinger-first-install.sh ~/ahmedzaghloul-edu
```

ثم عدّل `.env` وشغّل:

```bash
cd ~/ahmedzaghloul-edu
./deploy/post-deploy.sh
./deploy/verify-production.sh
```

للتحديثات اللاحقة:

```bash
./deploy/update-production.sh
```

---

## 3) إعداد Hostinger

### المتطلبات

| البند | القيمة |
|-------|--------|
| PHP | 8.2 أو أحدث |
| قاعدة البيانات | MySQL / MariaDB |
| امتدادات PHP | `mbstring`, `pdo_mysql`, `openssl`, `fileinfo`, `gd` (اختياري) |
| Composer | من Terminal في hPanel |

### قاعدة البيانات

1. hPanel → **Databases** → أنشئ قاعدة بيانات MySQL
2. أنشئ مستخدمًا واربطه بالقاعدة بصلاحيات كاملة
3. احفظ: `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`

---

## 3) رفع الملفات

### الطريقة الموصى بها (Document Root → public)

1. ارفع المشروع إلى مجلد خارج `public_html`:
   ```
   /home/USER/ahmedzaghloul-edu/
   ```
2. من hPanel → **Domains** → **Document Root**:
   ```
   /home/USER/ahmedzaghloul-edu/public
   ```
3. فعّل SSL (Let's Encrypt) من hPanel

### الطريقة البديلة (بدون تغيير Document Root)

1. ارفع المشروع إلى `/home/USER/ahmedzaghloul-edu/`
2. انسخ `deploy/hostinger-public-index.php` إلى `public_html/index.php`
3. عدّل سطر `$projectRoot` ليطابق مسار مشروعك:

```php
$projectRoot = __DIR__ . '/../ahmedzaghloul-edu';
```

4. انسخ محتويات `public/.htaccess` إلى `public_html/.htaccess` إن لزم

---

## 4) ملف `.env` على السيرفر

```env
APP_NAME="بيت المصور"
APP_ENV=production
APP_KEY=base64:GENERATED_KEY
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_db
DB_USERNAME=your_user
DB_PASSWORD=your_password

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
FILESYSTEM_DISK=local

MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=465
MAIL_USERNAME=your@domain.com
MAIL_PASSWORD=your_mail_password
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=your@domain.com
MAIL_FROM_NAME="${APP_NAME}"

NOTIFY_VIA_MAIL=true
LIVE_EVENT_REMINDER_HOURS=24

# تحويل بنكي يدوي
PAYMENT_DRIVER=manual
PAYMENT_BANK_NAME="اسم البنك"
PAYMENT_ACCOUNT_NAME="بيت المصور"
PAYMENT_IBAN="SAxx xxxx xxxx xxxx xxxx xxxx"
PAYMENT_MANUAL_INSTRUCTIONS="حوّل المبلغ وأرسل رقم الطلب في ملاحظة التحويل."

# أو الدفع الإلكتروني عبر Tap
# PAYMENT_DRIVER=tap
# TAP_SECRET_KEY=sk_live_xxxxx
# TAP_API_URL=https://api.tap.company/v2
# TAP_SOURCE_ID=src_all
```

> **Tap:** سجّل Webhook URL في لوحة Tap:
> `https://your-domain.com/payments/tap/webhook`

---

## 5) أوامر ما بعد الرفع

من Terminal في hPanel داخل مجلد المشروع:

```bash
composer install --no-dev --optimize-autoloader
php artisan key:generate
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

أو شغّل السكربت الجاهز:

```bash
chmod +x deploy/post-deploy.sh
./deploy/post-deploy.sh
```

### صلاحيات المجلدات

```bash
chmod -R 775 storage bootstrap/cache
```

---

## 6) Cron Jobs

### أ) جدولة Laravel (مطلوب)

hPanel → **Cron Jobs** → **Custom**:

```bash
* * * * * cd /home/u166250023/ahmedzaghloul-edu && /opt/alt/php83/usr/bin/php artisan schedule:run >> /dev/null 2>&1
```

يشغّل تلقائيًا:
- تذكيرات اللايفات (كل ساعة)
- نسخ احتياطي لقاعدة البيانات والملفات (يوميًا 02:00)
- مراجعة سجلات الأخطاء (يوميًا 06:00)
- فحص صحة الإنتاج (أسبوعيًا)

### ب) نشر تلقائي من GitHub (اختياري)

أضف Secrets في GitHub → Repository → Settings → Secrets:

| Secret | القيمة |
|--------|--------|
| `DEPLOY_HOST` | IP السيرفر |
| `DEPLOY_USER` | `u166250023` |
| `DEPLOY_PORT` | `65002` |
| `DEPLOY_SSH_KEY` | المفتاح الخاص لـ SSH |

بعدها workflow `deploy-production.yml` ينشر تلقائيًا بعد نجاح الاختبارات على `main`.

**بديل على السيرفر** (بدون GitHub Secrets):

```bash
# كل 10 دقائق يتحقق من تحديثات GitHub
*/10 * * * * cd /home/u166250023/ahmedzaghloul-edu && ./deploy/server-auto-pull.sh >> storage/logs/deploy.log 2>&1
```

### ج) أوامر البنية التحتية (المرحلة 1)

```bash
/opt/alt/php83/usr/bin/php artisan platform:health-check
/opt/alt/php83/usr/bin/php artisan platform:backup
/opt/alt/php83/usr/bin/php artisan platform:log-review
/opt/alt/php83/usr/bin/php artisan schedule:list
```

النسخ الاحتياطية تُحفظ في: `storage/app/backups/`

مرجع `.env` للإنتاج: `deploy/env.production.example`

---

## 6ب) المحتوى وما قبل الإطلاق التجاري (Phase 6)

### نشر المرحلة 4+ وملء المحتوى

```bash
cd ~/ahmedzaghloul-edu
git pull origin main
./deploy/post-deploy.sh
./deploy/seed-production-content.sh
```

`seed-production-content.sh` يضيف مقالات المدونة وSEO للخطط و`stream_url` للايف (آمن — `updateOrCreate` فقط).

**يدوياً من الإدارة:**
- صور خطط الاشتراك: `/admin/subscription-plans` → تحرير SEO → رفع صورة
- رابط بث حقيقي: `/admin/live-events` → تعديل اللايف → `stream_url` (YouTube embed)

### فحص الجاهزية التجارية

```bash
/opt/alt/php83/usr/bin/php artisan platform:prelaunch-check
```

### تأمين الإنتاج قبل الإطلاق

```bash
# 1) أنشئ حساب مدير حقيقي من /register ثم اجعله admin من DB أو استخدم الموجود
# 2) غيّر كلمة المرور
/opt/alt/php83/usr/bin/php artisan platform:rotate-admin-password owner@baytalmosawer.net

# 3) احذف حسابات التجربة
/opt/alt/php83/usr/bin/php artisan platform:remove-seed-users --force
```

### إعدادات `.env` قبل الإطلاق

```env
NOTIFY_VIA_MAIL=true
MAIL_USERNAME=...
MAIL_PASSWORD=...
MAIL_FROM_ADDRESS=...
PAYMENT_IBAN=SA...
GA4_MEASUREMENT_ID=G-...
```

---

## 7) التحقق بعد النشر

- [ ] الصفحة الرئيسية تفتح عبر HTTPS بدون أخطاء
- [ ] `https://your-domain.com/sitemap.xml` يعمل
- [ ] تسجيل الدخول والتسجيل يعملان
- [ ] رفع الصور يعمل (`storage:link` + صلاحيات `storage/`)
- [ ] تحميل PDF للمشتركين يعمل
- [ ] لوحة الإدارة `/admin` محمية (حساب `is_admin`)
- [ ] البريد يصل عند `NOTIFY_VIA_MAIL=true`
- [ ] Cron يعمل (`schedule:list` يظهر `live-events:send-reminders`)
- [ ] طلبات الاشتراك `/admin/subscription-orders` تعمل
- [ ] (إن استخدمت Tap) Webhook يستقبل تأكيد الدفع

---

## 8) استكشاف الأخطاء الشائعة

| المشكلة | الحل |
|---------|------|
| 500 Internal Server Error | راجع `storage/logs/laravel.log`، تأكد من `APP_KEY` وصلاحيات `storage/` |
| الصور لا تظهر | `php artisan storage:link` + تحقق من `public/storage` |
| الروابط تعيد HTTP | تأكد من `APP_URL=https://...` و SSL مفعّل |
| الجلسات لا تُحفظ | تأكد من `SESSION_DRIVER=database` وتشغيل `migrate` |
| Tap لا يفعّل الاشتراك | تحقق من Webhook URL و `TAP_SECRET_KEY` و `APP_URL` |

---

## 9) تحديث الإنتاج

```bash
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```
