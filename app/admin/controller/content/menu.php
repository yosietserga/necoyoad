<?php

require_once(DIR_CONTROLLER . "admincontroller.php");

class ControllerContentMenu extends ControllerAdmin {

    private int $menu_id = 0;

    protected string $object_type       = 'menu'; //this will be saved into related tables

    protected string $model_name        = 'modelMenu';  //this will load main model class
    protected string $model_route       = 'content/menu'; //path to main model class
    protected string $model_object_type = 'menu'; // to set into mode

    protected string $controller_name   = 'menu'; //controller name
    protected string $controller_route  = 'content/menu'; //controller route

    protected string $controller_template_basename = 'menu'; //template controller name
    protected string $controller_template_route = 'content/menu'; //template controller path

    protected array $form_vars = [
        'menu_id' => [
            'name' => 'menu_id',
            'type' => 'number',
        ],
        'parent_id' => [
            'name' => 'parent_id',
            'type' => 'number',
        ],
        'name' => [
            'name' => 'name',
            'type' => 'string',
        ],
        'sort_order' => [
            'name' => 'sort_order',
            'type' => 'number',
        ],
        'default' => [
            'name' => 'default',
            'type' => 'boolean',
        ],
        'stores' => [
            'name' => 'stores',
            'type' => 'array',
        ],
    ];

    protected array $filters = [
        'name' => [
            'name' => 'name',
            'type' => 'string',
        ],
        'parent_id' => [
            'name' => 'parent_id',
            'type' => 'number',
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
                    'formatter' => function ($result) {
                        return ($result['status']) ? $this->language->get('Active') : $this->language->get('Deactive');
                    }
                ],
            ];

