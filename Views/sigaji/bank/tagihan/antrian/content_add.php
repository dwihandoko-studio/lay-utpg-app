<table id="data-datatables" class="table table-bordered w-100 tb-datatables">
    <thead>
        <tr>
            <th data-orderable="false">#</th>
            <th data-orderable="false" width="20%">Nama</th>
            <th data-orderable="false">NIP</th>
            <th data-orderable="false">Instansi</th>
            <th data-orderable="false">Kecamatan</th>
            <th data-orderable="false">Besar Pinjaman</th>
            <th data-orderable="false">Jumlah Tagihan</th>
            <th data-orderable="false">Jml Bulan<br>Angs</th>
            <th data-orderable="false">Angs Ke</th>
            <th data-orderable="false"> </th>
        </tr>
    </thead>
    <tbody>
        <?php if (isset($datas)) { ?>
            <?php if (count($datas) > 0) { ?>
                <?php foreach ($variable as $key => $value) { ?>
                    <?php if ($key < 1) { ?>

                    <?php } else { ?>

                    <?php } ?>
                <?php } ?>
            <?php } else { ?>
                <tr>
                    <td>
                        <input class="form-check-input" type="checkbox" id="formCheck1">
                    </td>
                    <td>
                        <select class="form-control filter-pegawai" id="_filter_pegawai_1" name="_filter_pegawai[]" data-id="1" onchange="changePegawai(this)" required>
                            <option value="">&nbsp;</option>
                        </select>
                        <script>
                            $('#_filter_pegawai_1').select2({
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
                                placeholder: 'Cari Pegawai',
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
                                $container.find(".select2-result-repository__description").text(repo.nip + " - " + repo.nama_instansi + " ( Kec. " + repo.nama_kecamatan + ")");

                                return $container;
                            }

                            function formatRepoSelection(repo) {
                                $(repo.element).attr('data-custom-nip', repo.nip);
                                $(repo.element).attr('data-custom-instansi', repo.nama_instansi);
                                $(repo.element).attr('data-custom-kecamatan', repo.nama_kecamatan);
                                return repo.nama || repo.text;
                            }
                        </script>
                    </td>
                    <td>
                        <input class="form-control" type="text" value="" id="nip_1" name="nip[]" readonly>
                    </td>
                    <td>
                        <input class="form-control" type="text" value="" id="instansi_1" name="instansi[]" readonly>
                    </td>
                    <td>
                        <input class="form-control" type="text" value="" id="kecamatan_1" name="kecamatan[]" readonly>
                    </td>
                    <td>
                        <input class="form-control" type="number" value="1" id="example-text-input">
                    </td>
                    <td>
                        <input class="form-control" type="number" value="1" id="example-text-input">
                    </td>
                    <td>
                        <input class="form-control" type="number" value="1" id="example-text-input">
                    </td>
                    <td>
                        <input class="form-control" type="number" value="1" id="example-text-input">
                    </td>
                    <td>
                        <button type="button" class="btn btn-primary btn-rounded waves-effect waves-light">+</button>
                    </td>
                </tr>
            <?php } ?>
        <?php } else { ?>
            <tr>
                <td>
                    <input class="form-check-input" type="checkbox" id="formCheck1">
                </td>
                <td>
                    <select class="form-control filter-pegawai" id="_filter_pegawai" name="_filter_pegawai" required>
                        <option value="">&nbsp;</option>
                    </select>
                    <script>
                        $('#_filter_pegawai').select2({
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
                            placeholder: 'Cari Pegawai',
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
                            $container.find(".select2-result-repository__description").text(repo.nip + " - " + repo.nama_instansi + " ( Kec. " + repo.nama_kecamatan + ")");

                            return $container;
                        }

                        function formatRepoSelection(repo) {
                            return repo.nama || repo.text;
                        }
                    </script>
                </td>
                <td>
                    <input class="form-control" type="text" value="Nip" id="example-text-input">
                </td>
                <td>
                    <input class="form-control" type="text" value="instansi" id="example-text-input">
                </td>
                <td>
                    <input class="form-control" type="text" value="kecamatan" id="example-text-input">
                </td>
                <td>
                    <input class="form-control" type="number" value="1" id="example-text-input">
                </td>
                <td>
                    <input class="form-control" type="number" value="1" id="example-text-input">
                </td>
                <td>
                    <input class="form-control" type="number" value="1" id="example-text-input">
                </td>
                <td>
                    <input class="form-control" type="number" value="1" id="example-text-input">
                </td>
                <td>
                    <button type="button" class="btn btn-primary btn-rounded waves-effect waves-light">+</button>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>

<script>
    function changePegawai(event) {
        const getId = $(event).data('id');
        const getNip = $('#_filter_pegawai_' + getId).find(':selected').data('custom-nip');
        const getInstansi = $('#_filter_pegawai_' + getId).find(':selected').data('custom-instansi');
        const getKecamatan = $('#_filter_pegawai_' + getId).find(':selected').data('custom-kecamatan');

        $('#nip_' + getId).val(getNip);
        // $('#nip_' + getId).val(getNip);
        // // $(event).removeAttr('style');
        // // $('.' + color).html('');

        // if (event.value !== "") {
        //     $.ajax({
        //         url: './getPengguna',
        //         type: 'POST',
        //         data: {
        //             id: event.value,
        //         },
        //         dataType: 'JSON',
        //         beforeSend: function() {
        //             $('div._pengguna-block').block({
        //                 message: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>'
        //             });
        //         },
        //         success: function(msg) {
        //             $('div._pengguna-block').unblock();
        //             if (msg.status == 200) {
        //                 let html = "";
        //                 html += '<option value="">--Pilih--</option>';
        //                 if (msg.data.length > 0) {
        //                     for (let step = 0; step < msg.data.length; step++) {
        //                         html += '<option value="';
        //                         html += msg.data[step].id;
        //                         html += '">';
        //                         html += msg.data[step].fullname;
        //                         html += ' (';
        //                         html += msg.data[step].email;
        //                         html += ')</option>';
        //                     }

        //                 }

        //                 $('.pengguna').html(html);
        //             }
        //         },
        //         error: function(data) {
        //             $('div._pengguna-block').unblock();
        //         }
        //     })
        // }
    }
</script>