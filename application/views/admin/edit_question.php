
<div class="container">
    <h2>Edit Survey Question</h2>
    <form method="post">
        <div class="form-group" >
            <label>Question</label>
            <textarea name="question" class="form-control" required><?= htmlspecialchars($question['question']) ?></textarea>
        </div>

        <label>Options</label>
        <div id="options-wrapper">
            <?php if (!empty($question['options'])):
                foreach ($question['options'] as $index => $opt): ?>
                <div class="form-group option-field">
                    <input type="text" name="options[]" class="form-control" value="<?= htmlspecialchars($opt['option_text']) ?>" required>
                    <span class="remove-option">Remove</span>
                </div>
            <?php endforeach; endif; ?>
        </div>

        <button type="button" class="btn btn-default" id="add-option">Add More Option</button>
        <br><br>
        <button type="submit" class="btn btn-primary">Update Question</button>
    </form>
</div>