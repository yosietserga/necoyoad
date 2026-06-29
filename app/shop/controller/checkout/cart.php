<?php

class ControllerCheckoutCart extends Controller {

    public function index() {
        $this->language->load('checkout/cart');
        $this->session->clear('object_type');
        $this->session->clear('object_id');
        $this->session->clear('landing_page');
        
        $Url = new Url($this->registry);
        if ($this->config->get('config_store_mode') != 'store') {
            $this->redirect(HTTP_HOME);
        }
        
        $this->session->set('redirect', $Url::createUrl('checkout/cart'));

        $this->document->title = $this->data['heading_title'] = $this->language->get('heading_title');
        
        $this->document->breadcrumbs = [];
        $this->document->breadcrumbs[] = array(
            'href' => $Url::createUrl("common/home"),
            'text' => $this->language->get('text_home'),
            'separator' => false
        );

        $this->document->breadcrumbs[] = array(
            'href' => $Url::createUrl("checkout/cart"),
            'text' => $this->language->get('text_basket'),
            'separator' => $this->language->get('text_separator')
        );
        $this->data['breadcrumbs'] = $this->document->breadcrumbs;

        $this->session->set('landing_page','checkout/cart');
        $this->loadWidgets('featuredContent');
        $this->loadWidgets('main');
        $this->loadWidgets('featuredFooter');

        $this->addChild('common/column_left');
        $this->addChild('common/column_right');
        $this->addChild('common/header');
        $this->addChild('common/footer');
        
        $template = ($this->config->get('default_view_checkout_cart')) ? $this->config->get('default_view_checkout_cart') : 'checkout/cart.tpl';
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/' . $template)) {
            $this->template = $this->config->get('config_template') . '/' . $template;
        } else {
            $this->template = 'choroni/' . $template;
        }
        
        $this->response->setOutput($this->render(true), $this->config->get('config_compression'));
    }

    public function refresh() {
        if ($this->config->get('config_store_mode') != 'store') {
            $this->redirect(Url::createUrl('common/home'));
        }
        $this->cart->update($this->request->get['key'], $this->request->get['quantity']);
        $this->response->setOutput($this->updateCart(), $this->config->get('config_compression'));
    }

    public function delete() {
        if ($this->config->get('config_store_mode') != 'store') {
            $this->redirect(Url::createUrl('common/home'));
        }
        $this->cart->remove($_GET['key']);
        $this->response->setOutput($this->updateCart(), $this->config->get('config_compression'));
    }

    protected function updateCart() {
        $this->load->auto('weight');
        $this->load->auto('cart');
        $this->load->auto('json');
        $this->load->auto('checkout/extension');
        $data = [];
        if ($this->cart->getProducts()) {
            if ($this->config->get('config_cart_weight')) {
                $data['weight'] = $this->weight->format($this->cart->getWeight(), $this->config->get('config_weight_class'));
            } else {
                $data['weight'] = false;
            }

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
            $output = "";
            foreach ($total_data as $value) {
                $output .= "<tr>";
                $output .= "<td><b>" . $value['title'] . "</b></td>";
                $output .= "<td>" . $value['text'] . "</td>";
                $output .= "</tr>";
            }
            $data['totals'] = $output;
        } else {
            $data['error'] = "No hay productos en el carrito";
        }
        return Json::encode($data);
    }

}
