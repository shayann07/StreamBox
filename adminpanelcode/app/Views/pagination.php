<style>
    .disabled>.page-link, .page-link.disabled {
        background-color: #ffffff00;
        border-color: #dee2e600;
    }
</style>

<?php
if (!isset($page) || $page < 1) $page = 1;
$prev = $page - 1;
$next = $page + 1;
$lastpage = $total_pages;
$LastPagem1 = $lastpage - 1;
$stages = $stages ?? 2; // Default value if not set
$targetpage = htmlspecialchars($targetpage ?? ''); // Prevent undefined variable notice

$paginate = '';
if ($lastpage > 1) {
    $paginate .= "<nav aria-label='Page navigation example'>";
    $paginate .= "<ul class='pagination justify-content-center'>";

    // Prev Link
    $paginate .= ($page > 1) 
        ? "<li class='page-item'><a class='page-link' href='$targetpage?page=$prev'>Prev</a></li>" 
        : "<li class='page-item disabled'><a class='page-link'>Prev</a></li>";

    // First two pages
    if ($page > ($stages * 2) + 2) {
        $paginate .= "<li class='page-item'><a class='page-link' href='$targetpage?page=1'>1</a></li>";
        $paginate .= "<li class='page-item'><a class='page-link' href='$targetpage?page=2'>2</a></li>";
        $paginate .= "<li class='page-item disabled'><a class='page-link'>...</a></li>";
    }

    // Main page range
    $start = max(1, $page - $stages);
    $end = min($lastpage, $page + $stages);
    for ($counter = $start; $counter <= $end; $counter++) {
        if ($counter == $page) {
            $paginate .= "<li class='page-item active'><a class='page-link'>$counter</a></li>";
        } else {
            $paginate .= "<li class='page-item'><a class='page-link' href='$targetpage?page=$counter'>$counter</a></li>";
        }
    }

    // Last two pages
    if ($page < $lastpage - ($stages * 2)) {
        $paginate .= "<li class='page-item disabled'><a class='page-link'>...</a></li>";
        $paginate .= "<li class='page-item'><a class='page-link' href='$targetpage?page=$LastPagem1'>$LastPagem1</a></li>";
        $paginate .= "<li class='page-item'><a class='page-link' href='$targetpage?page=$lastpage'>$lastpage</a></li>";
    }

    // Next Link
    $paginate .= ($page < $lastpage) 
        ? "<li class='page-item'><a class='page-link' href='$targetpage?page=$next'>Next</a></li>" 
        : "<li class='page-item disabled'><a class='page-link'>Next</a></li>";

    $paginate .= "</ul>";
    $paginate .= "</nav>";
}

echo $paginate;
?>