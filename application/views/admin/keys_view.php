<!-- API Key Form + Table (Bootstrap 3 Compatible) -->
<div class="container">
<form method="POST" class="form-horizontal" role="form">
    <?php if (isset($msg)) echo $msg; ?>

    <div class="form-group">
        <label for="host" class="col-sm-2 control-label">Host</label>
        <div class="col-sm-12">
            <input type="text" class="form-control" name="host" id="host" placeholder="Enter Host" required>
        </div>
    </div>

    <div class="form-group">
        <label for="key" class="col-sm-2 control-label">API Key</label>
        <div class="col-sm-12">
            <input type="text" class="form-control" name="key" id="key" placeholder="Enter API Key" required>
        </div>
    </div>

    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <button type="submit" class="btn btn-primary">Save Key</button>
        </div>
    </div>
</form>

<hr>

<h4>Saved API Keys</h4>
<table class="table table-bordered table-striped table-hover">
    <thead>
        <tr>
            <th>#</th>
            <th>Host</th>
            <th>API Key</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($keys)) : $i = 1; foreach ($keys as $row) : ?>
        <tr>
            <td><?= $i++ ?></td>
            <td><?= htmlspecialchars($row->host) ?></td>
            <td>
                <input type="text" class="form-control" id="key<?= $row->id ?>" value="<?= htmlspecialchars($row->key) ?>" readonly>
            </td>
            <td>
                <button class="btn btn-xs btn-info" onclick="copyKey('key<?= $row->id ?>')">Copy</button>
                <a href="<?= base_url('admin/admin_keys/delete/' . $row->id) ?>" class="btn btn-xs btn-danger" onclick="return confirm('Are you sure to delete this key?')">Remove</a>
            </td>
        </tr>
        <?php endforeach; else: ?>
        <tr>
            <td colspan="4" class="text-center">No API keys found.</td>
        </tr>
        <?php endif; ?>
    </tbody>
</table>
</div>
<script>
function copyKey(id) {
    var copyText = document.getElementById(id);
    copyText.select();
    copyText.setSelectionRange(0, 99999); // For mobile devices
    document.execCommand("copy");
    alert("Copied the key: " + copyText.value);
}
</script>
