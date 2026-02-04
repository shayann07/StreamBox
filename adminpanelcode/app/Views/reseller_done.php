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
          width: 100%;
          max-width: 440px;
        }
        
        .form-group {
          margin-bottom: 16px;
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
        
        .reseller{
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .btn{
            width: 100%;
            padding: 12px;
        }
        
        p {
            margin-top: 0;
            margin-bottom: 4px;
        }
        
        .pb-clipboard {
            display: -webkit-flex;
            display: -ms-flex;
            display: flex;
            -webkit-align-items: center;
            align-items: center;
            border-radius: 4px;
            padding: 8px 16px;
        }
        
        .pb-clipboard .pb-clipboard__url {
            flex: 1;
            overflow-wrap: break-word;
            word-wrap: break-word;
            -webkit-hyphens: auto;
            -ms-hyphens: auto;
            -moz-hyphens: auto;
            hyphens: auto;
        }
        
        .pb-clipboard .pb-clipboard__link {
            color: inherit;
            display: inline-flex;
            flex-shrink: 0;
        }
        
        .pb-clipboard .pb-clipboard__link svg {
            width: 16px;
            height: 16px;
            color: #ff5500;
        }

  </style>
</head>
<body>
    <div class="form-container">
        <?php if(!empty(session()->get('response_msg'))) {
            $message = session()->get('response_msg');
        ?>
            <div class="border border-1 border-light rounded-2 bg-white p-1 shadow-sm p-4">
                <div class="pb-clipboard bg-light">
                    <span class="pb-clipboard__url"><span id="clipboard_policy"><?=$message['return_code']?></span></span>
                    <a class="pb-clipboard__link btn_policy" href="javascript:void(0);" data-clipboard-action="copy" data-clipboard-target="#clipboard_base_url" data-bs-toggle="tooltip" data-bs-placement="top" title="Copy">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1" />
                        </svg>
                    </a>
                </div>
            </div>
        <?php } ?>
        <a class="mt-3 reseller" href="<?= base_url('reseller') ?>">Back Reseller</a> 
    </div>
    <script src="<?= base_url('assets/js/jquery.min.js') ?>"></script>
    <script src="<?= base_url('assets/vendors/bootstrap/bootstrap.min.js') ?>"></script>
    <script src="<?= base_url('assets/vendors/notify/notify.min.js') ?>"></script>
    
    <script type="text/javascript">
    
    
        function copyToClipboard(el) {
            var text = el.innerText;
            
            if (window.clipboardData && window.clipboardData.setData) {
                return window.clipboardData.setData("Text", text);
                
            } else if (document.queryCommandSupported && document.queryCommandSupported("copy")) {
                var textarea = document.createElement("textarea");
                textarea.value = text;
                textarea.style.position = "fixed";  // Prevent scrolling to bottom of page in Microsoft Edge.
                el.appendChild(textarea);
                textarea.select();
                try {
                    return document.execCommand('copy');
                } catch (ex) {
                    console.warn("Copy to clipboard failed.", ex);
                    return prompt("Copy to clipboard: Ctrl+C, Enter", text);
                } finally {
                    el.removeChild(textarea);
                }
            }
        }
    
        $(document).ready(function(event) {
            $(document).on("click", ".btn_policy", function(e) {
                var el = document.getElementById('clipboard_policy');
                var successful = copyToClipboard(el);
                if (successful) {
                    $.notify('Copied!', { position:"top right",className: 'success'} );
                } else {
                    $.notify('Whoops, not copied!', { position:"top right",className: 'error'} );
                }
            });
        });
        
    </script>
    
    <?php
        if(!empty(session()->get('response_msg'))) {
            $message = session()->get('response_msg');
            $array_items = array('message' ,'class', 'return_code');
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