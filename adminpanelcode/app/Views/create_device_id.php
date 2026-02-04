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
                        <h5 class="mb-4"><?= isset($pageTitle) ? esc($pageTitle) : "" ?></h5>
                        <form action="<?= base_url('/ns-admin/create_device_handler');?>" name="addeditcategory" method="POST" enctype="multipart/form-data">
                            <?= csrf_field() ?>
                            <input  type="hidden" name="device" value="<?= (isset($device_id)) ? esc($device_id) : '' ?>" />
                            <div class="mb-3 row">
                                <label class="col-sm-2 col-form-label">Panel Type</label>
                                <div class="col-sm-10">
                                        <select name="user_type" id="user_type" class="form-select" required>
                                            <?php if(isset($device_id)){ ?>
                                                <option value="xui" <?php if($row['user_type']=='xui'){?>selected<?php }?>>Xtream Codes or XUI</option> 
                                                <option value="stream" <?php if($row['user_type']=='stream'){?>selected<?php }?>>1-Stream</option> 
                                            <?php } else { ?>
                                                <option value="xui">Xtream Codes or XUI</option> 
                                                <option value="stream">1-Stream</option>
                                            <?php } ?> 
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-sm-2 col-form-label">Username</label>
                                <div class="col-sm-10">
                                    <div class="nsofts-input-icon nsofts-input-icon--left">
                                        <label for="user_name" class="nsofts-input-icon__left">
                                            <i class="ri-user-line"></i>
                                        </label>
                                        <input type="text" name="user_name" placeholder="Enter your user name" class="form-control" value="<?= isset($device_id) ? esc($row['user_name']) : '' ?>" required>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-sm-2 col-form-label">Password</label>
                                <div class="col-sm-10">
                                    <div class="nsofts-input-icon nsofts-input-icon--left">
                                        <label for="user_password" class="nsofts-input-icon__left">
                                            <i class="ri-lock-line"></i>
                                        </label>
                                        <input type="text" name="user_password" id="user_password" placeholder="Enter your password" class="form-control" value="<?= isset($device_id) ? esc($row['user_password']) : '' ?>" required>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-sm-2 col-form-label">DNS</label>
                                <div class="col-sm-10">
                                    <div class="nsofts-input-icon nsofts-input-icon--left">
                                        <label for="user_password" class="nsofts-input-icon__left">
                                            <i class="ri-links-line"></i>
                                        </label>
                                        <input type="text" name="dns_base" id="dns_base" placeholder="Enter your dns url" class="form-control" value="<?= isset($device_id) ? esc($row['dns_base']) : '' ?>" required>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-sm-2 col-form-label">Device ID</label>
                                <div class="col-sm-10">
                                    <div class="nsofts-input-icon nsofts-input-icon--left">
                                        <label for="user_password" class="nsofts-input-icon__left">
                                            <i class="ri-cpu-line"></i>
                                        </label>
                                        <input type="text" name="device_id" id="device_id" placeholder="Enter your device id" class="form-control" value="<?= isset($device_id) ? esc($row['device_id']) : '' ?>" required>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" name="submit" class="btn btn-primary" style="min-width: 120px;"><?= esc($pageSave) ?></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?= $this-> include('templates/footer');?>