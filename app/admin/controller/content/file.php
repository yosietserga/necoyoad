<?php

class ControllerContentFile extends Controller {

    private $error = [];

    public function index() {
        $this->load->language('content/file');
        $this->load->library('url');
        $this->data['Url'] = new Url;

        $this->document->breadcrumbs = [];
        $this->document->breadcrumbs[] = array(
            'href' => Url::createAdminUrl("common/home"),
            'text' => $this->language->get('text_home'),
            'separator' => false
        );
        $this->document->breadcrumbs[] = array(
            'href' => Url::createAdminUrl("content/file") . $url,
            'text' => $this->language->get('heading_title'),
            'separator' => ' :: '
        );
        
        $this->document->title = $this->language->get('heading_title');

        if (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
        }

        if (isset($this->session->data['success'])) {
            $this->data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        } else {
            $this->data['success'] = '';
        }

        if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
            $this->data['base'] = HTTP_HOME;
        } else {
            $this->data['base'] = HTTP_HOME;
        }

        $this->data['action'] = Url::createAdminUrl("content/file/uploader");

        $this->data['directory'] = HTTP_IMAGE . 'data/';

        if (isset($this->request->get['field'])) {
            $this->data['field'] = $this->request->get['field'];
        } else {
            $this->data['field'] = '';
        }

        if (isset($this->request->get['CKEditorFuncNum'])) {
            $this->data['fckeditor'] = true;
        } else {
            $this->data['fckeditor'] = false;
        }
        
        $this->scripts = array_merge($this->scripts, $scripts);

        $template = ($this->config->get('default_admin_view_file_list')) ? $this->config->get('default_admin_view_file_list') : 'content/file_list.tpl';
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

    public function image() {
        $this->load->library('image');
        if (isset($this->request->post['image'])) {
            $this->response->setOutput(NTImage::resizeAndSave($this->request->post['image'], 60, 60));
        }
    }

    public function directory() {
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . "GMT");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: application/json");
        
        $json = [];
        $dir = $this->request->hasQuery('directory') ? $this->request->getQuery('directory') : null;
        if ($dir) {
            $directories = glob(rtrim(DIR_IMAGE . 'data/' . str_replace('../', '', $dir), '/') . '/*', GLOB_ONLYDIR);
            if ($directories) {
                $i = 0;
                foreach ($directories as $directory) {
                    $json[$i]['text'] = basename($directory);
                    $json[$i]['attributes']['directory'] = substr($directory, strlen(DIR_IMAGE . 'data/'));
                    $children = glob(rtrim($directory, '/') . '/*', GLOB_ONLYDIR);
                    if ($children) {
                        $json[$i]['children'] = ' ';
                    }
                    $i++;
                }
            }
        } else {
            $directories = glob(DIR_IMAGE . 'data/*', GLOB_ONLYDIR);
            $json['text'] = 'Inicio';
            $json['li_attr']['directory'] = '';
            $json['state'] = array(
                'opened'=>true,
                'selected'=>true
            );
            if ($directories) {
                $json['children'] = $this->__directory('');
                /*
                foreach ($directories as $i => $directory) {
                    $json['children'][$i]['text'] = basename($directory);
                    $json['children'][$i]['li_attr']['directory'] = substr($directory, strlen(DIR_IMAGE . 'data/'));
                    $children = glob(rtrim($directory, '/') . '/*', GLOB_ONLYDIR);
                    if ($children) {
                        $json['children'][$i]['children'] = ' ';
                    }
                }
                */
            }
        }
        
