<?php if (isset($data)) { ?>
    <div class="modal-body">
        <div class="row">
            <div class="col-lg-6 align-self-center">
                <div class="text-lg-center mt-4 mt-lg-0">
                    <div class="row">
                        <div class="col-4">
                            <div>
                                <p class="text-muted text-truncate mb-2">Jumlah Data Matching</p>
                                <h5 class="mb-0 text-info result_total" id="result_total">0</h5>
                            </div>
                        </div>
                        <div class="col-4">
                            <div>
                                <p class="text-muted text-truncate mb-2">Jumlah Lolos</p>
                                <h5 class="mb-0 text-success result_lolos" id="result_lolos">0</h5>
                            </div>
                        </div>
                        <div class="col-4">
                            <div>
                                <p class="text-muted text-truncate mb-2">Jumlah Gagal</p>
                                <h5 class="mb-0 text-danger result_gagal" id="result_gagal">0</h5>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 d-none d-lg-block">
                <div class="clearfix mt-4 mt-lg-0">
                    <div class="dropdown float-end">
                        <button class="btn btn-primary" type="button">
                            <i class="mdi mdi-relation-zero-or-many-to-zero-or-many align-middle me-1"></i> Proses Data
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="table-responsive">
                    <table class="table table-bordered border-primary mb-0 modals-datatables-datanya" id="modals-datatables-datanya">
                        <thead>
                            <tr>
                                <th rowspan="2">#</th>
                                <th colspan="6">DATA SIMTUN</th>
                                <th colspan="6">DATA USULAN</th>
                                <th rowspan="2">KETERANGAN</th>
                                <th rowspan="2">AKSI</th>
                            </tr>
                            <tr>
                                <th>NUPTK</th>
                                <th>NAMA</th>
                                <th>GOLONGAN</th>
                                <th>MK</th>
                                <th>GAJI POKOK</th>
                                <th>JJM SESUAI</th>
                                <th>NUPTK</th>
                                <th>NAMA</th>
                                <th>GOLONGAN</th>
                                <th>MK</th>
                                <th>GAJI POKOK</th>
                                <th>KET</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($data) > 0) { ?>
                                <?php foreach ($data as $key => $v) { ?>
                                    <?php if ($v->data_usulan == NULL || $v->data_usulan == "") { ?>
                                        <tr class="table-light">
                                            <th scope="row"><?= $key + 1 ?></th>
                                            <td><?= $v->nuptk ?></td>
                                            <td><?= $v->nama ?></td>
                                            <td><?= $v->golongan_code ?></td>
                                            <td><?= $v->masa_kerja ?></td>
                                            <td><?= $v->gaji_pokok ?></td>
                                            <td><?= $v->total_jjm_sesuai ?></td>
                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                            <td>Belum Mengusulkan</td>
                                            <td>Aksi</td>
                                        </tr>
                                    <?php } else { ?>
                                        <?php $keterangan = "";
                                        if (($v->data_usulan->lampiran_cuti == NULL || $v->data_usulan->lampiran_cuti == "") && ($v->data_usulan->lampiran_pensiun == NULL || $v->data_usulan->lampiran_pensiun == "") && ($v->data_usulan->lampiran_kematian == NULL || $v->data_usulan->lampiran_kematian == "")) {
                                            $keterangan .= "- ";
                                        }

                                        if (!($v->data_usulan->lampiran_cuti == NULL || $v->data_usulan->lampiran_cuti == "")) {
                                            $keterangan .= "Cuti ";
                                        }

                                        if (!($v->data_usulan->lampiran_pensiun == NULL || $v->data_usulan->lampiran_pensiun == "")) {
                                            $keterangan .= "Pensiun ";
                                        }

                                        if (!($v->data_usulan->lampiran_kematian == NULL || $v->data_usulan->lampiran_kematian == "")) {
                                            $keterangan .= "Kematian ";
                                        }
                                        ?>

                                        <?php if ($v->total_jjm_sesuai >= 24 && $v->total_jjm_sesuai <= 40) { ?>

                                            <?php if ($v->golongan == "" && !($v->nip == NULL || $v->nip == "")) { ?>
                                                <?php if ("IX" == $v->data_usulan->us_pang_golongan && $v->masa_kerja == $v->data_usulan->us_pang_mk_tahun && $v->gaji_pokok == $v->data_usulan->us_gaji_pokok) { ?>
                                                    <tr class="table-success">
                                                        <th scope="row"><?= $key + 1 ?></th>
                                                        <td><?= $v->nuptk ?></td>
                                                        <td><?= $v->nama ?></td>
                                                        <td><?= $v->golongan_code ?></td>
                                                        <td><?= $v->masa_kerja ?></td>
                                                        <td><?= $v->gaji_pokok ?></td>
                                                        <td><?= $v->total_jjm_sesuai ?></td>
                                                        <td><?= $v->data_usulan->nuptk ?></td>
                                                        <td><?= $v->data_usulan->nama ?></td>
                                                        <td><?= $v->data_usulan->us_pang_golongan ?></td>
                                                        <td><?= $v->data_usulan->us_pang_mk_tahun ?></td>
                                                        <td><?= $v->data_usulan->us_gaji_pokok ?></td>
                                                        <td><?= $keterangan ?></td>
                                                        <td>Siap Diusulkan SKTP</td>
                                                        <td>Aksi</td>
                                                    </tr>
                                                <?php } else { ?>
                                                    <tr class="table-danger">
                                                        <th scope="row"><?= $key + 1 ?></th>
                                                        <td><?= $v->nuptk ?></td>
                                                        <td><?= $v->nama ?></td>
                                                        <td><?= $v->golongan_code ?></td>
                                                        <td><?= $v->masa_kerja ?></td>
                                                        <td><?= $v->gaji_pokok ?></td>
                                                        <td><?= $v->total_jjm_sesuai ?></td>
                                                        <td><?= $v->data_usulan->nuptk ?></td>
                                                        <td><?= $v->data_usulan->nama ?></td>
                                                        <td><?= $v->data_usulan->us_pang_golongan ?></td>
                                                        <td><?= $v->data_usulan->us_pang_mk_tahun ?></td>
                                                        <td><?= $v->data_usulan->us_gaji_pokok ?></td>
                                                        <td><?= $keterangan ?></td>
                                                        <td>Belum Update Dapodik</td>
                                                        <td>Aksi</td>
                                                    </tr>
                                                <?php } ?>
                                            <?php } else { ?>
                                                <?php if ($v->golongan == $v->data_usulan->us_pang_golongan && $v->masa_kerja == $v->data_usulan->us_pang_mk_tahun && $v->gaji_pokok == $v->data_usulan->us_gaji_pokok) { ?>
                                                    <tr class="table-success">
                                                        <th scope="row"><?= $key + 1 ?></th>
                                                        <td><?= $v->nuptk ?></td>
                                                        <td><?= $v->nama ?></td>
                                                        <td><?= $v->golongan_code ?></td>
                                                        <td><?= $v->masa_kerja ?></td>
                                                        <td><?= $v->gaji_pokok ?></td>
                                                        <td><?= $v->total_jjm_sesuai ?></td>
                                                        <td><?= $v->data_usulan->nuptk ?></td>
                                                        <td><?= $v->data_usulan->nama ?></td>
                                                        <td><?= $v->data_usulan->us_pang_golongan ?></td>
                                                        <td><?= $v->data_usulan->us_pang_mk_tahun ?></td>
                                                        <td><?= $v->data_usulan->us_gaji_pokok ?></td>
                                                        <td><?= $keterangan ?></td>
                                                        <td>Siap Diusulkan SKTP</td>
                                                        <td>Aksi</td>
                                                    </tr>
                                                <?php } else { ?>
                                                    <tr class="table-danger">
                                                        <th scope="row"><?= $key + 1 ?></th>
                                                        <td><?= $v->nuptk ?></td>
                                                        <td><?= $v->nama ?></td>
                                                        <td><?= $v->golongan_code ?></td>
                                                        <td><?= $v->masa_kerja ?></td>
                                                        <td><?= $v->gaji_pokok ?></td>
                                                        <td><?= $v->total_jjm_sesuai ?></td>
                                                        <td><?= $v->data_usulan->nuptk ?></td>
                                                        <td><?= $v->data_usulan->nama ?></td>
                                                        <td><?= $v->data_usulan->us_pang_golongan ?></td>
                                                        <td><?= $v->data_usulan->us_pang_mk_tahun ?></td>
                                                        <td><?= $v->data_usulan->us_gaji_pokok ?></td>
                                                        <td><?= $keterangan ?></td>
                                                        <td>Belum Update Dapodik</td>
                                                        <td>Aksi</td>
                                                    </tr>
                                                <?php } ?>
                                            <?php } ?>
                                        <?php } else { ?>
                                            <tr class="table-danger">
                                                <th scope="row"><?= $key + 1 ?></th>
                                                <td><?= $v->nuptk ?></td>
                                                <td><?= $v->nama ?></td>
                                                <td><?= $v->golongan_code ?></td>
                                                <td><?= $v->masa_kerja ?></td>
                                                <td><?= $v->gaji_pokok ?></td>
                                                <td><?= $v->total_jjm_sesuai ?></td>
                                                <td><?= $v->data_usulan->nuptk ?></td>
                                                <td><?= $v->data_usulan->nama ?></td>
                                                <td><?= $v->data_usulan->us_pang_golongan ?></td>
                                                <td><?= $v->data_usulan->us_pang_mk_tahun ?></td>
                                                <td><?= $v->data_usulan->us_gaji_pokok ?></td>
                                                <td><?= $keterangan ?></td>
                                                <td>Belum Memenuhi Syarat</td>
                                                <td>Aksi</td>
                                            </tr>
                                        <?php } ?>
                                    <?php } ?>
                                <?php } ?>
                            <?php } ?>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <div class="col-8">
            <div>
                <progress id="progressBar" value="0" max="100" style="width:100%; display: none;"></progress>
            </div>
            <div>
                <h3 id="status" style="font-size: 15px; margin: 8px auto;"></h3>
            </div>
            <div>
                <p id="loaded_n_total" style="margin-bottom: 0px;"></p>
            </div>
        </div>
        <button type="button" class="btn btn-secondary waves-effect" data-bs-dismiss="modal">Close</button>
        <!-- <button type="submit" class="btn btn-primary waves-effect waves-light">Simpan</button> -->
    </div>
    </form>

    <script>
        const table = document.getElementById("modals-datatables-datanya");
        const tbody = table.getElementsByTagName("tbody")[0];

        fetch("./get_data_json?id=<?= $id ?>")
            .then(response => response.json())
            .then(data => {
                console.log(data);
                const result_total = document.getElementById("result_total");
                result_total.textContent = data.total.toString();
                const result_lolos = document.getElementById("result_lolos");
                result_lolos.textContent = data.lolos.toString();
                const result_gagal = document.getElementById("result_gagal");
                result_gagal.textContent = data.gagal.toString();
                // const result_total = document.querySelector(".result_total");
                // result_total.textContent = data.total.toString();
                // const result_lolos = document.querySelector(".result_lolos");
                // result_lolos.textContent = data.lolos.toString();
                // const result_gagal = document.querySelector(".result_gagal");
                // result_gagal.textContent = data.gagal.toString();

                for (let i = 0; i < data.data.length; i++) {
                    const row = document.createElement("tr");
                    const numberCell = document.createElement("td");
                    const nuptkCell = document.createElement("td");
                    const namaCell = document.createElement("td");
                    const golonganCodeCell = document.createElement("td");
                    const masaKerjaCell = document.createElement("td");
                    const gajiPokokCell = document.createElement("td");
                    const totalJjmCell = document.createElement("td");
                    const usNuptkCell = document.createElement("td");
                    const usNamaCell = document.createElement("td");
                    const usGolonganCell = document.createElement("td");
                    const usMkCell = document.createElement("td");
                    const usGapokCell = document.createElement("td");
                    const usKetCell = document.createElement("td");
                    const ketCell = document.createElement("td");
                    const aksiCell = document.createElement("td");
                    numberCell.textContent = data.data[i].number;
                    nuptkCell.textContent = data.data[i].nuptk;
                    namaCell.textContent = data.data[i].nama;
                    golonganCodeCell.textContent = data.data[i].golongan_code;
                    masaKerjaCell.textContent = data.data[i].masa_kerja;
                    gajiPokokCell.textContent = data.data[i].gaji_pokok;
                    totalJjmCell.textContent = data.data[i].total_jjm_sesuai;
                    usNuptkCell.textContent = data.data[i].us_nuptk;
                    usNamaCell.textContent = data.data[i].us_nama;
                    usGolonganCell.textContent = data.data[i].us_golongan;
                    usMkCell.textContent = data.data[i].us_masa_kerja;
                    usGapokCell.textContent = data.data[i].us_gaji_pokok;
                    usKetCell.textContent = data.data[i].us_keterangan;
                    ketCell.textContent = data.data[i].keterangan;
                    aksiCell.textContent = data.data[i].aksi;
                    row.appendChild(numberCell);
                    row.appendChild(nuptkCell);
                    row.appendChild(namaCell);
                    row.appendChild(golonganCodeCell);
                    row.appendChild(masaKerjaCell);
                    row.appendChild(gajiPokokCell);
                    row.appendChild(totalJjmCell);
                    row.appendChild(usNuptkCell);
                    row.appendChild(usNamaCell);
                    row.appendChild(usGolonganCell);
                    row.appendChild(usMkCell);
                    row.appendChild(usGapokCell);
                    row.appendChild(usKetCell);
                    row.appendChild(ketCell);
                    row.appendChild(aksiCell);
                    row.classList.add(data.data[i].status);
                    tbody.appendChild(row);
                }
            });
    </script>
<?php } ?>