<?php

require_once(DIR_CONTROLLER . "admincontroller.php");

class ControllerLocalisationZone extends ControllerAdmin {
    protected string $object_type       = 'zone'; //this will be saved into related tables
    
    protected string $model_name        = 'modelZone';  //this will load main model class
    protected string $model_route       = 'localisation/zone'; //path to main model class
    protected string $model_object_type = 'zone'; // to set into mode

    protected string $controller_name   = 'zone'; //controller name
    protected string $controller_route  = 'localisation/zone'; //controller route

    protected string $controller_template_basename = 'zone'; //template controller name
    protected string $controller_template_route = 'localisation/zone'; //template controller path

    protected array $form_vars = [
        'zone_id' => [
            'name' => 'zone_id',
            'type' => 'number',
        ],
        'country_id' => [
            'name' => 'country_id',
            'type' => 'number',
        ],
        'name' => [
            'name' => 'name',
            'type' => 'string',
        ],
        'code' => [
            'name' => 'code',
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
        'code' => [
            'name' => 'code',
            'type' => 'string',
        ],
        'country' => [
            'name' => 'country',
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
                    'label' => 'Name',
                    'isSortable' => true,
                ],
                'country' => [
                    'name' => 'country',
                    'label' => 'Country',
                    'formatter' => function($column) {
                        if (!isset($this->modelCountry)) $this->load->model('localisation/country');
                        $column['country'] = $this->modelCountry->getById($column['country_id']);
                        return $column['country']['title'];
                    }
                ],
            ];

            return $data;
        });
    }
}