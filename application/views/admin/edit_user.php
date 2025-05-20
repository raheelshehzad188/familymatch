
<div class="container">
    <h2 class="text-center">Edit User</h2>

    <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success"><?= $this->session->flashdata('success'); ?></div>
    <?php endif; ?>

    <div class="row">
        <!-- Form Column -->
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading"><strong>Edit User Form</strong></div>
                <div class="panel-body">
                    <form method="post" action="<?= base_url('admin/user/update_user') ?>">
                        <input type="hidden" name="id" value="<?= $user->id ?>">

                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" name="name" class="form-control" value="<?= $user->name ?>" required>
                        </div>

                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" value="<?= $user->email ?>" required>
                        </div>

                        <div class="form-group">
                            <label>Phone</label>
                            <input type="text" name="phone" class="form-control" value="<?= $user->phone ?>" required>
                        </div>

                        <button type="submit" class="btn btn-primary">Update</button>
                        <a href="<?= base_url('admin/users') ?>" class="btn btn-default">Cancel</a>
                    </form>
                </div>
            </div>
        </div>

        <!-- Optional Right Column -->
        <div class="col-md-6">
            <div class="panel panel-info">
                <div class="panel-heading"><strong>User Info</strong></div>
                <div class="panel-body">
                    <p><strong>ID:</strong> <?= $user->id ?></p>
                    <p><strong>Registered At:</strong> <?= date('Y-m-d', strtotime($user->created_at)) ?></p>
                    <!-- Add more summary fields here if needed -->
                </div>
            </div>
        </div>
    </div>
</div>