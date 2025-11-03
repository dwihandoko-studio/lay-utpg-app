<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title><?= isset($title) ? $title : "Portal Layanan" ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Portal Layanan Resmi Dinas Pendidikan dan Kebudayaan Kab. Lampung Tengah" name="description" />
    <meta content="handokowae.my.id" name="author" />
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="keywords" content="portal, layanan, portal layanan, portal layanan disdikbud, disdikbud, disdik, pendidikan, lampung, lampung tengah, pendidikan lampung tengah, dinas pendidikan, dinas pendidikan lampung tengah, dinas pendidikan dan kebudayaan, dinas pendidikan dan kebudayaan lampung tengah, kabupaten lampung tengah">

    <meta property="og:title" content="Portal Layanan Resmi Dinas Pendidikan dan Kebudayaan Kab. Lampung Tengah" />
    <meta property="og:url" content="<?= base_url() ?>" />
    <meta property="og:image" content="<?= base_url('favicon/android-icon-192x192.png'); ?>" />
    <meta property="og:description" content="Portal Layanan Resmi Dinas Pendidikan dan Kebudayaan Kab. Lampung Tengah" />

    <meta itemprop="name" content="Portal Layanan Resmi Dinas Pendidikan dan Kebudayaan Kab. Lampung Tengah" />
    <meta itemprop="description" content="Portal Layanan Resmi Dinas Pendidikan dan Kebudayaan Kab. Lampung Tengah" />
    <meta itemprop="image" content="<?= base_url('favicon/android-icon-192x192.png'); ?>" />

    <link rel="apple-touch-icon" sizes="57x57" href="<?= base_url('favicon/apple-icon-57x57.png'); ?>">
    <link rel="apple-touch-icon" sizes="60x60" href="<?= base_url('favicon/apple-icon-60x60.png'); ?>">
    <link rel="apple-touch-icon" sizes="72x72" href="<?= base_url('favicon/apple-icon-72x72.png'); ?>">
    <link rel="apple-touch-icon" sizes="76x76" href="<?= base_url('favicon/apple-icon-76x76.png'); ?>">
    <link rel="apple-touch-icon" sizes="114x114" href="<?= base_url('favicon/apple-icon-114x114.png'); ?>">
    <link rel="apple-touch-icon" sizes="120x120" href="<?= base_url('favicon/apple-icon-120x120.png'); ?>">
    <link rel="apple-touch-icon" sizes="144x144" href="<?= base_url('favicon/apple-icon-144x144.png'); ?>">
    <link rel="apple-touch-icon" sizes="152x152" href="<?= base_url('favicon/apple-icon-152x152.png'); ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= base_url('favicon/apple-icon-180x180.png'); ?>">
    <link rel="icon" type="image/png" sizes="192x192" href="<?= base_url('favicon/android-icon-192x192.png'); ?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= base_url('favicon/favicon-32x32.png'); ?>">
    <link rel="icon" type="image/png" sizes="96x96" href="<?= base_url('favicon/favicon-96x96.png'); ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= base_url('favicon/favicon-16x16.png'); ?>">
    <link rel="manifest" href="<?= base_url('favicon/manifest.json'); ?>">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="<?= base_url('favicon/ms-icon-144x144.png'); ?>">
    <meta name="theme-color" content="#ffffff">
    <style>
        .active-menu-href {
            color: #556ee6 !important;
        }
    </style>

    <link href="<?= base_url() ?>/assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <link href="<?= base_url() ?>/assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="<?= base_url() ?>/assets/css/app.min.css" id="app-style" rel="stylesheet" type="text/css" />
    <link href="<?= base_url() ?>/assets/libs/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css" />
    <script>
        if (sessionStorage.getItem("is_visited") === null) {
            sessionStorage.setItem("is_visited", "dark-mode-switch");
        }

        // sessionStorage.setItem("is_visited", "dark-mode-switch");
        const BASE_URL = '<?= base_url() ?>';
    </script>
    <style>
        ._sorot-mouse:hover {
            background-color: #c3cbe4;
        }

        ._sorot-mouse:hover ._color-h-hover {
            color: #000 !important;
        }

        ._sorot-mouse:hover ._color-p-hover {
            color: #202022 !important;
        }
    </style>
</head>

