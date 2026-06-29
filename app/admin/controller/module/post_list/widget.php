<?php

require_once(realpath(dirname(__FILE__) . '/../widgetcontroller.php'));

class ControllerModulePostListWidget extends ControllerWidgetController
{
    protected string $moduleName = 'post_list';
    
    public function init() {
        $this->addFilter("widget:settings", function ($widget) {
            $this->load->model('content/post_category');

            $this->data['categories'] = $this->getCategories(
                $this->modelPost_category->getAll(array('parent_id' => 0, 'language_id' => $this->config->get('config_language_id'))), 
                true,
                $widget['name'], 
                null
            );

            return $widget;
        });
    }

    private function getCategories($categories, $parent = false, $name, $settingsCategories) {
        $output = '';
        if ($categories) {
            foreach ($categories as $result) {
                if ($parent === true)
                    $output .= '<li>';
                else
                    $output .= '<li>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                $output .= '<input id="'. $name .'Settingscategories' . $result['category_id'] . '" type="checkbox" name="Widgets['. $name .'][settings][categories][]" value="' . $result['category_id'] .'"';
                $output .= (in_array($result['category_id'], $settingsCategories) || empty($settingsCategories)) ? ' checked="checked"' : '';
                $output .= '">';
                $output .= '<label for="'. $name .'Settingscategories' . $result['category_id'] . '">' . $result['title'] . '</label>';

                // subcategories
                $children = $this->modelPost_category->getAll(array(
                    'parent_id' => $result['category_id'],
                    'language_id' => $this->config->get('config_language_id')
                ));
                if ($children) {
                    $output .= $this->getCategories($children, false, $name, $settingsCategories);
                }
                $output .= '</li>';
            }
        }
        return $output;
    }
}