<?php if (isset($data)) { ?>
    <div class="modal-body">
        <div class="row">
            <h2>DATA INDIVIDU</h2>
            <div class="col-lg-6">
                <label class="col-form-label">Nama Lengkap:</label>
                <input type="text" class="form-control" value="<?= $data->nama ?>" readonly />
            </div>
            <div class="col-lg-6">
                <label class="col-form-label">NIK:</label>
                <input type="text" class="form-control" value="<?= $data->nik ?>" readonly />
            </div>
            <div class="col-lg-6">
                <label class="col-form-label">NUPTK:</label>
                <input type="text" class="form-control" value="<?= $data->nuptk ?>" readonly />
            </div>
            <div class="col-lg-6">
                <label class="col-form-label">NIP:</label>
                <input type="text" class="form-control" value="<?= $data->nip ?>" readonly />
            </div>
            <div class="col-lg-6">
                <label class="col-form-label">NRG:</label>
                <input type="text" class="form-control" value="<?= $data->nrg ?>" readonly />
            </div>
            <div class="col-lg-6">
                <label class="col-form-label">No Peserta:</label>
                <input type="text" class="form-control" value="<?= $data->no_peserta ?>" readonly />
            </div>
            <div class="col-lg-6">
                <label class="col-form-label">NPWP:</label>
                <input type="text" class="form-control" value="<?= $data->npwp ?>" readonly />
            </div>
            <div class="col-lg-6">
                <label class="col-form-label">No Rekening:</label>
                <input type="text" class="form-control" value="<?= $data->no_rekening ?>" readonly />
            </div>
            <div class="col-lg-6">
                <label class="col-form-label">Cabang Bank:</label>
                <input type="text" class="form-control" value="<?= $data->cabang_bank ?>" readonly />
            </div>
            <div class="col-lg-6">
                <label class="col-form-label">Tempat Lahir:</label>
                <input type="text" class="form-control" value="<?= $data->tempat_lahir ?>" readonly />
            </div>
            <div class="col-lg-6">
                <label class="col-form-label">Tanggal Lahir:</label>
                <input type="text" class="form-control" value="<?= $data->tgl_lahir ?>" readonly />
            </div>
            <div class="col-lg-6">
                <label class="col-form-label">Jenis Kelamin:</label>
                <div><?php switch ($data->jenis_kelamin) {
                            case 'P':
                                echo '<span class="badge badge-pill badge-soft-primary">Perempuan</span>';
                                break;
                            case 'L':
                                echo '<span class="badge badge-pill badge-soft-primary">Laki-Laki</span>';
                                break;
                            default:
                                echo '-';
                                break;
                        } ?>
                </div>
            </div>
            <div class="col-lg-6">
                <label class="col-form-label">Email Dapodik:</label>
                <input type="text" class="form-control" value="<?= $data->email ?>" readonly />
            </div>
            <div class="col-lg-6">
                <label class="col-form-label">Email Akun:</label>
                <input type="text" class="form-control" value="<?= $data->emailAkun ?>" readonly />
            </div>
            <div class="col-lg-6">
                <label class="col-form-label">No Hanphone Dapodik:</label>
                <input type="text" class="form-control" value="<?= $data->no_hp ?>" readonly />
            </div>
            <div class="col-lg-6">
                <label class="col-form-label">No Hanphone Akun:</label>
                <input type="text" class="form-control" value="<?= $data->nohpAkun ?>" readonly />
                <?php switch ((int)$data->wa_verified) {
                    case 1:
                        echo '<span class="badge badge-pill badge-soft-success">WA Terverifikasi</span>';
                        break;
                    default:
                        echo '<span class="badge badge-pill badge-soft-danger">WA Tidak Tertaut</span>';
                        break;
                } ?>
            </div>
            <div class="col-lg-4 mt-4">

                <?php if ($data->image !== null) { ?>
                    <img style="max-width: 70px; max-height: 80px;" class="imagePreviewUpload" src="<?= base_url('upload/user') . '/' . $data->image ?>" id="imagePreviewUpload" />
                <?php } ?>

            </div>
        </div>
        <div class="row mt-4">
            <h2>DATA PENUGASAN</h2>

            <?php switch ($data->bidang_studi_sertifikasi) {
                case '':
                    echo '<div class="col-lg-6">
                            <label class="col-form-label">Status Sertifikasi:</label>
                            <div><span class="badge badge-pill badge-soft-danger">Belum</span></div>
                        </div>';
                    break;
                case null:
                    echo '<div class="col-lg-6">
                            <label class="col-form-label">Status Sertifikasi:</label>
                            <div><span class="badge badge-pill badge-soft-danger">Belum</span></div>
                        </div>';
                    break;
                case '-':
                    echo '<div class="col-lg-6">
                            <label class="col-form-label">Status Sertifikasi:</label>
                            <div><span class="badge badge-pill badge-soft-danger">Belum</span></div>
                        </div>';
                    break;
                case ' ':
                    echo '<div class="col-lg-6">
                            <label class="col-form-label">Status Sertifikasi:</label>
                            <div><span class="badge badge-pill badge-soft-danger">Belum</span></div>
                        </div>';
                    break;

                default:
                    echo '<div class="col-lg-6">
                        <label class="col-form-label">Status Sertifikasi:</label>
                        <div><span class="badge badge-pill badge-soft-success">Sudah</span></div>
                    </div>
                    <div class="col-lg-6">
                        <label class="col-form-label">Bidang Studi Sertifikasi:</label>
                        <input type="text" class="form-control" value="' . $data->bidang_studi_sertifikasi . '" readonly />
                    </div>';
                    break;
            } ?>
            <div class="col-lg-12 mt-4">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>NPSN</th>
                                <th>Satuan Pendidikan</th>
                                <th>Nomor Surat Tugas</th>
                                <th>Tanggal Surat</th>
                                <th>Status</th>
                                <th>Jumlah Jam</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (isset($penugasans)) {
                                if (count($penugasans) > 0) {
                                    foreach ($penugasans as $key => $v) { ?>
                                        <tr>
                                            <th scope="row"><?= $key + 1 ?></th>
                                            <td><?= $v->npsn ?></td>
                                            <td><?= $v->namaSekolah ?></td>
                                            <td><?= $v->nomor_surat_tugas ?></td>
                                            <td><?= $v->tanggal_surat_tugas ?></td>
                                            <td><?= $v->ptk_induk == "1" ? '<span class="badge badge-pill badge-soft-success">INDUK</span>' : '<span class="badge badge-pill badge-soft-warning">NON INDUK</span>' ?></td>
                                            <td><?= $v->jumlah_total_jam_mengajar_perminggu == NULL ? ($v->jenis_ptk == 'Kepala Sekolah' && $v->status_keaktifan == 'Aktif' && $v->jenis_keluar == NULL && $v->ptk_induk == '1' ? '24' : $v->jumlah_total_jam_mengajar_perminggu) : $v->jumlah_total_jam_mengajar_perminggu ?> Jam</td>
                                        </tr>
                                    <?php }
                                } else { ?>
                                    <tr>
                                        <td colspan="6">Tidak ada penugasan</td>
                                    </tr>
                                <?php }
                            } else { ?>
                                <tr>
                                    <td colspan="6">Tidak ada penugasan</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-lg-6">
                <label class="col-form-label">NPSN:</label>
                <div><?= $data->npsn ?></div>
            </div>
            <div class="col-lg-6">
                <label class="col-form-label">Tempat Tugas:</label>
                <div><?= $data->tempat_tugas ?></div>
            </div>
            <div class="col-lg-6">
                <label class="col-form-label">Status Tugas:</label>
                <div><?= $data->status_tugas ?></div>
            </div>
            <div class="col-lg-6">
                <label class="col-form-label">Kecamatan:</label>
                <div><?= $data->kecamatan_sekolah ?></div>
            </div>
            <div class="col-lg-6">
                <label class="col-form-label">Status PTK:</label>
                <div><?= $data->status_kepegawaian ?></div>
            </div>
            <div class="col-lg-6">
                <label class="col-form-label">Mapel Diajarkan:</label>
                <div><?= $data->mapel_diajarkan ?></div>
            </div>
            <div class="col-lg-6">
                <label class="col-form-label">Pendidikan Terakhir:</label>
                <div><?= $data->pendidikan ?></div>
            </div>
            <div class="col-lg-6">
                <label class="col-form-label">Bidang Studi Pendidikan:</label>
                <div><?= $data->bidang_studi_pendidikan ?></div>
            </div>
            <div class="col-lg-6">
                <label class="col-form-label">SK Pengangkatan:</label>
                <input type="text" class="form-control" value="<?= $data->sk_pengangkatan ?>" readonly />
            </div>
            <div class="col-lg-6">
                <label class="col-form-label">TMT Pengangkatan:</label>
                <input type="text" class="form-control" value="<?php switch ($data->tmt_pengangkatan) {
                                                                    case '':
                                                                        echo '';
                                                                        break;
                                                                    case '-':
                                                                        echo '';
                                                                        break;
                                                                    case NULL:
                                                                        echo '';
                                                                        break;
                                                                    case '1900-01-01':
                                                                        echo '';
                                                                        break;

                                                                    default:
                                                                        echo $data->tmt_pengangkatan;
                                                                        break;
                                                                } ?>" readonly />
            </div>
            <div class="col-lg-6">
                <label class="col-form-label">SK CPNS:</label>
                <input type="text" class="form-control" value="<?= $data->sk_cpns ?>" readonly />
            </div>
            <div class="col-lg-6">
                <label class="col-form-label">Tanggal CPNS:</label>
                <input type="text" class="form-control" value="<?php switch ($data->tgl_cpns) {
                                                                    case '':
                                                                        echo '';
                                                                        break;
                                                                    case '-':
                                                                        echo '';
                                                                        break;
                                                                    case NULL:
                                                                        echo '';
                                                                        break;
                                                                    case '1900-01-01':
                                                                        echo '';
                                                                        break;

                                                                    default:
                                                                        echo $data->tgl_cpns;
                                                                        break;
                                                                } ?>" readonly />
            </div>
        </div>
        <div class="row mt-4">
            <h2>DATA KEPEGAWAIAN</h2>
            <div class="col-lg-6">
                <label class="col-form-label">Jenis Pangkat/KGB:</label>
                <div><?= ($data->tmt_sk_kgb > $data->tmt_pangkat) ? 'KGB' : 'PANGKAT' ?></div>
            </div>
            <div class="col-lg-6">
                <label class="col-form-label">Pangkat/Golongan:</label>
                <div><?= $data->pangkat_golongan ?></div>
            </div>
            <div class="col-lg-6">
                <label class="col-form-label">Nomor SK:</label>
                <div><?= ($data->tmt_sk_kgb > $data->tmt_pangkat) ? $data->sk_kgb : $data->nomor_sk_pangkat ?></div>
            </div>
            <div class="col-lg-6">
                <label class="col-form-label">Tanggal SK:</label>
                <div><?= ($data->tmt_sk_kgb > $data->tmt_pangkat) ? $data->tgl_sk_kgb : $data->tgl_sk_pangkat ?></div>
            </div>
            <div class="col-lg-6">
                <label class="col-form-label">TMT:</label>
                <div><?= ($data->tmt_sk_kgb > $data->tmt_pangkat) ? $data->tmt_sk_kgb : $data->tmt_pangkat ?></div>
            </div>
            <div class="col-lg-3">
                <label class="col-form-label">Masa Kerja Tahun:</label>
                <div><?= ($data->tmt_sk_kgb > $data->tmt_pangkat) ? ($data->masa_kerja_tahun_kgb !== null ? $data->masa_kerja_tahun_kgb : 0) : ($data->masa_kerja_tahun !== null ? $data->masa_kerja_tahun : 0) ?></div>
            </div>
            <div class="col-lg-3">
                <label class="col-form-label">Masa Kerja Bulan:</label>
                <div><?= ($data->tmt_sk_kgb > $data->tmt_pangkat) ? ($data->masa_kerja_bulan_kgb !== null ? $data->masa_kerja_bulan_kgb : 0) : ($data->masa_kerja_bulan !== null ? $data->masa_kerja_bulan : 0) ?></div>
            </div>
            <div class="col-lg-3">
                <label class="col-form-label">Gaji Pokok:</label>
                <div><?= getGajiPokokByPangkatMkt($data->pangkat_golongan, ($data->tmt_sk_kgb > $data->tmt_pangkat) ? ($data->masa_kerja_bulan_kgb !== null ? $data->masa_kerja_bulan_kgb : 0) : ($data->masa_kerja_bulan !== null ? $data->masa_kerja_bulan : 0)) ?></div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary waves-effect" data-bs-dismiss="modal">Close</button>
    </div>
<?php } ?>