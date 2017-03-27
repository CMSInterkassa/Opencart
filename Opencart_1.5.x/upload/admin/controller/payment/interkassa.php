<?php

class ControllerPaymentInterkassa extends Controller
{
    private $error = array();

    public function index()
    {

        $this->load->model('setting/setting');
        $this->language->load('payment/interkassa');
        $this->document->setTitle($this->language->get('heading_title'));

        if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validate()) {
            $this->model_setting_setting->editSetting('interkassa', $this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            $this->redirect(HTTPS_SERVER . 'index.php?route=extension/payment&token=' . $this->session->data['token']);
        }

        $this->data['heading_title'] = $this->language->get('heading_title');
        $this->data['button_save'] = $this->language->get('button_save');
        $this->data['button_cancel'] = $this->language->get('button_cancel');
        $this->data['entry_status'] = $this->language->get('entry_status');
        $this->data['entry_sort_order'] = $this->language->get('entry_sort_order');
        $this->data['entry_order_status'] = $this->language->get('entry_order_status');
        $this->data['entry_order_status_failed'] = $this->language->get('entry_order_status_failed');
        $this->data['entry_interkassa_id'] = $this->language->get('entry_interkassa_id');
        $this->data['entry_interkassa_secret_key'] = $this->language->get('entry_interkassa_secret_key');
        $this->data['entry_interkassa_test_key'] = $this->language->get('entry_interkassa_test_key');
        $this->data['tab_general'] = $this->language->get('tab_general');
        $this->data['text_edit'] = $this->language->get('text_edit');
        $this->data['text_enabled'] = $this->language->get('text_enabled');
        $this->data['text_disabled'] = $this->language->get('text_disabled');
        $this->data['text_yes'] = $this->language->get('text_yes');
        $this->data['text_no'] = $this->language->get('text_no');
        $this->data['action'] = $this->url->link('payment/interkassa', 'token=' . $this->session->data['token'], true);
        $this->data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'] . '&type=payment', true);


        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'href' => $this->url->link('common/home', '&token=' . $this->session->data['token'], 'SSL'),
            'text' => $this->language->get('text_home'),
            'separator' => FALSE
        );

        $this->data['breadcrumbs'][] = array(
            'href' => $this->url->link('payment', '&token=' . $this->session->data['token'], 'SSL'),
            'text' => $this->language->get('text_payment'),
            'separator' => ' :: '
        );

        $this->data['breadcrumbs'][] = array(
            'href' => $this->url->link('payment/interkassa', '&token=' . $this->session->data['token'], 'SSL'),
            'text' => $this->language->get('heading_title'),
            'separator' => ' :: '
        );



        if (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
        }

        if (isset($this->error['interkassa_id'])) {
            $this->data['error_interkassa_id'] = $this->error['interkassa_id'];
        } else {
            $this->data['error_interkassa_id'] = '';
        }

        if (isset($this->error['interkassa_secret_key'])) {
            $this->data['error_interkassa_secret_key'] = $this->error['interkassa_secret_key'];
        } else {
            $this->data['error_interkassa_secret_key'] = '';
        }
        if (isset($this->error['interkassa_test_key'])) {
            $this->data['error_interkassa_test_key'] = $this->error['interkassa_test_key'];
        } else {
            $this->data['error_interkassa_test_key'] = '';
        }

        if (isset($this->request->post['interkassa_sort_order'])) {
            $this->data['interkassa_sort_order'] = $this->request->post['interkassa_sort_order'];
        } elseif ($this->config->get('interkassa_sort_order')) {
            $this->data['interkassa_sort_order'] = $this->config->get('interkassa_sort_order');
        } else {
            $this->data['interkassa_sort_order'] = "0";
        }

        if (isset($this->request->post['interkassa_id'])) {
            $this->data['interkassa_id'] = $this->request->post['interkassa_id'];
        } elseif ($this->config->get('interkassa_id')) {
            $this->data['interkassa_id'] = $this->config->get('interkassa_id');
        } else {
            $this->data['interkassa_id'] = "";
        }

        if (isset($this->request->post['interkassa_secret_key'])) {
            $this->data['interkassa_secret_key'] = $this->request->post['interkassa_secret_key'];
        } elseif ($this->config->get('interkassa_secret_key')) {
            $this->data['interkassa_secret_key'] = $this->config->get('interkassa_secret_key');
        } else {
            $this->data['interkassa_secret_key'] = "";
        }

        if (isset($this->request->post['interkassa_test_key'])) {
            $this->data['interkassa_test_key'] = $this->request->post['interkassa_test_key'];
        } elseif ($this->config->get('interkassa_test_key')) {
            $this->data['interkassa_test_key'] = $this->config->get('interkassa_test_key');
        } else {
            $this->data['interkassa_test_key'] = "";
        }

        if (isset($this->request->post['interkassa_order_failed_status_id'])) {
            $this->data['interkassa_order_failed_status_id'] = $this->request->post['interkassa_order_failed_status_id'];
        } elseif ($this->config->get('interkassa_order_failed_status_id')) {
            $this->data['interkassa_order_failed_status_id'] = $this->config->get('interkassa_order_failed_status_id');
        } else {
            $this->data['interkassa_order_failed_status_id'] = "7";
        }

        if (isset($this->request->post['interkassa_order_status_id'])) {
            $this->data['interkassa_order_status_id'] = $this->request->post['interkassa_order_status_id'];
        } elseif ($this->config->get('interkassa_order_status_id')) {
            $this->data['interkassa_order_status_id'] = $this->config->get('interkassa_order_status_id');
        } else {
            $this->data['interkassa_order_status_id'] = "";
        }

        if (isset($this->request->post['interkassa_status'])) {
            $this->data['interkassa_status'] = $this->request->post['interkassa_status'];
        } elseif ($this->config->get('interkassa_status')) {
            $this->data['interkassa_status'] = $this->config->get('interkassa_status');
        } else {
            $this->data['interkassa_status'] = "";
        }

        $this->load->model('localisation/order_status');
        $this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

        $this->template = 'payment/interkassa.tpl';
        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render());

    }

    private function validate()
    {
        if (!$this->user->hasPermission('modify', 'payment/interkassa')) {
            $this->error['warning'] = $this->language->get('error_permission');
        } else {
            if (!isset($this->request->post['interkassa_secret_key']) || !$this->request->post['interkassa_secret_key']) {
                $this->error['warning'] = $this->language->get('error_interkassa_secret_key');
            }
            if (!isset($this->request->post['interkassa_test_key']) || !$this->request->post['interkassa_test_key']) {
                $this->error['warning'] = $this->language->get('error_interkassa_test_key');
            }
            if (!isset($this->request->post['interkassa_id']) || !$this->request->post['interkassa_id']) {
                $this->error['warning'] = $this->language->get('error_interkassa_id');
            }
        }
        if (!$this->error) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
}