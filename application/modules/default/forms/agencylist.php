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

class Default_Form_agencylist extends Zend_Form
{
	public function init()
	{
	    $this->setMethod('post');		
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name','agencylist');
		$this->setAttrib('action',BASE_URL.'agencylist/edit/id/1');
		
		$id = new Zend_Form_Element_Hidden('id');
		$pocid_1 = new Zend_Form_Element_Hidden('pocid_1');
		$pocid_2 = new Zend_Form_Element_Hidden('pocid_2');		
		$pocid_3 = new Zend_Form_Element_Hidden('pocid_3');
		
		$agencyname = new Zend_Form_Element_Text('agencyname');
        $agencyname->setAttrib('maxLength', 50);
        $agencyname->addFilter(new Zend_Filter_StringTrim());
        $agencyname->setRequired(true);
        $agencyname->addValidator('NotEmpty', false, array('messages' => 'Please enter agency name.'));  
		$agencyname->addValidator("regex",true,array(                           
                           'pattern'=>'/^(?![0-9]{4})[a-zA-Z0-9.\- ?]+$/',
                           'messages'=>array(
                               'regexNotMatch'=>'Please enter valid agency name.'
                           )
        	));
			
		$address = new Zend_Form_Element_Textarea('address');
        $address->setAttrib('rows', 10);
        $address->setAttrib('cols', 50);	
		$address->setRequired(true);
        $address->addValidator('NotEmpty', false, array('messages' => 'Please enter address.'));  		
		
		$primaryphone = new Zend_Form_Element_Text('primaryphone');
        $primaryphone->setAttrib('maxLength', 15);
        $primaryphone->addFilter(new Zend_Filter_StringTrim());
        $primaryphone->setRequired(true);
        $primaryphone->addValidator('NotEmpty', false, array('messages' => 'Please enter primary phone number.'));  
		$primaryphone->addValidators(array(array('StringLength',false,
									  array('min' => 10,
											'max' => 15,
											'messages' => array(
											Zend_Validate_StringLength::TOO_LONG =>
											'Primary phone number must contain at most %max% characters',
											Zend_Validate_StringLength::TOO_SHORT =>
											'Primary phone number must contain at least %min% characters.'
											)))));
		$primaryphone->addValidator("regex",true,array(
                           'pattern'=>'/^(?!0{10})[0-9]+$/', 
                          
                           'messages'=>array(
                               'regexNotMatch'=>'Please enter valid phone number.'
                           )
        ));
		$primaryphone->addValidator(new Zend_Validate_Db_NoRecordExists(
	                                            array(  'table'=>'main_bgagencylist',
	                                                     'field'=>'primaryphone',
	                                                     'exclude'=>'id!="'.Zend_Controller_Front::getInstance()->getRequest()->getParam('id').'" and isactive=1',    
	
	                                                      ) ) );
		$primaryphone->getValidator('Db_NoRecordExists')->setMessage('Primary phone number already exists.');			
		 
		$secondaryphone = new Zend_Form_Element_Text('secondaryphone');
        $secondaryphone->setAttrib('maxLength', 15);
        $secondaryphone->addFilter(new Zend_Filter_StringTrim());
		$secondaryphone->addValidators(array(array('StringLength',false,
									  array('min' => 10,
											'max' => 15,
											'messages' => array(
											Zend_Validate_StringLength::TOO_LONG =>
											'Secondary phone number must contain at most %max% characters',
											Zend_Validate_StringLength::TOO_SHORT =>
											'Secondary phone number must contain at least %min% characters.'
											)))));
		$secondaryphone->addValidator("regex",true,array(
                           'pattern'=>'/^(?!0{10})[0-9]+$/', 
                          
                           'messages'=>array(
                               'regexNotMatch'=>'Please enter valid phone number.'
                           )
        ));
					 
		$checktype = new Zend_Form_Element_Multiselect('bg_checktype');
		$checktype->setRequired(true)->addErrorMessage('Please select screening type.');
		$checktype->addValidator('NotEmpty', false, array('messages' => 'Please select screening type.')); 
		
		$checktypeModal = new Default_Model_Bgscreeningtype();
	    	$typesData = $checktypeModal->fetchAll('isactive=1','type');
			foreach ($typesData->toArray() as $data){
		$checktype->addMultiOption($data['id'],$data['type']);
	    	}
		$checktype->setRegisterInArrayValidator(false);	
		
		$emprole = new Zend_Form_Element_Select('emprole');
		$emprole->setRequired(true)->addErrorMessage('Please select role.');
		$emprole->addValidator('NotEmpty', false, array('messages' => 'Please select role.')); 
		$emprole->addMultiOption('','Select Role');
		$agencyModal = new Default_Model_Agencylist();
	    	$roleData = $agencyModal->getagencyrole();
			foreach ($roleData as $data){
		$emprole->addMultiOption($data['id'],$data['rolename']);
	    	}
		$emprole->setRegisterInArrayValidator(false);	
						
		$website = new Zend_Form_Element_Text('website_url');
		$website->setAttrib('maxLength', 50);
        $website->addFilter(new Zend_Filter_StringTrim());
        $website->setRequired(true);
		$website->addValidator('NotEmpty', false, array('messages' => 'Please enter website URL.'));  
		
		$website->addValidator("regex",true,array(                           
                           'pattern'=>'/^(http:\/\/www|https:\/\/www|www)+\.([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,3})$/',
                           'messages'=>array(
                               'regexNotMatch'=>'Please enter valid URL.'
                           )
        	));
        
		$firstname_1 = new Zend_Form_Element_Text('firstname_1');
        $firstname_1->setAttrib('maxLength', 50);
        $firstname_1->addFilter(new Zend_Filter_StringTrim());
        $firstname_1->setRequired(true);
        $firstname_1->addValidator('NotEmpty', false, array('messages' => 'Please enter first name.'));  
		$firstname_1->addValidator("regex",true,array(                           
                           'pattern'=>'/^[a-zA-Z.\- ?]+$/',
                           'messages'=>array(
                               'regexNotMatch'=>'Please enter valid name.'
                           )
        	));
			
		$lastname_1 = new Zend_Form_Element_Text('lastname_1');
        $lastname_1->setAttrib('maxLength', 50);
        $lastname_1->addFilter(new Zend_Filter_StringTrim());
        $lastname_1->setRequired(true);
        $lastname_1->addValidator('NotEmpty', false, array('messages' => 'Please enter last name.'));  
		$lastname_1->addValidator("regex",true,array(                           
                           'pattern'=>'/^[a-zA-Z.\- ?]+$/',
                           'messages'=>array(
                               'regexNotMatch'=>'Please enter valid name.'
                           )
        	));
			
		$mobile_1 = new Zend_Form_Element_Text('mobile_1');
        $mobile_1->setAttrib('maxLength', 10);
        $mobile_1->addFilter(new Zend_Filter_StringTrim());
		$mobile_1->setRequired(true);
        $mobile_1->addValidator('NotEmpty', false, array('messages' => 'Please enter 10 digit mobile number.'));  
		$mobile_1->addValidators(array(array('StringLength',false,
									  array('min' => 10,
											'max' => 10,
											'messages' => array(
											Zend_Validate_StringLength::TOO_LONG =>
											'Mobile number must contain at most %max% characters',
											Zend_Validate_StringLength::TOO_SHORT =>
											'Mobile number must contain at least %min% characters.',
											)))));
		$mobile_1->addValidator("regex",true,array(
                           'pattern'=>'/^(?!0{10})[0-9]+$/', 
                          
                           'messages'=>array(
                               'regexNotMatch'=>'Please enter valid phone number.'
                           )
        ));
					 
		$email_1 = new Zend_Form_Element_Text('email_1');
        $email_1->setAttrib('maxLength', 50);
		$email_1->addFilter(new Zend_Filter_StringTrim());        
        $email_1->setRequired(true);
		$email_1->addValidator('NotEmpty', false, array('messages' => 'Please enter email.'));
        
		
		$location_1 = new Zend_Form_Element_Text('location_1');
        $location_1->setAttrib('maxLength', 50);
        $location_1->addFilter(new Zend_Filter_StringTrim());
		$location_1->setRequired(true);
        $location_1->addValidator('NotEmpty', false, array('messages' => 'Please enter location.')); 
		
		$country_1 = new Zend_Form_Element_Select('country_1');
        $country_1->setLabel('country');	
		$country_1->setRequired(true);
		$country_1->addValidator('NotEmpty', false, array('messages' => 'Please select country.')); 
		
		$country_1->setAttrib('onchange', 'displayParticularState_normal(this,"","state_1","city_1")');
		    $countryModal = new Default_Model_Countries();
	    	$countriesData = $countryModal->getTotalCountriesList('isactive=1','country_name');
	    $country_1->addMultiOption('','Select country');
	    	foreach ($countriesData as $data){
		$country_1->addMultiOption($data['id'],utf8_encode($data['country_name']) );
	    	}
		$country_1->setRegisterInArrayValidator(false);
		
		$state_1 = new Zend_Form_Element_Select('state_1');
        $state_1->setAttrib('class', 'selectoption');
        $state_1->setAttrib('onchange', 'displayParticularCity_normal(this,"","city_1","")');
        $state_1->setRegisterInArrayValidator(false);
		$state_1->addMultiOption('','Select State');
        $state_1->setRequired(true);
		$state_1->addValidator('NotEmpty', false, array('messages' => 'Please select state.')); 
		
		$city_1 = new Zend_Form_Element_Select('city_1');
        $city_1->setAttrib('class', 'selectoption');
		$city_1->setAttrib('onchange', 'displayCityCode(this)');
        $city_1->setRegisterInArrayValidator(false);
        $city_1->addMultiOption('','Select City');
		$city_1->setRequired(true);
		$city_1->addValidator('NotEmpty', false, array('messages' => 'Please select city.'));
		
		$contact_type_1 = new Zend_Form_Element_Select('contact_type_1');
		
                
                
		$contact_type_1->setLabel('contact_type_1')
		->setMultiOptions(array(	
							''=>'Select Contact Type',
							'1'=>'Primary' ,
							'2'=>'Secondary'
							));
		$contact_type_1->setRegisterInArrayValidator(false);
                $contact_type_1->setRequired(true);
                $contact_type_1->addValidator('NotEmpty', false, array('messages' => 'Please select contact type.')); 
		$firstname_2 = new Zend_Form_Element_Text('firstname_2');
        $firstname_2->setAttrib('maxLength', 50);
        $firstname_2->addFilter(new Zend_Filter_StringTrim());  
		
		
		$lastname_2 = new Zend_Form_Element_Text('lastname_2');
        $lastname_2->setAttrib('maxLength', 50);
        $lastname_2->addFilter(new Zend_Filter_StringTrim());
       
		$mobile_2 = new Zend_Form_Element_Text('mobile_2');
        $mobile_2->setAttrib('maxLength', 10);
        $mobile_2->addFilter(new Zend_Filter_StringTrim());
        										
		$email_2 = new Zend_Form_Element_Text('email_2');
        $email_2->setAttrib('maxLength', 50);
        $email_2->addFilter(new Zend_Filter_StringTrim());		
		
		$location_2 = new Zend_Form_Element_Text('location_2');
        $location_2->setAttrib('maxLength', 50);
        $location_2->addFilter(new Zend_Filter_StringTrim());
		
		$country_2 = new Zend_Form_Element_Select('country_2');
        $country_2->setLabel('country');	
		$country_2->setAttrib('onchange', 'displayParticularState_normal(this,"","state_2","city_2")');
		    $countryModal = new Default_Model_Countries();
	    	$countriesData = $countryModal->getTotalCountriesList('isactive=1','country_name');
	    $country_2->addMultiOption('','Select country');
	    	foreach ($countriesData as $data){
		$country_2->addMultiOption($data['id'],utf8_encode($data['country_name']) );
	    	}
		
		$state_2 = new Zend_Form_Element_Select('state_2');
        $state_2->setAttrib('class', 'selectoption');
        $state_2->setAttrib('onchange', 'displayParticularCity_normal(this,"","city_2","")');
        $state_2->addMultiOption('','Select State');
        
		$city_2 = new Zend_Form_Element_Select('city_2');
        $city_2->setAttrib('class', 'selectoption');
		$city_2->setAttrib('onchange', 'displayCityCode(this)');
        $city_2->addMultiOption('','Select City');
		
		$contact_type_2 = new Zend_Form_Element_Select('contact_type_2');
		$contact_type_2->setLabel('contact_type_2')
		->setMultiOptions(array(	
							''=>'select contact type',
							'1'=>'Primary' ,
							'2'=>'Secondary'
							));
		
		$secondpocid = new Zend_Form_Element_Hidden('secondpocid');
		$valfirstname_2 = Zend_Controller_Front::getInstance()->getRequest()->getParam('firstname_2',null);
		$vallastname_2 = Zend_Controller_Front::getInstance()->getRequest()->getParam('lastname_2',null);
		$valmobile_2 = Zend_Controller_Front::getInstance()->getRequest()->getParam('mobile_2',null);
		$valemail_2 = Zend_Controller_Front::getInstance()->getRequest()->getParam('email_2',null);
		$vallocation_2 = Zend_Controller_Front::getInstance()->getRequest()->getParam('location_2',null);
		$valcountry_2 = Zend_Controller_Front::getInstance()->getRequest()->getParam('country_2',null);
		$valstate_2 = Zend_Controller_Front::getInstance()->getRequest()->getParam('state_2',null);
		$valcity_2 = Zend_Controller_Front::getInstance()->getRequest()->getParam('city_2',null);
		$valcontact_type_2 = Zend_Controller_Front::getInstance()->getRequest()->getParam('contact_type_2',null);
		if($valfirstname_2 != '' || $vallastname_2 != '' || $valmobile_2 != '' || $valemail_2 != '' || $vallocation_2 != '' || $valcountry_2 != '' || $valstate_2  != '' || $valcity_2 != '' || $valcontact_type_2 != '')
		{
			$firstname_2->setRequired(true);
			$firstname_2->addValidator('NotEmpty', false, array('messages' => 'Please enter first name.'));  
			
			$lastname_2->setRequired(true);
			$lastname_2->addValidator('NotEmpty', false, array('messages' => 'Please enter last name.'));  
			
			$mobile_2->setRequired(true);
			$mobile_2->addValidator('NotEmpty', false, array('messages' => 'Please enter 10 digit mobile number.'));  
			
			$email_2->setRequired(true);
			$email_2->addValidator('NotEmpty', false, array('messages' => 'Please enter email.'));  		
			
			$location_2->setRequired(true);
			$location_2->addValidator('NotEmpty', false, array('messages' => 'Please enter location.')); 
			$country_2->setRequired(true);
			$country_2->addValidator('NotEmpty', false, array('messages' => 'Please select country.')); 
			$country_2->setRegisterInArrayValidator(false);
			$state_2->setRequired(true);
			$state_2->addValidator('NotEmpty', false, array('messages' => 'Please select state.')); 
			$state_2->setRegisterInArrayValidator(false);
			$city_2->setRequired(true);
			$city_2->addValidator('NotEmpty', false, array('messages' => 'Please select city.'));	
			$city_2->setRegisterInArrayValidator(false);
			$contact_type_2->setRequired(true);
			$contact_type_2->addValidator('NotEmpty', false, array('messages' => 'Please select contact type.'));	
			$contact_type_2->setRegisterInArrayValidator(false);
		}
		$firstname_2->addValidator("regex",true,array(                           
                           'pattern'=>'/^[a-zA-Z.\- ?]+$/',
                           'messages'=>array(
                               'regexNotMatch'=>'Please enter valid name.'
                           )
        	));
			
		$lastname_2->addValidator("regex",true,array(                           
								   'pattern'=>'/^[a-zA-Z.\- ?]+$/',
								   'messages'=>array(
									   'regexNotMatch'=>'Please enter valid name.'
								   )
					));
					
		$mobile_2->addValidators(array(array('StringLength',false,
											  array('min' => 10,
													'max' => 10,
													'messages' => array(
													Zend_Validate_StringLength::TOO_LONG =>
													'Mobile number must contain at most %max% characters',
													Zend_Validate_StringLength::TOO_SHORT =>
													'Mobile number must contain at least %min% characters.',
													)))));
		$mobile_2->addValidator("regex",true,array(
                           'pattern'=>'/^(?!0{10})[0-9]+$/', 
                          
                           'messages'=>array(
                               'regexNotMatch'=>'Please enter valid phone number.'
                           )
        ));
		
			
		
		$firstname_3 = new Zend_Form_Element_Text('firstname_3');
        $firstname_3->setAttrib('maxLength', 50);
        $firstname_3->addFilter(new Zend_Filter_StringTrim());
        
			
		$lastname_3 = new Zend_Form_Element_Text('lastname_3');
        $lastname_3->setAttrib('maxLength', 50);
        $lastname_3->addFilter(new Zend_Filter_StringTrim());	
			
		$mobile_3 = new Zend_Form_Element_Text('mobile_3');
        $mobile_3->setAttrib('maxLength', 10);
        $mobile_3->addFilter(new Zend_Filter_StringTrim());		
					 
		$email_3 = new Zend_Form_Element_Text('email_3');
        $email_3->setAttrib('maxLength', 50);
        $email_3->addFilter(new Zend_Filter_StringTrim());
		
		
		$location_3 = new Zend_Form_Element_Text('location_3');
        $location_3->setAttrib('maxLength', 50);
        $location_3->addFilter(new Zend_Filter_StringTrim());
		
		$country_3 = new Zend_Form_Element_Select('country_3');
        $country_3->setLabel('country');	
		$country_3->setAttrib('onchange', 'displayParticularState_normal(this,"","state_3","city_3")');
		    $countryModal = new Default_Model_Countries();
	    	$countriesData = $countryModal->getTotalCountriesList('isactive=1','country_name');
	    $country_3->addMultiOption('','Select country');
	    	foreach ($countriesData as $data){
		$country_3->addMultiOption($data['id'],utf8_encode($data['country_name']) );
	    	}
		
		$state_3 = new Zend_Form_Element_Select('state_3');
        $state_3->setAttrib('class', 'selectoption');
        $state_3->setAttrib('onchange', 'displayParticularCity_normal(this,"","city_3","")');
        $state_3->addMultiOption('','Select State');
        
		$city_3 = new Zend_Form_Element_Select('city_3');
        $city_3->setAttrib('class', 'selectoption');
		$city_3->setAttrib('onchange', 'displayCityCode(this)');
        $city_3->addMultiOption('','Select City');
		
		$contact_type_3 = new Zend_Form_Element_Select('contact_type_3');
		$contact_type_3->setLabel('contact_type_3')
		->setMultiOptions(array(
							''=>'select contact type',
							'1'=>'Primary' ,
							'2'=>'Secondary'
							));
		
		$thirdpocid = new Zend_Form_Element_Hidden('thirdpocid');
		$thirdData = Zend_Controller_Front::getInstance()->getRequest()->getParam('thirdpocid',null);
		$valfirstname_3 = Zend_Controller_Front::getInstance()->getRequest()->getParam('firstname_3',null);
		$vallastname_3 = Zend_Controller_Front::getInstance()->getRequest()->getParam('lastname_3',null);
		$valmobile_3 = Zend_Controller_Front::getInstance()->getRequest()->getParam('mobile_3',null);
		$valemail_3 = Zend_Controller_Front::getInstance()->getRequest()->getParam('email_3',null);
		$vallocation_3 = Zend_Controller_Front::getInstance()->getRequest()->getParam('location_3',null);
		$valcountry_3 = Zend_Controller_Front::getInstance()->getRequest()->getParam('country_3',null);
		$valstate_3 = Zend_Controller_Front::getInstance()->getRequest()->getParam('state_3',null);
		$valcity_3 = Zend_Controller_Front::getInstance()->getRequest()->getParam('city_3',null);
		$valcontact_type_3 = Zend_Controller_Front::getInstance()->getRequest()->getParam('contact_type_3',null);
		
		if($valfirstname_3 != '' || $vallastname_3 != '' || $valmobile_3 != '' || $valemail_3 != '' || $vallocation_3 != '' || $valcountry_3 != '' || $valstate_3  != '' || $valcity_3 != '' || $valcontact_type_3 != '')
		{
			$firstname_3->setRequired(true);
			$firstname_3->addValidator('NotEmpty', false, array('messages' => 'Please enter first name.'));  
			
			$lastname_3->setRequired(true);
			$lastname_3->addValidator('NotEmpty', false, array('messages' => 'Please enter last name.'));  
			
			$mobile_3->setRequired(true);
			$mobile_3->addValidator('NotEmpty', false, array('messages' => 'Please enter  10 digit mobile number.'));  
			
			$email_3->setRequired(true);
			$email_3->addValidator('NotEmpty', false, array('messages' => 'Please enter email.'));  
			
			$location_3->setRequired(true);
			$location_3->addValidator('NotEmpty', false, array('messages' => 'Please enter location.')); 
			$country_3->setRequired(true);
			$country_3->addValidator('NotEmpty', false, array('messages' => 'Please select country.')); 
			$country_3->setRegisterInArrayValidator(false);			
			$state_3->setRequired(true);
			$state_3->addValidator('NotEmpty', false, array('messages' => 'Please select state.')); 
			$state_3->setRegisterInArrayValidator(false);
			$city_3->setRequired(true);
			$city_3->addValidator('NotEmpty', false, array('messages' => 'Please select city.'));	
			$city_3->setRegisterInArrayValidator(false);
			$contact_type_3->setRequired(true);
			$contact_type_3->addValidator('NotEmpty', false, array('messages' => 'Please select contact type.'));	
			$contact_type_3->setRegisterInArrayValidator(false);
		}
		$firstname_3->addValidator("regex",true,array(                           
                           'pattern'=>'/^[a-zA-Z.\- ?]+$/',
                           'messages'=>array(
                               'regexNotMatch'=>'Please enter valid name.'
                           )
        	));
		$lastname_3->addValidator("regex",true,array(                           
                           'pattern'=>'/^[a-zA-Z.\- ?]+$/',
                           'messages'=>array(
                               'regexNotMatch'=>'Please enter valid name.'
                           )
        	));
		
		$mobile_3->addValidators(array(array('StringLength',false,
									  array('min' => 10,
											'max' => 10,
											'messages' => array(
											Zend_Validate_StringLength::TOO_LONG =>
											'Mobile number must contain at most %max% characters',
											Zend_Validate_StringLength::TOO_SHORT =>
											'Mobile number must contain at least %min% characters.',
											)))));
		$mobile_3->addValidator("regex",true,array(
                           'pattern'=>'/^(?!0{10})[0-9]+$/', 
                          
                           'messages'=>array(
                               'regexNotMatch'=>'Please enter valid phone number.'
                           )
        ));
					 
		
		
		
		
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		$submit->setLabel('Save');
		
		$this->addElements(array($id,$pocid_1,$pocid_2,$pocid_3,$agencyname,$address,$primaryphone,$secondaryphone,$checktype,$website,$firstname_1,$lastname_1,$mobile_1,$email_1,$location_1,$country_1,$state_1,$city_1,$contact_type_1,$firstname_2,$lastname_2,$mobile_2,$email_2,$location_2,$country_2,$state_2,$city_2,$contact_type_2,$secondpocid,$firstname_3,$lastname_3,$mobile_3,$email_3,$location_3,$country_3,$state_3,$city_3,$contact_type_3,$thirdpocid,$emprole,$submit));
        $this->setElementDecorators(array('ViewHelper')); 
	}
}
?>