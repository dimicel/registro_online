<?php

$dat_centro = $mysqli->query("SELECT * FROM config_centro");
$datos_cen= $dat_centro->fetch_assoc();
$dat_centro->free();

class MYPDF extends TCPDF {
	private $datos_cen;
    private $titulo;

	public function __construct($datos_cen, $titulo_PDF, $orientation = PDF_PAGE_ORIENTATION, $unit = PDF_UNIT, $format = PDF_PAGE_FORMAT, $unicode = true, $encoding = 'UTF-8', $diskcache = false, $pdfa = false) {
		parent::__construct($orientation, $unit, $format, $unicode, $encoding, $diskcache, $pdfa);
		$this->datos_cen = $datos_cen;
		$this->titulo = $titulo_PDF;
	}
 

	//Page header
	public function Header() {
		// Logo
		$image_file = '../recursos/logo_ccm.jpg';
		$this->Image($image_file, 10, 10, 25, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
		$image_file = '../recursos/mini_escudo.jpg';
		$this->Image($image_file, 140, 10, 20, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);

		$this->SetFont('helvetica', 'B', 12);
		$this->SetXY(0,0);
		$this->writeHTMLCell(105, 0, 35, 10, '<div style="text-align:center; font-weight:bold;">' . $this->titulo . '</div>', 0, 1, 0, true, 'C', true);
			
		$this->SetFont('helvetica', '', 8);

		$encab = "<label><strong>".$this->datos_cen["centro"]."</strong><br>".$this->datos_cen["direccion_centro"]."<br>".$this->datos_cen["cp_centro"]."-".$this->datos_cen["localidad_centro"]."<br>Tlf.:".$this->datos_cen["tlf_centro"]."<br>Fax:".$this->datos_cen["fax_centro"]."</label>";
		$this->writeHTMLCell(0, 0, 160, 11, $encab, 0, 1, 0, true, 'C', true);
		
	}
}