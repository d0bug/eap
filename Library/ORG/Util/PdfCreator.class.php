<?php

class PdfCreator{
	private $pdf = null;
	
	public function __construct($paperType = 'A4') {
		if(false == class_exists('tcpdf', false)) {
			$tcpdf = VENDOR_PATH . '/tcpdf';
			require_once($tcpdf . '/config/lang/chi.php');
			require_once($tcpdf . '/tcpdf.php');
		}
		$this->pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, $paperType, true, 'UTF-8', false);
		$this->pdf->SetMargins(PDF_MARGIN_LEFT, 10, PDF_MARGIN_RIGHT);
        $this->pdf->SetHeaderMargin(14);
        $this->pdf->SetFooterMargin(14);
        $this->pdf->setLanguageArray($l);
        $this->pdf->SetFont('stsongstdlight', '', 9);
        $this->pdf->SetAutoPageBreak(TRUE, 10);
        $this->pdf->SetPrintHeader(null);
        $this->pdf->SetPrintFooter(null);
	}
	
	public function addPage($html) {
		$this->pdf->addPage();
		$this->pdf->writeHTML($html, true, false, true, false, '');
	}
	
	public function savePdf($pdfFile) {
		$this->pdf->Output($pdfFile, 'F');
	}
}
?>