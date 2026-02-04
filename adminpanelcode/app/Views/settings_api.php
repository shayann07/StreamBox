<?= $this-> include('templates/header');?>
<main id="nsofts_main">
    <div class="nsofts-container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb align-items-center">
                <li class="breadcrumb-item d-inline-flex"><a href="dashboard.php"><i class="ri-home-4-fill"></i></a></li>
                <li class="breadcrumb-item d-inline-flex active" aria-current="page"><?= isset($pageTitle) ? esc($pageTitle) : "" ?></li>
            </ol>
        </nav>
        <div class="card">
            <div class="card-body p-0">                    
                <div class="nsofts-setting">
                    <div class="nsofts-setting__sidebar">
                        <div class="nav flex-column nav-pills" id="nsofts_api" role="tablist" aria-orientation="vertical">
                            <button class="nav-link active" id="nsofts_api_1" data-bs-toggle="pill" data-bs-target="#nsofts_api_content_1" type="button" role="tab" aria-controls="nsofts_api_1" aria-selected="true">
                                <i class="ri-list-settings-line"></i>
                                <span>Themoviedb</span>
                            </button>
                            
                            <button class="nav-link" id="nsofts_api_2" data-bs-toggle="pill" data-bs-target="#nsofts_api_content_2" type="button" role="tab" aria-controls="nsofts_api_2" aria-selected="false">
                                <i class="ri-key-2-line"></i>
                                <span>API Token</span>
                            </button>
                            
                            <button class="nav-link" id="nsofts_api_3" data-bs-toggle="pill" data-bs-target="#nsofts_api_content_3" type="button" role="tab" aria-controls="nsofts_api_3" aria-selected="false">
                                <i class="ri-key-2-line"></i>
                                <span>API IPTV Panel</span>
                            </button>
                        </div>
                        
                    </div>
                    <div class="nsofts-setting__content">
                        <div class="tab-content">
                            <!--Themoviedb Settings-->
                            <div class="tab-pane fade show active" id="nsofts_api_content_1" role="tabpanel" aria-labelledby="nsofts_api_1" tabindex="0">
                                <form action="<?= base_url('/ns-admin/api_handler');?>" name="settings_general" method="POST" enctype="multipart/form-data">
                                    <?= csrf_field() ?>
                                    <h4 class="mb-4">Themoviedb Settings</h4>
                                    <div class="mb-3 row">
                                        <label for="" class="col-sm-2 col-form-label">TMDb Access Token <a style="color: #f44336c7;" href="https://docs.nemosofts.com/streambox-app/admin-panel/themoviedb" target="_blank">link</a></label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" name="account_delete_intruction" id="account_delete_intruction" value="<?= esc($settings['account_delete_intruction']) ?>" >
                                        </div>
                                    </div>
                                    <button type="submit" name="submit_general" class="btn btn-primary" style="min-width: 120px;">Save</button>
                                </form>
                            </div>
                            
                            <div class="tab-pane fade" id="nsofts_api_content_2" role="tabpanel" aria-labelledby="nsofts_api_2" tabindex="0">
                                <form action="<?= base_url('/ns-admin/api_handler');?>" name="settings_api_update" method="POST" enctype="multipart/form-data">
                                    <?= csrf_field() ?>
                                    <div class="card h-100">
                                        <div class="card-top d-md-inline-flex align-items-center justify-content-between py-3 px-4">
                                            <div class="d-inline-flex align-items-center text-decoration-none fw-semibold">
                                                <span class="ps-2 lh-1">Manage Api token</span>
                                            </div>
                                            <div class="d-flex mt-2 mt-md-0">
                                                <a href="<?= base_url('ns-admin/create-token') ?>" class="btn btn-primary d-inline-flex align-items-center justify-content-center">
                                                    <i class="ri-add-line"></i>
                                                    <span class="ps-1 text-nowrap d-none d-sm-block">Create Token</span>
                                                </a>
                                            </div>
                                        </div>
                                        
                                        <div class="card-body p-4">
                                            <?php if(!empty($result)){ ?>
                                            <div class="row g-4">
                                                <table class="table">
                                                    <thead>
                                                        <tr>
                                                            <th>Name</th>
                                                            <th class="text-center">Status</th>
                                                            <th style="width: 150px;" class="text-center display-desktop">Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="load-more-container">
                                                        <?php $i=0; foreach($result as $row){ ?>
                                                            <tr class="card-item">
                                                                <td><?= esc($row['token_name']) ?></td>
                                                                <td class="text-center" >
                                                                    <div class="nsofts-switch enable_disable" data-bs-toggle="tooltip" data-bs-placement="top" title="Enable / Disable">
                                                                        <input type="checkbox" id="enable_disable_check_<?= $i ?>" data-id="<?= $row['id'] ?>" data-table="tbl_token_code" data-column="status" class="cbx hidden btn_enable_disable" <?php if ($row['status'] == 1) { echo 'checked'; } ?>>
                                                                        <label for="enable_disable_check_<?= $i ?>" class="nsofts-switch__label"></label>
                                                                    </div>
                                                                </td>
                                                                <td class="text-center display-desktop">
                                                                    <a href="<?= base_url('ns-admin/create-token/'.$row['id']) ?>" class="btn btn-primary btn-icon" style="padding: 10px 10px !important;  margin-right: 10px !important;" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
                                                                        <i class="ri-pencil-line"></i>
                                                                    </a>
                                                                    <a href="javascript:void(0)" class="btn btn-danger btn-icon btn_delete" data-action="<?= base_url('ns-admin/delete-token/'.$row['id']) ?>" style="padding: 10px 10px !important;" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete">
                                                                        <i class="ri-delete-bin-line"></i>
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                         <?php $i++; } ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        <?php } else { ?>
                                            <h3 class="text-center p-5">No data found</h3>
                                        <?php } ?>
                                        </div>
                                        
                                    </div>
                                </form>
                            </div>
                            
                            <!--Xtream codes / Xui Settings-->
                            <div class="tab-pane fade show" id="nsofts_api_content_3" role="tabpanel" aria-labelledby="nsofts_api_3" tabindex="0">
                                <form action="<?= base_url('/ns-admin/api_handler');?>" name="settings_general" method="POST" enctype="multipart/form-data">
                                    <?= csrf_field() ?>
                                    <h4 class="mb-3">Xtream codes / Xui</h4>
                                    <p class="mb-1">To create a trail line from the application by the users below,  api keys are needed.</p>
                                    <p class="mb-1">By this method, the user can create a trial line from the panel based on your trial pacakge id.</p>
                                    <p class="mb-1">Users able create only 1 trial line from a particular device.</p>
                                    
                                    <div class="mb-3 mt-4 row">
                                        <label for="" class="col-sm-2 col-form-label">LOGIN API URL</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" name="login_access_code" id="login_access_code" placeholder="https://[domain]:[port]" value="<?= esc($settings['login_access_code']) ?>">
                                        </div>
                                    </div>
                                    
                                    <h5 class="mb-2 mt-5">ADMIN API</h5>
                                    <div class="mb-3 row">
                                        <label for="" class="col-sm-2 col-form-label">API URL</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" name="admin_access_code" id="admin_access_code" placeholder="https://[domain]:[port]/[admin accesscode]" value="<?= esc($settings['admin_access_code']) ?>">
                                        </div>
                                    </div>
                                    <div class="mb-4 row">
                                        <label for="" class="col-sm-2 col-form-label">API KEY</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" name="admin_api_key" id="admin_api_key" placeholder="Enter your anmin api key" value="<?= esc($settings['admin_api_key']) ?>">
                                        </div>
                                    </div>
                                    <div class="mb-4 row">
                                        <label for="" class="col-sm-2 col-form-label">PACKAGE ID</label>
                                        <div class="col-sm-10">
                                            <input type="number" class="form-control" name="admin_packages" id="admin_packages" placeholder="Enter your packages id [trial package]" value="<?= esc($settings['admin_packages']) ?>">
                                            <a style="color: #c5554dc7;">Management > Service Setup > Packages</a>
                                        </div>
                                    </div>
                                    
                                    <h5 class="mb-2 mt-5">RESELLER API</h5>
                                    <div class="mb-3 row">
                                        <label for="" class="col-sm-2 col-form-label">API URL</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" name="reseller_access_code" id="reseller_access_code" placeholder="https://[domain]:[port]/[reseller accesscode]" value="<?= esc($settings['reseller_access_code']) ?>">
                                        </div>
                                    </div>
                                    <div class="mb-4 row">
                                        <label for="" class="col-sm-2 col-form-label">API KEY</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" name="reseller_api_key" id="reseller_api_key" placeholder="Enter your reseller api key" value="<?= esc($settings['reseller_api_key']) ?>">
                                        </div>
                                    </div>
                                    <div class="mb-4 row">
                                        <label for="" class="col-sm-2 col-form-label">Contact Note</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" name="admin_trial_note" id="admin_trial_note" placeholder="Enter contact note" value="<?= esc($settings['admin_trial_note']) ?>">
                                        </div>
                                    </div>
                                    
                                    <button type="submit" name="submit_panel_api" class="btn btn-primary" style="min-width: 120px;">Save</button>
                                </form>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?= $this-> include('templates/footer');?>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const tabLinks = document.querySelectorAll('#nsofts_api .nav-link');
        const tabContents = document.querySelectorAll('.tab-pane');
        
        // Load last active tab from localStorage
        const activeTab = localStorage.getItem('activeTabApi') || '#nsofts_api_content_1'; // Default to General tab
        const activeTabLink = document.querySelector(`#nsofts_api [data-bs-target="${activeTab}"]`);
        const activeTabContent = document.querySelector(activeTab);
        
        // Check if the tab exists, otherwise fallback to General tab
        if (activeTabLink && activeTabContent) {
            tabLinks.forEach(link => link.classList.remove('active'));
            tabContents.forEach(content => content.classList.remove('show', 'active'));
            activeTabLink.classList.add('active');
            activeTabContent.classList.add('show', 'active');
        }

        // Listen for tab click events and save to localStorage
        tabLinks.forEach(link => {
            link.addEventListener('click', function () {
                const targetTab = this.getAttribute('data-bs-target');
                localStorage.setItem('activeTabApi', targetTab);
            });
        });
    });
</script>