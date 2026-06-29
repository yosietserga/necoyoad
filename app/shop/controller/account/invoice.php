<?php

class ControllerAccountInvoice extends Controller {

    public function index() {
        $this->session->clear('object_type');
        $this->session->clear('object_id');
        $this->session->clear('landing_page');

        $Url = new Url($this->registry);
        if (!$this->customer->isLogged()) {
            if (isset($this->request->get['order_id'])) {
                $order_id = $this->request->get['order_id'];
            } else {
                $order_id = 0;
            }
            $this->session->set('redirect', Url::createUrl("account/invoice", array("order_id" => $order_id)));
            $this->redirect(Url::createUrl("account/login"));
        }

        $this->language->load('account/invoice');
        $this->load->model('account/customer');
        $customer_address = $this->modelCustomer->getCustomer($this->customer->getId());

        $this->document->title = $this->data['heading_title'] = $this->language->get('heading_title');

        $this->document->breadcrumbs = [];
        $this->document->breadcrumbs[] = array(
            'href' => $Url::createUrl("common/home"),
            'text' => $this->language->get('text_home'),
            'separator' => false
        );

        $this->document->breadcrumbs[] = array(
            'href' => $Url::createUrl("account/account"),
            'text' => $this->language->get('text_account'),
            'separator' => $this->language->get('text_separator')
        );

        $this->document->breadcrumbs[] = array(
            'href' => $Url::createUrl("account/history"),
            'text' => $this->language->get('text_history'),
            'separator' => $this->language->get('text_separator')
        );

        $this->document->breadcrumbs[] = array(
            'href' => $Url::createUrl("account/invoice", array("order_id" => $this->request->get['order_id'])),
            'text' => $this->language->get('text_invoice'),
            'separator' => $this->language->get('text_separator')
        );

        $this->load->model('account/order');

        if (isset($this->request->get['order_id'])) {
            $order_id = $this->request->get['order_id'];
        } else {
            $order_id = 0;
        }

        $order_info = $this->modelOrder->getOrder($order_id);

        if ($order_info) {
            $this->data['order_id'] = $this->request->get['order_id'];

            if ($order_info['invoice_id']) {
                $this->data['invoice_id'] = $order_info['invoice_prefix'] . $order_info['invoice_id'];
            } else {
                $this->data['invoice_id'] = '';
            }

            $this->data['email'] = $order_info['email'];
            $this->data['telephone'] = $order_info['telephone'];
            $this->data['fax'] = $order_info['fax'];

            if ($order_info['shipping_address_format']) {
                $format = $order_info['shipping_address_format'];
            } else {
                $format = "<div style='font-weight:bold;width:130px;float:left'>Raz&oacute;n Social:</div><div style='float:left'>{company}</div>
                        <div style='font-weight:bold;width:130px;float:left'>RIF:</div><div style='float:left'>{rif}</div>
                        <div style='font-weight:bold;width:130px;float:left'>Direcci&oacute;n:</div><div style='float:left'>{address_1}, {city}, {zone} - {country}</div>";
            }

            $find = array(
                '{company}',
                '{rif}',
                '{address_1}',
                '{city}',
                '{zone}',
                '{zone_code}',
                '{country}'
            );

            $replace = array(
                'company' => $customer_address['company'],
                'rif' => $customer_address['rif'],
                'address_1' => $order_info['shipping_address_1'],
                'city' => $order_info['shipping_city'],
                'zone' => $order_info['shipping_zone'],
                'zone_code' => $order_info['shipping_zone'],
                'country' => $order_info['shipping_country']
            );

            $this->data['shipping_address'] = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));

            $this->data['shipping_method'] = $order_info['shipping_method'];

            if ($order_info['payment_address_format']) {
                $format = $order_info['payment_address_format'];
            } else {
                $format = "<div style='font-weight:bold;width:130px;float:left'>Raz&oacute;n Social:</div><div style='float:left'>{company}</div>
                        <div style='font-weight:bold;width:130px;float:left'>RIF:</div><div style='float:left'>{rif}</div>
                        <div style='font-weight:bold;width:130px;float:left'>Direcci&oacute;n:</div><div style='float:left'>{address_1}, {city}, {zone} - {country}</div>
                        <div style='font-weight:bold;width:130px;float:left'>Tel&eacute;fono:</div><div style='float:left'>{telephone}</div>
                        <div style='font-weight:bold;width:130px;float:left'>Email:</div><div style='float:left'>{email}</div>";
            }

