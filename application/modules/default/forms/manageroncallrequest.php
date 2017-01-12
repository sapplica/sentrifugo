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

class Default_Form_manageroncallrequest extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'manageroncallrequest');


        $id = new Zend_Form_Element_Hidden('id');
			
		$appliedoncallsdaycount = new Zend_Form_Element_Text('appliedoncallsdaycount');
        $appliedoncallsdaycount->setAttrib('readonly', 'true');
		$appliedoncallsdaycount->setAttrib('onfocus', 'this.blur()');
		
		$employeename = new Zend_Form_Element_Text('employeename');
        $employeename->setAttrib('readonly', 'true');
		$employeename->setAttrib('onfocus', 'this.blur()');
		
		$managerstatus = new Zend_Form_Element_Select('managerstatus');
		$managerstatus->setLabel("Approve or Reject or Cancel");
        $managerstatus->setRegisterInArrayValidator(false);
        $managerstatus->setMultiOptions(array(							
							'1'=>'Approve' ,
							'2'=>'Reject',
        					'3'=>'Cancel',
							));
				
		$comments = new Zend_Form_Element_Textarea('comments');
		$comments->setLabel("Comments");
        $comments->setAttrib('rows', 10);
        $comments->setAttrib('cols', 50);
		$comments ->setAttrib('maxlength', '50');
							
		$oncalltypeid = new Zend_Form_Element_Select('oncalltypeid');
        $oncalltypeid->setAttrib('class', 'selectoption');
        $oncalltypeid->setRegisterInArrayValidator(false);
		$oncalltypeid->setAttrib('readonly', 'true');
		$oncalltypeid->setAttrib('onfocus', 'this.blur()');
               
        $oncallday = new Zend_Form_Element_Select('oncallday');
        $oncallday->setRegisterInArrayValidator(false);
        $oncallday->setMultiOptions(array(							
							'1'=>'Full Day' ,
							'2'=>'Half Day',
							));
		$oncallday->setAttrib('readonly', 'true');
        $oncallday->setAttrib('onfocus', 'this.blur()'); 		
							
        $from_date = new Zend_Form_Element_Text('from_date');
        $from_date->setAttrib('readonly', 'true');
		$from_date->setAttrib('onfocus', 'this.blur()');
        
        $to_date = new Zend_Form_Element_Text('to_date');
        $to_date->setAttrib('readonly', 'true'); 
        $to_date->setAttrib('onfocus', 'this.blur()');     		
		
		$reason = new Zend_Form_Element_Textarea('reason');
        $reason->setAttrib('rows', 10);
        $reason->setAttrib('cols', 50);
		$reason ->setAttrib('maxlength', '400');
		$reason->setAttrib('readonly', 'true');
		$reason->setAttrib('onfocus', 'this.blur()');
		
		$oncallstatus = new Zend_Form_Element_Text('oncallstatus');
        $oncallstatus->setAttrib('readonly', 'true');
		$oncallstatus->setAttrib('onfocus', 'this.blur()');
		
		$createddate = new Zend_Form_Element_Text('createddate');
        $createddate->setAttrib('readonly', 'true');
		$createddate->setAttrib('onfocus', 'this.blur()');

        $submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		$submit->setLabel('Save');

		$this->addElements(array($id,$employeename,$managerstatus,$comments,$reason,$oncallday,$from_date,$to_date,$oncalltypeid,$appliedoncallsdaycount,$oncallstatus,$createddate,$submit));
        $this->setElementDecorators(array('ViewHelper'));
      	 
	}
}