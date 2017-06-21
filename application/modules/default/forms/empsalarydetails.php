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

class Default_Form_empsalarydetails extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'empsalarydetails');
		
		$id = new Zend_Form_Element_Hidden('id');
				
		$userid = new Zend_Form_Element_Hidden('user_id');
				
		$currencyid = new Zend_Form_Element_Select('currencyid');
		$currencyid->setLabel('Salary Currency');
    	$currencyid->setRegisterInArrayValidator(false);
		
		$salarytype = new Zend_Form_Element_Select('salarytype');
		$salarytype->setLabel("Pay Frequency");
		$salarytype->setAttrib('id', 'jobpayfrequency');
		//$salarytype->setAttrib('onchange', 'changesalarytext(this)');
        $salarytype->setRegisterInArrayValidator(false);
        /*$salarytype->setMultiOptions(array(	
        					'' => 'Select Salary Type',						
							'1'=>'Yearly' ,
							'2'=>'Hourly',
							));*/
		
		$salary = new Zend_Form_Element_Text('salary');
		$salary->setLabel("Salary");
        $salary->setAttrib('maxLength', 8);
	    $salary->addFilter(new Zend_Filter_StringTrim());
		
		$salary->addValidators(array(
						 array(
							 'validator'   => 'Regex',
							 'breakChainOnFailure' => true,
							 'options'     => array( 
							 
							 'pattern'=>'/^[0-9\.]*$/', 
							  'messages' => array('regexNotMatch'=>'Please enter only numbers.'
								 )
							 )
						 )
					 ));
		
		$bankname = new Zend_Form_Element_Text('bankname');
		$bankname->setAttrib('maxlength',40);
		$bankname->setLabel('Bank Name');
		$bankname->addFilters(array('StringTrim'));
		$bankname->addValidator("regex",true,array(
                            'pattern'=>'/^[a-zA-Z][a-zA-Z0-9\-\. ]*$/', 
                           
                           'messages'=>array(
                               'regexNotMatch'=>'Please enter valid bank name.'
                           )
        	));
			
		$accountholder_name = new Zend_Form_Element_Text('accountholder_name');
		$accountholder_name->setAttrib('maxlength',40);
		$accountholder_name->setLabel('Account Holder Name');
		$accountholder_name->addFilters(array('StringTrim'));
		$accountholder_name->addValidators(array(
			         array(
			             'validator'   => 'Regex',
			             'breakChainOnFailure' => true,
			             'options'     => array( 
			             'pattern' =>'/^[a-zA-Z\s]+$/i',
			                 'messages' => array(
			                         'regexNotMatch'=>'Please enter only alphabets.'
			                 )
			             )
			         )
			     ));

        $accountholding = new ZendX_JQuery_Form_Element_DatePicker('accountholding');
		$accountholding->setLabel('Account Holding Since');
		$accountholding->setAttrib('readonly', 'true');
		$accountholding->setAttrib('onfocus', 'this.blur()');
		$accountholding->setOptions(array('class' => 'brdr_none'));	
		
		$accountclasstypeid = new Zend_Form_Element_Select('accountclasstypeid');
		$accountclasstypeid->setLabel('Account Class Type');
    	$accountclasstypeid->setRegisterInArrayValidator(false);
		
		$bankaccountid = new Zend_Form_Element_Select('bankaccountid');
		$bankaccountid->setLabel('Account Type');
    	$bankaccountid->setRegisterInArrayValidator(false);
		
		$accountnumber = new Zend_Form_Element_Text('accountnumber');
		$accountnumber->setAttrib('maxlength',20);
		$accountnumber->setLabel('Account Number');
		$accountnumber->addFilters(array('StringTrim'));
		$accountnumber->addValidator("regex",true,array(
                            'pattern'=>'/^[a-zA-Z0-9 ]*$/', 
                           
                           'messages'=>array(
                               'regexNotMatch'=>'Please enter only alphanumeric characters.'
                           )
        	));

        
    	
				
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		$submit->setLabel('Save');
		
		$this->addElements(array($id,$userid,$currencyid,$salarytype,$salary,$bankname,$accountholder_name,$accountholding,$accountclasstypeid,$bankaccountid,$accountnumber,$submit));
        $this->setElementDecorators(array('ViewHelper')); 
 		 $this->setElementDecorators(array(
                    'UiWidgetElement',
        ),array('accountholding')); 
	}
}