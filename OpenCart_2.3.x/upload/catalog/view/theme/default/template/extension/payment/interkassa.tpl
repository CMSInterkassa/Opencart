<div id="interkassa_error" style="color: red"></div>
<form name="payment_interkassa" id="InterkassaForm" action="javascript:selpayIK.selPaysys()" method="POST">
    <?php foreach ($formData as $key => $value) { ?>
    <input type="hidden" name="<?php echo $key; ?>" value="<?php echo $value; ?>"/>
    <?php } ?>
    <div class="buttons">
        <div class="pull-right">
            <button id="ik_button" class="btn btn-primary"><?php echo $button_confirm; ?></button>
        </div>
    </div>
</form>

<?php if (!empty($payment_systems)) {
    include_once 'catalog/view/theme/default/template/extension/payment/interkassa_pay_systems.tpl';
} ?>


<script type="text/javascript" src="/catalog/view/javascript/interkassa.js"></script>
<script type="text/javascript">
    $("#ik_button").on('click', function () {
        $.post('index.php?route=extension/payment/interkassa/confirm', 'flag=1', function (json) {
            $('#interkassa_error').html('');
            if (json != undefined) {
                selpayIK.actForm = '<?php echo $action; ?>';
                $('#InterkassaForm').submit();
            } else {
                $('#interkassa_error').html(json);
            }
        }).done(function () {
            $('#ik_button').button('loading');
        });
        return false;
    })
</script>