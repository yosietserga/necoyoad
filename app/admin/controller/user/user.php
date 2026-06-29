<?php

require_once(DIR_CONTROLLER . "admincontroller.php");

class ControllerUserUser extends ControllerAdmin
{
    protected string $object_type       = 'user'; //this will be saved into related tables

    protected string $model_name        = 'modelUser';  //this will load main model class
    protected string $model_route       = 'user/user'; //path to main model class
    protected string $model_object_type = 'user'; // to set into mode

    protected string $controller_name   = 'user'; //controller name
    protected string $controller_route  = 'user/user'; //controller route

    protected string $controller_template_basename = 'user'; //template controller name
    protected string $controller_template_route = 'user/user'; //template controller path

    protected array $form_vars = [
        'user_id' => [
            'name' => 'user_id',
            'type' => 'number',
        ],
        'user_group_id' => [
            'name' => 'user_group_id',
            'type' => 'number',
        ],
        'username' => [
            'name' => 'username',
            'type' => 'string',
            'required' => true,
        ],
        'email' => [
            'name' => 'email',
            'type' => 'string',
            'required' => true,
        ],
        'password' => [
            'name' => 'password',
            'type' => 'string',
        ],
        "firstname" => [
            "name"      => "firstname",
            "type"      => "string",
        ],
        "lastname" => [
            "name"      => "lastname",
            "type"      => "string",
        ],
        "image" => [
            "name"      => "image",
            "type"      => "string",
        ],
        'status' => [
            'name' => 'status',
            'type' => 'boolean',
            'default' => 1
        ],
        'stores' => [
            'name' => 'stores',
            'type' => 'array',
        ],
    ];

    protected array $filters = [
        'username' => [
            'name' => 'username',
            'type' => 'string',
        ],
        'email' => [
            'name' => 'email',
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
                    'image' => [
                        'name' => 'image',
                        'label' => 'Photo',
                        'formatter' => function ($column) {
                            return '<img src="'. $column['image'] .'" alt="'. $column['name'] .'" />';
                        }
                    ],
                    'username' => [
                        'name' => 'username',
                        'label' => 'Username',
                        'isSortable' => true,
                    ],
                    'email' => [
                        'name' => 'email',
                        'label' => 'Email',
                        'isSortable' => true,
                    ],
                    'user_group' => [
                        'name' => 'user_group',
                        'label' => 'User Group',
                        'isSortable' => true,
                        'formatter'=>function($column) {
                            if (!isset($this->modelUsergroup)) $this->load->model('user/usergroup');
                            $group = $this->modelUsergroup->getById($column['user_group_id']);
                            return $group['name'];
                        }
                    ],
                ];

            return $data;
        });

        $this->addFilter("getForm:data", function ($data) {
            if (!isset($this->modelUsergroup)) $this->load->model('user/usergroup');
            $data['user_groups'] = $this->modelUsergroup->getAll();
            return $data;
        });


    }

    private function validEmail($email) {
        $isValid = true;
        $atIndex = strrpos($email, "@");
        if (is_bool($atIndex) && !$atIndex) {
            $isValid = false;
        } else {
            $domain = substr($email, $atIndex + 1);
            $local = substr($email, 0, $atIndex);
            $localLen = strlen($local);
            $domainLen = strlen($domain);
            if ($localLen < 1 || $localLen > 64) {
                // local part length exceeded
                $isValid = false;
            } else if ($domainLen < 1 || $domainLen > 255) {
                // domain part length exceeded
                $isValid = false;
            } else if ($local[0] == '.' || $local[$localLen - 1] == '.') {
                // local part starts or ends with '.'
                $isValid = false;
            } else if (preg_match('/\\.\\./', $local)) {
                // local part has two consecutive dots
                $isValid = false;
            } else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain)) {
                // character not valid in domain part
                $isValid = false;
            } else if (preg_match('/\\.\\./', $domain)) {
                // domain part has two consecutive dots
                $isValid = false;
            } else if
            (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/', str_replace("\\\\", "", $local))) {
                // character not valid in local part unless 
                // local part is quoted
                if (!preg_match('/^"(\\\\"|[^"])+"$/', str_replace("\\\\", "", $local))) {
                    $isValid = false;
                }
            }
            if ($isValid && !(checkdnsrr($domain, "MX") || checkdnsrr($domain, "A"))) {
                // domain not found in DNS
                $isValid = false;
            }
        }
        return $isValid;
    }

    protected function validateDelete() {
        if (!$this->user->hasPermission('modify', 'user/user')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        foreach ($this->request->post['selected'] as $user_id) {
            if ($this->user->getId() == $user_id) {
                $this->error['warning'] = $this->language->get('error_account');
            }
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

}
