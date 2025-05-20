<nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                    <div class="sb-sidenav-menu">
                        <div class="nav">
                            <div class="sb-sidenav-menu-heading">Core</div>
                            <a class="nav-link" href="<?= $this->admin_url; ?>">
                                <div class="sb-nav-link-icon"><i class="fas fa-dtachometer-alt"></i></div>
                                Dashboard
                            </a>
                            <div class="sb-sidenav-menu-heading">Interface</div>
                            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapsebody" aria-expanded="false" aria-controls="collapsebody">
                                <div class="sb-nav-link-icon"><i class="fas fa-columns"></i></div>
                                Genders
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse <?= (isset($controller) && $controller == 'gender')?'show':'' ?>" id="collapsebody" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link <?= ($controller == 'gender' && isset($method) && $method == 'index')?'active':'' ?>" href="<?= $this->admin_url.'gender'; ?>">All Genders</a>
                                    <a class="nav-link <?= (isset($method) && $method == 'add')?'active':'' ?>" href="<?= $this->admin_url.'gender/add'; ?>">Add Gender</a>
                                </nav>
                            </div>
                            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapsebody" aria-expanded="false" aria-controls="collapsebody">
                                <div class="sb-nav-link-icon"><i class="fas fa-columns"></i></div>
                                Body Types
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse <?= (isset($controller) && $controller == 'body_type')?'show':'' ?>" id="collapsebody" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link <?= ( $controller == 'body_type' && isset($method) && $method == 'index')?'active':'' ?>" href="<?= $this->admin_url.'body_type'; ?>">All Body Types</a>
                                    <a class="nav-link <?= ( $controller == 'body_type' && isset($method) && $method == 'add')?'active':'' ?>" href="<?= $this->admin_url.'body_type/add'; ?>">Add Type</a>
                                </nav>
                            </div>
                            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseinterest" aria-expanded="false" aria-controls="collapseinterest">
                                <div class="sb-nav-link-icon"><i class="fas fa-columns"></i></div>
                                Referral
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse <?= (isset($controller) && $controller == 'referral')?'show':'' ?>" id="collapseinterest" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link <?= ( $controller == 'referral' && isset($method) && $method == 'index')?'active':'' ?>" href="<?= $this->admin_url.'referral'; ?>">All Referrals</a>
                                    <a class="nav-link <?= ( $controller == 'referral' && isset($method) && $method == 'add')?'active':'' ?>" href="<?= $this->admin_url.'referral/add'; ?>">Add Referral</a>
                                </nav>
                            </div>
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