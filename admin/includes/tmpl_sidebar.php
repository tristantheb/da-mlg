<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="index.php" class="brand-link">
        <img class="brand-image img-circle elevation-3 lazyload" src="./c_images/logo-gj.png" alt="AdminDA" style="opacity: .8">
        <span class="brand-text font-weight-light"><strong>Admin</strong>DA</span>
    </a>
    <!-- start:Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img class="img-circle elevation-2 lazyload" src="./c_images/user.png" alt="Profile">
            </div>
            <div class="info">
                <a href="settings.php" class="d-block"><?php echo $data['full_name']; ?></a>
            </div>
        </div>
        <!-- start:Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column nav-flat" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Global navigation -->
                <li class="nav-item">
                    <a href="index.php" class="nav-link active">
                        <i class="fad fa-chart-network nav-icon"></i>
                        <p>
                            Tableau de bord
                        </p>
                    </a>
                </li>
                <?php if (isAdmin()) { ?>
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="fad fa-location-arrow nav-icon"></i>
                        <p>
                            Administration
                            <i class="fas fa-angle-left right"></i>
                            <span class="badge badge-info right">2</span>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="advisors.php" class="nav-link">
                                <i class="fad fa-circle nav-icon"></i>
                                <p>Gérer les membres</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="groups.php" class="nav-link">
                                <i class="fad fa-circle nav-icon"></i>
                                <p>Gérer les jeunes</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <?php } ?>
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="fad fa-location-arrow nav-icon"></i>
                        <p>
                            Les déclarations
                            <i class="fas fa-angle-left right"></i>
                            <span class="badge badge-info right">5</span>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="activity_check.php" class="nav-link">
                                <i class="fad fa-list-alt nav-icon"></i>
                                <p>Déclarations d'activité</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="activity_month.php" class="nav-link">
                                <i class="fad fa-tasks nav-icon"></i>
                                <p>Déclarations du mois</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="code_gen.php" class="nav-link">
                                <i class="fad fa-link nav-icon"></i>
                                <p>
                                    Générer un lien
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="../index.php?key=G4R4Nt1eD4o19-xJEUNESx-G4R4Nt1eD4o19" target="_blank" class="nav-link">
                                <i class="fad fa-external-link-alt nav-icon"></i>
                                <p>
                                    Faire une DA jeune <small>1<sup>er</sup> au 15</small>
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="activity_send.php" target="_blank" class="nav-link">
                                <i class="fad fa-external-link-alt nav-icon"></i>
                                <p>
                                    Faire une DA admin <small>16 au 31</small>
                                </p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="fad fa-location-arrow nav-icon"></i>
                        <p>
                            La zone rouge
                            <i class="fas fa-angle-left right"></i>
                            <span class="badge badge-info right">2</span>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="red_list.php" class="nav-link">
                                <i class="fad fa-user-times nav-icon"></i>
                                <p>Liste rouge</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="notifs.php" class="nav-link">
                                <i class="fad fa-bell-exclamation nav-icon"></i>
                                <p>
                                    Notifications
                                    <?php echo $notifs; ?>
                                </p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link" onclick="pushTest();">
                        <i class="fad fa-hand-pointer nav-icon"></i>
                        <p>
                            Activer notification
                        </p>
                    </a>
                </li>
                <li class="nav-header">
                    SUPPORT
                </li>
                <li class="nav-item">
                    <a href="guide.php" class="nav-link">
                        <i class="fad fa-life-ring nav-icon text-blue"></i>
                        <p>
                            Guide d'utilisation
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="versions.php" class="nav-link">
                        <i class="fad fa-code-branch nav-icon text-green"></i>
                        <p>
                            Notes de version
                        </p>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- end:Sidebar Menu -->
    </div>
    <!-- end:Sidebar -->
</aside>
<script>
    function pushTest() {
        Swal.fire({
            position: 'top-end',
            title: 'Cette notification s\'affiche ?',
            text: 'Si vous lisez ce message, c\'est que vos scripts fonctionnent correctement sur ce navigateur.',
            icon: 'question',
            timer: 5000,
            allowOutsideClick: false,
            allowEscapeKey: false,
            allowEnterKey: false
        });
    }
</script>