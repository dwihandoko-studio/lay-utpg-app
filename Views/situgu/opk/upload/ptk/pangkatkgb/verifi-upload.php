<?php if (isset($data)) { ?>
    <div class="modal-body">
        <div class="row">
            <div class="col-lg-12 mb-4">
                <div class="row">
                    <div class="col-lg-10 align-self-center">
                        <div class="text-lg-center mt-4 mt-lg-0">
                            <div class="row">
                                <div class="col-3">
                                    <div>
                                        <p class="text-muted text-truncate mb-2">Jumlah Data Upload Pangkat KGB</p>
                                        <h5 class="mb-0 text-info result_total" id="result_total"><i class="mdi mdi-reload mdi-spin"></i></h5>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div>
                                        <p class="text-muted text-truncate mb-2">Jumlah PTK Terdeteksi</p>
                                        <h5 class="mb-0 text-success result_lolos" id="result_lolos"><i class="mdi mdi-reload mdi-spin"></i></h5>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div>
                                        <p class="text-muted text-truncate mb-2">Jumlah PTK Tidak Terdeteksi</p>
                                        <h5 class="mb-0 text-danger result_gagal" id="result_gagal"><i class="mdi mdi-reload mdi-spin"></i></h5>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 d-none d-lg-block">
                        <div class="clearfix mt-4 mt-lg-0">
                            <div class="dropdown float-end">
                                <button class="btn btn-primary button_aksi_matching" id="button_aksi_matching" type="button" onclick="aksiMatching()">
                                    <i class="mdi mdi-relation-zero-or-many-to-zero-or-many align-middle me-1"></i> Proses Data
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12 d-none d-lg-block mb-2 mt-2">
                        <div>
                            <progress id="progressBar" value="0" max="100" style="width:100%; display: none;"></progress>
                        </div>
                        <div>
                            <h3 id="status" style="font-size: 12px; margin: 8px auto;"></h3>
                        </div>
                        <div>
                            <p id="loaded_n_total" style="margin-bottom: 0px;"></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="table-responsive">
                    <table class="table table-bordered border-primary mb-0 modals-datatables-datanya" id="modals-datatables-datanya">
                        <thead>
                            <tr>
                                <th rowspan="3">#</th>
                                <th rowspan="3">NAMA</th>
                                <th rowspan="3">NUPTK</th>
                                <th colspan="13">DATA PANGKAT KGB BARU UPLOAD</th>
                                <th colspan="13">DATA PANGKAT KGB LAMA</th>
                            </tr>
                            <tr>
                                <th colspan="6">PANGKAT</th>
                                <th colspan="7">KGB</th>
                                <th colspan="6">PANGKAT</th>
                                <th colspan="7">KGB</th>
                            </tr>
                            <tr>
                                <th>PANGKAT GOL</th>
                                <th>NO SK</th>
                                <th>TGL SK</th>
                                <th>TMT SK</th>
                                <th>MKT</th>
                                <th>MKB</th>

                                <th>PANGKAT GOL</th>
                                <th>NO SK KGB</th>
                                <th>TGL SK KGB</th>
                                <th>TMT SK KGB</th>
                                <th>MKT KGB</th>
                                <th>MKB KGB</th>
                                <th>JJM</th>

                                <th>PANGKAT GOL</th>
                                <th>NO SK</th>
                                <th>TGL SK</th>
                                <th>TMT SK</th>
                                <th>MKT</th>
                                <th>MKB</th>
                                <th>PANGKAT GOL</th>
                                <th>NO SK KGB</th>
                                <th>TGL SK KGB</th>
                                <th>TMT SK KGB</th>
                                <th>MKT KGB</th>
                                <th>MKB KGB</th>
                                <th>JJM</th>
                            </tr>
                        </thead>
                        <tbody>
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
        const buttonAksiMatching = document.getElementById("button_aksi_matching");
        buttonAksiMatching.setAttribute("disabled", true);

        let dataSendMatching;

        fetch("./get_data_json?id=<?= $id ?>")
            .then(response => response.json())
            .then(data => {
                dataSendMatching = data;
                buttonAksiMatching.removeAttribute("disabled");

                const result_total = document.getElementById("result_total");
                result_total.textContent = data.total.toString();
                const result_lolos = document.getElementById("result_lolos");
                result_lolos.textContent = data.lolos.toString();
                const result_gagal = document.getElementById("result_gagal");
                result_gagal.textContent = data.gagal.toString();
                // const result_belumusul = document.getElementById("result_belumusul");
                // result_belumusul.textContent = data.belumusul.toString();
                // const result_total = document.querySelector(".result_total");
                // result_total.textContent = data.total.toString();
                // const result_lolos = document.querySelector(".result_lolos");
                // result_lolos.textContent = data.lolos.toString();
                // const result_gagal = document.querySelector(".result_gagal");
                // result_gagal.textContent = data.gagal.toString();

                for (let i = 0; i < data.data.length; i++) {
                    const row = document.createElement("tr");
                    const numberCell = document.createElement("td");
                    const namaCell = document.createElement("td");
                    const nuptkCell = document.createElement("td");
                    const pangCellUp = document.createElement("td");
                    const skCellUp = document.createElement("td");
                    const tglSkCellUp = document.createElement("td");
                    const tmtSkCellUp = document.createElement("td");
                    const mktCellUp = document.createElement("td");
                    const mkbCellUp = document.createElement("td");
                    const pangKgbCellUp = document.createElement("td");
                    const skKgbCellUp = document.createElement("td");
                    const tglSkKgbCellUp = document.createElement("td");
                    const tmtSkKgbCellUp = document.createElement("td");
                    const mktKgbCellUp = document.createElement("td");
                    const mkbKgbCellUp = document.createElement("td");
                    const jjmCellUp = document.createElement("td");
                    const pangCell = document.createElement("td");
                    const skCell = document.createElement("td");
                    const tglSkCell = document.createElement("td");
                    const tmtSkCell = document.createElement("td");
                    const mktCell = document.createElement("td");
                    const mkbCell = document.createElement("td");
                    const pangKgbCell = document.createElement("td");
                    const skKgbCell = document.createElement("td");
                    const tglSkKgbCell = document.createElement("td");
                    const tmtSkKgbCell = document.createElement("td");
                    const mktKgbCell = document.createElement("td");
                    const mkbKgbCell = document.createElement("td");
                    const jjmCell = document.createElement("td");

                    numberCell.textContent = 1 + i;
                    namaCell.textContent = data.data[i].nama;
                    nuptkCell.textContent = data.data[i].nuptk_up;
                    pangCellUp.textContent = data.data[i].pangkat_golongan_up;
                    skCellUp.textContent = data.data[i].nomor_sk_pangkat_up;
                    tglSkCellUp.textContent = data.data[i].tgl_sk_pangkat_up;
                    tmtSkCellUp.textContent = data.data[i].tmt_pangkat_up;
                    mktCellUp.textContent = data.data[i].masa_kerja_tahun_up;
                    mkbCellUp.textContent = data.data[i].masa_kerja_bulan_up;
                    pangKgbCellUp.textContent = data.data[i].pangkat_golongan_kgb_up;
                    skKgbCellUp.textContent = data.data[i].sk_kgb_up;
                    tglSkKgbCellUp.textContent = data.data[i].tgl_sk_kgb_up;
                    tmtSkKgbCellUp.textContent = data.data[i].tmt_sk_kgb_up;
                    mktKgbCellUp.textContent = data.data[i].masa_kerja_tahun_kgb_up;
                    mkbKgbCellUp.textContent = data.data[i].masa_kerja_bulan_kgb_up;
                    jjmCellUp.textContent = data.data[i].jam_mengajar_perminggu_up;
                    pangCell.textContent = data.data[i].pangkat_golongan;
                    skCell.textContent = data.data[i].nomor_sk_pangkat;
                    tglSkCell.textContent = data.data[i].tgl_sk_pangkat;
                    tmtSkCell.textContent = data.data[i].tmt_pangkat;
                    mktCell.textContent = data.data[i].masa_kerja_tahun;
                    mkbCell.textContent = data.data[i].masa_kerja_bulan;
                    pangKgbCell.textContent = data.data[i].pangkat_golongan_kgb;
                    skKgbCell.textContent = data.data[i].sk_kgb;
                    tglSkKgbCell.textContent = data.data[i].tgl_sk_kgb;
                    tmtSkKgbCell.textContent = data.data[i].tmt_sk_kgb;
                    mktKgbCell.textContent = data.data[i].masa_kerja_tahun_kgb;
                    mkbKgbCell.textContent = data.data[i].masa_kerja_bulan_kgb;
                    jjmCell.textContent = data.data[i].jam_mengajar_perminggu;

                    row.appendChild(numberCell);
                    row.appendChild(namaCell);
                    row.appendChild(nuptkCell);
                    row.appendChild(pangCellUp);
                    row.appendChild(skCellUp);
                    row.appendChild(tglSkCellUp);
                    row.appendChild(tmtSkCellUp);
                    row.appendChild(mktCellUp);
                    row.appendChild(mkbCellUp);
                    row.appendChild(pangKgbCellUp);
                    row.appendChild(skKgbCellUp);
                    row.appendChild(tglSkKgbCellUp);
                    row.appendChild(tmtSkKgbCellUp);
                    row.appendChild(mktKgbCellUp);
                    row.appendChild(mkbKgbCellUp);
                    row.appendChild(jjmCellUp);
                    row.appendChild(pangCell);
                    row.appendChild(skCell);
                    row.appendChild(tglSkCell);
                    row.appendChild(tmtSkCell);
                    row.appendChild(mktCell);
                    row.appendChild(mkbCell);
                    row.appendChild(pangKgbCell);
                    row.appendChild(skKgbCell);
                    row.appendChild(tglSkKgbCell);
                    row.appendChild(tmtSkKgbCell);
                    row.appendChild(mktKgbCell);
                    row.appendChild(mkbKgbCell);
                    row.appendChild(jjmCell);
                    row.classList.add(data.data[i].status);
                    tbody.appendChild(row);
                }
            });

        function aksiMatching() {
            buttonAksiMatching.setAttribute("disabled", true);
            console.log(dataSendMatching);
            const progBar = document.getElementById("progressBar");

            progBar.style.display = "block";

            ambilId("status").innerHTML = "Menyimpan Data . . .";

            let jumlahDataBerhasil = 0;
            let jumlahDataGagal = 0;

            let sendToServer = function(lines, index) {
                if (index > lines.length - 1) {
                    ambilId("progressBar").style.display = "none";
                    ambilId("status").innerHTML = "Proses Matching Berhasil.";
                    ambilId("status").style.color = "green";
                    ambilId("progressBar").value = 0;

                    Swal.fire(
                        'SELAMAT!',
                        "Proses Matching Data Berhasil.",
                        'success'
                    ).then((valRes) => {
                        document.location.href = "<?= base_url('situgu/su/masterdata/ptk'); ?>";
                    })
                    return; // guard condition
                }

                item = lines[index];
                let total = ((index + 1) / lines.length) * 100;
                total = total.toFixed(2);

                $.ajax({
                    url: "./prosesmatching",
                    type: 'POST',
                    data: item,
                    dataType: 'JSON',
                    success: function(msg) {
                        if (msg.code != 200) {
                            ambilId("status").style.color = "blue";
                            ambilId("progressBar").value = total;
                            ambilId("loaded_n_total").innerHTML = total + '%';
                            console.log(msg.message);
                            if (index + 1 === lines.length) {
                                ambilId("progressBar").style.display = "none";
                                ambilId("status").innerHTML = msg.message;
                                ambilId("status").style.color = "green";
                                ambilId("progressBar").value = 0;

                                Swal.fire(
                                    'SELAMAT!',
                                    "Proses Matching Data Berhasil.",
                                    'success'
                                ).then((valRes) => {
                                    document.location.href = "<?= base_url('situgu/su/masterdata/ptk'); ?>";
                                })
                            }
                        } else {
                            ambilId("status").style.color = "blue";
                            ambilId("progressBar").value = total;
                            ambilId("loaded_n_total").innerHTML = total + '%';

                            if (index + 1 === lines.length) {
                                ambilId("progressBar").style.display = "none";
                                ambilId("status").innerHTML = msg.message;
                                ambilId("status").style.color = "green";
                                ambilId("progressBar").value = 0;

                                Swal.fire(
                                    'SELAMAT!',
                                    "Proses Matching Data Berhasil.",
                                    'success'
                                ).then((valRes) => {
                                    document.location.href = "<?= base_url('situgu/su/masterdata/ptk'); ?>";
                                })
                            }
                        }

                        setTimeout(
                            function() {
                                sendToServer(lines, index + 1);
                            },
                            350 // delay in ms
                        );
                    },
                    error: function(error) {
                        ambilId("progressBar").style.display = "none";
                        ambilId("status").innerHTML = msg.message;
                        ambilId("status").style.color = "green";
                        ambilId("progressBar").value = 0;
                        buttonAksiMatching.removeAttribute("disabled");
                        Swal.fire(
                            'Failed!',
                            "Gagal.",
                            'warning'
                        );
                    }
                });
            };

            sendToServer(dataSendMatching.aksi, 0);
        }
    </script>
<?php } ?>