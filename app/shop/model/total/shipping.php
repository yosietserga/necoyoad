<?php
class ModelTotalShipping extends Model {
	public function getTotal(&$total_data, &$total, &$taxes) {
		if ($this->cart->hasShipping() && $this->session->has('shipping_method') && $this->config->get('shipping_status')) {
			$total_data[] = array( 
        		'title'      => $this->session->get('shipping_method','title') . ':',
        		'text'       => $this->currency->format($this->session->get('shipping_method','cost')),
        		'value'      => $this->session->get('shipping_method','cost'),
				'sort_order' => $this->config->get('shipping_sort_order')
			);

			if ($this->session->has('shipping_method','tax_class_id')) {
				if (!isset($taxes[$this->session->get('shipping_method','tax_class_id')])) {
					$taxes[$this->session->get('shipping_method','tax_class_id')] = $this->session->get('shipping_method','cost') / 100 * $this->tax->getRate($this->session->get('shipping_method','tax_class_id'));
				} else {//TODO: obtener variable y recorrerla localmente
					$taxes[$this->session->get('shipping_method','tax_class_id')] += $this->session->get('shipping_method','cost') / 100 * $this->tax->getRate($this->session->get('shipping_method','tax_class_id'));
				}
			}
			
			$total += $this->session->get('shipping_method','cost');
		}			
	}
}
