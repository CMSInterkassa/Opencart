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
<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right">
                <button type="submit" form="ik" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
                <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
            <h1><?php echo $heading_title; ?></h1>
            <ul class="breadcrumb">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <div class="container-fluid">
        <?php if ($error_warning) { ?>
        <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php } ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
            </div>
            <div class="panel-body">
                <ul id="tabs" class="nav nav-tabs">
                    <li class="active"><a href="#tab_general" data-toggle="tab" aria-expanded="true"><?php echo $tab_general; ?></a></li>
                    <li><a href="#tab_log" data-toggle="tab" aria-expanded="false"><?php echo $tab_log; ?></a></li>
                </ul>
                <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="ik" class="form-horizontal">
                    <div class="tab-content">
                        <div id="tab_general" class="tab-pane active">
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="ikgateway_status"><?php echo $entry_status; ?></label>
                                <div class="col-sm-10"><select class="form-control" id="ikgateway_status" name="ikgateway_status">
                                        <?php if ($ikgateway_status) { ?>
                                        <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                                        <option value="0"><?php echo $text_disabled; ?></option>
                                        <?php } else { ?>
                                        <option value="1"><?php echo $text_enabled; ?></option>
                                        <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                                        <?php } ?>
                                    </select></div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="ikgateway_order_status_id"><?php echo $entry_order_status; ?></label>
                                <div class="col-sm-10"><select class="form-control" id="ikgateway_order_status_id" name="ikgateway_order_status_id">
                                        <?php foreach ($order_statuses as $order_status) { ?>
                                        <?php if (!isset($order_status['order_status_id'])) continue; ?>
                                        <?php if ($order_status['order_status_id'] == $ikgateway_order_status_id) { ?>
                                        <option value="<?php echo $order_status['order_status_id']; ?>"
                                                selected="selected"><?php echo $order_status['name']; ?></option>
                                        <?php } else { ?>
                                        <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                        <?php } ?>
                                        <?php } ?>
                                    </select></div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="ikgateway_geo_zone_id"><?php echo $entry_geo_zone; ?></label>
                                <div class="col-sm-10"><select class="form-control" id="ikgateway_geo_zone_id" name="ikgateway_geo_zone_id">
                                        <option value="0"><?php echo $text_all_zones; ?></option>
                                        <?php foreach ($geo_zones as $geo_zone) { ?>
                                        <?php if ($geo_zone['geo_zone_id'] == $ikgateway_geo_zone_id) { ?>
                                        <option value="<?php echo $geo_zone['geo_zone_id']; ?>"
                                                selected="selected"><?php echo $geo_zone['name']; ?></option>
                                        <?php } else { ?>
                                        <option value="<?php echo $geo_zone['geo_zone_id']; ?>"><?php echo $geo_zone['name']; ?></option>
                                        <?php } ?>
                                        <?php } ?>
                                    </select></div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="ikgateway_sort_order"><?php echo $entry_sort_order; ?></label>
                                <div class="col-sm-10"><input type="text" class="form-control" id="ikgateway_sort_order" name="ikgateway_sort_order"
                                           value="<?php echo $ikgateway_sort_order; ?>"
                                           size="1"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-12"><strong><?php echo $text_ik_parameters; ?></strong></div>
                            </div>
                            <div class="form-group required">
                                <label class="col-sm-2 control-label required" for="ikgateway_shop_id">
                                    <span data-toggle="tooltip" title="" data-original-title="<?php echo $entry_ik_shop_id_help; ?>"><?php echo $entry_ik_shop_id ?></span>
                                </label>
                                <div class="col-sm-10"><input type="text" id="ikgateway_shop_id" class="form-control" name="ikgateway_shop_id" value="<?php echo $ikgateway_shop_id; ?>"/>
                                    <?php if ($error_ik_shop_id) { ?>
                                    <span class="error"><?php echo $error_ik_shop_id; ?></span>
                                    <?php } ?></div>
                            </div>
                            <div class="form-group required">
                                <label class="col-sm-2 control-label required" for="ikgateway_sign_hash">
                                    <span data-toggle="tooltip" title="" data-original-title="<?php echo $entry_ik_sign_hash_help; ?>"><?php echo $entry_ik_sign_hash ?></span>
                                </label>
                                <div class="col-sm-10"><input type="text" id="ikgateway_sign_hash" class="form-control" name="ikgateway_sign_hash" value="<?php echo $ikgateway_sign_hash; ?>"/>
                                    <?php if ($error_ik_sign_hash) { ?>
                                    <span class="error"><?php echo $error_ik_sign_hash; ?></span>
                                    <?php } ?></div>
                            </div>
                            <div class="form-group required">
                                <label class="col-sm-2 control-label required" for="ikgateway_sign_test_key">
                                    <span data-toggle="tooltip" title="" data-original-title="<?php echo $entry_ik_sign_test_key_help; ?>"><?php echo $entry_ik_sign_test_key ?></span>
                                </label>
                                <div class="col-sm-10"><input type="text" id="ikgateway_sign_test_key" class="form-control" name="ikgateway_sign_test_key" value="<?php echo $ikgateway_sign_test_key; ?>"/>
                                    <?php if ($error_ik_sign_test_key) { ?>
                                    <span class="error"><?php echo $error_ik_sign_test_key; ?></span>
                                    <?php } ?></div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="ikgateway_test_mode">
                                    <span data-toggle="tooltip" title="" data-original-title="<?php echo $entry_ik_test_mode_help; ?>"><?php echo $entry_ik_test_mode ?></span>
                                </label>
                                <div class="col-sm-10"><?php if ($ikgateway_test_mode) { ?>
                                    <input type="radio" id="ikgateway_test_mode" name="ikgateway_test_mode" value="1" checked="checked" />
                                    <?php echo $text_yes; ?>
                                    <input type="radio" id="ikgateway_test_mode" name="ikgateway_test_mode" value="0" />
                                    <?php echo $text_no; ?>
                                    <?php } else { ?>
                                    <input type="radio" id="ikgateway_test_mode" name="ikgateway_test_mode" value="1" />
                                    <?php echo $text_yes; ?>
                                    <input type="radio" id="ikgateway_test_mode" name="ikgateway_test_mode" value="0" checked="checked" />
                                    <?php echo $text_no; ?>
                                    <?php } ?></div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="ikgateway_currency">
                                    <span data-toggle="tooltip" title="" data-original-title="<?php echo $entry_ik_currency_help; ?>"><?php echo $entry_ik_currency; ?></span>
                                </label>
                                <div class="col-sm-10"><select class="form-control" id="ikgateway_currency" name="ikgateway_currency">
                                        <?php foreach ($currencies as $currency) { ?>
                                        <?php if ($currency['code'] == $ikgateway_currency) { ?>
                                        <option value="<?php echo $currency['code']; ?>"
                                                selected="selected"><?php echo $currency['title']; ?></option>
                                        <?php } else { ?>
                                        <option value="<?php echo $currency['code']; ?>"><?php echo $currency['title']; ?></option>
                                        <?php } ?>
                                        <?php } ?>
                                    </select></div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <div class="help"><?php echo $text_ik_urls ?></div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label"><?php echo $entry_ik_success_url; ?></label>
                                <div class="col-sm-10"><?php echo $ikgateway_success_url; ?></div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label"><?php echo $entry_ik_fail_url; ?></label>
                                <div class="col-sm-10"><?php echo $ikgateway_fail_url; ?></div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label"><?php echo $entry_ik_pending_url; ?></label>
                                <div class="col-sm-10"><?php echo $ikgateway_pending_url; ?></div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label"><?php echo $entry_ik_status_url; ?></label>
                                <div class="col-sm-10"><?php echo $ikgateway_status_url; ?></div>
                            </div>
                        </div><!-- </div id="tab_general">  -->
                        <div id="tab_log" class="tab-pane">
                            <div class="form-group">
                            <label class="col-sm-2 control-label">
                                <span data-toggle="tooltip" title="" data-original-title="<?php echo $entry_ik_log_help; ?>"><?php echo $entry_ik_log; ?></span>
                            </label>
                            <label class="col-sm-2 control-label" for="ikgateway_log"><select class="form-control" id="ikgateway_log"name="ikgateway_log">
                                    <?php foreach ($logs as $key => $log) { ?>
                                    <?php if ($key == $ikgateway_log) { ?>
                                    <option value="<?php echo $key; ?>"
                                            selected="selected"><?php echo $log; ?></option>
                                    <?php } else { ?>
                                    <option value="<?php echo $key; ?>"><?php echo $log; ?></option>
                                    <?php } ?>
                                    <?php } ?>
                                </select>
                        </div>
                            <div class="form-group">
                            <label class="col-sm-2 control-label">
                                <span data-toggle="tooltip" title="" data-original-title="<?php echo $entry_log_file_help; ?>"><?php echo $entry_log_file; ?></span>
                            </label>
                            <div class="col-sm-10">
                                <div class="scrollbox" style="height:300px; width:700px">
                                    <pre style="font-size:11px;">
                                        <?php foreach ($log_lines as $log_line) { ?>
                                        <?php echo $log_line; ?>
                                        <?php } ?>
                                    </pre>
                                </div>
                            </div>
                        </div>
                        </div><!-- </div id="tab_log">  -->
                    </div><!-- </div class="tab-content">  -->
                </form>
            </div>
        </div>
    </div>
</div>
<?php echo $footer; ?>