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
 * Create or Edit Expenses Form.
 * @author Sagarsoft
 *
 */
class Expenses_Form_Expenses extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('action',BASE_URL.'expenses/expenses/edit');
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'addOrEditExpenses');


        $id = new Zend_Form_Element_Hidden('id');
        
        $expense_name = new Zend_Form_Element_Text('expense_name');
        $expense_name->setLabel("Expense Name");
        $expense_name->setAttrib('maxLength', 50);
        $expense_name->addFilter(new Zend_Filter_StringTrim());
        $expense_name->setRequired(TRUE);
        $expense_name->addValidator('NotEmpty', false, array('messages' => 'Please enter expense name.'));  
		$expense_name->addValidator("regex",true,array(                           
                           'pattern'=>'/^(?![0-9]*$)[a-zA-Z0-9.,&\(\)\/\-_\' ?]+$/',
                           'messages'=>array(
                               'regexNotMatch'=>'Please enter a valid expense name.'
                           )
        			));
					
		$expense_category = new Zend_Form_Element_Select('category_id');
		$expense_category->setLabel("Category");
		$expense_category->addMultiOption('','Select Category');
		$expense_category->setRequired(true);
		$expense_category->addValidator('NotEmpty', false, array('messages' => 'Please select category.'));
		$expense_category->setRegisterInArrayValidator(false);
    		
		$expense_date = new ZendX_JQuery_Form_Element_DatePicker('expense_date');
		$expense_date->setLabel("Expense date");
		$expense_date->setOptions(array('class' => 'brdr_none'));
		$expense_date->setRequired(true);
		$expense_date->setAttrib('readonly', 'true');
		$expense_date->setAttrib('onfocus', 'this.blur()');
		$expense_date->addValidator('NotEmpty', false, array('messages' => 'Please select expense date.'));  
	
		
		$base_project = new Zend_Form_Element_Select('project_id');
		$base_project->addMultiOption('','Select Project');
		$base_project->setRegisterInArrayValidator(false);				
		
		$currency = new Zend_Form_Element_Select('expense_currency_id');
		//$currency->addMultiOption('','');
		$currency->setLabel("Amount");
		$currency->setRegisterInArrayValidator(false);	
        $currency->setRequired(true);
        $currency->setAttrib('onchange', 'getCurrency()');
        $currency->addValidator('NotEmpty', false, array('messages' => 'Please select currency.'));
         
		$currency->addValidator(new Zend_Validate_Db_RecordExists(
										array('table' => 'main_currency',
                                        		'field' => 'id',
                                                'exclude'=>'isactive = 1',
										)));
		$currency->getValidator('Db_RecordExists')->setMessage('Selected currency is inactivated.');
		
        $expense_amount = new Zend_Form_Element_Text('expense_amount');
        $expense_amount->setAttrib('maxLength', 180);
        $expense_amount->setLabel("Expense amount");
		$expense_amount->setRequired(true);
		$expense_amount->addValidator('NotEmpty', false, array('messages' => 'Please enter amount.'));
        $expense_amount->addFilter(new Zend_Filter_StringTrim());
		$expense_amount->addValidator("regex",true,array(
						  'pattern'=>'/^[0-9]*\.?[0-9]+$/', 
                           'messages'=>array(
                               'regexNotMatch'=>'Please enter only numbers.'
                           )
        	));
			
			
		
		
		
		$is_reimbursable = new Zend_Form_Element_Radio('is_reimbursable');
		$is_reimbursable->setSeparator('');
		$is_reimbursable->setLabel("Reimbursable ");
		$is_reimbursable->setRequired(true);
		$is_reimbursable->setAttrib('onchange', 'getReimbursable()');
		//$is_reimbursable->setAttrib('class', 'with-gap');
		$is_reimbursable->setRegisterInArrayValidator(false);
		$is_reimbursable->addValidator('NotEmpty', false, array('messages' => 'Please select  reimbursable.'));
		$is_reimbursable->addMultiOption('1','Yes');
		$is_reimbursable->addMultiOption('0','No');
	
		$expense_payment_methods = new Zend_Form_Element_Select('expense_payment_id');
		$expense_payment_methods->setLabel("Payment Mode");
		$expense_payment_methods->addMultiOption('','Select Payment Mode');
		$expense_payment_methods->setRequired(true);
		$expense_payment_methods->addValidator('NotEmpty', false, array('messages' => 'Please select payment mode.'));
		$expense_payment_methods->setRegisterInArrayValidator(false);
		
		$expense_trips = new Zend_Form_Element_Select('trip_id');
		$expense_trips->setLabel("Add To Trip");
		$expense_trips->addMultiOption('','Select Trip');		
		$expense_trips->setRegisterInArrayValidator(false);
		
		$is_from_advance = new Zend_Form_Element_Select('is_from_advance');
		$is_from_advance->setLabel("Select Advance");
		//$is_from_advance->addMultiOption('','Select');		
		$is_from_advance->setRegisterInArrayValidator(false);
        			
		$payment_ref_no = new Zend_Form_Element_Text('expense_payment_ref_no');
        $payment_ref_no->setLabel("Payment Ref #");
        $payment_ref_no->setAttrib('maxLength', 50);
        $payment_ref_no->addFilter(new Zend_Filter_StringTrim());
        $payment_ref_no->setRequired(false);
        //$payment_ref_no->addValidator('NotEmpty', false, array('messages' => 'Please enter Payment Ref.'));  
		/* $payment_ref_no->addValidator("regex",true,array(                           
                           'pattern'=>'/^(?![0-9]*$)[a-zA-Z0-9.,&\(\)\/\-_\' ?]+$/',
                           'messages'=>array(
                               'regexNotMatch'=>'Please enter a valid Payment Ref #.'
                           )
        			)); */
					
		/* $description = new Zend_Form_Element_TextArea('description');
        $description->setLabel("Description");       
        $description->addFilter(new Zend_Filter_StringTrim()); */
		$description = new Zend_Form_Element_Textarea('description');
		$description->setLabel("Description"); 
        $description->setAttrib('rows', 10);
        $description->setAttrib('cols', 50);
		$description ->setAttrib('maxlength', '500');
		$description->addFilter(new Zend_Filter_StringTrim());
       
		
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('onclick', 'getAmount()');
		$submit->setAttrib('id', 'submitbutton');
		$submit->setLabel('Save');
		


		$this->addElements(array($id,$expense_name,$expense_date,$expense_amount,$base_project,$currency,$expense_category,$is_reimbursable,$expense_payment_methods,$expense_trips,$payment_ref_no,$is_from_advance,$description,$submit));
        $this->setElementDecorators(array('ViewHelper')); 
		 $this->setElementDecorators(array(
                    'UiWidgetElement',
        ),array('expense_date')); 
         
	}
	
}