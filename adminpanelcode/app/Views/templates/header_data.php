<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

	<!-- SEO Meta -->
    <meta name="description" content="<?= esc($settings['site_description']) ?>">
    <meta name="keywords" content="<?= esc($settings['site_keywords']) ?>">
    
    <!-- Website Title -->
    <title><?= isset($pageTitle) ? esc($pageTitle) . ' | ' . esc($settings['app_name'] ?? 'Default App') : esc($settings['app_name'] ?? 'Default App') ?></title>
    
    <!-- Favicon --> 
    <link href="<?= base_url('images/' . esc($settings['app_logo'] ?? 'favicon.ico')) ?>" rel="icon" sizes="32x32">
    <link href="<?= base_url('images/' . esc($settings['app_logo'] ?? 'favicon.ico')) ?>" rel="icon" sizes="192x192">

    <!-- IOS Touch Icons -->
    <link rel="apple-touch-icon" href="<?= base_url('images/' . esc($settings['app_logo'] ?? 'favicon.ico')) ?>/">
    <link rel="apple-touch-icon" sizes="152x152" href="<?= base_url('images/' . esc($settings['app_logo'] ?? 'favicon.ico')) ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= base_url('images/' . esc($settings['app_logo'] ?? 'favicon.ico')) ?>">
    <link rel="apple-touch-icon" sizes="167x167" href="<?= base_url('images/' . esc($settings['app_logo'] ?? 'favicon.ico')) ?>">

    <!-- Google fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@300;400;500;700&display=swap" rel="stylesheet">

    <!-- Vendor styles -->
    <link id="theme-style" href="<?= base_url('assets/vendors/bootstrap/bootstrap.min.css') ?>" as="style" rel="stylesheet" />
    <link id="theme-style" href="<?= base_url('assets/vendors/perfect-scrollbar/perfect-scrollbar.min.css') ?>" as="style" rel="stylesheet" />
    <link id="theme-style" href="<?= base_url('assets/vendors/remixicon/remixicon.css') ?>" as="style" rel="stylesheet" />
    <link id="theme-style" href="<?= base_url('assets/vendors/quill/quill.min.css') ?>" as="style" rel="stylesheet" />
    <link id="theme-style" href="<?= base_url('assets/vendors/select2/select2.min.css') ?>" as="style" rel="stylesheet" />
    <link id="theme-style" href="<?= base_url('assets/vendors/sweetalerts2/sweetalert2.min.css') ?>" as="style" rel="stylesheet" />

    <!-- Main style -->
    <link id="theme-style" href="<?= base_url('assets/css/styles.css?v=1.0.0') ?>" as="style" rel="stylesheet" />
    <?php if (!empty($settings['site_direction']) && $settings['site_direction'] == '1'): ?>
        <link rel="stylesheet" href="<?= base_url('assets/css/rtl.css?v=1.0.0') ?>" type="text/css">
    <?php endif; ?>
   
    <!-- SET GLOBAL BASE URL -->
    <script>var base_url = '<?= base_url() ?>';</script>
    
    <!-- SECURE HEADER CODE WITH PURIFICATION -->
    <?= isset($settings['header_code']) ? service('purify')->purifyHtml(html_entity_decode($settings['header_code'])) : '' ?>
    
</head>
<body>
    <!-- Loader -->
    <div id="nsofts_loader">
        <div class="text-center">
            <i class="ri-3x ri-donut-chart-line nsofts-loader-icon"></i>
            <span class="d-block">Loading</span>
        </div>
    </div>