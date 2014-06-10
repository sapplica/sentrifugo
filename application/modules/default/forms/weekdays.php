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

class Default_Form_weekdays extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('action',DOMAIN.'weekdays/edit');
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'weekdays');


        $id = new Zend_Form_Element_Hidden('id');
		
		/*$dayname = new Zend_Form_Element_Text('day_name');
        $dayname->setAttrib('maxLength', 20);
        //$dayname->setAttrib('onblur', 'checkspecialcharactersformember(this.value,this.id);');
        $dayname->addFilter(new Zend_Filter_StringTrim());
        $dayname->setRequired(true);
        $dayname->addValidator('NotEmpty', false, array('messages' => 'Please enter Day name.'));  
        $dayname->addValidator("regex",true,array(
                           'pattern'=>'/^[a-zA-Z][a-zA-Z0-9\s\[\]\.\-#$@&_*()]*$/', 
                          // 'pattern'=>'/^[a-zA-Z][^(!~^?%`)]+$/',
                           'messages'=>array(
                               'regexNotMatch'=>'Please input Alphanumeric Value.'
                           )
        	));*/
		$dayname = new Zend_Form_Element_Select('day_name');
        $dayname->setAttrib('class', 'selectoption');
        $dayname->setRegisterInArrayValidator(false);
        $dayname->setRequired(true);
		$dayname->addValidator('NotEmpty', false, array('messages' => 'Please select day name.'));	
			
		$dayshortcode = new Zend_Form_Element_Text('dayshortcode');
        $dayshortcode->setAttrib('maxLength', 20);
        //$dayname->setAttrib('onblur', 'checkspecialcharactersformember(this.value,this.id);');
        $dayshortcode->addFilter(new Zend_Filter_StringTrim());
		$dayshortcode->addValidator(new Zend_Validate_Db_NoRecordExists(
                                              array('table'=>'main_weekdays',
                                                        'field'=>'dayshortcode',
                                                      'exclude'=>'id!="'.Zend_Controller_Front::getInstance()->getRequest()->getParam('id').'" and isactive=1',    
                                                 ) )  
                                    );
        $dayshortcode->getValidator('Db_NoRecordExists')->setMessage('Day short code already exists.'); 	
		
		$daylongcode = new Zend_Form_Element_Text('daylongcode');
        $daylongcode->setAttrib('maxLength', 20);
        //$dayname->setAttrib('onblur', 'checkspecialcharactersformember(this.value,this.id);');
        $daylongcode->addFilter(new Zend_Filter_StringTrim());
		
        $daylongcode->addValidator(new Zend_Validate_Db_NoRecordExists(
                                              array('table'=>'main_weekdays',
                                                        'field'=>'daylongcode',
                                                      'exclude'=>'id!="'.Zend_Controller_Front::getInstance()->getRequest()->getParam('id').'" and isactive=1',    
                                                 ) )  
                                    );
        $daylongcode->getValidator('Db_NoRecordExists')->setMessage('Day long code already exists.'); 		
			
		$description = new Zend_Form_Element_Textarea('description');
        $description->setAttrib('rows', 10);
        $description->setAttrib('cols', 50);
		$description ->setAttrib('maxlength', '200');
		//$description->setAttribs(array('style' => 'resize:none;overflow:auto;border:none;'));

        $submit = new Zend_Form_Element_Submit('submit');
		// $submit->setLabel('Upload File')
		 $submit->setAttrib('id', 'submitbutton');
		 $submit->setLabel('Save');

		$url = "'weekdays/saveupdate/format/json'";
		$dialogMsg = "''";
		$toggleDivId = "''";
		$jsFunction = "'redirecttocontroller(\'weekdays\');'";;
		 

		 //$submit->setOptions(array('onclick' => "saveDetails($url,$dialogMsg,$toggleDivId,$jsFunction);"
		//));

		 $this->addElements(array($id,$dayname,$dayshortcode,$daylongcode,$description,$submit));
         $this->setElementDecorators(array('ViewHelper')); 
	}
}