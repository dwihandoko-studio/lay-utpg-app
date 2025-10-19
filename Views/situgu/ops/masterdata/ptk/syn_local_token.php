<form id="formTarikDataLocalModalData" method="post">
    <div class="modal-body" style="padding-top: 0px; padding-bottom: 0px;">
        <div class="mb-3">
            <label for="_token" class="form-label">Token Api Local Dapodik</label>
            <input type="text" class="form-control token" <?= isset($data) ? ($data->token === NULL || $data->token === "" ? '' : 'value="' . $data->token . '"') : '' ?> id="_token" name="_token" placeholder="Token Api Dapodik Local..." onfocusin="inputFocus(this);" />
            <!-- <p style="padding: 5px 0px;">Silah isi Nomor Whatsapp dengan format: 08xxxxxxxxxx (Contoh: 081208120812)</p> -->
            <div class="help-block _token"></div>
        </div>
        <div id="result"></div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary waves-effect" data-bs-dismiss="modal">Close</button>
        <button type="button" onclick="getSyncDapoLocal(this)" class="btn btn-primary waves-effect waves-light">Tarik Data Semua PTK Dari Dapodik Local</button>
    </div>
</form>

<script>
    async function getSyncDapoLocal(event) {
        const token = document.getElementsByName('_token')[0].value;
        const npsn = '<?= isset($sekolah) ? $sekolah->npsn : '' ?>';

        if (token === "") {
            Swal.fire(
                'PERINGATAN!',
                "Token Api Dapodik tidak valid. Silahkan hubungi Operator Sekolah anda.",
                'warning'
            );
        }

        const url = "http://127.0.0.1:5774/WebService/getGtk?npsn=" + npsn;

        const headers = {
            'Authorization': 'Bearer ' + token,
            'Cache-Control': 'no-cache',
            'Postman-Token': generatePostmanToken(),
            'User-Agent': 'PostmanRuntime/7.49.0',
            'Accept': '*/*',
            'Accept-Encoding': 'gzip, deflate, br',
            'Connection': 'keep-alive',
            'Content-Type': 'application/json',
            'Cookie': 'killme=dont',
            'Origin': '127.0.0.1',

            // Header untuk manipulasi
            'X-Client-IP': 'localhost',
            'X-Client-Location': 'localhost',
            'X-Request-Source': 'Web-Browser',
            'X-Forwarded-For': '127.0.0.1',
            'X-Real-IP': '127.0.0.1',
            'X-Originating-IP': '127.0.0.1',
            'X-Remote-IP': '127.0.0.1',
            'X-Remote-Addr': '127.0.0.1',
            'Forwarded': 'for=127.0.0.1;host=localhost;proto=http',

            // Header khusus untuk aplikasi tertentu
            'Referer': 'http://127.0.0.1/',
            'Host': '127.0.0.1:5774'
        };

        try {
            const response = await fetch(url, {
                method: 'GET',
                headers: headers,
                mode: 'no-cors',
                credentials: 'include'
            });

            if (response.ok || response.type === 'opaque') {
                $('#result').html(`
                <div style="background: #d4edda; padding: 15px; border-radius: 5px;">
                    <h3 style="color: #155724;">✅ Request Berhasil Dikirim</h3>
                    <p><strong>Informasi Request:</strong></p>
                    <ul>
                        <li><strong>NPSN:</strong> ${npsn}</li>
                        <li><strong>Waktu:</strong> ${new Date().toLocaleString()}</li>
                    </ul>
                    <p><em>Note: Menggunakan mode no-cors - response body mungkin tidak dapat diakses</em></p>
                </div>
            `);
            }

        } catch (error) {
            $('#result').html(`
            <div style="background: #f8d7da; padding: 15px; border-radius: 5px;">
                <h3 style="color: #721c24;">❌ Error</h3>
            </div>
        `);
        }
    }

    // Generate random Postman-Token (mirip dengan yang di Postman)
    function generatePostmanToken() {
        const chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
        let token = '';
        for (let i = 0; i < 36; i++) {
            token += chars[Math.floor(Math.random() * chars.length)];
        }
        return token;
    }
</script>