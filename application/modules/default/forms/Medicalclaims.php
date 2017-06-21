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

class Default_Form_medicalclaims extends Zend_Form
{ 
	public function init()
	{
		
		$this->setMethod('post');		
        $this->setAttrib('id', 'formid');
        $this->setAttrib('name','medicalclaims');
       
		
        $id = new Zend_Form_Element_Hidden('id');
       $userid = new Zend_Form_Element_Hidden('user_id');
	   
		
       
	   //	Type of Injury .... 
        $type = new Zend_Form_Element_Select('type');
		$type->setRequired(true)->addErrorMessage('Please select medical claim type.');
	   	$type->addValidator('NotEmpty', false, array('messages' => 'Please select medical claim type.'));
		$type->addMultiOptions(array(''=>'Select Medical Claim Type',
									3=>"Disability",
									4=>"Injury",
									2=>"Maternity",
									1=>"Paternity"	));
		$type->setAttrib('onchange', 'showformFields(this.id,"'.DATE_DESCRIPTION .'")');	
		 
    	// Description	or Injury Reason ....
		$desc = new Zend_Form_Element_Textarea('description');
        $desc->setAttrib('rows', 10);
        $desc->setAttrib('cols', 50);	
		
        //	Injured Date ..
        $injured_date = new ZendX_JQuery_Form_Element_DatePicker('injured_date');
		$injured_date->setAttrib('readonly', 'true');
		$injured_date->setAttrib('onfocus', 'this.blur()'); 
		$injured_date->setOptions(array('class' => 'brdr_none'));	
		$injured_date->setRequired(true);
        $injured_date->addValidator('NotEmpty', false, array('messages' => 'Please select date.'));
		$injured_date->setAttrib('onchange', 'medicalclaimDates_validation("injured_date","leavebyemp_from_date",this,"",1)');	
		
        //Injury Name ... (disable for maternity & paternity types)
        
        $injury_name = new Zend_Form_Element_Text('injury_name');
        $injury_name->addFilter(new Zend_Filter_StringTrim());
        $injury_name->setAttrib('maxLength', 50);
		$injury_name->addValidators(array(
						 array(
							 'validator'   => 'Regex',
							 'breakChainOnFailure' => true,
							 'options'     => array( 
							 'pattern' =>'/^[a-zA-Z\s]+$/i',
								 'messages' => array(
										 'regexNotMatch'=>'Please enter only alphabetic characters.'
								 )
							 )
						 )
					 )); 
		
		// Injury Severity....(enable only for injury type)
		$injury_severity = new Zend_Form_Element_Select('injury_severity');
		$injury_severity->addMultiOptions(array(''=>"Select injury severity",1=>"Major",2=>"Minor"));
		$injury_severity->addValidator('NotEmpty', false, array('messages' => 'Please select severity type.'));
      
		//Disablity Type (Only for type - 'disablity')
		$disabilityType = new Zend_Form_Element_Select('disability_type');
       	$disabilityType->addMultiOptions(array(''=>'Select disability type',
									'blindness and visual impairments'=>"Blindness and visual impairments",
									'health impairments'=>"Health Impairments",
									'hearing impairments'=>"Hearing impairments",
									'learning disabilities'=>"Learning Disabilities" ,
									'mental illness or emotional disturbances'=>"Mental illness or emotional disturbances",
									'mobility or orthopedic impairments'=>"Mobility or Orthopedic Impairments",
									
									'other impairments'=>"Other impairments",
									'speech or language impairments'=>"Speech or language impairments"
									));
		
		$disabilityType->setAttrib('onchange', 'showdisabilityField(this.id)');	
		
		//Other field for disability type....
		$other_disability_type = new Zend_Form_Element_Text('other_disability_type');
        $other_disability_type->addFilter(new Zend_Filter_StringTrim());
        $other_disability_type->setAttrib('maxLength', 50);
		$other_disability_type->addValidators(array(
						 array(
							 'validator'   => 'Regex',
							 'breakChainOnFailure' => true,
							 'options'     => array( 
							 'pattern' =>'/^[a-zA-Z\s]+$/i',
								 'messages' => array(
										 'regexNotMatch'=>'Please enter only alphabetic characters.'
								 )
							 )
						 )
					 )); 
		
		//Medical insurer name....
		$insurer_name = new Zend_Form_Element_Text('insurer_name');
        $insurer_name->addFilter(new Zend_Filter_StringTrim());
        $insurer_name->setRequired(true);
		$insurer_name->setAttrib('maxLength', 50);
        $insurer_name->addValidator('NotEmpty', false, array('messages' => 'Please enter insurer name.'));
        $insurer_name->addValidators(array(
						 array(
							 'validator'   => 'Regex',
							 'breakChainOnFailure' => true,
							 'options'     => array( 
							 'pattern' =>'/^[a-zA-Z0-9\s]+$/i',
								 'messages' => array(
										 'regexNotMatch'=>'Please enter valid insurer name.'
								 )
							 )
						 )
					 )); 
        
		// Date to join...
        $expected_date_join = new ZendX_JQuery_Form_Element_DatePicker('expected_date_join');
		$expected_date_join->setAttrib('readonly', 'true');	
        $expected_date_join->setAttrib('onfocus', 'this.blur()');  		
		$expected_date_join->setOptions(array('class' => 'brdr_none'));	
		$expected_date_join->setRequired(true);
        $expected_date_join->addValidator('NotEmpty', false, array('messages' => 'Please select date.'));
        /*	date of joining should be greater than injured/paternity/maternity/disability date		*/
		$expected_date_join->setAttrib('onchange', 'medicalclaimDates_validation("injured_date","expected_date_join",this,"leavebyemp_from_date",5)');	
        
		//Leave by Employeer .. to date	(approved leave to date)
        $leavebyemp_to_date = new ZendX_JQuery_Form_Element_DatePicker('leavebyemp_to_date');
		$leavebyemp_to_date->setAttrib('readonly', 'true');	
        $leavebyemp_to_date->setAttrib('onfocus', 'this.blur()'); 		
		$leavebyemp_to_date->setOptions(array('class' => 'brdr_none'));	
		$leavebyemp_to_date->setRequired(true);
        $leavebyemp_to_date->addValidator('NotEmpty', false, array('messages' => 'Please select date.'));
       /*	Employee applied leave end date should be greater than or equal to start date	*/
		$leavebyemp_to_date->setAttrib('onchange', 'medicalclaimDates_validation("leavebyemp_from_date","leavebyemp_to_date",this,"",2)');	
       
	   //Leave by Employeer .. from date	(approved leave from date)
        $leavebyemp_from_date = new ZendX_JQuery_Form_Element_DatePicker('leavebyemp_from_date');
		$leavebyemp_from_date->setAttrib('readonly', 'true');
        $leavebyemp_from_date->setAttrib('onfocus', 'this.blur()'); 		
		$leavebyemp_from_date->setOptions(array('class' => 'brdr_none'));	
		$leavebyemp_from_date->setRequired(true);
        $leavebyemp_from_date->addValidator('NotEmpty', false, array('messages' => 'Please select date.'));
		/*	Employee applied leave start date should be greater than or equal to injured/paternity/maternity/disability	 date
		*/
		$leavebyemp_from_date->setAttrib('onchange', 'medicalclaimDates_validation("injured_date","leavebyemp_from_date",this,"",1)');	
		
		      
        // No of days...
        $leavebyemp_days = new Zend_Form_Element_Text('leavebyemp_days');
        $leavebyemp_days->addFilter(new Zend_Filter_StringTrim());
        $leavebyemp_days->setAttrib('readonly', 'true');	
        $leavebyemp_days->setAttrib('onfocus', 'this.blur()');  		
		        
        //Employee Leave to date....	(employee applied leave to date)
        $empleave_to_date = new ZendX_JQuery_Form_Element_DatePicker('empleave_to_date');
		$empleave_to_date->setAttrib('readonly', 'true');
		$empleave_to_date->setAttrib('onfocus', 'this.blur()'); 
		$empleave_to_date->setOptions(array('class' => 'brdr_none'));	
		

        //Employee Leave from date....(employee applied leave from date)
        $empleave_from_date = new ZendX_JQuery_Form_Element_DatePicker('empleave_from_date');
		$empleave_from_date->setAttrib('readonly', 'true');
		$empleave_from_date->setAttrib('onfocus', 'this.blur()'); 
		$empleave_from_date->setOptions(array('class' => 'brdr_none'));	
		
        
         // No of days...
        $empleave_days = new Zend_Form_Element_Text('empleave_days');
        $empleave_days->setAttrib('maxLength', 10);
		$empleave_days->setAttrib('readonly', 'true');		
        $empleave_days->addFilter(new Zend_Filter_StringTrim());
        $empleave_days->addValidators(array(
						 array(
							 'validator'   => 'Regex',
							 'breakChainOnFailure' => true,
							 'options'     => array( 
							 'pattern' =>'/^[0-9]+$/i',
								 'messages' => array(
										 'regexNotMatch'=>'Please enter only numeric characters.'
								 )
							 )
						 )
					 )); 
        //Hospital Name..
        $hosp_name = new Zend_Form_Element_Text('hospital_name');
        $hosp_name->setAttrib('maxLength', 50);
        $hosp_name->addFilter(new Zend_Filter_StringTrim());
		$hosp_name->addValidators(array(
						 array(
							 'validator'   => 'Regex',
							 'breakChainOnFailure' => true,
							 'options'     => array( 
							 'pattern' =>'/^[a-zA-z0-9\s]+$/i',
								 'messages' => array(
										 'regexNotMatch'=>'Please enter valid hospital name.'
								 )
							 )
						 )
					 )); 
        // Hospital Address .......
        $hosp_addr = new Zend_Form_Element_Textarea('hospital_addr');
        $hosp_addr->setAttrib('rows', 10);
        $hosp_addr->setAttrib('cols', 50);	
		        
       // Room or ward number......
       	$room_num = new Zend_Form_Element_Text('room_num');
      	$room_num->addFilter(new Zend_Filter_StringTrim());
        $room_num->setAttrib('maxLength', 5);
        $room_num->addValidators(array(array('StringLength',false,
									  array('min'=>1,
											'max' => 5,
											'messages' => array(
											Zend_Validate_StringLength::TOO_LONG =>
											'Room/Ward number must contain at most %max% characters.',
											Zend_Validate_StringLength::TOO_SHORT =>
											'Room/Ward number must contain at least %min% characters.'
											)))));
		$room_num->addValidators(array(
						 array(
							 'validator'   => 'Regex',
							 'breakChainOnFailure' => true,
							 'options'     => array( 
							 
							'pattern'=>'/^[a-zA-Z\d]+$/',
								 'messages' => array(
										 'regexNotMatch'=>'Please enter valid room number.'
								 )
							 )
						 )
					 )); 
        // Name of general physician..
       	$gp_name = new Zend_Form_Element_Text('gp_name');
      	$gp_name->setAttrib('maxLength', 50);
        $gp_name->addFilter(new Zend_Filter_StringTrim());
        $gp_name->addValidators(array(
						 array(
							 'validator'   => 'Regex',
							 'breakChainOnFailure' => true,
							 'options'     => array( 
							 'pattern' =>'/^[a-zA-z\.\s]+$/i',
								 'messages' => array(
										 'regexNotMatch'=>'Please enter only alphabetic characters.'
								 )
							 )
						 )
					 )); 
        // Deatils pf treatement provided .. 
        
        $treatment_details = new Zend_Form_Element_Textarea('treatment_details');
        $treatment_details->setAttrib('rows', 10);
        $treatment_details->setAttrib('cols', 50);	
		
        // total cost......
       	$total_cost = new Zend_Form_Element_Text('total_cost');
      	$total_cost->setAttrib('maxLength', 10);
        $total_cost->addFilter(new Zend_Filter_StringTrim());
       	
		$total_cost->addValidators(array(
						 array(
							 'validator'   => 'Regex',
							 'breakChainOnFailure' => true,
							 'options'     => array( 
							 'pattern' =>'/^[0-9]+$/i',
								 'messages' => array(
										 'regexNotMatch'=>'Please enter only numbers.'
								 )
							 )
						 )
					 )); 
        // Amount claimed for.....
        $amount_claimed = new Zend_Form_Element_Text('amount_claimed');
      	$amount_claimed->setAttrib('maxLength', 10);
        $amount_claimed->addFilter(new Zend_Filter_StringTrim());
		$amount_claimed->setAttrib('onblur', 'validatecost()');
        $amount_claimed->setRequired(true);
        $amount_claimed->addValidator('NotEmpty', false, array('messages' => 'Please enter amount claimed.'));
       	$amount_claimed->addValidators(array(
						 array(
							 'validator'   => 'Regex',
							 'breakChainOnFailure' => true,
							 'options'     => array( 
							 'pattern' =>'/^[0-9]+$/i',
								 'messages' => array(
										 'regexNotMatch'=>'Please enter only numbers.'
								 )
							 )
						 )
					 )); 
        // Amount approved ....
        $amount_approved = new Zend_Form_Element_Text('amount_approved');
      	$amount_approved->setAttrib('maxLength', 10);
        $amount_approved->addFilter(new Zend_Filter_StringTrim());
        $amount_approved->setRequired(true);
        $amount_approved->addValidator('NotEmpty', false, array('messages' => 'Please enter amount approved.'));
        $amount_approved->addValidators(array(
						 array(
							 'validator'   => 'Regex',
							 'breakChainOnFailure' => true,
							 'options'     => array( 
							 'pattern' =>'/^[0-9]+$/i',
								 'messages' => array(
										 'regexNotMatch'=>'Please enter only numbers.'
								 )
							 )
						 )
					 )); 
		
	   
	   //Form Submit....
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		$submit->setLabel('Save');

		$this->addElements(array($id,$userid,$type,$desc,$injured_date,$injury_name,
							$injury_severity,$insurer_name,
							$expected_date_join,$leavebyemp_to_date,$leavebyemp_from_date,$leavebyemp_days,
							$empleave_to_date,$empleave_from_date,$empleave_days,$disabilityType,$other_disability_type ,
							$hosp_name,
							$hosp_addr,
							$room_num,
							$gp_name,
							$treatment_details,
							$total_cost,
							$amount_claimed,
							$amount_approved,
							$submit));
       $this->setElementDecorators(array('ViewHelper')); 
		
		$this->setElementDecorators(array(
                    'UiWidgetElement',
        ),array('injured_date','expected_date_join','leavebyemp_to_date','leavebyemp_from_date','empleave_to_date','empleave_from_date'));
		
		
        
	}
	
}
         