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

class Default_Form_currencyconverter extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'currencyconverter');


        $id = new Zend_Form_Element_Hidden('id');
	$id_val = Zend_Controller_Front::getInstance()->getRequest()->getParam('id');
		
		$basecurrency = new Zend_Form_Element_Select('basecurrency');
        $basecurrency->setAttrib('class', 'selectoption');
        $basecurrency->addMultiOption('','Select base currency');
        $basecurrency->setAttrib('onchange', 'displayTargetCurrency(this)');
        $basecurrency->setRegisterInArrayValidator(false);
        
        $basecurrency->setRequired(true);
		$basecurrency->addValidator('NotEmpty', false, array('messages' => 'Please select base currency.'));
		
		$targetcurrency = new Zend_Form_Element_Select('targetcurrency');
        $targetcurrency->setAttrib('class', 'selectoption');
		$targetcurrency->addMultiOption('','Select target currency');
        $targetcurrency->setRegisterInArrayValidator(false);
        $targetcurrency->setRequired(true);
		$targetcurrency->addValidator('NotEmpty', false, array('messages' => 'Please select target currency.'));
        if($id_val == '')
        {
            $targetcurrency->addValidator(new Zend_Validate_Db_NoRecordExists(
	                                            array(  'table'=>'main_currencyconverter',
	                                                     'field'=>'targetcurrency',
	                                                     'exclude'=>'basecurrency="'.Zend_Controller_Front::getInstance()->getRequest()->getParam('basecurrency').'" AND targetcurrency="'.Zend_Controller_Front::getInstance()->getRequest()->getParam('targetcurrency').'" and isactive=1',    
	
	                                                      ) ) );
            $targetcurrency->getValidator('Db_NoRecordExists')->setMessage('Currency combination already exists.');	
        }	
		
		$exchangerate = new Zend_Form_Element_Text("exchangerate");
		$exchangerate->setAttrib('maxLength', 15);
		$exchangerate->addFilter(new Zend_Filter_StringTrim());
		$exchangerate->setRequired(true);
        $exchangerate->addValidator('NotEmpty', false, array('messages' => 'Please enter exchange rate.'));
		
		$exchangerate->addValidator("regex", false, array("/^[0-9]+(\.[0-9]{1,6})?$/","messages"=>"Please enter valid exchange rate."));


		
		

        $start_date = new ZendX_JQuery_Form_Element_DatePicker('start_date');
		$start_date->setAttrib('readonly', 'true');
		$start_date->setAttrib('onfocus', 'this.blur()');
		
		$start_date->setOptions(array('class' => 'brdr_none'));	
		$start_date->setRequired(true);
        $start_date->addValidator('NotEmpty', false, array('messages' => 'Please select start date.'));

	    $end_date = new ZendX_JQuery_Form_Element_DatePicker('end_date');
		$end_date->setAttrib('readonly', 'true');
		$end_date->setAttrib('onfocus', 'this.blur()');
		
		$end_date->setOptions(array('class' => 'brdr_none'));	
		$end_date->setRequired(true);
        $end_date->addValidator('NotEmpty', false, array('messages' => 'Please select end date.'));		
   	
		$description = new Zend_Form_Element_Textarea('description');
        $description->setAttrib('rows', 10);
        $description->setAttrib('cols', 50);
		$description ->setAttrib('maxlength', '200');

        $submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		$submit->setLabel('Save');

		 $this->addElements(array($id,$basecurrency,$targetcurrency,$exchangerate,$start_date,$end_date,$description,$submit));
         $this->setElementDecorators(array('ViewHelper')); 
		 $this->setElementDecorators(array(
                    'UiWidgetElement',
        ),array('start_date','end_date'));
	}
}