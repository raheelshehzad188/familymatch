<h2>Add <?= $label ?></h2>
<form method="post" enctype="multipart/form-data">
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
    <button type="submit" class="btn btn-success">Submit</button>
</form>
