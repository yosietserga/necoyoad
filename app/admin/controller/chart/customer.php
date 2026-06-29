<?php   
class ControllerChartCustomer extends Controller {   
	public function index() {
        
        $query = $this->db->query("SELECT MONTH(date_added) AS month, COUNT(*) AS total 
            FROM `" . DB_PREFIX . "customer` 
            WHERE status = 1
            AND approved = 1
            AND YEAR(date_added) = '" . date('Y') . "'
            GROUP BY MONTH(date_added)
            ORDER BY MONTH(date_added) ASC");
            
        $this->data['customers'] = [];
        for ($i = 0; $i <= 11; $i++) {
            if (isset($query->rows[$i]['month'])) {
                $this->data['customers'][(int)$query->rows[$i]['month']] = (int)$query->rows[$i]['total'];
            } elseif (!isset($this->data['customers'][$i + 1])) {
                $this->data['customers'][$i + 1] = 0;
            }
        }

        $template = ($this->config->get('default_admin_view_chart_customers')) ? $this->config->get('default_admin_view_chart_customers') : 'chart/customer_new_line.tpl';
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_admin_template') . '/'. $template)) {
            $this->template = $this->config->get('config_admin_template') . '/' . $template;
        } else {
            $this->template = 'default/' . $template;
        }

        $this->response->setOutput($this->render(true), $this->config->get('config_compression'));
  	}
}