            return $data;
        });

        $this->addFilter("getForm:data", function ($data) {
            $this->menu_id = (int)$data['menu_id'];

            if (!isset($this->modelLanguage)) $this->load->model('localisation/language');
            if (!isset($this->modelStore)) $this->load->model('store/store');
            if (!isset($this->modelPage)) $this->load->model('content/page');
            if (!isset($this->modelManufacturer)) $this->load->model('store/manufacturer');
            
            $data['languages']     = $this->modelLanguage->getAll();
            $data['stores']        = $this->modelStore->getAll();
            $data['pages']         = $this->modelPage->getAll(['language_id' => $this->config->get('config_language_id')]);
            $data['manufacturers'] = $this->modelManufacturer->getAll(['language_id' => $this->config->get('config_language_id')]);
            $data['categories']    = $this->getCategories();
            $data['post_categories'] = $this->getPostCategories();

            //improve memory into getLinks function
            $this->data['languages'] = $data['languages'];
            $this->data['pages'] = $data['pages'];

            if ($this->menu_id) {
                $data['links'] = $this->getLinks();
                $data['action']      = $this->getUrl("/save", ['menu_id' => $this->menu_id]);
            } else {
                $data['action']      = $this->getUrl("/save");
            }

            return $data;
        });
        
        //TODO:re-enable sortable links after add (page|category|post_category|url)
        $this->addFilter("getForm:scripts", function (array $scripts) {
            $scripts[] = array('id' => 'menuFunctions', 'method' => 'function', 'script' =>
            "function saveAndKeep() {
                $('#temp').remove();
                $('#menuMsg').append('<div class=\"message success\" id=\"temp\">" . $this->language->get('text_success') . "</div>');
                window.onbeforeunload = null;
                
                data = $.extend(true, $('#formMenu').serializeFormJSON(), $('#menuItems').serializeFormJSON(), {items:$('#menuItems').serialize()}); 
                
                $.post('" . $this->getUrl("/save") . "', data);
            }
            (function($) {
                $.fn.serializeFormJSON = function() {
                
                   var o = {};
                   var a = this.serializeArray();
                   $.each(a, function() {
                       if (o[this.name]) {
                           if (!o[this.name].push) {
                               o[this.name] = [o[this.name]];
                           }
                           o[this.name].push(this.value || '');
                       } else {
                           o[this.name] = this.value || '';
                       }
                   });
                   return o;
                };
                })(jQuery);");

            return $scripts;
        });
    }

    public function save() {
        $this->load->model('content/menu');
        $this->load->library('json');
        $data = [];
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $data = $this->request->getPost();
            if ($this->request->hasQuery('menu_id')) {
                $this->model->update($this->request->get['menu_id'], $data);
                $this->session->set('success', $this->language->get('text_success'));
                $this->redirect($this->getUrl("/update", ['menu_id' => $this->request->getQuery('menu_id')]));
            } else {
                $menu_id = $this->model->add($data);
                $this->session->set('success', $this->language->get('text_success'));
                $this->redirect($this->getUrl("/update", ['menu_id' => $menu_id]));
            }
        } else {
            $data['error'] = 1;
            $data['message'] = $this->error;
        }
        $this->response->setOutput(Json::encode($data), $this->config->get('config_compression'));
    }

    public function getPostCategories($parent_id = 0, $marginLeft = 5) {
        $categories = $this->modelPost_category->getAll([
            'parent_id' => $parent_id,
            'language_id' => $this->config->get('config_language_id')
        ]);
        $return = '';
        if ($categories) {
            foreach ($categories as $key => $value) {
                $return .= '<li style="padding-left:' . $marginLeft . 'px">';
                $return .= '<input id="scrollboxPostCategories'. $value['category_id'] .'" type="checkbox" name="post_categories[]" value="' . $value['category_id'] . '" />';
                $return .= '<label for="scrollboxPostCategories'. $value['category_id'] .'">' . $value['title'] . '</label>';
                $return .= '</li>';

                $data['parent_id'] = $value['category_id'];
                $children = $this->modelPost_category->getAll($data);
                if ($children) {
                    $return .= $this->getPostCategories($value['category_id'], $marginLeft + 20);
                }
            }
        }
        return $return;
    }

    public function getCategories($parent_id = 0, $marginLeft = 5) {
        $categories = $this->modelCategory->getAll([
            'parent_id' => $parent_id,
            'language_id' => $this->config->get('config_language_id')
        ]);
        $return = '';
        if ($categories) {
            foreach ($categories as $key => $value) {
                $return .= '<li style="padding-left:' . $marginLeft . 'px">';
                $return .= '<input id="scrollboxCategories'. $value['category_id'] .'" type="checkbox" name="categories[]" value="' . $value['category_id'] . '" />';
                $return .= '<label for="scrollboxCategories'. $value['category_id'] .'">' . $value['title'] . '</label>';
                $return .= '</li>';

                $children = $this->modelCategory->getAll(array(
                  'parent_id'=>$value['category_id']
                ));
                if ($children) {
                    $return .= $this->getCategories($value['category_id'], $marginLeft + 20);
                }
            }
        }
        return $return;
    }

    public function getParentsTree($id) {
        $this->load->auto('content/menu');

        $links = $this->modelMenu->getAllItems(array(
            'menu_link_id'=>$id
        ));

        if ($links[0]['parent_id']) {
            return $links[0]['parent_id'];
        }
    }

    public function getLinks($parent_id = 0) {
        $output = '';
        $scripts = [];

        $this->load->model('content/page');
        $this->load->model('localisation/language');

        $links = $this->model->getAllItems(array(
            'menu_id' => $this->menu_id,
            'parent_id' => $parent_id
        ));
        
        $languages = $this->data['languages'];
        $pages     = $this->data['pages'];

        foreach ($links as $key => $result) {
            if ($result['parent_id']) {
                $index = trim($this->getParentsTree($result['parent_id']) ."_". $result['parent_id'] ."_". $result['menu_link_id'], '_');
            } else {
                $index = $result['menu_link_id'];
            }
            
            $icon = str_replace('fa-', '', $result['icon']);
            $icon = rtrim($icon,' fab');
            $icon = rtrim($icon,' fas');
            $icon = rtrim($icon,' far');

            $output .= '<li id="li_' . $index . '">';
            $output .= '<div class="item">';
            $output .= '<b>' . ($result['tag'] ? $result['tag'] : ($icon ? '[icon] '. $icon : 'LINK')) . '</b>';
            $output .= '<a class="showOptions" onclick="$(\'#linkOptions' . $index . '\').slideToggle(\'fast\');$(\'#languages_'. $index .' .htab2\').eq(0).trigger(\'click\');">&darr;</a>';
            $output .= '</div>';
            $output .= '<input type="hidden" id="link_' . $index . '_menu_link_id" name="link[' . $index . '][menu_link_id]" value="' . $result['menu_link_id'] . '" />';
            $output .= '<div id="linkOptions' . $index . '" class="itemOptions">';

            $output .= '<a style="float:right;font-size:10px;" onclick="$(\'#li_' . $index . '\').remove()">[ Eliminar ]</a>';

            $output .= '<div class="row" data-icons id="link_row_' . $index . '_icons">';
            $output .= '<button onclick="showIcons(\'' . $index . '\', \''. $icon .'\')">Show Icons</button>';
            $output .= '</div>';
            
            if ($icon) {
              $output .= 
              '<script>'.
                'setTimeout(function(){ '.
                  'showIcons(\'' . $index . '\', \''. $icon .'\')'.
                '}, '. ($key+1) .' * 1000);'.
              '</script>';
            }
            
            $output .= '<div class="clear"></div>';

            $output .= '<div class="row">';
            $output .= '<label class="neco-label" for="link_' . $index . '_link">Url:</label>';
            $output .= '<input type="url" id="link_' . $index . '_link" name="link[' . $index . '][link]" value="' . $result['link'] . '" style="width: 60%;" class="menu_link" />';
            $output .= '</div>';

            $output .= '<div class="clear"></div>';

            $output .= '<div class="row">';
            $output .= '<label class="neco-label" for="link_' . $index . '_tag">Etiqueta:</label>';
            $output .= '<input type="text" id="link_' . $index . '_tag" name="link[' . $index . '][tag]" value="' . $result['tag'] . '" style="width: 60%;" class="menu_tag" />';
            $output .= '</div>';

            $output .= '<div class="clear"></div>';

            $output .= '<div class="row">';
            $output .= '<label class="neco-label" for="link_' . $index . '_class_css">Clases CSS:</label>';
            $output .= '<input type="text" id="link_' . $index . '_class_css" name="link[' . $index . '][class_css]" value="' . $result['class_css'] . '" style="width: 60%;" class="menu_class_css" />';
            $output .= '</div>';

            $output .= '<div class="clear"></div>';

            $output .= '<div class="row">';
              $output .= '<label class="neco-label" for="link_' . $index . '_submenu_type">Select Sub-Menu Type:</label>';
              $output .= '<select id="link_' . $index . '_submenu_type" name="link[' . $index . '][submenu_type]" style="width:40%" onchange="'.
              'if (this.value===\'links\') { $(\'#link_row_' . $index . '_page_id\').hide(); $(\'#link_row_' . $index . '_html_content\').hide(); }'.
              'if (this.value===\'page_id\') { $(\'#link_row_' . $index . '_html_content\').hide(); $(\'#link_row_' . $index . '_page_id\').show(); }'.
              'if (this.value===\'html_content\') { $(\'#link_row_' . $index . '_page_id\').hide(); $(\'#link_row_' . $index . '_html_content\').show(); }'.
              '">';

                $output .= '<option value="links"'. ( ('links'==$result['submenu_type']) ? ' selected="selected"' : '' ) .'>'. $this->language->get('Sub-Links') .'</option>';
                $output .= '<option value="page_id"'. ( ('page_id'==$result['submenu_type']) ? ' selected="selected"' : '' ) .'>'. $this->language->get('A Page') .'</option>';
                $output .= '<option value="html_content"'. ( ('html_content'==$result['submenu_type']) ? ' selected="selected"' : '' ) .'>'. $this->language->get('HTML Content') .'</option>';
              $output .= '</select>';
            $output .= '</div>';

            $output .= '<div class="clear"></div>';

            $output .= '<div class="row" id="link_row_' . $index . '_page_id"'. ( ('page_id'!=$result['submenu_type']) ? ' style="display:none;"' : '' ) .'>';
            $output .= '<label class="neco-label" for="link_' . $index . '_page_id">Page As Sub-Menu:</label>';
            $output .= '<select id="link_' . $index . '_page_id" name="link[' . $index . '][page_id]" style="width:40%">';

            $output .= '<option value="0">'. $this->language->get('text_none') .'</option>';

            foreach ($pages as $page) {
                if ($page['post_id']==$result['page_id']) {
                    $output .= '<option value="'. $page['post_id'] .'" selected="selected">'. $page['title'] .'</option>';
                } else {
                    $output .= '<option value="'. $page['post_id'] .'">'. $page['title'] .'</option>';
                }
            }

            $output .= '</select>';
            $output .= '</div>';
            
            $output .= '<div class="clear"></div>';
            $output .= '<div id="link_row_' . $index . '_html_content"'. ( ('html_content'!=$result['submenu_type']) ? ' style="display:none;"' : '' ) .'>';

            $output .= '<label class="neco-label" for="link_' . $index . '_html">Contenido HTML:</label>';

            $output .= '<div class="clear"></div>';
            $output .= '<div id="languages_' . $index . '" class="htabs2">';
                foreach ($languages as $language) {
                    $output .= '<a tab="#language'.  $language['language_id'] . $index .'" class="htab2">';

                    $output .= '<img src="images/flags/'. $language['image'] .'" title="'. $language['name'] .'" /> '. $language['name'];
                    $output .= '</a>';
                }
            $output .= '</div>';

            foreach ($languages as $language) {
                $i = $language["language_id"] . $index;
                $output .= '<div id="language'. $i . '">';

                    $output .= '<textarea name="link[' . $index . '][descriptions]['. 
                    $language['language_id'] .'][description]" id="description'. 
                    $i .'">'. 
                    ($result['descriptions'][$language['language_id']]['description'] ?? "") .'</textarea>';

                $output .= '</div>';

                $code = "var editor". $i ." = CKEDITOR.replace('description" . $i . "', {"
                    . "filebrowserBrowseUrl: '" . Url::createAdminUrl("common/filemanager") . "',"
                    . "filebrowserImageBrowseUrl: '" . Url::createAdminUrl("common/filemanager") . "',"
                    . "filebrowserFlashBrowseUrl: '" . Url::createAdminUrl("common/filemanager") . "',"
                    . "filebrowserUploadUrl: '" . Url::createAdminUrl("common/filemanager") . "',"
                    . "filebrowserImageUploadUrl: '" . Url::createAdminUrl("common/filemanager") . "',"
                    . "filebrowserFlashUploadUrl: '" . Url::createAdminUrl("common/filemanager") . "',"
                    . "height:100"
                    . "});"
                    . "editor". $i .".config.allowedContent = true;";
                $cssrules = "assets/theme/". ($this->config->get('config_template') ? $this->config->get('config_template') : 'choroni') ."/css/theme.css";
                if (file_exists(DIR_ROOT . $cssrules)) {
                    $code .= "editor". $i .".config.contentsCss = '". HTTP_CATALOG . $cssrules ."';";
                }

                $scripts[] = array('id' => 'menuLanguage' . $i, 'method' => 'ready', 'script' => $code );

            }

            $output .= '<div class="clear"></div>';
            $output .= '<hr />';

            $output .= '</div>';

            $output .= '<div class="clear"></div>';
            $output .= '<hr />';
            $output .= '</div>';

            // subcategories
            $children = $this->modelMenu->getAllItems(array(
                'menu_id'=> $this->menu_id,
                'parent_id'=>$result['menu_link_id']
            ));

            if ($children) {
                $output .= '<ol>';
                $output .= $this->getLinks($result['menu_link_id']);
                $output .= '</ol>';
            }

            $output .= '</li>';
        }
        $this->scripts = array_merge($this->scripts, $scripts);

        return $output;
    }

    public function page() {
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . "GMT");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: application/json");
        $this->load->library('json');

        $data = [];
        if ($this->request->server['REQUEST_METHOD'] == 'POST' && isset($this->request->post['pages'])) {
            $this->load->model('content/page');
            $this->load->library('url');

            foreach ($this->request->post['pages'] as $key => $value) {
                $result = $this->modelPage->getById($value);
                if (!$result)
                    continue;
                $data[$key]['title'] = $result['title'];
                $data[$key]['href'] = Url::createUrl('content/page', array('page_id' => $result['post_id']), 'NONSSL', HTTP_CATALOG);
            }
        }

        $this->response->setOutput(Json::encode($data));
    }

    public function category() {
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . "GMT");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: application/json");
        $this->load->library('json');

        $data = [];
        if ($this->request->server['REQUEST_METHOD'] == 'POST' && isset($this->request->post['categories'])) {
            $this->load->model('store/category');
            $this->load->library('url');
            foreach ($this->request->post['categories'] as $key => $value) {
                $result = $this->modelCategory->getById($value);
                if (!$result)
                    continue;
                $path = ($result['parent_id']) ? $result['parent_id'] . "_" . $result['category_id'] : $result['category_id'];

                $data[$value]['title'] = $result['title'];
                $data[$value]['href'] = Url::createUrl('store/category', array('path' => $path), 'NONSSL', HTTP_CATALOG);
            }
        }

        $this->response->setOutput(Json::encode($data));
    }

    public function postcategory() {
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . "GMT");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: application/json");
        $this->load->library('json');

        $data = [];
        if ($this->request->server['REQUEST_METHOD'] == 'POST' && isset($this->request->post['post_categories'])) {
            $this->load->model('content/post_category');
            $this->load->library('url');
            foreach ($this->request->post['post_categories'] as $key => $value) {
                $result = $this->modelPost_category->getById($value);
                if (!$result)
                    continue;
                $path = ($result['parent_id']) ? $result['parent_id'] . "_" . $result['category_id'] : $result['category_id'];

                $data[$value]['title'] = $result['title'];
                $data[$value]['href'] = Url::createUrl('content/category', array('path' => $path), 'NONSSL', HTTP_CATALOG);
            }
        }

        $this->response->setOutput(Json::encode($data));
    }

}
