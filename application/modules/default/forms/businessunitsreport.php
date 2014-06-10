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

class Default_Form_businessunitsreport extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');		
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'businessunitsreport');      
		$this->setAttrib('action', DOMAIN.'reports/businessunits');  
		
		$bunitname = new Zend_Form_Element_Text('bunitname');
		$bunitname->setLabel('Business Unit');
		$bunitname->setAttrib('onblur', 'clearbuname(this)');	
		/*$bunitname->addValidator("regex",true,array(                           
                                   'pattern'=>'/^([a-zA-Z.\-]+ ?)+$/',
                                   'messages'=>array(
                                       'regexNotMatch'=>'Please enter only alphabets.'
                                   )
                        ));*/
       
        		
		$bunitcode = new Zend_Form_Element_Text('bunitcode');
		$bunitcode->setLabel('Code');		
		$bunitcode->setAttrib('onblur', 'clearbuname(this)');	
        $bunitcode->setAttrib('class', 'selectoption');      
		/*$bunitcode->addValidator("regex",true,array(                           
                                   'pattern'=>'/^([a-zA-Z.\-]+ ?)+$/',
                                   'messages'=>array(
                                       'regexNotMatch'=>'Please enter only alphabets.'
                                   )
                        ));*/
						
		$startdate = new ZendX_JQuery_Form_Element_DatePicker('startdate');
		$startdate->setLabel('Started On');
		$startdate->setAttrib('readonly', 'true');	        
		$startdate->setOptions(array('class' => 'brdr_none'));	
		
		$start_date = new ZendX_JQuery_Form_Element_DatePicker('start_date');
		//$start_date->addValidator(new Zend_Validate_Date(array("format" => "MM-dd-yyyy")));	
		$start_date->setAttrib('readonly', 'true');	
        $start_date->setAttrib('onfocus', 'this.blur()'); 		
		$start_date->setOptions(array('class' => 'brdr_none'));	
		
		$country = new Zend_Form_Element_Select('country');
        $country->setLabel('Country');			
		    /*$countryModal = new Default_Model_Countries();
	    	$countriesData = $countryModal->fetchAll('isactive=1','country');
	    $country->addMultiOption('','All');
	    	foreach ($countriesData->toArray() as $data){
		$country->addMultiOption($data['country_id_org'],$data['country']);
	    	}*/		       

		$this->addElements(array($bunitname,$bunitcode,$startdate,$country));
        $this->setElementDecorators(array('ViewHelper')); 
		$this->setElementDecorators(array(
                    'UiWidgetElement',
        ),array('startdate'));		 
	}
}

