<?= $this-> include('templates/header');?>
<main id="nsofts_main">
    <div class="nsofts-container">
        <div class="card h-100">
            <div class="card-top d-md-inline-flex align-items-center justify-content-between py-3 px-4">
                <div class="d-inline-flex align-items-center text-decoration-none fw-semibold">
                    <span class="ps-2 lh-1"><?= isset($pageTitle) ? esc($pageTitle) : "" ?></span>
                </div>
                <div class="d-flex mt-2 mt-md-0">
                    <form method="get" id="searchForm" action="" class="me-2">
                        <div class="input-group">
                            <input type="text" id="search_input" class="form-control" placeholder="Search title" name="keyword" value="<?= esc(service('request')->getGet('keyword')) ?>" required="required">
                            <button class="btn btn-outline-default d-inline-flex align-items-center" type="search">
                                <i class="ri-search-2-line"></i>
                            </button>
                        </div>
                    </form>
                    <a href="<?= base_url('ns-admin/create-extream') ?>" class="btn btn-primary d-inline-flex align-items-center justify-content-center">
                        <i class="ri-add-line"></i>
                        <span class="ps-1 text-nowrap d-none d-sm-block">Create DNS</span>
                    </a>
                </div>
            </div>
            <div class="card-body p-4">
                <?php if(!empty($result)){ ?>
                    <div class="row g-4 mb-3" id="load-more-container">
                        <?php $i=0; foreach($result as $row){ ?>
                            <div class="col-lg-3 col-sm-6 card-item">
                                <div class="nsofts-card-light p-3">
                                    <h5 class="mb-2 text-truncate"><?= esc($row['dns_title']) ?></h5>
                                    <p><?= esc($row['dns_base']) ?></p>
                                    <div class="d-flex justify-content-between">
                                        <div class="d-flex">
                                            <a href="<?= base_url('ns-admin/create-extream/'.$row['id']) ?>" class="btn btn-outline-primary rounded-pill me-2 btn-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
                                                <i class="ri-pencil-line"></i>
                                            </a>
                                            <a href="javascript:void(0)" class="btn btn-outline-danger rounded-pill me-2 btn-icon btn_delete" data-action="<?= base_url('ns-admin/delete-extream/'.$row['id']) ?>" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete">
                                                <i class="ri-delete-bin-line"></i>
                                            </a>
                                        </div>
                                        <div class="nsofts-switch d-flex align-items-center enable_disable" data-bs-toggle="tooltip" data-bs-placement="top" title="Enable / Disable">
                                            <input type="checkbox" id="enable_disable_check_<?= $i ?>" data-action="<?= base_url('ns-admin/status-extream/'.$row['id']) ?>" data-column="status" class="cbx hidden btn_enable_disable" <?php if ($row['status'] == 1) { echo 'checked'; } ?>>
                                            <label for="enable_disable_check_<?= $i ?>" class="nsofts-switch__label"></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php $i++; } ?>
                    </div>
                    <button class="nsofts-load-btn mt-4 mb-2 d-flex align-items-center justify-content-center"
                        id="load-more-btn">
                        <span>Load More</span>
                        <i class="ri-sort-desc"></i>
                    </button>
                <?php } else { ?>
                    <h3 class="text-center p-5">No data found</h3>
                <?php } ?>
                </nav>
            </div>
        </div>
    </div>
</main>
<?= $this-> include('templates/footer');?>