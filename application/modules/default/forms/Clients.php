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
/**
 * 
 * Create or Edit Clients Form.
 * @author Sagarsoft
 *
 */
class Default_Form_Clients extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('action',BASE_URL.'default/clients/edit');
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'addOrEditClients');


        $id = new Zend_Form_Element_Hidden('id');
        
        $clientname = new Zend_Form_Element_Text('client_name');
        $clientname->setLabel("Client");
        $clientname->setAttrib('maxLength', 50);
        $clientname->addFilter(new Zend_Filter_StringTrim());
        $clientname->setRequired(TRUE);
        $clientname->addValidator('NotEmpty', false, array('messages' => 'Please enter client name.'));  
		$clientname->addValidator("regex",true,array(                           
                           'pattern'=>'/^(?![0-9]*$)[a-zA-Z0-9.,&\(\)\/\-_\' ?]+$/',
                           'messages'=>array(
                               'regexNotMatch'=>'Please enter a valid client name.'
                           )
        			));

        $phonenumber = new Zend_Form_Element_Text('phone_no');
        $phonenumber->addFilter(new Zend_Filter_StringTrim());
		$phonenumber->setAttrib('maxLength', 15);
		$phonenumber->setLabel("Phone Number");
		$phonenumber->addValidators(array(array('StringLength',false,
									  array('min' => 10,
											'max' => 15,
											'messages' => array(
											Zend_Validate_StringLength::TOO_LONG =>
											'Phone number must contain at most %max% characters.',
											Zend_Validate_StringLength::TOO_SHORT =>
											'Phone number must contain at least %min% characters.',
											)))));
		$phonenumber->addValidators(array(
						 array(
							 'validator'   => 'Regex',
							 'breakChainOnFailure' => true,
							 'options'     => array( 
							 'pattern' =>'/^[0-9-]+$/i',
								 'messages' => array(
										 
										 'regexNotMatch'=>'Please enter a valid phone number.'
								 )
							 )
						 )
					 )); 		

		$emailaddress = new Zend_Form_Element_Text("email");
		$emailaddress->setRequired(FALSE);
        //$emailaddress->addValidator('NotEmpty', false, array('messages' => 'Please enter email.'));
        $emailaddress->addValidator("regex",true,array(
         			    'pattern'=>'/^(?!.*\.{2})[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/',                            
                        'messages'=>array(
                        'regexNotMatch'=>'Please enter a valid email.'
              		)
        	    ));
        $emailaddress->setLabel("Email");
        $emailaddress->setAttrib("class", "formDataElement");

        $pointofcontact = new Zend_Form_Element_Text('poc');
        $pointofcontact->setAttrib('maxLength', 90);
        $pointofcontact->setLabel("Point of Contact");
        $pointofcontact->addFilter(new Zend_Filter_StringTrim());
        $pointofcontact->setRequired(TRUE);
        $pointofcontact->addValidator('NotEmpty', false, array('messages' => 'Please enter point of contact.'));  
		$pointofcontact->addValidator("regex",true,array(                           
                           'pattern'=>'/^(?![0-9]*$)[a-zA-Z.0-9\-_\' ?]+$/',
                           'messages'=>array(
                               'regexNotMatch'=>'Please enter a valid point of contact.'
                           )
        			));
		
        $address = new Zend_Form_Element_Textarea('address');
        $address->setAttrib('maxLength', 180);
        $address->setLabel("Address");
        $address->addFilter(new Zend_Filter_StringTrim());
        			
        $country = new Zend_Form_Element_Select('country_id');
        $country->setLabel('Country');	
		$country->setRequired(FALSE);
		//$country->addValidator('NotEmpty', false, array('messages' => 'Please select country.')); 
		
		$country->setAttrib('onchange', 'getStates()');
		
		$countryModal = new Timemanagement_Model_Countries();
	    $countriesData = $countryModal->fetchAll('is_active=1','country_name');
	    $country->addMultiOption('','Select country');
	    foreach ($countriesData->toArray() as $data){
			$country->addMultiOption($data['id'],utf8_encode($data['country_name']));
	    }
		$country->setRegisterInArrayValidator(false);	
		
		$state = new Zend_Form_Element_Select('state_id');
		$state->setLabel('State');	
        $state->setAttrib('class', 'selectoption');
        $state->setRegisterInArrayValidator(false);
		$state->addMultiOption('','Select State');
        $state->setRequired(FALSE);
		//$state->addValidator('NotEmpty', false, array('messages' => 'Please select state.')); 
		
		$fax = new Zend_Form_Element_Text('fax');
        $fax->addFilter(new Zend_Filter_StringTrim());
		$fax->setAttrib('maxLength', 15);
		$fax->setLabel("Fax");
		$fax->addValidators(array(array('StringLength',false,
									  array('min' => 10,
											'max' => 15,
											'messages' => array(
											Zend_Validate_StringLength::TOO_LONG =>
											'Fax number must contain at most %max% characters.',
											Zend_Validate_StringLength::TOO_SHORT =>
											'Fhone number must contain at least %min% characters.',
											)))));
		$fax->addValidators(array(
						 array(
							 'validator'   => 'Regex',
							 'breakChainOnFailure' => true,
							 'options'     => array( 
							 'pattern' =>'/^[0-9-]+$/i',
								 'messages' => array(
										 
										 'regexNotMatch'=>'Please enter a valid fax number.'
								 )
							 )
						 )
					 )); 		
		
		
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		$submit->setLabel('Save');

		$this->addElements(array($id,$clientname,$country,$state,$address,$pointofcontact,$emailaddress,$phonenumber,$submit,$fax));
        $this->setElementDecorators(array('ViewHelper')); 
         
	}
	
}