        $this->load->library('json');
        $this->response->setOutput(Json::encode($json));
    }
    

    protected function __directory($dir) {
        $children = [];
        $directories = glob(rtrim(DIR_IMAGE . 'data/' . str_replace('../', '', $dir), '/') . '/*', GLOB_ONLYDIR);
        if ($directories) {
            foreach ($directories as $i => $directory) {
                $children[$i]['text'] = basename($directory);
                $subdir = substr($directory, strlen(DIR_IMAGE . 'data/'));
                $children[$i]['li_attr']['directory'] = $subdir;
                if (glob(rtrim($directory, '/') . '/*', GLOB_ONLYDIR)) {
                    $children[$i]['children'] = $this->__directory($subdir);
                }
            }
        }
        return $children;
    }
    
    public function directory_old() {

        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . "GMT");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: application/json");

        $json = [];
        $dir = $this->request->hasQuery('directory') ? $this->request->getQuery('directory') : null;
        if ($dir) {
            $directories = glob(rtrim(DIR_IMAGE . 'data/' . str_replace('../', '', $dir), '/') . '/*', GLOB_ONLYDIR);
        } else {
            $directories = glob(DIR_IMAGE . 'data/*', GLOB_ONLYDIR);
        }
        
        $json[0] = 'Inicio';
        if ($directories) {
            $i = 1;
            foreach ($directories as $directory) {
                $json[$i]['data'] = basename($directory);
                $json[$i]['attributes']['directory'] = substr($directory, strlen(DIR_IMAGE . 'data/'));
                $children = glob(rtrim($directory, '/') . '/*', GLOB_ONLYDIR);
                if ($children) {
                    $json[$i]['children'] = ' ';
                }
                $i++;
            }
        }
        $this->load->library('json');
        $this->response->setOutput(Json::encode($json));
    }
    
    public function files() {
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . "GMT");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: application/json");

        $json = [];
        $dir = $this->request->hasQuery('directory') ? $this->request->getQuery('directory') : null;
        if ($dir && $dir!='undefined') {
            $directory = DIR_IMAGE . 'data/' . str_replace('../', '', $dir);
        } else {
            $directory = DIR_IMAGE . 'data/';
        }
        
        $allowed = array(
            '.jpg',
            '.jpeg',
            '.png',
            '.pdf',
            '.doc',
            '.docx',
            '.xls',
            '.xlsx',
            '.txt',
            '.csv',
            '.gif'
        );

        $files = glob(rtrim($directory, '/') . '/*');

        foreach ($files as $file) {
            if (is_file($file)) {
                $ext = strrchr($file, '.');
            } else {
                $ext = '';
            }

            if (in_array(strtolower($ext), $allowed)) {
                $size = filesize($file);
                $i = 0;
                $suffix = array(
                    'B',
                    'KB',
                    'MB',
                    'GB',
                    'TB',
                    'PB',
                    'EB',
                    'ZB',
                    'YB'
                );

                while (($size / 1024) > 1) {
                    $size = $size / 1024;
                    $i++;
                }

                if ($ext == '.pdf') {
                    $thumb = HTTP_IMAGE . "icons/pdf.png";
                } elseif ($ext == '.doc' || $ext == '.docx') {
                    $thumb = HTTP_IMAGE . "icons/doc.png";
                } elseif ($ext == '.xls' || $ext == '.xlsx') {
                    $thumb = HTTP_IMAGE . "icons/xls.png";
                } elseif ($ext == '.txt') {
                    $thumb = HTTP_IMAGE . "icons/txt.png";
                } elseif ($ext == '.csv') {
                    $thumb = HTTP_IMAGE . "icons/csv.png";
                } elseif (!in_array($ext, $allowed)) {
                    $thumb = HTTP_IMAGE . "icons/_blank.png";
                } else {
                    $thumb = NTImage::resizeAndSave(substr($file, strlen(DIR_IMAGE)), 60, 60);
                }
                
                $json[] = array(
                    'file' => substr($file, strlen(DIR_IMAGE . 'data/')),
                    'filename' => basename($file),
                    'size' => round(substr($size, 0, strpos($size, '.') + 4), 2) . $suffix[$i],
                    'thumb' => $thumb
                );
            }
        }

        $this->load->library('json');
        $this->response->setOutput(Json::encode($json));
    }

    public function create() {
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . "GMT");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: application/json");

        $this->load->language('common/filemanager');
        $json = [];
        
        if ($this->request->hasQuery('directory') && $this->request->getQuery('directory') != 'undefined') {
            $directory = rtrim(DIR_IMAGE . 'data/' . str_replace('../', '', $this->request->getQuery('directory')), '/');
        } else {
            $directory = DIR_IMAGE . 'data';
        }
        
        if ($this->request->hasQuery('name')) {
            if (!is_dir($directory)) {
                $json['error'] = $this->language->get('error_directory');
            }

            if (file_exists($directory . '/' . str_replace('../', '', $this->request->getQuery('name')))) {
                $json['error'] = $this->language->get('error_exists');
            }
        } else {
            $json['error'] = $this->language->get('error_name');
        }

        if (!$this->user->hasPermission('modify', 'common/filemanager')) {
            $json['error'] = $this->language->get('error_permission');
        }

        if (!isset($json['error'])) {
            $path = strtolower($this->request->getQuery('name'));
            $path = str_replace(' ', '-', $path);
            $path = str_replace('á', 'a', $path);
            $path = str_replace('é', 'e', $path);
            $path = str_replace('í', 'i', $path);
            $path = str_replace('ó', 'o', $path);
            $path = str_replace('ú', 'u', $path);
            $path = str_replace('ñ', 'n', $path);

            if ($path !== mb_convert_encoding(mb_convert_encoding($path, 'UTF-32', 'UTF-8'), 'UTF-8', 'UTF-32'))
                $path = mb_convert_encoding($path, 'UTF-8', mb_detect_encoding($path));
            $path = htmlentities($path, ENT_NOQUOTES, 'UTF-8');
            $path = preg_replace('`&([a-z]{1,2})(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig);`i', '\1', $path);
            $path = html_entity_decode($path, ENT_NOQUOTES, 'UTF-8');
            $path = preg_replace(array('`[^a-z0-9]`i', '`[-]+`'), '-', $path);
            $path = strtolower(trim($path, '-'));

            mkdir($directory . '/' . str_replace('../', '', $path), 0777);

            $json['success'] = $this->language->get('text_create');
            $json['id'] = $this->request->getQuery('id');
            $json['name'] = $path;
            $json['path'] = $path;
            $directory_ = $this->request->getQuery('directory');
            if ($directory_ == 'undefined' || empty($directory_)) {
                $json['directory'] = $path;
            } else {
                $json['directory'] = $this->request->getQuery('directory') .'/'. $path;
            }
        }

        $this->load->library('json');

        $this->response->setOutput(Json::encode($json));
    }

    public function delete() {
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . "GMT");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: application/json");

        $this->load->language('common/filemanager');

        $json = [];
        
        if (!$this->user->hasPermission('modify', 'common/filemanager')) {
            $json['error'] = $this->language->get('error_permission');
        }

        if ($this->request->hasQuery('path')) {
            $paths = explode(':', $this->request->getQuery('path'));
            foreach ($paths as $dir) {
                if (empty($dir) || $dir === ':' || strpos($dir, 'undefined')) continue;
                $path = rtrim(DIR_IMAGE . 'data/' . str_replace('../', '', $dir), '/');

                if (!file_exists($path)) {
                    $json['error'] = $this->language->get('error_select');
                }

                if ($path == rtrim(DIR_IMAGE . 'data/', '/')) {
                    $json['error'] = $this->language->get('error_delete');
                }
                
                if (!isset($json['error'])) {
                    if (is_dir($path)) {
                        $this->recursiveDelete($path);
                    }
                }
            }
        } elseif ($this->request->hasPost('filess')) {
            foreach ($this->request->getPost('filess') as $file) {
                if (!isset($json['error'])) {
                    $file = rtrim(DIR_IMAGE . 'data/' . str_replace('../', '', $file), '/');
                    if (is_file($file)) {
                        unlink($file);
                    } elseif (!file_exists($file)) {
                        $json['error'] = $this->language->get('error_select');
                    }
                }
            }
        } else {
            $json['error'] = $this->language->get('error_select');
        }

        $this->load->library('json');

        $this->response->setOutput(Json::encode($json));
    }

    protected function recursiveDelete($directory) {
        if (is_dir($directory)) {
            $handle = opendir($directory);
        }

        if (!$handle) {
            return false;
        }

        while (false !== ($file = readdir($handle))) {
            if ($file != '.' && $file != '..') {
                if (!is_dir($directory . '/' . $file)) {
                    unlink($directory . '/' . $file);
                } else {
                    $this->recursiveDelete($directory . '/' . $file);
                }
            }
        }

        closedir($handle);

        rmdir($directory);

        return true;
    }

    public function move() {
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . "GMT");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: application/json");

        $this->load->language('common/filemanager');

        $json = [];

        if (isset($this->request->post['from']) && isset($this->request->post['to'])) {
            $from = rtrim(DIR_IMAGE . 'data/' . str_replace('../', '', $this->request->post['from']), '/');

            if (!file_exists($from)) {
                $json['error'] = $this->language->get('error_missing');
            }

            if ($from == DIR_IMAGE . 'data') {
                $json['error'] = $this->language->get('error_default');
            }

            $to = rtrim(DIR_IMAGE . 'data/' . str_replace('../', '', $this->request->post['to']), '/');

            if (!file_exists($to)) {
                $json['error'] = $this->language->get('error_move');
            }

            if (file_exists($to . '/' . basename($from))) {
                $json['error'] = $this->language->get('error_exists');
            }
        } else {
            $json['error'] = $this->language->get('error_directory');
        }

        if (!$this->user->hasPermission('modify', 'common/filemanager')) {
            $json['error'] = $this->language->get('error_permission');
        }

        if (!isset($json['error'])) {
            rename($from, $to . '/' . basename($from));

            $json['success'] = $this->language->get('text_move');
        }

        $this->load->library('json');

        $this->response->setOutput(Json::encode($json));
    }

    public function copy() {
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . "GMT");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: application/json");

        $this->load->language('common/filemanager');

        $json = [];

        if (isset($this->request->post['path']) && isset($this->request->post['name'])) {
            if ((strlen(utf8_decode($this->request->post['name'])) < 3) || (strlen(utf8_decode($this->request->post['name'])) > 255)) {
                $json['error'] = $this->language->get('error_filename');
            }

            $old_name = rtrim(DIR_IMAGE . 'data/' . str_replace('../', '', $this->request->post['path']), '/');

            if (!file_exists($old_name) || $old_name == DIR_IMAGE . 'data') {
                $json['error'] = $this->language->get('error_copy');
            }

            if (is_file($old_name)) {
                $ext = strrchr($old_name, '.');
            } else {
                $ext = '';
            }

            $new_name = dirname($old_name) . '/' . str_replace('../', '', $this->request->post['name'] . $ext);

            if (file_exists($new_name)) {
                $json['error'] = $this->language->get('error_exists');
            }
        } else {
            $json['error'] = $this->language->get('error_select');
        }

        if (!$this->user->hasPermission('modify', 'common/filemanager')) {
            $json['error'] = $this->language->get('error_permission');
        }

        if (!isset($json['error'])) {
            if (is_file($old_name)) {
                copy($old_name, $new_name);
            } else {
                $this->recursiveCopy($old_name, $new_name);
            }

            $json['success'] = $this->language->get('text_copy');
        }

        $this->load->library('json');

        $this->response->setOutput(Json::encode($json));
    }

    function recursiveCopy($source, $destination) {
        $directory = opendir($source);

        @mkdir($destination);

        while (false !== ($file = readdir($handle))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($source . '/' . $file)) {
                    $this->recursiveCopy($source . '/' . $file, $destination . '/' . $file);
                } else {
                    copy($source . '/' . $file, $destination . '/' . $file);
                }
            }
        }

        closedir($directory);
    }

    public function folders() {
        $this->response->setOutput($this->recursiveFolders(DIR_IMAGE . 'data/'));
    }

    protected function recursiveFolders($directory) {
        $output = '';

        $output .= '<option value="' . substr($directory, strlen(DIR_IMAGE . 'data/')) . '">' . substr($directory, strlen(DIR_IMAGE . 'data/')) . '</option>';

        $directories = glob(rtrim(str_replace('../', '', $directory), '/') . '/*', GLOB_ONLYDIR);

        foreach ($directories as $directory) {
            $output .= $this->recursiveFolders($directory);
        }

        return $output;
    }

    public function rename() {
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . "GMT");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: application/json");

        $this->load->language('common/filemanager');

        $json = [];

        if ($this->request->hasQuery('path') && $this->request->hasQuery('name')) {
            
            $name = strtolower($this->request->getQuery('name'));
            $name = str_replace(' ', '-', $name);
            $name = str_replace('á', 'a', $name);
            $name = str_replace('é', 'e', $name);
            $name = str_replace('í', 'i', $name);
            $name = str_replace('ó', 'o', $name);
            $name = str_replace('ú', 'u', $name);
            $name = str_replace('ñ', 'n', $name);

            if ($name !== mb_convert_encoding(mb_convert_encoding($name, 'UTF-32', 'UTF-8'), 'UTF-8', 'UTF-32'))
                $name = mb_convert_encoding($name, 'UTF-8', mb_detect_encoding($name));
            $name = htmlentities($name, ENT_NOQUOTES, 'UTF-8');
            $name = preg_replace('`&([a-z]{1,2})(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig);`i', '\1', $name);
            $name = html_entity_decode($name, ENT_NOQUOTES, 'UTF-8');
            $name = preg_replace(array('`[^a-z0-9]`i', '`[-]+`'), '-', $name);
            $name = strtolower(trim($name, '-'));

            if (strlen($name) < 1 || strlen($name) > 255) {
                $json['error'] = $this->language->get('error_filename');
            }
            $old_name = rtrim(DIR_IMAGE . 'data/' . str_replace('../', '', $this->request->getQuery('path')), '/');

            if (!file_exists($old_name) || $old_name == DIR_IMAGE . 'data') {
                $json['error'] = $this->language->get('error_rename');
            }

            if (is_file($old_name)) {
                $ext = strrchr($old_name, '.');
            } else {
                $ext = '';
            }

            $new_name = dirname($old_name) . '/' . str_replace('../', '', $name . $ext);

            if (file_exists($new_name)) {
                $json['error'] = $this->language->get('error_exists');
            }
        }

        if (!$this->user->hasPermission('modify', 'common/filemanager')) {
            $json['error'] = $this->language->get('error_permission');
        }

        if (!isset($json['error'])) {
            rename($old_name, $new_name);

            $json['id'] = $this->request->getQuery('id');
            $json['old'] = $this->request->getQuery('path');
            $json['name'] = $name;
            $json['success'] = $this->language->get('text_rename');
        }

        $this->load->library('json');

        $this->response->setOutput(Json::encode($json));
    }

    public function upload() {
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . "GMT");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: application/json");

        $this->load->language('common/filemanager');

        $json = [];

        if (isset($this->request->post['directory'])) {
            if (isset($this->request->files['files']) && $this->request->files['files']['tmp_name']) {
                if ((strlen(utf8_decode($this->request->files['image']['name'])) < 3) || (strlen(utf8_decode($this->request->files['image']['name'])) > 255)) {
                    $json['error'] = $this->language->get('error_filename');
                }

                $directory = rtrim(DIR_IMAGE . 'data/' . str_replace('../', '', $this->request->post['directory']), '/');

                if (!is_dir($directory)) {
                    $json['error'] = $this->language->get('error_directory');
                }

                if ($this->request->files['image']['size'] > 2000000) {
                    $json['error'] = $this->language->get('error_file_size');
                }

                $allowed = array(
                    'image/jpeg',
                    'image/pjpeg',
                    'image/png',
                    'image/x-png',
                    'image/gif',
                    'application/x-shockwave-flash'
                );

                if (!in_array($this->request->files['image']['type'], $allowed)) {
                    $json['error'] = $this->language->get('error_file_type');
                }

                $allowed = array(
                    '.jpg',
                    '.jpeg',
                    '.gif',
                    '.png',
                    '.flv'
                );

                if (!in_array(strtolower(strrchr($this->request->files['image']['name'], '.')), $allowed)) {
                    $json['error'] = $this->language->get('error_file_type');
                }

                if ($this->request->files['image']['error'] != UPLOAD_ERR_OK) {
                    $json['error'] = 'error_upload_' . $this->request->files['image']['error'];
                }
            } else {
                $json['error'] = $this->language->get('error_file');
            }
        } else {
            $json['error'] = $this->language->get('error_directory');
        }

        if (!$this->user->hasPermission('modify', 'common/filemanager')) {
            $json['error'] = $this->language->get('error_permission');
        }

        if (!isset($json['error'])) {
            if (@move_uploaded_file($this->request->files['image']['tmp_name'], $directory . '/' . basename($this->request->files['image']['name']))) {
                $json['success'] = $this->language->get('text_uploaded');
            } else {
                $json['error'] = $this->language->get('error_uploaded');
            }
        }

        $this->load->library('json');

        $this->response->setOutput(Json::encode($json));
    }

    public function uploader() {
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . "GMT");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: application/json");

        $this->load->language('common/filemanager');

        $json = [];

        $prevPath = 'data/' . str_replace('../', '', $this->request->post['directory']);
        $directory = rtrim(DIR_IMAGE . 'data/' . str_replace('../', '', $this->request->post['directory']), '/');
        if (!is_dir($directory)) {
            $json['error'] = $this->language->get('error_directory');
        }

        $files = $this->request->files['files'];
        if (isset($files) && !$json['error']) {
            $name = $files['name'][0];
            $ext = strtolower(substr($files['name'][0], (strrpos($files['name'][0], '.') + 1)));
            $tmp_name = $files['tmp_name'][0];
            $size = $files['size'][0];
            $type = $files['type'][0];
            $error = $files['error'][0];

            $name = str_replace('.' . $ext, '', $name);
            $name = $name . "-" . $this->config->get('config_name');
            $name = strtolower($name);
            $name = str_replace(' ', '-', $name);
            $name = str_replace('á', 'a', $name);
            $name = str_replace('é', 'e', $name);
            $name = str_replace('í', 'i', $name);
            $name = str_replace('ó', 'o', $name);
            $name = str_replace('ú', 'u', $name);
            $name = str_replace('ñ', 'n', $name);

            if ($name !== mb_convert_encoding(mb_convert_encoding($name, 'UTF-32', 'UTF-8'), 'UTF-8', 'UTF-32'))
                $name = mb_convert_encoding($name, 'UTF-8', mb_detect_encoding($name));
            $name = htmlentities($name, ENT_NOQUOTES, 'UTF-8');
            $name = preg_replace('`&([a-z]{1,2})(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig);`i', '\1', $name);
            $name = html_entity_decode($name, ENT_NOQUOTES, 'UTF-8');
            $name = preg_replace(array('`[^a-z0-9]`i', '`[-]+`'), '-', $name);
            $name = strtolower(trim($name, '-'));

            if ($size > 5000000) {
                $json['error'] = $this->language->get('error_file_size') . __LINE__;
            }

            $mime_types_allowed = array(
                'image/jpg',
                'image/jpeg',
                'image/pjpeg',
                'image/png',
                'image/x-png',
                'image/gif',
                "text/csv",
                "text/comma-separated-values",
                "text/tab-separated-values",
                "text/plain",
                'application/msword',
                'application/pdf',
                'application/x-pdf',
                'application/msexcel',
                'audio/x-mpeg'
            );

            if (!in_array(strtolower($type), $mime_types_allowed)) {
                $return['error'] = 1;
                $return['msg'] = "Archivo no permitido, debe seleccionar un archivo .CSV o .TXT";
            }

            $extension_allowed = array(
                'jpg',
                'jpeg',
                'pjpeg',
                'png',
                'gif',
                "csv",
                "txt",
                'doc',
                'docx',
                'xls',
                'xlsx',
                'pdf',
                'mp3'
            );

            if (!in_array(strtolower($ext), $extension_allowed)) {
                $return['error'] = 1;
                $return['msg'] = "Archivo no permitido, debe seleccionar un archivo .CSV o .TXT";
            }

            if ($size == 0 && !$return['error']) {
                $return['error'] = 1;
                $return['msg'] = "El archivo est&aacute; vac&iacute;o";
            }

            if (($size / 1024 / 1024) > 50 && !$return['error']) {
                $return['error'] = 1;
                $return['msg'] = "El tama&ntilde;o del archivo es muy grande, solo se permiten archivos hasta 50MB";
            }

            if ($error > 0 && !$return['error']) {
                $return['error'] = 1;
                $return['msg'] = $error;
            }

            if ($error == UPLOAD_ERR_INI_SIZE)
                $json['error'] = $this->language->get('UPLOAD_ERR_INI_SIZE') . __LINE__;
            if ($error == UPLOAD_ERR_FORM_SIZE)
                $json['error'] = $this->language->get('UPLOAD_ERR_FORM_SIZE') . __LINE__;
            if ($error == UPLOAD_ERR_PARTIAL)
                $json['error'] = $this->language->get('UPLOAD_ERR_PARTIAL') . __LINE__;
            if ($error == UPLOAD_ERR_NO_FILE)
                $json['error'] = $this->language->get('UPLOAD_ERR_NO_FILE') . __LINE__;
            if ($error == UPLOAD_ERR_NO_TMP_DIR)
                $json['error'] = $this->language->get('UPLOAD_ERR_NO_TMP_DIR') . __LINE__;
            if ($error == UPLOAD_ERR_CANT_WRITE)
                $json['error'] = $this->language->get('UPLOAD_ERR_CANT_WRITE') . __LINE__;
            if ($error == UPLOAD_ERR_EXTENSION)
                $json['error'] = $this->language->get('UPLOAD_ERR_EXTENSION') . __LINE__;

            if (!isset($json['error'])) {
                $filename = basename($name . '.' . $ext);
                if (@move_uploaded_file($tmp_name, $directory . '/' . $filename)) {
                    $json['success'] = $this->language->get('text_uploaded');
                    $json['name'] = $name . '.' . $ext;
                    $json['size'] = $size;
                    $json['type'] = $type;
                    $json['url'] = $this->request->get['directory'] . $name . '.' . $ext;
                    $json['thumbnail_url'] = NTImage::resizeAndSave($prevPath . '/' . $filename, 60, 60);
                    $json['delete_url'] = Url::createAdminUrl("common/filemanager/uploader");
                    $json['delete_type'] = 'DELETE';
                } else {
                    $json['error'] = $this->language->get('error_uploaded');
                }
            }
        } else {
            $json['error'] = $this->language->get('error_file') . __LINE__;
        }
        $this->load->auto('json');
        $this->response->setOutput(Json::encode($json));
    }
}
