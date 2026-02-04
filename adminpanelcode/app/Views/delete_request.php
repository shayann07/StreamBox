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
    <link id="theme-style" href="<?= base_url('assets/vendors/remixicon/remixicon.css') ?>" as="style" rel="stylesheet" />
    <!-- Main style -->
    <link id="theme-style" href="<?= base_url('assets/css/styles.css?v=1.0.0') ?>" as="style" rel="stylesheet" />
    <?php if (!empty($settings['site_direction']) && $settings['site_direction'] == '1'): ?>
        <link rel="stylesheet" href="<?= base_url('assets/css/rtl.css?v=1.0.0') ?>" type="text/css">
    <?php endif; ?>
   
    <!-- SET GLOBAL BASE URL -->
    <script>var base_url = '<?= base_url() ?>';</script>
    
</head>
<body>
<style>
    p {
        color: #5e5e5e;
    }
    h1, h2, h3, h4, h5, h6 {
    	color: #343434;
    }
</style>
<main class="d-flex justify-content-center align-items-center py-5 min-vh-100">
    <div class="container">
        <div class="col-xl-4 col-lg-5 col-md-7 col-sm-9 mx-auto">
            <div class="nsofts-auth position-relative">
                <img src="<?= base_url('assets/images/pattern-1.svg') ?>" class="nsofts-auth__pattern-1 position-absolute" alt="">
                <img src="<?= base_url('assets/images/pattern-2.svg') ?>" class="nsofts-auth__pattern-2 position-absolute" alt="">
                <div class="card position-relative">
                    <?php if ($createType == 'create'){ ?>
                        <form action="<?= base_url('/create_extream_handler');?>" name="datadeletion" method="POST" enctype="multipart/form-data">
                            <?= csrf_field() ?>
                            <div class="card-body px-4 py-4">
                                <div class="container mb-3">
                                    <div class="row justify-content-start">
                                        <div class="col-3 p-0 mb-2">
                                         <img src="<?= base_url('images/' . esc($settings['app_logo'] ?? 'favicon.ico')) ?>"  class="img-thumbnail" alt="image">
                                        </div>
                                        <div class="col-9">
                                            <h5>Welcome to <?= esc($settings['app_name']) ?>!</h5>
                                            <p class="m-0 p-0"> By submitting this you can completely delete you account and collected data.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label fw-semibold">Email or Username</label>
                                    <div class="nsofts-input-icon nsofts-input-icon--left">
                                        <label for="email" class="nsofts-input-icon__left">
                                            <i class="ri-user-line"></i>
                                        </label>
                                        <input type="text" name="user_email" id="user_email"  class="form-control" autocomplete="off" placeholder="Enter your email or username" required>
                                    </div>
                                </div>
                                <div class="mb-3 border-bottom"></div>
                                <div class="mb-2">
                                    <label class="form-check-label">
                                        Confirming to Delete your collected data
                                    </label>
                                </div>
                                <div class="mb-4">
                                    <button  type="submit" name="submit_clear" class="btn btn-warning btn-lg w-100">Clear Data</button>
                                </div>
                                <div class="mb-3 border-bottom"></div>
                                <div class="mb-2">
                                    <label class="form-check-label">
                                        Confirming to Delete your data and Account
                                    </label>
                                </div>
                                <div class="mb-3">
                                    <button  type="submit" name="submit_delete" class="btn btn-danger btn-lg w-100">Delete Account & Clear Data</button>
                                </div>
                                <label class="form-check-label">
                                   After submitting this you request will be reviewed and your data will be removed with in 7 days, After that you cant restore it.
                                </label>
                            </div>
                        </form>
                    <?php } else { ?>
                        <style>
                            body {
                                background-color: #919191;
                            }
                            .card-body {
                                border-radius: 5px;
                                background-color: #ffffff;
                                box-shadow: 0 10px 20px -10px #25252542;
                                -webkit-box-shadow: 0 10px 20px -10px #25252542;
                            }
                        </style>
                        <div class="card-body px-4 py-3">
                            <h4>Submitted successfully.</h4>
                            <div class="mb-2 border-bottom"></div>
                            <label class="form-check-label">
                                you request will be reviewed and your data will be removed with in 7 days, After that you cant restore it.
                            </label>
                            <label class="form-check-label">
                                thank you.
                            </label>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</main>
    <!-- Vendor scripts -->
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