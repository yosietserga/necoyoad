<?php

require_once(DIR_CONTROLLER . "admincontroller.php");

class ControllerLocalisationLanguage extends ControllerAdmin
{
    protected string $object_type       = 'language'; //this will be saved into related tables

    protected string $model_name        = 'modelLanguage';  //this will load main model class
    protected string $model_route       = 'localisation/language'; //path to main model class
    protected string $model_object_type = 'language'; // to set into mode

    protected string $controller_name   = 'language'; //controller name
    protected string $controller_route  = 'localisation/language'; //controller route

    protected string $controller_template_basename = 'language'; //template controller name
    protected string $controller_template_route = 'localisation/language'; //template controller path

    protected array $form_vars = [
        'language_id' => [
            'name' => 'language_id',
            'type' => 'number',
        ],
        'name' => [
            'name' => 'name',
            'type' => 'string',
            'required' => true,
        ],
        'code' => [
            'name' => 'code',
            'type' => 'string',
            'required' => true,
        ],
        'locale' => [
            'name' => 'locale',
            'type' => 'string',
        ],
        'image' => [
            'name' => 'image',
            'type' => 'string',
        ],
        'directory' => [
            'name' => 'directory',
            'type' => 'string',
        ],
        'filename' => [
            'name' => 'filename',
            'type' => 'string',
        ],
        'sort_order' => [
            'name' => 'sort_order',
            'type' => 'number',
        ],
        'status' => [
            'name' => 'status',
            'type' => 'boolean',
            'default' => 1
        ],
        //TODO: add validation patterns or functions 
    ];

    protected array $filters = [
        'name' => [
            'name' => 'name',
            'type' => 'string',
        ],
        'code' => [
            'name' => 'code',
            'type' => 'string',
        ],
        'sort' => [
            'name' => 'sort',
            'label' => 'Sort By',
            'type' => 'option',
            'options' => [
                't.name' => 'Name',
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
                    'name' => [
                        'name' => 'name',
                        'label' => 'Name',
                        'isSortable' => true,
                        'formatter' => function ($column) {
                            $str = $column['name'];
                            if ($column['language_id'] == $this->config->get('config_language_id')) {
                                $str .= '<small><b>' .  $this->language->get('text_default') . '</b></small>';
                            }
                            return $str;
                        }
                    ],
                    'code' => [
                        'name' => 'code',
                        'label' => 'Code',
                        'isSortable' => true,
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
    }
}