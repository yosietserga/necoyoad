<?php

class ControllerPaymentCod extends Controller {

    private $error = [];

    public function index() {
        $this->load->language('payment/cod');

        $this->document->title = $this->language->get('heading_title');

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validate())) {
            $this->modelSetting->update('cod', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            if ($_POST['to'] == "saveAndKeep") {
                $this->redirect(Url::createAdminUrl('payment/cod'));
            } else {
                $this->redirect(Url::createAdminUrl('extension/payment'));
            }
        }

        $this->data['error_warning'] = (isset($this->error['warning'])) ? $this->error['warning'] : '';

        $this->document->breadcrumbs = [];
        $this->document->breadcrumbs[] = array(
            'href' => HTTP_HOME . 'index.php?r=common/home&token=' . $this->session->data['token'],
            'text' => $this->language->get('text_home'),
            'separator' => FALSE
        );
        $this->document->breadcrumbs[] = array(
            'href' => HTTP_HOME . 'index.php?r=extension/payment&token=' . $this->session->data['token'],
            'text' => $this->language->get('text_payment'),
            'separator' => ' :: '
        );
        $this->document->breadcrumbs[] = array(
            'href' => HTTP_HOME . 'index.php?r=payment/cod&token=' . $this->session->data['token'],
            'text' => $this->language->get('heading_title'),
            'separator' => ' :: '
        );

        $this->data['action'] = HTTP_HOME . 'index.php?r=payment/cod&token=' . $this->session->data['token'];
        $this->data['cancel'] = HTTP_HOME . 'index.php?r=extension/payment&token=' . $this->session->data['token'];

        $this->setvar('cod_image');
        $this->setvar('cod_order_status_id');
        $this->setvar('cod_newsletter_id');
        $this->setvar('cod_geo_zone_id');
        $this->setvar('cod_status');
        $this->setvar('cod_sort_order');

        if ($this->data['cod_image'] && file_exists(DIR_IMAGE . $this->data['cod_image'])) {
            $this->data['preview'] = NTImage::resizeAndSave($this->data['cod_image'], 100, 100);
        } else {
            $this->data['preview'] = NTImage::resizeAndSave('no_image.jpg', 100, 100);
        }

        $this->load->model('localisation/orderstatus');
        $this->data['order_statuses'] = $this->modelOrderstatus->getAll();

        $this->load->model('localisation/geozone');
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

        $this->template = 'payment/cod.tpl';
        
        $this->children[] = 'common/header';
        $this->children[] = 'common/nav';
        $this->children[] = 'common/footer';
        
        $this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
    }

    private function validate() {
        if (!$this->user->hasPermission('modify', 'payment/cod')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

}
