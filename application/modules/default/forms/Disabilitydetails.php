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

class Default_Form_Disabilitydetails extends Zend_Form
{ 
	public function init()
	{
		
		$this->setMethod('post');		
        $this->setAttrib('id', 'formid');
        $this->setAttrib('name','disabilitydetails');
        $this->setAttrib('action',BASE_URL.'disabilitydetails/add/');
		
        $id = new Zend_Form_Element_Hidden('id');
        $user_id = new Zend_Form_Element_Hidden('user_id');
	   	// Description	or Reason ....
		$desc = new Zend_Form_Element_Textarea('disability_description');
        $desc->setAttrib('rows', 10);
        $desc->setAttrib('cols', 50);	
		$desc->addValidators(array(
						 array(
							 'validator'   => 'Regex',
							 'breakChainOnFailure' => true,
							 'options'     => array( 
							 'pattern' =>'/^[a-zA-Z][a-zA-Z0-9\-\,\.\&\:\"\'\s]+$/i',
								 'messages' => array(
										 'regexNotMatch'=>'Please start with alphabets.'
								 )
							 )
						 )
					 ));

       //Disability Name ... 
        
        $disability_name = new Zend_Form_Element_Text('disability_name');
        $disability_name->addFilter(new Zend_Filter_StringTrim());
		$disability_name->setAttrib("maxlength",50);
		
		$disability_name->addValidators(array(
						 array(
							 'validator'   => 'Regex',
							 'breakChainOnFailure' => true,
							 'options'     => array( 
							 'pattern' =>'/^[a-zA-Z][a-zA-Z0-9\-\s]+$/i',
								 'messages' => array(
										 'regexNotMatch'=>'Please enter valid disability name.'
								 )
							 )
						 )
					 )); 
		
		//Disablity Type 
		
		$disabilityType = new Zend_Form_Element_Select('disability_type');
       
		$disabilityType->setRegisterInArrayValidator(false);	
		$disabilityType->addMultiOptions(array(''=>'Select Disability type',
								'blindness and visual impairments'=>"Blindness and Visual Impairments",
								'health impairments'=>"Health Impairments",
								'hearing impairments'=>"Hearing Impairments",
						        'learning disabilities'=>"Learning Disabilities" ,
						       	'mental illness or emotional disturbances'=>"Mental Illness or Emotional disturbances",
								'mobility or orthopedic impairments'=>"Mobility or Orthopedic Impairments",
								'other impairments'=>"Other Impairments",
								'speech or language impairments'=>"Speech or Language Impairments"
									));
		
		$disabilityType->setAttrib('onchange', 'showdisabilityField(this.id)');	
		
		//Other field for disability type....
		$other_disability_type = new Zend_Form_Element_Text('other_disability_type');
        $other_disability_type->addFilter(new Zend_Filter_StringTrim());
		$other_disability_type->setAttrib("maxlength",50);
     
		$disabilitytypeVal = Zend_Controller_Front::getInstance()->getRequest()->getParam('disability_type',null);
		if($disabilitytypeVal == "other impairments")
		{
			//$other_disability_type->setRequired(true);
			//$other_disability_type->addValidator('NotEmpty', false, array('messages' => 'Please enter any other disability type.'));
		}
		
		$other_disability_type->addValidators(array(
						 array(
							 'validator'   => 'Regex',
							 'breakChainOnFailure' => true,
							 'options'     => array( 
							 'pattern' =>'/^[a-zA-Z][a-zA-Z0-9\-\s]+$/i',
							 'messages' => array(
										 'regexNotMatch'=>'Please enter valid disability type.'
								 )
							 )
						 )
					 )); 
		
		//Form Submit....
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		$submit->setLabel('Save');

		$this->addElements(array($id,$user_id,$disability_name,
								$disabilityType,$other_disability_type ,
								$desc,
							$submit));
       $this->setElementDecorators(array('ViewHelper')); 
				
		
        
	}
	
}
         