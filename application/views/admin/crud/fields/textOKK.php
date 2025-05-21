<div class="form-group">
  <label for="exampleInput"><?= $value['label'] ?>
    <?php
    if($value['is_required'])
    {
      ?>
      <span style="color: red;">*</span>
      <?php
    }
    ?>
  </label>
  <input type="text" class="form-control" id="exampleInput" placeholder="Enter <?= $value['label'] ?>" name="<?= $value['db_column'] ?>">
</div>
