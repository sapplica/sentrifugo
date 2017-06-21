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
 * This gives employee report form.
 */
class Default_Form_Candidatesreport extends Zend_Form{
    public function init(){
        $this->setMethod('post');
        $this->setAttrib('id', 'formid');
        $this->setAttrib('name', 'frm_requisition_report');

        $requisition_code = new Zend_Form_Element_Text("requisition_code");
        $requisition_code->setLabel("Requisition Code");
		$requisition_code->setAttrib('name', '');
        $requisition_code->setAttrib('id', 'idrequisition_code');
		
        $cand_status = new Zend_Form_Element_Select("cand_status");
        $cand_status->setRegisterInArrayValidator(false);
        $cand_status->addMultiOptions(
        	array(
        		'' => 'Select Candidate Status',
	        	'Shortlisted' => 'Shortlisted',
	        	'Selected' => 'Selected',
	        	'Rejected' => 'Rejected',
	        	'On hold' => 'On hold',
	        	'Disqualified' => 'Disqualified',
	        	'Scheduled' => 'Scheduled',
	        	'Not Scheduled' => 'Not Scheduled',
	        	'Recruited' => 'Recruited',
	        	'Requisition Closed/Completed' => 'Requisition Closed/Completed'
        	)
        );
        $cand_status->setLabel("Candidate Status");
        $cand_status->setAttrib('title', 'Candidate Status');
        $cand_status->setAttrib('id', 'idcand_status');
        
        $submit = new Zend_Form_Element_Button('submit');        
        $submit->setAttrib('id', 'idsubmitbutton');
        $submit->setLabel('Report'); 
        
        $this->addElements(array($requisition_code, $cand_status, $submit));
        $this->setElementDecorators(array('ViewHelper'));
    }
}