<main>
                    <div class="container-fluid px-4">
                        <h1 class="mt-4"><?= $heading ?></h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item"><a href="<?= $this->admin_url; ?>">Dashboard</a></li>
                            <li class="breadcrumb-item active"><?= $heading ?></li>
                        </ol>



 
                     <div class="col-md-12  text-end">
                         <a href="<?= base_url($add_link); ?>" class="btn btn-primary">Add New</a>
                     </div>



                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-table me-1"></i>
                            </div>
                            <div class="card-body">
                                <table id="datatablesSimple">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <?php
                                            foreach($fields as $k=> $v)
                                            {
                                                if($v['is_list'])
                                                {
                                                    ?>
                                                    <th><?= $v['label'] ?></th>

                                                    <?php

                                                }
                                            }

                                            ?>
								            <th>Created</th>
								            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>ID</th>
								            <?php
                                            foreach($fields as $k=> $v)
                                            {
                                                if($v['is_list'])
                                                {
                                                    ?>
                                                    <th><?= $v['label'] ?></th>

                                                    <?php

                                                }
                                            }

                                            ?>
								            <th>Created</th>
								            <th>Action</th>
                                        </tr>
                                    </tfoot>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </main>