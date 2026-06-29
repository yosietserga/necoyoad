<?php

require_once(DIR_CONTROLLER . "admincontroller.php");

class ControllerLocalisationTaxClass extends ControllerAdmin
{
    protected string $object_type       = 'tax_class'; //this will be saved into related tables

    protected string $model_name        = 'modelTaxclass';  //this will load main model class
    protected string $model_route       = 'localisation/taxclass'; //path to main model class
    protected string $model_object_type = 'tax_class'; // to set into mode

    protected string $controller_name   = 'tax_class'; //controller name
    protected string $controller_route  = 'localisation/tax_class'; //controller route

    protected string $controller_template_basename = 'tax_class'; //template controller name
    protected string $controller_template_route = 'localisation/tax_class'; //template controller path

    protected array $form_vars = [
        'tax_class_id' => [
            'name' => 'tax_class_id',
            'type' => 'number',
        ],
        'title' => [
            'name' => 'title',
            'type' => 'string',
            'required' => true,
        ],
        'description' => [
            'name' => 'description',
            'type' => 'string',
        ],
        'status' => [
            'name' => 'status',
            'type' => 'boolean',
            'default' => 1
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
                't.title' => 'Title',
                't.date_modified' => 'Date Modified',
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

            $this->load->model('localisation/geozone');
            $data['geo_zones'] = $this->modelGeozone->getAll(['language_id' => $this->config->get('config_language_id')]);

            if ($this->request->hasPost('tax_rate')) {
                $data['tax_rates'] = $this->request->getPost('tax_rate');
            } elseif (isset($data['tax_class_id'])) {
                $data['tax_rates'] = $this->model->getTaxRates($data['tax_class_id']);
            } else {
                $data['tax_rates'] = [];
            }

            return $data;
        });
    }
}