<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#"><i class="fad fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="index.php" class="nav-link">Accueil</a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="settings.php" class="nav-link">Paramètres</a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="logout.php" class="nav-link">Déconnexion</a>
        </li>
    </ul>
    <!-- SEARCH FORM -->
    <form action="search.php" method="GET" class="form-inline ml-3">
        <div class="input-group input-group-sm">
            <input class="form-control form-control-navbar" name="search" type="search" placeholder="Rechercher" aria-label="Rechercher">
            <input class="form-control form-control-navbar hidden" name="type" type="text" value="search" aria-hidden="true" hidden>
           <div class="input-group-append">
                <button class="btn btn-navbar" type="submit"><i class="fad fa-search"></i></button>
            </div>
        </div>
    </form>
    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <!-- Messages Dropdown Menu -->
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="fad fa-comments"></i>
                <?php if(count_stats('da_mailbox', 'sent_to = '.$_COOKIE['advisor'].' AND read_date IS NULL') == 0) { ?>
                <span class="badge badge-secondary navbar-badge">0</span>
                <?php } else { ?>
                <span class="badge badge-danger navbar-badge"><?php echo count_stats('da_mailbox', 'sent_to = '.$_COOKIE['advisor'].' AND read_date IS NULL'); ?></span>
                <?php } ?>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <?php
                $query = $db->prepare('SELECT * FROM da_mailbox WHERE sent_to = :id ORDER BY send_date DESC');
                $query->execute(array(
                    'id' => $_COOKIE['advisor']
                ));
                while($dmbox = $query->fetch()) {
                ?>
                <a href="inbox.php?view=read&id=<?php echo $dmbox['id']; ?>" class="dropdown-item">
                    <!-- Message Start -->
                    <div class="media">
                        <img src="./c_images/user.png" alt="Avatar" class="img-size-50 mr-3 img-circle">
                        <div class="media-body">
                            <h3 class="dropdown-item-title">
                                <?php
                                $query_name = $db->prepare("SELECT advisor_id, full_name FROM advisors WHERE advisor_id = :sent_by");
                                $query_name->execute(array(
                                    'sent_by' => $dmbox['sent_by']
                                ));
                                $name = $query_name->fetch();
                                echo $name['full_name'];
                                $query_name->closeCursor();
                                // Message type icon
                                if ($dmbox['type'] == 'information') {
                                    echo '<span class="float-right text-sm text-info"><i class="fad fa-info-circle"></i></span>';
                                } elseif ($dmbox['type'] == 'important') {
                                    echo '<span class="float-right text-sm text-danger"><i class="fad fa-exclamation"></i></span>';
                                } elseif ($dmbox['type'] == 'urgent') {
                                    echo '<span class="float-right text-sm text-danger"><i class="fad fa-exclamation-circle"></i></span>';
                                }
                                ?>
                            </h3>
                            <p class="text-sm"><?php echo $dmbox['title']; ?></p>
                            <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> <?php echo count_duration($dmbox['send_date']); ?></p>
                        </div>
                    </div>
                    <!-- Message End -->
                </a>
                <div class="dropdown-divider"></div>
                <?php } $query->closeCursor(); ?>
                <a href="inbox.php" class="dropdown-item dropdown-footer">Tous les messages</a>
            </div>
        </li>
    </ul>
</nav>