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
                        <form action="<?= base_url('/ns-admin/create_ads_handler');?>" name="addeditcategory" method="POST" enctype="multipart/form-data">
                            <?= csrf_field() ?>
                            <input  type="hidden" name="ads_id" value="<?= (isset($ads_id)) ? esc($ads_id) : '' ?>" />
                            <div class="mb-3 row">
                                <label class="col-sm-2 col-form-label">Ads Type</label>
                                <div class="col-sm-10">
                                    <select name="ads_type" id="ads_type" class="form-select" required>
                                        <?php if(isset($ads_id)){ ?>
                                            <option value="popup" <?php if($row['ads_type'] == 'popup'){?>selected<?php }?>>Popup</option>	       
                                        <?php } else { ?>
                                            <option value="popup">Popup</option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-sm-2 col-form-label">URL Action Type</label>
                                <div class="col-sm-10">
                                    <select name="ads_redirect_type" id="ads_redirect_type" class="form-select" required>
                                        <?php if(isset($ads_id)){ ?>
                                            <option value="external" <?php if($row['ads_redirect_type'] == 'external'){?>selected<?php }?>>External URL</option>
                                            <option value="internal" <?php if($row['ads_redirect_type'] == 'internal'){?>selected<?php }?>>Internal URL</option>	       
                                        <?php } else { ?>
                                            <option value="external">External URL</option>
                                            <option value="internal">Internal URL</option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-sm-2 col-form-label">Title</label>
                                <div class="col-sm-10">
                                    <div class="nsofts-input-icon nsofts-input-icon--left">
                                        <label for="ads_title" class="nsofts-input-icon__left">
                                            <i class="ri-text"></i>
                                        </label>
                                        <input type="text" name="ads_title" class="form-control" placeholder="Enter your title" value="<?= isset($ads_id) ? esc($row['ads_title']) : '' ?>" required>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-sm-2 col-form-label">URL</label>
                                <div class="col-sm-10">
                                    <div class="nsofts-input-icon nsofts-input-icon--left">
                                        <label for="ads_title" class="nsofts-input-icon__left">
                                            <i class="ri-links-line"></i>
                                        </label>
                                        <input type="text" name="ads_redirect_url" class="form-control" placeholder="Enter your url" value="<?= isset($ads_id) ? esc($row['ads_redirect_url']) : '' ?>" required>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-sm-2 col-form-label">Select Image</label>
                                <div class="col-sm-10">
                                    <input type="file" class="form-control" id="fileupload" name="ads_image" accept="image/*" <?php if(!isset($ads_id)){?>required<?php } ?>>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-sm-2 col-form-label">&nbsp;</label>
                                <div class="col-sm-10">
                                    <div class="fileupload_img" id="imagePreview">
                                        <?php if(isset($ads_id)) {?>
                                            <img src="<?= base_url('images/'.$row['ads_image']) ?>" class="col-sm-4 img-thumbnail" onerror="this.src='<?= base_url('assets/images/600x300.jpg') ?>';"  loading="lazy" alt="">
                                        <?php }else{?>
                                            <img class="col-sm-4 img-thumbnail" type="image" src="<?= base_url('assets/images/600x300.jpg') ?>" alt="image" />
                                        <?php } ?>
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