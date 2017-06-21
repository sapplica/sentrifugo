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

class Default_Form_businessunits extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('action',BASE_URL.'businessunits/edit');
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'businessunits');
		
		$id = new Zend_Form_Element_Hidden('id');
		
		$unitname = new Zend_Form_Element_Text('unitname');
        $unitname->setAttrib('maxLength', 50);
        $unitname->addFilter(new Zend_Filter_StringTrim());
        $unitname->setRequired(true);
        $unitname->addValidator('NotEmpty', false, array('messages' => 'Please enter name.'));  
		$unitname->addValidator("regex",true,array(                           
                           'pattern'=>'/^(?![0-9]{4})[a-zA-Z0-9.\- ?]+$/',
                           'messages'=>array(
                               'regexNotMatch'=>'Please enter valid name.'
                           )
        	));
		$unitname->addValidator(new Zend_Validate_Db_NoRecordExists(
	                                            array(  'table'=>'main_businessunits',
	                                                     'field'=>'unitname',
	                                                     'exclude'=>'id!="'.Zend_Controller_Front::getInstance()->getRequest()->getParam('id').'" and isactive=1',    
	
	                                                      ) ) );
		$unitname->getValidator('Db_NoRecordExists')->setMessage('Name already exists.');	
		 
		$unitcode = new Zend_Form_Element_Text('unitcode');
        $unitcode->addFilter(new Zend_Filter_StringTrim());
		$unitcode->setRequired(true);
		$unitcode->setAttrib("maxlength",4);
        $unitcode->addValidator('NotEmpty', false, array('messages' => 'Please enter code.')); 
		
		$unitcode->addValidators(array(array('StringLength',false,
									  array('min' => 2,
											'max' => 4,
											'messages' => array(
											Zend_Validate_StringLength::TOO_LONG =>
											'Code must contain at most %max% characters.',
											Zend_Validate_StringLength::TOO_SHORT =>
											'Code must contain at least %min% characters.',
											)))));
		$unitcode->addValidators(array(
			         array(
			             'validator'   => 'Regex',
			             'breakChainOnFailure' => true,
			             'options'     => array( 
			             'pattern' => '/^[a-zA-Z0-9\&\'\.\s]+$/',
			                 'messages' => array(
			                     Zend_Validate_Regex::NOT_MATCH =>'Please enter valid code.'
			                 )
			             )
			         )
			     ));
		$unitcode->addValidator(new Zend_Validate_Db_NoRecordExists(
	                                            array(  'table'=>'main_businessunits',
	                                                     'field'=>'unitcode',
	                                                     'exclude'=>'id!="'.Zend_Controller_Front::getInstance()->getRequest()->getParam('id').'" and isactive=1',    
	
	                                                      ) ) );
		 $unitcode->getValidator('Db_NoRecordExists')->setMessage('Code already exists.');	
		 
		$description = new Zend_Form_Element_Textarea('description');
        $description->setAttrib('rows', 10);
        $description->setAttrib('cols', 50);
		
		$start_date = new ZendX_JQuery_Form_Element_DatePicker('start_date');
		
		$start_date->setAttrib('readonly', 'true');	
        $start_date->setAttrib('onfocus', 'this.blur()'); 		
		$start_date->setOptions(array('class' => 'brdr_none'));	
		
        
		
		$country = new Zend_Form_Element_Select('country');
        $country->setLabel('country');	
		$country->setRequired(true);
		$country->addValidator('NotEmpty', false, array('messages' => 'Please select country.')); 
		
		$country->setAttrib('onchange', 'displayParticularState_normal(this,"state","state","city")');
		    $countryModal = new Default_Model_Countries();
	    	$countriesData = $countryModal->fetchAll('isactive=1','country');
	    $country->addMultiOption('','Select country');
	    	foreach ($countriesData->toArray() as $data){
		$country->addMultiOption($data['country_id_org'],$data['country']);
	    	}
		$country->setRegisterInArrayValidator(false);	
		
		$state = new Zend_Form_Element_Select('state');
        
        $state->setAttrib('onchange', 'displayParticularCity_normal(this,"city","city","")');
        $state->setRegisterInArrayValidator(false);
		$state->addMultiOption('','Select State');
        $state->setRequired(true);
		$state->addValidator('NotEmpty', false, array('messages' => 'Please select state.')); 
		
		$city = new Zend_Form_Element_Select('city');
        $city->setAttrib('class', 'selectoption');
		$city->setAttrib('onchange', 'displayCityCode(this)');
        $city->setRegisterInArrayValidator(false);
        $city->addMultiOption('','Select City');
		$city->setRequired(true);
		$city->addValidator('NotEmpty', false, array('messages' => 'Please select city.'));
		
		$address1 = new Zend_Form_Element_Textarea('address1');
        $address1->setAttrib('rows', 10);
        $address1->setAttrib('cols', 50);
		
		$address1->setRequired(true);
        $address1->addValidator('NotEmpty', false, array('messages' => 'Please enter street address.'));  		
		
		$address2 = new Zend_Form_Element_Textarea('address2');
        $address2->setAttrib('rows', 10);
        $address2->setAttrib('cols', 50);
		
		
		$address3 = new Zend_Form_Element_Textarea('address3');
        $address3->setAttrib('rows', 10);
        $address3->setAttrib('cols', 50);
		
		
		$timezone = new Zend_Form_Element_Select('timezone');
        $timezone->setLabel('timezone');	
		$timezone->setRequired(true);
		$timezone->addValidator('NotEmpty', false, array('messages' => 'Please select time zone.')); 		
		    $timezoneModel = new Default_Model_Timezone();
	    	$timezonedata = $timezoneModel->fetchAll('isactive=1','timezone');
	    $timezone->addMultiOption('','Select Time zone');
	    	foreach ($timezonedata->toArray() as $data){
		$timezone->addMultiOption($data['id'],$data['timezone'].' ['.$data['timezone_abbr'].']');
	    	}
		$timezone->setRegisterInArrayValidator(false);
	   
		$unithead = new Zend_Form_Element_Text('unithead');
        $unithead->setLabel('timezone');
		$unithead ->setAttrib('maxlength', '50');
		
		
	    $unithead->addValidator("regex",true,array(                           
                           'pattern'=>'/^[a-zA-Z.\- ?]+$/',
                           'messages'=>array(
                               'regexNotMatch'=>'Please enter valid name.'
                           )
        	));
			
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		$submit->setLabel('Save');
		
		$this->addElements(array($id,$unitname,$unitcode,$description,$start_date,$country,$state,$city,$address1,$address2,$address3,$timezone,$unithead,$submit));
        $this->setElementDecorators(array('ViewHelper')); 
		$this->setElementDecorators(array(
                    'UiWidgetElement',
        ),array('start_date'));
	}
}