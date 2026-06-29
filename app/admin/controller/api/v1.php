<?php

class ControllerApiV1 extends Controller {
	function __call($func, $params) {
		//TODO: get hooks from database
		$routes = array(
    		//localisation
			'countries'=> array(),
			'languages'=> array(),
			'currencies'=> array(),
			'zones'=> array(),
			'geo_zones'=> array(),
			'tax_classes'=> array(),
			'weight_classes'=> array(),
			'length_classes'=> array(),
			'order_statuses'=> array(),
			'payment_statuses'=> array(),
			'stock_statuses'=> array(),

    		//content
			'banners'=> array(),
			'banner_items'=> array(),
			'files'=> array(),
			'menus'=> array(),
			'pages'=> array(),
			'posts'=> array(),
			'post_categories'=> array(),

			//marketing
			'bounces'=> array(),
			'campaigns'=> array(),
			'contacts'=> array(),
			'contact_lists'=> array(),
			'mailservers'=> array(),
			'messages'=> array(),
			'newsletters'=> array(),

			//sale
			'balances'=> array(),
			'banks'=> array(),
			'bank_accounts'=> array(),
			'customers'=> array(),
			'customer_groups'=> array(),
			'orders'=> array(),
			'payments'=> array(),

    		//store
			'attributes'=> array(),
			'categories'=> array(),
			'downloads'=> array(),
			'manufacturers'=> array(),
			'products'=> array(),
			'reviews'=> array(),
			'stores'=> array(),

    		//style
			'templates'=> array(),
			'template_files'=> array(),
			'themes'=> array(),
			'views'=> array(),
			'widgets'=> array(),

    		//user
			'users'=> array(),
			'user_groups'=> array(),

    		//settings
			'settings'=> array(),
			'extension'=> array(),

    		//admin dashboard
			'adminmenu'=> array(
				'path'=>'admin/',
				'file'=>'menu'
			)
		);

		if (in_array($func, array_keys($routes))) {
			$route = $routes[$func];
			$path = !isset($route['path']) || empty($route['path']) ? '' : $route['path'];
			$file = !isset($route['file']) || empty($route['file']) ? $func : $route['file'];
			if (file_exists(dirname(__FILE__) .'/v1.0.0/'. $path . $file .'.php'))
				$this->proxy($path . $file); 
			else 
				$this->error404();
		} else {
			$this->error503();
		}
	}

    private function prepareData($object, $data) {
        require("v1.0.0/{$object}_data.php");
        return $return;
    }

    private function proxy($object) {
        $this->load->auto('json');
        if (!$this->validateTokens()) {
            $this->error503();
            return;
        }
		//TODO: define a contant to validate that token has approved
        require("v1.0.0/{$object}.php");
        $this->response->setOutput(Json::encode($return), $this->config->get('config_compression'));
    }

    private function validateTokens() {
        //TODO: check public and private API keys, tokens, expiry time, access level, license, premium member, etc.
        return true;
    }

    private function error503() {
        $this->load->auto('json');
        header("HTTP/1.0 503 Prohibited Access", true, 503);
        //header("Status Code: 503");
        $return = [];

        $return['status'] = array(
            'code'=>503,
            'message'=>'PROHIBITED ACCESS'
        );

        $return['error'] = array(
            'code'=>503,
            'message'=>'HTTP 503: Prohibited Access'
        );

        $this->response->setOutput(Json::encode($return), $this->config->get('config_compression'));
    }

    private function error404() {
        $this->load->auto('json');
        header("HTTP/1.0 404 Not Found", true, 404);
        //header("Status Code: 404");
        $return = [];

        $return['status'] = array(
            'code'=>404,
            'message'=>'PAGE NOT FOUND'
        );

        $return['error'] = array(
            'code'=>404,
            'message'=>'HTTP 404: Page Not Found'
        );

        $this->response->setOutput(Json::encode($return), $this->config->get('config_compression'));
    }
}