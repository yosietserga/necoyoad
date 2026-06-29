<?php

/**
 * ControllerStoreStore
 * 
 * @package NecoTienda
 * @author Yosiet Serga
 * @copyright Inversiones Necoyoad, C.A.
 * @version 1.0.0
 * @access public
 * @see Controller
 */
class ControllerStoreStore extends Controller {

    private $error = [];

    /**
     * ControllerStoreStore::index()
     * 
     * @see Load
     * @see Document
     * @see Language
     * @see getList
     * @return void
     */
    public function index() {
        $this->document->title = $this->language->get('heading_title');
        $this->getList();
    }

    /**
     * ControllerStoreStore::insert()
     * 
     * @see Load
     * @see Document
     * @see Request
     * @see Session
     * @see Redirect
     * @see Language
     * @see getForm
     * @return void
     */
    public function insert() {
        $this->document->title = $this->language->get('heading_title');
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {

            $this->data['folder'] = $this->request->post['folder'];
            $this->data['create_app'] = $this->request->post['create_app'];
            $this->data['store_id'] = $store_id;
            $this->data['adminPath'] = ADMIN_PATH;

            if (isset($this->request->post['config_token_ignore']))
                $this->request->post['config_token_ignore'] = serialize($this->request->post['config_token_ignore']);

            if ($this->request->post['config_folder'] && $this->createPath($this->request->post['config_folder'])) {
                $this->request->post['config_folder'] = $this->data['folder'];
            } else {
                $this->error['warning'] .= $this->language->get('error_folder_already_exists');
            }

            $this->data['store_id'] = $store_id = $this->modelStore->add($this->request->post);

            if ($this->data['folder']) {
                if ($this->createStandardApp()) {
                    $this->session->set('success', $this->language->get('text_success'));
                } else {
                    $this->error['warning'] .= $this->language->get('error_create_store');
                }
            }

            if ($this->config->get('config_currency_auto'))
                $this->modelCurrency->updateAll();

            if ($_POST['to'] == "saveAndKeep") {
                $this->redirect(Url::createAdminUrl('store/store/update', array('store_id' => $store_id)));
            } elseif ($_POST['to'] == "saveAndNew") {
                $this->redirect(Url::createAdminUrl('store/store/insert'));
            } else {
                $this->redirect(Url::createAdminUrl('store/store'));
            }
        }
        if (isset($this->request->post['config_maintenance']))
            $this->modelStore->editMaintenance($this->request->post['config_maintenance']);
        $this->getForm();
    }

    /**
     * ControllerStoreStore::update()
     * 
     * @see Load
     * @see Document
     * @see Request
     * @see Session
     * @see Redirect
     * @see Language
     * @see getForm
     * @return void
     */
    public function update() {
        $this->document->title = $this->language->get('heading_title');
        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {

        	$this->modelStore->deleteSettings('config', null, $this->request->getQuery('store_id'));

            if (isset($this->request->post['config_token_ignore']))
                $this->request->post['config_token_ignore'] = serialize($this->request->post['config_token_ignore']);

            $this->modelStore->update($this->request->get['store_id'], $this->request->post);

            if ($this->config->get('config_currency_auto'))
                $this->modelCurrency->updateAll();

            $this->session->set('success', $this->language->get('text_success'));

            if ($_POST['to'] == "saveAndKeep") {
                $this->redirect(Url::createAdminUrl('store/store/update', array('store_id' => $this->request->get['store_id'])));
            } elseif ($_POST['to'] == "saveAndNew") {
                $this->redirect(Url::createAdminUrl('store/store/insert'));
            } else {
                $this->redirect(Url::createAdminUrl('store/store'));
            }
        }
        if (isset($this->request->post['config_maintenance']))
            $this->modelStore->editMaintenance($this->request->post['config_maintenance']);
        $this->getForm();
    }

