<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
    .profile-img {
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #ddd;
    }
    .tab-buttons .btn {
        margin-right: 10px;
        margin-bottom: 10px;
        transition: background-color 0.3s, transform 0.2s;
    }
    .tab-buttons .btn:hover {
        background-color: #007bff;
        color: #fff;
        transform: translateY(-2px);
    }
    #tab-content {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 20px;
        background-color: #f8f9fa;
        min-height: 200px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.05);
    }
    /* Responsive tweaks */
    @media(max-width: 767px) {
        .tabs {
            flex-direction: column;
        }
        .tabs button {
            width: 100%;
            margin-bottom: 8px;
        }
    }
</style>

<div class="container my-4">
    <div class="d-flex align-items-center mb-4">
        <?php if (isset($user->img)) { ?>
            <img src="<?= base_url($user->img) ?>" alt="User Image" class="profile-img me-3" height="80" width="80"/>
        <?php } ?>
        <h2 class="mb-0">User Profile (User ID: <?= $user_id ?>)</h2>
    </div>

    <div class="d-flex flex-wrap tab-buttons mb-3" id="tabs">
        <button class="btn btn-outline-primary" onclick="loadTab('basic_info')">Basic Info</button>
        <button class="btn btn-outline-primary" onclick="loadTab('images')">Images</button>
        <button class="btn btn-outline-primary" onclick="loadTab('survey')">Survey</button>
        <button class="btn btn-outline-primary" onclick="loadTab('interst')">Interests</button>
        <button class="btn btn-outline-primary" onclick="loadTab('ethnicities')">Ethnicities</button>
        <button class="btn btn-outline-primary" onclick="loadTab('core_values')">Core Values</button>
    </div>

    <div id="tab-content" class="shadow-sm rounded-3">
        Please select a tab.
    </div>
</div>

<!-- JavaScript for loading tabs -->
<script>
function loadTab(tab) {
    const userId = <?= $user_id ?>;
    const contentDiv = document.getElementById('tab-content');
    contentDiv.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>';

    fetch(`<?= base_url() ?>admin/user/${tab}/${userId}`)
        .then(res => res.text())
        .then(html => {
            contentDiv.innerHTML = html;
        })
        .catch(() => {
            contentDiv.innerHTML = '<div class="alert alert-danger">Failed to load content. Please try again.</div>';
        });
}
</script>