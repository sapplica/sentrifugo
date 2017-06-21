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

class Default_Form_Appraisalgroups extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'appraisalgroups');

        $id = new Zend_Form_Element_Hidden('id');
		
		$appraisalgroup = new Zend_Form_Element_Text("group_name");
		$appraisalgroup->setLabel("Group");
		$appraisalgroup->setAttrib('maxLength', 30);
		$appraisalgroup->addFilter(new Zend_Filter_StringTrim());
		$appraisalgroup->setRequired(true);
        $appraisalgroup->addValidator('NotEmpty', false, array('messages' => 'Please enter group.'));
		$appraisalgroup->addValidator("regex",true,array(                           
                           'pattern'=>'/^[a-zA-Z0-9.\- ?]+$/',
                           'messages'=>array(
                               'regexNotMatch'=>'Please enter valid group.'
                           )
        	));
        $appraisalgroup->addValidator(new Zend_Validate_Db_NoRecordExists(
	                                            array(  'table'=>'main_pa_groups',
	                                                     'field'=>'group_name',
	                                                     'exclude'=>'id!="'.Zend_Controller_Front::getInstance()->getRequest()->getParam('id').'" AND isactive=1',    
	
	                                                      ) ) );
		$appraisalgroup->getValidator('Db_NoRecordExists')->setMessage('Group name already exists.');	

		$description = new Zend_Form_Element_Textarea('description');
		$description->setLabel("Description");
        $description->setAttrib('rows', 10);
        $description->setAttrib('cols', 50);
		$description ->setAttrib('maxlength', '200');

        $submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		$submit->setLabel('Save');

		 $this->addElements(array($id,$appraisalgroup,$description,$submit));
         $this->setElementDecorators(array('ViewHelper')); 
	}
}