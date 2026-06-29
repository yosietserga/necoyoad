<?php

/**
 * ControllerStyleWidget
 * 
 * @package NecoTienda
 * @author Yosiet Serga
 * @copyright Inversiones Necoyoad, C.A.
 * @version 1.0.0
 * @access public
 * @see Controller
 */
class ControllerStyleWidget extends Controller {

    private $error = [];

    /**
     * ControllerStyleWidget::index()
     * 
     * @see Load
     * @see Document
     * @see Language
     * @see getList
     * @return void
     */
    public function index() {
        $this->document->title = $this->data['heading_title'] = $this->language->get('heading_title');

        $extensions = $this->modelExtension->getInstalled('module');
        $this->data['extensions'] = [];
        $modules = glob(DIR_APPLICATION . "controller/module/*");
        if ($modules) {
            foreach ($modules as $module) {
                if (!file_exists($module . '/widget.php'))
                    continue;
                $extension = basename($module, '/widget.php');
                $m = basename($module);
                $this->load->language('module/' . $m);

                if (in_array($extension, $extensions)) {
                    $this->data['modules'][] = array(
                        'widget' => $extension,
                        'name' => $this->language->get('heading_title'),
                        'description' => $this->language->get('description')
                    );
                }
            }
        }

        $this->data['stores'] = $this->modelStore->getAll();
        $this->data['store_id'] = ($this->request->hasQuery('store_id')) ? $this->request->getQuery('store_id') : 0;
        $this->data['landing_page'] = ($this->request->hasQuery('landing_page')) ? $this->request->getQuery('landing_page') : 'all';
        if ((int) $this->data['store_id'] !== 0)
            $this->data['store_exists'] = $this->modelStore->getById($this->data['store_id']);
        if ((int) $this->data['store_id'] === 0)
            $this->data['store_exists'] = true;

        $this->data['routes'] = $this->modelWidget->getRoutes();

        $data['store_id'] = $this->data['store_id'] ?? 0;
        $data['full_tree'] = $this->data['full_tree'] ?? null;

        $rows = $this->modelWidget->getRows(array(
            'store_id'=>$this->data['store_id'],
            'landing_page'=>$this->data['landing_page']
        ));
        foreach ($rows as $k=>$row) {
            $r['settings'] = unserialize($row['value']);

            $cols = $this->modelWidget->getCols(array(
                'store_id'=>$this->data['store_id'],
                'landing_page'=>$this->data['landing_page'],
                'row_id'=>$row['key']
            ));

            $c = [];
            foreach ($cols as $j=>$col) {
                $widgets = $this->modelWidget->getWidgets(array(
                    'store_id'=>$this->data['store_id'],
                    'col_id'=>$col['key'],
                    'position'=>$row['group']
                ));

                $c[$j] = array(
                    'column_id'=>$col['key'],
                    'settings'=>unserialize($col['value']),
                    'widgets'=>$widgets
                );
            }

            $this->data['rows'][$row['group']][] = array(
                'row_id'=>$row['key'],
                'settings'=> $r['settings'],
                'columns'=>$c
            );
        }

        $this->document->breadcrumbs = [];
        $this->document->breadcrumbs[] = array(
            'href' => Url::createAdminUrl('common/home'),
            'text' => $this->language->get('text_home'),
            'separator' => false
        );

        $this->document->breadcrumbs[] = array(
            'href' => Url::createAdminUrl('style/widget'),
            'text' => $this->language->get('heading_title'),
            'separator' => ' :: '
        );

        $this->data['error_warning'] = isset($this->error['warning']) ? $this->error['warning'] : '';

        if ($this->session->has('success')) {
            $this->data['success'] = $this->session->get('success');
            $this->session->clear('success');
        } else {
            $this->data['success'] = '';
        }

        $template = ($this->config->get('default_admin_view_style_widget')) ? $this->config->get('default_admin_view_style_widget') : 'style/widget.tpl';
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

    public function load() {
        if ($this->request->hasQuery('oid')) {
            $this->load->auto('setting/extension');
            $this->load->auto('store/store');
            $this->load->auto('style/widget');
            $filter_name = '';
            $extensions = $this->modelExtension->getInstalled('module');
            $this->data['extensions'] = [];
            $modules = glob(DIR_APPLICATION . "controller/module/$filter_name*");
            if ($modules) {
                foreach ($modules as $module) {
                    if (!file_exists($module . '/widget.php'))
                        continue;
                    $extension = basename($module, '/widget.php');
                    $this->load->language('module/' . $extension);
    
                    if (in_array($extension, $extensions)) {
                        $this->data['modules'][] = array(
                            'widget' => $extension,
                            'name' => $this->language->get('heading_title'),
                            'description' => $this->language->get('description')
                        );
                    }
                }
            }

            $data['landing_page'] = ($this->request->hasQuery('landing_page')) ? $this->request->getQuery('landing_page') : 'all';
            $data['store_id'] = ($this->request->hasQuery('store_id')) ? $this->request->getQuery('store_id') : 0;
            $data['full_tree'] = true;
            $data['object_type'] = $this->request->getQuery('ot');
            $data['object_id'] = $this->request->getQuery('oid');

            $rows = $this->modelWidget->getRows(array(
                'store_id'=>$data['store_id'],
                'object_type'=>$data['object_type'],
                'object_id'=>$data['object_id'],
                'full_tree'=>$data['full_tree'],
                'landing_page'=>$data['landing_page']
            ), false);

            foreach ($rows as $k=>$row) {
                $r['settings'] = unserialize($row['value']);

                foreach ($row['columns'] as $j=>$col) {
                    $row['columns'][$j] = array(
                        'column_id'=>$col['key'],
                        'settings'=>unserialize($col['value']),
                        'widgets'=>$col['widgets']
                    );
                }

                $this->data['rows'][$row['group']][] = array(
                    'row_id'=>$row['key'],
                    'settings'=> $r['settings'],
                    'columns'=>$row['columns']
                );
            }

            $template = ($this->config->get('default_admin_view_common_form_widgets')) ? $this->config->get('default_admin_view_common_form_widgets') : 'common/form_widgets.tpl';
            if (file_exists(DIR_TEMPLATE . $this->config->get('config_admin_template') . '/'. $template)) {
                $this->template = $this->config->get('config_admin_template') . '/' . $template;
            } else {
                $this->template = 'default/' . $template;
            }

            $this->response->setOutput($this->render(true), $this->config->get('config_compression'));
        }
    }

    public function saveRow() {
        $data = $this->request->post;

        parse_str(str_replace('&amp;','&',($data['settings'])), $settings);

        $data['settings'] = $settings;
        if ($data['settings']['position'] == 'undefined') $data['settings']['position'] = $data['position'];

        foreach($data['settings'] as $k=>$v) {
            $data['settings']['filter_'.$k] = $k .'='. $v;
        }

        if (isset($data['row_id']))
            $data['settings']['filter_row_id'] = 'row_id='. $data['row_id'];
        if (isset($data['position']))
            $data['settings']['filter_position'] = 'position='. $data['position'];
        if (isset($data['landing_page']))
            $data['settings']['filter_landing_page'] = 'landing_page='. $data['landing_page'];
        if (isset($data['object_type']))
            $data['settings']['filter_object_type'] = 'object_type='. $data['object_type'];
        if (isset($data['object_id']))
            $data['settings']['filter_object_id'] = 'object_id='. $data['object_id'];
        if (isset($data['store_id']))
            $data['settings']['filter_store_id'] = 'store_id='. $data['store_id'];

        $this->load->helper('widgets');
        $widget = new NecoWidget($this->registry);
        $widget->saveRow($data);
    }

    public function saveCol() {
        $data = $this->request->post;
        parse_str(str_replace('&amp;','&',($data['settings'])), $settings);

        $data['settings'] = $settings;
        if ($data['settings']['position'] == 'undefined') $data['settings']['position'] = $data['position'];

        foreach($data['settings'] as $k=>$v) {
            $data['settings']['filter_'.$k] = $k .'='. $v;
        }

        if (isset($data['col_id']))
            $data['settings']['filter_col_id'] = 'col_id='. $data['col_id'];
        if (isset($data['row_id']))
            $data['settings']['filter_row_id'] = 'row_id='. $data['row_id'];
        if (isset($data['position']))
            $data['settings']['filter_position'] = 'position='. $data['position'];
        if (isset($data['landing_page']))
            $data['settings']['filter_landing_page'] = 'landing_page='. $data['landing_page'];
        if (isset($data['object_type']))
            $data['settings']['filter_object_type'] = 'object_type='. $data['object_type'];
        if (isset($data['object_id']))
            $data['settings']['filter_object_id'] = 'object_id='. $data['object_id'];
        if (isset($data['store_id']))
            $data['settings']['filter_store_id'] = 'store_id='. $data['store_id'];

        $this->load->helper('widgets');
        $widget = new NecoWidget($this->registry);
        $widget->saveCol($data);
    }

    /**
     * ControllerStoreCategory::delete()
     * elimina un objeto
     * @return boolean
     * */
    public function delete() {
        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            foreach ($this->request->post['selected'] as $id) {
                $this->modelWidget->delete($id);
            }
        } else {
            $this->modelWidget->delete($_GET['name']);
        }
    }

