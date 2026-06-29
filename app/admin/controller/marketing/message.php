<?php

class ControllerMarketingMessage extends Controller {

    private $error = [];

    public function index() {
        $this->document->title = $this->language->get('heading_title');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->load->auto('store/store');
            $stores = $this->modelStore->getAll();
            if (count($stores)) {
                foreach($stores as $store) {
                    $this->modelSetting->update('marketing', $this->request->post, $store['store_id']);
                }
            }
            $this->modelSetting->update('marketing', $this->request->post);
            $this->session->set('success', $this->language->get('text_success'));
        }

        if ($this->session->has('success')) {
            $this->data['success'] = $this->session->get('success');

            $this->session->clear('success');
        } else {
            $this->data['success'] = '';
        }

        $this->data['newsletters'] = $this->modelNewsletter->getAll();
        $this->data['pages'] = $this->modelPage->getAll();
        $this->load->auto('setting/setting');
        foreach ($this->modelSetting->getSetting('mail_server') as $id => $result) {
            $this->data['mail_servers'][$id] = unserialize($result);
        }

        $this->setvar('marketing_email_new_payment');
        $this->setvar('marketing_email_new_order');
        $this->setvar('marketing_email_order_pdf');
        $this->setvar('marketing_email_update_order');
        $this->setvar('marketing_email_old_order');

        $this->setvar('marketing_email_send_password_and_welcome');
        $this->setvar('marketing_email_new_customer');
        $this->setvar('marketing_email_activate_customer');
        $this->setvar('marketing_email_new_password');
        $this->setvar('marketing_email_happy_birthday');
        $this->setvar('marketing_email_add_balance');
        $this->setvar('marketing_email_subtract_balance');

        $this->setvar('marketing_email_recommended_products');
        $this->setvar('marketing_email_promote_product');
        $this->setvar('marketing_email_invite_friends');

        $this->setvar('marketing_email_new_comment');
        $this->setvar('marketing_email_new_reply');
        $this->setvar('marketing_email_new_contact');
        

        $this->setvar('marketing_mailserver_new_payment');
        $this->setvar('marketing_mailserver_new_order');
        $this->setvar('marketing_mailserver_order_pdf');
        $this->setvar('marketing_mailserver_update_order');
        $this->setvar('marketing_mailserver_old_order');

        $this->setvar('marketing_mailserver_send_password_and_welcome');
        $this->setvar('marketing_mailserver_new_customer');
        $this->setvar('marketing_mailserver_activate_customer');
        $this->setvar('marketing_mailserver_new_password');
        $this->setvar('marketing_mailserver_happy_birthday');
        $this->setvar('marketing_mailserver_add_balance');
        $this->setvar('marketing_mailserver_subtract_balance');

        $this->setvar('marketing_mailserver_recommended_products');
        $this->setvar('marketing_mailserver_promote_product');
        $this->setvar('marketing_mailserver_invite_friends');

        $this->setvar('marketing_mailserver_new_comment');
        $this->setvar('marketing_mailserver_new_reply');
        $this->setvar('marketing_mailserver_new_contact');
        

        $this->document->breadcrumbs = [];

        $this->document->breadcrumbs[] = array(
            'href' => Url::createAdminUrl('common/home'),
            'text' => $this->language->get('text_home'),
            'separator' => false
        );

        $this->document->breadcrumbs[] = array(
            'href' => Url::createAdminUrl('marketing/message'),
            'text' => $this->language->get('heading_title'),
            'separator' => ' :: '
        );

        $this->document->title = $this->data['heading_title'] = $this->language->get('heading_title');

        $template = ($this->config->get('default_admin_view_message')) ? $this->config->get('default_admin_view_message') : 'marketing/message.tpl';
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_admin_template') . '/'. $template)) {
            $this->template = $this->config->get('config_admin_template') . '/' . $template;
        } else {
            $this->template = 'default/' . $template;
        }

        $this->children[] = 'common/header';
        $this->children[] = 'common/nav';
        $this->children[] = 'common/footer';
        
        $this->response->setOutput($this->render(true), $this->config->get('marketing_compression'));
    }

    /**
     * ControllerMarketingNewsletter::activate()
     * duplicar un objeto
     * @return boolean
     */
    public function activate() {
        $result = 1;
        $this->load->auto('marketing/newsletter');
        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            foreach ($this->request->post['selected'] as $id) {
                $this->modelNewsletter->activate($id);
            }
        } else {
            $result = $this->modelNewsletter->toggleStatus($_GET['id']);
        }
        echo $result;
    }

    private function validate() {
        if (!$this->user->hasPermission('modify', 'marketing/message')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

}
