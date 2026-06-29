<?php

require_once(DIR_CONTROLLER . "module/modulecontroller.php");

class ControllerModulePostList extends ControllerModuleModuleController
{
    protected string $moduleName = 'post_list';
    protected array $defaults = [];

    public function init()
    {
        $this->addFilter("module:settings", function ($data) {
            $settings = $data['settings'];
            $widget   = $data['widget'];
            $render   = $data['render'];

            $Url = new Url($this->registry);
            $query_data = [];
            $query_data['featured_posts'] = $settings['featured_posts'] ?? null;

            $query_data['limit'] = $this->request->hasQuery('limit') ?
                $this->request->getQuery('limit') : 
                (
                    (
                        (int)$settings['limit'] ? 
                            (int)$settings['limit'] : 
                            (
                                (int)$this->config->get('config_catalog_limit') ? (int)$this->config->get('config_catalog_limit') : 24
                            )
                    )
                );

            $query_data['post_type'] = $this->request->hasQuery('post_type') ?
                $this->request->getQuery('post_type') : 
                (isset($settings['post_type']) ? $settings['post_type'] : 'post');

            $query_data['post_id'] = $this->request->hasPost('post_id') ?
                $this->request->getPost('post_id') :
                ($this->request->hasQuery('post_id') ? $this->request->getQuery('post_id') : null);

            $query_data['page'] = $this->request->hasQuery('page') ? $this->request->getQuery('page') : 1;
            $query_data['start'] = ($query_data['page'] - 1) * $query_data['limit'];
            $query_data['show_featured_image'] = (!empty($settings['show_featured_image'])) ? $settings['show_featured_image'] : null;

            if ($this->request->hasQuery('category_id') || $this->request->hasPost('category_id')) {
                $query_data['category_id'] = $this->request->hasPost('category_id') ? $this->request->getPost('category_id') : $this->request->getQuery('category_id');
                $url = $Url::createUrl("content/category", array('category_id' => $query_data['category_id']));
            } else {
                $query_data['category_id'] = (!empty($settings['categories'])) ? $settings['categories'] : null;
            }

            $query_data['image_popup_width']  = (!empty($settings['image_popup_width'])) ? $settings['image_popup_width'] : $this->config->get('config_image_popup_width');
            $query_data['image_popup_height'] = (!empty($settings['image_popup_height'])) ? $settings['image_popup_height'] : $this->config->get('config_image_popup_height');
            $query_data['image_thumb_width']  = (!empty($settings['image_thumb_width'])) ? $settings['image_thumb_width'] : $this->config->get('config_image_thumb_width');
            $query_data['image_thumb_height'] = (!empty($settings['image_thumb_height'])) ? $settings['image_thumb_height'] : $this->config->get('config_image_thumb_height');

            $this->data['posts'] = [];

            $func = $settings['module'];
            if (!$func || !in_array($func, array('random', 'latest', 'featured', 'recommended', 'related', 'popular'))) $func = 'random';
            $this->prefetch($query_data,$func, $settings);

            if (isset($settings['show_pagination']) && $settings['show_pagination'] && $this->data['total_posts']) {
                if (!is_callable('Pagination')) $this->load->library('pagination');
                $pagination = new Pagination(true);
                $pagination->total = $this->data['total_posts'];
                $pagination->page  = $query_data['page'];
                $pagination->limit = $query_data['limit'];
                $pagination->text  = $this->language->get('text_pagination');
                $pagination->url   = $url . '&page={page}';
                if ($settings['endless_scroll']) {
                    $pagination->ajax = true;
                    $pagination->ajaxTarget = isset($settings['endless_scroll_target']) ? $settings['endless_scroll_target'] : "#{$widget['name']}_results";
                }
                $this->data['pagination'] = $pagination->render();
            }

            return [
                'widget'   => $widget,
                'render'   => $render,
                'settings' => $settings,
            ];
        });
    }

    protected function prefetch($data, $func = 'random') {
        $Url = new Url($this->registry);
        $this->load->model('content/post');
        
        switch ($func) {
            case 'random':
            default:
                $results = $this->modelPost->getRandomPost($data);
                $this->data['total_posts'] = $this->modelPost->getAllTotal($data);
                break;
            case 'latest':
                $results = $this->modelPost->getLatestPost($data);
                $this->data['total_posts'] = $this->modelPost->getAllTotal($data);
                break;
            case 'featured':
                $data['post_id'] = $data['featured_posts'];
                $results = $this->modelPost->getAll($data);
                $this->data['total_posts'] = $this->modelPost->getAllTotal($data);
                break;
            case 'recommended':
                $results = $this->modelPost->getRecommendedPost($data);
                $this->data['total_posts'] = $this->modelPost->getTotalRecommendedPost($data);
                break;
            case 'related':
                $results = $this->modelPost->getPostRelated($this->request->getQuery('post_id'), $data);
                $this->data['total_posts'] = $this->modelPost->getTotalPostRelated($this->request->getQuery('post_id'), $data);
                break;
            case 'popular':
                $results = $this->modelPost->getPopularPost($data);
                $this->data['total_posts'] = $this->modelPost->getTotalPopularPost($data);
                break;
        }
        
        $this->load->auto('store/review');
        
        $this->data['posts'] = [];
        foreach ($results as $k => $result) {
            $image = 'no_image.jpg';
            if (isset($data['show_featured_image']) && !empty($result['pimage'])) {
                $image = $result['pimage'];
            }
            
            if ($this->config->get('config_review')) {
                $rating = $this->modelReview->getAllAvg(array(
                    'object_type'=>'post',
                    'object_id'=>$result['post_id']
                ));
            } else {
                $rating = false;
            }
            
            $this->data['posts'][$k] = array(
                'post_id' => $result['post_id'],
                'name' => $result['title'],
                'overview' => $result['meta_description'],
                'description' => html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'),
                'rating' => $rating,
                'stars' => sprintf($this->language->get('text_stars'), $rating),
                'popup' => NTImage::resizeAndSave($image, $data['image_popup_width'], $data['image_popup_height']),
                'thumb' => NTImage::resizeAndSave($image, $data['image_thumb_width'], $data['image_thumb_height']),
                'href' => $Url::createUrl('content/post', array('post_id' => $result['post_id'])),
                'created' => $result['date_publish_start']
            );
        }
    }
}