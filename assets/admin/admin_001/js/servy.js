$(document).ready(function() {
    let optionCount = 1;

    $('#add-option').click(function() {
        optionCount++;
        $('#options-wrapper').append(
            `<div class="form-group option-field">
                <input type="text" name="options[]" class="form-control" placeholder="Option ${optionCount}" required>
                <span class="remove-option">Remove</span>
            </div>`
        );
    });

    $(document).on('click', '.remove-option', function() {
        $(this).closest('.option-field').remove();
    });
});

    $(document).ready(function() {
            $('#datatablesSimple').DataTable({
                "ajax": {
                    "url": BASE_URL+'admin/servey/get_question',
                    "dataSrc": "data",
                    "order": [], // no initial sort
                    "columnDefs": [
                        { "orderable": false, "targets": 0 } // 0 means first column, make it unsortable
                    ]
                }
            });
        });