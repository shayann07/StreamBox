<?= $this-> include('templates/header_data');?>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<main class="d-flex justify-content-center align-items-center py-5 min-vh-100">
    <div class="container">
        <div class="col-xl-4 col-lg-5 col-md-7 col-sm-9 mx-auto">
            <div class="nsofts-auth position-relative">
                <img src="<?= base_url('assets/images/pattern-1.svg') ?>" class="nsofts-auth__pattern-1 position-absolute" alt="">
                <img src="<?= base_url('assets/images/pattern-2.svg') ?>" class="nsofts-auth__pattern-2 position-absolute" alt="">
                <div class="card position-relative">
                    <div class="card-body px-4 py-5">
                        <div class="mb-4">
                            <h5>Welcome to <?= esc($settings['app_name']) ?></h5>
                            <p>Please sign-in to your account and start the adventure.</p>
                        </div>
                        <form action="<?= base_url('/ns-admin/login_handler');?>" method="POST">
                            <?= csrf_field() ?>
                            <div class="mb-3">
                                <label for="email" class="form-label fw-semibold">Email or Username</label>
                                <div class="nsofts-input-icon nsofts-input-icon--left">
                                    <label for="email" class="nsofts-input-icon__left">
                                        <i class="ri-user-line"></i>
                                    </label>
                                    <input type="text" name="user_login" id="user_login" value="" class="form-control" autocomplete="off" placeholder="Enter your email or username" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <label for="nsofts_password_input" class="form-label fw-semibold mb-0">Password</label>
                                    <a href="<?= base_url('ns-admin/forgot-password') ?>" class="text-decoration-none">Forgot Password?</a>
                                </div>
                                <div class="nsofts-input-icon nsofts-input-icon--both">
                                    <label for="nsofts_password_input" class="nsofts-input-icon__left">
                                        <i class="ri-door-lock-line"></i>
                                    </label>
                                    <input type="password" name="nsofts_password_input" id="nsofts_password_input" value="" class="form-control" autocomplete="off" placeholder="Enter your password" required>
                                    <button type="button" id="nsofts_password_toggler" class="nsofts-input-icon__right btn p-0 border-0 lh-1">
                                        <i class="ri-eye-line nsofts-eye-open"></i>
                                        <i class="ri-eye-off-line nsofts-eye-close d-none"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="remember">
                                    <label class="form-check-label" for="remember">
                                        Remember Me
                                    </label>
                                </div>
                            </div>
                            <?php if (!empty($settings['recaptcha_site_key']) && in_array($settings['is_recaptcha'], [true, "true", 1, "1"], true)) { ?>
                                <div class="d-flex align-items-center justify-content-center mb-4">
                                    <div class="g-recaptcha" data-sitekey="<?= esc($settings['recaptcha_site_key'], 'attr') ?>"></div>
                                </div>
                            <?php } ?>
                            <div class="mb-3">
                                <button type="submit" class="btn btn-primary btn-lg w-100">Sign in</button>
                            </div>
                            <p class="text-center">New on our platform? <a href="<?= base_url('ns-admin/register') ?>" class="text-decoration-none">Create an account</a></p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?= $this-> include('templates/footer_data');?>