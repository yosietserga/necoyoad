<?php

require_once(DIR_CONTROLLER . "admincontroller.php");

class ControllerStoreAttribute extends ControllerAdmin {

    protected string $post_type         = 'attribute'; //this will be saved into main table 
    protected string $object_type       = 'attribute'; //this will be saved into related tables

    protected string $model_name        = 'modelAttribute';  //this will load main model class
    protected string $model_route       = 'store/attribute'; //path to main model class
    protected string $model_object_type = 'attribute'; // to set into mode

    protected string $controller_name   = 'attribute'; //controller name
    protected string $controller_route  = 'store/attribute'; //controller route

    protected string $controller_template_basename = 'attribute'; //template controller name
    protected string $controller_template_route = 'store/attribute'; //template controller path

    protected array $form_vars = [
		'product_attribute_group_id' => [
            'name' => 'product_attribute_group_id',
            'type' => 'number',
        ],
        'name' => [
            'name' => 'name',
            'type' => 'string',
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
        'categories' => [
            'name' => 'categories',
            'type' => 'array',
            'object_type' => 'category'
        ],
        'stores' => [
            'name' => 'stores',
            'type' => 'array',
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
        'category' => [
            'name' => 'category',
            'type' => 'string',
        ],
        'status' => [
            'name' => 'status',
            'type' => 'boolean',
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
				'name' => [
                    'name' => 'name',
                    'label' => 'Name',
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
			if (!isset($this->modelCategory)) $this->load->model('store/category');

			if (isset($data['model_info']['product_attribute_group_id'])) {
				$data['model_info']['attributes'] = $this->model->getAllAttributes([
					'product_attribute_group_id' => $data['model_info']['product_attribute_group_id']
				]);
			}
			$data['object_category'] = $data['category'];

            return $data;
        });

		$this->addFilter("getForm:scripts", function (array $scripts) {

			$scripts[] = array('id' => 'form', 'method' => 'ready', 'script' =>
			"$('#accordion').accordion({
                collapsible: true
            });
            
            $('#q').on('change',function(e){
                var that = this;
                var valor = $(that).val().toLowerCase();
                if (valor.length <= 0) {
                    $('#categoriesWrapper li').show();
                } else {
                    $('#categoriesWrapper li b').each(function(){
                        if ($(this).text().toLowerCase().indexOf( valor ) != -1) {
                            $(this).closest('li').show();
                        } else {
                            $(this).closest('li').hide();
                        }
                    });
                }
            });");

			return $scripts;
		});
    }
}