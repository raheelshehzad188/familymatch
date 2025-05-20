
                    <div class="container-fluid px-4">
                        <h1 class="mt-4"><?= $heading = 'Results'; ?></h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item"><a href="<?= $this->admin_url; ?>">Dashboard</a></li>
                            <li class="breadcrumb-item active"><?= $heading ?></li>
                        </ol>
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-table me-1"></i>
                            </div>
                            <div class="card-body">
                                <table id="datatablesSimple">
                                    <thead>
                                        <tr>
                                            <th>User</th>
                                            <th>Question</th>
                                            <th>Answer</th>
                                            <th>Submitted At</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>User</th>
                                            <th>Question</th>
                                            <th>Answer</th>
                                            <th>Submitted At</th>
                                        </tr>
                                    </tfoot>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </main>