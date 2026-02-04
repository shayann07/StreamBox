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
                        <form action="<?= base_url('/ns-admin/create_token_handler');?>" name="addedittoken" method="POST" enctype="multipart/form-data">
                            <?= csrf_field() ?>
                            <input type="hidden" name="token_id" value="<?= (isset($token_id)) ? esc($token_id) : '' ?>" />
                            <div class="mb-3 row">
                                <label class="col-sm-2 col-form-label">Token name</label>
                                <div class="col-sm-10">
                                    <div class="nsofts-input-icon nsofts-input-icon--left">
                                        <label for="user_name" class="nsofts-input-icon__left">
                                            <i class="ri-user-line"></i>
                                        </label>
                                        <input type="text" name="token_name" placeholder="Enter your token name" class="form-control" value="<?php if(isset($token_id)){ echo esc($row['token_name']); } ?>" required>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <input type="hidden" name="token_code" id="token_code" value="<?= isset($token_id) ? esc($row['token_code']) : esc($activationCode) ?>" required>
                                <label class="col-sm-2 col-form-label">Token Code</label>
                                <div class="col-sm-10">
                                    <div class="pb-clipboard mb-2">
                                        <span class="pb-clipboard__url"><a class="fw-bold text-decoration-none" style="color: var(--ns-primary);"></a><span id="clipboard_delete"><?= isset($token_id) ? esc($row['token_code']) : esc($activationCode) ?></span></span>
                                        <a class="pb-clipboard__link btn_delete_ac" href="javascript:void(0);" data-clipboard-action="copy" data-clipboard-target="#clipboard_base_url" data-bs-toggle="tooltip" data-bs-custom-class="custom-tooltip" data-bs-placement="top" title="Copy Token Code">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1" />
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-sm-2 col-form-label">&nbsp;</label>
                                <div class="col-sm-10">
                                    <button type="submit" name="submit" class="btn btn-primary" style="min-width: 120px;"><?= esc($pageSave) ?></button>
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
<script type="text/javascript">
    $(document).on("click", ".btn_delete_ac", function(e) {
        var el = document.getElementById('clipboard_delete');
        var successful = copyToClipboard(el);
        if (successful) {
            $.notify('Copied!', { position:"top right",className: 'success'} );
        } else {
            $.notify('Whoops, not copied!', { position:"top right",className: 'error'} );
        }
    });
</script>