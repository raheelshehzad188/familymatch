<nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                    <div class="sb-sidenav-menu">
                        <div class="nav">
                            <div class="sb-sidenav-menu-heading">Core</div>
                            <a class="nav-link" href="<?= $this->admin_url; ?>">
                                <div class="sb-nav-link-icon"><i class="fas fa-dtachometer-alt"></i></div>
                                Dashboard
                            </a>
                            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#user_attribute" aria-expanded="false" aria-controls="user_attribute">
                                <div class="sb-nav-link-icon"><i class="fas fa-columns"></i></div>
                                User Attributes 
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <?php
                            $arr = array('religions','blood_groups','marital_status');

                            ?>
                            <div class="collapse <?= (isset($controller) && $controller == 'crud' && isset($param1) && in_array($param1,$arr))?'show':'' ?>" id="user_attribute" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link <?= ($param1 == 'religions')?'active':'' ?>" href="<?= base_url('admin/crud/all/religions'); ?>">Religion</a>
                                    <a class="nav-link <?= ($param1 == 'blood_groups')?'active':'' ?>" href="<?= base_url('admin/crud/all/blood_groups') ?>">Blood group</a>
                                    <a class="nav-link <?= ($param1 == 'marital_status')?'active':'' ?>" href="<?= base_url('admin/crud/all/marital_status') ?>">Marital status</a>
                                </nav>
                            </div>
                      
                            
                            

                            <div class="sb-sidenav-menu-heading">Interface</div>
                            
                               <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#Manage_user" aria-expanded="false" aria-controls="Manage_user">
                                <div class="sb-nav-link-icon"><i class="fas fa-columns"></i></div>
                                User Manage
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse " id="Manage_user" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link " href="#">Achived user</a>
                                    <a class="nav-link " href="#">Baned user</a>
                                    <a class="nav-link " href="#">KYC pending</a>
                                    <a class="nav-link " href="#">Email unvarified</a>
                                    <a class="nav-link " href="#">Mobile unvarified</a>
                                    <a class="nav-link " href="#">All Users</a>
                                </nav>

                            </div>


                             <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#User_interacion" aria-expanded="false" aria-controls="User_interacion">
                                <div class="sb-nav-link-icon"><i class="fas fa-columns"></i></div>
                                User interacion
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse " id="User_interacion" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link " href="#">Interests</a>
                                    <a class="nav-link " href="#">Ignored profile</a>
                                    <a class="nav-link " href="#">Reports</a>
                                </nav>
                            </div> 

                            <!-- Trigger -->
                   <!--  <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#User_interacion" aria-expanded="false">
                        User Interaction
                    </a> -->

                    <!-- Collapsible content -->
                  <!--   <div class="collapse" id="User_interacion" data-bs-parent="#sidenavAccordion">
                        <nav class="sb-sidenav-menu-nested nav">
                            <a class="nav-link" href="#">Interests</a>
                            <a class="nav-link" href="#">Ignored profile</a>
                            <a class="nav-link" href="#">Reports</a>
                        </nav>
                    </div> -->


                            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseinterest" aria-expanded="false" aria-controls="collapseinterest">
                                <div class="sb-nav-link-icon"><i class="fas fa-columns"></i></div>
                                Interestes
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse <?= (isset($controller) && $controller == 'interest')?'show':'' ?>" id="collapseinterest" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link <?= ( $controller == 'interest' && isset($method) && $method == 'index')?'active':'' ?>" href="<?= $this->admin_url.'interest'; ?>">All Interests</a>
                                    <a class="nav-link <?= ( $controller == 'interest' && isset($method) && $method == 'add')?'active':'' ?>" href="<?= $this->admin_url.'interest/add'; ?>">Add Interest</a>
                                </nav>
                            </div>
                            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseETH" aria-expanded="false" aria-controls="collapseETH">
                                <div class="sb-nav-link-icon"><i class="fas fa-columns"></i></div>
                                Ethnicities
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse <?= (isset($controller) && $controller == 'ethnicity')?'show':'' ?>" id="collapseETH" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link <?= ( $controller == 'ethnicity' && isset($method) && $method == 'index')?'active':'' ?>" href="<?= $this->admin_url.'ethnicity'; ?>">All Ethnicities</a>
                                    <a class="nav-link <?= ( $controller == 'ethnicity' && isset($method) && $method == 'add')?'active':'' ?>" href="<?= $this->admin_url.'ethnicity/add'; ?>">Add Ethnicity</a>
                                </nav>
                            </div>
                            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseSur" aria-expanded="false" aria-controls="collapseSur">
                                <div class="sb-nav-link-icon"><i class="fas fa-columns"></i></div>
                                Servey
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse <?= (isset($controller) && $controller == 'servey')?'show':'' ?>" id="collapseSur" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link <?= ($controller == 'servey' && isset($method) && $method == 'index')?'active':'' ?>" href="<?= $this->admin_url.'servey'; ?>">All Questions</a>
                                    <a class="nav-link <?= ($controller == 'servey' && isset($method) && $method == 'add_question')?'active':'' ?>" href="<?= $this->admin_url.'servey/add_question'; ?>">Add Question</a>
                                    <a class="nav-link <?= ($controller == 'servey' && isset($method) && $method == 'results')?'active':'' ?>" href="<?= $this->admin_url.'servey/results'; ?>">Results</a>
                                </nav>
                            </div>
                            <a class="nav-link <?= (isset($controller) && $controller == 'user')?'active':'' ?>" href="<?= $this->admin_url.'user'; ?>">All Users</a>
                            <a class="nav-link <?= (isset($controller) && $controller == 'admin_keys')?'active':'' ?>" href="<?= $this->admin_url.'admin_keys'; ?>">API Keys</a>
                            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseLayouts" aria-expanded="false" aria-controls="collapseLayouts">
                                <div class="sb-nav-link-icon"><i class="fas fa-columns"></i></div>
                                Layouts
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapseLayouts" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="layout-static.html">Static Navigation</a>
                                    <a class="nav-link" href="layout-sidenav-light.html">Light Sidenav</a>
                                </nav>
                            </div>
                            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapsePages" aria-expanded="false" aria-controls="collapsePages">
                                <div class="sb-nav-link-icon"><i class="fas fa-book-open"></i></div>
                                Pages
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapsePages" aria-labelledby="headingTwo" data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav accordion" id="sidenavAccordionPages">
                                    <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#pagesCollapseAuth" aria-expanded="false" aria-controls="pagesCollapseAuth">
                                        Authentication
                                        <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                                    </a>
                                    <div class="collapse" id="pagesCollapseAuth" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordionPages">
                                        <nav class="sb-sidenav-menu-nested nav">
                                            <a class="nav-link" href="login.html">Login</a>
                                            <a class="nav-link" href="register.html">Register</a>
                                            <a class="nav-link" href="password.html">Forgot Password</a>
                                        </nav>
                                    </div>
                                    <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#pagesCollapseError" aria-expanded="false" aria-controls="pagesCollapseError">
                                        Error
                                        <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                                    </a>
                                    <div class="collapse" id="pagesCollapseError" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordionPages">
                                        <nav class="sb-sidenav-menu-nested nav">
                                            <a class="nav-link" href="401.html">401 Page</a>
                                            <a class="nav-link" href="404.html">404 Page</a>
                                            <a class="nav-link" href="500.html">500 Page</a>
                                        </nav>
                                    </div>
                                </nav>
                            </div>
                            <div class="sb-sidenav-menu-heading">Addons</div>
                            <a class="nav-link" href="charts.html">
                                <div class="sb-nav-link-icon"><i class="fas fa-chart-area"></i></div>
                                Charts
                            </a>
                            <a class="nav-link" href="tables.html">
                                <div class="sb-nav-link-icon"><i class="fas fa-table"></i></div>
                                Tables
                            </a>
                        </div>
                    </div>
                    <div class="sb-sidenav-footer">
                        <div class="small">Logged in as:</div>
                        <?= $admin->name ?>
                    </div>

                    
                </nav>