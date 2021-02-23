<script type="text/javascript">

window.editor.on('asset:remove', function(asset) {
    let assetId = asset.attributes.public_id;
    $.ajax({
        type: "POST",
        url: "<?= phpb_url('pagebuilder', ['action' => 'upload_delete', 'page_id' => $page->getId()]) ?>",
        data: {
            id: assetId
        },
        success: function() {
        },
        error: function() {
        }
    });
});

</script>
