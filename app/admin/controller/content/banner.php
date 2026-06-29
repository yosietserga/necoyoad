<?php

require_once(DIR_CONTROLLER . "admincontroller.php");

class ControllerContentBanner extends ControllerAdmin {

    protected string $object_type       = 'banner'; //this will be saved into related tables

    protected string $model_name        = 'modeBanner';  //this will load main model class
    protected string $model_route       = 'content/banner'; //path to main model class
    protected string $model_object_type = 'banner'; // to set into mode

    protected string $controller_name   = 'banner'; //controller name
    protected string $controller_route  = 'content/banner'; //controller route

    protected string $controller_template_basename = 'banner'; //template controller name
    protected string $controller_template_route = 'content/banner'; //template controller path

    //TODO: add formatter IN option 
    //TODO: add formatter OUT option 
    protected array $form_vars = [
        'banner_id' => [
            'name' => 'banner_id',
            'type' => 'number',
        ],
        'name' => [
            'name' => 'name',
            'type' => 'string',
        ],
        'jquery_plugin' => [
            'name' => 'jquery_plugin',
            'type' => 'string',
        ],
        'publish_date_start' => [
            'name' => 'publish_date_start',
            'type' => 'date',
        ],
        'publish_date_end' => [
            'name' => 'publish_date_end',
            'type' => 'date',
        ],
        'banner_stores' => [
            'name' => 'banner_stores',
            'type' => 'array',
        ],
        'banner_items' => [
            'name' => 'banner_items',
            'type' => 'array',
        ],
        'banner_properties' => [
            'name' => 'banner_properties',
            'type' => 'array',
        ],
        'stores' => [
            'name' => 'stores',
            'type' => 'array',
        ],
    ];

    protected array $filters = [
        'name' => [
            'name' => 'name',
            'label' => 'Name',
            'type' => 'string',
        ],
        'date_start' => [
            'name' => 'date_start',
            'label' => 'Date Start',
            'type' => 'date',
        ],
        'date_end' => [
            'name' => 'date_end',
            'label' => 'Date End',
            'type' => 'date',
        ],
        'publish_date_start' => [
            'name' => 'publish_date_start',
            'label' => 'Publish Date Start',
            'type' => 'date',
        ],
        'publish_date_end' => [
            'name' => 'publish_date_end',
            'label' => 'Publish Date End',
            'type' => 'date',
        ],
        'sort' => [
            'name' => 'sort',
            'label' => 'Sort By',
            'type' => 'option',
            'options' => [
                't.name' => 'Name',
                't.sort_order' => 'Sort Order',
                't.date_added' => 'Date Added',
            ]
        ],
        'limit' => [
            'name' => 'limit',
            'label' => 'Items Per Page',
            'type' => 'option',
            'options' => [
                '10' => '10 Items per page',
                '25' => '25 Items per page',
                '50' => '50 Items per page',
                '100' => '100 Items per page',
                '250' => '250 Items per page',
            ]
        ],
    ];

    protected array $public_methods = ['insert', 'update', 'copy', 'delete', 'activate', 'grid'];

