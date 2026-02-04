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
                            <input type="text" id="search_input" class="form-control" placeholder="Search username or device" name="keyword" value="<?= esc(service('request')->getGet('keyword')) ?>" required="required">
                            <button class="btn btn-outline-default d-inline-flex align-items-center" type="search">
                                <i class="ri-search-2-line"></i>
                            </button>
                        </div>
                    </form>
                    <a href="<?= base_url('ns-admin/create-device-id') ?>" class="btn btn-primary d-inline-flex align-items-center justify-content-center">
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
                                    <th>Device ID</th>
                                    <th>Name</th>
                                    <th class="display-desktop">Seller</th>
                                    <th class="display-desktop">Registered</th>
                                    <th class="text-center">Status</th>
                                    <th style="width: 150px;" class="text-center display-desktop">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i=0; foreach($result as $row){ ?>
                                    <tr>
                                        <td><?= esc($row['device_id']) ?></td>
                                        <td><?= esc($row['user_name']) ?></td>
                                        <td><?= isset($row['admin_id']) ? esc(\App\Models\AdminModel::getUserdata($row['admin_id'])['username'] ?? 'N/A') : 'N/A' ?></td>
                                        <td class="display-desktop"><?= esc(date('d-m-Y', $row['registered_on'])) ?></td>
                                        <td class="text-center" >
                                            <div class="nsofts-switch enable_disable" data-bs-toggle="tooltip" data-bs-placement="top" title="Enable / Disable">
                                                <input type="checkbox" id="enable_disable_check_<?= $i ?>" data-action="<?= base_url('ns-admin/status-device-id/'.$row['id']) ?>" data-column="status" class="cbx hidden btn_enable_disable" <?php if ($row['status'] == 1) { echo 'checked'; } ?>>
                                                <label for="enable_disable_check_<?= $i ?>" class="nsofts-switch__label"></label>
                                            </div>
                                        </td>
                                        <td class="text-center display-desktop">
                                            <a href="<?= base_url('ns-admin/create-device-id/'.$row['id']) ?>" class="btn btn-primary btn-icon" style="padding: 10px 10px !important;  margin-right: 10px !important;" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
                                                <i class="ri-pencil-line"></i>
                                            </a>
                                            <a href="javascript:void(0)" class="btn btn-danger btn-icon btn_delete" data-action="<?= base_url('ns-admin/delete-device-id/'.$row['id']) ?>" style="padding: 10px 10px !important;" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete">
                                                <i class="ri-delete-bin-line"></i>
                                            </a>
                                        </td>
                                    </tr>
                                 <?php $i++; } ?>
                            </tbody>
                        </table>
                    </div>
                    <?php include("pagination.php"); ?>
                <?php } else { ?>
                    <h3 class="text-center p-5">No data found</h3>
                <?php } ?>
                </nav>
            </div>
        </div>
    </div>
</main>
<?= $this-> include('templates/footer');?>