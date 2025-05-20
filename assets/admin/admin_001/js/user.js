    $(document).ready(function() {
            $('#datatablesSimple').DataTable({
                "ajax": {
                    "url": BASE_URL+'admin/user/get_users',
                    "dataSrc": "data",
                    "order": [], // no initial sort
                    "columnDefs": [
                        { "orderable": false, "targets": 0 } // 0 means first column, make it unsortable
                    ]
                }
            });
        });