<body data-topbar="dark" data-layout="horizontal" data-layout-size="boxed" class="loading-logout">

    <!-- Begin page -->
    <div id="layout-wrapper">

        <?php $uri = current_url(true); ?>
        <header id="page-topbar">
            <div class="navbar-header">
                <div class="d-flex">
                    <!-- LOGO -->
                    <div class="navbar-brand-box">
                        <a href="javascript:;" class="logo logo-dark">
                            <span class="logo-sm">
                                <img src="<?= base_url() ?>/assets/images/logo.svg" alt="" height="22">
                            </span>
                            <span class="logo-lg">
                                <img src="<?= base_url() ?>/assets/images/logo-dark.png" alt="" height="17">
                            </span>
                        </a>

                        <a href="javascript:;" class="logo logo-light">
                            <span class="logo-sm">
                                <img src="<?= base_url() ?>/assets/images/logo-light.svg" alt="" height="22">
                            </span>
                            <span class="logo-lg">
                                <img src="<?= base_url() ?>/assets/images/logo-light-old.png" alt="" height="19">
                            </span>
                        </a>
                    </div>

                    <button type="button" class="btn btn-sm px-3 font-size-16 d-lg-none header-item waves-effect waves-light" data-bs-toggle="collapse" data-bs-target="#topnav-menu-content">
                        <i class="fa fa-fw fa-bars"></i>
                    </button>

                    <!-- App Search-->
                    <form class="app-search d-none d-lg-block">
                        <div class="position-relative">
                            <input type="text" class="form-control" placeholder="Search..." style="background-color: #ffffff1a;">
                            <span class="bx bx-search-alt"></span>
                        </div>
                    </form>
                </div>

                <div class="d-flex">

                    <div class="dropdown d-inline-block d-lg-none ms-2">
                        <button type="button" class="btn header-item noti-icon waves-effect" id="page-header-search-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="mdi mdi-magnify"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0" aria-labelledby="page-header-search-dropdown">

                            <form class="p-3">
                                <div class="form-group m-0">
                                    <div class="input-group">
                                        <input type="text" class="form-control" placeholder="Search ..." aria-label="Search input">

                                        <button class="btn btn-primary" type="submit"><i class="mdi mdi-magnify"></i></button>s
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="dropdown d-inline-block">
                        <button type="button" class="btn header-item noti-icon waves-effect" id="page-header-notifications-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="bx bx-bell bx-tada"></i>
                            <span class="badge bg-danger rounded-pill">3</span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0" aria-labelledby="page-header-notifications-dropdown">
                            <div class="p-3">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h6 class="m-0" key="t-notifications"> Notifications </h6>
                                    </div>
                                    <div class="col-auto">
                                        <a href="#!" class="small" key="t-view-all"> View All</a>
                                    </div>
                                </div>
                            </div>
                            <div data-simplebar style="max-height: 230px;">
                                <a href="javascript: void(0);" class="text-reset notification-item">
                                    <div class="d-flex">
                                        <div class="avatar-xs me-3">
                                            <span class="avatar-title bg-primary rounded-circle font-size-16">
                                                <i class="bx bx-cart"></i>
                                            </span>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1" key="t-your-order">Your order is placed</h6>
                                            <div class="font-size-12 text-muted">
                                                <p class="mb-1" key="t-grammer">If several languages coalesce the grammar</p>
                                                <p class="mb-0"><i class="mdi mdi-clock-outline"></i> <span key="t-min-ago">3 min ago</span></p>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                                <a href="javascript: void(0);" class="text-reset notification-item">
                                    <div class="d-flex">
                                        <img src="<?= base_url() ?>/assets/images/users/avatar-3.jpg" class="me-3 rounded-circle avatar-xs" alt="user-pic">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">James Lemire</h6>
                                            <div class="font-size-12 text-muted">
                                                <p class="mb-1" key="t-simplified">It will seem like simplified English.</p>
                                                <p class="mb-0"><i class="mdi mdi-clock-outline"></i> <span key="t-hours-ago">1 hours ago</span></p>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                                <a href="javascript: void(0);" class="text-reset notification-item">
                                    <div class="d-flex">
                                        <div class="avatar-xs me-3">
                                            <span class="avatar-title bg-success rounded-circle font-size-16">
                                                <i class="bx bx-badge-check"></i>
                                            </span>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1" key="t-shipped">Your item is shipped</h6>
                                            <div class="font-size-12 text-muted">
                                                <p class="mb-1" key="t-grammer">If several languages coalesce the grammar</p>
                                                <p class="mb-0"><i class="mdi mdi-clock-outline"></i> <span key="t-min-ago">3 min ago</span></p>
                                            </div>
                                        </div>
                                    </div>
                                </a>

                                <a href="javascript: void(0);" class="text-reset notification-item">
                                    <div class="d-flex">
                                        <img src="<?= base_url() ?>/assets/images/users/avatar-4.jpg" class="me-3 rounded-circle avatar-xs" alt="user-pic">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">Salena Layfield</h6>
                                            <div class="font-size-12 text-muted">
                                                <p class="mb-1" key="t-occidental">As a skeptical Cambridge friend of mine occidental.</p>
                                                <p class="mb-0"><i class="mdi mdi-clock-outline"></i> <span key="t-hours-ago">1 hours ago</span></p>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="p-2 border-top d-grid">
                                <a class="btn btn-sm btn-link font-size-14 text-center" href="javascript:void(0)">
                                    <i class="mdi mdi-arrow-right-circle me-1"></i> <span key="t-view-more">View More..</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="dropdown d-inline-block">
                        <button type="button" class="btn header-item waves-effect" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img class="rounded-circle header-profile-user" src="<?= isset($user) ? ($user->image !== null ? base_url() . '/upload/user/' . $user->image : base_url() . '/assets/images/users/avatar-1.jpg') : base_url() . '/assets/images/users/avatar-1.jpg' ?>" alt="Header Avatar">
                            <span class="d-none d-xl-inline-block ms-1" key="t-henry"><?= isset($user) ? $user->fullname : '-' ?></span>
                            <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                            <!-- item-->
                            <a class="dropdown-item" href="contacts-profile.html"><i class="bx bx-user font-size-16 align-middle me-1"></i> <span key="t-profile">Profile</span></a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item text-danger" href="javascript:aksiLogout(this);"><i class="bx bx-power-off font-size-16 align-middle me-1 text-danger"></i> <span key="t-logout">Logout</span></a>
                        </div>
                    </div>

                    <div class="dropdown d-none d-lg-inline-block ms-1">
                        <button type="button" class="btn header-item noti-icon waves-effect" data-bs-toggle="fullscreen">
                            <i class="bx bx-fullscreen"></i>
                        </button>
                    </div>

                </div>
            </div>
        </header>

        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                <h4 class="mb-sm-0 font-size-18">PORTAL LAYANAN</h4>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <?php if (isset($layanans)) { ?>
                            <?php if (count($layanans) > 0) {
                                foreach ($layanans as $key => $value) { ?>
                                    <?php if ($value['layanan_nama'] == "SITUPENG") { ?>
                                        <?php if ($user->role_user == 8 || $user->role_user == 1 || $user->role_user == 2) { ?>
                                            <?php if (cantGrantAccessSitupeng($user->id)) { ?>
                                            <?php } else { ?>
                                                <div class="col-xl-3 col-sm-6">
                                                    <div class="card text-center  _sorot-mouse">
                                                        <a href="<?= $value['layanan_url'] ?>">
                                                            <div class="card-body">
                                                                <div class="mb-4">
                                                                    <img class="avatar-lg" src="<?= base_url('uploads/layanan/' . $value['layanan_image']) ?>" alt="">
                                                                </div>
                                                                <h5 class="font-size-15 mb-1"><a href="<?= $value['layanan_url'] ?>" style="color: rgba(var(--bs-dark-rgb),var(--bs-text-opacity));" class="_color-h-hover"><?= $value['layanan_nama'] ?></a></h5>
                                                                <p><a class="_color-p-hover" style="color: #c3cbe4;" href="<?= $value['layanan_url'] ?>"><?= $value['layanan_deskripsi'] ?></a></p>

                                                            </div>
                                                        </a>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        <?php } else { ?>
                                            <?php continue; ?>
                                        <?php } ?>

                                    <?php } else if ($value['layanan_nama'] == "SIGAJI") { ?>
                                        <?php if (grantAccessSigaji($user->id)) { ?>
                                            <div class="col-xl-3 col-sm-6">
                                                <div class="card text-center  _sorot-mouse">
                                                    <a href="<?= $value['layanan_url'] ?>">
                                                        <div class="card-body">
                                                            <div class="mb-4">
                                                                <img class="avatar-lg" src="<?= base_url('uploads/layanan/' . $value['layanan_image']) ?>" alt="">
                                                            </div>
                                                            <h5 class="font-size-15 mb-1"><a href="<?= $value['layanan_url'] ?>" style="color: rgba(var(--bs-dark-rgb),var(--bs-text-opacity));" class="_color-h-hover"><?= $value['layanan_nama'] ?></a></h5>
                                                            <p><a class="_color-p-hover" style="color: #c3cbe4;" href="<?= $value['layanan_url'] ?>"><?= $value['layanan_deskripsi'] ?></a></p>

                                                        </div>
                                                    </a>
                                                </div>
                                            </div>
                                        <?php } else { ?>
                                            <?php continue; ?>
                                        <?php } ?>
                                    <?php } else if ($value['layanan_nama'] == "BACKBONE") { ?>
                                        <?php if (grantAccessBackbone($user->id)) { ?>
                                            <div class="col-xl-3 col-sm-6">
                                                <div class="card text-center  _sorot-mouse">
                                                    <a href="<?= $value['layanan_url'] ?>">
                                                        <div class="card-body">
                                                            <div class="mb-4">
                                                                <img class="avatar-lg" src="<?= base_url('uploads/layanan/' . $value['layanan_image']) ?>" alt="">
                                                            </div>
                                                            <h5 class="font-size-15 mb-1"><a href="<?= $value['layanan_url'] ?>" style="color: rgba(var(--bs-dark-rgb),var(--bs-text-opacity));" class="_color-h-hover"><?= $value['layanan_nama'] ?></a></h5>
                                                            <p><a class="_color-p-hover" style="color: #c3cbe4;" href="<?= $value['layanan_url'] ?>"><?= $value['layanan_deskripsi'] ?></a></p>

                                                        </div>
                                                    </a>
                                                </div>
                                            </div>
                                        <?php } else { ?>
                                            <?php continue; ?>
                                        <?php } ?>
                                    <?php } else { ?>
                                        <?php if (($value['layanan_nama'] == "SITUGU" && $user->role_user == 8) || ($value['layanan_nama'] == "SITUGU" && $user->role_user == 9)) { ?>
                                            <?php continue; ?>
                                        <?php } else { ?>
                                            <?php if ($value['layanan_nama'] == "SITUGU" && $user->role_user == 2) { ?>
                                                <?php if (grantAccessSitugu($user->id)) { ?>
                                                    <div class="col-xl-3 col-sm-6">
                                                        <div class="card text-center  _sorot-mouse">
                                                            <a href="<?= $value['layanan_url'] ?>">
                                                                <div class="card-body">
                                                                    <div class="mb-4">
                                                                        <img class="avatar-lg" src="<?= base_url('uploads/layanan/' . $value['layanan_image']) ?>" alt="">
                                                                    </div>
                                                                    <h5 class="font-size-15 mb-1"><a href="<?= $value['layanan_url'] ?>" style="color: rgba(var(--bs-dark-rgb),var(--bs-text-opacity));" class="_color-h-hover"><?= $value['layanan_nama'] ?></a></h5>
                                                                    <p><a class="_color-p-hover" style="color: #c3cbe4;" href="<?= $value['layanan_url'] ?>"><?= $value['layanan_deskripsi'] ?></a></p>

                                                                </div>
                                                            </a>
                                                        </div>
                                                    </div>
                                                <?php } else { ?>
                                                    <?php continue; ?>
                                                <?php } ?>
                                            <?php } else { ?>
                                                <div class="col-xl-3 col-sm-6">
                                                    <div class="card text-center  _sorot-mouse">
                                                        <a href="<?= $value['layanan_url'] ?>">
                                                            <div class="card-body">
                                                                <div class="mb-4">
                                                                    <img class="avatar-lg" src="<?= base_url('uploads/layanan/' . $value['layanan_image']) ?>" alt="">
                                                                </div>
                                                                <h5 class="font-size-15 mb-1"><a href="<?= $value['layanan_url'] ?>" style="color: rgba(var(--bs-dark-rgb),var(--bs-text-opacity));" class="_color-h-hover"><?= $value['layanan_nama'] ?></a></h5>
                                                                <p><a class="_color-p-hover" style="color: #c3cbe4;" href="<?= $value['layanan_url'] ?>"><?= $value['layanan_deskripsi'] ?></a></p>

                                                            </div>
                                                        </a>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        <?php } ?>
                                    <?php } ?>
                                <?php } ?>
                            <?php } ?>
                        <?php } ?>

                    </div>
                </div>
            </div>

            <?= $this->include('templates/footer'); ?>
        </div>
    </div>
    <div class="right-bar">
        <div data-simplebar class="h-100">
            <div class="rightbar-title d-flex align-items-center px-3 py-4">
                <h5 class="m-0 me-2">Settings</h5>
                <a href="javascript:void(0);" class="right-bar-toggle ms-auto">
                    <i class="mdi mdi-close noti-icon"></i>
                </a>
            </div>
            <hr class="mt-0" />
            <h6 class="text-center mb-0">Choose Layouts</h6>
            <div class="p-4">
                <div class="mb-2">
                    <img src="<?= base_url() ?>/assets/images/layouts/layout-1.jpg" class="img-thumbnail" alt="layout images">
                </div>
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input theme-choice" type="checkbox" id="light-mode-switch">
                    <label class="form-check-label" for="light-mode-switch">Light Mode</label>
                </div>
                <div class="mb-2">
                    <img src="<?= base_url() ?>/assets/images/layouts/layout-2.jpg" class="img-thumbnail" alt="layout images">
                </div>
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input theme-choice" type="checkbox" id="dark-mode-switch" checked>
                    <label class="form-check-label" for="dark-mode-switch">Dark Mode</label>
                </div>
                <div class="mb-2">
                    <img src="<?= base_url() ?>/assets/images/layouts/layout-3.jpg" class="img-thumbnail" alt="layout images">
                </div>
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input theme-choice" type="checkbox" id="rtl-mode-switch">
                    <label class="form-check-label" for="rtl-mode-switch">RTL Mode</label>
                </div>
                <div class="mb-2">
                    <img src="<?= base_url() ?>/assets/images/layouts/layout-4.jpg" class="img-thumbnail" alt="layout images">
                </div>
                <div class="form-check form-switch mb-5">
                    <input class="form-check-input theme-choice" type="checkbox" id="dark-rtl-mode-switch">
                    <label class="form-check-label" for="dark-rtl-mode-switch">Dark RTL Mode</label>
                </div>
            </div>
        </div>
    </div>
    <div class="rightbar-overlay"></div>
    <script src="<?= base_url() ?>/assets/libs/jquery/jquery.min.js"></script>
    <script src="<?= base_url() ?>/assets/js/blockUI.min.js"></script>
    <script src="<?= base_url() ?>/assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="<?= base_url() ?>/assets/libs/metismenu/metisMenu.min.js"></script>
    <script src="<?= base_url() ?>/assets/libs/simplebar/simplebar.min.js"></script>
    <script src="<?= base_url() ?>/assets/libs/node-waves/waves.min.js"></script>
    <script src="<?= base_url() ?>/assets/libs/sweetalert2/sweetalert2.min.js"></script>
    <script src="<?= base_url() ?>/assets/js/app.js"></script>
    <?= $this->renderSection('scriptBottom'); ?>
    <script>
        function reloadPage(action = "") {
            if (action === "") {
                document.location.href = "<?= current_url(true); ?>";
            } else {
                document.location.href = action;
            }
        }

        function aksiLogout(e) {
            // e.preventDefault();
            const href = BASE_URL + "/auth/logout";
            Swal.fire({
                title: 'Apakah anda yakin ingin keluar?',
                text: "Keluar Dari Aplikasi.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Sign Out!'
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        url: href,
                        type: 'GET',
                        contentType: false,
                        cache: false,
                        beforeSend: function() {
                            $('body.loading-logout').block({
                                message: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>'
                            });
                        },
                        success: function(resMsg) {
                            Swal.fire(
                                'Berhasil!',
                                "Anda berhasil logout.",
                                'success'
                            ).then((valRes) => {
                                document.location.href = BASE_URL + "/web/home";
                            })
                        },
                        error: function() {
                            $('body.loading-logout').unblock();
                            Swal.fire(
                                'Gagal!',
                                "Server sedang sibuk, silahkan ulangi beberapa saat lagi.",
                                'warning'
                            );
                        }
                    })
                }
            })
        };
    </script>
</body>

</html>