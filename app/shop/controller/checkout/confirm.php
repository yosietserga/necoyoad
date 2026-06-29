<?php

class ControllerCheckoutConfirm extends Controller {

    private $error = [];

    public function index() {
        $this->session->clear('object_type');
        $this->session->clear('object_id');
        $this->session->clear('landing_page');

        $Url = new Url($this->registry);
        if ($this->config->get('config_store_mode') != 'store') {
            $this->redirect(HTTP_HOME);
        }
        $this->load->model('checkout/extension');
        if (!$this->cart->hasProducts() || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
            $this->session->set('message', 'No existe la cantidad solicitada disponible para el art&iacute;lo se&ntilde;alado con tres (3) asteriscos');
            if ($this->request->getQuery('resp') != 'json') $this->redirect(Url::createUrl("checkout/cart"));
        }

        if (!$this->customer->isLogged()) {
            $this->session->set('redirect', Url::createUrl("checkout/cart"));
            if ($this->request->getQuery('resp') != 'json') $this->redirect(Url::createUrl("account/login"));
        }

        if ($this->cart->hasShipping() && $this->session->get('shipping_methods') && !$this->request->hasPost('shipping_method')) {
            $this->session->set('message', $this->language->get('error_must_select_shipping_method'));
            if ($this->request->getQuery('resp') != 'json') $this->redirect(Url::createUrl("checkout/cart"));
        } else {
            $this->session->clear('shipping_address_id');
            $this->session->clear('shipping_methods');
            $this->session->clear('shipping_method');

            $this->tax->setZone($this->config->get('config_country_id'), $this->config->get('config_zone_id'));
        }
        /*
          if (!$this->session->has('payment_address_id')) {
          $this->redirect(Url::createUrl("checkout/payment"));
          }
          if (!$this->session->has('payment_method')) {
          $this->redirect(Url::createUrl("checkout/payment"));
          }
         */

        $total_data = [];
        $total = 0;
        $taxes = $this->cart->getTaxes();

        $sort_order = [];
        $results = $this->modelExtension->getExtensions('total');
        foreach ($results as $key => $value) {
            $sort_order[$key] = $this->config->get($value['key'] . '_sort_order');
        }
        array_multisort($sort_order, SORT_ASC, $results);
        foreach ($results as $result) {
            $this->load->model('total/' . $result['key']);

            $this->{'model_total_' . $result['key']}->getTotal($total_data, $total, $taxes);
        }
        $sort_order = [];
        foreach ($total_data as $key => $value) {
            $sort_order[$key] = $value['sort_order'];
        }
        array_multisort($sort_order, SORT_ASC, $total_data);

        $data = [];

        $data['store_name'] = $this->config->get('config_name');
        $data['store_url'] = $this->config->get('config_url');
        $data['customer_id'] = $this->customer->getId();
        $data['customer_group_id'] = $this->customer->getCustomerGroupId();
        $data['firstname'] = $this->customer->getFirstName();
        $data['lastname'] = $this->customer->getLastName();
        $data['email'] = $this->customer->getEmail();
        $data['telephone'] = $this->customer->getTelephone();

        $this->load->model('account/address');
        $this->load->model('account/customer');

        $shipping_address_id = ($this->session->has('shipping_address_id')) ? $this->session->get('shipping_address_id') : $this->request->getPost('shipping_address_id');
        if ($shipping_address_id) {
            $shipping_address = $this->modelAddress->getAddress($shipping_address_id);
            $data['shipping_company'] = $data['company'];
            $data['shipping_rif'] = $data['rif'];
            $data['shipping_firstname'] = $data['firstname'];
            $data['shipping_lastname'] = $data['lastname'];
            $data['shipping_address_1'] = $shipping_address['address_1'];
            $data['shipping_address_2'] = $shipping_address['address_2'];
            $data['shipping_city'] = $shipping_address['city'];
            $data['shipping_postcode'] = $shipping_address['postcode'];
            $data['shipping_zone'] = $shipping_address['zone'];
            $data['shipping_zone_id'] = $shipping_address['zone_id'];
            $data['shipping_country'] = $shipping_address['country'];
            $data['shipping_country_id'] = $shipping_address['country_id'];
            $data['shipping_address_format'] = $shipping_address['address_format'];
        } else {
            $data['shipping_company'] = $this->customer->getCompany();
            $data['shipping_rif'] = $this->customer->getRif();
            $data['shipping_firstname'] = $this->customer->getFirstName();
            $data['shipping_lastname'] = $this->customer->getLastName();
            $data['shipping_address_1'] = $this->request->getPost('shipping_address_1');
            $data['shipping_city'] = $this->request->getPost('shipping_city');
            $data['shipping_postcode'] = $this->request->getPost('shipping_postcode');
            $data['shipping_zone_id'] = $this->request->getPost('shipping_zone_id');
            $data['shipping_country_id'] = $this->request->getPost('shipping_country_id');
        }
        $data['shipping_method'] = $this->request->getPost('shipping_method');

        $payment_address_id = ($this->session->has('payment_address_id')) ? $this->session->get('payment_address_id') : $this->request->getPost('payment_address_id');
        if ($payment_address_id) {
            $payment_address = $this->modelAddress->getAddress($payment_address_id);
            $data['payment_company'] = $payment_address['company'];
            $data['payment_rif'] = $payment_address['rif'];
            $data['payment_firstname'] = $payment_address['firstname'];
            $data['payment_lastname'] = $payment_address['lastname'];
            $data['payment_address_1'] = $payment_address['address_1'];
            $data['payment_address_2'] = $payment_address['address_2'];
            $data['payment_city'] = $payment_address['city'];
            $data['payment_postcode'] = $payment_address['postcode'];
            $data['payment_zone'] = $payment_address['zone'];
            $data['payment_zone_id'] = $payment_address['zone_id'];
            $data['payment_country'] = $payment_address['country'];
            $data['payment_country_id'] = $payment_address['country_id'];
            $data['payment_telephone'] = $payment_address['telephone'];
            $data['payment_email'] = $payment_address['email'];
            $data['payment_address_format'] = $payment_address['address_format'];
        } else {
            $data['payment_company'] = $this->customer->getCompany();
            $data['payment_rif'] = $this->customer->getRif();
            $data['payment_firstname'] = $this->customer->getFirstName();
            $data['payment_lastname'] = $this->customer->getLastName();
            $data['payment_address_1'] = $this->request->getPost('payment_address_1');
            $data['payment_city'] = $this->request->getPost('payment_city');
            $data['payment_postcode'] = $this->request->getPost('payment_postcode');
            $data['payment_zone_id'] = $this->request->getPost('payment_zone_id');
            $data['payment_country_id'] = $this->request->getPost('payment_country_id');
        }

        $product_data = [];
        foreach ($this->cart->getProducts() as $product) {
            $option_data = [];
            foreach ($product['option'] as $option) {
                $option_data[] = array(
                    'product_option_value_id' => $option['product_option_value_id'],
                    'name' => $option['name'],
                    'value' => $option['value'],
                    'prefix' => $option['prefix']
                );
            }

            $product_data[] = array(
                'product_id' => $product['product_id'],
                'name' => $product['name'],
                'model' => $product['model'],
                'option' => $option_data,
                'attributes' => $result['attributes'],
                'download' => $product['download'],
                'quantity' => $product['quantity'],
                'price' => $product['price'],
                'total' => $product['total'],
                'tax' => $this->tax->getRate($product['tax_class_id'])
            );
        }

        $data['products'] = $product_data;
        $data['totals'] = $total_data;
        $data['comment'] = $this->session->get('comment');
        $data['total'] = $total;
        $data['language_id'] = $this->config->get('config_language_id');
        $data['currency_id'] = $this->currency->getId();
        $data['currency'] = $this->currency->getCode();
        $data['value'] = $this->currency->getValue($this->currency->getCode());

        if ($this->session->has('coupon')) {
            $this->load->model('checkout/coupon');
            $coupon = $this->modelCoupon->getCoupon($this->session->get('coupon'));
            if ($coupon) {
                $data['coupon_id'] = $coupon['coupon_id'];
            } else {
                $data['coupon_id'] = 0;
            }
        } else {
            $data['coupon_id'] = 0;
        }

        $data['ip'] = $this->request->server['REMOTE_ADDR'];
        $this->load->model('checkout/order');

        $order_id = $this->modelOrder->create($data);
        if ($order_id) {
            $this->session->set('order_id', $order_id);
            $this->modelOrder->confirm($order_id, $this->config->get('cheque_order_status_id'));
            if ($this->request->hasQuery('resp') && $this->request->getQuery('resp') === 'json') {
                $this->load->auto('json');
                $this->response->setOutput(Json::encode(array(
                    'order_id'=>$order_id
                )), $this->config->get('config_compression'));
            } else {
                $this->redirect(Url::createUrl('checkout/success', array('order_id' => $order_id)));
            }
        }
    }

}
