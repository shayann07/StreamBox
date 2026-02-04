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
    <style>
        * {
          box-sizing: border-box;
        }
    
        body {
          font-family: 'Inter', sans-serif;
          background: #f8f9fa !important;
          display: flex;
          justify-content: center;
          align-items: center;
          min-height: 100vh;
          margin: 0;
          padding: 20px;
          overflow-y: auto;
        }
        
        .form-container {
          background: white;
          padding: 25px;
          width: 100%;
          max-width: 440px;
        }
    
        .form-group {
          margin-bottom: 16px;
        }
    
        label {
          font-weight: bold;
          display: block;
          margin-bottom: 6px;
        }
    
        input[type="text"],
        input[type="url"],
        input[type="password"],
        select {
          width: 100%;
          padding: 7px;
         
          font-size: 14px;
          background-color: #f9f9f9;
        }
    
        .radio-group {
          display: flex;
          gap: 20px;
          margin-bottom: 16px;
        }
    
        .radio-group label {
          font-weight: normal;
          color: #494949;
          font-size: 15px;
        }
    
        button {
          width: 100%;
          padding: 12px;
          background: #007bff;
          color: white;
          font-weight: bold;
          border: none;
          border-radius: 6px;
          font-size: 16px;
          cursor: pointer;
        }
  </style>
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>
    <div class="form-container border border-1 border-light rounded-2 bg-white p-1 shadow-sm p-4">
        <form action="<?= base_url('reseller/login_handler');?>" method="POST">
            <?= csrf_field() ?>
            <h4 class="mb-3">Welcome to Reseller</h4>
            <div class="form-group">
                <label class="form-label fw-semibold mb-1">Username</label>
                <input type="text" name="user_login" id="user_login" class="form-control" placeholder="Enter your username" value="" autocomplete="off" required>
            </div>
            <div class="form-group">
                <label class="form-label fw-semibold mb-1">Password</label>
                <input type="password" name="nsofts_password_input" id="nsofts_password_input" class="form-control" placeholder="Enter your password" value="" autocomplete="off" required>
            </div>
            <?php if (!empty($settings['recaptcha_site_key']) && in_array($settings['is_recaptcha'], [true, "true", 1, "1"], true)) { ?>
                <div class="d-flex align-items-center justify-content-center mb-3 mt-6">
                    <div class="g-recaptcha" data-sitekey="<?= esc($settings['recaptcha_site_key'], 'attr') ?>"></div>
                </div>
            <?php } ?>
            <button class="btn btn-warning mt-1" type="submit">Login</button>
        </form>
    </div>

    <script src="<?= base_url('assets/js/jquery.min.js') ?>"></script>
    <script src="<?= base_url('assets/vendors/bootstrap/bootstrap.min.js') ?>"></script>
    <script src="<?= base_url('assets/vendors/notify/notify.min.js') ?>"></script>
    
     <?php
        if(!empty(session()->get('response_msg'))) {
            $message = session()->get('response_msg');
            $array_items = array('message' ,'class');
            session()->remove('response_msg',$array_items);
        ?>
            <script type="text/javascript">
                var _msg='<?=$message['message']?>';
                var _class='<?=$message['class']?>';
                _msg=_msg.replace(/(<([^>]+)>)/ig,"");
                $('.notifyjs-corner').empty();
                $.notify(
                _msg, 
                { position:"top right",className: _class} ); 
            </script>
     <?php } ?>
</body>
</html>
