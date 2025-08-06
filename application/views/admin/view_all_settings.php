<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4 text-gray-800">All Settings in Database Table</h1>
            
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Settings Table Contents</h6>
                </div>
                <div class="card-body">
                    <?php if(!empty($all_settings)): ?>
                        <div class="table-responsive">
                            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Meta Key</th>
                                        <th>Meta Value</th>
                                        <th>Field Type</th>
                                        <th>Created At</th>
                                        <th>Updated At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($all_settings as $setting): ?>
                                    <tr>
                                        <td><?php echo $setting->id; ?></td>
                                        <td>
                                            <strong><?php echo $setting->meta_key; ?></strong>
                                            <br>
                                            <small class="text-muted"><?php echo ucwords(str_replace('_', ' ', $setting->meta_key)); ?></small>
                                        </td>
                                        <td>
                                            <?php if($setting->field_type == 'image' && !empty($setting->meta_value)): ?>
                                                <?php 
                                                $image_path = $setting->meta_value;
                                                // Check if path already includes uploads/
                                                if (strpos($image_path, 'uploads/') === 0) {
                                                    $image_url = base_url($image_path);
                                                } else {
                                                    $image_url = base_url('uploads/' . $image_path);
                                                }
                                                ?>
                                                <img src="<?php echo $image_url; ?>" alt="<?php echo $setting->meta_key; ?>" style="max-width: 50px; max-height: 50px;" class="img-thumbnail">
                                                <br>
                                                <small><?php echo $setting->meta_value; ?></small>
                                            <?php else: ?>
                                                <?php echo strlen($setting->meta_value) > 50 ? substr($setting->meta_value, 0, 50) . '...' : $setting->meta_value; ?>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge badge-<?php echo $setting->field_type == 'image' ? 'info' : 'primary'; ?>">
                                                <?php echo ucfirst($setting->field_type); ?>
                                            </span>
                                        </td>
                                        <td><?php echo isset($setting->created_at) ? date('Y-m-d H:i:s', strtotime($setting->created_at)) : 'N/A'; ?></td>
                                        <td><?php echo isset($setting->updated_at) ? date('Y-m-d H:i:s', strtotime($setting->updated_at)) : 'N/A'; ?></td>
                                        <td>
                                            <a href="<?php echo base_url('admin/settings'); ?>" class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-3">
                            <div class="alert alert-info">
                                <strong>Total Rows:</strong> <?php echo count($all_settings); ?> settings in database
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-database fa-3x text-gray-300 mb-3"></i>
                            <h5 class="text-gray-500">No settings found in database</h5>
                            <p class="text-gray-400">Create your first setting using the settings page.</p>
                            <a href="<?php echo base_url('admin/settings'); ?>" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Create First Setting
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#dataTable').DataTable({
        "order": [[ 0, "desc" ]]
    });
});
</script> 