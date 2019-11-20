<?php
class ControllerPaymentInterkassa extends Controller {
    private $error = array();
    private $version = '2.0.ik';
    const MAX_LAST_LOG_LINES = 500;
    const FILE_NAME_LOG = 'interkassa.log';

    public function __construct($registry) {
        parent::__construct($registry);
        $this->load->language('payment/interkassa');
        $this->document->setTitle($this->language->get('heading_title'));
    }

    public function index() {
//        $data = $this->language->all();
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validate())) {
            $this->_trimData(array(
//                'shoputils_ik_shop_id',
//                'shoputils_ik_sign_hash',
//                'shoputils_ik_sign_test_key',
//                'shoputils_ik_minimal_order',
//                'shoputils_ik_maximal_order'
                'interkassa_cashbox_id',
                'interkassa_secret_key',
                'interkassa_test_key',
                'interkassa_api_key',
                'interkassa_api_id',
                'interkassa_total',
            ));
            $this->request->post['interkassa_total'] = str_replace(',', '.', $this->request->post['interkassa_total']);

//            $this->_replaceData(',', '.', array(
//                'shoputils_ik_minimal_order',
//                'shoputils_ik_maximal_order'
//            ));

            $this->load->model('setting/setting');
            $this->model_setting_setting->editSetting('interkassa', $this->request->post);
            $this->session->data['success'] = sprintf($this->language->get('text_success'), $this->language->get('heading_title'));

            $this->response->redirect($this->makeUrl('extension/payment'));

        }

        $this->load->model('localisation/currency');
        $this->load->model('localisation/geo_zone');
        $this->load->model('localisation/language');
        $this->load->model('localisation/order_status');

        $ik_currencies = array(
            ''    => $this->language->get('text_currency_auto'),
            'RUB' => $this->language->get('text_currency_rub'),
            'UAH' => $this->language->get('text_currency_uah'),
            'USD' => $this->language->get('text_currency_usd'),
            'EUR' => $this->language->get('text_currency_eur')
        );

//        $ik_lifetimes = array(
//            5     => $this->language->get('text_lifetime_5minuts'),
//            30    => $this->language->get('text_lifetime_30minuts'),
//            60    => $this->language->get('text_lifetime_1hour'),
//            1440  => $this->language->get('text_lifetime_1day'),
//            10080 => $this->language->get('text_lifetime_1weekly'),
//            43200 => $this->language->get('text_lifetime_30days')
//        );

        $permission = $this->validatePermission();
        if (!$permission ) {
            $this->error['warning'] = sprintf($this->language->get('error_permission'), $this->language->get('heading_title'));
        }

