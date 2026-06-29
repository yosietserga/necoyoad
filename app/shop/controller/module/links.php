<?php

require_once(DIR_CONTROLLER . "module/modulecontroller.php");

class ControllerModuleLinks extends ControllerModuleModuleController
{
    protected string $moduleName = 'links';
    protected array $defaults = [];

    protected $links_id = 0;
    protected $path = [];

    public function init()
    {
        $this->addFilter("module:settings", function ($data) {
            $settings = $data['settings'];
            $widget   = $data['widget'];
            $render   = $data['render'];
            
            $this->data['links'] = isset($settings['menu_id']) && (int)$settings['menu_id']>0 ? $this->drawLinksGroup($this->getLinks($settings['menu_id'])) : '';

            return [
                'widget'   => $widget,
                'render'   => $render,
                'settings' => $settings,
            ];
        });
    }

    protected function getLinks($menu_id = 0, $parent_id = 0) {
        $this->load->model('content/menu');
        $this->load->model('content/page');

        $return = [];
        $results = $this->modelMenu->getAllItems(array(
            'menu_id'=>$menu_id,
            'parent_id'=>$parent_id
        ));

        if ($results) {
            foreach ($results as $k => $result) {
                $return[$k] = $result;

                $result['class_css'] = $this->modelMenu->getProperty($result['menu_link_id'], 'menu_link', 'class_css');
                $result['submenu_type'] = $this->modelMenu->getProperty($result['menu_link_id'], 'menu_link', 'submenu_type');
                $result['icon'] = $this->modelMenu->getProperty($result['menu_link_id'], 'menu_link', 'icon');

                if ($result['submenu_type'] === 'page_id') {
                    $result['page_id'] = $this->modelMenu->getProperty($result['menu_link_id'], 'menu_link', 'page_id');
                    if (!empty($result['page_id'])) {
                        $pageController = $this->load->controller('content/page');
                        $return[$k]['description'] =  html_entity_decode($pageController->embed($result['page_id']));
                    }
                } elseif ($result['submenu_type'] === 'html_content') {
                    $descriptions = $this->modelMenu->getDescriptions($result['menu_link_id']);
                    var_dump($descriptions);

                    $return[$k]['description'] = html_entity_decode($descriptions[$this->config->get('config_language_id')]['description']??"");
                } else {
                    $return[$k]['children'] = $this->getLinks($menu_id, $result['menu_link_id']);
                }

                if (isset($result['class_css']) && !empty($result['class_css'])) $return[$k]['class_css'] = $result['class_css'];
                if (isset($result['icon']) && !empty($result['icon'])) $return[$k]['icon'] = $result['icon'];

            }
        }

        return $return;
    }

    protected function drawLinksGroup($links, $submenu = false) {
        $output = "<ul". ($submenu ? ' class="submenu"' : "") .">";
        foreach ($links as $k => $result) {
            $output .= '<li'. ((isset($result['class_css']) && !empty($result['class_css'])) ? ' class="'. $result['class_css'] .'"': "") .'>';

            $output .= '<a href="'. Url::rewrite($result['link']) .'"'.
            (isset($result['tag']) ? ' title="'. $result['tag'] .'"' : '') .
            '>' . 
            (isset($result['icon']) ? '<span class="'. $result['icon'] .'"></span>' : '') .
            (isset($result['tag']) ? $result['tag'] : '') . 
            '</a>';

            if (isset($result['description']) && !empty($result['description'])) {
                $output .= '<div class="submenu">';
                $output .= $result['description'];
                $output .= '</div>';
            } elseif (isset($result['children'])) {
                $output .= $this->drawLinksGroup($result['children'], true);
            }

            $output .= '</li>';

        }
        $output .= '</ul>';

        return $output;
    }
}
