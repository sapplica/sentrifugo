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

class Timemanagement_Form_Expensecategory extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('action',BASE_URL.'timemanagement/expensecategory/edit');
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'expensecategory');


        $id = new Zend_Form_Element_Hidden('id');
		
		$category = new Zend_Form_Element_Text('expense_category');
        $category->setAttrib('maxLength', 200);
        $category->setLabel("Category");
        
        $category->setRequired(true);
        $category->addValidator('NotEmpty', false, array('messages' => 'Please enter Category.'));
		$category->addValidator("regex",true,array(
									'pattern'=> '/^(?=.*[a-zA-Z])([^ ][a-zA-Z0-9 ]*)$/',
								    'messages'=>array(
									     'regexNotMatch'=>'Please enter valid Category.'
								     )
					       ));	
        $category->addValidator(new Zend_Validate_Db_NoRecordExists(
                                              array('table'=>'tm_expense_categories',
                                                     'field'=>'expense_category',
                                                     'exclude'=>'id!="'.Zend_Controller_Front::getInstance()->getRequest()->getParam('id').'" and is_active=1',    
                                                 ) )  
                                    );
        $category->getValidator('Db_NoRecordExists')->setMessage('Category already exists.');	
        	
       //http://stackoverflow.com/questions/9299012/using-for-decimals-in-zend-validator-float
        $unitPrice = new Zend_Form_Element_Text('unit_price');
        $unitPrice->setAttrib('maxLength', 7);
        $unitPrice->setLabel("Unit Price");
		$unitPrice->addValidator("regex",true,array(
									'pattern'=> '/^[1-9]+(\.\d{1,2})?$/',
								    'messages'=>array(
									     'regexNotMatch'=>'Please enter valid price.'
								     )
					       ));        
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		$submit->setLabel('Save');

		 $this->addElements(array($id,$category,$unitPrice,$submit));
         $this->setElementDecorators(array('ViewHelper')); 
	}
}