<h2>Edit Interest</h2>
<form method="post" enctype="multipart/form-data">
    <input type="text" name="title" class="form-control" value="<?= $interest['title'] ?>" required><br>
    <?php if ($interest['image']): ?>
        <img src="<?= base_url($interest['image']) ?>" width="80"><br>
    <?php endif; ?>
    <input type="file" name="image" class="form-control"><br>
    <button type="submit" class="btn btn-primary">Update</button>
</form>
