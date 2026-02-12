<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-laugh-wink"></i>
        </div>
        <div class="sidebar-brand-text mx-3 tek">SB Admin <sup>2</sup></div>
    </a>

    <hr class="sidebar-divider my-0">

    <!-- Dashboard -->
    <!-- <li class="nav-item">
        <a class="nav-link" href="../../tambahStok/dashboard.php">
            <img src="https://cdn-icons-png.flaticon.com/128/8323/8323511.png"
                 class="icon d-none" height="24">
            <span class="tek">Dashboard</span>
        </a>
    </li> -->

    <hr class="sidebar-divider">

    <!-- Dynamic Modules -->
    <?php $menuConfig = loadMenuConfig(); ?>
    <?php foreach ($menuConfig['modules'] as $module => $config): ?>
        <?php if (userCanAccess($config['allowed_roles'])): ?>
            <li class="nav-item">
                <a class="nav-link" href="../<?= $module ?>/index.php">
                    <img src="https://cdn-icons-png.flaticon.com/128/4763/4763161.png"
                         class="icon d-none" height="24">
                    <span class="tek"><?= $config['label'] ?></span>
                </a>
            </li>
        <?php endif; ?>
    <?php endforeach; ?>

    <!-- Logout -->
    <li class="nav-item">
        <a class="nav-link" href="../logout.php">
            <img src="https://cdn-icons-png.flaticon.com/128/16385/16385164.png"
                 class="icon d-none" height="24">
            <span class="tek">Logout</span>
        </a>
    </li>

    <hr class="sidebar-divider d-none d-md-block">

    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
