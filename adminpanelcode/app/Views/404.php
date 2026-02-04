<?= $this-> include('templates/header_data');?>
<main class="d-flex justify-content-center align-items-center py-5 min-vh-100">
    <div class="container">
        <div class="col-xl-5 col-lg-6 col-md-8 col-sm-10 text-center mx-auto">
            <h3>Page Not Found :(</h3>
            <p>Oops! The requested URL was not found on this server.</p>
            <a href="<?= base_url('') ?>" class="btn btn-primary">Back to Homepage</a>
        </div>
    </div>
</main>
<?= $this-> include('templates/footer_data');?>