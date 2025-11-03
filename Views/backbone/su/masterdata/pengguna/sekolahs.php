<div class="mb-3">
    <label for="_sekolah" class="col-form-label">SEKOLAH:</label>
    <select class="form-control select2 sekolah" id="_sekolah" name="_sekolah" style="width: 100%">
        <option value="">&nbsp;</option>
        <?php if (isset($sekolahs)) {
            if (count($sekolahs) > 0) {
                foreach ($sekolahs as $key => $value) { ?>
                    <option value="<?= $value->npsn ?>"><?= $value->nama ?> - (NPSN: <?= $value->npsn ?>)</option>
        <?php }
            }
        } ?>
    </select>
    <div class="help-block _sekolah"></div>
</div>
<script>
    initSelect2("_sekolah", '.content-detailModal');
</script>