<?php 
/* Создано в компании www.gateon.net
 * =================================================================
 * Интеркасса модуль OPENCART 2.3.x ПРИМЕЧАНИЕ ПО ИСПОЛЬЗОВАНИЮ
 * =================================================================
 *  Этот файл предназначен для Opencart 2.3.x
 *  www.gateon.net не гарантирует правильную работу этого расширения на любой другой
 *  версии Opencart, кроме Opencart 2.3.x
 *  данный продукт не поддерживает программное обеспечение для других
 *  версий Opencart.
 * =================================================================
*/
?>
<form action="<?php echo $action ?>" method="post" id="checkout">
    <input type="hidden" name="ik_am" value="<?php echo $ik_payment_amount; ?>"/>
    <input type="hidden" name="ik_co_id" value="<?php echo $ik_shop_id; ?>"/>
    <input type="hidden" name="ik_cur" value="<?php echo $ik_cur; ?>" />
    <input type="hidden" name="ik_desc" value="<?php echo $ik_payment_desc; ?>"/>
    <input type="hidden" name="ik_pm_no" value="<?php echo $ik_payment_id; ?>"/>
    <input type="hidden" name="ik_sign" value="<?php echo $ik_sign_hash; ?>"/>

</form>
<div class="buttons">
    <div class="right">
        <a onclick="document.forms['checkout'].submit()" class="btn btn-primary"><span><?php echo $button_confirm; ?></span></a>
    </div>
</div>