<?= $this->include('templates/header'); ?>

<?php 
    $file_path = base_url();
    $web_api_file_path = base_url('api-web');
    $privacy_policy_file_path = base_url('privacy-policy');
    $terms_file_path = base_url('terms');
    $delete_request_file_path = base_url('account-delete-request');
    $reseller_file_path = base_url('reseller');

    $sbox_api_file_path = base_url().'sbox_api.php?token=xxxxxxxxx&action';

    $sbox_api_device_id_stream = 'action=create&create_type=device_id_stream&username=xxxxxx&password=xxxxxx&dns=https://abc.com&device_id=xxxxxxxxxx';
    $sbox_api_device_id_xtream  = 'action=create&create_type=device_id_xtream&username=xxxxxx&password=xxxxxx&dns=https://abc.com&device_id=xxxxxxxxxx';

    $sbox_api_activation_stream = 'action=create&create_type=activation_stream&username=xxxxxx&password=xxxxxx&dns=https://abc.com';
    $sbox_api_activation_xtream  = 'action=create&create_type=activation_xtream&username=xxxxxx&password=xxxxxx&dns=https://abc.com';
?>

<!-- Start: main -->
<main id="nsofts_main">
    <div class="nsofts-container">
        
        <!-- Base URLs -->
        <div class="row g-4 mb-3">
            <div class="col-12">
                <div class="card h-100">
                    <div class="card-body p-3">
                        <h5 class="mb-3"><?= isset($pageTitle) ? esc($pageTitle) : "" ?></h5>

                        <div class="pb-clipboard mb-2">
                            <span class="pb-clipboard__url">
                                <a class="fw-bold text-decoration-none" style="color: var(--ns-primary);">Base URL : </a>
                                <span id="clipboard_base_url"><?= esc($file_path) ?></span>
                            </span>
                            <a class="pb-clipboard__link copy-btn" href="javascript:void(0);" data-clipboard-target="#clipboard_base_url" title="Copy URL">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1" />
                                </svg>
                            </a>
                        </div>
                        
                        <div class="pb-clipboard mb-2">
                            <span class="pb-clipboard__url">
                                <a class="fw-bold text-decoration-none" style="color: var(--ns-primary);">Privacy Policy : </a>
                                <span id="clipboard_policy"><?= esc($privacy_policy_file_path) ?></span>
                            </span>
                            <a class="pb-clipboard__link copy-btn" href="javascript:void(0);" data-clipboard-target="#clipboard_policy" title="Copy URL">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1" />
                                </svg>
                            </a>
                        </div>

                        <div class="pb-clipboard mb-2">
                            <span class="pb-clipboard__url">
                                <a class="fw-bold text-decoration-none" style="color: var(--ns-primary);">Terms & Conditions : </a>
                                <span id="clipboard_terms"><?= esc($terms_file_path) ?></span>
                            </span>
                            <a class="pb-clipboard__link copy-btn" href="javascript:void(0);" data-clipboard-target="#clipboard_terms" title="Copy URL">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Play Store Policy -->
        <div class="row g-4 mb-3">
            <div class="col-12">
                <div class="card h-100">
                    <div class="card-body p-3">
                        <h5 class="mb-3">Play Store Policy</h5>
                        <div class="pb-clipboard mb-2">
                            <span class="pb-clipboard__url">
                                <a class="fw-bold text-decoration-none" style="color: var(--ns-primary);">Account Delete Request : </a>
                                <span id="clipboard_delete"><?= esc($delete_request_file_path) ?></span>
                            </span>
                            <a class="pb-clipboard__link copy-btn" href="javascript:void(0);" data-clipboard-target="#clipboard_delete" title="Copy URL">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reseller -->
        <div class="row g-4 mb-3">
            <div class="col-12">
                <div class="card h-100">
                    <div class="card-body p-3">
                        <h5 class="mb-3">Reseller Panel</h5>
                        <div class="pb-clipboard mb-2">
                            <span class="pb-clipboard__url">
                                <a class="fw-bold text-decoration-none" style="color: var(--ns-primary);">Reseller : </a>
                                <span id="clipboard_reseller"><?= esc($reseller_file_path) ?></span>
                            </span>
                            <a class="pb-clipboard__link copy-btn" href="javascript:void(0);" data-clipboard-target="#clipboard_reseller" title="Copy URL">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Custom API -->
        <div class="row g-4 mb-3">
            <div class="col-12">
                <div class="card h-100">
                    <div class="card-body p-3">
                        <h5 class="mb-3">Custom API</h5>

                        <div class="pb-clipboard mb-3">
                            <span class="pb-clipboard__url">
                                <a class="fw-bold text-decoration-none" style="color: var(--ns-primary);">API URL : </a>
                                <span id="clipboard_api"><?= esc($sbox_api_file_path) ?></span>
                            </span>
                            <a class="pb-clipboard__link copy-btn" href="javascript:void(0);" data-clipboard-target="#clipboard_api" title="Copy URL">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1" />
                                </svg>
                            </a>
                        </div>

                        <h6 class="mb-3 mt-4">Create User Device ID</h6>
                        <div class="pb-clipboard mb-2">
                            <span class="pb-clipboard__url"><a class="fw-bold text-decoration-none" style="color: var(--ns-primary);">1-Stream : </a>
                                <span id="clipboard_device_stream"><?= esc($sbox_api_device_id_stream) ?></span>
                            </span>
                            <a class="pb-clipboard__link copy-btn" href="javascript:void(0);" data-clipboard-target="#clipboard_device_stream" title="Copy URL">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1" />
                                </svg>
                            </a>
                        </div>

                        <div class="pb-clipboard mb-2">
                            <span class="pb-clipboard__url"><a class="fw-bold text-decoration-none" style="color: var(--ns-primary);">Xtream Codes or XUI : </a>
                                <span id="clipboard_device_xtream"><?= esc($sbox_api_device_id_xtream) ?></span>
                            </span>
                            <a class="pb-clipboard__link copy-btn" href="javascript:void(0);" data-clipboard-target="#clipboard_device_xtream" title="Copy URL">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1" />
                                </svg>
                            </a>
                        </div>

                        <h6 class="mb-3 mt-4">Create User Activation Code</h6>
                        <div class="pb-clipboard mb-2">
                            <span class="pb-clipboard__url"><a class="fw-bold text-decoration-none" style="color: var(--ns-primary);">1-Stream : </a>
                                <span id="clipboard_activation_stream"><?= esc($sbox_api_activation_stream) ?></span>
                            </span>
                            <a class="pb-clipboard__link copy-btn" href="javascript:void(0);" data-clipboard-target="#clipboard_activation_stream" title="Copy URL">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1" />
                                </svg>
                            </a>
                        </div>

                        <div class="pb-clipboard mb-2">
                            <span class="pb-clipboard__url"><a class="fw-bold text-decoration-none" style="color: var(--ns-primary);">Xtream Codes or XUI : </a>
                                <span id="clipboard_activation_xtream"><?= esc($sbox_api_activation_xtream) ?></span>
                            </span>
                            <a class="pb-clipboard__link copy-btn" href="javascript:void(0);" data-clipboard-target="#clipboard_activation_xtream" title="Copy URL">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</main>
<!-- End: main -->

<?= $this->include('templates/footer'); ?>

<script type="text/javascript">
$(document).ready(function() {
    // Universal copy handler
    $(document).on("click", ".copy-btn", function() {
        const targetSelector = $(this).data("clipboard-target");
        const element = document.querySelector(targetSelector);
        if (!element) {
            $.notify('Invalid target!', { position: "top right", className: 'error' });
            return;
        }
        const range = document.createRange();
        range.selectNode(element);
        const selection = window.getSelection();
        selection.removeAllRanges();
        selection.addRange(range);
        try {
            const success = document.execCommand('copy');
            $.notify(success ? 'Copied!' : 'Whoops, not copied!', {
                position: "top right",
                className: success ? 'success' : 'error'
            });
        } catch (err) {
            $.notify('Copy failed!', { position: "top right", className: 'error' });
        }
        selection.removeAllRanges();
    });
});
</script>
