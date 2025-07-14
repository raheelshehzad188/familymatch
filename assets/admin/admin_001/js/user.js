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
                    { "data": 3, "title": "Phone" },
                    { "data": 4, "title": "Created Date" },
                    { "data": 5, "title": "Actions" }
                ],
                "order": [], // no initial sort
                "columnDefs": [
                    { "orderable": false, "targets": 5 } // Actions column is not sortable
                ],
                "processing": true,
                "serverSide": false,
                "responsive": true
            });
        });