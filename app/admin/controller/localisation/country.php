<?php

require_once(DIR_CONTROLLER . "admincontroller.php");

class ControllerLocalisationCountry extends ControllerAdmin {
    protected string $object_type       = 'country'; //this will be saved into related tables
    
    protected string $model_name        = 'modelCountry';  //this will load main model class
    protected string $model_route       = 'localisation/country'; //path to main model class
    protected string $model_object_type = 'country'; // to set into mode

    protected string $controller_name   = 'country'; //controller name
    protected string $controller_route  = 'localisation/country'; //controller route

    protected string $controller_template_basename = 'country'; //template controller name
    protected string $controller_template_route = 'localisation/country'; //template controller path

    protected array $form_vars = [
        'country_id' => [
            'name' => 'country_id',
            'type' => 'number',
        ],
        'name' => [
            'name' => 'name',
            'type' => 'string',
        ],
        'iso_code_2' => [
            'name' => 'iso_code_2',
            'type' => 'string',
            'required' => true,
        ],
        'iso_code_3' => [
            'name' => 'iso_code_3',
            'type' => 'string',
            'required' => true,
        ],
        'address_format' => [
            'name' => 'address_format',
            'type' => 'string',
        ],
        'status' => [
            'name' => 'status',
            'type' => 'boolean',
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
        'iso_code_2' => [
            'name' => 'iso_code_2',
            'type' => 'string',
        ],
        'iso_code_3' => [
            'name' => 'iso_code_3',
            'type' => 'string',
        ],
        'sort' => [
            'name' => 'sort',
            'label' => 'Sort By',
            'type' => 'option',
            'options' => [
                'td.title' => 'Title',
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
            $data['batch_available'] = ['deleteAll'];

            $data['columns'] =
            [
                'title' => [
                    'name' => 'title',
                    'label' => 'Country',
                    'isSortable' => true,
                    'formatter'=>function($column) {
                        $str = $column['title'];
                        if ($column['country_id'] == $this->config->get('config_country_id')) {
                            $str .= '<small><b>'.  $this->language->get('text_default') .'</b></small>';
                        }
                        return $str;
                    }
                ],
                'iso_code_2' => [
                    'name' => 'iso_code_2',
                    'label' => 'ISO Code 2',
                ],
                'iso_code_3' => [
                    'name' => 'iso_code_3',
                    'label' => 'ISO Code 3',
                ],
            ];

            return $data;
        });
    }
}