    public function init() {
        parent::init();
        $this->addFilter("grid:data", function ($data) {
            $data['batch_available'] = ['copyAll', 'deleteAll'];

            $data['columns'] =
                [
                    'name' => [
                        'name' => 'name',
                        'label' => 'Name',
                        'isSortable' => true,
                    ],
                    'jquery_plugin' => [
                        'name' => 'jquery_plugin',
                        'label' => 'View',
                    ],
                    'publish_date_start' => [
                        'name' => 'publish_date_start',
                        'label' => 'Fecha Inicial de Publicación',
                        'isSortable' => true,
                        'formatter' => function ($result) {
                            return "0000-00-00" != $result['publish_date_start'] ? date('d-m-Y h:i A', strtotime($result['publish_date_start'])) : "--";
                        }
                    ],
                    'publish_date_end' => [
                        'name' => 'publish_date_end',
                        'label' => 'Fecha Final de Publicación',
                        'isSortable' => true,
                        'formatter' => function ($result) {
                            return "0000-00-00" != $result['publish_date_end'] ? date('d-m-Y h:i A', strtotime($result['publish_date_end'])) : "--";
                        }
                    ],
                    'status' => [
                        'name' => 'status',
                        'label' => 'Status',
                        'isSortable' => true,
                        'formatter' => function ($result) {
                            return ($result['status']) ? $this->language->get('Active') : $this->language->get('Deactive');
                        }
                    ],
                ];

            return $data;
        });

        $this->addFilter("getForm:data", function ($data) {
            if (!isset($this->modelLanguage)) $this->load->model('localisation/language');
            if (!isset($this->modelStore)) $this->load->model('store/store');
            if (!isset($this->modelExtension)) $this->load->model('setting/extension');

            $data['languages']     = $this->modelLanguage->getAll();
            $data['stores']        = $this->modelStore->getAll();
            $data['NTImage'] = new NTImage;

            $folderJS = DIR_JS . 'sliders/';
            $directories = glob($folderJS . "*", GLOB_ONLYDIR);
            $data['sliders'] = [];
            foreach ($directories as $key => $directory) {
                $data['sliders'][$key] = basename($directory);
            }

            $extensions = $this->modelExtension->getInstalled('module');
            $data['extensions'] = [];
            $modules = glob(DIR_APPLICATION . "controller/module/*");
            if ($modules) {
                foreach ($modules as $module) {
                    if (!file_exists($module . '/widget.php'))
                    continue;
                    $extension = basename($module, '/widget.php');
                    $m = basename($module);
                    $this->load->language('module/' . $m);

                    if (in_array($extension, $extensions)) {
                        $data['modules'][] = array(
                            'widget' => $extension,
                            'name' => $this->language->get('heading_title'),
                            'description' => $this->language->get('description')
                        );
                    }
                }
            }

            return $data;
        });

        $this->addFilter("getForm:scripts", function (array $scripts) {

            $scripts[] = array('id' => 'bannerFunctions', 'method' => 'function', 'script' =>
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
            		title: '" . ($this->data['text_image_manager'] ?? '') . "',
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

            return $scripts;
        });
    }

    public function deleteItem() {
        $this->load->auto('content/banner');
        $this->model->deleteItem($_GET['id']);
    }

    public function saveItem() {
        $this->load->auto('content/banner');
        $banner_item_id = $this->request->hasPost('banner_item_id') ? $this->request->getPost('banner_item_id') : $this->request->getQuery('banner_item_id');
        $banner_id = $this->request->hasPost('id') ? $this->request->getPost('id') : $this->request->getQuery('id');

        if ($banner_item_id) {
            $this->model->deleteItem($banner_item_id);
        }

        $banner_item_id = $this->model->setItem(array(
            'banner_id' =>$banner_id,
            'sort_order'=>$this->request->getPost('sort_order'),
            'status'    =>$this->request->getPost('status'),
            'image'     =>$this->request->getPost('image'),
            'link'      =>$this->request->getPost('link')
        ));

        if ($this->request->getPost('descriptions')) {
            $this->model->setDescriptions($banner_item_id, $this->request->getPost('descriptions'));
        } else {
            $this->model->setDescriptions($banner_item_id, array($this->config->get('config_language_id') => array(
                'title'=>'',
                'descriptions'=>''
            )));
        }

        if ($this->request->getPost('properties')) {
            foreach ($this->request->getPost('properties') as $group => $props) {
                foreach ($props as $key => $value) {
                    $this->model->setItemProperty($banner_item_id, $group, $key, $value);
                }
            }
        }

        $return = array(
            'banner_item_id'=>$banner_item_id
        );

        $this->load->auto('json');
        $this->response->setOutput(Json::encode($return), $this->config->get('config_compression'));
    }
}