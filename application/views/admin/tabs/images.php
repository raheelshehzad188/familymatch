<h3>User Images</h3>
<?php if (!empty($images)) foreach ($images as $img): ?>
    <img src="<?= base_url($img->thumb_path) ?>" width="100" style="margin:5px;">
<?php endforeach; ?>
<?php if (empty($images)) echo "<p>No images uploaded.</p>"; ?>
