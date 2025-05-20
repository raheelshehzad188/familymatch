<h4>Interests</h4>

<?php 

if (!empty($interests)) : ?>
    <ul>
        <?php foreach ($interests as $interest): ?>
            <li><img src="<?= base_url('uploads/interests/'.$interest->image) ?>" height="20" height="20" /><?= htmlspecialchars($interest->title) ?></li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p>No interests selected for this profile.</p>
<?php endif; ?>
