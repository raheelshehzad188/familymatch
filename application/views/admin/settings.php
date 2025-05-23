<div class="container">
    <h2 class="page-header">Settings</h2>

    <form method="post" class="form-horizontal">

        <div class="form-group">
            <label class="col-sm-2 control-label">Admin Email</label>
            <div class="col-sm-6">
                <input type="email" class="form-control" name="meta[admin_email]" value="<?php echo isset($settings['admin_email']) ? $settings['admin_email'] : ''; ?>">
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label">Site Title</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="meta[site_title]" value="<?php echo isset($settings['site_title']) ? $settings['site_title'] : ''; ?>">
            </div>
        </div>

        <!-- Add more fields here as needed -->

        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-6">
                <button type="submit" class="btn btn-primary">Save Settings</button>
            </div>
        </div>

    </form>
</div>
