<div class="container">
    <h2 class="page-header">API Settings</h2>

    <form method="post" class="form-horizontal" enctype="multipart/form-data">

        <?php foreach ($api_settings as $key => $setting): ?>
        <div class="form-group">
            <label class="col-sm-2 control-label"><?php echo ucwords(str_replace('_', ' ', $key)); ?></label>
            <div class="col-sm-6">
                <?php if (isset($setting['field_type']) && $setting['field_type'] == 'image'): ?>
                    <!-- Image Field -->
                    <div class="image-field-container">
                        <?php if (!empty($setting['value'])): ?>
                            <div class="current-image mb-2">
                                <?php 
                                $image_path = $setting['value'];
                                // Check if path already includes uploads/
                                if (strpos($image_path, 'uploads/') === 0) {
                                    $image_url = base_url($image_path);
                                } else {
                                    $image_url = base_url('uploads/' . $image_path);
                                }
                                ?>
                                <img src="<?php echo $image_url; ?>" alt="<?php echo $key; ?>" style="max-width: 200px; max-height: 150px;" class="img-thumbnail">
                                <br>
                                <small class="text-muted">Current: <?php echo $setting['value']; ?></small>
                            </div>
                        <?php endif; ?>
                        <input type="file" class="form-control" name="api_settings[<?php echo $key; ?>]" accept="image/*">
                        <input type="hidden" name="field_types[<?php echo $key; ?>]" value="image">
                        <?php if (!empty($setting['value'])): ?>
                            <input type="hidden" name="existing_values[<?php echo $key; ?>]" value="<?php echo $setting['value']; ?>">
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <!-- Text Field -->
                    <input type="text" class="form-control" name="api_settings[<?php echo $key; ?>]" value="<?php echo isset($setting['value']) ? $setting['value'] : ''; ?>">
                    <input type="hidden" name="field_types[<?php echo $key; ?>]" value="text">
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>

        <!-- Add New API Setting Field -->
        <div class="form-group">
            <label class="col-sm-2 control-label">Add New API Setting</label>
            <div class="col-sm-6">
                <div class="row">
                    <div class="col-sm-4">
                        <input type="text" class="form-control" name="new_api_setting_key" placeholder="API Setting Key">
                    </div>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" name="new_api_setting_value" placeholder="API Setting Value">
                    </div>
                    <div class="col-sm-4">
                        <select class="form-control" name="new_api_setting_type">
                            <option value="text">Text</option>
                            <option value="image">Image</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-6">
                <button type="submit" class="btn btn-primary">Save API Settings</button>
            </div>
        </div>

    </form>
</div>

<style>
.image-field-container {
    border: 1px solid #ddd;
    padding: 10px;
    border-radius: 4px;
    background-color: #f9f9f9;
}
.current-image {
    text-align: center;
}
</style> 