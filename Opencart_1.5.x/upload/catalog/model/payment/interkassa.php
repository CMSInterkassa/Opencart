<?php

class ModelPaymentInterkassa extends Model {
    public function getMethod($address) {
        $this->load->language('payment/interkassa');
            $method_data = array(
                'code' => 'interkassa',
                'title' => $this->language->get('text_title'),
                'description' => $this->language->get('text_description'),
                'sort_order' => $this->config->get('interkassa_sort_order')
            );
        return $method_data;
    }
}
?>