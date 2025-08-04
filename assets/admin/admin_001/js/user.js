    $(document).ready(function() {
            $('#datatablesSimple').DataTable({
                "ajax": {
                    "url": BASE_URL+'admin/user/get_users',
                    "dataSrc": function(json) {
                        // Check if there's an authentication error
                        if (json.error && json.redirect) {
                            alert('Please login to continue');
                            window.location.href = json.redirect;
                            return [];
                        }
                        return json.data || [];
                    },
                    "error": function(xhr, error, thrown) {
                        console.error('DataTables error:', error);
                        console.error('Response:', xhr.responseText);
                        
                        // Try to parse response as JSON
                        try {
                            var response = JSON.parse(xhr.responseText);
                            if (response.error && response.redirect) {
                                alert('Please login to continue');
                                window.location.href = response.redirect;
                                return;
                            }
                        } catch (e) {
                            // Not JSON, show generic error
                        }
                        
                        alert('Error loading data. Please check console for details.');
                    }
                },
                "columns": [
                    { "data": 0, "title": "ID" },
                    { "data": 1, "title": "Name" },
                    { "data": 2, "title": "Email" },
                    { "data": 3, "title": "Email Verified" },
                    { "data": 4, "title": "Phone" },
                    { "data": 5, "title": "Created Date" },
                    { "data": 6, "title": "Actions" }
                ],
                "order": [], // no initial sort
                "columnDefs": [
                    { "orderable": false, "targets": 6 } // Actions column is not sortable
                ],
                "processing": true,
                "serverSide": false,
                "responsive": true
            });
        });
$(document).on('click', '.btn-delete-user', function(e) {
    e.preventDefault();
    var btn = $(this);
    var userId = btn.data('id');
    if (!confirm('Are you sure you want to delete this user and all related data?')) return;
    $.ajax({
        url: BASE_URL + 'admin/user/delete_user/' + userId,
        type: 'POST',
        dataType: 'json',
        success: function(res) {
            if (res.success) {
                // Remove row from DataTable
                var table = $('#datatablesSimple').DataTable();
                table.row(btn.parents('tr')).remove().draw();
                alert('User deleted successfully.');
            } else {
                alert('Delete failed: ' + res.message);
            }
        },
        error: function(xhr) {
            alert('Delete failed.');
        }
    });
});