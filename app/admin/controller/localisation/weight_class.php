<?php

require_once(DIR_CONTROLLER . "admincontroller.php");

class ControllerLocalisationWeightClass extends ControllerAdmin
{
    protected string $object_type       = 'weight_class'; //this will be saved into related tables

    protected string $model_name        = 'modelLengthclass';  //this will load main model class
    protected string $model_route       = 'localisation/weightclass'; //path to main model class
    protected string $model_object_type = 'weight_class'; // to set into mode

    protected string $controller_name   = 'weight_class'; //controller name
    protected string $controller_route  = 'localisation/weight_class'; //controller route

    protected string $controller_template_basename = 'weight_class'; //template controller name
    protected string $controller_template_route = 'localisation/weight_class'; //template controller path

    protected array $form_vars = [
        'weight_class_id' => [
            'name' => 'weight_class_id',
            'type' => 'number',
        ],
        'value' => [
            'name' => 'value',
            'type' => 'float',
            'required' => true,
        ],
        'descriptions' => [
            'name' => 'descriptions',
            'type' => 'array',
            'fields' => [
                'title' => [
                    'name' => 'title',
                    'type' => 'string',
                ],
            ]
        ],
    ];

    protected array $filters = [
        'title' => [
            'name' => 'title',
            'type' => 'string',
        ],
        'sort' => [
            'name' => 'sort',
            'label' => 'Sort By',
            'type' => 'option',
            'options' => [
                'td.title' => 'Title',
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

    public function init()
    {
        parent::init();
        $this->addFilter("grid:data", function ($data) {
            $data['batch_available'] = ['deleteAll'];

            $data['columns'] =
                [
                    'title' => [
                        'name' => 'title',
                        'label' => 'Title',
                        'isSortable' => true,
                        'formatter' => function ($column) {
                            $str = $column['title'];
                            if ($column['weight_class_id'] == $this->config->get('config_weight_class')) {
                                $str .= '<small><b>' .  $this->language->get('text_default') . '</b></small>';
                            }
                            return $str;
                        }
                    ],
                ];

            return $data;
        });
    }
}