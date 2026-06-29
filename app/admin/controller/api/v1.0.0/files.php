<?php

$this->load->auto('json');

$return = [];
$request_type = $this->request->server['REQUEST_METHOD'];

switch(strtolower($request_type)) {
    case 'get':
    default:

        $filters = [];
        $items = [];

        //text filters
        $filters['action'] = $this->request->getQuery('action') ? $this->request->getQuery('action') : '';
        $filters['directory'] = $this->request->getQuery('directory') ? $this->request->getQuery('directory') : '';

        $getDirectories = function($directory, $flag=null) {
            if ($flag) {
                return glob(
                    rtrim(DIR_IMAGE . 'data/' . 
                        str_replace('../', '', $directory)
                    , '/') 
                . '/*', $flag);
            } else {
                return glob(
                    rtrim(DIR_IMAGE . 'data/' . 
                        str_replace('../', '', $directory)
                    , '/') 
                . '/*');
            }
        };

        if ($filters['action']=='files') {
            $d = !empty($filters['directory']) ?  '.in.'. $filters['directory'] : '.in.public';
            $cache_prefix = "admin.files{$d}";
            $cachedId = $cache_prefix.
                (int)STORE_ID ."_".
                serialize($filters).
                $this->config->get('config_language_id') . "." .
                $this->request->getQuery('hl') . "." .
                $this->request->getQuery('cc') . "." .
                $this->user->getId() . "." .
                $this->config->get('config_currency') . "." .
                (int)$this->config->get('config_store_id');

            $cached = $this->cache->get($cachedId, $cache_prefix);

            if (!$cached) {
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

                foreach ($getDirectories($filters['directory']) as $file) {
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
                            $thumb = NTImage::resizeAndSave(
                                substr($file, strlen(DIR_IMAGE))
                            , 100, 100);
                        }

                        $items[] = array(
                            'filepath' => substr($file, strlen(DIR_IMAGE . 'data/')),
                            'filename' => basename($file),
                            'size' => round(substr($size, 0, strpos($size, '.') + 4), 2) . $suffix[$i],
                            'thumb' => $thumb
                        );
                    }
                }

                $this->cache->set($cachedId, $items, $cache_prefix);
            } else {
                $items = $cached;
            }
        } else {
            $d = !empty($filters['directory']) ?  '.in.'. $filters['directory'] : '.in.public';
            $cache_prefix = "admin.directories{$d}";
            $cachedId = $cache_prefix.
                (int)STORE_ID ."_".
                serialize($filters).
                $this->config->get('config_language_id') . "." .
                $this->request->getQuery('hl') . "." .
                $this->request->getQuery('cc') . "." .
                $this->user->getId() . "." .
                $this->config->get('config_currency') . "." .
                (int)$this->config->get('config_store_id');

            $cached = $this->cache->get($cachedId, $cache_prefix);

            if (!$cached) {
                $directories = $getDirectories($filters['directory'], GLOB_ONLYDIR);

                if ($directories) {
                    $i = 0;
                    foreach ($directories as $dir) {
                        $items[$i]['directory'] = basename($dir);
                        $items[$i]['path'] = substr($dir, strlen(DIR_IMAGE . 'data/'));
                        $items[$i]['directories'] = $getDirectories($dir);
                        $i++;
                    }
                    //$this->cache->set($cachedId, $items, $cache_prefix);
                }
            } else {
                $items = $cached;
            }
        }

        $return['status'] = array(
            'code'=>200,
            'message'=>'OK'
        );

        $return['error'] = array(
            'code'=>null,
            'message'=>''
        );

        $return['payload'] = array(
            'results'=>$items,
            'filters'=>$filters
        );
    break;

    case 'post':
        $this->request->post = json_decode(file_get_contents('php://input'), true);

        $recursiveCopy = function ($source, $destination) use (&$recursiveCopy) {
            $directory = opendir($source);

            @mkdir($destination);

            while (false !== ($file = readdir($handle))) {
                if (($file != '.') && ($file != '..')) {
                    if (is_dir($source . '/' . $file)) {
                        $recursiveCopy($source . '/' . $file, $destination . '/' . $file);
                    } else {
                        copy($source . '/' . $file, $destination . '/' . $file);
                    }
                }
            }

            closedir($directory);
        };

        $return['error'] = array(
            'code'=>null,
            'message'=>''
        );

        if ($filters['action']=='makedir') {

            $folder = str_replace('../', '', $this->request->getPost('name'));

            if ($folder) {
                $directory = 
                rtrim(DIR_IMAGE . 'data/' . $folder, '/');

                if (!is_dir($directory)) {
                    //TODO: build a error codes map
                    $return['error'] = array(
                        'code'=>1,
                        'message'=>$this->language->get('error_directory')
                    );
                }

                if (file_exists($directory . '/' . $folder)) {
                    //TODO: build a error codes map
                    $return['error'] = array(
                        'code'=>1,
                        'message'=>$this->language->get('error_exists')
                    );
                }
            } else {
                //TODO: build a error codes map
                $return['error'] = array(
                    'code'=>1,
                    'message'=>$this->language->get('error_name')
                );
            }

            if (!$this->user->hasPermission('modify', 'common/filemanager')) {
                //TODO: build a error codes map
                $return['error'] = array(
                    'code'=>1,
                    'message'=>$this->language->get('error_permission')
                );
            }

            if (!$return['error']['code']) {
                mkdir($directory . '/' . $folder, 0777);
                $items['success'] = 1;
            }

        }

        if ($filters['action']=='copy') {
            if (isset($this->request->post['path']) && isset($this->request->post['name'])) {
                if ((strlen(utf8_decode($this->request->post['name'])) < 3) || (strlen(utf8_decode($this->request->post['name'])) > 255)) {
                    //TODO: build a error codes map
                    $return['error'] = array(
                        'code'=>1,
                        'message'=>$this->language->get('error_filename')
                    );
                }

                $old_name = rtrim(DIR_IMAGE . 'data/' . str_replace('../', '', $this->request->post['path']), '/');

                if (!file_exists($old_name) || $old_name == DIR_IMAGE . 'data') {
                    //TODO: build a error codes map
                    $return['error'] = array(
                        'code'=>1,
                        'message'=>$this->language->get('error_copy')
                    );
                }

                if (is_file($old_name)) {
                    $ext = strrchr($old_name, '.');
                } else {
                    $ext = '';
                }

                $new_name = dirname($old_name) . '/' . str_replace('../', '', $this->request->post['name'] . $ext);

                if (file_exists($new_name)) {
                    //TODO: build a error codes map
                    $return['error'] = array(
                        'code'=>1,
                        'message'=>$this->language->get('error_exists')
                    );
                }
            } else {
                //TODO: build a error codes map
                $return['error'] = array(
                    'code'=>1,
                    'message'=>$this->language->get('error_select')
                );
            }

            if (!$this->user->hasPermission('modify', 'common/filemanager')) {
                //TODO: build a error codes map
                $return['error'] = array(
                    'code'=>1,
                    'message'=>$this->language->get('error_permission')
                );
            }

            if (!$return['error']['code']) {
                if (is_file($old_name)) {
                    copy($old_name, $new_name);
                } else {
                    $recursiveCopy($old_name, $new_name);
                }
            }

        }

        if ($filters['action']=='upload') {

        }

        if ($filters['action']=='rename' || $filters['action']=='move') {

            if (isset($this->request->post['from']) && isset($this->request->post['to'])) {
                $from = rtrim(DIR_IMAGE . 'data/' . str_replace('../', '', $this->request->post['from']), '/');

                if (!file_exists($from)) {
                    //TODO: build a error codes map
                    $return['error'] = array(
                        'code'=>1,
                        'message'=>$this->language->get('error_missing')
                    );
                }

                if ($from == DIR_IMAGE . 'data') {
                    //TODO: build a error codes map
                    $return['error'] = array(
                        'code'=>1,
                        'message'=>$this->language->get('error_default')
                    );
                }

                $to = rtrim(DIR_IMAGE . 'data/' . str_replace('../', '', $this->request->post['to']), '/');

                if (!file_exists($to)) {
                    //TODO: build a error codes map
                    $return['error'] = array(
                        'code'=>1,
                        'message'=>$this->language->get('error_move')
                    );
                }

                if (file_exists($to . '/' . basename($from))) {
                    //TODO: build a error codes map
                    $return['error'] = array(
                        'code'=>1,
                        'message'=>$this->language->get('error_exists')
                    );
                }
            } else {
                //TODO: build a error codes map
                $return['error'] = array(
                    'code'=>1,
                    'message'=>$this->language->get('error_directory')
                );
            }

            if (!$this->user->hasPermission('modify', 'common/filemanager')) {
                //TODO: build a error codes map
                $return['error'] = array(
                    'code'=>1,
                    'message'=>$this->language->get('error_permission')
                );
            }

            if (!$return['error']['code']) {
                rename($from, $to . '/' . basename($from));
            }

        }

        $return['status'] = array(
            'code'=>200,
            'message'=>'OK'
        );

        $return['payload'] = $items;
        break;
    case 'delete':
        $this->request->post = json_decode(file_get_contents('php://input'), true);

        $return['error'] = array(
            'code'=>null,
            'message'=>''
        );

        $recursiveDelete = function($directory) use (&$recursiveDelete) {
            if (is_dir($directory)) $handle = opendir($directory);
            if (!$handle) return false;
            
            while (false !== ($file = readdir($handle))) {
                if ($file != '.' && $file != '..') {
                    if (!is_dir($directory . '/' . $file)) {
                        unlink($directory . '/' . $file);
                    } else {
                        $recursiveDelete($directory . '/' . $file);
                    }
                }
            }

            closedir($handle);
            rmdir($directory);
        };

        if (isset($this->request->post['path'])) {
            $path = rtrim(DIR_IMAGE . 'data/' . str_replace('../', '', $this->request->post['path']), '/');

            if (!file_exists($path)) {
                //TODO: build a error codes map
                $return['error'] = array(
                    'code'=>null,
                    'message'=>$this->language->get('error_select')
                );
            }

            if ($path == rtrim(DIR_IMAGE . 'data/', '/')) {
                //TODO: build a error codes map
                $return['error'] = array(
                    'code'=>null,
                    'message'=>$this->language->get('error_delete')
                );
            }
        } elseif (isset($this->request->post['filess'])) {
            foreach ($this->request->post['filess'] as $file) {

                $file = rtrim(DIR_IMAGE . 'data/' . str_replace('../', '', $file), '/');
                if (is_file($file)) {
                    unlink($file);
                } elseif (!file_exists($file)) {
                    //TODO: build a error codes map
                    $return['error'] = array(
                        'code'=>null,
                        'message'=>$this->language->get('error_select')
                    );
                }
            }
        } else {
            //TODO: build a error codes map
            $return['error'] = array(
                'code'=>null,
                'message'=>$this->language->get('error_select')
            );
        }

        if (!$this->user->hasPermission('modify', 'common/filemanager')) {
            //TODO: build a error codes map
            $return['error'] = array(
                'code'=>null,
                'message'=>$this->language->get('error_permission')
            );
        }

        if (!$return['error']['code']) {
            if (is_file($file)) {
                unlink($file);
            } elseif (is_dir($path)) {
                $recursiveDelete($path);
            }
        }

        break;
}