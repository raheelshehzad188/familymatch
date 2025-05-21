<h2>Edit <?= $label ?></h2>
<?php
function get_file_type($file_path) {
    // First check if file exists
    if (!file_exists($file_path)) {
        return 'invalid'; // Or return false
    }

    // Get MIME type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $file_path);
    finfo_close($finfo);

    // Check if it's an image
    if (strpos($mime_type, 'image/') === 0) {
        return 'image';
    } else {
        return 'other';
    }
}
?>
<form method="post"  enctype="multipart/form-data">
    <?php
    foreach ($fields as $key => $value) {

        $file = dirname(__FILE__).'/fields/'.$value['type'].'.php';

        if(file_exists($file))
        {
            include $file;
        }
        else
        {

            $file = 'fields/default.php';
            include $file;
        }


        
    }
    ?>
    <button type="submit" class="btn btn-primary">Save</button>
</form>
