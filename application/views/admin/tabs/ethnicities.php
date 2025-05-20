<h4>Ethnicities</h4>

<?php 
if (!empty($interests)) : ?>
    <ul>
        <?php foreach ($interests as $interest): ?><?= htmlspecialchars($interest->name) ?></li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p>No ethnicities selected for this profile.</p>
<?php endif; ?>