            $find = array(
                '{company}',
                '{rif}',
                '{address_1}',
                '{city}',
                '{zone}',
                '{zone_code}',
                '{country}',
                '{telephone}',
                '{email}'
            );

            $replace = array(
                'company' => $customer_address['company'],
                'rif' => $customer_address['rif'],
                'address_1' => $order_info['payment_address_1'],
                'city' => $order_info['payment_city'],
                'zone' => $order_info['payment_zone'],
                'zone_code' => $order_info['payment_zone_code'],
                'country' => $order_info['payment_country'],
                'telephone' => $customer_address['telephone'],
                'email' => $customer_address['email']
            );

            $this->data['payment_address'] = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));

            $this->data['payment_method'] = $order_info['payment_method'];

            $this->data['products'] = [];

            $products = $this->modelOrder->getOrderProducts($this->request->get['order_id']);

            foreach ($products as $product) {
                $options = $this->modelOrder->getOrderOptions($this->request->get['order_id'], $product['order_product_id']);

                $option_data = [];

                foreach ($options as $option) {
                    $option_data[] = array(
                        'name' => $option['name'],
                        'value' => $option['value'],
                    );
                }

                $this->data['products'][] = array(
                    'name' => $product['name'],
                    'model' => $product['model'],
                    'option' => $option_data,
                    'quantity' => $product['quantity'],
                    'price' => $this->currency->format($product['price'], $order_info['currency'], $order_info['value']),
                    'total' => $this->currency->format($product['total'], $order_info['currency'], $order_info['value'])
                );
            }

            $this->data['totals'] = $this->modelOrder->getOrderTotals($this->request->get['order_id']);
            $this->data['comment'] = $order_info['comment'];
            $this->data['historys'] = [];

            $results = $this->modelOrder->getOrderHistories($this->request->get['order_id']);

            foreach ($results as $result) {
                $this->data['historys'][] = array(
                    'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
                    'status' => $result['status'],
                    'comment' => nl2br($result['comment'])
                );
            }

            $this->data['continue'] = Url::createUrl("account/history");

            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/account/invoice.tpl')) {
                $this->template = $this->config->get('config_template') . '/account/invoice.tpl';
            } else {
                $this->template = 'choroni/account/invoice.tpl';
            }

            // style files
            $csspath = defined("CDN_CSS") ? CDN_CSS : HTTP_CSS;
            str_replace('%theme%', $this->config->get('config_template'), HTTP_THEME_CSS);
            if (file_exists(str_replace('%theme%', $this->config->get('config_template'), HTTP_THEME_CSS) . 'neco.form.css')) {
                $styles[] = array('media' => 'all', 'href' => str_replace('%theme%', $this->config->get('config_template'), HTTP_THEME_CSS) . 'neco.form.css');
            } else {
                $styles[] = array('media' => 'all', 'href' => $csspath . 'neco.form.css');
            }
            $this->styles = array_merge($styles, $this->styles);

            // javascript files
            $jspath = defined("CDN_JS") ? CDN_JS : HTTP_JS;
            $javascripts[] = $jspath . "necojs/neco.form.js";
            $this->javascripts = array_merge($this->javascripts, $javascripts);

            // SCRIPTS
            $scripts[] = array('id' => 'messageScripts', 'method' => 'ready', 'script' =>
                "$('#form').ntForm({
            ajax:true,
            url:'{$this->data['action']}',
            success:function(data) {
                if (data.success) {
                    window.location.href = '" . Url::createUrl('account/message') . "';
                }
                if (data.error) {
                    $('#messageForm').append(data.msg);
                }
            }
        });
        
        $('#form textarea').ntInput();
        ");

            $this->scripts = array_merge($this->scripts, $scripts);
        } else {
            $this->data['heading_title'] = $this->language->get('heading_title');
            $this->data['continue'] = $Url::createUrl("account/history");

            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/error/not_found.tpl')) {
                $this->template = $this->config->get('config_template') . '/error/not_found.tpl';
            } else {
                $this->template = 'choroni/error/not_found.tpl';
            }
        }

        $this->session->set('landing_page','account/invoice');
        $this->loadWidgets('featuredContent');
        $this->loadWidgets('main');
        $this->loadWidgets('featuredFooter');

        $this->addChild('account/column_left');
            $this->addChild('common/column_left');
            $this->addChild('common/column_right');
            $this->addChild('common/header');
            $this->addChild('common/footer');



        $this->response->setOutput($this->render(true), $this->config->get('config_compression'));
    }

}
