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

class Default_Form_managerleaverequest extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'managerleaverequest');


        $id = new Zend_Form_Element_Hidden('id');
			
		$appliedleavesdaycount = new Zend_Form_Element_Text('appliedleavesdaycount');
        $appliedleavesdaycount->setAttrib('readonly', 'true');
		$appliedleavesdaycount->setAttrib('onfocus', 'this.blur()');
		
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
							
		$leavetypeid = new Zend_Form_Element_Select('leavetypeid');
        $leavetypeid->setAttrib('class', 'selectoption');
        $leavetypeid->setRegisterInArrayValidator(false);
		$leavetypeid->setAttrib('readonly', 'true');
		$leavetypeid->setAttrib('onfocus', 'this.blur()');
               
        $leaveday = new Zend_Form_Element_Select('leaveday');
        $leaveday->setRegisterInArrayValidator(false);
        $leaveday->setMultiOptions(array(							
							'1'=>'Full Day' ,
							'2'=>'Half Day',
							));
		$leaveday->setAttrib('readonly', 'true');
        $leaveday->setAttrib('onfocus', 'this.blur()'); 		
							
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
		
		$leavestatus = new Zend_Form_Element_Text('leavestatus');
        $leavestatus->setAttrib('readonly', 'true');
		$leavestatus->setAttrib('onfocus', 'this.blur()');
		
		$createddate = new Zend_Form_Element_Text('createddate');
        $createddate->setAttrib('readonly', 'true');
		$createddate->setAttrib('onfocus', 'this.blur()');

        $submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		$submit->setLabel('Save');

		$this->addElements(array($id,$employeename,$managerstatus,$comments,$reason,$leaveday,$from_date,$to_date,$leavetypeid,$appliedleavesdaycount,$leavestatus,$createddate,$submit));
        $this->setElementDecorators(array('ViewHelper'));
      	 
	}
}