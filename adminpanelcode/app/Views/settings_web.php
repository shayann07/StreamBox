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
                                <i class="ri-list-settings-line"></i>
                                <span>General</span>
                            </button>
                            <button class="nav-link" id="nsofts_setting_2" data-bs-toggle="pill" data-bs-target="#nsofts_setting_content_2" type="button" role="tab" aria-controls="nsofts_setting_2" aria-selected="false">
                                <i class="ri-settings-5-line"></i>
                                <span>Web Settings</span>
                            </button>
                            <button class="nav-link" id="nsofts_setting_3" data-bs-toggle="pill" data-bs-target="#nsofts_setting_content_3" type="button" role="tab" aria-controls="nsofts_setting_3" aria-selected="false">
                                <i class="ri-survey-line"></i>
                                <span>Privacy Policy</span>
                            </button>
                            <button class="nav-link" id="nsofts_setting_4" data-bs-toggle="pill" data-bs-target="#nsofts_setting_content_4" type="button" role="tab" aria-controls="nsofts_setting_4" aria-selected="false">
                                <i class="ri-survey-line"></i>
                                <span>Terms & Conditions</span>
                            </button>
                            <a class="nav-link text-danger" href="http://player.envatonemosofts.com/" target="_blank">
                                <i class="ri-global-line"></i>
                                <span>Demo Website</span>
                            </a>
                        </div>
                    </div>
                    <div class="nsofts-setting__content">
                        <div class="tab-content">
                            <!--General Settings-->
                            <div class="tab-pane fade show active" id="nsofts_setting_content_1" role="tabpanel" aria-labelledby="nsofts_setting_1" tabindex="0">
                                <form action="<?= base_url('/ns-admin/web_handler');?>" name="settings_general" method="POST" enctype="multipart/form-data">
                                    <?= csrf_field() ?>
                                    <h4 class="mb-4">General Settings</h4>
                                    
                                    <div class="mb-3 row">
                                        <label for="" class="col-sm-2 col-form-label">Site Name</label>
                                        <div class="col-sm-10">
                                           <input type="text" name="site_name" id="site_name" value="<?= esc($settings_data['site_name']) ?>"  class="form-control">
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3 row">
                                        <label for="" class="col-sm-2 col-form-label">Site Description</label>
                                        <div class="col-sm-10">
                                            <textarea rows="3" name="site_description" class="form-control" required=""><?= esc($settings_data['site_description']) ?></textarea>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3 row">
                                        <label for="" class="col-sm-2 col-form-label">Site Keywords</label>
                                        <div class="col-sm-10">
                                            <input type="text" name="site_keywords" id="site_keywords" value="<?= esc($settings_data['site_keywords']) ?>"  class="form-control" required="required">
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3 row">
                                        <label for="" class="col-sm-2 col-form-label">Site Favicon</label>
                                        <div class="col-sm-10">
                                            <div class="row">
                                                <div class="col-md-5">
                                                    <input type="file" class="form-control-file" name="web_favicon" value="fileupload" accept=".png, .jpg, .JPG .PNG" onchange="fileValidation()" id="fileupload">
                                                    <p class="control-label-help hint_lbl">(Recommended resolution: 16x16 or 32x32)</p>
                                                </div>
                                                <div class="col-md-3">
                                                    <?php if($settings_data['web_favicon']!='') { ?>
                                                        <div class="fileupload_img" id="imagePreview">
                                                            <img  type="image" src="<?= base_url('images/'.$settings_data['web_favicon']) ?>" style="width: 50px;height: 50px"   alt="image" />
                                                        </div>
                                                    <?php }else{ ?>
                                                        <div class="fileupload_img" id="imagePreview">
                                                            <img type="image" src="<?= base_url('assets/images/300x300.jpg') ?>" style="width: 50px; height: 50px"  alt="image" />
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3 row">
                                        <label for="" class="col-sm-2 col-form-label">Site Logo</label>
                                        <div class="col-sm-10">
                                            <div class="row">
                                                <div class="col-md-5">
                                                    <input type="file" class="form-control-file" name="web_logo_1" value="fileupload2" accept=".png, .jpg, .JPG .PNG" onchange="fileValidation2()" id="fileupload2">
                                                    <p class="control-label-help hint_lbl">(Recommended resolution: 500x500)</p>
                                                </div>
                                                <div class="col-md-3">
                                                    <?php if($settings_data['web_logo_1']!='') { ?>
                                                        <div class="fileupload_img" id="imagePreview2">
                                                            <img  type="image" src="<?= base_url('images/'.$settings_data['web_logo_1']) ?>" style="width: 50px;height: 50px"   alt="image" />
                                                        </div>
                                                    <?php }else{ ?>
                                                        <div class="fileupload_img" id="imagePreview2">
                                                            <img type="image" src="<?= base_url('assets/images/300x300.jpg') ?>" style="width: 50px; height: 50px"  alt="image" />
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3 row">
                                        <label for="" class="col-sm-2 col-form-label">Copyright Text</label>
                                        <div class="col-sm-10">
                                            <input type="text" name="copyright_text" id="copyright_text" value="<?= esc($settings_data['copyright_text']) ?>"  class="form-control" required="required">
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3 row">
                                        <label for="" class="col-sm-2 col-form-label">Header Code</label>
                                        <div class="col-sm-10">
                                            <textarea rows="6" name="header_code" class="form-control"  placeholder="Custom CSS or JS Script"><?= isset($settings_data['header_code']) ? service('purify')->purifyHtml(html_entity_decode($settings_data['header_code'])) : '' ?></textarea>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3 row">
                                        <label for="" class="col-sm-2 col-form-label">Footer Code</label>
                                        <div class="col-sm-10">
                                            <textarea rows="6" name="footer_code" class="form-control" placeholder="Custom CSS or JS Script"><?= isset($settings_data['footer_code']) ? service('purify')->purifyHtml(html_entity_decode($settings_data['footer_code'])) : '' ?></textarea>
                                        </div>
                                    </div>
                                    
                                    <button type="submit" name="submit_general" class="btn btn-primary" style="min-width: 120px;">Save</button>
                                </form>
                            </div>
                            
                            <!--App Settings-->
                            <div class="tab-pane fade" id="nsofts_setting_content_2" role="tabpanel" aria-labelledby="nsofts_setting_2" tabindex="0">
                                <form action="<?= base_url('/ns-admin/web_handler');?>" name="settings_app" method="POST" enctype="multipart/form-data">
                                    <?= csrf_field() ?>
                                    <div class="row mb-4">
                                        <div class="col-md-6">
                                             <h4 class="mb-4">Active Settings</h4>
                                            <div class="mb-3 row">
                                                <label for="" class="col-sm-5 col-form-label">Xtream codes</label>
                                                <div class="col-sm-7">
                                                    <div class="nsofts-switch d-flex align-items-center">
                                                        <input type="checkbox" id="is_select_xui" name="is_select_xui" value="true" class="nsofts-switch__label" <?php if($settings_data['is_select_xui']=='true'){ echo 'checked'; }?>/>
                                                        <label for="is_select_xui" class="nsofts-switch__label"></label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mb-3 row">
                                                <label for="" class="col-sm-5 col-form-label">1-Stream</label>
                                                <div class="col-sm-7">
                                                    <div class="nsofts-switch d-flex align-items-center">
                                                        <input type="checkbox" id="is_select_stream" name="is_select_stream" value="true" class="nsofts-switch__label" <?php if($settings_data['is_select_stream']=='true'){ echo 'checked'; }?>/>
                                                        <label for="is_select_stream" class="nsofts-switch__label"></label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mb-3 row">
                                                <label for="" class="col-sm-5 col-form-label">Device ID</label>
                                                <div class="col-sm-7">
                                                    <div class="nsofts-switch d-flex align-items-center">
                                                        <input type="checkbox" id="is_select_device_id" name="is_select_device_id" value="true" class="nsofts-switch__label" <?php if($settings_data['is_select_device_id']=='true'){ echo 'checked'; }?>/>
                                                        <label for="is_select_device_id" class="nsofts-switch__label"></label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mb-3 row">
                                                <label for="" class="col-sm-5 col-form-label">Activation Code</label>
                                                <div class="col-sm-7">
                                                    <div class="nsofts-switch d-flex align-items-center">
                                                        <input type="checkbox" id="is_select_activation_code" name="is_select_activation_code" value="true" class="nsofts-switch__label" <?php if($settings_data['is_select_activation_code']=='true'){ echo 'checked'; }?>/>
                                                        <label for="is_select_activation_code" class="nsofts-switch__label"></label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mb-3 row">
                                                <label for="" class="col-sm-5 col-form-label">Single Stream</label>
                                                <div class="col-sm-7">
                                                    <div class="nsofts-switch d-flex align-items-center">
                                                        <input type="checkbox" id="is_select_single" name="is_select_single" value="true" class="nsofts-switch__label" <?php if($settings_data['is_select_single']=='true'){ echo 'checked'; }?>/>
                                                        <label for="is_select_single" class="nsofts-switch__label"></label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <h4 class="mb-4">App Settings</h4>
                                            <div class="mb-3 row">
                                                <label for="" class="col-sm-5 col-form-label">Maintenance</label>
                                                <div class="col-sm-7">
                                                    <div class="nsofts-switch d-flex align-items-center">
                                                        <input type="checkbox" id="is_maintenance" name="is_maintenance" value="true" class="cbx hidden" <?php if($settings_data['is_maintenance']=='true'){ echo 'checked'; }?>/>
                                                        <label for="is_maintenance" class="nsofts-switch__label"></label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row mb-4">
                                        <div class="col-md-6">
                                            <h4 class="mb-4">Extream codes Settings</h4>
                                            <div class="mb-3 row">
                                                <label for="" class="col-sm-5 col-form-label">Active DNS</label>
                                                <div class="col-sm-7">
                                                    <div class="nsofts-switch d-flex align-items-center">
                                                        <input type="checkbox" id="is_xui_dns" name="is_xui_dns" value="true" class="nsofts-switch__label" <?php if($settings_data['is_xui_dns']=='true'){ echo 'checked'; }?>/>
                                                        <label for="is_xui_dns" class="nsofts-switch__label"></label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <h4 class="mb-4">1-Stream Settings</h4>
                                            <div class="mb-3 row">
                                                <label for="" class="col-sm-5 col-form-label">Active DNS</label>
                                                <div class="col-sm-7">
                                                    <div class="nsofts-switch d-flex align-items-center">
                                                        <input type="checkbox" id="is_stream_dns" name="is_stream_dns" value="true" class="nsofts-switch__label" <?php if($settings_data['is_stream_dns']=='true'){ echo 'checked'; }?>/>
                                                        <label for="is_stream_dns" class="nsofts-switch__label"></label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="submit" name="web_settings_submit" class="btn btn-primary" style="min-width: 120px;">Save</button>
                                </form>
                            </div>
                            
                            <!--Privacy Policy-->
                            <div class="tab-pane fade" id="nsofts_setting_content_3" role="tabpanel" aria-labelledby="nsofts_setting_3" tabindex="0">
                                <form action="<?= base_url('/ns-admin/web_handler');?>" name="submit_privacy" method="POST" enctype="multipart/form-data">
                                    <?= csrf_field() ?>
                                    <h4 class="mb-4">Privacy Policy</h4>
                                    <div class="mb-3 row">
                                        <label for="" class="col-sm-2 col-form-label">Page title</label>
                                        <div class="col-sm-10">
                                           <input type="text" name="privacy_page_title" id="privacy_page_title" value="<?= esc($settings_data['privacy_page_title']) ?>"  class="form-control">
                                        </div>
                                    </div>
                                    <div>
                                        <textarea name="privacy_content" id="privacy_content" rows="5" class="nsofts-editor mb-4">
                                            <?= esc(stripslashes($settings_data['privacy_content'])) ?>
                                        </textarea>
                                    </div>
                                    <button type="submit" name="submit_privacy" class="btn btn-primary" style="min-width: 120px;">Save</button>
                                </form>
                            </div>
                            
                            <!--Terms & Conditions-->
                            <div class="tab-pane fade" id="nsofts_setting_content_4" role="tabpanel" aria-labelledby="nsofts_setting_4" tabindex="0">
                               <form action="<?= base_url('/ns-admin/web_handler');?>" name="submit_terms"  method="POST" enctype="multipart/form-data">
                                    <?= csrf_field() ?>
                                    <h4 class="mb-4">Terms & Conditions</h4>
                                    <div class="mb-3 row">
                                        <label for="" class="col-sm-2 col-form-label">Page title</label>
                                        <div class="col-sm-10">
                                           <input type="text" name="terms_of_use_page_title" id="terms_of_use_page_title" value="<?= esc($settings_data['terms_of_use_page_title']) ?>"  class="form-control">
                                        </div>
                                    </div>
                                    <div>
                                        <textarea name="terms_of_use_content" id="terms_of_use_content" rows="5" class="nsofts-editor mb-4">
                                            <?= esc(stripslashes($settings_data['terms_of_use_content'])) ?>
                                        </textarea>
                                    </div>
                                    <button type="submit" name="submit_terms" class="btn btn-primary" style="min-width: 120px;">Save</button>
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
        const activeTab = localStorage.getItem('activeTabWeb') || '#nsofts_setting_content_1'; // Default to General tab
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
                localStorage.setItem('activeTabWeb', targetTab);
            });
        });
    });
</script>
<script type="text/javascript">
    function fileValidation2(){
        var fileInput = document.getElementById('fileupload2');
        var filePath = fileInput.value;
        var allowedExtensions = /(\.png|.PNG|.jpg|.JPG")$/i;
        if(!allowedExtensions.exec(filePath)){
            if(filePath!='')
            fileInput.value = '';
            $.notify('Please upload file having extension .png, .jpg, .JPG .PNG!', { position:"top right",className: 'error'} ); 
            return false;
         }else {
            if (fileInput.files && fileInput.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $("#imagePreview2").find("img").attr("src", e.target.result);
                };
                reader.readAsDataURL(fileInput.files[0]);
            }
        }
    }
</script>