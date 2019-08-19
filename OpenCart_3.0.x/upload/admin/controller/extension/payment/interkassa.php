<?php
class ControllerExtensionPaymentInterkassa extends Controller {
    private $error = array();
    private $version = '1.0';
    const MAX_LAST_LOG_LINES = 500;
    const FILE_NAME_LOG = 'interkassa.log';

    public function __construct($registry) {
        parent::__construct($registry);
        $this->load->language('extension/payment/interkassa');
        $this->document->setTitle($this->language->get('heading_title'));
    }

    public function index()
    {
        $data = $this->language->all();

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->_trimData(array(
                'payment_interkassa_cashbox_id',
                'payment_interkassa_secret_key',
                'payment_interkassa_test_key',
                'payment_interkassa_api_key',
                'payment_interkassa_api_id',
                'payment_interkassa_total',
            ));

            $this->request->post['payment_interkassa_total'] = str_replace(',', '.', $this->request->post['payment_interkassa_total']);

            $this->load->model('setting/setting');
            $this->model_setting_setting->editSetting('payment_interkassa', $this->request->post);
            $this->session->data['success'] = sprintf($this->language->get('text_success'), $this->language->get('heading_title'));

            $this->response->redirect($this->makeUrl('extension/payment/interkassa'));
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

        $permission = $this->validatePermission();
        if (!$permission ) {
            $this->error['warning'][] = sprintf($this->language->get('error_permission'), $this->language->get('heading_title'));
        }

        if(!empty($this->session->data['success'])){
            $data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        }

        $data['help_log_file'] = sprintf($this->language->get('help_log_file'), self::MAX_LAST_LOG_LINES);
        $data['help_log'] = sprintf($this->language->get('help_log'), self::FILE_NAME_LOG);
        $data['title_default'] = explode(',', $this->language->get('heading_title'));
        $data['action'] = $this->makeUrl('extension/payment/interkassa');
        $data['cancel'] = $this->makeUrl('marketplace/extension');
        $data['clear_log'] = $this->makeUrl('extension/payment/interkassa/clearLog');
        $data['interkassa_success_url'] = HTTPS_CATALOG . 'index.php?route=extension/payment/interkassa/success';
        $data['interkassa_fail_url'] = HTTPS_CATALOG . 'index.php?route=extension/payment/interkassa/fail';
        $data['interkassa_pending_url'] = HTTPS_CATALOG . 'index.php?route=extension/payment/interkassa/success';
        $data['interkassa_callback_url'] = HTTPS_CATALOG . 'index.php?route=extension/payment/interkassa/callback';
        $data['permission'] = $permission;
        $data['error_warning'] = isset($this->error['warning']) ? implode("<br>",$this->error['warning']) : '';
        $data['error_cashbox_id'] = isset($this->error['error_cashbox_id']) ? $this->error['error_cashbox_id'] : '';
        $data['error_secret_key'] = isset($this->error['error_secret_key']) ? $this->error['error_secret_key'] : '';
        $data['error_test_key'] = isset($this->error['error_test_key']) ? $this->error['error_test_key'] : '';
        $data['error_api_id'] = isset($this->error['error_api_id']) ? $this->error['error_api_id'] : '';
        $data['error_api_key'] = isset($this->error['error_api_key']) ? $this->error['error_api_key'] : '';
        $data['version'] = $this->version;
        $data['log_lines'] = $this->readLastLines(DIR_LOGS . 'interkassa.log', self::MAX_LAST_LOG_LINES);
        $data['log_filename'] = self::FILE_NAME_LOG;
        $data['currencies'] = array_intersect_key($ik_currencies, $this->model_localisation_currency->getCurrencies());

        $data['oc_languages'] = $this->model_localisation_language->getLanguages();

        $data['breadcrumbs'][] = array(
           'href'      => $this->makeUrl('common/dashboard'),
           'text'      => $this->language->get('text_home')
        );
        
        $data['breadcrumbs'][] = array(
           'href'      => $this->makeUrl('marketplace/extension'),
           'text'      => $this->language->get('text_extension')
        );
        
        $data['breadcrumbs'][] = array(
           'href'      => $this->makeUrl('extension/payment/interkassa'),
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

        $this->prepareSettings($data);

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/payment/interkassa', $data));
    }

    public function clearLog() {
      $json = array();

      if ($this->validatePermission()) {
          if (is_file(DIR_LOGS . self::FILE_NAME_LOG)) {
              unlink(DIR_LOGS . self::FILE_NAME_LOG);
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
            'payment_interkassa_status',
            'payment_interkassa_sort_order',
            'payment_interkassa_geo_zone_id',
            'payment_interkassa_total',
            'payment_interkassa_order_status_confirm',
            'payment_interkassa_order_status_success',
            'payment_interkassa_order_status_fail',
            'payment_interkassa_langdata',
            'payment_interkassa_test_mode',
            'payment_interkassa_cashbox_id',
            'payment_interkassa_secret_key',
            'payment_interkassa_test_key',
            'payment_interkassa_api_enable',
            'payment_interkassa_api_key',
            'payment_interkassa_api_id',
            'payment_interkassa_currency',
            'payment_interkassa_log',
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
            $this->error['warning'][] = sprintf($this->language->get('error_permission'), $this->language->get('heading_title'));
        } else {
            if (!isset($this->request->post['payment_interkassa_cashbox_id']) || !trim($this->request->post['payment_interkassa_cashbox_id'])) {
                $this->error['warning'][]= $this->error['error_cashbox_id'] = sprintf($this->language->get('error_form'),
                        $this->language->get('entry_cashbox_id'), $this->language->get('tab_settings'));
            }
            if (!isset($this->request->post['payment_interkassa_secret_key']) || !trim($this->request->post['payment_interkassa_secret_key'])) {
                $this->error['warning'][]= $this->error['error_secret_key'] = sprintf($this->language->get('error_form'),
                        $this->language->get('entry_secret_key'), $this->language->get('tab_settings'));
            }
            if (!isset($this->request->post['payment_interkassa_test_key']) || !trim($this->request->post['payment_interkassa_test_key'])) {
                $this->error['warning'][]= $this->error['error_test_key'] = sprintf($this->language->get('error_form'),
                        $this->language->get('entry_test_key'), $this->language->get('tab_settings'));
            }

            if(!empty($this->request->post['payment_interkassa_api_enable']) && $this->request->post['payment_interkassa_api_enable'] !== null) {
                if (!isset($this->request->post['payment_interkassa_api_id']) || !trim($this->request->post['payment_interkassa_api_id'])) {
                    $this->error['warning'][] = $this->error['error_api_id'] = sprintf($this->language->get('error_form'),
                        $this->language->get('entry_api_id'), $this->language->get('tab_settings'));
                }
                if (!isset($this->request->post['payment_interkassa_api_key']) || !trim($this->request->post['payment_interkassa_api_key'])) {
                    $this->error['warning'][] = $this->error['error_api_key'] = sprintf($this->language->get('error_form'),
                        $this->language->get('entry_api_key'), $this->language->get('tab_settings'));
                }
            }
        }

        return !$this->error;
    }

    protected function validatePermission() {
        return $this->user->hasPermission('modify', 'extension/payment/interkassa');
    }

    protected function _trimData($values) {
        foreach ($values as $value) {
            if (isset($this->request->post[$value])) {
                $this->request->post[$value] = trim($this->request->post[$value]);
            }
        }
    }

    protected function makeUrl($route, $url = '') {
        return str_replace('&amp;', '&', $this->url->link($route, $url . '&user_token=' . $this->session->data['user_token'], 'SSL'));
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