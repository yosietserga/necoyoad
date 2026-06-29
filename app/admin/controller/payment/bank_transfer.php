<?php

class ControllerPaymentBankTransfer extends Controller {

    private $error = [];

    public function index() {
        $this->load->language('payment/bank_transfer');

        $this->document->title = $this->language->get('heading_title');

        $this->load->auto('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validate())) {
            $this->modelSetting->update('bank_transfer', $this->request->post);
            $this->session->set('success', $this->language->get('text_success'));

            if ($_POST['to'] == "saveAndKeep") {
                $this->redirect(Url::createAdminUrl('payment/bank_transfer'));
            } else {
                $this->redirect(Url::createAdminUrl('extension/payment'));
            }
        }

        $this->data['error_warning'] = (isset($this->error['warning'])) ? $this->error['warning'] : '';

        $this->load->auto('localisation/language');
        $languages = $this->modelLanguage->getAll();

        foreach ($languages as $language) {
            if (isset($this->error['bank_' . $language['language_id']])) {
                $this->data['error_bank_' . $language['language_id']] = $this->error['bank_' . $language['language_id']];
            } else {
                $this->data['error_bank_' . $language['language_id']] = '';
            }

            if (isset($this->request->post['bank_transfer_bank_' . $language['language_id']])) {
                $this->data['bank_transfer_bank_' . $language['language_id']] = $this->request->post['bank_transfer_bank_' . $language['language_id']];
            } else {
                $this->data['bank_transfer_bank_' . $language['language_id']] = $this->config->get('bank_transfer_bank_' . $language['language_id']);
            }
        }

        $this->document->breadcrumbs = [];
        $this->document->breadcrumbs[] = array(
            'href' => Url::createAdminUrl('common/home'),
            'text' => $this->language->get('text_home'),
            'separator' => false
        );
        $this->document->breadcrumbs[] = array(
            'href' => Url::createAdminUrl('extension/payment'),
            'text' => $this->language->get('text_payment'),
            'separator' => ' :: '
        );
        $this->document->breadcrumbs[] = array(
            'href' => Url::createAdminUrl('payment/bank_transfer'),
            'text' => $this->language->get('heading_title'),
            'separator' => ' :: '
        );

        $this->data['action'] = Url::createAdminUrl('payment/bank_transfer');
        $this->data['cancel'] = Url::createAdminUrl('extension/payment');

        $this->data['languages'] = $languages;

        $this->setvar('bank_transfer_image');
        $this->setvar('bank_transfer_order_status_id');
        $this->setvar('bank_transfer_newsletter_id');
        $this->setvar('bank_transfer_geo_zone_id');
        $this->setvar('bank_transfer_status');
        $this->setvar('bank_transfer_sort_order');

        if ($this->data['bank_transfer_image'] && file_exists(DIR_IMAGE . $this->data['bank_transfer_image'])) {
            $this->data['preview'] = NTImage::resizeAndSave($this->data['bank_transfer_image'], 100, 100);
        } else {
            $this->data['preview'] = NTImage::resizeAndSave('no_image.jpg', 100, 100);
        }

        $this->load->auto('localisation/orderstatus');
        $this->data['order_statuses'] = $this->modelOrderstatus->getAll();

        $this->load->auto('localisation/geozone');
        $this->data['geo_zones'] = $this->modelGeozone->getAll();

        $this->load->model('marketing/newsletter');
        $this->data['newsletters'] = $this->modelNewsletter->getAll();

        $scripts[] = array('id' => 'categoryFunctions', 'method' => 'function', 'script' =>
            "function image_delete(field, preview) {
                $('#' + field).val('');
                $('#' + preview).attr('src','" . HTTP_IMAGE . "cache/no_image-100x100.jpg');
            }
            
            function image_upload(field, preview) {
                var height = $(window).height() * 0.8;
                var width = $(window).width() * 0.8;
                
            	$('#dialog').remove();
            	$('.box').prepend('<div id=\"dialog\" style=\"padding: 3px 0px 0px 0px;z-index:10000;\"><iframe src=\"" . Url::createAdminUrl("common/filemanager") . "&field=' + encodeURIComponent(field) + '\" style=\"padding:0; margin: 0; display: block; width: 100%; height: 100%;z-index:10000;\" frameborder=\"no\" scrolling=\"auto\"></iframe></div>');
                
                $('#dialog').dialog({
            		title: '" . $this->data['text_image_manager'] . "',
            		close: function (event, ui) {
            			if ($('#' + field).attr('value')) {
            				$.ajax({
            					url: '" . Url::createAdminUrl("common/filemanager/image") . "',
            					type: 'POST',
            					data: 'image=' + encodeURIComponent($('#' + field).val()),
            					dataType: 'text',
            					success: function(data) {
            						$('#' + preview).replaceWith('<img src=\"' + data + '\" id=\"' + preview + '\" class=\"image\" onclick=\"image_upload(\'' + field + '\', \'' + preview + '\');\">');
            					}
            				});
            			}
            		},	
            		bgiframe: false,
            		width: width,
            		height: height,
            		resizable: false,
            		modal: false
            	});}");

        $this->scripts = array_merge($this->scripts, $scripts);

        $this->template = 'payment/bank_transfer.tpl';

        $this->children[] = 'common/header';
        $this->children[] = 'common/nav';
        $this->children[] = 'common/footer';
        
        $this->response->setOutput($this->render(true), $this->config->get('config_compression'));
    }

    private function validate() {
        if (!$this->user->hasPermission('modify', 'payment/bank_transfer')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        $this->load->auto('localisation/language');

        $languages = $this->modelLanguage->getAll();

        foreach ($languages as $language) {
            if (!$this->request->post['bank_transfer_bank_' . $language['language_id']]) {
                $this->error['bank_' . $language['language_id']] = $this->language->get('error_bank');
            }
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

}
