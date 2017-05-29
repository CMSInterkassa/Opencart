<form action="<?php echo $action ?>" method="post" id="checkout">
    <input type="hidden" name="ik_am" value="<?php echo $ik_am; ?>"/>
    <input type="hidden" name="ik_co_id" value="<?php echo $ik_co_id; ?>"/>
    <input type="hidden" name="ik_cur" value="<?php echo $ik_cur; ?>" />
    <input type="hidden" name="ik_desc" value="<?php echo $ik_desc; ?>"/>
    <input type="hidden" name="ik_pm_no" value="<?php echo $ik_pm_no; ?>"/>
    <input type="hidden" name="ik_loc" value="<?php echo $ik_loc; ?>"/>
    <input type="hidden" name="ik_suc_u" value="<?php echo $ik_suc_u; ?>"/>
    <input type="hidden" name="ik_fal_u" value="<?php echo $ik_fal_u; ?>"/>
    <input type="hidden" name="ik_pnd_u" value="<?php echo $ik_pnd_u; ?>"/>
    <input type="hidden" name="ik_ia_u" value="<?php echo $ik_ia_u; ?>"/>
    <?php if($ik_pw_via) { ?>
		<input type="hidden" name="ik_pw_via" value="<?php echo $ik_pw_via; ?>"/>
    <?php } ?>
    <input type="hidden" name="ik_sign" value="<?php echo $ik_sign; ?>"/>
</form>
<div class="buttons">
    <div class="right">
        <a onclick="document.forms['checkout'].submit()" class="btn btn-primary"><span><?php echo $button_confirm; ?></span></a>
    </div>
</div>