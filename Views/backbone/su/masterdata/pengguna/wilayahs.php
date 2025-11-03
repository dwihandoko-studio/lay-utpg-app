<div class="mb-3">
    <label for="_wilayah" class="col-form-label">Pilih Wilayah:</label>
    <select class="form-control select2 ptk" id="_wilayah" name="_wilayah" onchange="changeWilayah(this)" style="width: 100%">
        <option value="">&nbsp;</option>
        <?php if (isset($wilayahs)) {
            if (count($wilayahs) > 0) {
                foreach ($wilayahs as $key => $value) { ?>
                    <option value="<?= $value->kode_kecamatan ?>"><?= $value->nama_kecamatan ?></option>
        <?php }
            }
        } ?>
    </select>
    <div class="help-block _wilayah"></div>
</div>
<script>
    initSelect2("_wilayah", '.content-detailModal');
</script>