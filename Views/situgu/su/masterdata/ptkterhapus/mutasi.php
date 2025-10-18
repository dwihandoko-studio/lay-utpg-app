<?php if (isset($id)) { ?>
    <div class="modal-body">
        <div class="col-lg-12">
            <div class="mb-3 _sekolah-block">
                <label for="_sekolah_pilihan_mutasi" class="col-form-label">Sekolah:</label>
                <select class="form-control ptk" id="_sekolah_pilihan_mutasi" name="_sekolah_pilihan_mutasi" style="width: 100%">
                    <option value="">&nbsp;</option>
                </select>
                <div class="help-block _sekolah_pilihan_mutasi"></div>
            </div>
        </div>
        <div class="col-lg-12">
            <label class="col-form-label">Keterangan Mutasi:</label>
            <textarea rows="10" class="form-control" id="_keterangan_mutasi" name="_keterangan_mutasi" required></textarea>
        </div>
    </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary waves-effect" data-bs-dismiss="modal">Close</button>
        <button type="button" onclick="simpanTolak(this)" class="btn btn-primary waves-effect waves-light">Mutasi Data PTK</button>
    </div>
    <script>
        $('#_sekolah_pilihan_mutasi').select2({
            dropdownParent: ".content-tolakModal",
            ajax: {
                url: "./getSekolahMutasi",
                type: 'POST',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        keyword: params.term,
                    };
                },
                processResults: function(data, params) {
                    // parse the results into the format expected by Select2
                    // since we are using custom formatting functions we do not need to
                    // alter the remote JSON data, except to indicate that infinite
                    // scrolling can be used
                    // params.page = params.page || 1;
                    if (data.status === 200) {
                        return {
                            results: data.data
                        };
                    } else {
                        return {
                            results: []
                        };
                    }

                    // return {
                    //     results: data.items,
                    //     pagination: {
                    //         more: (params.page * 30) < data.total_count
                    //     }
                    // };
                },
                cache: true
            },
            placeholder: 'Cari Sekolah',
            minimumInputLength: 3,
            templateResult: formatRepo,
            templateSelection: formatRepoSelection
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
            $container.find(".select2-result-repository__description").text(repo.npsn + " (" + repo.bentuk_pendidikan + " - " + repo.kecamatan + ")");

            return $container;
        }

        function formatRepoSelection(repo) {
            return repo.nama || repo.text;
        }


        function simpanTolak(e) {
            const keterangan = document.getElementsByName('_keterangan_mutasi')[0].value;
            const sekolah_tujuan = document.getElementsByName('_sekolah_pilihan_mutasi')[0].value;

            if (sekolah_tujuan === "") {
                $("select#_sekolah_pilihan_mutasi").css("color", "#dc3545");
                $("select#_sekolah_pilihan_mutasi").css("border-color", "#dc3545");
                $('._sekolah_pilihan_mutasi').html('<ul role="alert" style="color: #dc3545; list-style-type:none; padding-inline-start: 10px;"><li style="color: #dc3545;">Silahkan pilih Sekolah Tujuan.</li></ul>');
                return false;
            }

            if (keterangan === "" || keterangan === undefined) {
                Swal.fire(
                    'PERINGATAN!!!',
                    "Keterangan mutasi tidak boleh kosong.",
                    'warning'
                );
                return;
            }
            $.ajax({
                url: "./mutasi",
                type: 'POST',
                data: {
                    id: '<?= $id ?>',
                    nama: '<?= $nama ?>',
                    sekolah_tujuan: sekolah_tujuan,
                    keterangan: keterangan,
                },
                dataType: 'JSON',
                beforeSend: function() {
                    e.disabled = true;
                    $('div.modal-content-loading-tolak').block({
                        message: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>'
                    });
                },
                success: function(resul) {
                    $('div.modal-content-loading-tolak').unblock();

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
                                e.disabled = false;
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
                error: function(erro) {
                    console.log(erro);
                    // e.attr('disabled', false);
                    e.disabled = false
                    $('div.modal-content-loading-tolak').unblock();
                    Swal.fire(
                        'PERINGATAN!',
                        "Server sedang sibuk, silahkan ulangi beberapa saat lagi.",
                        'warning'
                    );
                }
            });
        };
    </script>
<?php } ?>