<?= $this-> include('templates/header_data');?>
<main class="d-flex justify-content-center align-items-center py-5 min-vh-100">
    <div class="container">
        <div class="col-xl-4 col-lg-5 col-md-7 col-sm-9 mx-auto">
            <div class="nsofts-auth position-relative">
                <img src="<?= base_url('assets/images/pattern-1.svg') ?>" class="nsofts-auth__pattern-1 position-absolute" alt="">
                <img src="<?= base_url('assets/images/pattern-2.svg') ?>" class="nsofts-auth__pattern-2 position-absolute" alt="">
                <div class="card position-relative">
                    <div class="card-body px-4 py-5">
                        <form action="<?= base_url('/ns-admin/forgot_handler') ?>" method="POST">
                            <?= csrf_field() ?>
                            <div class="mb-4">
                                <h5>Forgot password!</h5>
                                <p>Enter your register email to get password on your mail id.</p>
                            </div>
                            <div class="mb-3">
                                <div class="nsofts-input-icon nsofts-input-icon--left">
                                    <label for="email" class="nsofts-input-icon__left">
                                        <i class="ri-at-line"></i>
                                    </label>
                                    <input type="text" name="forgot_email" id="forgot_email"  class="form-control" autocomplete="off" placeholder="Enter your email" required>
                                </div>
                            </div>
                            <div class="mb-4">
                                <button  type="submit" name="submit" class="btn btn-primary btn-lg w-100">Send Password to Email</button>
                            </div>
                            <p class="text-center">Go to login page? <a href="<?= base_url('ns-admin/login') ?>" class="text-decoration-none">Login</a></p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?= $this-> include('templates/footer_data');?>