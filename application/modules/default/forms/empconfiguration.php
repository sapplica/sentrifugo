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

class Default_Form_empconfiguration extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('action',BASE_URL.'empconfiguration');
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'empconfiguration');

		 

		$empConfigureArray = array(
									'employeedocs' => 'Employee Documents',
									'emp_leaves' => 'Employee Leaves',
							   		'emp_holidays' => 'Employee Holidays',
							   		'emp_salary' => 'Salary Details',
									'emppersonaldetails'=>'Personal Details',
								   'empcommunicationdetails'=>'Contact Details',
								   'emp_skills' => 'Employee Skills',
								   'emp_jobhistory' => 'Employee Job History',
								   'experience_details' => 'Experience Details',
								   'education_details' => 'Education  Details',
								   'trainingandcertification_details' => 'Training & Certification  Details',
								   'medical_claims' => 'Medical Claims',
								   'disabilitydetails' => 'Disability Details',
								   'dependency_details' => 'Dependency Details',
								   'visadetails' => 'Visa and Immigration Details',
								   'creditcarddetails' => 'Corporate Card Details',
								   'workeligibilitydetails' => 'Work Eligibility Details',
								   'emp_additional' => 'Additional Details',
								   'emp_renumeration' => 'Remuneration Details',
								   'emp_security' => 'Security Credentials',
								   //'emp_performanceappraisal' => 'Performance Appraisal',
								   'emp_payslips' => 'Pay Slips',
								   'emp_benifits' => 'Benefits',
								   'assetdetails' => 'Asset Details'
								   );

								   $checktype = new Zend_Form_Element_MultiCheckbox('checktype');
								   foreach ($empConfigureArray as $key => $val){
								   	$checktype->addMultiOption($key,$val);
								   }
								   $checktype->setRequired(true);
								   $checktype->addValidator('NotEmpty', false, array('messages' => 'Please select atleast one employee configuration.'));
								   $checktype->setOptions(array('class'=>'empconfigcheckbox'));
								   $checktype->setSeparator(PHP_EOL);
								    
								   $checkall = new Zend_Form_Element_Checkbox('checkall');
								   $checkall->setLabel('Check All');
								 
								   $submit = new Zend_Form_Element_Submit('submit');
								   $submit->setAttrib('id', 'submitbutton');
								   $submit->setLabel('Save');

								   $this->addElements(array($checktype,$checkall,$submit));
								   $this->setElementDecorators(array('ViewHelper'));
	}

}