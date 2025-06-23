<!DOCTYPE html>
<html>
<head>
    <title><?= isset($admin) ? 'Edit' : 'Add' ?> Admin</title>
</head>
<body>
<h2><?= isset($admin) ? 'Edit' : 'Add' ?> Admin</h2>
<form method="post" action="<?= site_url('admins/save') ?>">
    <input type="hidden" name="id" value="<?= isset($admin) ? $admin->id : '' ?>">

    Name: <input type="text" name="name" value="<?= isset($admin) ? $admin->name : '' ?>" required><br><br>
    Username: <input type="text" name="username" value="<?= isset($admin) ? $admin->username : '' ?>" required><br><br>
    Email: <input type="email" name="email" value="<?= isset($admin) ? $admin->email : '' ?>" required><br><br>
    Password: <input type="password" name="password" <?= isset($admin) ? '' : 'required' ?>><br><br>
    Role: <input type="text" name="role" value="<?= isset($admin) ? $admin->role : 'admin' ?>" required><br><br>

    <button type="submit">Save</button>
</form>

<a href="<?= site_url('admins') ?>">Back to List</a>
</body>
</html>
