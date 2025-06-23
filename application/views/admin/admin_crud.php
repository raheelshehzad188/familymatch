
    <h2 class="text-center"><?= isset($admin_user) ? 'Edit Admin' : 'Add Admin' ?></h2>

    <div class="panel panel-primary">
        <div class="panel-heading"><?= isset($admin_user) ? 'Edit Admin' : 'Add New Admin' ?></div>
        <div class="panel-body">
            <form method="post" action="<?= site_url('admin/admins/save') ?>">
                <input type="hidden" name="id" value="<?= isset($admin_user) ? $admin_user->id : '' ?>">

                <div class="form-group">
                    <label>Name:</label>
                    <input type="text" name="name" value="<?= isset($admin_user) ? $admin_user->name : '' ?>" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Username:</label>
                    <input type="text" name="username" value="<?= isset($admin_user) ? $admin_user->username : '' ?>" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Email:</label>
                    <input type="email" name="email" value="<?= isset($admin_user) ? $admin_user->email : '' ?>" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Password: <?= isset($admin_user) ? '(Leave blank to keep current password)' : '' ?></label>
                    <input type="password" name="password" class="form-control" <?= isset($admin_user) ? '' : 'required' ?>>
                </div>

                <div class="form-group">
                    <label>Role:</label>
                    <select name="role_id" class="form-control" required>
                        <option value="">Select Role</option>
                        <?php foreach($roles as $role): ?>
                            <option value="<?= $role->id ?>" <?= isset($admin_user) && $admin_user->role_id == $role->id ? 'selected' : '' ?>>
                                <?= $role->title ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit" class="btn btn-success"><?= isset($admin_user) ? 'Update' : 'Add' ?></button>
            </form>
        </div>
    </div>

    <h2 class="text-center">Admin List</h2>

    <table class="table table-bordered table-striped">
        <thead>
            <tr class="info">
                <th>ID</th>
                <th>Name</th>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($admins as $a): ?>
            <tr>
                <td><?= $a->id ?></td>
                <td><?= $a->name ?></td>
                <td><?= $a->username ?></td>
                <td><?= $a->email ?></td>
                <td><?= $a->role_title ?></td>
                <td><?= $a->created_at ?></td>
                <td>
                    <a href="<?= site_url('admin/admins/index/'.$a->id) ?>" class="btn btn-xs btn-primary">Edit</a>
                    <a href="<?= site_url('admin/admins/delete/'.$a->id) ?>" onclick="return confirm('Are you sure?')" class="btn btn-xs btn-danger">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>