    /**
     * ControllerStoreStore::update()
     * 
     * @see Load
     * @see Document
     * @see Request
     * @see Session
     * @see Redirect
     * @see Language
     * @see getForm
     * @return void
     */
    public function saveContent() {
        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            $this->load->model('store/store');

            $data = [];
            foreach ($this->request->post as $key => $value) {
                $value = str_replace("&amp;", "&", $value);
                parse_str($value, $result);
                $data = array_merge($data, $result);
            }

            $store_id = ($this->request->hasQuery('store_id')) ? $this->request->getQuery('store_id') : 0;
            $this->modelStore->saveContent($store_id, $data);
        }
    }

    /**
     * ControllerStoreCategory::delete()
     * elimina un objeto
     * @return boolean
     * */
    public function delete() {
        $this->load->auto('store/store');
        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            foreach ($this->request->post['selected'] as $id) {
                $this->modelStore->delete($id);
            }
        } else {
            $this->modelStore->delete($_GET['id']);
        }
    }

    /**
     * ControllerStoreStore::getById()
     * 
     * @see Load
     * @see Document
     * @see Request
     * @see Session
     * @see Response
     * @see Pagination
     * @see Language
     * @return void
     */
    private function getList() {
        $this->document->breadcrumbs = [];
        $this->document->breadcrumbs[] = array(
            'href' => Url::createAdminUrl('common/home'),
            'text' => $this->language->get('text_home'),
            'separator' => false
        );

        $this->document->breadcrumbs[] = array(
            'href' => Url::createAdminUrl('store/store'),
            'text' => $this->language->get('heading_title'),
            'separator' => ' :: '
        );

        $this->data['insert'] = Url::createAdminUrl('store/store/insert');

        $this->data['error_warning'] = isset($this->error['warning']) ? $this->error['warning'] : '';

        if ($this->session->has('success')) {
            $this->data['success'] = $this->session->get('success');
            $this->session->clear('success');
        } else {
            $this->data['success'] = '';
        }

        // SCRIPTS
        $scripts[] = array('id' => 'storeList', 'method' => 'function', 'script' =>
            "function activate(e) {
                $.getJSON('" . Url::createAdminUrl("store/store/activate") . "',{
                    id:e
                },function(data){
                    if (data > 0) {
                        $('#img_' + e).attr('src','image/good.png');
                    } else {
                        $('#img_' + e).attr('src','image/minus.png');
                    }
                });
            }
            function deleteAll() {
                if (confirm('\\xbfDesea eliminar todos los objetos seleccionados?')) {
                    $('#gridWrapper').hide();
                    $('#gridPreloader').show();
                    $.post('" . Url::createAdminUrl("store/store/delete") . "',$('#form').serialize(),function(){
                        $('#gridWrapper').load('" . Url::createAdminUrl("store/store/grid") . "',function(){
                            $('#gridWrapper').show();
                            $('#gridPreloader').hide();
                        });
                    });
                }
                return false;
            }
            function eliminar(e) {
                if (confirm('\\xbfDesea eliminar este objeto?')) {
                    $('#tr_' + e).remove();
                	$.getJSON('" . Url::createAdminUrl("store/store/delete") . "',{
                        id:e
                    });
                }
                return false;
             }");
        $scripts[] = array('id' => 'sortable', 'method' => 'ready', 'script' =>
            "$('#gridWrapper').load('" . Url::createAdminUrl("store/store/grid") . "',function(e){
                $('#gridPreloader').hide();
            });
                
            $('#formFilter').ntForm({
                lockButton:false,
                ajax:true,
                type:'get',
                dataType:'html',
                url:'" . Url::createAdminUrl("store/store/grid") . "',
                beforeSend:function(){
                    $('#gridWrapper').hide();
                    $('#gridPreloader').show();
                },
                success:function(data){
                    $('#gridPreloader').hide();
                    $('#gridWrapper').html(data).show();
                }
            });
            $('#formFilter').on('keyup', function(e){
                var code = e.keyCode || e.which;
                if (code == 13){
                    $('#formFilter').ntForm('submit');
                }
            });");

        $this->scripts = array_merge($this->scripts, $scripts);

        $template = ($this->config->get('default_admin_view_store_store_list')) ? $this->config->get('default_admin_view_store_store_list') : 'store/store_list.tpl';
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_admin_template') . '/'. $template)) {
            $this->template = $this->config->get('config_admin_template') . '/' . $template;
        } else {
            $this->template = 'default/' . $template;
        }


        $this->children[] = 'common/header';
        $this->children[] = 'common/nav';
        $this->children[] = 'common/footer';
        
        $this->response->setOutput($this->render(true), $this->config->get('config_compression'));
    }

    public function grid() {
        $this->load->auto('image');

        $filter_name = isset($this->request->get['filter_name']) ? $this->request->get['filter_name'] : null;
        $filter_date_start = isset($this->request->get['filter_date_start']) ? $this->request->get['filter_date_start'] : null;
        $filter_date_end = isset($this->request->get['filter_date_end']) ? $this->request->get['filter_date_end'] : null;
        $page = isset($this->request->get['page']) ? $this->request->get['page'] : 1;
        $sort = isset($this->request->get['sort']) ? $this->request->get['sort'] : 'name';
        $order = isset($this->request->get['order']) ? $this->request->get['order'] : 'ASC';
        $limit = !empty($this->request->get['limit']) ? $this->request->get['limit'] : $this->config->get('config_admin_limit');

        $url = '';

        if (isset($this->request->get['filter_name'])) {
            $url .= '&filter_name=' . $this->request->get['filter_name'];
        }
        if (isset($this->request->get['filter_date_start'])) {
            $url .= '&filter_date_start=' . $this->request->get['filter_date_start'];
        }
        if (isset($this->request->get['filter_date_end'])) {
            $url .= '&filter_date_end=' . $this->request->get['filter_date_end'];
        }
        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }
        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }
        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }
        if (!empty($this->request->get['limit'])) {
            $url .= '&limit=' . $this->request->get['limit'];
        }

        $this->data['stores'] = [];

        $data = array(
            'filter_name' => $filter_name,
            'filter_date_start' => $filter_date_start,
            'filter_date_end' => $filter_date_end,
            'sort' => $sort,
            'order' => $order,
            'start' => ($page - 1) * $limit,
            'limit' => $limit
        );

        $store_total = $this->modelStore->getAllTotal();

        if ($store_total) {
            $results = $this->modelStore->getAll($data);
        $i = str_replace('%theme%',$this->config->get('config_admin_template'),HTTP_ADMIN_THEME_IMAGE);
            foreach ($results as $result) {
                $action = array(
                    'edit' => array(
                        'action' => 'edit',
                        'text' => $this->language->get('text_edit'),
                        'href' => Url::createAdminUrl('store/store/update') . '&store_id=' . $result['store_id'] . $url,
                        'img' =>  $i .'edit.png'
                    ),
                    'delete' => array(
                        'action' => 'delete',
                        'text' => $this->language->get('text_delete'),
                        'href' => '',
                        'img' => $i .'delete.png'
                    )
                );

                if ($result['config_logo'] && file_exists(DIR_IMAGE . $result['config_logo'])) {
                    $image = NTImage::resizeAndSave($result['config_logo'], 50, 50);
                } else {
                    $image = NTImage::resizeAndSave('no_image.jpg', 50, 50);
                }

                $this->data['stores'][] = array(
                    'store_id' => $result['store_id'],
                    'name' => $result['name'],
                    'folder' => $result['folder'],
                    'image' => $image,
                    'selected' => isset($this->request->post['selected']) && in_array($result['store_id'], $this->request->post['selected']),
                    'action' => $action
                );
            }
        }

        $url = '';

        if ($order == 'ASC') {
            $url .= '&order=DESC';
        } else {
            $url .= '&order=ASC';
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $this->data['sort_name'] = Url::createAdminUrl('store/store/grid') . '&sort=name' . $url;

        $url = '';

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        $pagination = new Pagination();
        $pagination->ajax = true;
        $pagination->ajaxTarget = "gridWrapper";
        $pagination->total = $store_total;
        $pagination->page = $page;
        $pagination->limit = $limit;
        $pagination->text = $this->language->get('text_pagination');
        $pagination->url = Url::createAdminUrl('store/store/grid') . $url . '&page={page}';

        $this->data['pagination'] = $pagination->render();

        $this->data['sort'] = $sort;
        $this->data['order'] = $order;

        $template = ($this->config->get('default_admin_view_store_store_grid')) ? $this->config->get('default_admin_view_store_store_grid') : 'store/store_grid.tpl';
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_admin_template') . '/'. $template)) {
            $this->template = $this->config->get('config_admin_template') . '/' . $template;
        } else {
            $this->template = 'default/' . $template;
        }


        $this->response->setOutput($this->render(true), $this->config->get('config_compression'));
    }

    /**
     * ControllerStoreStore::getForm()
     * 
     * @see Load
     * @see Document
     * @see Request
     * @see Session
     * @see Response
     * @see Pagination
     * @see Language
     * @return void
     */
    private function getForm() {
        $this->data['error_warning'] = isset($this->error['warning']) ? $this->error['warning'] : null;
        $this->data['error_name'] = isset($this->error['name']) ? $this->error['name'] : null;
        $this->data['error_rif'] = isset($this->error['rif']) ? $this->error['rif'] : null;
        $this->data['error_url'] = isset($this->error['url']) ? $this->error['url'] : null;
        $this->data['error_owner'] = isset($this->error['owner']) ? $this->error['owner'] : null;
        $this->data['error_address'] = isset($this->error['address']) ? $this->error['address'] : null;
        $this->data['error_email'] = isset($this->error['email']) ? $this->error['email'] : null;
        $this->data['error_telephone'] = isset($this->error['telephone']) ? $this->error['telephone'] : null;

        if ($this->session->has('success')) {
            $this->data['success'] = $this->session->get('success');
            $this->session->clear('success');
        } else {
            $this->data['success'] = '';
        }

        $this->data['cancel'] = Url::createAdminUrl('store/store');

        if (!$this->request->hasQuery('store_id')) {
            $this->data['action'] = Url::createAdminUrl('store/store/insert');
        } else {
            $this->data['action'] = Url::createAdminUrl('store/store/update', array('store_id' => $this->request->get['store_id']));
        }

        if ($this->request->hasQuery('store_id') && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $store_info = $this->modelStore->getById($this->request->getQuery('store_id'));
            $settings = $this->modelStore->getSettings('config', null, $this->request->getQuery('store_id'));
            $this->data['showContent'] = true;
        }

        foreach ($settings as $k=>$v) {
            $this->data[$v['key']] = $v['value'];
        }
        
        $this->data['config_name'] = $store_info['name'];
        $this->data['config_folder'] = $store_info['folder'];

        $directories = glob(DIR_CATALOG . 'view/theme/*', GLOB_ONLYDIR);
        $this->data['templates'] = [];
        foreach ($directories as $directory) {
            $this->data['templates'][] = basename($directory);
        }

        $languages = $this->data['languages'] = $this->modelLanguage->getAll();
        foreach ($languages as $language) {
            if (isset($this->request->post['config_title_' . $language['language_id']])) {
                $this->data['config_title_' . $language['language_id']] = $this->request->post['config_title_' . $language['language_id']];
            } elseif ($store_info['config_title_' . $language['language_id']]) {
                $this->data['config_title_' . $language['language_id']] = $store_info['config_title_' . $language['language_id']];
            } else {
                $this->data['config_title_' . $language['language_id']] = '';
            }

            if (isset($this->request->post['config_meta_description_' . $language['language_id']])) {
                $this->data['config_meta_description_' . $language['language_id']] = $this->request->post['config_meta_description_' . $language['language_id']];
            } elseif ($store_info['config_meta_description_' . $language['language_id']]) {
                $this->data['config_meta_description_' . $language['language_id']] = $store_info['config_meta_description_' . $language['language_id']];
            } else {
                $this->data['config_meta_description_' . $language['language_id']] = '';
            }
        }

        $this->data['countries'] = $this->modelCountry->getAll();
        $this->data['currencies'] = $this->modelCurrency->getAll();
        $this->data['pages']= $this->modelPage->getAll();
        $this->data['customer_groups']= $this->modelCustomergroup->getAll();
        $this->data['order_statuses'] = $this->modelOrderstatus->getAll();
        $this->data['stock_statuses'] = $this->modelStockstatus->getAll();

        if (!empty($this->request->post['config_logo']) && file_exists(DIR_IMAGE . $this->request->post['config_logo'])) {
            $this->data['preview_logo'] = HTTP_IMAGE . $this->request->post['config_logo'];
        } elseif (!empty($this->data['config_logo']) && file_exists(DIR_IMAGE . $this->data['config_logo'])) {
            $this->data['preview_logo'] = HTTP_IMAGE . $this->data['config_logo'];
        } else {
            $this->data['preview_logo'] = NTImage::resizeAndSave('no_image.jpg', 100, 100);
        }

        if (!empty($this->request->post['config_email_logo']) && file_exists(DIR_IMAGE . $this->request->post['config_email_logo'])) {
            $this->data['preview_email_logo'] = HTTP_IMAGE . $this->request->post['config_email_logo'];
        } elseif (!empty($this->data['config_email_logo']) && file_exists(DIR_IMAGE . $this->data['config_email_logo'])) {
            $this->data['preview_email_logo'] = HTTP_IMAGE . $this->data['config_email_logo'];
        } else {
            $this->data['preview_email_logo'] = NTImage::resizeAndSave('no_image.jpg', 100, 100);
        }

        if (!empty($this->request->post['config_mobile_logo']) && file_exists(DIR_IMAGE . $this->request->post['config_mobile_logo'])) {
            $this->data['preview_mobile_logo'] = HTTP_IMAGE . $this->request->post['config_mobile_logo'];
        } elseif (!empty($this->data['config_mobile_logo']) && file_exists(DIR_IMAGE . $this->data['config_mobile_logo'])) {
            $this->data['preview_mobile_logo'] = HTTP_IMAGE . $this->data['config_mobile_logo'];
        } else {
            $this->data['preview_mobile_logo'] = NTImage::resizeAndSave('no_image.jpg', 100, 100);
        }

        if (!empty($this->request->post['config_icon']) && file_exists(DIR_IMAGE . $this->request->post['config_icon'])) {
            $this->data['preview_icon'] = HTTP_IMAGE . $this->request->post['config_icon'];
        } elseif (!empty($this->data['config_logo']) && file_exists(DIR_IMAGE . $this->data['config_icon'])) {
            $this->data['preview_icon'] = HTTP_IMAGE . $this->data['config_icon'];
        } else {
            $this->data['preview_icon'] = NTImage::resizeAndSave('no_image.jpg', 100, 100);
        }

        $ignore = array(
            'common/login',
            'common/logout',
            'error/not_found',
            'error/permission'
        );

        $this->data['tokens'] = [];
        $files = glob(DIR_APPLICATION . 'controller/*/*.php');
        foreach ($files as $file) {
            $data = explode('/', dirname($file));
            $token = end($data) . '/' . basename($file, '.php');
            if (!in_array($token, $ignore)) {
                $this->data['tokens'][] = $token;
            }
        }

        if (isset($this->request->post['config_token_ignore'])) {
            $this->data['config_token_ignore'] = $this->request->post['config_token_ignore'];
        } elseif (isset($store_info['config_token_ignore'])) {
            $this->data['config_token_ignore'] = unserialize($store_info['config_token_ignore']);
        } else {
            $this->data['config_token_ignore'] = [];
        }

        $scripts[] = array('id' => 'form', 'method' => 'ready', 'script' =>
            "$('.product_tab').hide();
            $('#general').show();
            $('.product_tabs .htab').on('click',function(e){
                $('.product_tab').hide();
                $($(this).attr('data-target')).show();
            });");

        $scripts[] = array('id' => 'Functions', 'method' => 'function', 'script' =>
            "function image_delete(field, preview) {
                $('#' + field).val('');
                $('#' + preview).attr('src','" . HTTP_IMAGE . "cache/no_image-100x100.jpg');
            }");

        $this->scripts = array_merge($this->scripts, $scripts);

        $template = ($this->config->get('default_admin_view_store_store_form')) ? $this->config->get('default_admin_view_store_store_form') : 'store/store_form.tpl';
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_admin_template') . '/'. $template)) {
            $this->template = $this->config->get('config_admin_template') . '/' . $template;
        } else {
            $this->template = 'default/' . $template;
        }

        
        $this->children[] = 'common/header';
        $this->children[] = 'common/nav';
        $this->children[] = 'common/footer';
        
        $this->response->setOutput($this->render(true), $this->config->get('config_compression'));
    }

    private function setVarData($varname, $model = null, $default = null) {
        if (isset($this->request->post[$varname])) {
            $this->data[$varname] = $this->request->post[$varname];
        } elseif (isset($model[$varname])) {
            $this->data[$varname] = $model[$varname];
        } elseif (isset($default)) {
            $this->data[$varname] = $default;
        } else {
            $this->data[$varname] = '';
        }
        return $this->data[$varname];
    }

    /**
     * ControllerStoreStore::validateForm()
     * 
     * @see Request
     * @see Language
     * @return bool
     */
    private function validateForm() {
        $prohibited = array(
            'necotienda',
            'shop',
            'blog',
            'carrito',
            'buscar',
            'search',
            'cart',
            'profile',
            'contact',
            'contacto',
            'special',
            'ofertas',
            'pages',
            'paginas',
            'productos',
            'products',
            'categories',
            'categorias',
            'buscar',
            'search',
            'pedidos',
            'orders',
            'mensajes',
            'pagos',
            'payments',
            'comentarios',
            'reviews',
        );

        if (in_array($prohibited, $this->request->getPost('config_folder'))) {
            $this->error['warning'] = $this->language->get('error_folder_name_prohibited');
        }

        if (!$this->user->hasPermission('modify', 'setting/setting')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        /*
          if (!$this->request->post['config_name']) {
          $this->error['name'] = $this->language->get('error_name');
          }


          if (!$this->validate_form->esRif($this->request->post['config_rif'])) {
          $this->error['rif'] = $this->language->get('error_rif');
          }


          if (!$this->request->post['config_url']) {
          $this->error['url'] = $this->language->get('error_url');
          }


          if (!$this->request->post['config_owner']) {
          $this->error['owner'] = $this->language->get('error_owner');
          }


          if (!$this->request->post['config_address']) {
          $this->error['address'] = $this->language->get('error_address');
          }

          $pattern = '/^[A-Z0-9._%-]+@[A-Z0-9][A-Z0-9.-]{0,61}[A-Z0-9]\.[A-Z]{2,6}$/i';


          if ((strlen(utf8_decode($this->request->post['config_email'])) > 96) || (!preg_match($pattern, $this->request->post['config_email']))) {
          $this->error['email'] = $this->language->get('error_email');
          }


          if (!$this->request->post['config_image_thumb_width'] || !$this->request->post['config_image_thumb_height']) {
          $this->error['image_thumb'] = $this->language->get('error_image_thumb');
          }

          if (!$this->request->post['config_image_popup_width'] || !$this->request->post['config_image_popup_height']) {
          $this->error['image_popup'] = $this->language->get('error_image_popup');
          }

          if (!$this->request->post['config_image_category_width'] || !$this->request->post['config_image_category_height']) {
          $this->error['image_category'] = $this->language->get('error_image_category');
          }

          if (!$this->request->post['config_image_post_width'] || !$this->request->post['config_image_post_height']) {
          $this->error['image_post'] = $this->language->get('error_image_post');
          }

          if (!$this->request->post['config_image_product_width'] || !$this->request->post['config_image_product_height']) {
          $this->error['image_product'] = $this->language->get('error_image_product');
          }

          if (!$this->request->post['config_image_additional_width'] || !$this->request->post['config_image_additional_height']) {
          $this->error['image_additional'] = $this->language->get('error_image_additional');
          }

          if (!$this->request->post['config_image_related_width'] || !$this->request->post['config_image_related_height']) {
          $this->error['image_related'] = $this->language->get('error_image_related');
          }

          if (!$this->request->post['config_image_cart_width'] || !$this->request->post['config_image_cart_height']) {
          $this->error['image_cart'] = $this->language->get('error_image_cart');
          }

          if (!$this->request->post['config_error_filename']) {
          $this->error['error_filename'] = $this->language->get('error_error_filename');
          }

          if (isset($this->request->post['config_smtp_port']) && !$this->validate_form->esSoloNumeros($this->request->post['config_smtp_port'], $this->language->get('entry_smtp_port'))) {
          $this->error['smtp_port'] = $this->language->get('error_smtp_port');
          }

          if (isset($this->request->post['config_pop3_port']) && !$this->validate_form->esSoloNumeros($this->request->post['config_pop3_port'], $this->language->get('entry_pop3_port'))) {
          $this->error['pop3_port'] = $this->language->get('error_pop3_port');
          }

          if (isset($this->request->post['config_smtp_timeout']) && !$this->validate_form->esSoloNumeros($this->request->post['config_smtp_timeout'], $this->language->get('entry_smtp_timeout'))) {
          $this->error['smtp_timeout'] = $this->language->get('error_smtp_timeout');
          }

          if (isset($this->request->post['config_smtp_from_email']) && !$this->validate_form->validEmail($this->request->post['config_smtp_from_email'], $this->language->get('entry_smtp_from_email'))) {
          $this->error['smtp_from_email'] = $this->language->get('error_smtp_from_email');
          }

          if (isset($this->request->post['config_replyto_email']) && !$this->validate_form->validEmail($this->request->post['config_replyto_email'], $this->language->get('entry_replyto_email'))) {
          $this->error['replyto_email'] = $this->language->get('error_replyto_email');
          }

          if (isset($this->request->post['config_bounce_email']) && !$this->validate_form->validEmail($this->request->post['config_bounce_email'], $this->language->get('entry_bounce_email'))) {
          $this->error['bounce_email'] = $this->language->get('error_bounce_email');
          }
         */
        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * ControllerStoreStore::validateDelete()
     * 
     * @see Request
     * @see Language
     * @return bool
     */
    private function validateDelete() {
        if (!$this->user->hasPermission('modify', 'store/store')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * ControllerStoreCategory::activate()
     * activar o desactivar un objeto accedido por ajax
     * @return boolean
     * */
    public function activate() {
        if (!isset($_GET['id']))
            return false;
        $this->load->auto('store/store');
        $status = $this->modelStore->getStore($_GET['id']);
        if ($status) {
            if ($status['status'] == 0) {
                $this->modelStore->activate($_GET['id']);
                echo 1;
            } else {
                $this->modelStore->deactivate($_GET['id']);
                echo -1;
            }
        } else {
            echo 0;
        }
    }

    /**
     * ControllerStoreCategory::sortable()
     * ordenar el listado actualizando la posici�n de cada objeto
     * @return boolean
     * */
    public function sortable() {
        if (!isset($_POST['tr']))
            return false;
        $this->load->auto('store/store');
        $result = $this->modelStore->sortTable($_POST['tr']);
        if ($result) {
            echo 1;
        } else {
            echo 0;
        }
    }

    public function zone() {
        $output = '';
        $this->load->auto('localisation/zone');
        $results = $this->modelZone->getAll(array(
            'country_id'=>$this->request->get['country_id']
        ));

        foreach ($results as $result) {
            $output .= '<option value="' . $result['zone_id'] . '"';
            if (isset($this->request->get['zone_id']) && ($this->request->get['zone_id'] == $result['zone_id'])) {
                $output .= ' selected="selected"';
            }
            $output .= '>' . $result['name'] . '</option>';
        }
        if (!$results) {
            $output .= '<option value="0">' . $this->language->get('text_none') . '</option>';
        }
        $this->response->setOutput($output, $this->config->get('config_compression'));
    }

    public function template() {
        $template = basename($this->request->get['template']);
        if (file_exists(DIR_IMAGE . 'templates/' . $template . '.png')) {
            $image = HTTP_IMAGE . 'templates/' . $template . '.png';
        } else {
            $image = HTTP_IMAGE . 'no_image.jpg';
        }
        $this->response->setOutput('<img src="' . $image . '" width="200" />');
    }

    private function createStandardApp() {
        if ($this->createFolder($this->data['folder'])) {
            if ($this->data['create_app']) {
                $this->copyFiles();
                $this->copyFiles(DIR_ROOT . "web/assets/css", DIR_ROOT . "web/{$this->data['folder']}/css");
                $this->copyFiles(DIR_ROOT . "web/assets/js", DIR_ROOT . "web/{$this->data['folder']}/js");
                $this->copyFiles(DIR_ROOT . "web/assets/theme", DIR_ROOT . "web/{$this->data['folder']}/theme");
            }
            $this->createConfig();
        } else {
            return false;
        }
        return true;
    }

    private function createPath($folder) {
        if (empty($folder))
            return false;
        if ($folder !== mb_convert_encoding(mb_convert_encoding($folder, 'UTF-32', 'UTF-8'), 'UTF-8', 'UTF-32'))
            $folder = mb_convert_encoding($folder, 'UTF-8', mb_detect_encoding($folder));
        $folder = htmlentities($folder, ENT_NOQUOTES, 'UTF-8');
        $folder = preg_replace('`&([a-z]{1,2})(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig);`i', '\1', $folder);
        $folder = html_entity_decode($folder, ENT_NOQUOTES, 'UTF-8');
        $folder = preg_replace(array('`[^a-z0-9]`i', '`[-]+`'), '_', $folder);
        $folder = strtolower(trim($folder, '_'));

        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "url_alias WHERE `keyword` = '" . $folder . "'");
        if ($query->row['total']) {
            return false;
        }

        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "store WHERE `folder` = '" . $folder . "'");
        if ($query->row['total']) {
            return false;
        }

        $avoid = array('profile', 'products', 'productos', 'categories', 'categorias', 'carrito', 'cart', 'sitemap', 'contact', 'contacto', 'special', 'ofertas', 'blog', 'pages', 'paginas', 'buscar', 'search', 'pedidos', 'orders', 'mensajes', 'profile', 'pagos', 'payments', 'comentarios', 'reviews');

        if (in_array($folder, $avoid)) {
            return false;
        }

        $this->data['folder'] = $folder;
        return $folder;
    }

    private function createFolder($folder) {
        if (!is_dir(DIR_ROOT . "app/$folder"))
            $appFolder = mkdir(DIR_ROOT . "app/$folder", 0755);
        if (!is_dir(DIR_ROOT . "web/$folder"))
            $webFolder = mkdir(DIR_ROOT . "web/$folder", 0755);
        return ($appFolder && $webFolder);
    }

    private function createConfig() {
        if (file_exists(DIR_ROOT . "app/{$this->data['folder']}") && is_writable(DIR_ROOT . "app/{$this->data['folder']}")) {
            if ($this->data['create_app']) {
                $config = file_get_contents(DIR_SYSTEM . "config/config_custom.txt");
            } else {
                $config = file_get_contents(DIR_SYSTEM . "config/config_shared.txt");
            }
            $config = str_replace('%folder%', $this->data['folder'], $config);
            $config = str_replace('%store_id%', $this->data['store_id'], $config);
            $config = str_replace('%admin_path%', $this->data['adminPath'], $config);
            file_put_contents(DIR_ROOT . "app/{$this->data['folder']}/config.php", $config);
            chmod(DIR_ROOT . "app/{$this->data['folder']}/config.php", 0644);

            $index = file_get_contents(DIR_SYSTEM . "config/index.txt");
            $index = str_replace('%folder%', $this->data['folder'], $index);
            $index = str_replace('%package%', PACKAGE, $index);
            $index = str_replace('%version%', VERSION, $index);
            file_put_contents(DIR_ROOT . "web/{$this->data['folder']}/index.php", $index);
            chmod(DIR_ROOT . "web/{$this->data['folder']}/index.php", 0644);
            return true;
        }
        return false;
    }

    public function copyFiles($src = null, $dest = null) {
        if (!$src)
            $src = DIR_CATALOG;
        if (!$dest)
            $dest = DIR_ROOT . "app/{$this->data['folder']}";

        if (!file_exists($src))
            return false;
        if (is_dir($src))
            mkdir($dest, 0755);
        if (!is_dir($src))
            copy($src, $dest);
        foreach (scandir($src) as $item) {
            if ($item == '.' || $item == '..')
                continue;
            if (!$this->copyFiles($src . DIRECTORY_SEPARATOR . $item, $dest . DIRECTORY_SEPARATOR . $item))
                return false;
        }
        return true;
    }

    public function products() {
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . "GMT");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: application/json");
        $this->load->auto("store/store");
        $this->load->auto("store/product");
        if ($this->request->hasQuery('store_id')) {
            $rows = $this->modelProduct->getAll(array(
                'store_id'=>$this->request->getQuery('store_id')
            ));
            $products_by_store = [];
            foreach ($rows as $row) {
                $products_by_store[] = $row['product_id'];
            }
        }
        $cache = $this->cache->get("products.for.store.form." . $this->request->getQuery('store_id'));
        if ($cache) {
            $products = unserialize($cache);
        } else {
            $products = $this->modelProduct->getAll();
            $this->cache->set("products.for.store.form." . $this->request->getQuery('store_id'), serialize($products));
        }

        $output = [];

        foreach ($products as $product) {
            if (!empty($products_by_store) && in_array($product['product_id'], $products_by_store)) {
                $output[] = array(
                    'product_id' => $product['product_id'],
                    'pimage' => NTImage::resizeAndSave($product['pimage'], 50, 50),
                    'pname' => $product['pname'],
                    'class' => 'added',
                    'value' => $product['product_id']
                );
            } else {
                $output[] = array(
                    'product_id' => $product['product_id'],
                    'pimage' => NTImage::resizeAndSave($product['pimage'], 50, 50),
                    'pname' => $product['pname'],
                    'class' => 'add',
                    'value' => $product['product_id']
                );
            }
        }
        $this->load->auto('json');
        $this->response->setOutput(Json::encode($output), $this->config->get('config_compression'));
    }

    public function categories() {
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . "GMT");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: application/json");
        $this->load->auto("store/store");
        $this->load->auto("store/category");
        if ($this->request->hasQuery('store_id')) {
            $rows = $this->modelCategory->getAll(array(
                'store_id'=>$this->request->getQuery('store_id')
            ));
            $categories_by_store = [];
            foreach ($rows as $row) {
                $categories_by_store[] = $row['category_id'];
            }
        }
        $cache = $this->cache->get("categories.for.store.form." . $this->request->getQuery('store_id'));
        if ($cache) {
            $categories = unserialize($cache);
        } else {
            $categories = $this->modelCategory->getAll();
            $this->cache->set("categories.for.store.form." . $this->request->getQuery('store_id'), serialize($categories));
        }

        $output = [];

        foreach ($categories as $category) {
            if (!empty($categories_by_store) && in_array($category['category_id'], $categories_by_store)) {
                $output[] = array(
                    'id' => $category['category_id'],
                    'image' => NTImage::resizeAndSave($category['image'], 50, 50),
                    'name' => $category['name'],
                    'class' => 'added',
                    'value' => 1
                );
            } else {
                $output[] = array(
                    'id' => $category['category_id'],
                    'image' => NTImage::resizeAndSave($category['image'], 50, 50),
                    'name' => $category['name'],
                    'class' => 'add',
                    'value' => 0
                );
            }
        }
        $this->load->auto('json');
        $this->response->setOutput(Json::encode($output), $this->config->get('config_compression'));
    }

    public function manufacturers() {
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . "GMT");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: application/json");
        $this->load->auto("store/store");
        $this->load->auto("store/manufacturer");
        if ($this->request->hasQuery('store_id')) {
            $rows = $this->modelManufacturer->getAll(array(
                'store_id'=>$this->request->getQuery('store_id')
            ));
            $manufacturers_by_store = [];
            foreach ($rows as $row) {
                $manufacturers_by_store[] = $row['manufacturer_id'];
            }
        }
        $cache = $this->cache->get("manufacturers.for.store.form." . $this->request->getQuery('store_id'));
        if ($cache) {
            $manufacturers = unserialize($cache);
        } else {
            $manufacturers = $this->modelManufacturer->getAll();
            $this->cache->set("manufacturers.for.store.form." . $this->request->getQuery('store_id'), serialize($manufacturers));
        }

        $output = [];

        foreach ($manufacturers as $manufacturer) {
            if (!empty($manufacturers_by_store) && in_array($manufacturer['manufacturer_id'], $manufacturers_by_store)) {
                $output[] = array(
                    'id' => $manufacturer['manufacturer_id'],
                    'image' => NTImage::resizeAndSave($manufacturer['image'], 50, 50),
                    'name' => $manufacturer['name'],
                    'class' => 'added',
                    'value' => 1
                );
            } else {
                $output[] = array(
                    'id' => $manufacturer['manufacturer_id'],
                    'image' => NTImage::resizeAndSave($manufacturer['image'], 50, 50),
                    'name' => $manufacturer['name'],
                    'class' => 'add',
                    'value' => 0
                );
            }
        }
        $this->load->auto('json');
        $this->response->setOutput(Json::encode($output), $this->config->get('config_compression'));
    }

    public function pages() {
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . "GMT");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: application/json");
        $this->load->auto("store/store");
        $this->load->auto("content/page");
        if ($this->request->hasQuery('store_id')) {
            $rows = $this->modelPage->getAll(array(
                'store_id'=>$this->request->getQuery('store_id')
            ));
            $posts_by_store = [];
            foreach ($rows as $row) {
                $posts_by_store[] = $row['post_id'];
            }
        }
        $cache = $this->cache->get("pages.for.store.form." . $this->request->getQuery('store_id'));
        if ($cache) {
            $posts = unserialize($cache);
        } else {
            $posts = $this->modelPage->getAll();
            $this->cache->set("pages.for.store.form." . $this->request->getQuery('store_id'), serialize($posts));
        }

        $output = [];

        foreach ($posts as $post) {
            if (!empty($posts_by_store) && in_array($post['post_id'], $posts_by_store)) {
                $output[] = array(
                    'id' => $post['post_id'],
                    'name' => $post['title'],
                    'class' => 'added',
                    'value' => 1
                );
            } else {
                $output[] = array(
                    'id' => $post['post_id'],
                    'name' => $post['title'],
                    'class' => 'add',
                    'value' => 0
                );
            }
        }
        $this->load->auto('json');
        $this->response->setOutput(Json::encode($output), $this->config->get('config_compression'));
    }

    public function posts() {
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . "GMT");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: application/json");
        $this->load->auto("store/store");
        $this->load->auto("content/post");
        if ($this->request->hasQuery('store_id')) {
            $rows = $this->modelPost->getAll(array(
                'store_id'=>$this->request->getQuery('store_id')
            ));
            $posts_by_store = [];
            foreach ($rows as $row) {
                $posts_by_store[] = $row['post_id'];
            }
        }
        $cache = $this->cache->get("posts.for.store.form." . $this->request->getQuery('store_id'));
        if ($cache) {
            $posts = unserialize($cache);
        } else {
            $posts = $this->modelPost->getAll();
            $this->cache->set("posts.for.store.form." . $this->request->getQuery('store_id'), serialize($posts));
        }

        $output = [];

        foreach ($posts as $post) {
            if (!empty($posts_by_store) && in_array($post['post_id'], $posts_by_store)) {
                $output[] = array(
                    'id' => $post['post_id'],
                    'name' => $post['title'],
                    'class' => 'added',
                    'value' => 1
                );
            } else {
                $output[] = array(
                    'id' => $post['post_id'],
                    'name' => $post['title'],
                    'class' => 'add',
                    'value' => 0
                );
            }
        }
        $this->load->auto('json');
        $this->response->setOutput(Json::encode($output), $this->config->get('config_compression'));
    }

    public function postcategories() {
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . "GMT");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: application/json");
        $this->load->auto("store/store");
        $this->load->auto("content/post_category");
        if ($this->request->hasQuery('store_id')) {
            $rows = $this->modelPost_category->getAll(array(
                'store_id'=>$this->request->getQuery('store_id')
            ));
            $post_categories_by_store = [];
            foreach ($rows as $row) {
                $post_categories_by_store[] = $row['category_id'];
            }
        }
        $cache = $this->cache->get("post_categories.for.store.form." . $this->request->getQuery('store_id'));
        if ($cache) {
            $post_categories = unserialize($cache);
        } else {
            $post_categories = $this->modelPost_category->getAll();
            $this->cache->set("post_categories.for.store.form." . $this->request->getQuery('store_id'), serialize($post_categories));
        }

        $output = [];

        foreach ($post_categories as $post_category) {
            if (!empty($post_categories_by_store) && in_array($post_category['category_id'], $post_categories_by_store)) {
                $output[] = array(
                    'id' => $post_category['category_id'],
                    'name' => $post_category['name'],
                    'class' => 'added',
                    'value' => 1
                );
            } else {
                $output[] = array(
                    'id' => $post_category['category_id'],
                    'name' => $post_category['name'],
                    'class' => 'add',
                    'value' => 0
                );
            }
        }
        $this->load->auto('json');
        $this->response->setOutput(Json::encode($output), $this->config->get('config_compression'));
    }

    public function banners() {
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . "GMT");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: application/json");
        $this->load->auto("store/store");
        $this->load->auto("content/banner");
        if ($this->request->hasQuery('store_id')) {
            $rows = $this->modelBanner->getAll(array(
                'store_id'=>$this->request->getQuery('store_id')
            ));
            $banners_by_store = [];
            foreach ($rows as $row) {
                $banners_by_store[] = $row['banner_id'];
            }
        }
        $cache = $this->cache->get("banners.for.store.form." . $this->request->getQuery('store_id'));
        if ($cache) {
            $banners = unserialize($cache);
        } else {
            $banners = $this->modelBanner->getAll();
            $this->cache->set("banners.for.store.form." . $this->request->getQuery('store_id'), serialize($banners));
        }

        $output = [];

        foreach ($banners as $banner) {
            if (!empty($banners_by_store) && in_array($banner['banner_id'], $banners_by_store)) {
                $output[] = array(
                    'id' => $banner['banner_id'],
                    'name' => $banner['name'],
                    'class' => 'added',
                    'value' => 1
                );
            } else {
                $output[] = array(
                    'id' => $banner['banner_id'],
                    'name' => $banner['name'],
                    'class' => 'add',
                    'value' => 0
                );
            }
        }
        $this->load->auto('json');
        $this->response->setOutput(Json::encode($output), $this->config->get('config_compression'));
    }

    public function downloads() {
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . "GMT");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: application/json");
        $this->load->auto("store/store");
        $this->load->auto("store/download");
        if ($this->request->hasQuery('store_id')) {
            $rows = $this->modelDownload->getAll(array(
                'store_id'=>$this->request->getQuery('store_id')
            ));
            $downloads_by_store = [];
            foreach ($rows as $row) {
                $downloads_by_store[] = $row['download_id'];
            }
        }
        $cache = $this->cache->get("downloads.for.store.form." . $this->request->getQuery('store_id'));
        if ($cache) {
            $downloads = unserialize($cache);
        } else {
            $downloads = $this->modelDownload->getAll();
            $this->cache->set("downloads.for.store.form." . $this->request->getQuery('store_id'), serialize($downloads));
        }

        $output = [];

        foreach ($downloads as $download) {
            if (!empty($downloads_by_store) && in_array($download['download_id'], $downloads_by_store)) {
                $output[] = array(
                    'id' => $download['download_id'],
                    'name' => $download['name'],
                    'class' => 'added',
                    'value' => 1
                );
            } else {
                $output[] = array(
                    'id' => $download['download_id'],
                    'name' => $download['name'],
                    'class' => 'add',
                    'value' => 0
                );
            }
        }
        $this->load->auto('json');
        $this->response->setOutput(Json::encode($output), $this->config->get('config_compression'));
    }

    public function coupons() {
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . "GMT");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: application/json");
        $this->load->auto("store/store");
        $this->load->auto("sale/coupon");
        if ($this->request->hasQuery('store_id')) {
            $rows = $this->modelCoupon->getAll(array(
                'store_id'=>$this->request->getQuery('store_id')
            ));
            $coupons_by_store = [];
            foreach ($rows as $row) {
                $coupons_by_store[] = $row['coupon_id'];
            }
        }
        $cache = $this->cache->get("coupons.for.store.form." . $this->request->getQuery('store_id'));
        if ($cache) {
            $coupons = unserialize($cache);
        } else {
            $coupons = $this->modelCoupon->getCoupons();
            $this->cache->set("coupons.for.store.form." . $this->request->getQuery('store_id'), serialize($coupons));
        }

        $output = [];

        foreach ($coupons as $coupon) {
            if (!empty($coupons_by_store) && in_array($coupon['coupon_id'], $coupons_by_store)) {
                $output[] = array(
                    'id' => $coupon['coupon_id'],
                    'name' => $coupon['name'],
                    'class' => 'added',
                    'value' => 1
                );
            } else {
                $output[] = array(
                    'id' => $coupon['coupon_id'],
                    'name' => $coupon['name'],
                    'class' => 'add',
                    'value' => 0
                );
            }
        }
        $this->load->auto('json');
        $this->response->setOutput(Json::encode($output), $this->config->get('config_compression'));
    }

    public function bankaccounts() {
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . "GMT");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: application/json");
        $this->load->auto("store/store");
        $this->load->auto("sale/bank_account");
        if ($this->request->hasQuery('store_id')) {
            $rows = $this->modelBank_account->getAll(array(
                'store_id'=>$this->request->getQuery('store_id')
            ));
            $bank_accounts_by_store = [];
            foreach ($rows as $row) {
                $bank_accounts_by_store[] = $row['bank_account_id'];
            }
        }
        $cache = $this->cache->get("bank_accounts.for.store.form." . $this->request->getQuery('store_id'));
        if ($cache) {
            $bank_accounts = unserialize($cache);
        } else {
            $bank_accounts = $this->modelBank_account->getAll();
            $this->cache->set("bank_accounts.for.store.form." . $this->request->getQuery('store_id'), serialize($bank_accounts));
        }

        $output = [];

        foreach ($bank_accounts as $bank_account) {
            if (!empty($bank_accounts_by_store) && in_array($bank_account['bank_account_id'], $bank_accounts_by_store)) {
                $output[] = array(
                    'id' => $bank_account['bank_account_id'],
                    'name' => $bank_account['name'],
                    'class' => 'added',
                    'value' => 1
                );
            } else {
                $output[] = array(
                    'id' => $bank_account['bank_account_id'],
                    'name' => $bank_account['name'],
                    'class' => 'add',
                    'value' => 0
                );
            }
        }
        $this->load->auto('json');
        $this->response->setOutput(Json::encode($output), $this->config->get('config_compression'));
    }

    public function customers() {
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . "GMT");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: application/json");
        $this->load->auto("store/store");
        $this->load->auto("sale/customer");
        if ($this->request->hasQuery('store_id')) {
            $rows = $this->modelCustomer->getAll(array(
                'store_id'=>$this->request->getQuery('store_id')
            ));
            $customers_by_store = [];
            foreach ($rows as $row) {
                $customers_by_store[] = $row['customer_id'];
            }
        }
        $cache = $this->cache->get("customers.for.store.form." . $this->request->getQuery('store_id'));
        if ($cache) {
            $customers = unserialize($cache);
        } else {
            $customers = $this->modelCustomer->getAll();
            $this->cache->set("customers.for.store.form." . $this->request->getQuery('store_id'), serialize($customers));
        }

        $output = [];

        foreach ($customers as $customer) {
            if (!empty($customers_by_store) && in_array($customer['customer_id'], $customers_by_store)) {
                $output[] = array(
                    'id' => $customer['customer_id'],
                    'name' => $customer['company'],
                    'class' => 'added',
                    'value' => 1
                );
            } else {
                $output[] = array(
                    'id' => $customer['customer_id'],
                    'name' => $customer['company'],
                    'class' => 'add',
                    'value' => 0
                );
            }
        }
        $this->load->auto('json');
        $this->response->setOutput(Json::encode($output), $this->config->get('config_compression'));
    }

    public function menus() {
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . "GMT");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: application/json");
        $this->load->auto("store/store");
        $this->load->auto("content/menu");
        if ($this->request->hasQuery('store_id')) {
            $rows = $this->modelMenu->getAll(array(
                'store_id'=>$this->request->getQuery('store_id')
            ));
            $menus_by_store = [];
            foreach ($rows as $row) {
                $menus_by_store[] = $row['menu_id'];
            }
        }
        $cache = $this->cache->get("menus.for.store.form." . $this->request->getQuery('store_id'));
        if ($cache) {
            $menus = unserialize($cache);
        } else {
            $menus = $this->modelMenu->getAll();
            $this->cache->set("menus.for.store.form." . $this->request->getQuery('store_id'), serialize($menus));
        }

        $output = [];

        foreach ($menus as $menu) {
            if (!empty($menus_by_store) && in_array($menu['menu_id'], $menus_by_store)) {
                $output[] = array(
                    'id' => $menu['menu_id'],
                    'name' => $menu['name'],
                    'class' => 'added',
                    'value' => 1
                );
            } else {
                $output[] = array(
                    'id' => $menu['menu_id'],
                    'name' => $menu['name'],
                    'class' => 'add',
                    'value' => 0
                );
            }
        }
        $this->load->auto('json');
        $this->response->setOutput(Json::encode($output), $this->config->get('config_compression'));
    }

}
