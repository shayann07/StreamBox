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
                        <form action="<?= base_url('/ns-admin/register_handler');?>" method="POST" enctype="multipart/form-data">
                            <?= csrf_field() ?>
                            <div class="mb-4">
                                <h5>Welcome to <?= esc($settings['app_name']) ?>!</h5>
                                <p>Please Register to your account</p>
                            </div>
                            <div class="mb-3">
                                <div class="nsofts-input-icon nsofts-input-icon--left">
                                     <select name="admin_type" id="admin_type" class="form-select" required>
                                        <option value="2">RESSELLER</option>
                                        <option value="1">ADMIN</option>
                                        <option value="0">EDITOR</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="nsofts-input-icon nsofts-input-icon--left">
                                    <label for="email" class="nsofts-input-icon__left">
                                        <i class="ri-user-line"></i>
                                    </label>
                                    <input type="text" name="register_name" id="register_name"  class="form-control" autocomplete="off" placeholder="Enter your username" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="nsofts-input-icon nsofts-input-icon--left">
                                    <label for="email" class="nsofts-input-icon__left">
                                        <i class="ri-at-line"></i>
                                    </label>
                                    <input type="text" name="register_email" id="register_email"  class="form-control" autocomplete="off" placeholder="Enter your email " required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="nsofts-input-icon nsofts-input-icon--left">
                                    <label for="email" class="nsofts-input-icon__left">
                                         <i class="ri-lock-2-line"></i>
                                    </label>
                                    <input type="text" name="register_password" id="register_password"  class="form-control" autocomplete="off" placeholder="Enter your password " required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="nsofts-input-icon nsofts-input-icon--left">
                                    <label for="email" class="nsofts-input-icon__left">
                                         <i class="ri-lock-2-line"></i>
                                    </label>
                                    <input type="text" name="register_confirm_password" id="register_confirm_password"  class="form-control" autocomplete="off" placeholder="Enter your confirm password " required>
                                </div>
                            </div>
                            <?php if (!empty($settings['recaptcha_site_key']) && in_array($settings['is_recaptcha'], [true, "true", 1, "1"], true)) { ?>
                                <div class="d-flex align-items-center justify-content-center mb-4">
                                    <div class="g-recaptcha" data-sitekey="<?= esc($settings['recaptcha_site_key']); ?>"></div>
                                </div>
                            <?php } ?>
                            <div class="mb-4">
                                <button  type="submit" name="submit" class="btn btn-primary btn-lg w-100">Register</button>
                            </div>
                            <p class="text-center">Already have an account? <a href="<?= base_url('ns-admin/login') ?>" class="text-decoration-none">Login</a></p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?= $this-> include('templates/footer_data');?>