    /**
     * ControllerStoreCategory::deleteRow()
     * elimina un objeto
     * @return boolean
     * */
    public function deleteRow() {
        $this->load->auto('style/widget');
        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            foreach ($this->request->post['selected'] as $id) {
                $this->modelWidget->deleteRow($id);
            }
        } else {
            $this->modelWidget->deleteRow(str_replace('#','',$_GET['row_id']));
        }
    }

    /**
     * ControllerStoreCategory::deleteRow()
     * elimina un objeto
     * @return boolean
     * */
    public function deleteColumn() {
        $this->load->auto('style/widget');
        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            foreach ($this->request->post['selected'] as $id) {
                $this->modelWidget->deleteColumn($id);
            }
        } else {
            $this->modelWidget->deleteColumn(str_replace('#','',$_GET['col_id']));
        }
    }

    /**
     * ControllerStoreCategory::sortable()
     * ordenar el listado actualizando la posici�n de cada objeto
     * @return boolean
     * */
    public function sortable() {
        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            $this->modelWidget->sortWidget($this->request->post);
        }
    }

    /**
     * ControllerStoreCategory::sortable()
     * ordenar el listado actualizando la posici�n de cada objeto
     * @return boolean
     * */
    public function sortrow() {
        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            $this->load->auto('style/widget');
            $this->modelWidget->sortRow($this->request->post);
        }
    }

    /**
     * ControllerStoreCategory::sortable()
     * ordenar el listado actualizando la posici�n de cada objeto
     * @return boolean
     * */
    public function sortCol() {
        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            $this->load->auto('style/widget');
            $this->modelWidget->sortCol($this->request->post);
        }
    }

    public function getalljson() {
    $this->load->auto('setting/extension');
        $extensions = $this->modelExtension->getInstalled('module');
        $json = [];
        $filter_name = '';
        $modules = glob(DIR_APPLICATION . "controller/module/$filter_name*");
        if ($modules) {
            foreach ($modules as $module) {
                if (!file_exists($module . '/widget.php'))
                    continue;
                $extension = basename($module, '/widget.php');
                $this->load->language('module/' . $extension);
                $action = [];

                if (in_array($extension, $extensions)) {
                    $json['modules'][] = array(
                        'widget' => $extension,
                        'name' => $this->language->get('heading_title'),
                        'description' => $this->language->get('description')
                    );
                }
            }
        }
        $this->load->auto('json');
        
        $this->response->setOutput(Json::encode($json), $this->config->get('config_compression'));
    }
}
