# بيت المصور — منصة أحمد زغلول

منصة تعليمية عربية مبنية بـ **Laravel + Bootstrap + MySQL**، مع فصل واضح بين:

- **الموقع العام**: الرئيسية، المسارات، الاشتراكات، اللايفات، المجتمع
- **لوحة العضو**: التقدم في الدروس، المجتمع، تسجيل اللايفات
- **لوحة الإدارة**: إدارة المسارات، الدروس، اللايفات، الاشتراكات، المستخدمين، المجتمع

## المتطلبات

- PHP 8.2+
- Composer
- MySQL/MariaDB (XAMPP)
- Node.js (اختياري لـ Vite)

## التشغيل السريع

1. شغّل **Apache** و **MySQL** من XAMPP
2. أنشئ قاعدة البيانات:

```sql
CREATE DATABASE zaghloul CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

3. انسخ الإعدادات وثبّت الحزم:

```bash
copy .env.example .env
composer install
php artisan key:generate
php artisan migrate --seed
```

4. شغّل المشروع:

```bash
php artisan serve
```

ثم افتح: [http://127.0.0.1:8000](http://127.0.0.1:8000)

## حسابات تجريبية

| الدور | البريد | كلمة المرور |
|-------|--------|-------------|
| عضو | `test@example.com` | `password` |
| مدير | `admin@example.com` | `password` |

## هيكل قاعدة البيانات

- `learning_paths` — المسارات التعليمية
- `lessons` — الدروس داخل كل مسار
- `subscription_plans` — خطط الاشتراك
- `user_subscriptions` — اشتراكات الأعضاء
- `live_events` — اللايفات والفعاليات
- `live_event_registrations` — تسجيل الحضور
- `community_posts` / `community_comments` / `community_post_likes` — المجتمع
- `lesson_progress` — تقدم العضو في الدروس

## التصميم

- Bootstrap 5 RTL
- خلفية بيضاء افتراضية
- Dark Mode مركزي عبر متغيرات CSS في `resources/css/app.css`
- واجهة عربية كاملة (`dir="rtl"`)

## رفع الصور والملفات

- **صور عامة** (أغلفة المسارات، اللايفات، المجتمع، الصورة الشخصية): تُخزَّن في `storage/app/public`
- **ملفات PDF للدروس**: تُخزَّن في `storage/app/private` ولا تُحمَّل إلا للمشتركين المصرح لهم

بعد التثبيت:

```bash
php artisan storage:link
```

| المكان | ما يمكن رفعه |
|--------|--------------|
| لوحة الإدارة → مسار جديد | صورة غلاف |
| لوحة الإدارة → درس جديد | صورة مصغرة + ملف PDF |
| لوحة الإدارة → لايف جديد | صورة غلاف |
| المجتمع | صورة مع البوست |
| لوحة العضو | صورة الملف الشخصي |
| لوحة الإدارة → تعديل | استبدال أو حذف أي ملف مرفوع |
| المجتمع → معرض الصور | عرض شبكي لصور الأعضاء مع فلترة حسب القسم |
| المجتمع → تعديل البوست | العضو يعدل النص والصور (حتى 5 صور) ويحذف صورًا محددة |
| المجتمع → التعليقات | نص + صورة اختيارية مع كل تعليق |
| الإشعارات | تنبيه صاحب البوست عند التعليق أو الإعجاب (`/notifications`) |

## الإشعارات والبريد

- إشعارات داخل المنصة: تعليقات، إعجابات، حجز لايف، تذكير لايف
- البريد الإلكتروني اختياري عبر `NOTIFY_VIA_MAIL=true` في `.env`
- تذكيرات اللايفات تُرسل تلقائيًا عبر:

```bash
php artisan live-events:send-reminders
php artisan schedule:work
```

## الدفع والاشتراكات

- خطط: مجاني، شهري (99 ريال)، سنوي (990 ريال) — قابلة للتعديل من لوحة الإدارة
- `PAYMENT_DRIVER=manual` للتحويل البنكي مع تأكيد من الإدارة
- `PAYMENT_DRIVER=tap` للدفع الإلكتروني عبر Tap Payments
- `PAYMENT_DRIVER=demo` للتفعيل الفوري أثناء التطوير
- طلبات الاشتراك: `/admin/subscription-orders`

## SEO

- Meta tags و Open Graph عبر مكوّن `seo-meta`
- صفحات المسارات: عنوان/وصف/كلمات مفتاحية + JSON-LD (`Course`)
- صفحات الدروس: وصف + JSON-LD (`LearningResource`)
- صفحات اللايفات والاشتراكات: صفحات هبوط عامة + JSON-LD (`Event` / `Product`)
- المدونة: `/blog` + مقالات + JSON-LD (`Article`)
- الصفحة الرئيسية: `Organization` + `WebSite`
- Google Analytics 4 عبر `GA4_MEASUREMENT_ID`
- `noindex` تلقائي لصفحات الدخول والدفع والإدارة
- خريطة الموقع: `/sitemap.xml`
- `robots.txt` يشير إلى Sitemap

## المدفوعات والاشتراكات (Phase 3)

- بوابات الدفع: `manual` (افتراضي)، `demo`، `tap`، `stripe`
- انتهاء الاشتراكات تلقائيًا + تنبيه قبل 7 أيام (`subscriptions:process`)
- بث اللايف لمن حجز مقعدًا خلال نافذة الموعد
- Stripe Checkout + webhook على `/payments/stripe/webhook`

## النشر (GitHub + Hostinger)

راجع دليل النشر الكامل في [DEPLOYMENT.md](DEPLOYMENT.md).

### البنية التحتية (المرحلة 1)

```bash
./deploy/post-deploy.sh          # يستخدم PHP 8.3 تلقائيًا على Hostinger
./deploy/verify-production.sh
php artisan platform:health-check
php artisan platform:backup
php artisan platform:log-review
```

النسخ الاحتياطية: `storage/app/backups/` — مجدولة يوميًا عبر Cron + `schedule:run`.

## الاختبارات

```bash
php artisan test
```

عند الرفع إلى GitHub، workflow تلقائي يشغّل الاختبارات على كل Push.
