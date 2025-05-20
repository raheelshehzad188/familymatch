<style>
        .tabs button { margin-right: 10px; padding: 8px 16px; cursor: pointer; }
        #tab-content { border: 1px solid #ccc; padding: 15px; margin-top: 10px; }
    </style>
<?php
if(isset($user->img))
{
	?>
	<img src="<?= base_url($user->img) ?>" height="40" width="40"/>
	<?php
}
?>
<h2>User Profile (User ID: <?= $user_id ?>)</h2>

<div class="tabs">
    <button onclick="loadTab('basic_info')">Basic Info</button>
    <button onclick="loadTab('images')">Images</button>
    <button onclick="loadTab('survey')">Survey</button>
    <button onclick="loadTab('interst')">Interests</button>
    <button onclick="loadTab('ethnicities')">Ethnicities</button>
</div>

<div id="tab-content">Please select a tab.</div>

<!-- views/admin/user_profile_ajax_view.php -->
<script>
function loadTab(tab) {
    const userId = <?= $user_id ?>;
    document.getElementById('tab-content').innerHTML = 'Loading...';

    fetch(`<?= base_url() ?>admin/user/${tab}/${userId}`)
        .then(res => res.text())
        .then(html => {
            document.getElementById('tab-content').innerHTML = html;
        });
}
</script>
