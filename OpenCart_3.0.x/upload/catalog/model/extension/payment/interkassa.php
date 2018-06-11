<?php 
class ModelExtensionPaymentInterkassa extends Model
{
    public function getMethod($address, $total)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone
        WHERE geo_zone_id = '" . (int) $this->config->get('payment_interkassa_geo_zone_id')
            . "' AND country_id = '" . (int) $address['country_id'] . "' AND (zone_id = '" . (int) $address['zone_id'] . "' OR zone_id = '0')");

        if (floatval($this->config->get('payment_interkassa_total')) > 0 && floatval($this->config->get('payment_interkassa_total')) > floatval($total)) {
            $status = false;
        }
        elseif (!$this->config->get('payment_interkassa_currency')) {
            $status = false;
        }
        elseif (!$this->config->get('payment_interkassa_geo_zone_id')) {
            $status = true;
        } elseif ($query->num_rows) {
            $status = true;
        } else {
            $status = false;
        }

        $method_data = array();

        if ($status) {
            $title = $this->config->get('payment_interkassa_langdata');

            if(!empty($title[$this->config->get('config_language_id')]['title']))
                $l_title = $title[$this->config->get('config_language_id')]['title'];
            else
                $l_title = 'Interkassa';

            $method_data = array(
                'code'          => 'interkassa',
                'title'         => $l_title,
                'terms'         => '',
                'sort_order'    => $this->config->get('payment_interkassa_sort_order')
            );
        }
        return $method_data;
    }
}