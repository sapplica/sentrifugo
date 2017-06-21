<?php
class Zend_Controller_Action_Helper_PdfHelper extends Zend_Controller_Action_Helper_Abstract{

	public function generateReport($field_names, $field_data, $field_widths, $data=array()){
		try{
			$main_heading = '';
			//require('mc_table.php');
			defined('FPDF_FONTPATH') || define('FPDF_FONTPATH', 'FPDF/font');

			if((isset($data['grid_no']) && $data['grid_no']==1) || (isset($data['grid_count']) && $data['grid_count']==1)){
				$pdf=new PDF_MC_Table();
				$pdf->AddPage();
					
				// Headings
				$pdf->SetFont('Arial','',21);

				if(!empty($data['project_name'])){
					$main_heading .= $data['project_name'];
					$pdf->Row(array($main_heading), $data);
				}

			}elseif(isset($data['pdf_object'])){
				$pdf = $data['pdf_object'];
				$pdf->Ln(10);
			}

			$pdf->Ln(4);

			// Sub heading
			$pdf->SetFont('Arial','',15);
			$pdf->SetTextColor(20);
			$pdf->Cell(60, 10, $data['object_name'], 0, 0);
			$pdf->Cell(88);
			$pdf->SetFont('Arial','',9);
			$pdf->Cell(32, 10, date("F jS, Y", time()), 0, 1);  // To show report generated date
			$pdf->Ln(5);
			if(isset($data['count_emp_reporting'])){
				$pdf->Cell(60, 10, "My Team Count : ".$data['count_emp_reporting'], 0, 1);
				$pdf->Ln(5);
			}

			$pdf->SetFont('Arial','',9);
			//Table with 20 rows and 4 columns
			$pdf->SetWidths($field_widths);
			srand(microtime()*1000000);

			// Header
			// Colors, line width and bold font
			$pdf->SetFillColor(249,249,249);
			$pdf->SetTextColor(20);
			$pdf->SetDrawColor(198,200,201);
			$pdf->SetLineWidth(.3);
			$pdf->SetFont('','B');

			foreach($field_names as $field_name){
				$pdf->field_names[] = strtoupper($field_name['field_label']);
			}

			if(isset($data['field_name_align'])){
				$pdf->SetAligns($data['field_name_align']);
				$pdf->Row($pdf->field_names);
				$pdf->field_name_align = $data['field_name_align'];
			}else{
				$pdf->Row($pdf->field_names);
			}
			
			//Data
			// Color and font restoration
			$pdf->SetFillColor(249,249,249);
			$pdf->SetTextColor(68);
			$pdf->SetFont('');

			if($field_data){
				foreach($field_data as $rec){
					$row_values = array();
					foreach($field_names as $field_name){
						if(isset($field_name['field_name'])){
							if(isset($data['field_value_align'])){
								$pdf->SetAligns($data['field_value_align']);
								$pdf->field_value_align = $data['field_value_align'];								
							}else{
								//$pdf->SetAligns(array());
							}
							if(is_object($rec[$field_name['field_name']])){
								$rec[$field_name['field_name']] = $rec[$field_name['field_name']]->format('m-d-Y');
							}
							$row_values[] = $rec[$field_name['field_name']] == ''?"--":$rec[$field_name['field_name']];
						}
					}
						
					$pdf->Row($row_values);

				}
			}

			if((isset($data['grid_no']) && $data['grid_no']=='last') || (isset($data['grid_count']) && $data['grid_count']==1)){
				// AJAX download
				$file = BASE_PATH.'/downloads/reports/'.$data['file_name'];
				$pdf->Output($file,'F');
				
				// Normal download
                /*$pdf->Output($data['file_name'],'D');
				exit();*/
			}else{
				return $pdf;
			}

		}catch(Exception $e){
			exit($e->getMessage());
		}

	}

	//function to generate file name for pdf
	public function generateFileName(array $variables = null)
	{
		$fileName = '';
		if(!empty($variables))
		{
			foreach($variables as $var)
			{
				$fileName .= $var.'_';
			}
			//replacing empty space with _
			$fileName = preg_replace('/\s+/', '_', $fileName);
			//trimming extra _
			$fileName = rtrim($fileName,'_');
		}
		return $fileName;
	}

}


// Include library scripts below

require_once('FPDF/fpdf.php');

class PDF_MC_Table extends FPDF
{
	var $widths;
	var $aligns;

	// Custom code
	var $fill='D';
	public $field_names;
	public $field_name_align = array();
	public $field_value_align = array();
	
	function Header()
	{
		if(isset($this->field_names)){
			// Reset values for table header
			$this->SetFillColor(249,249,249);
			$this->SetTextColor(20);
			$this->SetDrawColor(198,200,201);
			$this->SetLineWidth(.3);
			$this->SetFont('','B');		
			$this->fill='D';
			$this->SetAligns($this->field_name_align);
			
			$this->Row($this->field_names);
			
			// Reset alignment for table body
			$this->SetAligns($this->field_value_align);
		}
	}

	function SetWidths($w)
	{
		//Set the array of column widths
		$this->widths=$w;
	}

	function SetAligns($a)
	{
		//Set the array of column alignments
		$this->aligns=$a;
	}

	//function Row($data) - Original definition
	function Row($data, $style=array())
	{
		//Calculate the height of the row
		$nb=0;
		for($i=0;$i<count($data);$i++){
			$nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
		}
		$h=7*$nb; // Changed value from 5 to 7
		//Issue a page break first if needed
		$this->CheckPageBreak($h);
		//Draw the cells of the row
		for($i=0;$i<count($data);$i++)
		{
			$w=$this->widths[$i];

			$a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';

			//Save the current position
			$x=$this->GetX();
			$y=$this->GetY();
			//Draw the border

			$this->Rect($x,$y,$w,$h,$this->fill);
			//Print the text
			$this->MultiCell($w,7,$data[$i],0,$a);	// Height was changed in MultiCell(second parameter), from the value 5 to 7

			//Put the position to the right of the cell
			$this->SetXY($x+$w,$y);
		}

		// Custom code
		$this->fill = ($this->fill=='DF')?'D':'DF';

		//Go to the next line
		$this->Ln($h);
	}

	function CheckPageBreak($h)
	{
		//If the height h would cause an overflow, add a new page immediately
		if($this->GetY()+$h>$this->PageBreakTrigger)
		$this->AddPage($this->CurOrientation);
	}

	function NbLines($w,$txt)
	{
		//Computes the number of lines a MultiCell of width w will take
		$cw=&$this->CurrentFont['cw'];
		if($w==0)
		$w=$this->w-$this->rMargin-$this->x;
		$wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
		$s=str_replace("\r",'',$txt);
		$nb=strlen($s);
		if($nb>0 and $s[$nb-1]=="\n")
		$nb--;
		$sep=-1;
		$i=0;
		$j=0;
		$l=0;
		$nl=1;
		while($i<$nb)
		{
			$c=$s[$i];
			if($c=="\n")
			{
				$i++;
				$sep=-1;
				$j=$i;
				$l=0;
				$nl++;
				continue;
			}
			if($c==' ')
			$sep=$i;
			$l+=$cw[$c];
			if($l>$wmax)
			{
				if($sep==-1)
				{
					if($i==$j)
					$i++;
				}
				else
				$i=$sep+1;
				$sep=-1;
				$j=$i;
				$l=0;
				$nl++;
			}
			else
			$i++;
		}
		return $nl;
	}
}
