<?php if ($instruction) { ?>
  <div class="well well-sm"><p><?php echo $instruction; ?></p></div>
<?php } ?>
<form action="<?php echo $action ?>" method="post" id="ik_checkout">
    <?php foreach ($parameters as $key => $value) { ?>
      <?php if (is_array($value)) { ?>
        <?php foreach ($value as $val) { ?>
          <input type="hidden" name="<?php echo $key; ?>" value="<?php echo $val; ?>"/>
        <?php } ?>
      <?php } else { ?>
        <input type="hidden" name="<?php echo $key; ?>" value="<?php echo $value; ?>"/>
      <?php } ?>
    <?php } ?>
</form>
<div class="buttons">
  <div class="pull-right">
    <input type="button" value="<?php echo $button_confirm; ?>" id="button-confirm" class="btn btn-primary" />
  </div>
<script type="text/javascript">
    $('#button-confirm').on('click', function() {    
        $.ajax({
            type: 'get',
            url: 'index.php?route=extension/payment/shoputils_ik/confirm',
            beforeSend: function() {
                $('#button-confirm').button('loading');
            },
            complete: function() {
                $('#button-confirm').button('reset');
            },
            success: function() {
				$.each($('#ik_checkout').find('input'), function(i, elm){
					elm.removeAttribute('disabled');
				});
				setTimeout(function(){
					document.forms['ik_checkout'].submit();
				}, 200);
           }
        });
    });
</script>
</div>
