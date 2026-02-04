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
                        <form action="<?= base_url('/ns-admin/create_select_handler');?>" name="addeditselect" method="POST" enctype="multipart/form-data">
                            <?= csrf_field() ?>
                            <input  type="hidden" name="select_id" value="<?= (isset($select_id)) ? esc($select_id) : '' ?>" />
                            <div class="mb-3 row">
                                <label class="col-sm-2 col-form-label">Action URL</label>
                                <div class="col-sm-10">
                                    <select name="redirect_type" id="redirect_type" class="form-select" required>
                                        <?php if(isset($select_id)){ ?>
                                            <option value="internal" <?php if($row['redirect_type'] == 'internal'){?>selected<?php }?>>Internal URL</option>	
                                            <option value="whatsapp" <?php if($row['redirect_type'] == 'whatsapp'){?>selected<?php }?>>Whatsapp</option>
                                            <option value="telegram" <?php if($row['redirect_type'] == 'telegram'){?>selected<?php }?>>Telegram</option>
                                            <option value="external" <?php if($row['redirect_type'] == 'external'){?>selected<?php }?>>External URL</option>
                                        <?php } else { ?>
                                            <option value="internal">Internal</option>
                                            <option value="whatsapp">Whatsapp</option>
                                            <option value="telegram">Telegram</option>
                                            <option value="external">External</option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-sm-2 col-form-label">Title</label>
                                <div class="col-sm-10">
                                    <div class="nsofts-input-icon nsofts-input-icon--left">
                                        <label for="title" class="nsofts-input-icon__left">
                                            <i class="ri-text"></i>
                                        </label>
                                        <input type="text" name="title" class="form-control" placeholder="Enter your title" value="<?= isset($select_id) ? esc($row['title']) : '' ?>" required>
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
                                        <input type="text" name="page_data" class="form-control" placeholder="Enter your url" value="<?= isset($select_id) ? esc($row['page_data']) : '' ?>" required>
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