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
                    <a href="<?= base_url('ns-admin/notification-onesignal') ?>" class="btn btn-primary d-inline-flex align-items-center justify-content-center">
                        <i class="ri-add-line"></i>
                        <span class="ps-1 text-nowrap d-none d-sm-block">Send Notification</span>
                    </a>
                </div>
            </div>
            
            <div class="card-body p-4">
                <?php if(!empty($result)){ ?>
                    <div class="row g-4">
                        <table class="table ">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th  class="display-desktop">Msg</th>
                                    <th class="text-center">Date</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="load-more-container">
                                <?php $i=0; foreach($result as $row){ ?>
                                    <tr class="card-item">
                                        <td><?= esc($row['notification_title']) ?></td>
                                        <td  class="display-desktop"><?= esc($row['notification_msg']) ?></td>
                                        <td class="text-center"><?= esc(date('d-m-Y', $row['notification_on'])) ?></td>
                                        <td class="text-center">
                                            <a href="javascript:void(0)" class="btn btn-danger btn-icon btn_delete" data-action="<?= base_url('ns-admin/delete-notification/'.$row['id']) ?>" style="padding: 10px 10px !important;" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete">
                                                <i class="ri-delete-bin-line"></i>
                                            </a>
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