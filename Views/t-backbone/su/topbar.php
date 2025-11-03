<?php $uri = current_url(true); ?>
<div class="topnav">
    <div class="container-fluid">
        <nav class="navbar navbar-light navbar-expand-lg topnav-menu">
            <div class="collapse navbar-collapse" id="topnav-menu-content">
                <ul class="navbar-nav">
                    <?php if (isset($user)) { ?>
                        <li class="nav-item">
                            <a class="nav-link <?= ($uri->getSegment(2) == "su" && $uri->getSegment(3) == "home") ? ' active-menu-href' : '' ?>" href="<?= ($uri->getSegment(2) == "su" && $uri->getSegment(3) == "home") ? 'javascript:;' : base_url('backbone/su/home') ?>">
                                <i class="bx bx-home-circle me-2"></i>
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle arrow-none <?= ($uri->getSegment(2) == "su" && $uri->getSegment(3) == "masterdata") ? ' active-menu-href' : '' ?>" href="#" id="topnav-masterdata" role="button">
                                <i class="bx bx-layout me-2"></i><span key="t-masterdata">MASTER DATA</span>
                                <div class="arrow-down"></div>
                            </a>
                            <div class="dropdown-menu" aria-labelledby="topnav-masterdata">
                                <a href="<?= base_url('backbone/su/masterdata/pegawai') ?>" class="dropdown-item <?= ($uri->getSegment(2) == "su" && $uri->getSegment(3) == "masterdata" && $uri->getSegment(4) == "pegawai") ? ' active-menu-href' : '' ?>" key="t-masterdata-pegawai">Pegawai</a>
                                <a href="<?= base_url('backbone/su/masterdata/ptk') ?>" class="dropdown-item <?= ($uri->getSegment(2) == "su" && $uri->getSegment(3) == "masterdata" && $uri->getSegment(4) == "ptk") ? ' active-menu-href' : '' ?>" key="t-masterdata-ptk">PTK</a>
                                <a href="<?= base_url('backbone/su/masterdata/sekolah') ?>" class="dropdown-item <?= ($uri->getSegment(2) == "su" && $uri->getSegment(3) == "masterdata" && $uri->getSegment(4) == "sekolah") ? ' active-menu-href' : '' ?>" key="t-masterdata-sekolah">Sekolah</a>
                                <a href="<?= base_url('backbone/su/masterdata/pengguna') ?>" class="dropdown-item <?= ($uri->getSegment(2) == "su" && $uri->getSegment(3) == "masterdata" && $uri->getSegment(4) == "pengguna") ? ' active-menu-href' : '' ?>" key="t-masterdata-pengguna">Pengguna</a>
                            </div>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="javascript:aksiLogout(this);">
                                <i class="bx bx-power-off font-size-16 align-middle me-1 text-danger"></i><span key="t-logout">Logout</span>
                            </a>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        </nav>
    </div>
</div>