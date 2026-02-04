    <!-- Vendor scripts -->
    <script src="<?= base_url('assets/js/jquery.min.js') ?>"></script>
    <script src="<?= base_url('assets/vendors/bootstrap/bootstrap.min.js') ?>"></script>
    <script src="<?= base_url('assets/vendors/notify/notify.min.js') ?>"></script>
    <script src="<?= base_url('assets/vendors/perfect-scrollbar/perfect-scrollbar.min.js') ?>"></script>
    <script src="<?= base_url('assets/vendors/quill/quill.min.js') ?>"></script>
    <script src="<?= base_url('assets/vendors/select2/select2.min.js') ?>"></script>
    <script src="<?= base_url('assets/vendors/sweetalerts2/sweetalert2.min.js') ?>"></script>
    <script src="<?= base_url('assets/vendors/chartjs/chart.min.js') ?>"></script>
    
    <!-- Main script -->
    <script src="<?= base_url('assets/js/main.js?v=1.0.0') ?>"></script>
    
    <script type="text/javascript">
    
        $(document).ready(function(event) {
            $(document).on("click", ".btn_enable_disable", function(e) {
                var _checked;
                
                var _currentElement = $(this);
                var _actionUrl = _currentElement.data("action");
                var _column = _currentElement.data("column");
                
                // Determine action based on checkbox state
                var _forAction = _currentElement.prop("checked") ? "enable" : "disable";
                
                $.ajax({
                    type: 'POST',
                    url: _actionUrl,
                    dataType: 'json',
                    data: {
                        for_action: _forAction,
                        column: _column
                    },
                    success: function(res) {
                        $.notify(res.msg, { position: "top right", className: res.class }); 
                    },
                    error: function() {
                        $.notify("Error updating status", { position: "top right", className: "error" });
                    }
                });
            });
        });
        
        $(document).on("click", ".btn_delete", function(e) {
            e.preventDefault();
            
            var _action = $(this).data("action");
            swal({
                title: "Are you sure to delete this?",
                type: "warning",
                confirmButtonClass: 'btn btn-primary m-2 px-3',
                cancelButtonClass: 'btn btn-danger m-2 px-3',
                buttonsStyling: false,
                showCancelButton: true,
                confirmButtonText: "Yes",
                cancelButtonText: "No",
                closeOnConfirm: false,
                closeOnCancel: false,
                showLoaderOnConfirm: true
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        url: _action,
                        success: function(res) {
                            location.reload();
                        }
                    });
                } else {
                    Swal.close();
                }
            });
        });
    </script>

        
    <script type="text/javascript">
        function copyToClipboard(el) {
            var text = el.innerText;
            
            if (window.clipboardData && window.clipboardData.setData) {
                return window.clipboardData.setData("Text", text);
                
            } else if (document.queryCommandSupported && document.queryCommandSupported("copy")) {
                var textarea = document.createElement("textarea");
                textarea.value = text;
                textarea.style.position = "fixed";  // Prevent scrolling to bottom of page in Microsoft Edge.
                el.appendChild(textarea);
                textarea.select();
                try {
                    return document.execCommand('copy');
                } catch (ex) {
                    console.warn("Copy to clipboard failed.", ex);
                    return prompt("Copy to clipboard: Ctrl+C, Enter", text);
                } finally {
                    el.removeChild(textarea);
                }
            }
        }
    </script>
    
    <?php
        if(!empty(session()->get('response_msg'))) {
            $message = session()->get('response_msg');
            $array_items = array('message' ,'class');
            session()->remove('response_msg',$array_items);
        ?>
            <script type="text/javascript">
                var _msg = '<?= esc($message['message']) ?>';
                var _class = '<?= esc($message['class']) ?>';
                _msg=_msg.replace(/(<([^>]+)>)/ig,"");
                $('.notifyjs-corner').empty();
                $.notify(
                _msg, 
                { position:"top right",className: _class} ); 
            </script>
     <?php } ?>
     
     
    <!-- SECURE HEADER CODE WITH PURIFICATION -->
    <?= isset($settings['footer_code']) ? service('purify')->purifyHtml(html_entity_decode($settings['footer_code'])) : '' ?>

</body>
</html>