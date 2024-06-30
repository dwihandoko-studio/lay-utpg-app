<?= form_open('./verifikasitagihan', ['class' => 'formsimpanbanyak']) ?>

<input type="hidden" value="<?= $tw_active ?>" id="id" name="id" readonly>
<input type="hidden" value="<?= $id_bank ?>" id="bank" name="bank" readonly>
<div class="tomboh-simpan-data" style="display: block;">
    <button type="button" onclick="checkedAllHijau()" class="btn btn-sm btn-primary waves-effect waves-light bntcheckhijau"><i class="fas fa-check-double font-size-16 align-middle me-2"></i> PILIH WARNA HIJAU</button>&nbsp;&nbsp;
    <button type="button" onclick="checkedAllMerah()" class="btn btn-sm btn-warning waves-effect waves-light bntcheckmerah"><i class="fas fa-check-double font-size-16 align-middle me-2"></i> PILIH WARNA MERAH</button>&nbsp;&nbsp;
    <button type="button" onclick="setujuiAjuanProses()" class="btn btn-sm btn-success waves-effect waves-light btnverifikasi"><i class="bx bx-save font-size-16 align-middle me-2"></i> SETUJUI VEFIKASI</button> &nbsp;&nbsp;
    <button type="button" onclick="tolakAjuanProses()" class="btn btn-sm btn-danger waves-effect waves-light btnverifikasitolak"><i class="fas fa-times-circle font-size-16 align-middle me-2"></i> TOLAK VEFIKASI</button> &nbsp;&nbsp;