//        $server = isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1')) ? HTTPS_CATALOG : HTTP_CATALOG;
        if(!empty($this->session->data['success'])){
            $data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        }

        $data = $this->_setData(array(
            'heading_title',
            'button_save',
            'button_cancel',
            'button_clear',
            'tab_general',
            'tab_emails',
            'tab_settings',
            'tab_log',
            'tab_information',
            'lang',
            'text_confirm',
            'text_enabled',
            'text_disabled',
            'text_all_zones',
            'text_yes',
            'text_no',
            'text_info',
            'text_info_content',
//            'text_parameters',
            'entry_geo_zone',
            'entry_status',
            'entry_sort_order',
            'entry_minimal_order',
            'entry_maximal_order',
            'entry_order_status',
            'entry_order_confirm_status',
            'entry_order_fail_status',
//            'entry_laterpay_mode',
//            'entry_order_later_status',
            'entry_title',
//            'entry_instruction',

//            'entry_notify_customer_success',
//            'entry_mail_customer_success_subject',
//            'entry_mail_customer_success_content',
//            'entry_notify_customer_fail',
//            'entry_mail_customer_fail_subject',
//            'entry_mail_customer_fail_content',
//            'entry_notify_admin_success',
//            'entry_mail_admin_success_subject',
//            'entry_mail_admin_success_content',
//            'entry_notify_admin_fail',
//            'entry_mail_admin_fail_subject',
//            'entry_mail_admin_fail_content',

//            'entry_shop_id',
//            'entry_sign_hash',
//            'entry_sign_test_key',
            'entry_cashbox_id',
            'entry_secret_key',
            'entry_test_key',
            'entry_api_enable',
            'entry_api_id',
            'entry_api_key',

            'entry_test_mode',
            'entry_currency',
//            'entry_lifetime',
            'entry_success_url',
            'entry_fail_url',
            'entry_pending_url',
//            'entry_status_url',
            'entry_callback_url',

            'entry_log',
            'entry_log_file',

            'placeholder_instruction',

            'help_minimal_order',
            'help_maximal_order',
            'help_order_confirm_status',
            'help_order_status',
            'help_order_fail_status',
//            'help_laterpay_mode',
//            'help_order_later_status',
            'help_title',
//            'help_instruction',
        'help_cashbox_id',
        'help_secret_key',
        'help_test_key',
        'help_test_mode',
        'help_currency',
        'help_log_file',
        'help_log',
        'help_api_id',
        'help_api_key',

            'help_notify_customer_success',
            'help_mail_customer_success_subject',
            'help_mail_customer_success_content',
            'help_notify_customer_fail',
            'help_mail_customer_fail_subject',
            'help_mail_customer_fail_content',
            'help_notify_admin_success',
            'help_mail_admin_success_subject',
            'help_mail_admin_success_content',
            'help_notify_admin_fail',
            'help_mail_admin_fail_subject',
            'help_mail_admin_fail_content',

            'help_shop_id',
            'help_sign_hash',
            'help_sign_test_key',
            'help_test_mode',
            'help_currency',
            'help_lifetime',

        'help_log_file' => sprintf($this->language->get('help_log_file'), self::MAX_LAST_LOG_LINES),
        'help_log' => sprintf($this->language->get('help_log'), self::FILE_NAME_LOG),
        'title_default' => explode(',', $this->language->get('heading_title')),
        'action' => $this->makeUrl('payment/interkassa'),
        'cancel' => $this->makeUrl('extension/payment'),
        'clear_log' => $this->makeUrl('payment/interkassa/clearLog'),
        'interkassa_success_url' => HTTPS_CATALOG . 'index.php?route=payment/interkassa/success',
        'interkassa_fail_url' => HTTPS_CATALOG . 'index.php?route=payment/interkassa/fail',
        'interkassa_pending_url' => HTTPS_CATALOG . 'index.php?route=payment/interkassa/success',
        'interkassa_callback_url' => HTTPS_CATALOG . 'index.php?route=payment/interkassa/callback',
        'permission' => $permission,
        'error_warning' => isset($this->error['warning']) ? $this->error['warning'] : '',
        'error_cashbox_id' => isset($this->error['error_cashbox_id']) ? $this->error['error_cashbox_id'] : '',
        'error_secret_key' => isset($this->error['error_secret_key']) ? $this->error['error_secret_key'] : '',
        'error_test_key' => isset($this->error['error_test_key']) ? $this->error['error_test_key'] : '',
        'error_api_id' => isset($this->error['error_api_id']) ? $this->error['error_api_id'] : '',
        'error_api_key' => isset($this->error['error_api_key']) ? $this->error['error_api_key'] : '',
        'version' => $this->version,
        'log_lines' => $this->readLastLines(DIR_LOGS . 'interkassa.log', self::MAX_LAST_LOG_LINES),
        'log_filename' => self::FILE_NAME_LOG,
        'currencies' => array_intersect_key($ik_currencies, $this->model_localisation_currency->getCurrencies()),
        'oc_languages' => $this->model_localisation_language->getLanguages(),
 ));

        $data['breadcrumbs'][] = array(
            'href'      => $this->makeUrl('common/dashboard'),
            'text'      => $this->language->get('text_home')
        );

        $data['breadcrumbs'][] = array(
            'href'      => $this->makeUrl('extension/payment'),
            'text'      => $this->language->get('text_extension')
        );

        $data['breadcrumbs'][] = array(
            'href'      => $this->makeUrl('payment/interkassa'),
            'text'      => $this->language->get('heading_title')
        );

        $data['logs'] = array(
            '0' => $this->language->get('text_log_off'),
            '1' => $this->language->get('text_log_short'),
            '2' => $this->language->get('text_log_full')
        );

        $data['test_modes'] = array(
            '0' => $this->language->get('text_disabled'),
            '1' => $this->language->get('text_enabled'),
        );

