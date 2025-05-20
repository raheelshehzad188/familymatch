

<h2>User Profile: <?= $user->name ?></h2>

<div class="tabs">
    <button onclick="showTab('tab_basic')">Basic Info</button>
    <button onclick="showTab('tab_images')">Images</button>
    <button onclick="showTab('tab_survey')">Survey</button>
</div>

<div id="tab_basic" class="tab-content active">
    <h3>Basic Info</h3>
    <p><strong>Name:</strong> <?= $user->name ?></p>
    <p><strong>Date of Birth:</strong> <?= $user->dob ?></p>
    <p><strong>Interests:</strong> <?= $user->interests ?></p>
    <p><strong>Reference:</strong> <?= $user->reference ?></p>
</div>

<div id="tab_images" class="tab-content">
    <h3>User Images</h3>
    <?php if (!empty($images)) foreach ($images as $img): ?>
        <img src="<?= base_url($img->image_path) ?>" width="100" style="margin:5px;">
    <?php endforeach; ?>
</div>

<div id="tab_survey" class="tab-content">
    <h3>Survey Info</h3>
    <?php if ($survey): ?>
        <p><strong>Q1:</strong> <?= $survey->question1 ?></p>
        <p><strong>Q2:</strong> <?= $survey->question2 ?></p>
        <!-- Add more fields as needed -->
    <?php else: ?>
        <p>No survey submitted.</p>
    <?php endif; ?>
</div>