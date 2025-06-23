<!DOCTYPE html>
<html>
<head>
    <title>Admin List</title>
</head>
<body>
<h2>Admin List</h2>

<a href="<?= site_url('admins/add') ?>">Add New Admin</a><br><br>

<table border="1" cellpadding="10" cellspacing="0">
    <thead>
        <tr>
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
        <?php foreach($admins as $admin): ?>
        <tr>
            <td><?= $admin->id ?></td>
            <td><?= $admin->name ?></td>
            <td><?= $admin->username ?></td>
            <td><?= $admin->email ?></td>
            <td><?= $admin->role ?></td>
            <td><?= $admin->created_at ?></td>
            <td>
                <a href="<?= site_url('admins/edit/'.$admin->id) ?>">Edit</a> |
                <a href="<?= site_url('admins/delete/'.$admin->id) ?>" onclick="return confirm('Are you sure?')">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</body>
</html>
