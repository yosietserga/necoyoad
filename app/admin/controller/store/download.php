<?php

require_once(DIR_CONTROLLER . "admincontroller.php");

class ControllerStoreDownload extends ControllerAdmin {

    protected string $object_type       = 'download'; //this will be saved into related tables

    protected string $model_name        = 'modelDownload';  //this will load main model class
    protected string $model_route       = 'store/download'; //path to main model class
    protected string $model_object_type = 'download'; // to set into mode

    protected string $controller_name   = 'download'; //controller name
    protected string $controller_route  = 'store/download'; //controller route

    protected string $controller_template_basename = 'download'; //template controller name
    protected string $controller_template_route = 'store/download'; //template controller path

    protected array $form_vars = [
		'download_id' => [
            'name' => 'download_id',
            'type' => 'number',
        ],
        'filename' => [
            'name' => 'filename',
            'type' => 'string',
        ],
        'mask' => [
            'name' => 'mask',
            'type' => 'string',
        ],
        'remaining' => [
            'name' => 'remaining',
            'type' => 'number',
        ],
        'update' => [
            'name' => 'update',
            'type' => 'boolean',
        ],
        'status' => [
            'name' => 'status',
            'type' => 'number',
        ],
        'stores' => [
            'name' => 'stores',
            'type' => 'array',
        ],
        'descriptions' => [
            'name' => 'descriptions',
            'type' => 'array',
            'fields' => [
                'title' => [
                    'name' => 'title',
                    'type' => 'string',
                ],
                'meta_description' => [
                    'name' => 'meta_description',
                    'type' => 'string',
                ],
                'description' => [
                    'name' => 'description',
                    'type' => 'string',
                ],
            ]
        ],
        'layout' => [
            'name' => 'layout',
            'type' => 'string',
            'isProperty' => true,
            'group' => 'style',
            'key'  => 'view',
        ],
    ];

    protected array $filters = [
        'filename' => [
            'name' => 'filename',
            'type' => 'string',
        ],
        'product' => [
            'name' => 'product',
            'type' => 'string',
        ],
        'date_start' => [
            'name' => 'date_start',
            'type' => 'date',
        ],
        'date_end' => [
            'name' => 'date_end',
            'type' => 'date',
        ],
    ];

    protected array $public_methods = ['insert', 'update', 'copy', 'delete', 'activate', 'grid'];

    public function init() {
        parent::init();
        $this->addFilter("grid:data", function ($data) {
            $data['batch_available'] = ['deleteAll'];

            $data['columns'] =
            [
                'filename' => [
                    'name' => 'filename',
                    'label' => 'Filename',
                    'isSortable' => true,
                ],
                'remaining' => [
                    'name' => 'remaining',
                    'label' => 'Remaining',
                    'isSortable' => true,
                ],
                'status' => [
                    'name' => 'status',
                    'label' => 'Status',
                    'isSortable' => true,
                    'formatter' => function($result) {
                        return ($result['status']) ? $this->language->get('Active') : $this->language->get('Deactive');
                    }
                ],
            ];

            return $data;
        });

        $this->addFilter("getForm:data", function ($data) {
            $data['show_update'] = $this->request->hasQuery('download_id') ? true : false;

            return $data;
        });

        $this->addFilter("getForm:scripts", function ($scripts) {
            $scripts[] = array('id' => 'downloadFunctions', 'method' => 'function', 'script' =>
            "function file_delete(field, preview) {
                $('#' + field).val('');
                $('#' + preview).parent('.row').find('.clear').remove();
                $('#' + preview).replaceWith('<a class=\"button\" id=\"'+ preview +'\" onclick=\"file_upload(\\'download\\', \\'preview\\');\">Seleccionar Archivo</a>');
            }
            
            function file_upload(field, preview) {
                var height = $(window).height() * 0.8;
                var width = $(window).width() * 0.8;
            	$('#dialog').remove();
            	$('#form').prepend('<div id=\"dialog\" style=\"padding: 3px 0px 0px 0px;z-index:10000;\"><iframe src=\"" . Url::createAdminUrl("common/filemanager") . "&field=' + encodeURIComponent(field) + '\" style=\"padding:0; margin: 0; display: block; width: 100%; height: 100%;z-index:10000;\" frameborder=\"no\" scrolling=\"auto\"></iframe></div>');
                
                $('#dialog').dialog({
            		title: '" . ($this->data['text_image_manager'] ?? "") . "',
            		close: function (event, ui) {
            			var csv = $('#' + field).val();
            			if (csv) {
            				$('#' + preview).replaceWith('<input type=\"text\" value=\"' + csv.replace('data/','') + '\" id=\"' + preview + '\" disabled=\"disabled\" /><div class=\"clear\"></div>');
            			}
            		},	
            		bgiframe: false,
            		width: width,
            		height: height,
            		resizable: false,
            		modal: false
            	});}");

            return $scripts;
        });

        $this->addFilter("formData", function ($data) {
            $data['filename'] = $data['download'];
            if (isset($data["update"]) && $data["update"]) $data["mask"] = md5(mt_rand(111111,99999) . time());
            return $data;
        });
    }
    
    protected function validateForm() {
        if (!$this->user->hasPermission('modify', 'store/download')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        //TODO: Colocar validaciones propias

        foreach ($this->request->post['descriptions'] as $language_id => $value) {
            if ((strlen(utf8_decode($value['title'])) < 3) || (strlen(utf8_decode($value['title'])) > 64)) {
                $this->error['title'][$language_id] = $this->language->get('error_name');
            }
        }

        if ($this->request->files['download']['title']) {
            if ((strlen(utf8_decode($this->request->files['download']['title'])) < 3) || (strlen(utf8_decode($this->request->files['download']['title'])) > 128)) {
                $this->error['download'] = $this->language->get('error_filename');
            }

            if (substr(strrchr($this->request->files['download']['title'], '.'), 1) == 'php') {
                $this->error['download'] = $this->language->get('error_filetype');
            }

            if ($this->request->files['download']['error'] != UPLOAD_ERR_OK) {
                $this->error['warning'] = $this->language->get('error_upload_' . $this->request->files['download']['error']);
            }
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

    protected function validateDelete() {
        if (!$this->user->hasPermission('modify', 'store/download')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        $this->load->auto('store/product');

        foreach ($this->request->post['selected'] as $download_id) {
            $product_total = $this->modelProduct->getAllTotalByDownloadId($download_id);

            if ($product_total) {
                $this->error['warning'] = sprintf($this->language->get('error_product'), $product_total);
            }
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }
}
