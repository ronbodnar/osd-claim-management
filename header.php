<?php

#ini_set('display_errors', 0);
#ini_set('display_startup_errors', 0);
#error_reporting(E_ALL & ~E_NOTICE);

ini_set('session.cookie_lifetime', 120 * 60);
ini_set('session.gc_maxlifetime', 120 * 60); // expires in 120 minutes

// each client should remember their session id for EXACTLY 120 minutes
session_set_cookie_params(120 * 60);

session_start();

/*
 * Sessions should expire after 120 minutes
 */
if (!isset($_SESSION['CREATED'])) {
    $_SESSION['CREATED'] = time();
} else if (time() - $_SESSION['CREATED'] > 7200) {
    session_unset();
    session_destroy();
}

require 'functions.php';
require 'Database.class.php';

$database = new Database();

$formattedPageNames = array(
    'osd-claims' => 'Dashboard',
    'submit-report' => 'Submit Report'
);

$directoryName = end(explode('/', basename(getcwd())));

$pageTitle = $formattedPageNames[$directoryName];

?>

<!doctype html>
<html lang="en" class="h-100">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="OS&D Claims Management supports drivers reporting OS&D claims, an administrative claim dashboard, and internal process management and claim status information.">
    <meta name="author" content="Ron Bodnar">

    <title><?php echo $pageTitle; ?> | OS&D Claims</title>

    <link rel="canonical" href="https://ronbodnar.com/projects/osd-claims/">

    <script>
        // Render blocking
        if (localStorage.theme) document.documentElement.setAttribute("data-theme", localStorage.theme);
    </script>

    <link href="<?php echo getRelativePath(); ?>assets/css/bootstrap.min.css?v=<?php echo filemtime(getRelativePath() . 'assets/css/bootstrap.min.css'); ?>" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.1/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link href="<?php echo getRelativePath(); ?>assets/css/style.css?v=<?php echo filemtime(getRelativePath() . 'assets/css/style.css'); ?>" rel="stylesheet">

    <link rel="icon" href="/assets/img/favicon.png?v=<?php echo filemtime('/assets/img/favicon.png'); ?>">

    <meta name="theme-color" content="#7952b3">
</head>

<body class="body" id="body-pd">
    <?php if (isLoggedIn()) { ?>
        <!-- Top Header -->
        <header class="header top-bar" id="header">
            <div class="header-toggle">
               <!--<i class="bx bx-menu" id="header-toggle"></i>-->
               <i class="bi bi-house px-4" id="back"></i>
            </div>

            <div class="d-flex justify-content-center align-items-center">
                <div class="dropdown">
                    <a href="#" class="text-mron d-flex align-items-center text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="https://ronbodnar.com/assets/img/favicon-32x32.png" alt="MR" width="32" height="32" class="rounded-circle">
                        <span class="d-none d-sm-inline mx-1"><?php echo 'Demo Account';//$user->getFullName(); ?></span>
                    </a>
                    <ul class="dropdown-menu text-small shadow" aria-labelledby="dropdownUser1">
                        <li>
                            <a class="dropdown-item d-flex justify-content-between">
                                <label class="form-check-label d-inline-block" for="darkModeSwitch">Dark Mode</label>
                                <div class="form-check form-switch d-inline-block">
                                    <input class="form-check-input" type="checkbox" id="darkModeSwitch" onclick="toggleDarkMode()">
                                </div>
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="<?php echo getRelativePath(); ?>src/login.php" id="signOut">Sign out</a></li>
                    </ul>
                </div>
                
            </div>
        </header>

        <!-- Sidebar -->
        <div class="l-navbar" id="nav-bar">
            <div class="header-toggle-mobile">
                <i class="bx bx-x" id="header-toggle-mobile"></i>
            </div>
            <nav class="sidebar">
                <div>
                    <div class="nav_logo">
                        <a href="https://ronbodnar.com/projects/logistics-management/">
                            <img src="https://ronbodnar.com/assets/img/header-lg.png" id="nav-header" alt="MRon Development" />
                        </a>
                    </div>

                    <div class="nav_list">
                        <a href="/projects/osd-claims/" class="nav_link<?php echo ($directoryName === 'osd-claims' ? ' active' : '') ?>">
                            <i class="bi bi-grid"></i>
                            <span class="nav_name">Dashboard</span>
                        </a>

                        <span class="separator">Drivers View</span>

                        <a href="<?php echo getRelativePath(); ?>submit-report/" class="nav_link<?php echo ($directoryName === 'submit-report' ? ' active' : '') ?>">
                            <i class="bi bi-boxes"></i>
                            <span class="nav_name">Submit Report</span>
                        </a>
                    </div>
                </div>
            </nav>
        </div>

    <?php } ?>