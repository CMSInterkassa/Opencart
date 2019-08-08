<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid heading">
      <div class="pull-right">
        <?php if ($permission) { ?>
        <button type="submit" form="form-shoputils-ik" id="button_save" class="btn btn-primary"><i class="fa fa-save"></i> <?php echo $button_save; ?></button>
        <?php } ?>
        <a href="<?php echo $cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i> <?php echo $button_cancel; ?></a></div>
      <h1><img src="view/image/payment/interkassa_23x30.gif"> <?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <?php if ($error_warning) {
foreach ($error_warning as $value) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $value; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } } ?>
    <div class="panel panel-default">
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-shoputils-ik" class="form-horizontal">
          <ul class="nav nav-tabs">
            <li class="active"><a href="#tab-general" data-toggle="tab"><i class="fa fa-power-off"></i> <?php echo $tab_general; ?></a></li>
            <li><a href="#tab-settings" data-toggle="tab"><i class="fa fa-wrench"></i> <?php echo $tab_settings; ?></a></li>
            <li><a href="#tab-log" data-toggle="tab"><i class="fa fa-eye"></i> <?php echo $tab_log; ?></a></li>
            <li><a href="#tab-information" data-toggle="tab"><i class="fa fa-info-circle"></i> <?php echo $tab_information; ?></a></li>
          </ul>
          <div class="tab-content">
            <div class="tab-pane active" id="tab-general">
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-geo-zone"><?php echo $entry_geo_zone; ?></label>
                <div class="col-sm-10">
                  <select name="interkassa_geo_zone_id" id="input-geo-zone" class="form-control">
                    <option value="0"><?php echo $text_all_zones; ?></option>
                    <?php foreach ($geo_zones as $geo_zone) { ?>
                    <?php if ($geo_zone['geo_zone_id'] == $interkassa_geo_zone_id) { ?>
                    <option value="<?php echo $geo_zone['geo_zone_id']; ?>" selected="selected"><?php echo $geo_zone['name']; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $geo_zone['geo_zone_id']; ?>"><?php echo $geo_zone['name']; ?></option>
                    <?php } ?>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
                <div class="col-sm-10">
                  <select name="interkassa_status" id="input-status" class="form-control">
                    <?php if ($interkassa_status) { ?>
                    <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                    <option value="0"><?php echo $text_disabled; ?></option>
                    <?php } else { ?>
                    <option value="1"><?php echo $text_enabled; ?></option>
                    <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-sort-order"><?php echo $entry_sort_order; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="interkassa_sort_order" value="<?php echo $interkassa_sort_order; ?>"
                         placeholder="<?php echo $entry_sort_order; ?>" id="input-sort-order" class="form-control" />
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-minimal-order"><?php echo $entry_minimal_order; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="interkassa_total" value="<?php echo $interkassa_total; ?>"
                         placeholder="<?php echo $entry_minimal_order; ?>" id="input-minimal-order" class="form-control" />
                  <span class="help-block"><?php echo $help_minimal_order; ?></span>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-order-confirm-status"><?php echo $entry_order_confirm_status; ?></label>
                <div class="col-sm-10">
                  <select name="interkassa_order_status_confirm" id="input-order-confirm-status" class="form-control">
                    <?php foreach ($order_statuses as $order_status) { ?>
                    <?php if ($order_status['order_status_id'] == $interkassa_order_status_confirm) { ?>
                    <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                    <?php } ?>
                    <?php } ?>
                  </select>
                  <span class="help-block"><?php echo $help_order_confirm_status; ?></span>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-order-status"><?php echo $entry_order_status; ?></label>
                <div class="col-sm-10">
                  <select name="interkassa_order_status_success" id="input-order-status" class="form-control">
                    <?php foreach ($order_statuses as $order_status) { ?>
                    <?php if (!$order_status['order_status_id']) continue; ?>
                    <?php if ($order_status['order_status_id'] == $interkassa_order_status_success) { ?>
                    <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                    <?php } ?>
                    <?php } ?>
                  </select>
                  <span class="help-block"><?php echo $help_order_status; ?></span>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-order-fail-status"><?php echo $entry_order_fail_status; ?></label>
                <div class="col-sm-10">
                  <select name="interkassa_order_status_fail" id="input-order-fail-status" class="form-control">
                    <?php foreach ($order_statuses as $order_status) { ?>
                    <?php if (!$order_status['order_status_id']) continue; ?>
                    <?php if ($order_status['order_status_id'] == $interkassa_order_status_fail) { ?>
                    <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                    <?php } ?>
                    <?php } ?>
                  </select>
                  <span class="help-block"><?php echo $help_order_fail_status; ?></span>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_title; ?></label>
                <div class="col-sm-10">
                  <?php foreach ($oc_languages as $language) { ?>
                  <div class="input-group">
                    <?php $language_image = sprintf('language/%1$s/%1$s.png', $language['code']); ?>
                    <span class="input-group-addon"><img src="<?php echo $language_image; ?>" title="<?php echo $language['name']; ?>" /></span>
                    <input type="text" name="interkassa_langdata[<?php echo $language['language_id']; ?>][title]"
                           value="<?php echo !empty($interkassa_langdata[$language['language_id']]['title'])
                                  ? $interkassa_langdata[$language['language_id']]['title'] : $title_default[0]; ?>"
                           placeholder="<?php echo $entry_title; ?>" class="form-control" />
                  </div>
                  <?php } ?>
                  <span class="help-block"><?php echo $help_title; ?></span>
                </div>
              </div>
            </div><!-- </div id="tab-general"> -->

            <div class="tab-pane" id="tab-settings">
              <?php if ($permission) { ?>
              <div class="form-group required">
                <label class="col-sm-2 control-label" for="input-shop-id"><?php echo $entry_cashbox_id; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="interkassa_cashbox_id" value="<?php echo $interkassa_cashbox_id; ?>"
                         placeholder="<?php echo $entry_cashbox_id; ?>" id="input-shop-id" class="form-control" />
                  <span class="help-block"><?php echo $help_cashbox_id; ?></span>
                  <?php if ($error_cashbox_id) { ?>
                  <div class="text-danger"><?php echo $error_cashbox_id; ?></div>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-test-mode"><?php echo $entry_test_mode; ?></label>
                <div class="col-sm-10">
                  <select name="interkassa_test_mode" id="input-test-mode" class="form-control">
                    <?php foreach ($test_modes as $key => $title) { ?>
                    <?php if ($key == $interkassa_test_mode) { ?>
                    <option value="<?php echo $key; ?>"
                            selected="selected"><?php echo $title ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $key; ?>"><?php echo $title ?></option>
                    <?php } ?>
                    <?php } ?>
                  </select>
                  <span class="help-block"><?php echo $help_test_mode; ?></span>
                </div>
              </div>
              <div class="form-group required">
                <label class="col-sm-2 control-label" for="input-sign-hash"><?php echo $entry_secret_key; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="interkassa_secret_key" value="<?php echo $interkassa_secret_key; ?>"
                         placeholder="<?php echo $entry_secret_key; ?>" id="input-sign-hash" class="form-control" />
                  <span class="help-block"><?php echo $help_secret_key; ?></span>
                  <?php if ($error_secret_key) { ?>
                  <div class="text-danger"><?php echo $error_secret_key; ?></div>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group required">
                <label class="col-sm-2 control-label" for="input-sign-test-key"><?php echo $entry_test_key; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="interkassa_test_key" value="<?php echo $interkassa_test_key; ?>"
                         placeholder="<?php echo $entry_test_key; ?>" id="input-sign-test-key" class="form-control" />
                  <span class="help-block"><?php echo $help_test_key; ?></span>
                  <?php if ($error_test_key) { ?>
                  <div class="text-danger"><?php echo $error_test_key; ?></div>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="api-status"><?php echo $entry_api_enable; ?></label>
                <div class="col-sm-10">
                  <select name="interkassa_api_enable" id="api-status" class="form-control">
                    <?php if ($interkassa_api_enable) { ?>
                    <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                    <option value="0"><?php echo $text_disabled; ?></option>
                    <?php } else { ?>
                    <option value="1"><?php echo $text_enabled; ?></option>
                    <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                    <?php } ?>
                  </select>
                  <span class="help-block"><?php echo $help_test_mode; ?></span>
                </div>
              </div>
              <div class="form-group required">
                <label class="col-sm-2 control-label" for="input-test-key"><?php echo $entry_api_id; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="interkassa_api_id" value="<?php echo $interkassa_api_id; ?>"
                         placeholder="<?php echo $entry_api_id; ?>" id="input-api-id" class="form-control" />
                  <span class="help-block"><?php echo $help_api_id; ?></span>
                  <?php if ($error_api_id) { ?>
                  <div class="text-danger"><?php echo $error_api_id; ?></div>
                  <?php } ?>
                </div>
              </div>
              <div class="form-group required">
                <label class="col-sm-2 control-label" for="input-api-key"><?php echo $entry_api_key; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="interkassa_api_key" value="<?php echo $interkassa_api_key; ?>"
                         placeholder="<?php echo $entry_api_key; ?>" id="input-api-key" class="form-control" />
                  <span class="help-block"><?php echo $help_api_key; ?></span>
                  <?php if ($error_api_key) { ?>
                  <div class="text-danger"><?php echo $error_api_key; ?></div>
                  <?php } ?>
                </div>
              </div>
              <?php } ?>

              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-currency"><?php echo $entry_currency; ?></label>
                <div class="col-sm-10">
                  <select name="interkassa_currency" id="input-currency" class="form-control">
                    <?php foreach ($currencies as $key => $value) { ?>
                    <?php if ($key == $interkassa_currency) { ?>
                    <option value="<?php echo $key; ?>"
                            selected="selected"><?php echo $value; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                    <?php } ?>
                    <?php } ?>
                  </select>
                  <span class="help-block"><?php echo $help_currency; ?></span>
                </div>
              </div>
            </div><!-- </div id="tab-settings"> -->

            <div class="tab-pane" id="tab-log">
              <div class="form-group">
                <label class="col-sm-2 control-label" for="input-log"><?php echo $entry_log; ?></label>
                <div class="col-sm-8">
                  <input type="hidden" name="interkassa_log_filename" value="<?php echo $log_filename ?>" />
                  <input type="hidden" name="interkassa_version" value="<?php echo $version ?>" />
                  <select name="interkassa_log" id="input-log" class="form-control">
                    <?php foreach ($logs as $key => $value) { ?>
                    <?php if ($key == $interkassa_log) { ?>
                    <option value="<?php echo $key; ?>"
                            selected="selected"><?php echo $value; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                    <?php } ?>
                    <?php } ?>
                  </select>
                  <span class="help-block"><?php echo $help_log; ?></span>
                </div>
                <div class="col-sm-2">
                  <a class="btn btn-danger" id="button-clear"><i class="fa fa-eraser"></i> <?php echo $button_clear; ?></a>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_log_file; ?></label>
                <div class="col-sm-10">
                  <div class="well well-sm" style="height: 300px; overflow: auto;">
                    <pre id="pre-log" style="font-size:11px; min-height: 280px;"><?php foreach ($log_lines as $log_line) {echo $log_line;} ?></pre>
                  </div>
                  <span class="help-block"><?php echo $help_log_file; ?></span>
                </div>
              </div>
            </div><!-- </div id="tab-log"> -->

            <div class="tab-pane" id="tab-information">
              <div class="alert alert-success" style="padding: 30px 10px;"><i class="fa fa-check-circle"></i>
                <?php echo $text_info ?>
              </div>
              <div class="form-group">
                <span class="col-sm-12 help-block"><?php echo $text_info_content; ?></span>
              </div>
              <div class="form-group">
                <label class="col-sm-4 control-label" for="input-success_url"><?php echo $entry_success_url; ?></label>
                <div class="col-sm-8">
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-link"></i></span>
                    <input type="text" readonly="readonly" value="<?php echo $interkassa_success_url; ?>" id="input-success_url" class="form-control">
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-4 control-label" for="input-fail_url"><?php echo $entry_fail_url; ?></label>
                <div class="col-sm-8">
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-link"></i></span>
                    <input type="text" readonly="readonly" value="<?php echo $interkassa_fail_url; ?>" id="input-fail_url" class="form-control">
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-4 control-label" for="input-pending_url"><?php echo $entry_pending_url; ?></label>
                <div class="col-sm-8">
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-link"></i></span>
                    <input type="text" readonly="readonly" value="<?php echo $interkassa_pending_url; ?>" id="input-pending_url" class="form-control">
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-4 control-label" for="input-status_url"><?php echo $entry_callback_url; ?></label>
                <div class="col-sm-8">
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-link"></i></span>
                    <input type="text" readonly="readonly" value="<?php echo $interkassa_callback_url; ?>" id="input-status_url" class="form-control">
                  </div>
                </div>
              </div>
            </div><!-- </div id="tab-information"> -->
          </div><!-- </div class="tab-content"> -->
        </form>
      </div><!-- </div class="panel-body"> -->
    </div><!-- </div class="panel panel-default"> -->
  </div><!-- </div class="container-fluid"> -->
</div><!-- </div id="content"> -->
<script type="text/javascript"><!--
  $(document).delegate('#button-clear', 'click', function() {
    if (confirm('<?php echo $text_confirm; ?>')){
      $.ajax({
        url: '<?php echo $clear_log; ?>',
        type: 'post',
        dataType: 'json',
        beforeSend: function() {
          $('#button-clear').after('<img src="view/image/loading.gif" class="loading" style="padding-left: 5px;" />');
        },
        complete: function() {
          $('.loading').remove();
        },
        success: function(json) {
          $('.alert-success, .alert-danger').remove();

          if (json['error']) {
            $('#content > .container-fluid').before('<div class="alert alert-danger" style="display: none;"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + '</div>');
            $('.alert-danger').fadeIn('slow');
          }

          if (json['success']) {
            $('#content > .container-fluid').before('<div class="alert alert-success" style="display: none;"><i class="fa fa-check-circle"></i> ' + json['success'] + '</div>');

            $('#pre-log').empty();
            $('.alert-success').fadeIn('slow');
          }

          $('html, body').animate({ scrollTop: 0 }, 'slow');
        },
        error: function(xhr, ajaxOptions, thrownError) {
          alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
        }
      });
    }
  });
  //--></script>
<?php echo $footer; ?>