</div>
<table id="data-datatables" class="table table-bordered w-100 tb-datatables">
    <thead>
        <tr>
            <th data-orderable="false"><input class="form-check-input" type="checkbox" id="centangsemua"></th>
            <th data-orderable="false" width="15%">Nama</th>
            <th data-orderable="false" width="14.5%">NIP</th>
            <th data-orderable="false">Instansi</th>
            <th data-orderable="false">Kecamatan</th>
            <th data-orderable="false" width="11%">Besar Pinjaman</th>
            <th data-orderable="false" width="10%">Jumlah Tagihan</th>
            <th data-orderable="false" width="7.5%">Jml Bulan<br>Angs</th>
            <th data-orderable="false" width="7%">Angs Ke</th>
            <th data-orderable="false" width="15%">&nbsp;</th>
        </tr>
    </thead>
    <tbody class="formtambah">
        <?php if (isset($datas)) { ?>
            <?php if (count($datas) > 0) { ?>
                <?php foreach ($datas as $key => $value) { ?>
                    <?php if ($value->jumlah_transfer == NULL || $value->jumlah_transfer == "") {
                        $value->jumlah_transfer = 0;
                    } ?>
                    <?php if (($value->jumlah_transfer - ($value->jumlah_tagihan + $value->jumlah_potongan)) > 0) { ?>
                        <tr class="lolosVerifikasi">
                            <td>
                                <input class="form-check-input centangIdTag" type="checkbox" onchange="changedChecked(this)" name="id_tag[]" value="<?= $value->id ?>">
                            </td>
                            <td style="padding: 0 !important;">
                                <select class="form-control filter-pegawai" id="_filter_pegawai_<?= $key + 1; ?>" name="_filter_pegawai[]" data-id="<?= $key + 1; ?>" onchange="changePegawai(this)" aria-readonly="">
                                    <option value="<?= $value->id_pegawai; ?>" selected><?= $value->nama; ?></option>
                                </select>
                            </td>
                            <td style="padding: 0 !important;">
                                <input class="form-control" type="text" value="<?= $value->nip; ?>" id="nip_<?= $key + 1; ?>" name="nip[]" readonly>
                            </td>
                            <td style="padding: 0 !important;">
                                <input class="form-control" type="text" value="<?= $value->nama_instansi; ?>" id="instansi_<?= $key + 1; ?>" name="instansi[]" readonly>
                            </td>
                            <td style="padding: 0 !important;">
                                <input class="form-control" type="text" value="<?= $value->nama_kecamatan; ?>" id="kecamatan_<?= $key + 1; ?>" name="kecamatan[]" readonly>
                            </td>
                            <td style="padding: 0 !important;">
                                <input class="form-control jumlah-pinjaman" type="text" value="<?= number_rupiah($value->besar_pinjaman); ?>" id="jumlah_pinjaman_<?= $key + 1; ?>" name="jumlah_pinjaman[]" readonly>
                            </td>
                            <td class="table-success" style="padding: 0 !important;">
                                <input class="form-control jumlah-pinjaman" type="text" value="<?= number_rupiah($value->jumlah_tagihan); ?>" id="jumlah_tagihan_<?= $key + 1; ?>" name="jumlah_tagihan[]" readonly>
                            </td>
                            <td style="padding: 0 !important;">
                                <input class="form-control" type="number" value="<?= $value->jumlah_bulan_angsuran; ?>" id="jumlah_bulan_angsuran_<?= $key + 1; ?>" name="jumlah_bulan_angsuran[]" readonly>
                            </td>
                            <td style="padding: 0 !important;">
                                <input class="form-control" type="number" value="<?= $value->angsuran_ke; ?>" id="angsuran_ke_<?= $key + 1; ?>" name="angsuran_ke[]" readonly>
                            </td>
                        </tr>
                    <?php } else { ?>
                        <tr class="gagalVerifikasi">
                            <td>
                                <input class="form-check-input centangIdTag" onchange="changedChecked(this)" type="checkbox" name="id_tag[]" value="<?= $value->id ?>">
                            </td>
                            <td style="padding: 0 !important;">
                                <select class="form-control filter-pegawai" id="_filter_pegawai_<?= $key + 1; ?>" name="_filter_pegawai[]" data-id="<?= $key + 1; ?>" onchange="changePegawai(this)" aria-readonly="">
                                    <option value="<?= $value->id_pegawai; ?>" selected><?= $value->nama; ?></option>
                                </select>
                            </td>
                            <td style="padding: 0 !important;">
                                <input class="form-control" type="text" value="<?= $value->nip; ?>" id="nip_<?= $key + 1; ?>" name="nip[]" readonly>
                            </td>
                            <td style="padding: 0 !important;">
                                <input class="form-control" type="text" value="<?= $value->nama_instansi; ?>" id="instansi_<?= $key + 1; ?>" name="instansi[]" readonly>
                            </td>
                            <td style="padding: 0 !important;">
                                <input class="form-control" type="text" value="<?= $value->nama_kecamatan; ?>" id="kecamatan_<?= $key + 1; ?>" name="kecamatan[]" readonly>
                            </td>
                            <td style="padding: 0 !important;">
                                <input class="form-control jumlah-pinjaman" type="text" value="<?= number_rupiah($value->besar_pinjaman); ?>" id="jumlah_pinjaman_<?= $key + 1; ?>" name="jumlah_pinjaman[]" readonly>
                            </td>
                            <td class="table-danger" style="padding: 0 !important;">
                                <input class="form-control jumlah-pinjaman" type="text" value="<?= number_rupiah($value->jumlah_tagihan); ?>" id="jumlah_tagihan_<?= $key + 1; ?>" name="jumlah_tagihan[]" readonly>
                            </td>
                            <td style="padding: 0 !important;">
                                <input class="form-control" type="number" value="<?= $value->jumlah_bulan_angsuran; ?>" id="jumlah_bulan_angsuran_<?= $key + 1; ?>" name="jumlah_bulan_angsuran[]" readonly>
                            </td>
                            <td style="padding: 0 !important;">
                                <input class="form-control" type="number" value="<?= $value->angsuran_ke; ?>" id="angsuran_ke_<?= $key + 1; ?>" name="angsuran_ke[]" readonly>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } ?>
            <?php } else { ?>

            <?php } ?>
        <?php } else { ?>
        <?php } ?>
    </tbody>
</table>
<?= form_close(); ?>
<script>
    function changedChecked(event) {
        const parentTrChanged = event.parentElement.parentElement; // Get the parent table row (<tr>)
        const hasTextareaTdChanged = parentTrChanged.querySelector('td.textarea-td'); // Check if textarea TD exists

        if (event.checked) {
            if (!hasTextareaTdChanged) {
                // Create a new table cell (<td>) for the textarea
                const newTdChanged = document.createElement('td');
                newTdChanged.classList.add('textarea-td');

                // Create a textarea element
                const textareaChanged = document.createElement('textarea');
                textareaChanged.className = 'form-control keteranganPenolakan';
                textareaChanged.rows = '3';
                textareaChanged.placeholder = 'Keterangan penolakan...';
                textareaChanged.value = ''; // Set your desired value here
                textareaChanged.name = 'keterangan[]'; // Set the name attribute for the new field
                textareaChanged.name = "new_textarea[]"; // Adjust the name as needed

                // Add the textarea to the new table cell
                newTdChanged.appendChild(textareaChanged);

                // Append the new table cell to the parent table row
                parentTrChanged.appendChild(newTdChanged);
            }
        }
        // checkboxhijau.checked = true; // Set the checkbox to checked

        // const ketCell = document.createElement('td');

        // // const newCell = checkboxhijau.insertCell();

        // // Create the input element for the new field
        // const newInput = document.createElement('textarea');
        // newInput.className = 'form-control keteranganPenolakan';
        // newInput.rows = '3';
        // newInput.placeholder = 'Keterangan penolakan...';
        // newInput.value = ''; // Set your desired value here
        // newInput.name = 'keterangan[]'; // Set the name attribute for the new field

        // // Append the input element to the new cell
        // ketCell.appendChild(newInput);
        // rowhijau.appendChild(ketCell);
    }

    function tolakAjuanProses() {
        // e.preventDefault();

        let checkedBoxesKirimTolak = [];
        let keterangansKirimTolak = [];

        // Select all checkboxes with class "centangIdTag" (existing code)
        let checkboxesKirimTolak = document.querySelectorAll('.centangIdTag');
        // let keteranganKirimTolak = document.querySelectorAll('.keteranganPenolakan');

        // Loop through checkboxes
        for (let i = 0; i < checkboxesKirimTolak.length; i++) {
            const checkboxkirimTolak = checkboxesKirimTolak[i];

            // Check if the checkbox is checked
            if (checkboxkirimTolak.checked) {
                const rowcheckboxkirimTolak = checkboxkirimTolak.parentElement.parentElement;
                const keteranganTolak = rowcheckboxkirimTolak.querySelector('textarea[name="keterangan[]"]').value;

                // console.log(keteranganTolak);
                // return;
                // const keteranganTolak = keteranganKirimTolak[i].value;
                checkedBoxesKirimTolak.push(checkboxkirimTolak.value); // Add checkbox value to the array
                if (keteranganTolak === "") {
                    // keterangansKirimTolak.push("-"); // Add checkbox value to the array
                } else {
                    keterangansKirimTolak.push(keteranganTolak); // Add checkbox value to the array
                }

            }
        }


        // let jmlData = $('.centangIdTag:checked');

        if (checkedBoxesKirimTolak.length === 0) {
            Swal.fire(
                'Perhatian!',
                "Maaf, silahkan pilih data yang akan di verifikasi.",
                'error'
            );
        } else {
            if (checkedBoxesKirimTolak.length !== keterangansKirimTolak.length) {
                Swal.fire(
                    'Maaf!',
                    "Masih ada keterangan yang kosong, silahkan berikan keterangan penolakan verifikasi pada row yang telah di pilih.",
                    'error'
                );
                return;
            }
            Swal.fire({
                title: 'Apakah anda yakin ingin menyetujui verifikasi proses tagihan data ini?',
                text: `Setujui Proses tagihan : <?= $tw->tahun ?> - <?= $tw->bulan ?> untuk Bank <?= getNamaBank($id_bank) ?> Sejumlah ${checkedBoxesKirimTolak.length} data`,
                showCancelButton: true,
                icon: 'question',
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Stujui Proses Tagihan!'
            }).then((result) => {
                if (result.value) {
                    // const formData = $(this).serializeArray();
                    // let processedData = {};
                    // for (let i = 0; i < formData.length; i++) {
                    //     const field = formData[i].name;
                    //     const value = formData[i].value;

                    //     if (field.endsWith('[]')) { // Check if field name ends with [] for multiple values
                    //         processedData[field.slice(0, -2)] = processedData[field.slice(0, -2)] || []; // Initialize array if needed
                    //         processedData[field.slice(0, -2)].push(value);
                    //     } else {
                    //         processedData[field] = value;
                    //     }
                    // }
                    let processedDataTolak = {};
                    processedDataTolak['id_tag'] = checkedBoxesKirimTolak;
                    processedDataTolak['keterangan'] = keterangansKirimTolak;
                    processedDataTolak['id'] = document.getElementsByName('id')[0].value;
                    processedDataTolak['bank'] = document.getElementsByName('bank')[0].value;

                    const jsonDataTolak = JSON.stringify(processedDataTolak);
                    // const jsonData = JSON.stringify(formData);
                    $.ajax({
                        url: './tolakverifikasitagihan',
                        // url: $(this).attr('action'),
                        type: 'POST',
                        data: {
                            data: jsonDataTolak,
                            format: "json"
                        },
                        dataType: "json",
                        beforeSend: function() {
                            $('.btnverifikasitolak').attr('disable', 'disabled');
                            $('.btnverifikasitolak').html('<i class="mdi mdi-reload mdi-spin"></i>');
                        },
                        complete: function() {
                            $('.btnverifikasitolak').removeAttr('disable')
                            $('.btnverifikasitolak').html('<i class="fas fa-times-circle font-size-16 align-middle me-2"></i> TOLAK VERIFIKASI');
                        },
                        success: function(response) {
                            if (response.status == 200) {
                                Swal.fire(
                                    'SELAMAT!',
                                    response.message + " " + response.data,
                                    'success'
                                ).then((valRes) => {
                                    reloadPage();
                                })
                            } else {
                                Swal.fire(
                                    'Gagal!',
                                    response.message,
                                    'warning'
                                );
                            }
                        },
                        error: function(xhr, ajaxOptions, thrownError) {
                            Swal.fire(
                                'Failed!',
                                "gagal mengambil data (" + xhr.status.toString + ")",
                                'warning'
                            );
                        }

                    });
                }
            })
        }
    }

    function setujuiAjuanProses() {
        // e.preventDefault();

        let checkedBoxesKirim = [];

        // Select all checkboxes with class "centangIdTag" (existing code)
        let checkboxesKirim = document.querySelectorAll('.centangIdTag');

        // Loop through checkboxes
        for (let i = 0; i < checkboxesKirim.length; i++) {
            const checkboxkirim = checkboxesKirim[i];

            // Check if the checkbox is checked
            if (checkboxkirim.checked) {
                checkedBoxesKirim.push(checkboxkirim.value); // Add checkbox value to the array
            }
        }


        // let jmlData = $('.centangIdTag:checked');

        if (checkedBoxesKirim.length === 0) {
            Swal.fire(
                'Perhatian!',
                "Maaf, silahkan pilih data yang akan di verifikasi.",
                'error'
            );
        } else {
            Swal.fire({
                title: 'Apakah anda yakin ingin menyetujui verifikasi proses tagihan data ini?',
                text: `Setujui Proses tagihan : <?= $tw->tahun ?> - <?= $tw->bulan ?> untuk Bank <?= getNamaBank($id_bank) ?> Sejumlah ${checkedBoxesKirim.length} data`,
                showCancelButton: true,
                icon: 'question',
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Stujui Proses Tagihan!'
            }).then((result) => {
                if (result.value) {
                    // const formData = $(this).serializeArray();
                    // let processedData = {};
                    // for (let i = 0; i < formData.length; i++) {
                    //     const field = formData[i].name;
                    //     const value = formData[i].value;

                    //     if (field.endsWith('[]')) { // Check if field name ends with [] for multiple values
                    //         processedData[field.slice(0, -2)] = processedData[field.slice(0, -2)] || []; // Initialize array if needed
                    //         processedData[field.slice(0, -2)].push(value);
                    //     } else {
                    //         processedData[field] = value;
                    //     }
                    // }
                    let processedData = {};
                    processedData['id_tag'] = checkedBoxesKirim;
                    processedData['id'] = document.getElementsByName('id')[0].value;
                    processedData['bank'] = document.getElementsByName('bank')[0].value;

                    const jsonData = JSON.stringify(processedData);
                    // const jsonData = JSON.stringify(formData);
                    $.ajax({
                        url: './verifikasitagihan',
                        // url: $(this).attr('action'),
                        type: 'POST',
                        data: {
                            data: jsonData,
                            format: "json"
                        },
                        dataType: "json",
                        beforeSend: function() {
                            $('.btnverifikasi').attr('disable', 'disabled');
                            $('.btnverifikasi').html('<i class="mdi mdi-reload mdi-spin"></i>');
                        },
                        complete: function() {
                            $('.btnverifikasi').removeAttr('disable')
                            $('.btnverifikasi').html('<i class="bx bx-save font-size-16 align-middle me-2"></i> SETUJUI VERIFIKASI');
                        },
                        success: function(response) {
                            if (response.status == 200) {
                                Swal.fire(
                                    'SELAMAT!',
                                    response.message + " " + response.data,
                                    'success'
                                ).then((valRes) => {
                                    reloadPage();
                                })
                            } else {
                                Swal.fire(
                                    'Gagal!',
                                    response.message,
                                    'warning'
                                );
                            }
                        },
                        error: function(xhr, ajaxOptions, thrownError) {
                            Swal.fire(
                                'Failed!',
                                "gagal mengambil data (" + xhr.status.toString + ")",
                                'warning'
                            );
                        }

                    });
                }
            })
        }
    }

    function checkedAllHijau() {
        // console.log("working");
        let checkboxeshijau = document.querySelectorAll('.centangIdTag');

        // Loop through each checkbox
        for (let i = 0; i < checkboxeshijau.length; i++) {
            const checkboxhijau = checkboxeshijau[i];
            const rowhijau = checkboxhijau.parentElement.parentElement; // Get the parent row (TR)
            // console.log(rowhijau);

            // Check if the row has the class "table-success"
            if (rowhijau.classList.contains('lolosVerifikasi')) {
                checkboxhijau.checked = true; // Set the checkbox to checked
            }
            if (rowhijau.classList.contains('gagalVerifikasi')) {
                checkboxhijau.checked = false; // Set the checkbox to checked
            }
        }
    }

    function checkedAllMerah() {
        // console.log("working");
        let checkboxeshijau = document.querySelectorAll('.centangIdTag');

        // Loop through each checkbox
        for (let i = 0; i < checkboxeshijau.length; i++) {
            const checkboxhijau = checkboxeshijau[i];
            const rowhijau = checkboxhijau.parentElement.parentElement; // Get the parent row (TR)
            // const rowhijau1 = checkboxhijau.parentElement; // Get the parent row (TR)
            // console.log(rowhijau1);
            // console.log(rowhijau);

            // Check if the row has the class "table-success"
            if (rowhijau.classList.contains('lolosVerifikasi')) {
                checkboxhijau.checked = false; // Set the checkbox to checked
            }
            if (rowhijau.classList.contains('gagalVerifikasi')) {
                checkboxhijau.checked = true; // Set the checkbox to checked

                const ketCell = document.createElement('td');

                // const newCell = checkboxhijau.insertCell();

                // Create the input element for the new field
                const newInput = document.createElement('textarea');
                newInput.className = 'form-control keteranganPenolakan';
                newInput.rows = '3';
                newInput.placeholder = 'Keterangan penolakan...';
                newInput.value = ''; // Set your desired value here
                newInput.name = 'keterangan[]'; // Set the name attribute for the new field

                // Append the input element to the new cell
                ketCell.appendChild(newInput);
                rowhijau.appendChild(ketCell);
            }
        }


    }
    // Function untuk mengubah data pegawai saat dipilih
    function changePegawai(event) {
        const getId = $(event).data('id');
        const selectedOption = $('#_filter_pegawai_' + getId).find(':selected');

        $('#formCheck_' + getId).val(selectedOption.data('custom-idpegawai'));
        $('#nip_' + getId).val(selectedOption.data('custom-nip'));
        $('#instansi_' + getId).val(selectedOption.data('custom-instansi'));
        $('#kecamatan_' + getId).val(selectedOption.data('custom-kecamatan'));
    }

    // Function untuk memformat angka menjadi Rupiah
    function formatRupiah(angka, prefix) {
        var number_string = angka.replace(/[^,\d]/g, '').toString(),
            split = number_string.split(','),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/g);

        if (ribuan) {
            separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        // rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        // return 'Rp. ' + rupiah;
        rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        return prefix == undefined ? rupiah : (rupiah ? prefix + rupiah : '');
    }
    let rowBody = 1;


    $(document).ready(function() {
        <?php if (isset($datas)) { ?>
            <?php if (count($datas) > 0) { ?>
                rowBody = <?= count($datas) ?>;
            <?php } else { ?>
                rowBody = 1;
            <?php } ?>
        <?php } else { ?>
            rowBody = 1;
        <?php } ?>
        $('#_filter_pegawai_' + rowBody).select2({
            dropdownParent: ".data-contens",
            allowClear: true,
            ajax: {
                url: "./getPegawai",
                type: 'POST',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        keyword: params.term,
                    };
                },
                processResults: function(data, params) {
                    if (data.status === 200) {
                        return {
                            results: data.data
                        };
                    } else {
                        return {
                            results: []
                        };
                    }
                },
                cache: true
            },
            placeholder: 'Cari Pegawai',
            minimumInputLength: 3,
            templateResult: formatRepo,
            templateSelection: formatRepoSelection
        });
        // Tambahkan event handler untuk tombol tambah baris
        $('.btnaddform').click(function(e) {
            e.preventDefault();
            rowBody++;

            // Tambahkan baris baru ke dalam tabel
            let newRow = `
                    <tr>
                        <td>
                            <input class="form-check-input" type="checkbox" id="formCheck_${rowBody}" name="check[]" value="">
                        </td>
                        <td>
                            <select class="form-control filter-pegawai" id="_filter_pegawai_${rowBody}" name="_filter_pegawai[]" data-id="${rowBody}" onchange="changePegawai(this)" required>
                                <option value="">&nbsp;</option>
                            </select>
                        </td>
                        <td>
                            <input class="form-control" type="text" value="" id="nip_${rowBody}" name="nip[]" readonly>
                        </td>
                        <td>
                            <input class="form-control" type="text" value="" id="instansi_${rowBody}" name="instansi[]" readonly>
                        </td>
                        <td>
                            <input class="form-control" type="text" value="" id="kecamatan_${rowBody}" name="kecamatan[]" readonly>
                        </td>
                        <td>
                            <input class="form-control jumlah-pinjaman" type="text" value="" id="jumlah_pinjaman_${rowBody}" name="jumlah_pinjaman[]" required>
                        </td>
                        <td>
                            <input class="form-control  jumlah-pinjaman" type="text" value="" id="jumlah_tagihan_${rowBody}" name="jumlah_tagihan[]" required>
                        </td>
                        <td>
                            <input class="form-control" type="number" value="" id="jumlah_bulan_angsuran_${rowBody}" name="jumlah_bulan_angsuran[]" required>
                        </td>
                        <td>
                            <input class="form-control" type="number" value="" id="angsuran_ke_${rowBody}" name="angsuran_ke[]" required>
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger btn-rounded waves-effect waves-light btnhapusform"><i class="bx bxs-trash"></i></button>
                        </td>
                    </tr>`;

            // Tambahkan baris baru ke tabel
            $('.formtambah').append(newRow);

            // Inisialisasi Select2 pada elemen terbaru
            $('#_filter_pegawai_' + rowBody).select2({
                dropdownParent: ".data-contens",
                allowClear: true,
                ajax: {
                    url: "./getPegawai",
                    type: 'POST',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            keyword: params.term,
                        };
                    },
                    processResults: function(data, params) {
                        if (data.status === 200) {
                            return {
                                results: data.data
                            };
                        } else {
                            return {
                                results: []
                            };
                        }
                    },
                    cache: true
                },
                placeholder: 'Cari Pegawai',
                minimumInputLength: 3,
                templateResult: formatRepo,
                templateSelection: formatRepoSelection
            });
        });

        // Event delegation untuk memformat input jumlah pinjaman
        $('.formtambah').on('keyup', '.jumlah-pinjaman', function() {
            $(this).val(formatRupiah($(this).val()));
        });

        $('.formsimpanbanyak').submit(function(e) {
            e.preventDefault();

            let checkedBoxesKirim = [];

            // Select all checkboxes with class "centangIdTag" (existing code)
            let checkboxesKirim = document.querySelectorAll('.centangIdTag');

            // Loop through checkboxes
            for (let i = 0; i < checkboxesKirim.length; i++) {
                const checkboxkirim = checkboxesKirim[i];

                // Check if the checkbox is checked
                if (checkboxkirim.checked) {
                    checkedBoxesKirim.push(checkboxkirim.value); // Add checkbox value to the array
                }
            }


            // let jmlData = $('.centangIdTag:checked');

            if (checkedBoxesKirim.length === 0) {
                Swal.fire(
                    'Perhatian!',
                    "Maaf, silahkan pilih data yang akan di verifikasi.",
                    'error'
                );
            } else {
                Swal.fire({
                    title: 'Apakah anda yakin ingin menyetujui verifikasi proses tagihan data ini?',
                    text: `Setujui Proses tagihan : <?= $tw->tahun ?> - <?= $tw->bulan ?> untuk Bank <?= getNamaBank($id_bank) ?> Sejumlah ${checkedBoxesKirim.length} data`,
                    showCancelButton: true,
                    icon: 'question',
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Stujui Proses Tagihan!'
                }).then((result) => {
                    if (result.value) {
                        // const formData = $(this).serializeArray();
                        // let processedData = {};
                        // for (let i = 0; i < formData.length; i++) {
                        //     const field = formData[i].name;
                        //     const value = formData[i].value;

                        //     if (field.endsWith('[]')) { // Check if field name ends with [] for multiple values
                        //         processedData[field.slice(0, -2)] = processedData[field.slice(0, -2)] || []; // Initialize array if needed
                        //         processedData[field.slice(0, -2)].push(value);
                        //     } else {
                        //         processedData[field] = value;
                        //     }
                        // }
                        let processedData = {};
                        processedData['id_tag'] = checkedBoxesKirim;
                        processedData['id'] = document.getElementsByName('id')[0].value;
                        processedData['bank'] = document.getElementsByName('bank')[0].value;

                        const jsonData = JSON.stringify(processedData);
                        // const jsonData = JSON.stringify(formData);
                        $.ajax({
                            url: './verifikasitagihan',
                            // url: $(this).attr('action'),
                            type: 'POST',
                            data: {
                                data: jsonData,
                                format: "json"
                            },
                            dataType: "json",
                            beforeSend: function() {
                                $('.btnverifikasi').attr('disable', 'disabled');
                                $('.btnverifikasi').html('<i class="mdi mdi-reload mdi-spin"></i>');
                            },
                            complete: function() {
                                $('.btnverifikasi').removeAttr('disable')
                                $('.btnverifikasi').html('<i class="bx bx-save font-size-16 align-middle me-2"></i> SETUJUI VERIFIKASI');
                            },
                            success: function(response) {
                                if (response.status == 200) {
                                    Swal.fire(
                                        'SELAMAT!',
                                        response.message + " " + response.data,
                                        'success'
                                    ).then((valRes) => {
                                        reloadPage();
                                    })
                                } else {
                                    Swal.fire(
                                        'Gagal!',
                                        response.message,
                                        'warning'
                                    );
                                }
                            },
                            error: function(xhr, ajaxOptions, thrownError) {
                                Swal.fire(
                                    'Failed!',
                                    "gagal mengambil data (" + xhr.status.toString + ")",
                                    'warning'
                                );
                            }

                        });
                    }
                })
            }


        })

        $('#centangsemua').click(function(e) {

            if ($(this).is(':checked')) {
                $('.centangIdTag').prop('checked', true);
            } else {
                $('.centangIdTag').prop('checked', false);
            }
        })

        // $('#bntcheckhijau').click(function(e) {

        // })
    });

    $(document).on('click', '.btnhapusform', function(e) {
        e.preventDefault();
        $(this).parents('tr').remove();
    });

    function formatRepo(repo) {
        if (repo.loading) {
            return repo.text;
        }

        var $container = $(
            "<div class='select2-result-repository clearfix'>" +
            "<div class='select2-result-repository__meta'>" +
            "<div class='select2-result-repository__title'></div>" +
            "<div class='select2-result-repository__description'></div>" +
            "</div>" +
            "</div>"
        );

        $container.find(".select2-result-repository__title").text(repo.nama);
        $container.find(".select2-result-repository__description").text(repo.nip + " - " + repo.nama_instansi + " ( Kec. " + repo.nama_kecamatan + ")");

        return $container;
    }

    function formatRepoSelection(repo) {
        $(repo.element).attr('data-custom-idpegawai', repo.id);
        $(repo.element).attr('data-custom-nip', repo.nip);
        $(repo.element).attr('data-custom-instansi', repo.nama_instansi);
        $(repo.element).attr('data-custom-kecamatan', repo.nama_kecamatan);
        return repo.nama || repo.text;
    }
