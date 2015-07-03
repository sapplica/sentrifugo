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
 * This gives candidate details multiple resume upload form.
 */
class Default_Form_Multipleresume extends Zend_Form
{
    public function init()
    {
        $this->setMethod('post');
        $this->setAttrib('id', 'frm_multiple_resume');
        $this->setAttrib('name', 'frm_multiple_resume');
        $this->setAttrib('action', BASE_URL.'candidatedetails/multipleresume');

        $id = new Zend_Form_Element_Hidden('id');
        $id_val = Zend_Controller_Front::getInstance()->getRequest()->getParam('id',null);
        $requisition_id = new Zend_Form_Element_Select("requisition_id");
        $requisition_id->setRegisterInArrayValidator(false);
        $requisition_id->setLabel("Requisition ID");		
        $requisition_id->setAttrib("class", "formDataElement");
		$requisition_id->setAttrib('onchange', 'displayParticularCandidates(this,"cand")');
        $requisition_id->setAttrib('title', 'Requisition ID');
		
        if($id_val == '')
        {
            $requisition_id->setRequired(true);
            $requisition_id->addValidator('NotEmpty', false, array('messages' => 'Please select requisition id.')); 
        }
                       
        $candidate_firstname = new Zend_Form_Element_Text('candidate_firstname');
        $candidate_firstname->setIsArray(TRUE);
        $candidate_firstname->setAttrib('maxLength', 90);
        $candidate_firstname->setAttrib('title', 'Candidate First Name');
        $candidate_firstname->setAttrib('class', 'candidate_firstname');
        $candidate_firstname->addFilter(new Zend_Filter_StringTrim());
        $candidate_firstname->setRequired(true);
        $candidate_firstname->addValidator("regex",true,array(                           
                           'pattern'=>'/^[a-zA-Z.\- ?]+$/',
                           'messages'=>array(
                               'regexNotMatch'=>'Please enter valid candidate first name.'
                           )
        				));
        $candidate_lastname = new Zend_Form_Element_Text('candidate_lastname');
        $candidate_lastname->setIsArray(TRUE);
        $candidate_lastname->setAttrib('maxLength', 90);
        $candidate_lastname->setAttrib('title', 'Candidate Last Name');
        $candidate_lastname->setAttrib('class', 'candidate_lastname');
        $candidate_lastname->addFilter(new Zend_Filter_StringTrim());
        $candidate_lastname->setRequired(true);
        $candidate_lastname->addValidator("regex",true,array(                           
                           'pattern'=>'/^[a-zA-Z.\- ?]+$/',
                           'messages'=>array(
                               'regexNotMatch'=>'Please enter valid candidate last name.'
                           )
        				));				
                       
        $candidate_resumes = new Zend_Form_Element_Hidden('cand_resume');
        $candidate_resumes->setIsArray(TRUE);
        $candidate_resumes->setRequired(true);

        $candidate_resumes->addValidator('NotEmpty', false, array('messages' => 'Please select file.'));  
        	
		
        $cand_status = new Zend_Form_Element_Select("cand_status");
        $cand_status->setRegisterInArrayValidator(false);
        
        $cand_status->setLabel("Status");		
        $cand_status->setAttrib("class", "formDataElement");         
        $cand_status->setAttrib('title', 'Candidate status');
                        
        $submit = new Zend_Form_Element_Submit('submit');       
        $submit->setAttrib('id', 'multiple-submit-button');
        $submit->setLabel('Save'); 

		
        $this->addElements(array($cand_status,$id,$requisition_id,$candidate_firstname,$candidate_lastname,$candidate_resumes,$submit));
        $this->setElementDecorators(array('ViewHelper')); 
    }
}