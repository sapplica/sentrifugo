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
class Default_Form_Requisitionsstatusreport extends Zend_Form{
    public function init(){
        $this->setMethod('post');
        $this->setAttrib('id', 'formid');
        $this->setAttrib('name', 'frm_requisition_report');

        $raised_by = new Zend_Form_Element_Text("raised_by");
        $raised_by->setLabel("Raised By");
        $raised_by->setAttrib('name','');
        $raised_by->setAttrib('id', 'idraised_by');
        $raised_by->setAttrib('title', 'Raised By');  
        
        $requisition_status = new Zend_Form_Element_Select("req_status");      
        $requisition_status->setLabel("Requisition Status");
        $requisition_status->addMultiOptions(
	        array(''=>'Select Requisition Status',
				'Initiated'=>'Initiated',
				'Approved'=>'Approved',
				'Rejected'=>'Rejected',
				'Closed'=>'Closed',
				'On hold'=>'On hold',
				'Complete'=>'Complete',
				'In process'=>'In process'	        
	        )
        );
        
        $requisition_status->setAttrib('title', 'Requisition Status');

        $raised_in = new Zend_Form_Element_Select('createdon');
        $raised_in->setLabel('Raised In');
        $raised_in->setAttrib('id', 'createdon');
        
        $reporting_manager = new Zend_Form_Element_Text("reporting_manager");        
        $reporting_manager->setLabel("Reporting Manager");
        $reporting_manager->setAttrib('name', '');
        $reporting_manager->setAttrib('id', 'idreporting_manager');
        
        $job_title = new Zend_Form_Element_Select("jobtitle");      
        $job_title->setLabel("Job Title");
        $job_title->setAttrib("onchange", "getpositions_req('department','business_unit','position_id','jobtitle');");
        $job_title->setAttrib('title', 'Job Title.');  
        
        $submit = new Zend_Form_Element_Button('submit');        
        $submit->setAttrib('id', 'idsubmitbutton');
        $submit->setLabel('Report'); 
        
        $this->addElements(array($raised_by, $requisition_status, $raised_in, $reporting_manager, $job_title, $submit));
        $this->setElementDecorators(array('ViewHelper')); 
    }
}