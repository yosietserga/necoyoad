<?php   
class ControllerChartProduct extends Controller {   
	public function index() {
        
        $query = $this->db->query("SELECT MONTH(date_added) AS month, COUNT(*) AS total 
            FROM `" . DB_PREFIX . "product_stats` 
            WHERE YEAR(date_added) = '" . date('Y') . "'
            GROUP BY MONTH(date_added)
            ORDER BY MONTH(date_added) ASC");
            
        $this->data['visits'] = [];
        for ($i = 0; $i <= 11; $i++) {
            if (isset($query->rows[$i]['month'])) {
                $this->data['visits'][(int)$query->rows[$i]['month']] = (int)$query->rows[$i]['total'];
            } elseif (!isset($this->data['visits'][$i])) {
                $this->data['visits'][$i] = 0;
            }
        }

        $template = ($this->config->get('default_admin_view_chart_products')) ? $this->config->get('default_admin_view_chart_products') : 'chart/product_visits_line.tpl';
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_admin_template') . '/'. $template)) {
            $this->template = $this->config->get('config_admin_template') . '/' . $template;
        } else {
            $this->template = 'default/' . $template;
        }

        $this->response->setOutput($this->render(true), $this->config->get('config_compression'));
  	}
}