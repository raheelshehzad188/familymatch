<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4 text-gray-800">Site Options Management</h1>
            
            <!-- Success/Error Messages -->
            <?php if($this->session->flashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo $this->session->flashdata('success'); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if($this->session->flashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo $this->session->flashdata('error'); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Form 1: Update Existing Options -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Update Existing Options</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in">
                            <a class="dropdown-item" href="#" onclick="exportOptions()">Export Options</a>
                            <a class="dropdown-item" href="#" onclick="importOptions()">Import Options</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form method="post" action="<?php echo base_url('admin/settings/save'); ?>" class="form-horizontal" enctype="multipart/form-data">
                        
                        <?php if(!empty($settings)): ?>
                            <div class="row">
                                <?php foreach ($settings as $key => $setting): ?>
                                <div class="col-md-6 mb-3">
                                    <div class="card border-left-primary shadow h-100 py-2">
                                        <div class="card-body">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                        <?php echo ucwords(str_replace('_', ' ', $key)); ?>
                                                    </div>
                                                    <div class="h6 mb-0 font-weight-bold text-gray-800">
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
                                                                        <img src="<?php echo $image_url; ?>" alt="<?php echo $key; ?>" style="max-width: 100px; max-height: 75px;" class="img-thumbnail">
                                                                        <br>
                                                                        <small class="text-muted">Current: <?php echo $setting['value']; ?></small>
                                                                    </div>
                                                                <?php endif; ?>
                                                                <input type="file" class="form-control form-control-sm" name="<?php echo $key; ?>" accept="image/*">
                                                                <input type="hidden" name="field_types[<?php echo $key; ?>]" value="image">
                                                                <?php if (!empty($setting['value'])): ?>
                                                                    <input type="hidden" name="existing_values[<?php echo $key; ?>]" value="<?php echo $setting['value']; ?>">
                                                                <?php endif; ?>
                                                            </div>
                                                        <?php else: ?>
                                                            <!-- Text Field -->
                                                            <input type="text" class="form-control form-control-sm" name="settings[<?php echo $key; ?>]" value="<?php echo isset($setting['value']) ? $setting['value'] : ''; ?>" placeholder="Enter value">
                                                            <input type="hidden" name="field_types[<?php echo $key; ?>]" value="text">
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                                <div class="col-auto">
                                                    <i class="fas fa-<?php echo (isset($setting['field_type']) && $setting['field_type'] == 'image') ? 'image' : 'text'; ?> fa-2x text-gray-300"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <div class="text-center mt-3">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-save"></i> Update All Options
                                </button>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="fas fa-cog fa-3x text-gray-300 mb-3"></i>
                                <h5 class="text-gray-500">No options found</h5>
                                <p class="text-gray-400">Create your first option using the form below.</p>
                            </div>
                        <?php endif; ?>
                    </form>
                </div>
            </div>

            <!-- Form 2: Add New Option -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Create New Option</h6>
                </div>
                <div class="card-body">
                    <form method="post" action="<?php echo base_url('admin/settings/add_new'); ?>" class="form-horizontal" enctype="multipart/form-data">
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Option Key</label>
                                <input type="text" class="form-control" name="new_setting_key" placeholder="e.g., site_logo, contact_email" required>
                                <small class="form-text text-muted">Use lowercase with underscores</small>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Field Type</label>
                                <select class="form-control" name="new_setting_type" id="new_setting_type" onchange="toggleValueField()">
                                    <option value="text">Text</option>
                                    <option value="image">Image</option>
                                    <option value="textarea">Textarea</option>
                                    <option value="select">Select</option>
                                    <option value="checkbox">Checkbox</option>
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Option Group</label>
                                <select class="form-control" name="option_group">
                                    <option value="general">General</option>
                                    <option value="appearance">Appearance</option>
                                    <option value="social">Social Media</option>
                                    <option value="contact">Contact Info</option>
                                    <option value="api">API Settings</option>
                                    <option value="custom">Custom</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3" id="text_value_field">
                                <label class="form-label">Option Value</label>
                                <input type="text" class="form-control" name="new_setting_value" placeholder="Enter option value">
                            </div>

                            <div class="col-md-6 mb-3" id="textarea_value_field" style="display: none;">
                                <label class="form-label">Option Value</label>
                                <textarea class="form-control" name="new_setting_textarea" rows="3" placeholder="Enter option value"></textarea>
                            </div>

                            <div class="col-md-6 mb-3" id="image_value_field" style="display: none;">
                                <label class="form-label">Upload Image</label>
                                <input type="file" class="form-control" name="new_setting_image" accept="image/*">
                                <small class="form-text text-muted">Upload an image file (JPG, PNG, GIF)</small>
                            </div>

                            <div class="col-md-6 mb-3" id="select_value_field" style="display: none;">
                                <label class="form-label">Select Options</label>
                                <textarea class="form-control" name="new_setting_select_options" rows="3" placeholder="Enter options separated by comma (e.g., option1,option2,option3)"></textarea>
                                <small class="form-text text-muted">Enter options separated by commas</small>
                            </div>

                            <div class="col-md-6 mb-3" id="checkbox_value_field" style="display: none;">
                                <label class="form-label">Default Value</label>
                                <select class="form-control" name="new_setting_checkbox_value">
                                    <option value="1">Enabled (Yes)</option>
                                    <option value="0">Disabled (No)</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" name="option_description" rows="2" placeholder="Describe what this option does..."></textarea>
                            </div>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-plus"></i> Create New Option
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Options Summary -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Options Summary</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Total Options
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo count($settings); ?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-cog fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                Text Options
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                <?php 
                                                $text_count = 0;
                                                foreach($settings as $setting) {
                                                    if(isset($setting['field_type']) && $setting['field_type'] == 'text') $text_count++;
                                                }
                                                echo $text_count;
                                                ?>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-font fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-info shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                                Image Options
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                <?php 
                                                $image_count = 0;
                                                foreach($settings as $setting) {
                                                    if(isset($setting['field_type']) && $setting['field_type'] == 'image') $image_count++;
                                                }
                                                echo $image_count;
                                                ?>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-image fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-warning shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                Last Updated
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">Today</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.image-field-container {
    border: 1px solid #e3e6f0;
    padding: 8px;
    border-radius: 4px;
    background-color: #f8f9fc;
}
.current-image {
    text-align: center;
}
.card {
    border: 1px solid #e3e6f0;
    border-radius: 0.35rem;
}
.card-header {
    background-color: #f8f9fc;
    border-bottom: 1px solid #e3e6f0;
    font-weight: bold;
}
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}
.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}
.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}
.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}
</style>

<script>
function toggleValueField() {
    var fieldType = document.getElementById('new_setting_type').value;
    var textField = document.getElementById('text_value_field');
    var textareaField = document.getElementById('textarea_value_field');
    var imageField = document.getElementById('image_value_field');
    var selectField = document.getElementById('select_value_field');
    var checkboxField = document.getElementById('checkbox_value_field');
    
    // Hide all fields first
    textField.style.display = 'none';
    textareaField.style.display = 'none';
    imageField.style.display = 'none';
    selectField.style.display = 'none';
    checkboxField.style.display = 'none';
    
    // Show appropriate field
    if (fieldType === 'text') {
        textField.style.display = 'block';
    } else if (fieldType === 'textarea') {
        textareaField.style.display = 'block';
    } else if (fieldType === 'image') {
        imageField.style.display = 'block';
    } else if (fieldType === 'select') {
        selectField.style.display = 'block';
    } else if (fieldType === 'checkbox') {
        checkboxField.style.display = 'block';
    }
}

function exportOptions() {
    // Export functionality
    alert('Export functionality will be implemented');
}

function importOptions() {
    // Import functionality
    alert('Import functionality will be implemented');
}
</script>
