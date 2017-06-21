<?php
/********************************************************************************* 
 *  This file is part of Sentrifugo.
 *  Copyright (C) 2015 Sapplica
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

class Default_Form_Categories extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('id','pdcategories');
		$this->setAttrib('name','pdcategories');

		$categoryName = new Zend_Form_Element_Text('category');
		$categoryName->setAttrib('id','category');
		$categoryName->setAttrib('name','category');
		$categoryName->setAttrib('maxlength','30');
		$categoryName->setAttrib('onblur','chkCategory()');
		$categoryName->setAttrib('onkeypress','chkCategory()');
		$categoryName->addFilter(new Zend_Filter_StringTrim());
		$categoryName->setRequired(true);
		$categoryName->addValidator('NotEmpty',false,array("messages"=>'Please enter category'));
		$categoryName->addValidator('regex',true,array(
			'pattern'=>'/^[a-zA-Z0-9][\s+[a-zA-Z0-9]+]*$/', //'/^[a-zA-Z][a-zA-Z0-9\s]*$/', 	/^[a-z0-9 ]+$/i						
			'messages'=>array('regexNotMatch'=>'Please enter valid category')
		));
		$categoryName->addValidator(new Zend_Validate_Db_NoRecordExists(
			array(
				'table' => 'main_pd_categories',
				'field' => 'category',
				'exclude' => 'id!="'.Zend_Controller_Front::getInstance()->getRequest()->getParam('id').'" and isactive = 1'
			)	
		));
		$categoryName->getValidator('Db_NoRecordExists')->setMessage('Category already exists');

		$categoryDesc = new Zend_Form_Element_Textarea('description');
		$categoryDesc->setAttrib('id','description');
		$categoryDesc->setAttrib('name','description');
		$categoryDesc->setAttrib('rows',10);
		$categoryDesc->setAttrib('cols',50);
		$categoryDesc->setAttrib('maxlength',250);

		$submitBtn = new Zend_Form_Element_Submit('submit');
		$submitBtn->setAttrib('id','submitBtn');
		$submitBtn->setLabel('Save');

		$this->addElements(array($categoryName,$categoryDesc,$submitBtn));
		$this->setElementDecorators(array('ViewHelper'));
	}
}
?>