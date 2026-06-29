<?php

class ControllerAccountNewsletter extends Controller {

    public function index() {
        $this->session->clear('object_type');
        $this->session->clear('object_id');
        $this->session->clear('landing_page');

        $Url = new Url($this->registry);
        if (!$this->customer->isLogged()) {
            $this->session->set('redirect', Url::createUrl("account/newsletter"));

            $this->redirect(Url::createUrl("account/login"));
        }

        $this->language->load('account/newsletter');

        $this->document->title = $this->language->get('heading_title');

        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            $this->load->model('account/customer');
            $this->modelCustomer->editNewsletter($this->request->post['newsletter']);
            $this->session->set('success', $this->language->get('text_success'));
            $this->redirect(Url::createUrl("account/account"));
        }

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
            'href' => $Url::createUrl("account/newsletter"),
            'text' => $this->language->get('text_newsletter'),
            'separator' => $this->language->get('text_separator')
        );

        $this->data['heading_title'] = $this->language->get('heading_title');

        $this->data['action'] = Url::createUrl("account/newsletter");

        $this->data['newsletter'] = $this->customer->getNewsletter();

        $this->data['back'] = Url::createUrl("account/account");

        

        $this->session->set('landing_page','account/newsletter');
        $this->loadWidgets('featuredContent');
        $this->loadWidgets('main');
        $this->loadWidgets('featuredFooter');

        $this->addChild('account/column_left');
            $this->addChild('common/column_left');
            $this->addChild('common/column_right');
            $this->addChild('common/header');
            $this->addChild('common/footer');



        $template = ($this->config->get('default_view_account_newsletter')) ? $this->config->get('default_view_account_newsletter') : 'account/newsletter.tpl';
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/' . $template)) {
            $this->template = $this->config->get('config_template') . '/' . $template;
        } else {
            $this->template = 'choroni/' . $template;
        }

        $this->response->setOutput($this->render(true), $this->config->get('config_compression'));
    }

}
