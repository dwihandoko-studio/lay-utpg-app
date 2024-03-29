<?php $uri = current_url(true); ?>
<div class="topnav">
    <div class="container-fluid">
        <nav class="navbar navbar-light navbar-expand-lg topnav-menu">
            <div class="collapse navbar-collapse" id="topnav-menu-content">
                <ul class="navbar-nav">
                    <?php if (isset($user)) { ?>
                        <li class="nav-item">
                            <a class="nav-link <?= ($uri->getSegment(2) == "peng" && $uri->getSegment(3) == "home") ? ' active-menu-href' : '' ?>" href="<?= ($uri->getSegment(2) == "peng" && $uri->getSegment(3) == "home") ? 'javascript:;' : base_url('situpeng/peng/home') ?>">
                                <i class="bx bx-home-circle me-2"></i>
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle arrow-none <?= ($uri->getSegment(2) == "peng" && $uri->getSegment(3) == "masterdata") ? ' active-menu-href' : '' ?>" href="#" id="topnav-masterdata" role="button">
                                <i class="bx bx-layout me-2"></i><span key="t-masterdata">MASTER DATA</span>
                                <div class="arrow-down"></div>
                            </a>
                            <div class="dropdown-menu" aria-labelledby="topnav-masterdata">
                                <a href="<?= base_url('situpeng/peng/masterdata/individu') ?>" class="dropdown-item <?= ($uri->getSegment(2) == "peng" && $uri->getSegment(3) == "masterdata" && $uri->getSegment(4) == "individu") ? ' active-menu-href' : '' ?>" key="t-masterdata-individu">Individu</a>
                            </div>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle arrow-none <?= ($uri->getSegment(2) == "peng" && $uri->getSegment(3) == "binaan") ? ' active-menu-href' : '' ?>" href="#" id="topnav-binaan" role="button">
                                <i class="bx bx-shape-circle me-2"></i><span key="t-binaan">DATA BINAAN</span>
                                <div class="arrow-down"></div>
                            </a>
                            <div class="dropdown-menu" aria-labelledby="topnav-binaan">
                                <a href="<?= base_url('situpeng/peng/binaan/sekolah') ?>" class="dropdown-item <?= ($uri->getSegment(2) == "peng" && $uri->getSegment(3) == "binaan" && $uri->getSegment(4) == "sekolah") ? ' active-menu-href' : '' ?>" key="t-binaan-sekolah">Sekolah Binaan</a>
                                <a href="<?= base_url('situpeng/peng/binaan/guru') ?>" class="dropdown-item <?= ($uri->getSegment(2) == "peng" && $uri->getSegment(3) == "binaan" && $uri->getSegment(4) == "guru") ? ' active-menu-href' : '' ?>" key="t-binaan-guru">Guru Binaan</a>
                            </div>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle arrow-none <?= ($uri->getSegment(2) == "peng" && $uri->getSegment(3) == "doc") ? ' active-menu-href' : '' ?>" href="#" id="topnav-updokument" role="button">
                                <i class="bx bx-receipt me-2"></i><span key="t-updokument">DOKUMEN</span>
                                <div class="arrow-down"></div>
                            </a>
                            <div class="dropdown-menu" aria-labelledby="topnav-updokument">
                                <a href="<?= base_url('situpeng/peng/doc/master') ?>" class="dropdown-item <?= ($uri->getSegment(2) == "peng" && $uri->getSegment(3) == "doc" && $uri->getSegment(4) == "master") ? ' active-menu-href' : '' ?>" key="t-updokument-master">Data Master</a>
                                <a href="<?= base_url('situpeng/peng/doc/atribut') ?>" class="dropdown-item <?= ($uri->getSegment(2) == "peng" && $uri->getSegment(3) == "doc" && $uri->getSegment(4) == "atribut") ? ' active-menu-href' : '' ?>" key="t-updokument-atribut">Data Atribut</a>
                            </div>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= ($uri->getSegment(2) == "peng" && $uri->getSegment(3) == "ajuan") ? ' active-menu-href' : '' ?>" href="<?= ($uri->getSegment(2) == "peng" && $uri->getSegment(3) == "ajuan") ? 'javascript:;' : base_url('situpeng/peng/ajuan') ?>">
                                <i class="bx bx-columns me-2"></i><span key="t-absen">USUL VERVAL</span>
                            </a>
                        </li>
                        <?php
                        $grandtVerif = grantedVerifikasiPengawas($user->id);
                        if ($grandtVerif) {
                            // if ($grandtVerif->id !== NULL) {
                        ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle arrow-none <?= ($uri->getSegment(2) == "peng" && $uri->getSegment(3) == "verifikasi") ? ' active-menu-href' : '' ?>" href="#" id="topnav-verifikasi" role="button">
                                    <i class="bx bx-rename me-2"></i><span key="t-verifikasi">Verifikasi</span>
                                    <div class="arrow-down"></div>
                                </a>
                                <div class="dropdown-menu" aria-labelledby="topnav-verifikasi">
                                    <a href="<?= base_url('situpeng/peng/verifikasi/tpg') ?>" class="dropdown-item <?= ($uri->getSegment(2) == "peng" && $uri->getSegment(3) == "verifikasi" && $uri->getSegment(4) == "tpg") ? ' active-menu-href' : '' ?>" key="t-verifikasi-tpg">Tunjangan Profesi Guru</a>
                                </div>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle arrow-none <?= ($uri->getSegment(2) == "peng" && $uri->getSegment(3) == "sptjm") ? ' active-menu-href' : '' ?>" href="#" id="topnav-sptjm" role="button">
                                    <i class="bx bx-spreadsheet me-2"></i><span key="t-sptjm">SPTJM</span>
                                    <div class="arrow-down"></div>
                                </a>
                                <div class="dropdown-menu" aria-labelledby="topnav-sptjm">
                                    <a href="<?= base_url('situpeng/peng/sptjm/tpg') ?>" class="dropdown-item <?= ($uri->getSegment(2) == "peng" && $uri->getSegment(3) == "sptjm" && $uri->getSegment(4) == "tpg") ? ' active-menu-href' : '' ?>" key="t-sptjm-tpg">Tunjangan Profesi Guru</a>
                                </div>
                            </li>
                            <?php //} 
                            ?>
                        <?php } ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle arrow-none" href="#" id="topnav-spj" role="button">
                                <i class="bx bx-task me-2"></i>
                                <span key="t-spj"> SPJ</span>
                                <div class="arrow-down"></div>
                            </a>

                            <div class="dropdown-menu mega-dropdown-menu px-2 dropdown-mega-menu-xl" aria-labelledby="topnav-spj">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <h6> TPG (Sertifikasi)</h6>
                                        <div>
                                            <a href="<?= base_url('situpeng/peng/spj/tpg/antrian') ?>" class="dropdown-item <?= ($uri->getSegment(2) == "peng" && $uri->getSegment(3) == "spj" && $uri->getSegment(4) == "tpg"  && $uri->getSegment(5) == "antrian") ? ' active-menu-href' : '' ?>" key="t-spj-antrian">Antrian</a>
                                            <a href="<?= base_url('situpeng/peng/spj/tpg/ditolak') ?>" class="dropdown-item <?= ($uri->getSegment(2) == "peng" && $uri->getSegment(3) == "spj" && $uri->getSegment(4) == "tpg"  && $uri->getSegment(5) == "ditolak") ? ' active-menu-href' : '' ?>" key="t-spj-ditolak">Ditolak</a>
                                            <a href="<?= base_url('situpeng/peng/spj/tpg/lolosberkas') ?>" class="dropdown-item <?= ($uri->getSegment(2) == "peng" && $uri->getSegment(3) == "spj" && $uri->getSegment(4) == "tpg"  && $uri->getSegment(5) == "lolosberkas") ? ' active-menu-href' : '' ?>" key="t-spj-lolosberkas">Lolos Verifikasi</a>
                                        </div>
                                    </div>
                                </div>

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