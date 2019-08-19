<!--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" crossorigin="anonymous">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" crossorigin="anonymous"></script>-->
<link rel="stylesheet" href="/catalog/view/theme/default/stylesheet/interkassa.css" crossorigin="anonymous">
<div class="interkasssa" style="text-align: center;">
    <?php if (is_array($payment_systems)) { ?>
    <button type="button" class="sel-ps-ik btn btn-info btn-lg" data-toggle="modal" data-target="#InterkassaModal"
            style="display: none;">
        Select Payment Method
    </button>
    <div id="InterkassaModal" class="ik-modal fade" role="dialog">
        <div class="ik-modal-dialog ik-modal-lg">
            <div class="ik-modal-content" id="plans">
                <div class="container">
                    <h3>
                        1. <?php echo $text['text_select_payment_method']; ?><br>
                        2. <?php echo $text['text_select_currency']; ?><br>
                        3. <?php echo $text['text_press_pay']; ?><br>
                    </h3>

                    <div class="ik-row">
                        <?php foreach ($payment_systems as $ps => $info) { ?>
                        <div class="col-sm-3 text-center payment_system">
                            <div class="panel panel-warning panel-pricing">
                                <div class="panel-heading">
                                    <div class="panel-image">
                                        <img src="/catalog/view/theme/default/image/interkassa/images/<?php echo $ps; ?>.png"
                                             alt="<?php echo $info['title']; ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="input-group">
                                        <div class="radioBtn btn-group">
                                            <?php foreach ($info['currency'] as $currency => $currencyAlias) { ?>
                                            <a class="btn btn-primary btn-sm notActive"
                                               data-toggle="fun"
                                               data-title="<?php echo $currencyAlias; ?>"><?php echo $currency; ?></a>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel-footer">
                                    <a class="btn btn-lg btn-block ik-payment-confirmation"
                                       data-title="<?php //echo $ps; ?>"
                                       href="#"><?php echo $text['text_pay_through']; ?><br>
                                        <strong><?php echo $info['title']; ?></strong>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        var interkassa_lang = []
        interkassa_lang.error_selected_currency = "<?php echo $text['text_not_selected_currency']; ?>"
        interkassa_lang.something_wrong = "<?php echo $text['text_something_wrong']; ?>"
    </script>
    <?php } else { ?>
    <?php echo $payment_systems; ?>
    <?php } ?>
</div>