//        $data = array_merge($data, $this->_updateData(
//            array(
//                'shoputils_ik_geo_zone_id',
//                'shoputils_ik_sort_order',
//                'shoputils_ik_status',
//                'shoputils_ik_minimal_order',
//                'shoputils_ik_maximal_order',
//                'shoputils_ik_order_status_id',
//                'shoputils_ik_order_fail_status_id',
//                'shoputils_ik_order_confirm_status_id',
//                'shoputils_ik_langdata',
//
//                'shoputils_ik_shop_id',
//                'shoputils_ik_sign_hash',
//                'shoputils_ik_sign_test_key',
//                'shoputils_ik_test_mode',
//                'shoputils_ik_currency',
//                'shoputils_ik_lifetime',
//                'shoputils_ik_log'
//            ),
//            array()
//        ));
//        $data = array_merge($data, $this->_setData(
//            array(
//                'header'       => $this->load->controller('common/header'),
//                'column_left'  => $this->load->controller('common/column_left'),
//                'footer'       => $this->load->controller('common/footer')
//            )
//        ));
        $this->prepareSettings($data);

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('payment/interkassa.tpl', $data));
    }

    public function clearLog() {
        $json = array();

        if ($this->validatePermission()) {
            if (is_file(DIR_LOGS . self::FILE_NAME_LOG)) {
                @unlink(DIR_LOGS . self::FILE_NAME_LOG);
            }
            $json['success'] = $this->language->get('text_clear_log_success');
        } else {
            $json['error'] = $this->language->get('error_clear_log');
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function prepareSettings(&$data)
    {
        $this->load->model('localisation/order_status');
        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
        $this->load->model('localisation/geo_zone');
        $data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();
        $list_settings = array(
            'interkassa_status',
            'interkassa_sort_order',
            'interkassa_geo_zone_id',
            'interkassa_total',
            'interkassa_order_status_confirm',
            'interkassa_order_status_success',
            'interkassa_order_status_fail',
            'interkassa_langdata',
            'interkassa_test_mode',
            'interkassa_cashbox_id',
            'interkassa_secret_key',
            'interkassa_test_key',
            'interkassa_api_enable',
            'interkassa_api_key',
            'interkassa_api_id',
            'interkassa_currency',
            'interkassa_log',
        );
        foreach ($list_settings as $setting) {
            if (isset($this->request->post[$setting])) {
                $data[$setting] = $this->request->post[$setting];
            } else {
                $data[$setting] = $this->config->get($setting);
            }
        }
        return $data;
    }

    protected function validate() {
        if (!$this->validatePermission()) {
            $this->error['warning'] = sprintf($this->language->get('error_permission'), $this->language->get('heading_title'));
        } else {
            if (!isset($this->request->post['interkassa_cashbox_id']) || !trim($this->request->post['interkassa_cashbox_id'])) {
                $this->error['warning'][]= $this->error['error_cashbox_id'] = sprintf($this->language->get('error_form'),
                        $this->language->get('entry_cashbox_id'), $this->language->get('tab_settings'));
            }
            if (!isset($this->request->post['interkassa_secret_key']) || !trim($this->request->post['interkassa_secret_key'])) {
                $this->error['warning'][]= $this->error['error_secret_key'] = sprintf($this->language->get('error_form'),
                        $this->language->get('entry_secret_key'), $this->language->get('tab_settings'));
            }
            if (!isset($this->request->post['interkassa_test_key']) || !trim($this->request->post['interkassa_test_key'])) {
                $this->error['warning'][]= $this->error['error_test_key'] = sprintf($this->language->get('error_form'),
                        $this->language->get('entry_test_key'), $this->language->get('tab_settings'));
            }

            if(!empty($this->request->post['interkassa_api_enable']) && $this->request->post['interkassa_api_enable'] !== null) {
                if (!isset($this->request->post['interkassa_api_id']) || !trim($this->request->post['interkassa_api_id'])) {
                    $this->error['warning'][] = $this->error['error_api_id'] = sprintf($this->language->get('error_form'),
                        $this->language->get('entry_api_id'), $this->language->get('tab_settings'));
                }
                if (!isset($this->request->post['interkassa_api_key']) || !trim($this->request->post['interkassa_api_key'])) {
                    $this->error['warning'][] = $this->error['error_api_key'] = sprintf($this->language->get('error_form'),
                        $this->language->get('entry_api_key'), $this->language->get('tab_settings'));
                }
            }
        }

        return !$this->error;
    }

    protected function _setData($values) {
        $data = array();
        foreach ($values as $key => $value) {
            if (is_int($key)) {
                $data[$value] = $this->language->get($value);
            } else {
                $data[$key] = $value;
            }
        }
        return $data;
    }
//    protected function _updateData($keys, $info = array()) {
//        $data = array();
//        foreach ($keys as $key) {
//            if (isset($this->request->post[$key])) {
//                $data[$key] = $this->request->post[$key];
//            } elseif (isset($info[$key])) {
//                $data[$key] = $info[$key];
//            } else {
//                $data[$key] = $this->config->get($key);
//            }
//        }
//        return $data;
//    }

    protected function validatePermission() {
        return $this->user->hasPermission('modify', 'payment/interkassa');
    }

    protected function _trimData($values) {
        foreach ($values as $value) {
            if (isset($this->request->post[$value])) {
                $this->request->post[$value] = trim($this->request->post[$value]);
            }
        }
    }

//    protected function _replaceData($search, $replace, $values) {
//        foreach ($values as $value) {
//            if (isset($this->request->post[$value])) {
//                $this->request->post[$value] = str_replace($search, $replace, $this->request->post[$value]);
//            }
//        }
//    }

    protected function makeUrl($route, $url = '') {
        return str_replace('&amp;', '&', $this->url->link($route, $url . '&token=' . $this->session->data['token'], 'SSL'));
    }

    protected function readLastLines($filename, $lines) {
        if (!is_file($filename)) {
            return array();
        }
        $handle = @fopen($filename, "r");
        if (!$handle) {
            return array();
        }
        $linecounter = $lines;
        $pos = -1;
        $beginning = false;
        $text = array();

        while ($linecounter > 0) {
            $t = " ";

            while ($t != "\n") {
                /* if fseek() returns -1 we need to break the cycle*/
                if (fseek($handle, $pos, SEEK_END) == -1) {
                    $beginning = true;
                    break;
                }
                $t = fgetc($handle);
                $pos--;
            }

            $linecounter--;

            if ($beginning) {
                rewind($handle);
            }

            $text[$lines - $linecounter - 1] = fgets($handle);

            if ($beginning) {
                break;
            }
        }
        fclose($handle);

        return array_reverse($text);
    }
}
?>