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
                        <form action="<?= base_url('/ns-admin/onesignal_handler');?>" name="addeditone" method="POST" enctype="multipart/form-data">
                            <?= csrf_field() ?>
                            <div class="mb-3 row">
                                <label class="col-sm-2 col-form-label">Title</label>
                                <div class="col-sm-10">
                                    <input type="text" name="notification_title" id="notification_title" value="" class="form-control" required>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-sm-2 col-form-label">Sub Message</label>
                                <div class="col-sm-10">
                                    <input type="text" name="notification_des" id="notification_des" value="" class="form-control" required>
                                </div>
                            </div>
                             <div class="mb-3 row">
                                <label class="col-sm-2 col-form-label">Description</label>
                                <div class="col-sm-10">
                                    <textarea name="notification_msg" id="notification_msg" rows="5" class="nsofts-editor"></textarea>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-sm-2 col-form-label">&nbsp;</label>
                                <div class="col-sm-10">
                                    <button type="submit" name="submit" class="btn btn-primary" style="min-width: 120px;">Send</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?= $this-> include('templates/footer');?>