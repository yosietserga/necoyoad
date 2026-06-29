<?php

require_once(realpath(dirname(__FILE__) . '/../widgetcontroller.php'));

class ControllerModuleProductTabsWidget extends ControllerWidgetController
{
    protected string $moduleName = 'product_tabs';

    public function init()
    {
        $this->addFilter("widget:settings", function ($widget) {
            $this->load->model('store/category');
            $this->load->model('store/manufacturer');
            
            $c = $this->modelCategory->getAll(array('parent_id' => 0, 'language_id' => $this->config->get('config_language_id')));
            $ma = $this->modelManufacturer->getAll();

            if (isset($this->data['settings']['tabs'])) {
                foreach ($this->data['settings']['tabs'] as $k => $tab) {
                    $t = [];
                    $t['categories'] = $this->getCategories($k, $c, true, $widget['name'], $tab['categories']??[]);
                    $t['manufacturers'] = $this->getManufacturers($k, $ma, $widget['name'], $tab['manufacturers']??[]);
                    $t['module'] = $tab['module'];
                    $t['view'] = $tab['view'];
                    $this->data['settings']['tabs'][$k] = $t;
                }
            }

            return $widget;
        });
    }

    private function getCategories($index, $categories, $parent = false, $name, array $settingsCategories = [])
    {
        $output = '';
        if ($categories) {
            foreach ($categories as $result) {
                if ($parent === true)
                    $output .= '<li>';
                else
                    $output .= '<li>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                $output .= '<input id="' . $name . 'Settingscategories' . $result['category_id'] . '" type="checkbox" name="Widgets[' . $name . '][settings][tabs][' . $index . '][categories][]" value="' . $result['category_id'] . '"';
                $output .= (in_array($result['category_id'], $settingsCategories) || empty($settingsCategories)) ? ' checked="checked"' : '';
                $output .= '">';
                $output .= '<label for="' . $name . 'Settingscategories' . $result['category_id'] . '">' . $result['title'] . '</label>';

                // subcategories
                $children = $this->modelCategory->getAll(array(
                    'parent_id' => $result['category_id'],
                    'language_id' => $this->config->get('config_language_id')
                ));
                if ($children) {
                    $output .= $this->getCategories($index, $children, false, $name, $settingsCategories);
                }
                $output .= '</li>';
            }
        }
        return $output;
    }

    private function getManufacturers($index, $manufacturers, $name, array $settingsManufacturers = [])
    {
        $output = '';
        if ($manufacturers) {
            foreach ($manufacturers as $result) {

                $output .= '<li>';
                $output .= '<input id="' . $name . 'Settingsmanufacturers' . $result['manufacturer_id'] . '" type="checkbox" name="Widgets[' . $name . '][settings][tabs][' . $index . '][manufacturers][]" value="' . $result['manufacturer_id'] . '"';
                $output .= (in_array($result['manufacturer_id'], $settingsManufacturers) || empty($settingsCategories)) ? ' checked="checked"' : '';
                $output .= '">';
                $output .= '<label for="' . $name . 'Settingsmanufacturers' . $result['manufacturer_id'] . '">' . $result['name'] . '</label>';

                $output .= '</li>';
            }
        }
        return $output;
    }
}