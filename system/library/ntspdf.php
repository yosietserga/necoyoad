<?php
/**
 * Backup
 *
 * @package NecoTienda Standalone
 * @author Yosiet Serga
 * @copyright NecoTienda
 * @version 2012
 * @access public
 */

if (file_exists((dirname(__FILE__) .'/tcpdf/tcpdf.php'))) {
    require_once(dirname(__FILE__) . '/tcpdf/tcpdf.php');
    require_once(dirname(__FILE__) . '/tcpdf/config/lang/spa.php');
} else {
    exit('TCPDF Class is neccesary to use with NTSPDF Class');
}

class ntsPDF extends TCPDF {

    protected $data = [];

    public function init() {
        $this->setPrintFooter(false);
        $this->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $this->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        $this->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $this->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $this->SetHeaderMargin(PDF_MARGIN_HEADER);
        $this->SetFooterMargin(PDF_MARGIN_FOOTER);
        $this->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $this->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $this->SetFont('dejavusans', '', 12);
    }

    public function Footer() {
        if (!$this->footer_data_isset) $this->SetFooterData(null);
        
        if ($this->image && file_exists($this->image)) {
            $this->Image($this->image, 11, 241, 189, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        }
        
        $this->SetY($this->margin_bottom);
        $this->SetFont($this->font_family, $this->font_weight, $this->font_size);

        $this->Cell(0, 5, 'Powered By NecoTienda '. date("m/d/Y H\hi:s"), 0, false, 'C', 0, '', 0, false, 'T', 'M');
        
        if ($this->show_page_number) {
            $this->Cell(0, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
        }
    }

    public function SetFooterData($params) {
        $this->footer_data_isset = true;

        if (isset($params['margin_bottom'])) {
            $this->margin_bottom = $params['margin_bottom'];
        } else {
            $this->margin_bottom = -15;
        }

        if (isset($params['font_family'])) {
            $this->font_family = $params['font_family'];
        } else {
            $this->font_family = 'helvetica';
        }
        
        if (isset($params['font_weight'])) {
            $this->font_weight = (strtolower($this->font_weight) == 'bold') ? 'B' : $params['font_weight'];
        } else {
            $this->font_weight = 'N';
        }
        
        if (isset($params['font_size'])) {
            $this->font_size = $params['font_size'];
        } else {
            $this->font_size = 6;
        }

        if (isset($params['show_page_number'])) {
            $this->show_page_number = $params['show_page_number'];
        }
        
        if (isset($params['image'])) {
            $this->image = $params['image'];
        }

    }

    public function __get($k) {
        return $this->data[$k];
    }

    public function __set($k, $v) {
        return $this->data[$k] = $v;
    }

    public function __isset($k) {
        return isset($this->data[$k]);
    }
}