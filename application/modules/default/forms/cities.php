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

class Default_Form_cities extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('action',BASE_URL.'cities/edit');
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'cities');


        $id = new Zend_Form_Element_Hidden('id');
		
		$country = new Zend_Form_Element_Select('countryid');
        $country->setAttrib('class', 'selectoption');
        $country->setAttrib('onchange', 'displayParticularState(this,"state","state","")');
        $country->setRegisterInArrayValidator(false);
        $country->addMultiOption('','Select Country');
	    $country->setRequired(true);
		$country->addValidator('NotEmpty', false, array('messages' => 'Please select country.'));
		
		$state = new Zend_Form_Element_Select('state');
        $state->setAttrib('class', 'selectoption');
        $state->setAttrib('onchange', 'displayParticularCity(this,"otheroption","city","")');
        $state->setRegisterInArrayValidator(false);
        $state->addMultiOption('','Select State');
			
        $state->setRequired(true);
		$state->addValidator('NotEmpty', false, array('messages' => 'Please select state.')); 
		
		$city = new Zend_Form_Element_Multiselect('city');
        $city->setAttrib('class', 'selectoption');
		$city->setAttrib('onchange', 'displayCityCode(this)');
        $city->setRegisterInArrayValidator(false);
       
        $city->setRequired(true);
		$city->addValidator('NotEmpty', false, array('messages' => 'Please select city.'));
		$city->addValidator(new Zend_Validate_Db_NoRecordExists(
	                                            array(  'table'=>'main_cities',
	                                                     'field'=>'city_org_id',
	                                                     'exclude'=>'id!="'.Zend_Controller_Front::getInstance()->getRequest()->getParam('id').'" and isactive=1',    
	
	                                                      ) ) );
		$city->getValidator('Db_NoRecordExists')->setMessage('City already exists.');
		
		$othercityname = new Zend_Form_Element_Text('othercityname');
        $othercityname->setAttrib('maxLength', 20);
        
       	$othercityname->addValidators(array(
						 array(
							 'validator'   => 'Regex',
							 'breakChainOnFailure' => true,
							 'options'     => array( 
							 'pattern' =>'/^[^ ][a-zA-Z\s]*$/i',
								 'messages' => array(
										 'regexNotMatch'=>'Please enter valid city name.'
								 )
							 )
						 )
					 ));
		
        $submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		$submit->setLabel('Save');


		 $this->addElements(array($id,$country,$state,$othercityname,$city,$submit));
         $this->setElementDecorators(array('ViewHelper')); 
	}
}