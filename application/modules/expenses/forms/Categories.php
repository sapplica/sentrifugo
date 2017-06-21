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

class Expenses_Form_Categories extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('action',BASE_URL.'expenses/expensecategories/edit');
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'Categories');


        $id = new Zend_Form_Element_Hidden('id');
		
		$expense_category_name = new Zend_Form_Element_Text('expense_category_name');
        $expense_category_name ->setAttrib('maxLength', 20);
        
       $expense_category_name ->addFilter(new Zend_Filter_StringTrim());
	   $expense_category_name->setRequired(true);
       $expense_category_name ->addValidator('NotEmpty', false, array('messages' => 'Please enter Category Name.'));  
        
	   $expense_category_name ->addValidator("regex",true,array(
                           'pattern'=>'/^([a-zA-Z0-9_@.]+ ?)+$/', 
        
                           'messages'=>array(
                               'regexNotMatch'=>'Enter valid Category Name.'
                           )
        	));	

		$expense_category_name->addValidator(new Zend_Validate_Db_NoRecordExists(
                                                        array('table' => 'expense_categories',
                                                        'field' => 'expense_category_name',
                                                        'exclude'=>'id!="'.Zend_Controller_Front::getInstance()->getRequest()->getParam('id',0).'" and isactive=1',
                                                        )));
        $expense_category_name->getValidator('Db_NoRecordExists')->setMessage('Category already  exist.');
		
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		$submit->setLabel('Save');
		
		$this->addElements(array($id,$expense_category_name,$submit));
		
		
        $this->setElementDecorators(array('ViewHelper')); 
	}
}
?>