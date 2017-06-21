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
 * Create or Edit Clients Form.
 * @author Sagarsoft
 *
 */
class Assets_Form_AssetCategories extends Zend_Form
{
	public function init()
	{
		
		
		$this->setMethod('post');
		$this->setAttrib('action',BASE_URL.'assets/assetcategories/edit');
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'addOrEditassetcategories');


        $id = new Zend_Form_Element_Hidden('id');
        
        
        
        $assetcategoryname = new Zend_Form_Element_Text('name');
        $assetcategoryname->setLabel("Category");
        $assetcategoryname->setAttrib('maxLength', 20);
        $assetcategoryname->addFilter(new Zend_Filter_StringTrim());
        $assetcategoryname->setRequired(TRUE);
        $assetcategoryname->setAttrib('placeholder', 'Enter Category Name');
        $assetcategoryname->addValidator('NotEmpty', false, array('messages' => 'Please enter asset category.'));  
		$assetcategoryname->addValidator("regex",true,array(                           
                           'pattern'=>'/^(?![0-9]*$)[a-zA-Z0-9.,&\(\)\/\-_\' ?]+$/',
                           'messages'=>array(
                               'regexNotMatch'=>'Please enter a valid asset category.'
                           )
        			));
		
		$assetcategoryname->addValidator(new Zend_Validate_Db_NoRecordExists(
	                                            array(  'table'=>'assets_categories',
	                                                     'field'=>'name',
	                                            		'exclude'=>'id!="'.Zend_Controller_Front::getInstance()->getRequest()->getParam('id').'" and is_active=1 and parent=0',
	                                            		 
	
	                                                      ) ) );
		$assetcategoryname->getValidator('Db_NoRecordExists')->setMessage('category already exists.');
	
			
		
	
		$subcatfield = new Zend_Form_Element_Text('subcatfield');
        $subcatfield->setLabel("sub Category");
		$subcatfield->setAttrib('maxLength', 20);
		$subcatfield->setAttrib('placeholder', 'Enter Sub Category');
		$subcatfield->setAttrib('onkeyup', 'checkvalidity();');
        $subcatfield->addFilter(new Zend_Filter_StringTrim());
        $subcatfield->addValidator("regex",true,array(
        		'pattern'=>'/^(?![0-9]*$)[a-zA-Z0-9.,&\(\)\/\-_\' ?]+$/',
        		'messages'=>array(
        				'regexNotMatch'=>'Please enter a valid sub category name.'
        		)
        ));
      /*   $subcatfield->addValidator(new Zend_Validate_Db_NoRecordExists(
        		array(  'table'=>'assets_categories',
        				'field'=>'name',
        				'exclude'=>'id!="'.Zend_Controller_Front::getInstance()->getRequest()->getParam('subcatfield').'" and is_active=1',
        
        
        		) ) );
        $assetcategoryname->getValidator('Db_NoRecordExists')->setMessage('sub Category already exists.');
         */
        
        
      /*   $add_parent = new Zend_Form_Element_Text('add_parent');
        $add_parent->setLabel("Sub Category");
        $add_parent->setAttrib('maxLength', 50);
        $add_parent->addFilter(new Zend_Filter_StringTrim());      
        $add_parent->setAttrib('placeholder', 'Enter Sub Category'); */
		
            
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		$submit->setLabel('Save');

		$this->addElements(array($id,$assetcategoryname,$subcatfield,$submit));
        $this->setElementDecorators(array('ViewHelper')); 
         
	}
	
}