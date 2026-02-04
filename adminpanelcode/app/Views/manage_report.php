<?= $this-> include('templates/header');?>
<main id="nsofts_main">
    <div class="nsofts-container">
        <div class="card h-100">
            <div class="card-top d-md-inline-flex align-items-center justify-content-between py-3 px-4">
                <div class="d-inline-flex align-items-center text-decoration-none fw-semibold">
                    <span class="ps-2 lh-1"><?= isset($pageTitle) ? esc($pageTitle) : "" ?></span>
                </div>
                <div class="d-flex mt-2 mt-md-0">
                </div>
            </div>
            <div class="card-body p-4">
                <?php if(!empty($result)){ ?>
                    <div class="row g-4">
                        <table class="table ">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Title</th>
                                    <th class="display-desktop">Report</th>
                                    <th class="text-center display-desktop">Date</th>
                                    <th style="width: 200px;" class="text-center display-desktop">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="load-more-container">
                                <?php $i=0; foreach($result as $row){ ?>
                                    <tr class="card-item">
                                        <td><?= esc($row['user_name']) ?></td>
                                        <td><?= esc($row['report_title']) ?></td>
                                        <td class="display-desktop"><?= esc($row['report_msg']) ?></td>
                                        <td class="text-center display-desktop"><?= esc(date('d-m-Y', $row['report_on'])) ?></td>
                                        
                                        <td class="text-center display-desktop">
                                            <a href="<?= base_url('ns-admin/create-report/'.$row['id']) ?>" class="btn btn-primary btn-icon" style="padding: 10px 10px !important;  margin-right: 10px !important;" data-bs-toggle="tooltip" data-bs-placement="top" title="View Report">
                                                <i class="ri-honour-line"></i>
                                            </a>
                                            <a href="javascript:void(0)" class="btn btn-danger btn-icon btn_delete display-desktop" data-action="<?= base_url('ns-admin/delete-report/'.$row['id']) ?>" style="padding: 10px 10px !important;" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete">
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