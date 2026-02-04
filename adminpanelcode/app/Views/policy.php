<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

	<!-- Seo Meta -->
    <meta name="description" content="<?= isset($pageTitle) ? esc($pageTitle) : "" ?>">
 
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
</head>

<style>
    p {
        color: #5e5e5e;
        margin-top: 0;
        margin-bottom: 5px;
    }
    a {
        outline: none;
    }
    p:last-child {
        margin-bottom: 0;
    }

    .title {
        color: #dc3535;
    }
    
    h1, h2, h3, h4, h5, h6 {
    	color: #343434;
    }
    .nsofts-container {
        width: 100%;
        max-width: 1200px;
        margin-left: auto;
        margin-right: auto;
    }
    body {
        --ns-body-font-family: 'Inter', sans-serif;
        --ns-body-font-size: 14px;
        font-family: var(--ns-body-font-family);
        font-size: var(--ns-body-font-size);
    }
</style>

<body>
    <main class="d-flex justify-content-center py-1 min-vh-100">
        <div class="nsofts-container">
            <div class="card-body p-4">
                <div class="privacyHeader mb-2">
                    <h2 class="title"><?= isset($pageTitle) ? esc($pageTitle) : "" ?> for <?= esc($settings['app_name']) ?></h2>
                    <p class="mb-5">Updated <?= esc(date('D m, Y ')) ?></p>
                </div>
                <?php if($currentFile =='policy'){ ?>
                    <?= esc(stripslashes($settings['app_privacy_policy'])) ?>
                <?php } else if($currentFile =='terms'){ ?>
                    <?= esc(stripslashes($settings['app_terms'])) ?>
                <?php } ?>
            </div>
        </div>
    </main>
    
    <!-- Vendor scripts -->
    <script src="<?= base_url('assets/js/jquery.min.js') ?>"></script>
    <script src="<?= base_url('assets/vendors/bootstrap/bootstrap.min.js') ?>"></script>

</body>
</html>