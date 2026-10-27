# 🦷 نظام إدارة عيادة أسنان

نظام ويب عربي (RTL) لإدارة عيادة أسنان: المرضى، المواعيد، مخطط الأسنان التفاعلي، الزيارات والإجراءات، الفواتير والدفعات، وصلاحيات حسب الدور.

![لوحة التحكم](docs/screenshots/dashboard.png)

## المزايا

- **المرضى:** ملف كامل (بيانات، حساسية، تاريخ مرضي) مع بحث وترقيم تلقائي للملفات
- **المواعيد:** عرض يومي، حجز، تغيير الحالة، **منع تعارض المواعيد**
- **مخطط الأسنان (Odontogram):** ترقيم FDI دولي، 8 حالات للسن، حفظ فوري بدون إعادة تحميل
- **الزيارات والإجراءات:** إجراءات مرتبطة برقم السن مع تحديث المخطط تلقائياً
- **الفواتير والدفعات:** فاتورة من الزيارة، خصم، دفعات جزئية، رصيد وحالة تلقائية
- **لوحة تحكم:** إحصائيات حقيقية ورسم إيراد آخر 7 أيام
- **الصلاحيات:** مدير، طبيب، سكرتير/ة، محاسب (Gates + Middleware)، وإدارة المستخدمين

## التقنيات

Laravel 12 · Blade · Tailwind CSS · Alpine.js · MySQL · Vite

## التشغيل محلياً

```bash
git clone https://github.com/USERNAME/dental-clinic.git
cd dental-clinic
composer install
copy .env.example .env      # على لينكس/ماك: cp
php artisan key:generate
```

أنشئ قاعدة بيانات `dental_clinic` وعدّل بيانات `DB_*` في `.env`، ثم:

```bash
php artisan migrate --seed
npm install
npm run build
php artisan serve
```

## حسابات تجريبية

| الدور | البريد | كلمة المرور |
|---|---|---|
| مدير | admin@admin.com | password |
| طبيب | doctor@clinic.com | password |
| سكرتير/ة | secretary@clinic.com | password |
| محاسب | accountant@clinic.com | password |

> للعرض والتجربة فقط. غيّرها قبل أي نشر حقيقي.

## لقطات شاشة

| مخطط الأسنان | الفاتورة |
|---|---|
| ![](docs/screenshots/odontogram.png) | ![](docs/screenshots/invoice.png) |