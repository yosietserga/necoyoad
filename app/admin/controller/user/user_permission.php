<?php

require_once(DIR_CONTROLLER . "admincontroller.php");

class ControllerUserUserPermission extends ControllerAdmin
{
    protected string $object_type       = 'user_group'; //this will be saved into related tables

    protected string $model_name        = 'modelUsergroup';  //this will load main model class
    protected string $model_route       = 'user/usergroup'; //path to main model class
    protected string $model_object_type = 'usergroup'; // to set into mode

    protected string $controller_name   = 'user_permission'; //controller name
    protected string $controller_route  = 'user/user_permission'; //controller route

    protected string $controller_template_basename = 'user_group'; //template controller name
    protected string $controller_template_route = 'user/user_group'; //template controller path

    protected array $form_vars = [
        'user_group_id' => [
            'name' => 'user_group_id',
            'type' => 'number',
        ],
        'name' => [
            'name' => 'name',
            'type' => 'string',
            'required' => true,
        ],
        'permission' => [
            'name' => 'permission',
            'type' => 'array',
        ],
        'status' => [
            'name' => 'status',
            'type' => 'boolean',
            'default' => 1
        ],
    ];

    protected array $filters = [
        'name' => [
            'name' => 'name',
            'type' => 'string',
        ],
        'user' => [
            'name' => 'user',
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
                    'name' => [
                        'name' => 'name',
                        'label' => 'Name',
                        'isSortable' => true,
                    ],
                ];

            return $data;
        });

        $this->addFilter("getForm:data", function ($data) {
            $ignore = array(
                'common/home',
                'common/layout',
                'common/login',
                'common/logout',
                'error/not_found',
                'error/permission',
                'common/footer',
                'common/header',
                'common/menu'
            );

            $data['permissions'] = [];

            $files2 = glob(DIR_APPLICATION . 'controller/module/*/*.php');
            $files = glob(DIR_APPLICATION . 'controller/*/*.php');
            $files = array_merge($files, $files2);
            foreach ($files as $file) {
                $route = explode('/', substr($file, strpos($file, "controller/")));
                if ($route[1] == 'module') {
                    $route = "module/" . $route[2];
                } else {
                    $route = $route[1];
                }
                $permission = $route . '/' . basename($file, '.php');

                if (!in_array($permission, $ignore)) {
                    $data['permissions'][] = $permission;
                }
            }
            asort($data['permissions']);

            return $data;
        });
    }
}