<?php

require_once(DIR_CONTROLLER . "admincontroller.php");

class ControllerStoreManufacturer extends ControllerAdmin {

    protected string $object_type       = 'manufacturer'; //this will be saved into related tables

    protected string $model_name        = 'modelManufacturer';  //this will load main model class
    protected string $model_route       = 'store/manufacturer'; //path to main model class
    protected string $model_object_type = 'manufacturer'; // to set into mode

    protected string $controller_name   = 'manufacturer'; //controller name
    protected string $controller_route  = 'store/manufacturer'; //controller route

    protected string $controller_template_basename = 'manufacturer'; //template controller name
    protected string $controller_template_route = 'store/manufacturer'; //template controller path

    protected array $form_vars = [
        'manufacturer_id' => [
            'name' => 'manufacturer_id',
            'type' => 'number',
        ],
        'name' => [
            'name' => 'name',
            'type' => 'string',
        ],
        'keyword' => [
            'name' => 'keyword',
            'type' => 'hidden',
        ],
        'image' => [
            'name' => 'image',
            'type' => 'string',
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
        'name' => [
            'name' => 'name',
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
                'image' => [
                    'name' => 'image',
                    'label' => 'Logo',
                    'formatter' => function ($column) {
                        return '<img src="'. $column['image'] .'" alt="'. $column['name'] .'" />';
                    }
                ],
                'name' => [
                    'name' => 'name',
                    'label' => 'Name',
                    'isSortable' => true,
                ],
            ];

            return $data;
        });
        
        $this->addFilter("getForm:data", function ($data) {

            if (isset($data['model_info']['image']) && file_exists(DIR_IMAGE . $data['model_info']['image'])) {
                $data['preview'] = NTImage::resizeAndSave($data['model_info']['image'], 100, 100);
            } else {
                $data['preview'] = NTImage::resizeAndSave('no_image.jpg', 100, 100);
            }

            return $data;
        });

        $this->addFilter("getForm:scripts", function ($scripts) {

            //TODO: mostrar los productos al scrolldown para no colapsar el navegador cuando se listan todos los productos
            $scripts[] = array('id' => 'form', 'method' => 'ready', 'script' =>
            "$('#name').blur(function(e){
                $.getJSON('" . Url::createAdminUrl('common/home/slug') . "',
                { 
                    slug : $(this).val(),
                    query : 'manufacturer_id=" . $this->request->getQuery('manufacturer_id') . "',
                    language_id : '{$this->config->get("config_language_id")}',
                },
                function(data){
                        $('#slug').val(data.slug);
                });
            });");

            return $scripts;
        });
    }
}