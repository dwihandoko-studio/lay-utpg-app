<?php $uri = current_url(true); ?>
<div class="topnav">
    <div class="container-fluid">
        <nav class="navbar navbar-light navbar-expand-lg topnav-menu">
            <div class="collapse navbar-collapse" id="topnav-menu-content">
                <ul class="navbar-nav">
                    <?php if (isset($user)) { ?>
                        <li class="nav-item">
                            <a class="nav-link <?= ($uri->getSegment(2) == "su" && $uri->getSegment(3) == "home") ? ' active-menu-href' : '' ?>" href="<?= ($uri->getSegment(2) == "su" && $uri->getSegment(3) == "home") ? 'javascript:;' : base_url('situgu/su/home') ?>">
                                <i class="bx bx-home-circle me-2"></i>
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle arrow-none <?= ($uri->getSegment(2) == "su" && $uri->getSegment(3) == "masterdata") ? ' active-menu-href' : '' ?>" href="#" id="topnav-masterdata" role="button">
                                <i class="bx bx-layout me-2"></i><span key="t-masterdata">MASTER DATA</span>
                                <div class="arrow-down"></div>
                            </a>
                            <div class="dropdown-menu" aria-labelledby="topnav-masterdata">
                                <a href="<?= base_url('situgu/su/masterdata/sekolah') ?>" class="dropdown-item <?= ($uri->getSegment(2) == "su" && $uri->getSegment(3) == "masterdata" && $uri->getSegment(4) == "sekolah") ? ' active-menu-href' : '' ?>" key="t-masterdata-sekolah">Sekolah</a>
                                <a href="<?= base_url('situgu/su/masterdata/ptk') ?>" class="dropdown-item <?= ($uri->getSegment(2) == "su" && $uri->getSegment(3) == "masterdata" && $uri->getSegment(4) == "ptk") ? ' active-menu-href' : '' ?>" key="t-masterdata-ptk">PTK</a>
                                <a href="<?= base_url('situgu/su/masterdata/refgaji') ?>" class="dropdown-item <?= ($uri->getSegment(2) == "su" && $uri->getSegment(3) == "masterdata" && $uri->getSegment(4) == "refgaji") ? ' active-menu-href' : '' ?>" key="t-masterdata-refgaji">Referensi Gaji</a>
                                <a href="<?= base_url('situgu/su/masterdata/reftw') ?>" class="dropdown-item <?= ($uri->getSegment(2) == "su" && $uri->getSegment(3) == "masterdata" && $uri->getSegment(4) == "reftw") ? ' active-menu-href' : '' ?>" key="t-masterdata-reftw">Referensi Tahun TW</a>
                                <a href="<?= base_url('situgu/su/masterdata/pengguna') ?>" class="dropdown-item <?= ($uri->getSegment(2) == "su" && $uri->getSegment(3) == "masterdata" && $uri->getSegment(4) == "pengguna") ? ' active-menu-href' : '' ?>" key="t-masterdata-pengguna">Pengguna</a>
                                <a href="<?= base_url('situgu/su/masterdata/ptkterhapus') ?>" class="dropdown-item <?= ($uri->getSegment(2) == "su" && $uri->getSegment(3) == "masterdata" && $uri->getSegment(4) == "ptkterhapus") ? ' active-menu-href' : '' ?>" key="t-masterdata-ptkterhapus">PTK Terhapus</a>
                            </div>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle arrow-none <?= ($uri->getSegment(2) == "su" && $uri->getSegment(3) == "verifikasi") ? ' active-menu-href' : '' ?>" href="#" id="topnav-verifikasi" role="button">
                                <i class="bx bx-rename me-2"></i><span key="t-verifikasi">VERIFIKASI</span>
                                <div class="arrow-down"></div>
                            </a>
                            <div class="dropdown-menu" aria-labelledby="topnav-verifikasi">
                                <a href="<?= base_url('situgu/su/verifikasi/pengguna') ?>" class="dropdown-item <?= ($uri->getSegment(2) == "su" && $uri->getSegment(3) == "verifikasi" && $uri->getSegment(4) == "pengguna") ? ' active-menu-href' : '' ?>" key="t-verifikasi-pengguna">Pengguna Sekolah</a>
                                <a href="<?= base_url('situgu/su/verifikasi/ptk') ?>" class="dropdown-item <?= ($uri->getSegment(2) == "su" && $uri->getSegment(3) == "verifikasi" && $uri->getSegment(4) == "ptk") ? ' active-menu-href' : '' ?>" key="t-verifikasi-ptk">Penghapusan PTK</a>
                                <a href="<?= base_url('situgu/su/verifikasi/tpg') ?>" class="dropdown-item <?= ($uri->getSegment(2) == "su" && $uri->getSegment(3) == "verifikasi" && $uri->getSegment(4) == "tpg") ? ' active-menu-href' : '' ?>" key="t-verifikasi-tpg">Tunjangan Profesi Guru</a>
                                <a href="<?= base_url('situgu/su/verifikasi/tamsil') ?>" class="dropdown-item <?= ($uri->getSegment(2) == "su" && $uri->getSegment(3) == "verifikasi" && $uri->getSegment(4) == "tamsil") ? ' active-menu-href' : '' ?>" key="t-verifikasi-tamsil">Tamsil</a>
                                <a href="<?= base_url('situgu/su/verifikasi/spj/tpg') ?>" class="dropdown-item <?= ($uri->getSegment(2) == "su" && $uri->getSegment(3) == "verifikasi" && $uri->getSegment(4) == "spj" && $uri->getSegment(5) == "tpg") ? ' active-menu-href' : '' ?>" key="t-verifikasi-spj-tpg">SPJ Tunjangan Profesi Guru</a>
                                <a href="<?= base_url('situgu/su/verifikasi/spj/tamsil') ?>" class="dropdown-item <?= ($uri->getSegment(2) == "su" && $uri->getSegment(3) == "verifikasi" && $uri->getSegment(4) == "spj" && $uri->getSegment(5) == "tamsil") ? ' active-menu-href' : '' ?>" key="t-verifikasi-spj-tamsil">SPJ Tamsil</a>
                                <!-- <a href="<?= base_url('situgu/su/verifikasi/pghm') ?>" class="dropdown-item <?= ($uri->getSegment(2) == "su" && $uri->getSegment(3) == "verifikasi" && $uri->getSegment(4) == "pghm") ? ' active-menu-href' : '' ?>" key="t-verifikasi-pghm">PGHM</a> -->
                            </div>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle arrow-none <?= ($uri->getSegment(2) == "su" && $uri->getSegment(3) == "us") ? ' active-menu-href' : '' ?>" href="javascript:;" id="topnav-usulan" role="button">
                                <i class="bx bx-columns me-2"></i>
                                <span key="t-usulan"> USULAN</span>
                                <div class="arrow-down"></div>
                            </a>

                            <div class="dropdown-menu mega-dropdown-menu px-2 dropdown-mega-menu-xl" aria-labelledby="topnav-usulan">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <h6> TPG (Sertifikasi)</h6>
                                        <div>
                                            <a href="<?= base_url('situgu/su/us/tpg/antrian') ?>" class="dropdown-item <?= ($uri->getSegment(2) == "su" && $uri->getSegment(3) == "us" && $uri->getSegment(4) == "tpg"  && $uri->getSegment(5) == "antrian") ? ' active-menu-href' : '' ?>" key="t-us-antrian">Antrian</a>
                                            <a href="<?= base_url('situgu/su/us/tpg/ditolak') ?>" class="dropdown-item <?= ($uri->getSegment(2) == "su" && $uri->getSegment(3) == "us" && $uri->getSegment(4) == "tpg"  && $uri->getSegment(5) == "ditolak") ? ' active-menu-href' : '' ?>" key="t-us-ditolak">Ditolak</a>
                                            <a href="<?= base_url('situgu/su/us/tpg/lolosberkas') ?>" class="dropdown-item <?= ($uri->getSegment(2) == "su" && $uri->getSegment(3) == "us" && $uri->getSegment(4) == "tpg"  && $uri->getSegment(5) == "lolosberkas") ? ' active-menu-href' : '' ?>" key="t-us-lolosberkas">Lolos Verifikasi</a>
                                            <a href="<?= base_url('situgu/su/us/tpg/siapsk') ?>" class="dropdown-item <?= ($uri->getSegment(2) == "su" && $uri->getSegment(3) == "us" && $uri->getSegment(4) == "tpg"  && $uri->getSegment(5) == "siapsk") ? ' active-menu-href' : '' ?>" key="t-us-siapsk">Siap SK</a>
                                            <a href="<?= base_url('situgu/su/us/tpg/skterbit') ?>" class="dropdown-item <?= ($uri->getSegment(2) == "su" && $uri->getSegment(3) == "us" && $uri->getSegment(4) == "tpg"  && $uri->getSegment(5) == "skterbit") ? ' active-menu-href' : '' ?>" key="t-us-skterbit">SK Terbit</a>
                                            <a href="<?= base_url('situgu/su/us/tpg/prosestransfer') ?>" class="dropdown-item <?= ($uri->getSegment(2) == "su" && $uri->getSegment(3) == "us" && $uri->getSegment(4) == "tpg"  && $uri->getSegment(5) == "prosestransfer") ? ' active-menu-href' : '' ?>" key="t-us-prosestransfer">Proses Transfer</a>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <h6> TAMSIL</h6>
                                        <div>
                                            <a href="<?= base_url('situgu/su/us/tamsil/antrian') ?>" class="dropdown-item <?= ($uri->getSegment(2) == "su" && $uri->getSegment(3) == "us" && $uri->getSegment(4) == "tamsil"  && $uri->getSegment(5) == "antrian") ? ' active-menu-href' : '' ?>" key="t-us-antrian">Antrian</a>
                                            <a href="<?= base_url('situgu/su/us/tamsil/ditolak') ?>" class="dropdown-item <?= ($uri->getSegment(2) == "su" && $uri->getSegment(3) == "us" && $uri->getSegment(4) == "tamsil"  && $uri->getSegment(5) == "ditolak") ? ' active-menu-href' : '' ?>" key="t-us-ditolak">Ditolak</a>
                                            <a href="<?= base_url('situgu/su/us/tamsil/lolosberkas') ?>" class="dropdown-item <?= ($uri->getSegment(2) == "su" && $uri->getSegment(3) == "us" && $uri->getSegment(4) == "tamsil"  && $uri->getSegment(5) == "lolosberkas") ? ' active-menu-href' : '' ?>" key="t-us-lolosberkas">Lolos Verifikasi</a>
                                            <a href="<?= base_url('situgu/su/us/tamsil/prosestransfer') ?>" class="dropdown-item <?= ($uri->getSegment(2) == "su" && $uri->getSegment(3) == "us" && $uri->getSegment(4) == "tamsil"  && $uri->getSegment(5) == "prosestransfer") ? ' active-menu-href' : '' ?>" key="t-us-prosestransfer">Proses Transfer</a>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle arrow-none <?= ($uri->getSegment(2) == "su" && $uri->getSegment(3) == "upload") ? ' active-menu-href' : '' ?>" href="javascript:;" id="topnav-upload" role="button">
                                <i class="bx bx-cloud-upload me-2"></i>
                                <span key="t-upload"> UPLOAD</span>
                                <div class="arrow-down"></div>
                            </a>

                            <div class="dropdown-menu mega-dropdown-menu px-2 dropdown-mega-menu-xl" aria-labelledby="topnav-upload">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <h6> TPG (Sertifikasi)</h6>
                                        <div>
                                            <a href="<?= base_url('situgu/su/upload/tpg/matching') ?>" class="dropdown-item <?= ($uri->getSegment(2) == "su" && $uri->getSegment(3) == "upload" && $uri->getSegment(4) == "tpg"  && $uri->getSegment(5) == "matching") ? ' active-menu-href' : '' ?>" key="t-upload-tpg-matching">Matching Simtun</a>
                                            <a href="<?= base_url('situgu/su/upload/tpg/skterbit') ?>" class="dropdown-item <?= ($uri->getSegment(2) == "su" && $uri->getSegment(3) == "upload" && $uri->getSegment(4) == "tpg"  && $uri->getSegment(5) == "skterbit") ? ' active-menu-href' : '' ?>" key="t-upload-tpg-skterbit">SK Terbit</a>
                                            <a href="<?= base_url('situgu/su/upload/tpg/prosestransfer') ?>" class="dropdown-item <?= ($uri->getSegment(2) == "su" && $uri->getSegment(3) == "upload" && $uri->getSegment(4) == "tpg"  && $uri->getSegment(5) == "prosestransfer") ? ' active-menu-href' : '' ?>" key="t-upload-tpg-prosestransfer">Proses Transfer</a>
                                            <a href="<?= base_url('situgu/su/upload/tpg/lanjutkantw') ?>" class="dropdown-item <?= ($uri->getSegment(2) == "su" && $uri->getSegment(3) == "upload" && $uri->getSegment(4) == "tpg"  && $uri->getSegment(5) == "lanjutkantw") ? ' active-menu-href' : '' ?>" key="t-upload-tpg-lanjutkantw">Lanjutkan TW</a>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <h6> TAMSIL</h6>
                                        <div>
                                            <a href="<?= base_url('situgu/su/upload/tamsil/prosestransfer') ?>" class="dropdown-item <?= ($uri->getSegment(2) == "su" && $uri->getSegment(3) == "upload" && $uri->getSegment(4) == "tamsil"  && $uri->getSegment(5) == "prosestransfer") ? ' active-menu-href' : '' ?>" key="t-upload-tamsil-prosestransfer">Proses Transfer</a>
                                            <a href="<?= base_url('situgu/su/upload/tamsil/lanjutkantw') ?>" class="dropdown-item <?= ($uri->getSegment(2) == "su" && $uri->getSegment(3) == "upload" && $uri->getSegment(4) == "tamsil"  && $uri->getSegment(5) == "lanjutkantw") ? ' active-menu-href' : '' ?>" key="t-upload-tamsil-lanjutkantw">Lanjutkan TW</a>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle arrow-none <?= ($uri->getSegment(2) == "su" && $uri->getSegment(3) == "sptjm") ? ' active-menu-href' : '' ?>" href="#" id="topnav-sptjm" role="button">
                                <i class="bx bx-spreadsheet me-2"></i><span key="t-sptjm">SPTJM</span>
                                <div class="arrow-down"></div>
                            </a>
                            <div class="dropdown-menu" aria-labelledby="topnav-sptjm">
                                <a href="<?= base_url('situgu/su/sptjm/tpg') ?>" class="dropdown-item <?= ($uri->getSegment(2) == "su" && $uri->getSegment(3) == "sptjm" && $uri->getSegment(4) == "tpg") ? ' active-menu-href' : '' ?>" key="t-sptjm-tpg">Tunjangan Profesi Guru</a>
                                <a href="<?= base_url('situgu/su/sptjm/tamsil') ?>" class="dropdown-item <?= ($uri->getSegment(2) == "su" && $uri->getSegment(3) == "sptjm" && $uri->getSegment(4) == "tamsil") ? ' active-menu-href' : '' ?>" key="t-sptjm-tamsil">Tamsil</a>
                                <a href="<?= base_url('situgu/su/sptjm/verifikasi') ?>" class="dropdown-item <?= ($uri->getSegment(2) == "su" && $uri->getSegment(3) == "sptjm" && $uri->getSegment(4) == "verifikasi") ? ' active-menu-href' : '' ?>" key="t-sptjm-tamsil">Verifikasi</a>
                            </div>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle arrow-none <?= ($uri->getSegment(2) == "su" && $uri->getSegment(3) == "spj") ? ' active-menu-href' : '' ?>" href="#" id="topnav-spj" role="button">
                                <i class="bx bx-spreadsheet me-2"></i><span key="t-spj">SPJ</span>
                                <div class="arrow-down"></div>
                            </a>
                            <div class="dropdown-menu" aria-labelledby="topnav-spj">
                                <a href="<?= base_url('situgu/su/spj/tpg') ?>" class="dropdown-item <?= ($uri->getSegment(2) == "su" && $uri->getSegment(3) == "spj" && $uri->getSegment(4) == "tpg") ? ' active-menu-href' : '' ?>" key="t-spj-tpg">Tunjangan Profesi Guru</a>
                                <a href="<?= base_url('situgu/su/spj/tamsil') ?>" class="dropdown-item <?= ($uri->getSegment(2) == "su" && $uri->getSegment(3) == "spj" && $uri->getSegment(4) == "tamsil") ? ' active-menu-href' : '' ?>" key="t-spj-tamsil">Tamsil</a>
                            </div>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle arrow-none <?= ($uri->getSegment(2) == "su" && $uri->getSegment(3) == "setting") ? ' active-menu-href' : '' ?>" href="#" id="topnav-setting" role="button">
                                <i class="bx bx-cog me-2"></i><span key="t-setting">SETTING</span>
                                <div class="arrow-down"></div>
                            </a>
                            <div class="dropdown-menu" aria-labelledby="topnav-setting">
                                <a href="<?= base_url('situgu/su/setting/informasi') ?>" class="dropdown-item <?= ($uri->getSegment(2) == "su" && $uri->getSegment(3) == "setting" && $uri->getSegment(4) == "informasi") ? ' active-menu-href' : '' ?>" key="t-setting-informasi">Informasi</a>
                                <a href="<?= base_url('situgu/su/setting/role') ?>" class="dropdown-item <?= ($uri->getSegment(2) == "su" && $uri->getSegment(3) == "setting" && $uri->getSegment(4) == "role") ? ' active-menu-href' : '' ?>" key="t-setting-role">Role Access</a>
                                <a href="<?= base_url('situgu/su/setting/grantedverifikasi') ?>" class="dropdown-item <?= ($uri->getSegment(2) == "su" && $uri->getSegment(3) == "setting" && $uri->getSegment(4) == "grantedverifikasi") ? ' active-menu-href' : '' ?>" key="t-setting-grantedverifikasi">Acess Verifikasi</a>
                                <a href="<?= base_url('situgu/su/setting/sptjm') ?>" class="dropdown-item <?= ($uri->getSegment(2) == "su" && $uri->getSegment(3) == "setting" && $uri->getSegment(4) == "sptjm") ? ' active-menu-href' : '' ?>" key="t-setting-sptjm">SPTJM</a>
                                <a href="<?= base_url('situgu/su/setting/verifikasi') ?>" class="dropdown-item <?= ($uri->getSegment(2) == "su" && $uri->getSegment(3) == "setting" && $uri->getSegment(4) == "verifikasi") ? ' active-menu-href' : '' ?>" key="t-setting-verifikasi">Verifikasi</a>
                                <a href="<?= base_url('situgu/su/setting/upspj') ?>" class="dropdown-item <?= ($uri->getSegment(2) == "su" && $uri->getSegment(3) == "setting" && $uri->getSegment(4) == "upspj") ? ' active-menu-href' : '' ?>" key="t-setting-upspj">Upload SPJ</a>
                                <a href="<?= base_url('situgu/su/setting/mt') ?>" class="dropdown-item <?= ($uri->getSegment(2) == "su" && $uri->getSegment(3) == "setting" && $uri->getSegment(4) == "mt") ? ' active-menu-href' : '' ?>" key="t-setting-mt">Maintenance</a>
                                <a href="<?= base_url('situgu/su/setting/accessmt') ?>" class="dropdown-item <?= ($uri->getSegment(2) == "su" && $uri->getSegment(3) == "setting" && $uri->getSegment(4) == "accessmt") ? ' active-menu-href' : '' ?>" key="t-setting-accessmt">Granted Access MT</a>
                                <a href="<?= base_url('situgu/su/setting/accesstugu') ?>" class="dropdown-item <?= ($uri->getSegment(2) == "su" && $uri->getSegment(3) == "setting" && $uri->getSegment(4) == "accesstugu") ? ' active-menu-href' : '' ?>" key="t-setting-accessmt">Granted Access Admin Situgu</a>
                                <a href="<?= base_url('situgu/su/setting/accessgaji') ?>" class="dropdown-item <?= ($uri->getSegment(2) == "su" && $uri->getSegment(3) == "setting" && $uri->getSegment(4) == "accessgaji") ? ' active-menu-href' : '' ?>" key="t-setting-accessgaji">Granted Access Sigaji</a>
                                <a href="<?= base_url('situgu/su/setting/accessbank') ?>" class="dropdown-item <?= ($uri->getSegment(2) == "su" && $uri->getSegment(3) == "setting" && $uri->getSegment(4) == "accessbank") ? ' active-menu-href' : '' ?>" key="t-setting-accessbank">Granted Access Bank</a>
                                <a href="<?= base_url('situgu/su/setting/cantaccesssitupeng') ?>" class="dropdown-item <?= ($uri->getSegment(2) == "su" && $uri->getSegment(3) == "setting" && $uri->getSegment(4) == "cantaccesssitupeng") ? ' active-menu-href' : '' ?>" key="t-setting-cantaccesssitupeng">Cant Access Situpeng</a>
                                <a href="<?= base_url('situgu/su/setting/grantedsynbakbone') ?>" class="dropdown-item <?= ($uri->getSegment(2) == "su" && $uri->getSegment(3) == "setting" && $uri->getSegment(4) == "grantedsynbakbone") ? ' active-menu-href' : '' ?>" key="t-setting-grantsyncrone">Granted Access Syncrone Backbone</a>
                                <a href="<?= base_url('situgu/su/setting/grantedusulancustom') ?>" class="dropdown-item <?= ($uri->getSegment(2) == "su" && $uri->getSegment(3) == "setting" && $uri->getSegment(4) == "grantedusulancustom") ? ' active-menu-href' : '' ?>" key="t-setting-grantedusulancustom">Granted Create Usulan</a>
                                <a href="<?= base_url('situgu/su/setting/grantedverifikasicustom') ?>" class="dropdown-item <?= ($uri->getSegment(2) == "su" && $uri->getSegment(3) == "setting" && $uri->getSegment(4) == "grantedverifikasicustom") ? ' active-menu-href' : '' ?>" key="t-setting-grantedverifikasicustom">Granted Create Verifikasi Usulan</a>
                                <a href="<?= base_url('situgu/su/setting/grantedcreateaduan') ?>" class="dropdown-item <?= ($uri->getSegment(2) == "su" && $uri->getSegment(3) == "setting" && $uri->getSegment(4) == "grantedcreateaduan") ? ' active-menu-href' : '' ?>" key="t-setting-grantedcreateaduan">Granted Access Create Aduan</a>
                                <a href="<?= base_url('situgu/su/setting/granteduploadpangkatkgb') ?>" class="dropdown-item <?= ($uri->getSegment(2) == "su" && $uri->getSegment(3) == "setting" && $uri->getSegment(4) == "granteduploadpangkatkgb") ? ' active-menu-href' : '' ?>" key="t-setting-granteduploadpangkatkgb">Granted Upload Pangkat KGB</a>
                                <a href="<?= base_url('situgu/su/setting/granteduploadspj') ?>" class="dropdown-item <?= ($uri->getSegment(2) == "su" && $uri->getSegment(3) == "setting" && $uri->getSegment(4) == "granteduploadspj") ? ' active-menu-href' : '' ?>" key="t-setting-granteduploadspj">Granted Upload SPJ</a>
                                <a href="<?= base_url('situgu/su/setting/grantedverifikasispj') ?>" class="dropdown-item <?= ($uri->getSegment(2) == "su" && $uri->getSegment(3) == "setting" && $uri->getSegment(4) == "grantedverifikasispj") ? ' active-menu-href' : '' ?>" key="t-setting-grantedverifikasidspj">Granted Verifikasi SPJ</a>
                            </div>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= ($uri->getSegment(2) == "su" && $uri->getSegment(3) == "cs") ? ' active-menu-href' : '' ?>" href="<?= ($uri->getSegment(2) == "su" && $uri->getSegment(3) == "cs") ? 'javascript:;' : base_url('situgu/su/cs') ?>">
                                <i class="bx bx-help-circle me-2"></i><span key="t-dashboards">ADUAN</span>
                            </a>
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