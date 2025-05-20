    $(document).ready(function() {
            $('#datatablesSimple').DataTable({
                "ajax": {
                    "url": BASE_URL+DTABLE,
                    "dataSrc": "data",
                    "order": [], // no initial sort
                    "columnDefs": [
                        { "orderable": false, "targets": 0 } // 0 means first column, make it unsortable
                    ]
                }
            });
        });