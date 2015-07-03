<?php
/********************************************************************************* 
 *  This file is part of Sentrifugo.
 *  Copyright (C) 2014 Sapplica
 *   
 *  Sentrifugo is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  Sentrifugo is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with Sentrifugo.  If not, see <http://www.gnu.org/licenses/>.
 *
 *  Sentrifugo Support <support@sentrifugo.com>
 ********************************************************************************/

class Default_Form_empscreeningreport extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');		
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'empscreeningreport');      
		$this->setAttrib('action', BASE_URL.'reports/empscreening');  
		
		$specimen = new Zend_Form_Element_Select('specimen');
        $specimen->setLabel('Select Employee/Candidate');					    
	    $specimen->addMultiOption('','Select Employee/Candidate');
		$specimen->addMultiOption('1','Employee');
		$specimen->addMultiOption('2','Candidate');
		
	    $empname = new Zend_Form_Element_Text('empname');
		$empname->setLabel('Employee / Candidate Name');
		$empname->addValidator("regex",true,array(                           
                                   'pattern'=>'/^([a-zA-Z.\-]+ ?)+$/',
                                   'messages'=>array(
                                       'regexNotMatch'=>'Please enter only alphabets.'
                                   )
                        ));
		$empname->setAttrib('onblur', 'clearEmpScreeningAutoCompleteNames(this)');	
       
        		
		$agencyname = new Zend_Form_Element_Text('agencyname');
		$agencyname->setLabel('Agency Name');		
        $agencyname->setAttrib('class', 'selectoption');      
		$agencyname->addValidator("regex",true,array(                           
                                   'pattern'=>'/^([a-zA-Z.\-]+ ?)+$/',
                                   'messages'=>array(
                                       'regexNotMatch'=>'Please enter only alphabets.'
                                   )
                        ));
		$agencyname->setAttrib('onblur', 'clearEmpScreeningAutoCompleteNames(this)');	
		
		$screeningtype = new Zend_Form_Element_Multiselect('screeningtype');
		$screeningtype->setLabel('Screening Type');
		
		$checktypeModal = new Default_Model_Bgscreeningtype();
	    	$typesData = $checktypeModal->fetchAll('isactive=1','type');
			foreach ($typesData->toArray() as $data){
		$screeningtype->addMultiOption($data['id'],$data['type']);
	    	}
		$screeningtype->setRegisterInArrayValidator(false);	
		
		
		$process_status = new Zend_Form_Element_Select('process_status');
        $process_status->setLabel('Select Status');					    
	    $process_status->addMultiOption('','Select Status');
		$process_status->addMultiOption('In process','In process');
		$process_status->addMultiOption('Complete','Complete');
		$process_status->addMultiOption('On hold','On hold');
		
		$month = new Zend_Form_Element_Select('month');
        $month->setLabel('Select Month');					    
	    $month->addMultiOption('','Select Month');
		$monthnamesarray = array(
								'1'=>'January','2'=>'February','3'=>'March','4'=>'April','5'=>'May','6'=>'June',
								'7'=>'July','8'=>'August','9'=>'September','10'=>'October','11'=>'November',
								'12'=>'December');
		for($i=1;$i<=sizeof($monthnamesarray);$i++)
		{
			$month->addMultiOption($i,$monthnamesarray[$i]);
		}
		
		$year = new Zend_Form_Element_Select('year');
        $year->setLabel('Select Year');			
		$curYear = date("Y");
		$preYear = $curYear-10; 	   
		$year->addMultiOption('','Select Year');				
		for($i = $preYear;$i<= $curYear;$i++)
		{
			$year->addMultiOption($i,$i);
		}
		
		
		$this->addElements(array($specimen,$empname,$agencyname,$screeningtype,$process_status,$month,$year));
        $this->setElementDecorators(array('ViewHelper')); 
	}
}

