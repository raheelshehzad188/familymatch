<?php

include APPPATH . "views/admin/header.php";
?>
        <div id="layoutSidenav">
            <div id="layoutSidenav_nav">
                <?php

include APPPATH . "views/admin/sidebar.php";
?>
            </div>
            <div id="layoutSidenav_content">
                <?php
                echo $content;
                ?>
<?php
include APPPATH . "views/admin/footer.php";
?>
                