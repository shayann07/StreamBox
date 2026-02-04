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
                        <form action="<?= base_url('/ns-admin/create_extream_handler');?>" name="addeditcategory" method="POST" enctype="multipart/form-data">
                            <?= csrf_field() ?>
                            <input  type="hidden" name="dns_id" value="<?= (isset($dns_id)) ? esc($dns_id) : '' ?>" />
                            <div class="mb-3 row">
                                <label class="col-sm-2 col-form-label">Title</label>
                                <div class="col-sm-10">
                                    <div class="nsofts-input-icon nsofts-input-icon--left">
                                        <label for="dns_title" class="nsofts-input-icon__left">
                                            <i class="ri-text"></i>
                                        </label>
                                        <input type="text" name="dns_title" class="form-control" placeholder="Enter your title" value="<?= isset($dns_id) ? esc($row['dns_title']) : '' ?>" required>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-sm-2 col-form-label">http://url_here.com:port</label>
                                <div class="col-sm-10">
                                    <div class="nsofts-input-icon nsofts-input-icon--left">
                                        <label for="dns_base" class="nsofts-input-icon__left">
                                            <i class="ri-links-line"></i>
                                        </label>
                                        <input type="text" name="dns_base" class="form-control" placeholder="Enter your dns" value="<?= isset($dns_id) ? esc($row['dns_base']) : '' ?>" required>
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