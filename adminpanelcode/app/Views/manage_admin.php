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
                            <input type="text" id="search_input" class="form-control" placeholder="Search username or email" name="keyword" value="<?= esc(service('request')->getGet('keyword')) ?>" required="required">
                            <button class="btn btn-outline-default d-inline-flex align-items-center" type="search">
                                <i class="ri-search-2-line"></i>
                            </button>
                        </div>
                    </form>
                    <a href="<?= base_url('ns-admin/create-admin') ?>" class="btn btn-primary d-inline-flex align-items-center justify-content-center">
                        <i class="ri-add-line"></i>
                        <span class="ps-1 text-nowrap d-none d-sm-block">Create User</span>
                    </a>
                </div>
            </div>
            <div class="card-body p-4">
                <?php if(!empty($result)){ ?>
                    <div class="row g-4">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th style="width: 40px;">#</th>
                                    <th class="display-desktop" style="width: 70px !important;">Image</th>
                                    <th>Name</th>
                                    <th class="display-desktop text-truncate">Email</th>
                                    <th class="text-center display-desktop">Type</th>
                                    <th class="text-center">Status</th>
                                    <th style="width: 150px;" class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="load-more-container">
                                <?php $i=0; foreach($result as $row){ ?>
                                    <tr class="card-item">
                                        <td><?=$i+1 ?></td>
                                        <td class="display-desktop">
                                            <div class="text-center">
                                                <img src="<?= base_url('images/'.$row['image']) ?>" class="image-card__cover" onerror="this.src='<?= base_url('assets/images/user_photo.png') ?>';"  loading="lazy" alt="">
                                            </div>
                                        </td>
                                        <td class="text-truncate"><?= esc($row['username']) ?></td>
                                        <td class="display-desktop text-truncate"><?= esc($row['email']) ?></td>
                                        <td class="text-center display-desktop">
                                            <?php if($row['admin_type'] == '3') { ?>
                                                <span class="nsofts-badge nsofts-badge-primary">SUPER ADMIN</span>
                                            <?php } else if($row['admin_type'] == '2') { ?>
                                                <span class="nsofts-badge nsofts-badge-primary">RESELLER</span>
                                            <?php } else if($row['admin_type'] == '1') { ?>
                                                <span class="nsofts-badge nsofts-badge-primary">ADMIN</span>
                                            <?php } else if($row['admin_type'] == '0') { ?>
                                                <span class="nsofts-badge nsofts-badge-primary">EDITOR</span>
                                            <?php } ?>
                                        </td>
                                        <td class="text-center" >
                                            <?php if($row['admin_type'] == '3') { ?>
                                                <div class="nsofts-switch disable" data-bs-toggle="tooltip" data-bs-placement="top" title="Enable / Disable">
                                                </div>
                                            <?php } else { ?>
                                                <div class="nsofts-switch enable_disable" data-bs-toggle="tooltip" data-bs-placement="top" title="Enable / Disable">
                                                    <input type="checkbox" id="enable_disable_check_<?= $i ?>" data-action="<?= base_url('ns-admin/status-admin/'.$row['id']) ?>" data-column="status" class="cbx hidden btn_enable_disable" <?php if ($row['status'] == 1) { echo 'checked'; } ?>>
                                                    <label for="enable_disable_check_<?= $i ?>" class="nsofts-switch__label"></label>
                                                </div>
                                            <?php } ?>
                                        </td>
                                        
                                        <td class="text-center">
                                            <a href="<?= base_url('ns-admin/create-admin/'.$row['id']) ?>" class="btn btn-primary btn-icon" style="padding: 10px 10px !important;  margin-right: 10px !important;" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
                                                <i class="ri-pencil-line"></i>
                                            </a>
                                            <?php if($row['admin_type'] == '3') { ?>
                                                <a class="btn btn-secondary btn-icon" style="padding: 10px 10px !important;">
                                                    <i class="ri-delete-bin-line"></i>
                                                </a>
                                            <?php } else { ?>
                                                <a href="javascript:void(0)" class="btn btn-danger btn-icon btn_delete" data-action="<?= base_url('ns-admin/delete-admin/'.$row['id']) ?>" style="padding: 10px 10px !important;" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete">
                                                    <i class="ri-delete-bin-line"></i>
                                                </a>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                 <?php $i++; } ?>
                            </tbody>
                        </table>
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