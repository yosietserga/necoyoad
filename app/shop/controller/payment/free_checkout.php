<?php

class ControllerPaymentFreeCheckout extends Controller {

	protected function index() {
		$this->language->load('payment/free_checkout');
		$this->load->library('image');
        $this->data['Image'] = new NTImage;
        
        $this->load->model("marketing/newsletter");
        $result = $this->modelNewsletter->getById($this->config->get('free_checkout_newsletter_id'));
        $this->data['instructions'] = html_entity_decode($result['htmlbody']);
                
        // style files
        $csspath = defined("CDN") ? CDN.CSS : HTTP_CSS;
            
        $styles[] = array('media'=>'all','href'=>$csspath.'jquery-ui/jquery-ui.min.css');
        $styles[] = array('media'=>'all','href'=>$csspath.'neco.form.css');
            
        if (count($styles)) {
            $this->data['styles'] = $this->styles = array_merge($this->styles,$styles);
        }



		$this->id = 'payment';

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/payment/free_checkout.tpl')) {
            $this->template = $this->config->get('config_template') . '/payment/free_checkout.tpl';
		} else {
            $this->template = 'choroni/payment/free_checkout.tpl';
        }
		
		$this->render();		 
	}
	
	public function confirm() {
		$this->load->model('checkout/order');
		$this->model_checkout_order->confirm($this->session->data['order_id'], $this->config->get('free_checkout_order_status_id'));
	}
}