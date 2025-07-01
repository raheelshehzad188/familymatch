<main>
    <div class="container-fluid px-4 py-4 bg-light rounded shadow-sm">
        <!-- Dynamic heading -->
        <div class="new d-flex justify-content-between align-items-center mb-4">
            <h1 class="display-4 text-primary">All <?= $heading ?></h1>
            <!-- Wrap label and button -->
            <div class="d-flex align-items-center mb-4">
                <span class="religion-label me-2">Religion</span>
                <a href="<?= base_url($add_link); ?>" class="new_1">
                    <i class="fas fa-plus me-2"></i> Add New
                </a>
            </div>
        </div>
        
     

        <!-- Card for data table -->
        <div class="card border-0 shadow-sm rounded">
          
            <div class="card-body p-4">
                <div class="table-responsive rounded">
                    <table id="datatablesSimple" class="table table-striped table-hover align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <?php
                                foreach ($fields as $field) {
                                    if ($field['is_list']) {
                                        echo "<th>{$field['label']}</th>";
                                    }
                                }
                                ?>
                                <th>Created</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        
                        <tbody>
                            <?php
                            foreach ($data as $row) {
                                echo "<tr class='transition-hover'>";
                                echo "<td class='fw-bold text-primary'>{$row['id']}</td>";
                                foreach ($fields as $field) {
                                    if ($field['is_list']) {
                                        $field_key = strtolower(str_replace(' ', '_', $field['label']));
                                        echo "<td>{$row[$field_key]}</td>";
                                    }
                                }
                                echo "<td>" . date('d M, Y', strtotime($row['created'])) . "</td>";
                                // Action buttons with colors
                                echo "<td class='text-center'>
                                        <!-- View Button -->
                                        <a href='" . base_url("view/{$row['id']}") . "' class='btn btn-sm btn-outline-primary me-2' title='View'>
                                            <i class='fas fa-eye'></i>
                                        </a>
                                        <!-- Edit Button (Color Blue) -->
                                        <a href='" . base_url("edit/{$row['id']}") . "' 
                                           class='btn btn-sm btn-outline-info me-2' 
                                           title='Edit' 
                                           style='border-color:#0d6efd; color:#0d6efd;'>
                                            <i class='fas fa-edit'></i>
                                        </a>
                                        <!-- Delete Button (Color Red) -->
                                        <a href='" . base_url("delete/{$row['id']}") . "' 
                                           class='btn btn-sm btn-outline-danger' 
                                           onclick='return confirm(\"Are you sure?\")' 
                                           title='Delete' 
                                           style='border-color:#dc3545; color:#dc3545;'>
                                            <i class='fas fa-trash-alt'></i>
                                        </a>
                                      </td>";
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- DataTables CDN links (uncomment if needed) -->
<!--
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" />
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
-->

<script>
$(document).ready(function() {
    $('#datatablesSimple').DataTable({
        "paging": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "responsive": true,
        initComplete: function() {
            // Placeholder text
            $('#datatablesSimple_filter input').attr('placeholder', 'Search');
            // Broader width
            $('#datatablesSimple_filter input').css({
                'width': '350px',
                'transition': 'box-shadow 0.3s ease'
            });
            // Hover effect with blue shadow
            $('#datatablesSimple_filter input').hover(
                function() {
                    $(this).css('box-shadow', '0 0 12px 3px rgba(0, 123, 255, 0.7)');
                },
                function() {
                    $(this).css('box-shadow', 'none');
                }
            );
        }
    });
});
</script>

<style>
/* Hover effect on table rows */
.transition-hover:hover {
    background-color: #f8f9fa;
    transition: background-color 0.3s ease;
}

/* Smaller label styling */
.religion-label {
    font-size: 0.9rem;
    font-weight: 600;
    color: #555;
}

/* Small button style */
.small-btn {
    font-size: 0.85rem;
    padding: 0.25rem 0.6rem;
}
.new h1{
    font-size: 27px;
    color: black!important;
    font-weight: 400;
}
</style>