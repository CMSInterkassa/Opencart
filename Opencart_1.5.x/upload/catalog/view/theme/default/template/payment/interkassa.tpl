<form action="<?php echo $action ?>" accept-charset="utf-8" method="post" id="checkout">
    <input type="hidden" name="ik_co_id" value="<?php echo $ik_co_id; ?>"/>
    <input type="hidden" name="ik_pm_no" value="<?php echo $ik_pm_no; ?>"/>
    <input type="hidden" name="ik_desc" value="<?php echo $ik_desc; ?>"/>
    <input type="hidden" name="ik_am" value="<?php echo $ik_am; ?>"/>
    <input type="hidden" name="ik_cur" value="<?php echo $ik_cur; ?>"/>

    <input type="hidden" name="ik_ia_u" value="<?php echo $ik_ia_u; ?>"/>
    <input type="hidden" name="ik_suc_u" value="<?php echo $ik_suc_u; ?>"/>
    <input type="hidden" name="ik_fal_u" value="<?php echo $ik_fal_u; ?>"/>
    <input type="hidden" name="ik_pnd_u" value="<?php echo $ik_pnd_u; ?>"/>
    <input type="hidden" name="ik_sign" value="<?php echo $ik_sign; ?>"/>
</form>
<div class="buttons">
    <div class="right">
        <a onclick="window.open(\'https://interkassa.com/\');"><img src="https://www.interkassa.com/img/logo.png"  style="max-width: 150px;width: 150px; height: auto; margin: 0 auto; display: block;" align="center" alt="interkassa"></a>
        <a onclick="document.forms['checkout'].submit()" class="button"><span><?php echo $button_confirm; ?></span></a>
    </div>
</div>