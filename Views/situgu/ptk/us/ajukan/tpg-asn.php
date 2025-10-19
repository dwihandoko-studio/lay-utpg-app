<?php if (isset($ptk)) { ?>
    <div class="table-responsive">
        <h4>Konfirmasi Pengajuan Usulan Tunjangan Profesi Guru untuk ASN.</h4>
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Variabel</th>
                    <th>Isian</th>
                    <th>Konfirm</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td scope="row" colspan="4">Data Individu</td>
                </tr>
                <tr>
                    <th scope="row">1</th>
                    <td><label class="form-check-label" for="_nama">Nama</label></td>
                    <td><label class="form-check-label" for="_nama"><?= $ptk->nama ?></label></td>
                    <td>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="_nama" onchange="changeChecked()" name="hasil[]">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th scope="row">2</th>
                    <td><label class="form-check-label" for="_nuptk">NUPTK</label></td>
                    <td><label class="form-check-label" for="_nuptk"><?= $ptk->nuptk ?></label></td>
                    <td>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="_nuptk" onchange="changeChecked()" name="hasil[]">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th scope="row">2</th>
                    <td><label class="form-check-label" for="_nrg">NRG</label></td>
                    <td><label class="form-check-label" for="_nrg"><?= $ptk->nrg ?></label></td>
                    <td>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="_nrg" onchange="changeChecked()" name="hasil[]">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th scope="row">2</th>
                    <td><label class="form-check-label" for="_nopes">No Peserta</label></td>
                    <td><label class="form-check-label" for="_nopes"><?= $ptk->no_peserta ?></label></td>
                    <td>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="_nopes" onchange="changeChecked()" name="hasil[]">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th scope="row">3</th>
                    <td><label class="form-check-label" for="_status_kepegawaian">Status Kepegawaian</label></td>
                    <td><label class="form-check-label" for="_status_kepegawaian"><?= $ptk->status_kepegawaian ?></label></td>
                    <td>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="_status_kepegawaian" onchange="changeChecked()" name="hasil[]">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th scope="row">4</th>
                    <td><label class="form-check-label" for="_pendidikan_terakhir">Pendidikan Terakhir</label></td>
                    <td><label class="form-check-label" for="_pendidikan_terakhir"><?= $ptk->pendidikan ?></label></td>
                    <td>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="_pendidikan_terakhir" onchange="changeChecked()" name="hasil[]">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th scope="row">5</th>
                    <td><label class="form-check-label" for="_bidang_studi_sertifikasi">Bidang Studi Sertifikasi</label></td>
                    <td><label class="form-check-label" for="_bidang_studi_sertifikasi"><?= $ptk->bidang_studi_sertifikasi ?></label></td>
                    <td>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="_bidang_studi_sertifikasi" onchange="changeChecked()" name="hasil[]">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th scope="row">5</th>
                    <td><label class="form-check-label" for="_no_rekening">No Rekening</label></td>
                    <td><label class="form-check-label" for="_no_rekening"><?= $ptk->no_rekening ?></label></td>
                    <td>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="_no_rekening" onchange="changeChecked()" name="hasil[]">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th scope="row">6</th>
                    <td><label class="form-check-label" for="_cabang_bank">Cabang Bank</label></td>
                    <td><label class="form-check-label" for="_cabang_bank"><?= $ptk->cabang_bank ?></label></td>
                    <td>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="_cabang_bank" onchange="changeChecked()" name="hasil[]">
                        </div>
                    </td>
                </tr>
                <tr>
                    <td scope="row" colspan="4">Data Kepegawaian</td>
                </tr>
                <tr>
                    <th scope="row">1</th>
                    <td><label class="form-check-label" for="_pang_golongan">Pangkat Golongan</label></td>
                    <td><label class="form-check-label" for="_pang_golongan"><?= $ptk->pang_golongan ?></label></td>
                    <td>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="_pang_golongan" onchange="changeChecked()" name="hasil[]">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th scope="row">2</th>
                    <td><label class="form-check-label" for="_pang_no">No SK</label></td>
                    <td><label class="form-check-label" for="_pang_no"><?= $ptk->pang_no ?></label></td>
                    <td>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="_pang_no" onchange="changeChecked()" name="hasil[]">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th scope="row">3</th>
                    <td><label class="form-check-label" for="_pang_tmt">TMT</label></td>
                    <td><label class="form-check-label" for="_pang_tmt"><?= $ptk->pang_tmt ?></label></td>
                    <td>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="_pang_tmt" onchange="changeChecked()" name="hasil[]">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th scope="row">4</th>
                    <td><label class="form-check-label" for="_pang_tgl">Tanggal SK</label></td>
                    <td><label class="form-check-label" for="_pang_tgl"><?= $ptk->pang_tgl ?></label></td>
                    <td>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="_pang_tgl" onchange="changeChecked()" name="hasil[]">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th scope="row">5</th>
                    <td><label class="form-check-label" for="_pang_mk">Masa Kerja ( Tahun / Bulan )</label></td>
                    <td><label class="form-check-label" for="_pang_mk"><?= $ptk->pang_tahun ?> Tahun / <?= $ptk->pang_bulan ?> Bulan</label></td>
                    <td>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="_pang_mk" onchange="changeChecked()" name="hasil[]">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th scope="row">6</th>
                    <td><label class="form-check-label" for="_gaji_pokok">Gaji Pokok</label></td>
                    <td>
                        <span class="badge rounded-pill badge-soft-dark"><?= $ptk->gajiPokok == NULL || $ptk->gajiPokok == "" ? rpAwalan(2000000) : rpAwalan((int)$ptk->gajiPokok) ?></span>
                    </td>
                    <td>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="_gaji_pokok" onchange="changeChecked()" name="hasil[]">
                        </div>
                    </td>
                </tr>
                <tr>
                    <td scope="row" colspan="4">Data Absensi</td>
                </tr>
                <tr>
                    <th scope="row">1</th>
                    <td><label class="form-check-label" for="_absen_1">Absen 1</label></td>
                    <td><label class="form-check-label" for="_absen_1"><?= $ptk->bulan_1 ?></label></td>
                    <td>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="_absen_1" onchange="changeChecked()" name="hasil[]">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th scope="row">2</th>
                    <td><label class="form-check-label" for="_absen_2">Absen 2</label></td>
                    <td><label class="form-check-label" for="_absen_2"><?= $ptk->bulan_2 ?></label></td>
                    <td>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="_absen_2" onchange="changeChecked()" name="hasil[]">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th scope="row">3</th>
                    <td><label class="form-check-label" for="_absen_3">Absen 3</label></td>
                    <td><label class="form-check-label" for="_absen_3"><?= $ptk->bulan_3 ?></label></td>
                    <td>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="_absen_3" onchange="changeChecked()" name="hasil[]">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th scope="row">4</th>
                    <td><label class="form-check-label" for="_cuti">Cuti</label></td>
                    <td><label class="form-check-label" for="_cuti"><?= $ptk->lampiran_cuti == null || $ptk->lampiran_cuti == "-" ? 'Tidak' : 'Ya' ?></label></td>
                    <td>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="_cuti" onchange="changeChecked()" name="hasil[]">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th scope="row">5</th>
                    <td><label class="form-check-label" for="_pensiun">Pensiun</label></td>
                    <td><label class="form-check-label" for="_pensiun"><?= $ptk->lampiran_pensiun == null || $ptk->lampiran_pensiun == "-" ? 'Tidak' : 'Ya' ?></label></td>
                    <td>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="_pensiun" onchange="changeChecked()" name="hasil[]">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th scope="row">6</th>
                    <td><label class="form-check-label" for="_kematian">Kematian</label></td>
                    <td><label class="form-check-label" for="_kematian"><?= $ptk->lampiran_kematian == null || $ptk->lampiran_kematian == "-" ? 'Tidak' : 'Ya' ?></label></td>
                    <td>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="_kematian" onchange="changeChecked()" name="hasil[]">
                        </div>
                    </td>
                </tr>
                <tr>
                    <td scope="row" colspan="4">Lampiran Dokumen</td>
                </tr>
                <tr>
                    <th scope="row">1</th>
                    <td><label class="form-check-label" for="_lampiran_ktp">KTP</label></td>
                    <td><a class="badge rounded-pill badge-soft-dark" target="popup" onclick="window.open('<?= strpos($ptk->lampiran_ktp, '/') !== false ? getDokumentPreviewStorage('situgu', $ptk->lampiran_ktp) : base_url('upload/ptk/ktp') . '/' . $ptk->lampiran_ktp ?>','popup','width=600,height=600'); return false;" href="<?= strpos($ptk->lampiran_ktp, '/') !== false ? getDokumentPreviewStorage('situgu', $ptk->lampiran_ktp) : base_url('upload/ptk/ktp') . '/' . $ptk->lampiran_ktp ?>">Lihat Lampiran</a></td>
                    <td>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="_lampiran_ktp" onchange="changeChecked()" name="hasil[]">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th scope="row">2</th>
                    <td><label class="form-check-label" for="_lampiran_nuptk">NUPTK</label></td>
                    <td><a class="badge rounded-pill badge-soft-dark" target="popup" onclick="window.open('<?= strpos($ptk->lampiran_nuptk, '/') !== false ? getDokumentPreviewStorage('situgu', $ptk->lampiran_nuptk) : base_url('upload/ptk/nuptk') . '/' . $ptk->lampiran_nuptk ?>','popup','width=600,height=600'); return false;" href="<?= strpos($ptk->lampiran_nuptk, '/') !== false ? getDokumentPreviewStorage('situgu', $ptk->lampiran_nuptk) : base_url('upload/ptk/nuptk') . '/' . $ptk->lampiran_nuptk ?>">Lihat Lampiran</a></td>
                    <td>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="_lampiran_nuptk" onchange="changeChecked()" name="hasil[]">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th scope="row">3</th>
                    <td><label class="form-check-label" for="_lampiran_karpeg">Karpeg</label></td>
                    <td><a class="badge rounded-pill badge-soft-dark" target="popup" onclick="window.open('<?= strpos($ptk->lampiran_karpeg, '/') !== false ? getDokumentPreviewStorage('situgu', $ptk->lampiran_karpeg) : base_url('upload/ptk/karpeg') . '/' . $ptk->lampiran_karpeg ?>','popup','width=600,height=600'); return false;" href="<?= strpos($ptk->lampiran_karpeg, '/') !== false ? getDokumentPreviewStorage('situgu', $ptk->lampiran_karpeg) : base_url('upload/ptk/karpeg') . '/' . $ptk->lampiran_karpeg ?>">Lihat Lampiran</a></td>
                    <td>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="_lampiran_karpeg" onchange="changeChecked()" name="hasil[]">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th scope="row">4</th>
                    <td><label class="form-check-label" for="_lampiran_serdik">Serdik</label></td>
                    <td><a class="badge rounded-pill badge-soft-dark" target="popup" onclick="window.open('<?= strpos($ptk->lampiran_serdik, '/') !== false ? getDokumentPreviewStorage('situgu', $ptk->lampiran_serdik) : base_url('upload/ptk/serdik') . '/' . $ptk->lampiran_serdik ?>','popup','width=600,height=600'); return false;" href="<?= strpos($ptk->lampiran_serdik, '/') !== false ? getDokumentPreviewStorage('situgu', $ptk->lampiran_serdik) : base_url('upload/ptk/serdik') . '/' . $ptk->lampiran_serdik ?>">Lihat Lampiran</a></td>
                    <td>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="_lampiran_serdik" onchange="changeChecked()" name="hasil[]">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th scope="row">5</th>
                    <td><label class="form-check-label" for="_lampiran_npwp">NPWP</label></td>
                    <td><a class="badge rounded-pill badge-soft-dark" target="popup" onclick="window.open('<?= strpos($ptk->lampiran_npwp, '/') !== false ? getDokumentPreviewStorage('situgu', $ptk->lampiran_npwp) : base_url('upload/ptk/npwp') . '/' . $ptk->lampiran_npwp ?>','popup','width=600,height=600'); return false;" href="<?= strpos($ptk->lampiran_npwp, '/') !== false ? getDokumentPreviewStorage('situgu', $ptk->lampiran_npwp) : base_url('upload/ptk/npwp') . '/' . $ptk->lampiran_npwp ?>">Lihat Lampiran</a></td>
                    <td>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="_lampiran_npwp" onchange="changeChecked()" name="hasil[]">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th scope="row">6</th>
                    <td><label class="form-check-label" for="_lampiran_ijazah">Ijazah</label></td>
                    <td><a class="badge rounded-pill badge-soft-dark" target="popup" onclick="window.open('<?= strpos($ptk->lampiran_ijazah, '/') !== false ? getDokumentPreviewStorage('situgu', $ptk->lampiran_ijazah) : base_url('upload/ptk/ijazah') . '/' . $ptk->lampiran_ijazah ?>','popup','width=600,height=600'); return false;" href="<?= strpos($ptk->lampiran_ijazah, '/') !== false ? getDokumentPreviewStorage('situgu', $ptk->lampiran_ijazah) : base_url('upload/ptk/ijazah') . '/' . $ptk->lampiran_ijazah ?>">Lihat Lampiran</a></td>
                    <td>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="_lampiran_ijazah" onchange="changeChecked()" name="hasil[]">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th scope="row">7</th>
                    <td><label class="form-check-label" for="_lampiran_buku_rekening">Buku Rekening</label></td>
                    <td><a class="badge rounded-pill badge-soft-dark" target="popup" onclick="window.open('<?= strpos($ptk->lampiran_buku_rekening, '/') !== false ? getDokumentPreviewStorage('situgu', $ptk->lampiran_buku_rekening) : base_url('upload/ptk/bukurekening') . '/' . $ptk->lampiran_buku_rekening ?>','popup','width=600,height=600'); return false;" href="<?= strpos($ptk->lampiran_buku_rekening, '/') !== false ? getDokumentPreviewStorage('situgu', $ptk->lampiran_buku_rekening) : base_url('upload/ptk/bukurekening') . '/' . $ptk->lampiran_buku_rekening ?>">Lihat Lampiran</a></td>
                    <td>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="_lampiran_buku_rekening" onchange="changeChecked()" name="hasil[]">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th scope="row">8</th>
                    <td><label class="form-check-label" for="_lampiran_absen_1">Absen 1</label></td>
                    <td><a class="badge rounded-pill badge-soft-dark" target="popup" onclick="window.open('<?= strpos($ptk->lampiran_absen1, '/') !== false ? getDokumentPreviewStorage('situgu', $ptk->lampiran_absen1) : base_url('upload/sekolah/kehadiran') . '/' . $ptk->lampiran_absen1 ?>','popup','width=600,height=600'); return false;" href="<?= strpos($ptk->lampiran_absen1, '/') !== false ? getDokumentPreviewStorage('situgu', $ptk->lampiran_absen1) : base_url('upload/sekolah/kehadiran') . '/' . $ptk->lampiran_absen1 ?>">Lihat Lampiran</a></td>
                    <td>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="_lampiran_absen_1" onchange="changeChecked()" name="hasil[]">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th scope="row">9</th>
                    <td><label class="form-check-label" for="_lampiran_absen_2">Absen 2</label></td>
                    <td><a class="badge rounded-pill badge-soft-dark" target="popup" onclick="window.open('<?= strpos($ptk->lampiran_absen2, '/') !== false ? getDokumentPreviewStorage('situgu', $ptk->lampiran_absen2) : base_url('upload/sekolah/kehadiran') . '/' . $ptk->lampiran_absen2 ?>','popup','width=600,height=600'); return false;" href="<?= strpos($ptk->lampiran_absen2, '/') !== false ? getDokumentPreviewStorage('situgu', $ptk->lampiran_absen2) : base_url('upload/sekolah/kehadiran') . '/' . $ptk->lampiran_absen2 ?>">Lihat Lampiran</a></td>
                    <td>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="_lampiran_absen_2" onchange="changeChecked()" name="hasil[]">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th scope="row">10</th>
                    <td><label class="form-check-label" for="_lampiran_absen_3">Absen 3</label></td>
                    <td><a class="badge rounded-pill badge-soft-dark" target="popup" onclick="window.open('<?= strpos($ptk->lampiran_absen3, '/') !== false ? getDokumentPreviewStorage('situgu', $ptk->lampiran_absen3) : base_url('upload/sekolah/kehadiran') . '/' . $ptk->lampiran_absen3 ?>','popup','width=600,height=600'); return false;" href="<?= strpos($ptk->lampiran_absen3, '/') !== false ? getDokumentPreviewStorage('situgu', $ptk->lampiran_absen3) : base_url('upload/sekolah/kehadiran') . '/' . $ptk->lampiran_absen3 ?>">Lihat Lampiran</a></td>
                    <td>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="_lampiran_absen_3" onchange="changeChecked()" name="hasil[]">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th scope="row">11</th>
                    <td><label class="form-check-label" for="_lampiran_pembagian_tugas">Pembagian Tugas</label></td>
                    <td><a class="badge rounded-pill badge-soft-dark" target="popup" onclick="window.open('<?= strpos($ptk->lampiran_pembagian_tugas, '/') !== false ? getDokumentPreviewStorage('situgu', $ptk->lampiran_pembagian_tugas) : base_url('upload/sekolah/pembagian-tugas') . '/' . $ptk->lampiran_pembagian_tugas ?>','popup','width=600,height=600'); return false;" href="<?= strpos($ptk->lampiran_pembagian_tugas, '/') !== false ? getDokumentPreviewStorage('situgu', $ptk->lampiran_pembagian_tugas) : base_url('upload/sekolah/pembagian-tugas') . '/' . $ptk->lampiran_pembagian_tugas ?>">Lihat Lampiran</a></td>
                    <td>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="_lampiran_pembagian_tugas" onchange="changeChecked()" name="hasil[]">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th scope="row">12</th>
                    <td><label class="form-check-label" for="_lampiran_slip_gaji">Slip Gaji</label></td>
                    <td><a class="badge rounded-pill badge-soft-dark" target="popup" onclick="window.open('<?= strpos($ptk->lampiran_slip_gaji, '/') !== false ? getDokumentPreviewStorage('situgu', $ptk->lampiran_slip_gaji) : base_url('upload/sekolah/slip-gaji') . '/' . $ptk->lampiran_slip_gaji ?>','popup','width=600,height=600'); return false;" href="<?= strpos($ptk->lampiran_slip_gaji, '/') !== false ? getDokumentPreviewStorage('situgu', $ptk->lampiran_slip_gaji) : base_url('upload/sekolah/slip-gaji') . '/' . $ptk->lampiran_slip_gaji ?>">Lihat Lampiran</a></td>
                    <td>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="_lampiran_slip_gaji" onchange="changeChecked()" name="hasil[]">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th scope="row">13</th>
                    <td><label class="form-check-label" for="_lampiran_pangkat">Pangkat</label></td>
                    <td><a class="badge rounded-pill badge-soft-dark" target="popup" onclick="window.open('<?= strpos($ptk->lampiran_pangkat, '/') !== false ? getDokumentPreviewStorage('situgu', $ptk->lampiran_pangkat) : base_url('upload/ptk/pangkat') . '/' . $ptk->lampiran_pangkat ?>','popup','width=600,height=600'); return false;" href="<?= strpos($ptk->lampiran_pangkat, '/') !== false ? getDokumentPreviewStorage('situgu', $ptk->lampiran_pangkat) : base_url('upload/ptk/pangkat') . '/' . $ptk->lampiran_pangkat ?>">Lihat Lampiran</a></td>
                    <td>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="_lampiran_pangkat" onchange="changeChecked()" name="hasil[]">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th scope="row">14</th>
                    <td><label class="form-check-label" for="_lampiran_kgb">KGB (Berkala)</label></td>
                    <td><a class="badge rounded-pill badge-soft-dark" target="popup" onclick="window.open('<?= strpos($ptk->lampiran_kgb, '/') !== false ? getDokumentPreviewStorage('situgu', $ptk->lampiran_kgb) : base_url('upload/ptk/kgb') . '/' . $ptk->lampiran_kgb ?>','popup','width=600,height=600'); return false;" href="<?= strpos($ptk->lampiran_kgb, '/') !== false ? getDokumentPreviewStorage('situgu', $ptk->lampiran_kgb) : base_url('upload/ptk/kgb') . '/' . $ptk->lampiran_kgb ?>">Lihat Lampiran</a></td>
                    <td>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="_lampiran_kgb" onchange="changeChecked()" name="hasil[]">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th scope="row">15</th>
                    <td><label class="form-check-label" for="_lampiran_pernyataan24">Pernyataan 24Jam</label></td>
                    <td><a class="badge rounded-pill badge-soft-dark" target="popup" onclick="window.open('<?= strpos($ptk->lampiran_pernyataan24, '/') !== false ? getDokumentPreviewStorage('situgu', $ptk->lampiran_pernyataan24) : base_url('upload/ptk/pernyataanindividu') . '/' . $ptk->lampiran_pernyataan24 ?>','popup','width=600,height=600'); return false;" href="<?= strpos($ptk->lampiran_pernyataan24, '/') !== false ? getDokumentPreviewStorage('situgu', $ptk->lampiran_pernyataan24) : base_url('upload/ptk/pernyataanindividu') . '/' . $ptk->lampiran_pernyataan24 ?>">Lihat Lampiran</a></td>
                    <td>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="_lampiran_pernyataan24" onchange="changeChecked()" name="hasil[]">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th scope="row">16</th>
                    <td><label class="form-check-label" for="_lampiran_cuti">Cuti</label></td>
                    <td>
                        <?php if ($ptk->lampiran_cuti == null || $ptk->lampiran_cuti == "") {
                            echo 'Tidak ada';
                        } else { ?>
                            <a class="badge rounded-pill badge-soft-dark" target="popup" onclick="window.open('<?= strpos($ptk->lampiran_cuti, '/') !== false ? getDokumentPreviewStorage('situgu', $ptk->lampiran_cuti) : base_url('upload/ptk/keterangancuti') . '/' . $ptk->lampiran_cuti ?>','popup','width=600,height=600'); return false;" href="<?= strpos($ptk->lampiran_cuti, '/') !== false ? getDokumentPreviewStorage('situgu', $ptk->lampiran_cuti) : base_url('upload/ptk/keterangancuti') . '/' . $ptk->lampiran_cuti ?>">Lihat Lampiran</a>
                        <?php } ?>
                    </td>
                    <td>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="_lampiran_cuti" onchange="changeChecked()" name="hasil[]">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th scope="row">17</th>
                    <td><label class="form-check-label" for="_lampiran_pensiun">Pensiun</label></td>
                    <td>
                        <?php if ($ptk->lampiran_pensiun == null || $ptk->lampiran_pensiun == "") {
                            echo 'Tidak ada';
                        } else { ?>
                            <a class="badge rounded-pill badge-soft-dark" target="popup" onclick="window.open('<?= strpos($ptk->lampiran_pensiun, '/') !== false ? getDokumentPreviewStorage('situgu', $ptk->lampiran_pensiun) : base_url('upload/ptk/pensiun') . '/' . $ptk->lampiran_pensiun ?>','popup','width=600,height=600'); return false;" href="<?= strpos($ptk->lampiran_pensiun, '/') !== false ? getDokumentPreviewStorage('situgu', $ptk->lampiran_pensiun) : base_url('upload/ptk/pensiun') . '/' . $ptk->lampiran_pensiun ?>">Lihat Lampiran</a>
                        <?php } ?>
                    </td>
                    <td>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="_lampiran_pensiun" onchange="changeChecked()" name="hasil[]">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th scope="row">18</th>
                    <td><label class="form-check-label" for="_lampiran_kematian">Kematian</label></td>
                    <td>
                        <?php if ($ptk->lampiran_kematian == null || $ptk->lampiran_kematian == "") {
                            echo 'Tidak ada';
                        } else { ?>
                            <a class="badge rounded-pill badge-soft-dark" target="popup" onclick="window.open('<?= strpos($ptk->lampiran_kematian, '/') !== false ? getDokumentPreviewStorage('situgu', $ptk->lampiran_kematian) : base_url('upload/ptk/kematian') . '/' . $ptk->lampiran_kematian ?>','popup','width=600,height=600'); return false;" href="<?= strpos($ptk->lampiran_kematian, '/') !== false ? getDokumentPreviewStorage('situgu', $ptk->lampiran_kematian) : base_url('upload/ptk/kematian') . '/' . $ptk->lampiran_kematian ?>">Lihat Lampiran</a>
                        <?php } ?>
                    </td>
                    <td>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="_lampiran_kematian" onchange="changeChecked()" name="hasil[]">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th scope="row">19</th>
                    <td><label class="form-check-label" for="_lampiran_atribut_lainnya">Atribut Lainnya</label></td>
                    <td>
                        <?php if ($ptk->lampiran_att_lain == null || $ptk->lampiran_att_lain == "") {
                            echo 'Tidak ada';
                        } else { ?>
                            <a class="badge rounded-pill badge-soft-dark" target="popup" onclick="window.open('<?= strpos($ptk->lampiran_attr_lain, '/') !== false ? getDokumentPreviewStorage('situgu', $ptk->lampiran_attr_lain) : base_url('upload/ptk/lainnya') . '/' . $ptk->lampiran_attr_lain ?>','popup','width=600,height=600'); return false;" href="<?= strpos($ptk->lampiran_attr_lain, '/') !== false ? getDokumentPreviewStorage('situgu', $ptk->lampiran_attr_lain) : base_url('upload/ptk/lainnya') . '/' . $ptk->lampiran_attr_lain ?>">Lihat Lampiran</a>
                        <?php } ?>
                    </td>
                    <td>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="_lampiran_atribut_lainnya" onchange="changeChecked()" name="hasil[]">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th scope="row">20</th>
                    <td><label class="form-check-label" for="_lampiran_igd">Info GTK Digital</label></td>
                    <td>
                        <?php if (isset($igd)) { ?>
                            <?php if ($igd) { ?>
                                <a class="badge rounded-pill badge-soft-dark" target="popup" onclick="window.open('<?= $igd->qrcode ?>','popup','width=600,height=600'); return false;" href="<?= $igd->qrcode ?>">Lihat Info GTK Digital</a>
                            <?php } else { ?>
                                Tidak ada
                            <?php } ?>
                        <?php } else { ?>
                            Tidak ada
                        <?php } ?>
                    </td>
                    <td>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="_lampiran_igd" onchange="changeChecked()" name="hasil[]">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th scope="row">21</th>
                    <td><label class="form-check-label" for="_lampiran_absen_lain">Lainnya</label></td>
                    <td>
                        <?php if ($ptk->lampiran_doc_absen_lain == null || $ptk->lampiran_doc_absen_lain == "") {
                            echo 'Tidak ada';
                        } else { ?>
                            <a class="badge rounded-pill badge-soft-dark" target="popup" onclick="window.open('<?= strpos($ptk->lampiran_doc_absen_lain, '/') !== false ? getDokumentPreviewStorage('situgu', $ptk->lampiran_doc_absen_lain) : base_url('upload/sekolah/doc-lainnya') . '/' . $ptk->lampiran_doc_absen_lain ?>','popup','width=600,height=600'); return false;" href="<?= strpos($ptk->lampiran_doc_absen_lain, '/') !== false ? getDokumentPreviewStorage('situgu', $ptk->lampiran_doc_absen_lain) : base_url('upload/sekolah/doc-lainnya') . '/' . $ptk->lampiran_doc_absen_lain ?>">Lihat Lampiran</a>
                        <?php } ?>
                    </td>
                    <td>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="_lampiran_absen_lain" onchange="changeChecked()" name="hasil[]">
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <script>
        function changeChecked() {
            let checkedCount = document.getElementsByName('hasil[]');
            for (let i = 0, n = checkedCount.length; i < n; i++) {
                if (!checkedCount[i].checked) {
                    $('.submit-button-ajuan').attr('disabled', true);
                    return;
                }
            }

            $('.submit-button-ajuan').attr('disabled', false);
        }

        $("#formEditModalData").on("submit", function(e) {
            e.preventDefault();
            const id = document.getElementsByName('_id')[0].value;
            const jenis = document.getElementsByName('_jenis')[0].value;
            const tw = document.getElementsByName('_tw')[0].value;

            Swal.fire({
                title: 'Apakah anda yakin ingin mengajukan Usulan Tunjangan Penghasilan Guru (TPG)?',
                text: "Usulkan Tunjangan TPG: <?= $ptk->nama ?>",
                showCancelButton: true,
                icon: 'question',
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Usulkan!',
                cancelButtonText: 'Tidak',
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        url: "./prosesajukan",
                        type: 'POST',
                        data: {
                            id: id,
                            jenis: jenis,
                            tw: tw,
                        },
                        dataType: 'JSON',
                        beforeSend: function() {
                            $('.submit-button-ajuan').attr('disabled', true);
                            $('div.modal-content-loading').block({
                                message: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>'
                            });
                        },
                        success: function(resul) {
                            $('div.modal-content-loading').unblock();

                            if (resul.status !== 200) {
                                if (resul.status !== 201) {
                                    if (resul.status === 401) {
                                        Swal.fire(
                                            'Failed!',
                                            resul.message,
                                            'warning'
                                        ).then((valRes) => {
                                            reloadPage();
                                        });
                                    } else {
                                        $('.submit-button-ajuan').attr('disabled', false);
                                        Swal.fire(
                                            'GAGAL!',
                                            resul.message,
                                            'warning'
                                        );
                                    }
                                } else {
                                    Swal.fire(
                                        'Peringatan!',
                                        resul.message,
                                        'success'
                                    ).then((valRes) => {
                                        reloadPage();
                                    })
                                }
                            } else {
                                Swal.fire(
                                    'SELAMAT!',
                                    resul.message,
                                    'success'
                                ).then((valRes) => {
                                    reloadPage();
                                })
                            }
                        },
                        error: function() {
                            $('.submit-button-ajuan').attr('disabled', true);
                            $('div.modal-content-loading').unblock();
                            Swal.fire(
                                'PERINGATAN!',
                                "Server sedang sibuk, silahkan ulangi beberapa saat lagi.",
                                'warning'
                            );
                        }
                    });
                }
            })
        });
    </script>
<?php } ?>