<footer class="py-4 bg-light mt-auto">
                    <div class="container-fluid px-4">
                        <div class="d-flex align-items-center justify-content-between small">
                            <div class="text-muted">Copyright &copy; Your Website 2023</div>
                            <div>
                                <a href="#">Privacy Policy</a>
                                &middot;
                                <a href="#">Terms &amp; Conditions</a>
                            </div>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
        <script type="text/javascript">
            var BASE_URL = '<?= base_url(); ?>';
            <?php
            if(isset($dtable))
            {
                ?>
                var DTABLE = '<?= $dtable ?>';
                <?php
            }

            ?>
        </script>
        <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>


        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

        <script src="<?php echo $assets_url; ?>js/scripts.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
        <script src="<?php echo $assets_url; ?>assets/demo/chart-area-demo.js"></script>
        <script src="<?php echo $assets_url; ?>assets/demo/chart-bar-demo.js"></script>
        <?php
        if(isset($js))
        {
            foreach ($js as $key => $url) {
                ?>
                <script src="<?php echo $url; ?>"></script>

                <?php
            }
        }
        ?>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/notify/0.4.2/notify.min.js"></script>
        <?php
            if(isset($dtable))
            {
                ?>
                <script src="<?php echo $assets_url.'js/dtable.js'; ?>"></script>
                <?php
            }

            ?>
        <?php
            $this->load->view('flash_js');
        ?>
    </body>
</html>
