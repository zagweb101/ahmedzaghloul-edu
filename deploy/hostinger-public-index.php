<?php

/**
 * استخدم هذا الملف عندما لا تستطيع جعل Document Root يشير إلى مجلد public/.
 *
 * 1. ارفع المشروع كاملًا إلى مجلد خارج public_html (مثال: /home/USER/ahmedzaghloul-edu)
 * 2. انسخ هذا الملف إلى public_html/index.php
 * 3. عدّل المسار أدناه ليطابق موقع مشروعك على السيرفر
 */

$projectRoot = __DIR__ . '/../ahmedzaghloul-edu';

define('LARAVEL_START', microtime(true));

if (file_exists($maintenance = $projectRoot . '/storage/framework/maintenance.php')) {
    require $maintenance;
}

require $projectRoot . '/vendor/autoload.php';

/** @var \Illuminate\Foundation\Application $app */
$app = require_once $projectRoot . '/bootstrap/app.php';

$app->handleRequest(\Illuminate\Http\Request::capture());
