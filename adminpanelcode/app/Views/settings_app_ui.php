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
                        <div class="nav flex-column nav-pills" id="nsofts_setting" role="tablist" aria-orientation="vertical">
                            <button class="nav-link active" id="nsofts_setting_1" data-bs-toggle="pill" data-bs-target="#nsofts_setting_content_1" type="button" role="tab" aria-controls="nsofts_setting_1" aria-selected="true">
                                <i class="ri-pencil-ruler-2-line"></i>
                                <span>App Themes</span>
                            </button>
                            
                            <button class="nav-link" id="nsofts_setting_3" data-bs-toggle="pill" data-bs-target="#nsofts_setting_content_3" type="button" role="tab" aria-controls="nsofts_setting_3" aria-selected="false">
                                <i class="ri-settings-5-line"></i>
                                <span>Select Page UI</span>
                            </button>
                            
                            <button class="nav-link" id="nsofts_setting_2" data-bs-toggle="pill" data-bs-target="#nsofts_setting_content_2" type="button" role="tab" aria-controls="nsofts_setting_2" aria-selected="false">
                                <i class="ri-settings-5-line"></i>
                                <span>EPG Page UI</span>
                            </button>
                        </div>
                    </div>
                    <div class="nsofts-setting__content">
                        <div class="tab-content">
                            
                            <!--App Themes-->
                            <div class="tab-pane fade show active" id="nsofts_setting_content_1" role="tabpanel" aria-labelledby="nsofts_setting_1" tabindex="0">
                                <form action="<?= base_url('/ns-admin/app_ui_handler');?>" name="settings_theme" method="POST" enctype="multipart/form-data">
                                    <?= csrf_field() ?>
                                    <h4 class="mb-4">App Themes</h4>
                                    <div class="mb-3 row">
                                        <label for="" class="col-sm-2 col-form-label">App UI</label>
                                        <div class="col-sm-10">
                                            <select name="is_theme" id="is_theme" class="form-select" required>
                                                <option value="">-- Select App UI --</option>
                                                <option value="1" <?php if ($settings['is_theme'] == '1') { ?>selected<?php } ?>>OneUI</option>
                                                <option value="2" <?php if ($settings['is_theme'] == '2') { ?>selected<?php } ?>>Glossy</option>
                                                <option value="3" <?php if ($settings['is_theme'] == '3') { ?>selected<?php } ?>>Black Panther</option>
                                                <option value="4" <?php if ($settings['is_theme'] == '4') { ?>selected<?php } ?>>Movie UI</option>
                                                <option value="5" <?php if ($settings['is_theme'] == '5') { ?>selected<?php } ?>>VUI</option>
                                                <option value="6" <?php if ($settings['is_theme'] == '6') { ?>selected<?php } ?>>Christmas UI</option>
                                                <option value="7" <?php if ($settings['is_theme'] == '7') { ?>selected<?php } ?>>Halloween UI</option>
                                                <option value="8" <?php if ($settings['is_theme'] == '8') { ?>selected<?php } ?>>Ramadan UI</option>
                                                <option value="9" <?php if ($settings['is_theme'] == '9') { ?>selected<?php } ?>>Sports UI</option>
                                                <option value="10" <?php if ($settings['is_theme'] == '10') { ?>selected<?php } ?>>Vibe UI</option>
                                            </select>
                                        </div>
                                    </div>
                                    <button type="submit" name="app_theme" class="btn btn-primary" style="min-width: 120px;">Save</button>
                                    <div class="row g-4 mb-3 mt-3">
                                        <div class="col-lg-6 col-sm-6 col-xs-12">
                                            <a href="<?= base_url('assets/images/themes/OneUI.webp') ?>" data-bs-toggle="tooltip" data-bs-placement="top" title="View UI">
                                                <div class="nsofts-image-card">
                                                    <div class="nsofts-image-card__cover" style="width: 100%; height: 220px; min-width: 100%; min-height: 100%; max-width: 100%;">
                                                        <img src="<?= base_url('assets/images/themes/OneUI.webp') ?>" onerror="this.src='<?= base_url('assets/images/600x300.jpg') ?>';" loading="lazy" alt="">
                                                    </div>
                                                    <div class="nsofts-image-card__content">
                                                        <div class="position-relative">
                                                            <div class="d-flex align-items-center justify-content-between nsofts-image-card__content__text">
                                                                <span class="d-block text-truncate fs-6 fw-semibold pe-2">OneUI</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="col-lg-6 col-sm-6 col-xs-12">
                                            <a href="<?= base_url('assets/images/themes/VibeUI.webp') ?>" data-bs-toggle="tooltip" data-bs-placement="top" title="View UI">
                                                <div class="nsofts-image-card">
                                                    <div class="nsofts-image-card__cover" style="width: 100%; height: 220px; min-width: 100%; min-height: 100%; max-width: 100%;">
                                                        <img src="<?= base_url('assets/images/themes/VibeUI.webp') ?>" onerror="this.src='<?= base_url('assets/images/600x300.jpg') ?>';" loading="lazy" alt="">
                                                    </div>
                                                    <div class="nsofts-image-card__content">
                                                        <div class="position-relative">
                                                            <div class="d-flex align-items-center justify-content-between nsofts-image-card__content__text">
                                                                <span class="d-block text-truncate fs-6 fw-semibold pe-2">VibeUI</span>
                                                                <div class="nsofts-image-card__option d-flex">
                                                                    <span style="color: #ff0068;">NEW</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="col-lg-6 col-sm-6 col-xs-12">
                                            <a href="<?= base_url('assets/images/themes/ChristmasUI.webp') ?>" data-bs-toggle="tooltip" data-bs-placement="top" title="View UI">
                                                <div class="nsofts-image-card">
                                                    <div class="nsofts-image-card__cover" style="width: 100%; height: 220px; min-width: 100%; min-height: 100%; max-width: 100%;">
                                                        <img src="<?= base_url('assets/images/themes/ChristmasUI.webp') ?>" onerror="this.src='<?= base_url('assets/images/600x300.jpg') ?>';" loading="lazy" alt="">
                                                    </div>
                                                    <div class="nsofts-image-card__content">
                                                        <div class="position-relative">
                                                            <div class="d-flex align-items-center justify-content-between nsofts-image-card__content__text">
                                                                <span class="d-block text-truncate fs-6 fw-semibold pe-2">Christmas UI</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="col-lg-6 col-sm-6 col-xs-12">
                                            <a href="<?= base_url('assets/images/themes/VUI.webp') ?>" data-bs-toggle="tooltip" data-bs-placement="top" title="View UI">
                                                <div class="nsofts-image-card">
                                                    <div class="nsofts-image-card__cover" style="width: 100%; height: 220px; min-width: 100%; min-height: 100%; max-width: 100%;">
                                                        <img src="<?= base_url('assets/images/themes/VUI.webp') ?>" onerror="this.src='<?= base_url('assets/images/600x300.jpg') ?>';" loading="lazy" alt="">
                                                    </div>
                                                    <div class="nsofts-image-card__content">
                                                        <div class="position-relative">
                                                            <div class="d-flex align-items-center justify-content-between nsofts-image-card__content__text">
                                                                <span class="d-block text-truncate fs-6 fw-semibold pe-2">VUI</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="col-lg-6 col-sm-6 col-xs-12">
                                            <a href="<?= base_url('assets/images/themes/HalloweenUI.webp') ?>" data-bs-toggle="tooltip" data-bs-placement="top" title="View UI">
                                                <div class="nsofts-image-card">
                                                    <div class="nsofts-image-card__cover" style="width: 100%; height: 220px; min-width: 100%; min-height: 100%; max-width: 100%;">
                                                        <img src="<?= base_url('assets/images/themes/HalloweenUI.webp') ?>" onerror="this.src='<?= base_url('assets/images/600x300.jpg') ?>';" loading="lazy" alt="">
                                                    </div>
                                                    <div class="nsofts-image-card__content">
                                                        <div class="position-relative">
                                                            <div class="d-flex align-items-center justify-content-between nsofts-image-card__content__text">
                                                                <span class="d-block text-truncate fs-6 fw-semibold pe-2">Halloween UI</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="col-lg-6 col-sm-6 col-xs-12">
                                            <div class="nsofts-image-card">
                                                <div class="nsofts-image-card__cover" style="width: 100%; height: 220px; min-width: 100%; min-height: 100%; max-width: 100%;">
                                                    <img src="<?= base_url('assets/images/themes/MovieUI.webp') ?>" onerror="this.src='<?= base_url('assets/images/600x300.jpg') ?>';" loading="lazy" alt="">
                                                </div>
                                                <div class="nsofts-image-card__content">
                                                    <div class="position-relative">
                                                        <div class="d-flex align-items-center justify-content-between nsofts-image-card__content__text">
                                                            <span class="d-block text-truncate fs-6 fw-semibold pe-2">Movie UI</span>
                                                            <div class="nsofts-image-card__option d-flex">
                                                                <a href="<?= base_url('ns-admin/create-gallery?type=movie_ui') ?>" class="btn border-0 text-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Theme">
                                                                    <i class="ri-pencil-fill"></i>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-sm-6 col-xs-12">
                                            <a href="<?= base_url('assets/images/themes/Glossy.webp') ?>" data-bs-toggle="tooltip" data-bs-placement="top" title="View UI">
                                                <div class="nsofts-image-card">
                                                    <div class="nsofts-image-card__cover" style="width: 100%; height: 220px; min-width: 100%; min-height: 100%; max-width: 100%;">
                                                        <img src="<?= base_url('assets/images/themes/Glossy.webp') ?>" onerror="this.src='<?= base_url('assets/images/600x300.jpg') ?>';" loading="lazy" alt="">
                                                    </div>
                                                    <div class="nsofts-image-card__content">
                                                        <div class="position-relative">
                                                            <div class="d-flex align-items-center justify-content-between nsofts-image-card__content__text">
                                                                <span class="d-block text-truncate fs-6 fw-semibold pe-2">Glossy</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="col-lg-6 col-sm-6 col-xs-12">
                                            <a href="<?= base_url('assets/images/themes/BlackPanther.webp') ?>" data-bs-toggle="tooltip" data-bs-placement="top" title="View UI">
                                                <div class="nsofts-image-card">
                                                    <div class="nsofts-image-card__cover" style="width: 100%; height: 220px; min-width: 100%; min-height: 100%; max-width: 100%;">
                                                        <img src="<?= base_url('assets/images/themes/BlackPanther.webp') ?>" onerror="this.src='<?= base_url('assets/images/600x300.jpg') ?>';" loading="lazy" alt="">
                                                    </div>
                                                    <div class="nsofts-image-card__content">
                                                        <div class="position-relative">
                                                            <div class="d-flex align-items-center justify-content-between nsofts-image-card__content__text">
                                                                <span class="d-block text-truncate fs-6 fw-semibold pe-2">Black Panther</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="col-lg-6 col-sm-6 col-xs-12">
                                            <div class="nsofts-image-card">
                                                <div class="nsofts-image-card__cover" style="width: 100%; height: 220px; min-width: 100%; min-height: 100%; max-width: 100%;">
                                                    <img src="<?= base_url('assets/images/themes/Ramadan.webp') ?>" onerror="this.src='<?= base_url('assets/images/600x300.jpg') ?>';" loading="lazy" alt="">
                                                </div>
                                                <div class="nsofts-image-card__content">
                                                    <div class="position-relative">
                                                        <div class="d-flex align-items-center justify-content-between nsofts-image-card__content__text">
                                                            <span class="d-block text-truncate fs-6 fw-semibold pe-2">Ramadan UI</span>
                                                            <div class="nsofts-image-card__option d-flex">
                                                                <a href="<?= base_url('ns-admin/create-gallery?type=ramadan_ui') ?>" class="btn border-0 text-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Theme">
                                                                    <i class="ri-pencil-fill"></i>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-sm-6 col-xs-12">
                                            <div class="nsofts-image-card">
                                                <div class="nsofts-image-card__cover" style="width: 100%; height: 220px; min-width: 100%; min-height: 100%; max-width: 100%;">
                                                    <img src="<?= base_url('assets/images/themes/Sports.webp') ?>" onerror="this.src='<?= base_url('assets/images/600x300.jpg') ?>';" loading="lazy" alt="">
                                                </div>
                                                <div class="nsofts-image-card__content">
                                                    <div class="position-relative">
                                                        <div class="d-flex align-items-center justify-content-between nsofts-image-card__content__text">
                                                            <span class="d-block text-truncate fs-6 fw-semibold pe-2">Sports UI</span>
                                                            <div class="nsofts-image-card__option d-flex">
                                                                <a href="<?= base_url('ns-admin/create-gallery?type=sports_ui') ?>" class="btn border-0 text-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Theme">
                                                                    <i class="ri-pencil-fill"></i>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            
                            <!--Select Page UI-->
                            <div class="tab-pane fade" id="nsofts_setting_content_3" role="tabpanel" aria-labelledby="nsofts_setting_3" tabindex="0">
                                <form action="<?= base_url('/ns-admin/app_ui_handler');?>" name="settings_epg" method="POST" enctype="multipart/form-data">
                                    <?= csrf_field() ?>
                                    <h4 class="mb-4">Select Page UI</h4>
                                    <div class="mb-3 row">
                                        <label for="" class="col-sm-2 col-form-label">App UI</label>
                                        <div class="col-sm-10">
                                            <select name="is_epg" id="is_epg" class="form-select" required>
                                                <option value="">-- Select Page UI --</option>
                                                <option value="1" selected >UI ONE</option>
                                            </select>
                                        </div>
                                    </div>
                                    <button type="submit" name="app_select_page" class="btn btn-primary" style="min-width: 120px;">Save</button>
                                    <div class="row g-4 mb-3 mt-3">
                                        <div class="col-lg-6 col-sm-6 col-xs-12">
                                            <div class="nsofts-image-card">
                                                <div class="nsofts-image-card__cover" style="width: 100%; height: 220px; min-width: 100%; min-height: 100%; max-width: 100%;">
                                                    <img src="<?= base_url('assets/images/themes/select-page-one.webp') ?>" onerror="this.src='<?= base_url('assets/images/600x300.jpg') ?>';"  loading="lazy" alt="">
                                                </div>
                                                <div class="nsofts-image-card__content">
                                                    <div class="position-relative">
                                                        <div class="d-flex align-items-center justify-content-between nsofts-image-card__content__text">
                                                            <span class="d-block text-truncate fs-6 fw-semibold pe-2">UI ONE</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="card h-100 mt-5">
                                        <div class="card-top d-md-inline-flex align-items-center justify-content-between py-3 px-4">
                                            <div class="d-inline-flex align-items-center text-decoration-none fw-semibold">
                                                <span class="ps-2 lh-1">Select Page</span>
                                            </div>
                                            <div class="d-flex mt-2 mt-md-0">
                                                <a href="<?= base_url('ns-admin/create-select') ?>" class="btn btn-primary d-inline-flex align-items-center justify-content-center">
                                                    <i class="ri-add-line"></i>
                                                    <span class="ps-1 text-nowrap d-none d-sm-block">Create Select Page</span>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="card-body p-4">
                                            <?php if(!empty($resultPage)){ ?>
                                                <div class="row g-4 mb-3">
                                                    <?php $i=0; foreach($resultPage as $row){ ?>
                                                        <div class="col-lg-6 col-sm-6 col-xs-12">
                                                            <div class="nsofts-card-light p-3">
                                                                <h5 class="mb-2 text-truncate"><?= esc($row['title']) ?></h5>
                                                                <p><?= esc($row['redirect_type']) ?></p>
                                                                <div class="d-flex justify-content-between">
                                                                    <div class="d-flex">
                                                                        <a href="<?= base_url('ns-admin/create-select/'.$row['id']) ?>" class="btn btn-outline-primary rounded-pill me-2 btn-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
                                                                            <i class="ri-pencil-line"></i>
                                                                        </a>
                                                                        <a href="javascript:void(0)" class="btn btn-outline-danger rounded-pill me-2 btn-icon btn_delete" data-action="<?= base_url('ns-admin/delete-select/'.$row['id']) ?>" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete">
                                                                            <i class="ri-delete-bin-line"></i>
                                                                        </a>
                                                                    </div>
                                                                    <div class="nsofts-switch d-flex align-items-center enable_disable" data-bs-toggle="tooltip" data-bs-placement="top" title="Enable / Disable">
                                                                        <input type="checkbox" id="enable_disable_check_<?= $i ?>" data-action="<?= base_url('ns-admin/status-select/'.$row['id']) ?>" data-column="status" class="cbx hidden btn_enable_disable" <?php if ($row['status'] == 1) { echo 'checked'; } ?>>
                                                                        <label for="enable_disable_check_<?= $i ?>" class="nsofts-switch__label"></label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php $i++; } ?>
                                                </div>
                                            <?php } else { ?>
                                                <h3 class="text-center p-4">No data found</h3>
                                            <?php } ?>
                                            </nav>
                                        </div>
                                    </div>
                                    
                                </form>
                            </div>
                            
                            <!--EPG Page UI-->
                            <div class="tab-pane fade" id="nsofts_setting_content_2" role="tabpanel" aria-labelledby="nsofts_setting_2" tabindex="0">
                                <form action="<?= base_url('/ns-admin/app_ui_handler');?>" name="settings_epg" method="POST" enctype="multipart/form-data">
                                    <?= csrf_field() ?>
                                    <h4 class="mb-4">EPG Page UI</h4>
                                    <div class="mb-3 row">
                                        <label for="" class="col-sm-2 col-form-label">App UI</label>
                                        <div class="col-sm-10">
                                            <select name="is_epg" id="is_epg" class="form-select" required>
                                                <option value="">-- Select Page UI --</option>
                                                <option value="1" <?php if ($settings['is_epg'] == '1') { ?>selected<?php } ?>>UI ONE</option>
                                                <option value="2" <?php if ($settings['is_epg'] == '2') { ?>selected<?php } ?>>UI TWO</option>
                                            </select>
                                        </div>
                                    </div>
                                    <button type="submit" name="app_theme_epg" class="btn btn-primary" style="min-width: 120px;">Save</button>
                                    <div class="row g-4 mb-3 mt-3">
                                        <div class="col-lg-6 col-sm-6 col-xs-12">
                                            <div class="nsofts-image-card">
                                                <div class="nsofts-image-card__cover" style="width: 100%; height: 220px; min-width: 100%; min-height: 100%; max-width: 100%;">
                                                    <img src="<?= base_url('assets/images/themes/epg_one.webp') ?>" onerror="this.src='<?= base_url('assets/images/600x300.jpg') ?>';"  loading="lazy" alt="">
                                                </div>
                                                <div class="nsofts-image-card__content">
                                                    <div class="position-relative">
                                                        <div class="d-flex align-items-center justify-content-between nsofts-image-card__content__text">
                                                            <span class="d-block text-truncate fs-6 fw-semibold pe-2">UI ONE</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-sm-6 col-xs-12">
                                            <div class="nsofts-image-card">
                                                <div class="nsofts-image-card__cover" style="width: 100%; height: 220px; min-width: 100%; min-height: 100%; max-width: 100%;">
                                                    <img src="<?= base_url('assets/images/themes/epg_two.webp') ?>" onerror="this.src='<?= base_url('assets/images/600x300.jpg') ?>';"  loading="lazy" alt="">
                                                </div>
                                                <div class="nsofts-image-card__content">
                                                    <div class="position-relative">
                                                        <div class="d-flex align-items-center justify-content-between nsofts-image-card__content__text">
                                                            <span class="d-block text-truncate fs-6 fw-semibold pe-2">UI TWO</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
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
        const tabLinks = document.querySelectorAll('#nsofts_setting .nav-link');
        const tabContents = document.querySelectorAll('.tab-pane');
        
        // Load last active tab from localStorage
        const activeTab = localStorage.getItem('activeTabUI') || '#nsofts_setting_content_1'; // Default to General tab
        const activeTabLink = document.querySelector(`#nsofts_setting [data-bs-target="${activeTab}"]`);
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
                localStorage.setItem('activeTabUI', targetTab);
            });
        });
    });
</script>