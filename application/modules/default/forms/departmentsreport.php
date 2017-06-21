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

class Default_Form_departmentsreport extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');		
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'departmentsreport');      
		$this->setAttrib('action', BASE_URL.'reports/departments');  
		
		$deptname = new Zend_Form_Element_Text('deptname');
		$deptname->setLabel('Department');
		
		$deptname->setAttrib('onblur', 'clearautocompletenames(this)');	
		
		$dcode = new Zend_Form_Element_Text('dcode');
		$dcode->setLabel('Code');		
        $dcode->setAttrib('class', 'selectoption');      
		$dcode->addValidator("regex",true,array(                           
                                   'pattern'=>'/^([a-zA-Z0-9.\-]+ ?)+$/',
                                   'messages'=>array(
                                       'regexNotMatch'=>'Please enter only alpha numeric characters.'
                                   )
                        ));
		$dcode->setAttrib('onblur', 'clearautocompletenames(this)');	
		
		$bname = new Zend_Form_Element_Text('bname');
		$bname->setLabel('Business Unit');
		$bname->addValidator("regex",true,array(                           
                                   'pattern'=>'/^([a-zA-Z.\-]+ ?)+$/',
                                   'messages'=>array(
                                       'regexNotMatch'=>'Please enter only alphabets.'
                                   )
                        ));
		$bname->setAttrib('onblur', 'clearautocompletenames(this)');	
       				
		$startdate = new ZendX_JQuery_Form_Element_DatePicker('startdate');
		$startdate->setLabel('Started On');
		$startdate->setAttrib('readonly', 'true');	        
		$startdate->setOptions(array('class' => 'brdr_none'));	
		
		$country = new Zend_Form_Element_Select('country');
        $country->setLabel('Country');			
		   		       

		$this->addElements(array($deptname,$dcode,$bname,$startdate,$country));
        $this->setElementDecorators(array('ViewHelper')); 
		$this->setElementDecorators(array(
                    'UiWidgetElement',
        ),array('startdate'));		 
	}
}

