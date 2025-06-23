<div class="form-group">
  <label for="exampleInput"><?= $value['label'] ?>
    <?php
    if($value['is_required'])
    {
      ?>
      <span style="color: red;">*</span>
      <?php
    }
    $CI =& get_instance();


    $options = $CI->db->select(array($value['opt_key'],$value['opt_val']))->get($value['tbl_name'])->result_array();
    ?>
  </label>
  <select name="<?= $value['db_column'] ?>" class="form-control">
      <?php
      foreach($options as $k=> $v)
      {
          
          ?>
          <option value="<?= $v[$value['opt_key']] ?>" <?= (isset($row[$value['db_column']]) && $row[$value['db_column']] == $v[$value['opt_key']]?'selected':'') ?>><?= ucfirst($v[$value['opt_val']]) ?></option>
          
          <?php
      }
      
      ?>
      
  </select>
  
</div>
