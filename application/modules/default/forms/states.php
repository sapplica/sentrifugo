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

class Default_Form_states extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('action',BASE_URL.'states/edit');
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'states');


        $id = new Zend_Form_Element_Hidden('id');
		
		$country = new Zend_Form_Element_Select('countryid');
        $country->setAttrib('class', 'selectoption');
        $country->setAttrib('onchange', 'displayParticularState(this,"otheroption","state","")');
        $country->setRegisterInArrayValidator(false);
        $country->addMultiOption('','Select Country');
        $country->setRequired(true);
		$country->addValidator('NotEmpty', false, array('messages' => 'Please select country.')); 
		
		$state = new Zend_Form_Element_Multiselect('state');
		$state->setAttrib('onchange', 'displayStateCode(this)');
        $state->setRegisterInArrayValidator(false);
        $state->setRequired(true);
		$state->addValidator('NotEmpty', false, array('messages' => 'Please select state.'));
		$state->addValidator(new Zend_Validate_Db_NoRecordExists(
	                                            array(  'table'=>'main_states',
	                                                     'field'=>'state_id_org',
	                                                     'exclude'=>'id!="'.Zend_Controller_Front::getInstance()->getRequest()->getParam('id').'" and isactive=1',    
	
	                                                      ) ) );
		$state->getValidator('Db_NoRecordExists')->setMessage('State already exists.');
		
		$otherstatename = new Zend_Form_Element_Text('otherstatename');
        $otherstatename->setAttrib('maxLength', 20);
       	$otherstatename->addValidators(array(
						 array(
							 'validator'   => 'Regex',
							 'breakChainOnFailure' => true,
							 'options'     => array( 
							 'pattern' =>'/^[^ ][a-zA-Z\s]*$/i',
								 'messages' => array(
										 'regexNotMatch'=>'Please enter valid state name.'
								 )
							 )
						 )
					 ));
		
        $submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		$submit->setLabel('Save');


		 $this->addElements(array($id,$country,$state,$otherstatename,$submit));
         $this->setElementDecorators(array('ViewHelper')); 
	}
}