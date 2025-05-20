<div class="container">
    <h2>Add Survey Question</h2>
    <form method="post" action="<?= $this->admin_url ?>/servey/add_question">
        <div class="form-group">
            <label>Question</label>
            <textarea name="question" class="form-control" required></textarea>
        </div>

        <label>Options</label>
        <div id="options-wrapper">
            <div class="form-group option-field">
                <input type="text" name="options[]" class="form-control" placeholder="Option 1" required>
            </div>
        </div>

        <button type="button" class="btn btn-default" id="add-option">Add More Option</button>
        <br><br>
        <button type="submit" class="btn btn-primary">Save Question</button>
    </form>
</div>