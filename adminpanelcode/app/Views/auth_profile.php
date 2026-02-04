<?= $this-> include('templates/header');?>
<main id="nsofts_main">
    <div class="nsofts-container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb align-items-center">
                <li class="breadcrumb-item d-inline-flex"><a href="dashboard.php"><i class="ri-home-4-fill"></i></a></li>
                <li class="breadcrumb-item d-inline-flex active" aria-current="page"><?= isset($pageTitle) ? esc($pageTitle) : "" ?></li>
            </ol>
        </nav>
        <div class="row g-4">
            <div class="col-12">
                <div class="card h-100">
                    <div class="card-body p-4">
                        <h5 class="mb-3"><?= isset($pageTitle) ? esc($pageTitle) : "" ?></h5>
                        <form action="<?= base_url('/ns-admin/profile_handler');?>" name="addeditcategory" method="POST" enctype="multipart/form-data">
                            <?= csrf_field() ?>
                            <div class="mb-3 row">
                                <label class="col-sm-2 col-form-label">Username</label>
                                <div class="col-sm-10">
                                    <div class="nsofts-input-icon nsofts-input-icon--left">
                                        <label for="username" class="nsofts-input-icon__left">
                                            <i class="ri-user-line"></i>
                                        </label>
                                        <input type="text" name="username" placeholder="Enter your user name" class="form-control" value="<?= esc($row['username']); ?>" required>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-sm-2 col-form-label">Email</label>
                                <div class="col-sm-10">
                                    <div class="nsofts-input-icon nsofts-input-icon--left">
                                        <label for="email" class="nsofts-input-icon__left">
                                            <i class="ri-at-line"></i>
                                        </label>
                                        <input type="text" name="email" placeholder="Enter your email address" class="form-control" value="<?= esc($row['email']); ?>" required>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label for="" class="col-sm-2 col-form-label">Select Image</label>
                                <div class="col-sm-10">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <input type="file" name="image"  class="form-control" id="fileupload" accept="image/*">
                                            <p class="control-label-help hint_lbl">(Recommended resolution: 512x512)</p>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="fileupload_img" id="imagePreview">
                                                <?php if($row['image']!='') {?>
                                                    <img  type="image" src="<?= base_url('images/'.$row['image']) ?>" style="width: 50px;height: 50px" onerror="this.src='<?= base_url('assets/images/user_photo.png') ?>';" loading="lazy" alt="image"/>
                                                <?php } else { ?>
                                                    <img type="image" src="<?= base_url('assets/images/user_photo.png') ?>" style="width: 50px;height: 50px" loading="lazy" alt="image" />
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" name="submit" class="btn btn-primary" style="min-width: 120px;">Save to Change</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <div class="row g-4">
            <div class="col-12">
                <div class="card h-100">
                    <div class="card-body p-4">
                        <h5 class="mb-3">Change Password</h5>
                        <form action="<?= base_url('/ns-admin/profile_handler');?>" name="passwordprofile" method="POST" enctype="multipart/form-data">
                            <?= csrf_field() ?>
                            <div class="alert alert-warning alert-dismissible" role="alert">
                              <h6 class="alert-heading d-flex align-items-center fw-bold mb-1">Ensuer that these requirements are met</h6>
                              <p class="mb-0">Minimum 8 characters long, uppercase & symbol</p>
                            </div>
                            <div class="row g-3">
                              <div class="col-md-6">
                                    <div class="nsofts-input-icon nsofts-input-icon--left">
                                        <label for="register_password" class="nsofts-input-icon__left">
                                            <i class="ri-lock-line"></i>
                                        </label>
                                        <input type="text" name="register_password" id="register_password" class="form-control" placeholder="Enter your new password" autocomplete="off" required>
                                    </div>
                              </div>
                              <div class="col-md-6">
                                    <div class="nsofts-input-icon nsofts-input-icon--left">
                                        <label for="register_confirm_password" class="nsofts-input-icon__left">
                                            <i class="ri-lock-line"></i>
                                        </label>
                                        <input type="text" name="register_confirm_password" id="register_confirm_password" class="form-control" placeholder="Enter your new confirm password" autocomplete="off" required>
                                    </div>
                              </div>
                            </div>
                            <button type="submit" name="submit_password" class="btn btn-primary mt-3" style="min-width: 120px;">Save to Change Password</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?= $this-> include('templates/footer');?>