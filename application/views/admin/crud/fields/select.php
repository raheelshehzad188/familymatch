<div class="form-group">
  <label for="exampleInput"><?= $value['label'] ?>
    <?php
    if($value['is_required'])
    {
      ?>
      <span style="color: red;">*</span>
      <?php
    }
    $options = explode(',',$value['options']);
    ?>
  </label>
  <select name="<?= $value['db_column'] ?>" class="form-control">
      <?php
      foreach($options as $kk=> $vv)
      {
          $op = explode(':',$vv);
          $k = $v = '';
          if(isset($op[0]))
          {
              $k = $op[0];
          }
          if(isset($op[1]))
          {
              $v = $op[1];
          }
          ?>
          <option value="<?= $k ?>" <?= (isset($row[$value['db_column']]) && $row[$value['db_column']] == $k?'selected':'') ?>><?= ucfirst($v) ?></option>
          
          <?php
      }
      
      ?>
      
  </select>
  
</div>
