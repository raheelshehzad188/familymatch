<script>
	console.log(typeof $.notify);

    <?php if(isset($_SESSION['error'])): 
    	$error = $_SESSION['error'];
    	unset($_SESSION['error']);
    	?>
        $(document).ready(function() {
            $.notify("<?= $error; ?>", {
                className: 'error',
                globalPosition: 'top right'
            });
        });
    <?php endif; ?>
</script>
<?php if (isset($_SESSION['success'])): 
	$success = $_SESSION['success'];
    unset($_SESSION['success']);
	?>
    <script>
        $(document).ready(function() {
            $.notify("<?php echo $success; ?>", {
                className: 'success',
                globalPosition: 'top right',
                autoHide: true, // Message will disappear after a few seconds
                autoHideDelay: 5000, // Delay in milliseconds (5000ms = 5 seconds)
            });
        });
    </script>
<?php endif; ?>

