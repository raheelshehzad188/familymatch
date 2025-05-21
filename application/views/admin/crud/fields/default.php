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
  <input type="<?= $value['type'] ?>" value="<?= (isset($row[$value['db_column']])?$row[$value['db_column']]:'') ?>" class="form-control" id="exampleInput" placeholder="Enter <?= $value['label'] ?>" name="<?= $value['db_column'] ?>">
  <?php
  if($value['type'] == 'file' && isset($row[$value['db_column']]) && $row[$value['db_column']])
  {
    $t = get_file_type($row[$value['db_column']]);
    if($t == 'image')
    {
      ?>
      <div>
      <a target="_blank" href="<?= base_url($row[$value['db_column']]) ?>"><img src="<?= base_url($row[$value['db_column']]) ?>" height="50" width="50" /></a>
    </div>

      <?php
    }
    else
    {


    ?>
    <div>
      <a target="_blank" href="<?= base_url($row[$value['db_column']]) ?>">View File</a>
    </div>

    <?php
    }
  }
  ?>
</div>
