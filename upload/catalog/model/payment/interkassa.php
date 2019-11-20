<?php

class ModelPaymentInterkassa extends Model
{
    public function getMethod($address, $total)
    {
        $this->load->language('payment/interkassa');

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone
        WHERE geo_zone_id = '" . (int)$this->config->get('interkassa_geo_zone_id')
            . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

        if (floatval($this->config->get('interkassa_total')) > 0 && floatval($this->config->get('interkassa_total')) > floatval($total)) {
            $status = false;
        } elseif (!$this->config->get('interkassa_currency')) {
            $status = false;
        } elseif (!$this->config->get('interkassa_geo_zone_id')) {
            $status = true;
        } elseif ($query->num_rows) {
            $status = true;
        } else {
            $status = false;
        }
        $method_data = array();

        if ($status) {
            $title = $this->config->get('interkassa_langdata');
            if (!empty($title[$this->config->get('config_language_id')]['title']))
                $l_title = $title[$this->config->get('config_language_id')]['title'];
            else
                $l_title = 'Interkassa2.0';

            $server = isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1')) ? $this->config->get('config_ssl') : $this->config->get('config_url');
            $method_data = array(
                'code' => 'interkassa',
                'title' => $l_title.'<br>'.sprintf($this->language->get('text_description'), $server),
//                'description' => sprintf($this->language->get('text_description'), $server),
                'terms' => '',
                'sort_order' => $this->config->get('interkassa_sort_order')
            );
        }
        return $method_data;
    }
}

?>