<?php

class ControllerMarketingMailserver extends Controller
{

    private $error = [];

    public function index()
    {
        $this->document->title = $this->language->get('heading_title');
        $this->getList();
    }

    public function insert()
    {
        $this->document->title = $this->language->get('heading_title');
        if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validateForm()) {
            $data['server'] = $this->request->getPost('server');
            $data['port'] = $this->request->getPost('port');
            $data['security'] = $this->request->getPost('security');
            $data['username'] = $this->request->getPost('username');
            $data['password'] = $this->request->getPost('password');

            $mail_server_id = substr(md5(mt_rand(10000, 99999) . time()),0,10);

            $this->modelSetting->updateProperty('mail_server', $mail_server_id, serialize($data));

            if ($this->request->post['to'] == "saveAndKeep") {
                $this->redirect(Url::createAdminUrl('marketing/mailserver/update', array('mail_server_id' =>$mail_server_id)));
            } elseif ($this->request->post['to'] == "saveAndNew") {
                $this->redirect(Url::createAdminUrl('marketing/mailserver/insert'));
            } else {
                $this->redirect(Url::createAdminUrl('marketing/mailserver'));
            }
        } else {
            $this->getForm();
        }
    }

    public function update()
    {
        $this->load->auto('setting/setting');
        $this->document->title = $this->language->get('heading_title');
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm
            ()) {
            $this->session->set('success', $this->language->get('text_success'));

            $data['server'] = $this->request->getPost('server');
            $data['port'] = $this->request->getPost('port');
            $data['security'] = $this->request->getPost('security');
            $data['username'] = $this->request->getPost('username');
            $data['password'] = $this->request->getPost('password');

            $this->modelSetting->updateProperty('mail_server', $this->request->getQuery('mail_server_id'),
                serialize($data));

            if ($this->request->post['to'] == "saveAndKeep") {
                $this->redirect(Url::createAdminUrl('marketing/mailserver/update', array('mail_server_id' =>
                        $this->request->getQuery('mail_server_id'))));
            } elseif ($this->request->post['to'] == "saveAndNew") {
                $this->redirect(Url::createAdminUrl('marketing/mailserver/insert'));
            } else {
                $this->redirect(Url::createAdminUrl('marketing/mailserver'));
            }
        }
        $this->getForm();
    }

    /**
     * ControllerMarketingList::delete()
     * elimina un objeto
     * @return boolean
     * */
    public function delete()
    {
        //TODO: indicar que van a quedar las camapañas de marketing sin modo de envío
        $this->load->auto('setting/setting');
        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            foreach ($this->request->post['selected'] as $id) {
                $this->modelSetting->deleteProperty('mail_server', $id);
            }
        } else {
            $this->modelSetting->deleteProperty('mail_server', $id);
        }
    }

    private function getList()
    {
        $this->document->breadcrumbs = [];

        $this->document->breadcrumbs[] = array(
            'href' => Url::createAdminUrl('common/home'),
            'text' => $this->language->get('text_home'),
            'separator' => false);

        $this->document->breadcrumbs[] = array(
            'href' => Url::createAdminUrl('marketing/mailserver'),
            'text' => $this->language->get('heading_title'),
            'separator' => ' :: ');

        $this->document->title = $this->language->get('heading_title');

        if ($this->session->has('error')) {
            $this->data['error_warning'] = $this->session->get('error');

            $this->session->clear('error');
        } elseif (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
        }

        if ($this->session->has('success')) {
            $this->data['success'] = $this->session->get('success');

            $this->session->clear('success');
        } else {
            $this->data['success'] = '';
        }

        // SCRIPTS
        $scripts[] = array(
            'id' => 'campaignList',
            'method' => 'function',
            'script' => "function eliminar(e) {
                if (confirm('\\xbfDesea eliminar este objeto?')) {
                    $('#tr_' + e).remove();
                	$.getJSON('" . Url::createAdminUrl("marketing/mailserver/delete") .
                "',{
                        id:e
                    });
                }
                return false;
             }
            function deleteAll() {
                if (confirm('\\xbfDesea eliminar todos los objetos seleccionados?')) {
                    $('#gridWrapper').hide();
                    $('#gridPreloader').show();
                    $.post('" . Url::createAdminUrl("marketing/mailserver/delete") .
                "',$('#form').serialize(),function(){
                        $('#gridWrapper').load('" . Url::createAdminUrl("marketing/mailserver/grid") .
                "',function(){
                            $('#gridWrapper').show();
                            $('#gridPreloader').hide();
                        });
                    });
                }
                return false;
            }");
        $scripts[] = array(
            'id' => 'sortable',
            'method' => 'ready',
            'script' => "$('#gridWrapper').load('" . Url::createAdminUrl("marketing/mailserver/grid") .
                "',function(e){
                $('#gridPreloader').hide();
            });
                
            $('#formFilter').ntForm({
                lockButton:false,
                ajax:true,
                type:'get',
                dataType:'html',
                url:'" . Url::createAdminUrl("marketing/mailserver/grid") . "',
                beforeSend:function(){
                    $('#gridWrapper').hide();
                    $('#gridPreloader').show();
                },
                success:function(data){
                    $('#gridPreloader').hide();
                    $('#gridWrapper').html(data).show();
                }
            });
            $('#formFilter').on('keyup', function(e){
                var code = e.keyCode || e.which;
                if (code == 13){
                    $('#formFilter').ntForm('submit');
                }
            });");

        $this->scripts = array_merge($this->scripts, $scripts);

        $template = ($this->config->get('default_admin_view_mailserver_list')) ? $this->config->get('default_admin_view_mailserver_list') : 'marketing/mailserver_list.tpl';
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_admin_template') . '/'. $template)) {
            $this->template = $this->config->get('config_admin_template') . '/' . $template;
        } else {
            $this->template = 'default/' . $template;
        }

        $this->children[] = 'common/header';
        $this->children[] = 'common/nav';
        $this->children[] = 'common/footer';

        $this->response->setOutput($this->render(true), $this->config->get('config_compression'));
    }

    public function grid()
    {
        $this->load->auto('setting/setting');
        $results = $this->modelSetting->getSetting('mail_server');

        if ($results) {
            $i = str_replace('%theme%',$this->config->get('config_admin_template'),HTTP_ADMIN_THEME_IMAGE);
            foreach ($results as $id => $result) {
                $action = [];
                $result = unserialize($result);

                $action['edit'] = array(
                    'action' => 'edit',
                    'text' => $this->language->get('text_edit'),
                    'href' => Url::createAdminUrl('marketing/mailserver/update') .
                        '&mail_server_id=' . $id,
                    'img' =>  $i .'edit.png');

                $action['delete'] = array(
                    'action' => 'delete',
                    'text' => $this->language->get('text_delete'),
                    'href' => '',
                    'img' => $i.'delete.png');

                $result['action'] = $action;

                $this->data['servers'][$id] = $result;
            }
        }

        $template = ($this->config->get('default_admin_view_mailserver_grid')) ? $this->config->get('default_admin_view_mailserver_grid') : 'marketing/mailserver_grid.tpl';
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_admin_template') . '/'. $template)) {
            $this->template = $this->config->get('config_admin_template') . '/' . $template;
        } else {
            $this->template = 'default/' . $template;
        }

        $this->response->setOutput($this->render(true), $this->config->get('config_compression'));
    }

    public function getForm()
    {
        $this->document->breadcrumbs = [];
        $this->document->breadcrumbs[] = array(
            'href' => Url::createAdminUrl('common/home'),
            'text' => $this->language->get('text_home'),
            'separator' => false);
        $this->document->breadcrumbs[] = array(
            'href' => Url::createAdminUrl('marketing/mailserver'),
            'text' => $this->language->get('heading_title'),
            'separator' => ' :: ');

        if ($this->request->hasQuery('mail_server_id')) {
            $this->data['action'] = Url::createAdminUrl('marketing/mailserver/update') .
                "&amp;mail_server_id=" . $this->request->getQuery('mail_server_id');
        } else {
            $this->data['action'] = Url::createAdminUrl('marketing/mailserver/insert');
        }

        $this->data['cancel'] = Url::createAdminUrl('marketing/mailserver');

        $this->data['error_warning'] = isset($this->error['warning']) ? $this->error['warning'] :
            '';
        $this->data['error_name'] = isset($this->error['name']) ? $this->error['name'] :
            '';
        $this->data['error_description'] = isset($this->error['description']) ? $this->
            error['description'] : '';
        $this->data['error_lists'] = isset($this->error['lists']) ? $this->error['lists'] :
            '';
        $this->data['error_subject'] = isset($this->error['subject']) ? $this->error['subject'] :
            '';
        $this->data['error_from_name'] = isset($this->error['from_name']) ? $this->
            error['from_name'] : '';
        $this->data['error_from_email'] = isset($this->error['from_email']) ? $this->
            error['from_email'] : '';
        $this->data['error_replyto_email'] = isset($this->error['replyto_email']) ? $this->
            error['replyto_email'] : '';
        $this->data['error_bounce_email'] = isset($this->error['bounce_email']) ? $this->
            error['bounce_email'] : '';

        if ($this->request->hasQuery('mail_server_id')) {
            $info = $this->modelSetting->getProperty('mail_server', $this->request->getQuery('mail_server_id'));
        } else {
            $info = null;
        }
        
        $this->data['server'] = unserialize($info);

        $template = ($this->config->get('default_admin_view_mailserver_form')) ? $this->config->get('default_admin_view_mailserver_form') : 'marketing/mailserver_form.tpl';
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_admin_template') . '/'. $template)) {
            $this->template = $this->config->get('config_admin_template') . '/' . $template;
        } else {
            $this->template = 'default/' . $template;
        }

        $this->children[] = 'common/header';
        $this->children[] = 'common/nav';
        $this->children[] = 'common/footer';

        $this->response->setOutput($this->render(true), $this->config->get('config_compression'));
    }

    public function testConnection()
    {
        $this->load->auto('json');
        $json = [];
        if (!$this->request->hasQuery('server'))
            $json['error'] = 1;
        if (!$this->request->hasQuery('username'))
            $json['error'] = 1;
        if (!$this->request->hasQuery('password'))
            $json['error'] = 1;

        if (!isset($json['error'])) {
            $this->load->library('email/smtp');
            $this->load->library('email/mailer');
            $mailer = new Mailer;

            $mailer->IsSMTP();
            $mailer->Host = $this->request->getQuery('server');
            $mailer->Username = $this->request->getQuery('username');
            $mailer->Password = $this->request->getQuery('password');
            if ($this->request->hasQuery('port')) $mailer->Port = $this->request->getQuery('port');
            if ($this->request->hasQuery('security')) $mailer->SMTPSecure = $this->request->getQuery('security');
            $mailer->SMTPAuth = true;

            $result = $mailer->SmtpConnect();
            if ($result === true) {
                $json['success'] = 1;                    
            } else {
                $json['error'] = 1;
                $json['msg'] = $result;
            }
        }
        $this->response->setOutput(Json::encode($json), $this->config->get('config_compression'));
    }

    private function validateForm()
    {
        if (empty($this->request->post['username'])) {
            $this->error['username'] = $this->language->get('You must set the username');
        }

        if (empty($this->request->post['password'])) {
            $this->error['password'] = $this->language->get('You must set the password');
        }

        if (empty($this->request->post['server'])) {
            $this->error['server'] = $this->language->get('You must set the server address');
        }

        if (!$this->user->hasPermission('modify', 'marketing/mailserver')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

}
