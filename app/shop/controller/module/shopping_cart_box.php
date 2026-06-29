<?php

require_once(DIR_CONTROLLER . "module/modulecontroller.php");

class ControllerModuleShoppingCartBox extends ControllerModuleModuleController
{
    protected string $moduleName = 'shopping_cart_box';
    protected array $defaults = [];

    public function init()
    {
        $this->addFilter("module:settings", function ($data) {
            $settings = $data['settings'];
            $widget   = $data['widget'];
            $render   = $data['render'];

            $query_data = [];

            if ($this->config->get('config_store_mode') == 'store') {
                $this->load->auto('currency');
                $this->load->auto('tax');

                $Url = new Url($this->registry);

                $this->session->clear('shipping_methods');
                $this->session->clear('shipping_method');
                $this->session->clear('payment_methods');
                $this->session->clear('payment_method');

                if ($this->request->server['REQUEST_METHOD'] == 'POST') {

                    if ($this->request->hasPost('remove')) {
                        $result = explode('_', $this->request->getPost('remove'));
                        $this->cart->remove(trim($result[1]));
                    } else {
                        if ($this->request->hasPost('option')) {
                            $option = $this->request->getPost('option');
                        } else {
                            $option = [];
                        }

                        $this->cart->add($this->request->getPost('product_id'), $this->request->getPost('quantity'), $option);
                    }
                }

                $query_data['limit'] = $this->request->hasQuery('limit') ?
                    $this->request->getQuery('limit') : ((isset($settings['limit']) && (int)$settings['limit']) ? (int)$settings['limit'] : 12);

                $query_data['page'] = $this->request->hasQuery('page') ? $this->request->getQuery('page') : 1;
                $query_data['start'] = ($query_data['page'] - 1) * $query_data['limit'];

                $this->data['products'] = $this->cart->getProducts($query_data);

                $total_data = [];
                $total = 0;
                $taxes = $this->cart->getTaxes();

                $this->load->model('checkout/extension');

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

                $this->data['totals'] = $total_data;

                if (isset($this->data['settings']['show_pagination']) && $this->data['total_products']) {
                    $this->load->library('pagination');
                    $pagination = new Pagination(true);
                    $pagination->total = $this->cart->getProducts();
                    $pagination->page = $query_data['page'];
                    $pagination->limit = $query_data['limit'];
                    $pagination->text = $this->language->get('text_pagination');
                    $pagination->url = $Url::createUrl("module/shopping_cart_box") . '&page={page}&resp=json';
                    $pagination->ajax = true;
                    $pagination->ajaxTarget = "#{$widget['name']}_results";
                    $this->data['pagination'] = $pagination->render();
                }

                $this->data['settings'] = $settings;

                if ($this->request->getQuery('resp') == 'json') {
                    $query_data['payload'] = array(
                        'results' => $this->data['products'],
                        'pagination' => $this->data['pagination']
                    );

                    $this->load->library('json');
                    $this->response->setOutput(Json::encode($query_data), $this->config->get('config_compression'));
                    exit;
                }
            }

            return [
                'widget'   => $widget,
                'render'   => $render,
                'settings' => $settings,
            ];
        });
    }
}