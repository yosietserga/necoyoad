<?php
/**
 * ModelMarketingNewsletter
 * 
 * @package NecoTienda
 * @author Inversiones Necoyoad, C.A.
 * @copyright 2010
 * @version $Id$
 * @access public
 */
class ModelMarketingNewsletter extends Model {
    /**
     * ModelMarketingNewsletter::getById()
     * 
     * @return
     */
    public function getById($newsletter_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "newsletter WHERE newsletter_id = '" . (int)$newsletter_id . "' AND status = 1");
		return $query->row;
    }
}