</script>

<!-- <script>
    function changePegawai(event) {
        const getId = $(event).data('id');
        const getNip = $('#_filter_pegawai_' + getId).find(':selected').data('custom-nip');
        const getInstansi = $('#_filter_pegawai_' + getId).find(':selected').data('custom-instansi');
        const getKecamatan = $('#_filter_pegawai_' + getId).find(':selected').data('custom-kecamatan');

        $('#nip_' + getId).val(getNip);
        $('#instansi_' + getId).val(getInstansi);
        $('#kecamatan_' + getId).val(getKecamatan);
    }

    function formatRupiah(angka, prefix) {
        var number_string = angka.replace(/[^,\d]/g, '').toString(),
            split = number_string.split(','),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        return prefix == undefined ? rupiah : (rupiah ? 'Rp. ' + rupiah : '');
    }

    let rowBody = 1;

    $(document).ready(function(e) {
                $('.btnaddform').click(function(e) {
                            rowBody++;
                            e.preventDefault();
                            $('.formtambah').append(`
            <tr>
                    <td>
                        <input class="form-check-input" type="checkbox" id="formCheck1">
                    </td>
                    <td>
                        <select class="form-control filter-pegawai" id="_filter_pegawai_` + rowBody + `" name="_filter_pegawai[]" data-id="` + rowBody + `" onchange="changePegawai(this)" required>
                            <option value="">&nbsp;</option>
                        </select>
                        <script>
                            $('#_filter_pegawai_` + rowBody + `').select2({
                                dropdownParent: ".data-contens",
                                allowClear: true,
                                ajax: {
                                    url: "./getPegawai",
                                    type: 'POST',
                                    dataType: 'json',
                                    delay: 250,
                                    data: function(params) {
                                        return {
                                            keyword: params.term,
                                        };
                                    },
                                    processResults: function(data, params) {
                                        if (data.status === 200) {
                                            return {
                                                results: data.data
                                            };
                                        } else {
                                            return {
                                                results: []
                                            };
                                        }
                                    },
                                    cache: true
                                },
                                placeholder: 'Cari Pegawai',
                                minimumInputLength: 3,
                                templateResult: formatRepo,
                                templateSelection: formatRepoSelection
                            });
</script>
</td>
<td>
    <input class="form-control" type="text" value="" id="nip_` + rowBody + `" name="nip[]" readonly />
</td>
<td>
    <input class="form-control" type="text" value="" id="instansi_` + rowBody + `" name="instansi[]" readonly />
</td>
<td>
    <input class="form-control" type="text" value="" id="kecamatan_` + rowBody + `" name="kecamatan[]" readonly />
</td>
<td>
    <input class="form-control" type="text" value="" onchange="aksiChangeInput(this)" id="jumlah_pinjaman_` + rowBody + `" name="jumlah_pinjaman[]" required />
    <script>
        let jumlah_pinjaman_` + rowBody + ` = document.getElementById('jumlah_pinjaman_` + rowBody + `');
        jumlah_pinjaman_` + rowBody + `.addEventListener('keyup', function(e) {
            jumlah_pinjaman_` + rowBody + `.value = formatRupiah(this.value);
        });
    </script>
</td>
<td>
    <input class="form-control" type="text" value="" id="jumlah_tagihan_` + rowBody + `" name="jumlah_tagihan[]" required />
</td>
<td>
    <input class="form-control" type="text" value="" id="jumlah_bulan_angsuran_` + rowBody + `" name="jumlah_bulan_angsuran[]" required />
</td>
<td>
    <input class="form-control" type="text" value="" id="angsuran_ke_` + rowBody + `" name="angsuran_ke[]" required />
</td>
<td>
    <button type="button" onclick="aksiTambah(this)" class="btn btn-primary btn-rounded waves-effect waves-light">+</button>
</td>
</tr>
`);
});
});
</script> -->