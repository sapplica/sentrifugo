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

class Expenses_Form_Paymentmode extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('action',BASE_URL.'expenses/paymentmode/edit');
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'paymentmode');


        $id = new Zend_Form_Element_Hidden('id');
		
		$payment_method_name = new Zend_Form_Element_Text('payment_method_name');
        $payment_method_name ->setAttrib('maxLength', 20);
        
       $payment_method_name ->addFilter(new Zend_Filter_StringTrim());
	   $payment_method_name ->setRequired(true);
       $payment_method_name ->addValidator('NotEmpty', false, array('messages' => 'Please enter Payment Mode.'));  
        
	   $payment_method_name ->addValidator("regex",true,array(

                           'pattern'=>'/^([a-zA-Z0-9_@.]+ ?)+$/', 
        
                           'messages'=>array(
                               'regexNotMatch'=>'Enter valid Payment Mode.'
                           )
        	));	
		
		$payment_method_name->addValidator(new Zend_Validate_Db_NoRecordExists(
                                                        array('table' => 'expense_payment_methods',
                                                        'field' => 'payment_method_name',
                                                        'exclude'=>'id!="'.Zend_Controller_Front::getInstance()->getRequest()->getParam('id',0).'" and isactive=1',
                                                        )));
        $payment_method_name->getValidator('Db_NoRecordExists')->setMessage('Payment mode already  exist.');
		 
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		$submit->setLabel('Save');
		
		$this->addElements(array($id,$payment_method_name ,$submit));
		
		
        $this->setElementDecorators(array('ViewHelper')); 
	}
}
?>