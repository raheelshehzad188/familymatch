<?php

include APPPATH . "views/admin/header.php";
?>
        <div id="layoutSidenav">
            <div id="layoutSidenav_nav">
                <?php
                var_dump(APPPATH."views/admin/".$side_bar);

include APPPATH."views/admin/".$side_bar;
?>
            </div>
            <div id="layoutSidenav_content">
                <?php
                echo $content;
                ?>
<?php
include APPPATH . "views/admin/footer.php";
?>
                