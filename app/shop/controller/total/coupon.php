<?php

class ControllerTotalCoupon extends Controller
{
    public function index()
    {
        if ($this->config->get('coupon_status')) {
            $this->load->language('total/coupon');

            if ($this->session->has('coupon')) {
                $data['coupon'] = $this->session->get('coupon');
            } else {
                $data['coupon'] = '';
            }



            if ($scripts)
                $this->scripts = array_merge($this->scripts, $scripts);

            return $this->load->view('total/coupon', $data);
        }
    }

    public function coupon()
    {
        $Url = new Url($this->registry);
        $json = [];
        $this->load->auto('total/coupon');
        $this->load->auto('json');

        $coupon_info = $this->modelCoupon->getCoupon($this->request->getPost('coupon'));

        if ($coupon_info) {
            $this->session->set('coupon', $this->request->getPost('coupon'));
            $this->session->set('coupon_token', md5($this->request->getPost('coupon') . CRYPT_KEY));
            $json['redirect'] = $Url->createUrl('checkout/cart');
        } else {
            $this->session->clear('coupon');
            $json['error'] = $this->language->get('error_coupon');
        }

        $this->response->setOutput(Json::encode($json));
    }
}
