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
                <label class="col-form-label">Tempat Lahir:</label>
                <input type="text" class="form-control" value="<?= $data->tempat_lahir ?>" readonly />
            </div>
            <div class="col-lg-6">
                <label class="col-form-label">Tanggal Lahir:</label>
                <input type="text" class="form-control" value="<?= $data->tanggal_lahir ?>" readonly />
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
                <label class="col-form-label">AGAMA:</label>
                <input type="text" class="form-control" value="<?= $data->agama ?>" readonly />
            </div>
            <div class="col-lg-6">
                <label class="col-form-label">Jenis PTK:</label>
                <input type="text" class="form-control" value="<?= $data->jenis_ptk ?>" readonly />
            </div>
            <div class="col-lg-6">
                <label class="col-form-label">Jabatan PTK:</label>
                <input type="text" class="form-control" value="<?= $data->jabatan_ptk ?>" readonly />
            </div>
            <div class="col-lg-6">
                <label class="col-form-label">Status Kepegawaian:</label>
                <input type="text" class="form-control" value="<?= $data->status_kepegawaian ?>" readonly />
            </div>
            <div class="col-lg-6">
                <label class="col-form-label">Pendidikan Terakhir:</label>
                <input type="text" class="form-control" value="<?= $data->pendidikan_terakhir ?>" readonly />
            </div>
            <div class="col-lg-6">
                <label class="col-form-label">Bidang Studi Terakhir:</label>
                <input type="text" class="form-control" value="<?= $data->bidang_studi_terakhir ?>" readonly />
            </div>
            <div class="col-lg-6">
                <label class="col-form-label">Pangkat Gol Terakhir:</label>
                <input type="text" class="form-control" value="<?= $data->pangkat_golongan_terakhir ?>" readonly />
            </div>
        </div>
        <div class="row mt-4">
            <h2>RIWAYAT KEPANGKATAN</h2>
            <div class="col-lg-12 mt-4">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Pangkat Golongan</th>
                                <th>Nomor SK</th>
                                <th>Tanggal SK</th>
                                <th>TMT SK</th>
                                <th>MK Tahun</th>
                                <th>MK Bulan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (isset($pangkats)) {
                                if (count($pangkats) > 0) {
                                    foreach ($pangkats as $key => $v) { ?>
                                        <tr>
                                            <th scope="row"><?= $key + 1 ?></th>
                                            <td><?= $v->pangkat_golongan ?></td>
                                            <td><?= $v->nomor_sk ?></td>
                                            <td><?= $v->tanggal_sk ?></td>
                                            <td><?= $v->tmt_pangkat ?></td>
                                            <td><?= $v->masa_kerja_gol_tahun ?></td>
                                            <td><?= $v->masa_kerja_gol_bulan ?></td>
                                        </tr>
                                    <?php }
                                } else { ?>
                                    <tr>
                                        <td colspan="7">Tidak ada riwayat kepangkatan</td>
                                    </tr>
                                <?php }
                            } else { ?>
                                <tr>
                                    <td colspan="7">Tidak ada riwayat kepangkatan</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <h2>RIWAYAT PENDIDIKAN</h2>
            <div class="col-lg-12 mt-4">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Jenjang Pendidikan</th>
                                <th>Satuan Pendidikan Formal</th>
                                <th>Tahun Lulus</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (isset($pendidikans)) {
                                if (count($pendidikans) > 0) {
                                    foreach ($pendidikans as $key => $v) { ?>
                                        <tr>
                                            <th scope="row"><?= $key + 1 ?></th>
                                            <td><?= $v->jenjang_pendidikan ?></td>
                                            <td><?= $v->satuan_pendidikan_formal ?></td>
                                            <td><?= $v->tahun_lulus ?></td>
                                        </tr>
                                    <?php }
                                } else { ?>
                                    <tr>
                                        <td colspan="4">Tidak ada riwayat pendidikan</td>
                                    </tr>
                                <?php }
                            } else { ?>
                                <tr>
                                    <td colspan="4">Tidak ada riwayat pendidikan</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <h2>PEMBELAJARAN</h2>
            <div class="col-lg-12 mt-4">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama Mata Pelajaran</th>
                                <th>Jam Mengajar Perminggu</th>
                                <th>Status Di Kurikulum</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (isset($pembelajarans)) {
                                if (count($pembelajarans) > 0) {
                                    foreach ($pembelajarans as $key => $v) { ?>
                                        <tr>
                                            <th scope="row"><?= $key + 1 ?></th>
                                            <td><?= $v->nama_mata_pelajaran ?></td>
                                            <td><?= $v->jam_mengajar_per_minggu ?></td>
                                            <td><?= $v->status_di_kurikulum ?></td>
                                        </tr>
                                    <?php }
                                } else { ?>
                                    <tr>
                                        <td colspan="4">Tidak ada pembelajaran</td>
                                    </tr>
                                <?php }
                            } else { ?>
                                <tr>
                                    <td colspan="4">Tidak ada pembelajaran</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary waves-effect" data-bs-dismiss="modal">Close</button>
    </div>
<?php } ?>