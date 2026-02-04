<?= $this-> include('templates/header');?>
<main id="nsofts_main">
    <div class="nsofts-container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb align-items-center">
                <li class="breadcrumb-item d-inline-flex"><a href="dashboard.php"><i class="ri-home-4-fill"></i></a></li>
                <li class="breadcrumb-item d-inline-flex active" aria-current="page"><?= isset($pageTitle) ? esc($pageTitle) : "" ?></li>
            </ol>
        </nav>
        <div class="row g-4 mb-1">
            <div class="col-12">
                <div class="card h-100">
                    <div class="card-body p-4">
                        <h5 class="mb-3"><?= isset($pageTitle) ? esc($pageTitle) : "" ?></h5>
                        <form action="<?= base_url('/ns-admin/verification_handler');?>" name="addverify" method="POST" enctype="multipart/form-data">
                            <?= csrf_field() ?>
                            
                            <?php if (!empty(session()->get('response_msg'))) : 
                                $msg = session()->get('response_msg');
                                $alertClass = ($msg['class'] == 'success') ? 'alert-primary' : 'alert-danger';
                            ?>
                                <div class="alert <?= $alertClass ?> alert-dismissible fade show" role="alert">
                                    <strong><?= esc($msg['message']) ?></strong>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>
                            
                            <div class="mb-3 row">
                                <label class="col-sm-2 col-form-label">Envato Username</label>
                                <div class="col-sm-10">
                                    <div class="nsofts-input-icon nsofts-input-icon--left">
                                        <label for="envato_buyer_name" class="nsofts-input-icon__left">
                                            <i class="ri-user-line"></i>
                                        </label>
                                        <input type="text" name="envato_buyer_name" class="form-control" placeholder="Enter your envato user name" value="<?= esc($settings['envato_buyer_name']) ?>" autocomplete="off" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3 row">
                                <label class="col-sm-2 col-form-label">Envato Purchase Code</label>
                                <div class="col-sm-10">
                                    <div class="nsofts-input-icon nsofts-input-icon--left">
                                        <label for="envato_purchase_code" class="nsofts-input-icon__left">
                                            <i class="ri-key-line"></i>
                                        </label>
                                        <input type="text" name="envato_purchase_code"class="form-control" placeholder="Enter your item purchase code" value="<?= esc($settings['envato_purchase_code']) ?>" autocomplete="off" required>
                                    </div>
                                    <small id="sh-text1" class="form-text text-muted"><a style="color: #f44336c7;" href="https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code" target="_blank">Where Is My Purchase Code?</a></small>
                                </div>
                            </div>
                            
                            <div class="mb-3 row">
                                <label class="col-sm-2 col-form-label">Envato Api Key</label>
                                <div class="col-sm-10">
                                    <div class="nsofts-input-icon nsofts-input-icon--left">
                                        <label for="envato_api_key" class="nsofts-input-icon__left">
                                            <i class="ri-shield-keyhole-line"></i>
                                        </label>
                                        <input type="text" name="envato_api_key" class="form-control" placeholder="<?= esc($settings['envato_api_key']) ?>"  disabled readonly>
                                    </div>
                                    <small id="sh-text1" class="form-text text-muted col-md-6" style="padding: 0px;">Click the Save button This key will be generated automatically.</small>
                                </div>
                            </div>
                            
                            <div class="mb-3 row">
                                <label class="col-sm-2 col-form-label">Android Application ID</label>
                                <div class="col-sm-10">
                                    <div class="nsofts-input-icon nsofts-input-icon--left">
                                        <label for="envato_package_name" class="nsofts-input-icon__left">
                                            <i class="ri-android-line"></i>
                                        </label>
                                        <input type="text" name="envato_package_name"class="form-control" placeholder="Enter your android application id" value="<?= esc($settings['envato_package_name']) ?>" autocomplete="off" required>
                                    </div>
                                    <small id="sh-text1" class="form-text text-muted">(More info in Android Doc)</small>
                                </div>
                            </div>
                            
                            <button type="submit" name="submit" class="btn btn-primary" style="min-width: 120px;">Verify</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?= $this-> include('templates/footer');?>