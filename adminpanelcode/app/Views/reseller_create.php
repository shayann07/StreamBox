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
        
        .form-control {
            background-color: #f8f9fa;
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
        .radio-group label:hover {
            color: #ff4040;
        }
        .btnLogout {
          width: 100%;
        }
  </style>
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>
    <div class="form-container border border-1 border-light rounded-2 bg-white p-1 shadow-sm p-4">
        <form action="<?= base_url('/reseller/create_handler');?>" method="POST">
            <?= csrf_field() ?>
            <div class="form-group radio-group">
                <label><input type="radio" name="stream_type" value="m3u" checked> M3U Link</label>
                <label><input type="radio" name="stream_type" value="xtream">Xtream Codes or 1-Stream</label>
            </div>
            <div id="m3uFields">
                <div class="form-group">
                    <label>M3U Link</label>
                    <input type="text" name="m3u_link" id="m3u_link" class="form-control" placeholder="http://.../get.php?username=...&password=..." value="" autocomplete="off">
                </div>
            </div>
            <div id="xtreamFields" style="display:none;">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" id="username" class="form-control" placeholder="Enter username" value="" autocomplete="off">
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" id="password" class="form-control" placeholder="Enter password" value="" autocomplete="off">
                </div>
                <div class="form-group">
                    <label>Server URL</label>
                    <input type="text" name="server" id="server" class="form-control" placeholder="http://example.com:8080" value="" autocomplete="off">
                </div>
            </div>
            <div class="form-group">
                <label>Choose Method</label>
                <select name="actionSelect" id="actionSelect" class="form-control">
                    <option value="get_code">Get Activation Code</option>
                    <option value="add_device">Add Your Device ID</option>
                </select>
            </div>
            <div id="deviceIdField" class="form-group" style="display: none;">
                <label>Device ID</label>
                <input type="text" name="device_id" id="device_id" class="form-control" placeholder="Enter your device id" value="" autocomplete="off">
            </div>
            <button class="btn btn-warning mt-1" type="submit">Create User</button>
            <a href="<?= base_url('reseller/logout') ?>" class="btn btn-danger mt-3 btnLogout" type="submit">Logout</a>
        </form>
    </div>

    <script src="<?= base_url('assets/js/jquery.min.js') ?>"></script>
    <script src="<?= base_url('assets/vendors/bootstrap/bootstrap.min.js') ?>"></script>
    <script src="<?= base_url('assets/vendors/notify/notify.min.js') ?>"></script>
    
    <script>
      const methodRadios = document.querySelectorAll('input[name="stream_type"]');
      const m3uFields = document.getElementById('m3uFields');
      const xtreamFields = document.getElementById('xtreamFields');
      const actionSelect = document.getElementById('actionSelect');
      const deviceIdField = document.getElementById('deviceIdField');
      
      methodRadios.forEach(radio => {
        radio.addEventListener('change', () => {
          if (radio.value === 'm3u') {
            m3uFields.style.display = 'block';
            xtreamFields.style.display = 'none';
          } else {
            m3uFields.style.display = 'none';
            xtreamFields.style.display = 'block';
          }
        });
      });
      
      actionSelect.addEventListener('change', () => {
        deviceIdField.style.display = actionSelect.value === 'add_device' ? 'block' : 'none';
      });
    </script>
    
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
