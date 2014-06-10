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

class Default_Form_timezone extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('action',DOMAIN.'timezone/edit');
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'timezone');


        $id = new Zend_Form_Element_Hidden('id');
		
		/*$timezone = new Zend_Form_Element_Text('timezone');
        $timezone->setAttrib('maxLength', 50);
        //$timezone->setAttrib('onblur', 'checkspecialcharactersformember(this.value,this.id);');
        //$timezone->addFilter(new Zend_Filter_StringTrim());
        $timezone->setRequired(true);
        $timezone->addValidator('NotEmpty', false, array('messages' => 'Please enter time zone.'));  
        $timezone->addValidators(array(
						 array(
							 'validator'   => 'Regex',
							 'breakChainOnFailure' => true,
							 'options'     => array( 
							 'pattern'=> '/^(?=.*[a-zA-Z])([^ ][a-zA-Z0-9 ]*)$/',
								 'messages' => array(
										 'regexNotMatch'=>'Please enter valid time zone.'
								 )
							 )
						 )
					 ));
		$timezone->addValidator(new Zend_Validate_Db_NoRecordExists(
                                              array('table'=>'main_timezone',
                                                        'field'=>'timezone',
                                                      'exclude'=>'id!="'.Zend_Controller_Front::getInstance()->getRequest()->getParam('id').'" and isactive=1',    
                                                 ) )  
                                    );*/
		
		$timezone = new Zend_Form_Element_Multiselect('timezone');
       // $state->setAttrib('class', 'selectoption');		
        $timezone->setRegisterInArrayValidator(false);
        //$state->addMultiOption('','Select State');
        $timezone->setRequired(true);
		$timezone->addValidator('NotEmpty', false, array('messages' => 'Please select time zone.'));
		$timezoneModal = new Default_Model_Timezone();
	    	$timezoneData = $timezoneModal->getalltimezones();
			foreach ($timezoneData as $data){
		$timezone->addMultiOption($data['id'],$data['timezone'].' ['.$data['timezone_abbr'].']');
	    	}
		
		
		$description = new Zend_Form_Element_Textarea('description');
        $description->setAttrib('rows', 10);
        $description->setAttrib('cols', 50);
		$description ->setAttrib('maxlength', '200');
		//$description->setAttribs(array('style' => 'resize:none;overflow:auto;border:none;'));

        $submit = new Zend_Form_Element_Submit('submit');
		// $submit->setLabel('Upload File')
		 $submit->setAttrib('id', 'submitbutton');
		 $submit->setLabel('Save');

		$url = "'timezone/saveupdate/format/json'";
		$dialogMsg = "''";
		$toggleDivId = "''";
		$jsFunction = "'redirecttocontroller(\'timezone\');'";;
		 

		 //$submit->setOptions(array('onclick' => "saveDetails($url,$dialogMsg,$toggleDivId,$jsFunction);"
		//));

		 $this->addElements(array($id,$timezone,$description,$submit));
         $this->setElementDecorators(array('ViewHelper')); 
	}
}