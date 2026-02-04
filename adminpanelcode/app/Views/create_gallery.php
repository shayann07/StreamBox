<?= $this-> include('templates/header');?>
<main id="nsofts_main">
    <div class="nsofts-container">
        <div class="card h-100">
            <div class="card-top d-md-inline-flex align-items-center justify-content-between py-3 px-4">
                <div class="d-inline-flex align-items-center text-decoration-none fw-semibold">
                    <span class="ps-2 lh-1"><?= isset($pageTitle) ? esc($pageTitle) : "" ?></span>
                </div>
                <div class="d-flex mt-2 mt-md-0">
                </div>
            </div>
            <div class="card-body p-4">
                <form action="<?= base_url('/ns-admin/create_gallery_handler');?>" name="addeditcategory" method="POST" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <input type="hidden" name="type" value="<?= isset($_GET['type']) ? esc($_GET['type']) : '' ?>" />
                    <div class="mb-3 row">
                        <label class="col-sm-2 col-form-label">Select Image</label>
                        <div class="col-sm-10">
                            <input type="file" class="form-control-file" name="poster_image"   accept=".png, .jpg, .JPG .PNG" onchange="fileValidation()" id="fileupload" <?php if(!isset($_GET['cat_id'])){?>required<?php } ?>>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label class="col-sm-2 col-form-label">&nbsp;</label>
                        <div class="col-sm-10">
                            <button type="submit" name="submit" class="btn btn-primary" style="min-width: 120px;">Create Poster</button>
                        </div>
                    </div>
                </form>
                <?php if(!empty($result)){ ?>
                    <div class="row g-4">
                         <?php $i=0; foreach($result as $row){ ?>
                            <div class="col-lg-6 col-sm-6 col-xs-12">
                                <div class="nsofts-image-card">
                                    <div class="nsofts-image-card__cover" style="width: 100%; height: 250px; min-width: 100%; min-height: 100%; max-width: 100%;">
                                        <div class="nsofts-switch d-flex align-items-center enable_disable" data-bs-toggle="tooltip" data-bs-placement="top" title="Enable / Disable">
                                            <input type="checkbox" id="enable_disable_check_<?= $i ?>" data-action="<?= base_url('ns-admin/status-gallery/'.$row['id']) ?>" data-column="status"  class="cbx hidden btn_enable_disable" <?php if ($row['status'] == 1) { echo 'checked'; } ?>>
                                            <label for="enable_disable_check_<?= $i ?>" class="nsofts-switch__label"></label>
                                        </div>
                                        <img src="<?= base_url('images/'.$row['poster_image']) ?>" alt="" >
                                    </div>
                                    <div class="nsofts-image-card__content">
                                        <div class="position-relative">
                                            <div class="d-flex align-items-center justify-content-between nsofts-image-card__content__text">
                                                <span class="d-block text-truncate fs-6 fw-semibold pe-2">Banner</span>
                                                <div class="nsofts-image-card__option d-flex">
                                                    <a href="javascript:void(0)" class="btn border-0 text-danger btn_delete" data-action="<?= base_url('ns-admin/delete-gallery/'.$row['id']) ?>"  data-bs-toggle="tooltip" data-bs-placement="top" title="Delete"><i class="ri-delete-bin-fill"></i></a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php $i++; } ?>
                    </div>
                <?php } else { ?>
                   <h3 class="text-center p-5">No data found</h3>
                <?php } ?>
                </nav>
            </div>
        </div>
    </div>
</main>
<?= $this-> include('templates/footer');?>