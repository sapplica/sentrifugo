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

class Default_Form_Appraisalconfig extends Zend_Form
{
	public $bunitdata = array();
	
	public function __construct(array $bunitdata = array())  //calling constructer for data catching from controller 
	{
		$this->bunitdata = $bunitdata;
		parent::__construct($options = array());
	}
	public function init()
	{
		$this->setMethod('post');
		//$this->setAttrib('action',BASE_URL.'language/edit');
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'appraisalconfig');
		$id = new Zend_Form_Element_Hidden('id');
		$postid = Zend_Controller_Front::getInstance()->getRequest()->getParam('id');
		$businessunit_id = new Zend_Form_Element_Select('businessunit_id');
		$businessunit_id->setLabel("Business Unit");
        $businessunit_id->setAttrib('class', 'selectoption');
		if($postid == '')
		{
			$businessunit_id->setAttrib('onchange', 'displayDept(this)');
			$bunitdata = $this->bunitdata;
	    	 if(!empty($bunitdata))
	    	 {
				$businessunit_id->addMultiOptions(array(''=>'Select Business unit',
                                            '0'=>'No Business Unit'));
                
	    	    foreach ($bunitdata as $data){
				   $businessunit_id->addMultiOption($data['id'],$data['unitname']);
	    	     }
	    	 }
	    	 else 
	    	 {
	    	 	$businessunit_id->addMultiOptions(array(''=>'Select Business unit'));
	    	 	
	    	 }
		}else
		{
			$businessunit_id->addMultiOptions(array(''=>'Select Business unit'));
		}	
        $businessunit_id->setRegisterInArrayValidator(false);
        $businessunit_id->setRequired(true);
		$businessunit_id->addValidator('NotEmpty', false, array('messages' => 'Please select business unit.'));
		
		
		$department_id = new Zend_Form_Element_Select('department_id');
		$department_id->setLabel("Department");
        $department_id->setAttrib('class', 'selectoption');
        $department_id->addMultiOption('','Select Department');
        if($postid == '')
         $department_id->setAttrib('onchange', 'displayDept(this)');
        $department_id->setRegisterInArrayValidator(false);
		
		$performance_app_flag = new Zend_Form_Element_Radio('performance_app_flag');
		$performance_app_flag->setLabel("Applicability");
        $performance_app_flag->setAttrib('onclick', 'checkimplementfun(this)');
        $performance_app_flag->addMultiOptions(array(
										        '1' => 'Business unit wise',
										        '0' => 'Department wise',
    									   ));
		$performance_app_flag->setSeparator('');
		$performance_app_flag->setValue(1);    									   
		$performance_app_flag->setRegisterInArrayValidator(false);
		$performance_app_flag->setRequired(true);
		$performance_app_flag->addValidator('NotEmpty', false, array('messages' => 'Please select applicability.'));
		
		
		
		
		$appraisal_mode = new Zend_Form_Element_Select('appraisal_mode');
		$appraisal_mode->setLabel("Appraisal Mode");
        $appraisal_mode->setAttrib('class', 'selectoption');
        $appraisal_mode->addMultiOptions(array(''   => 'Select appraisal mode',
        										'Quarterly'  => 'Quarterly', 
        										'Half-yearly'	 => 'Half-yearly',
        										'Yearly'	 =>	'Yearly'   ));
		$appraisal_mode->setRegisterInArrayValidator(false);
		$appraisal_mode->setRequired(true);
		$appraisal_mode->addValidator('NotEmpty', false, array('messages' => 'Please select appraisal mode.'));
  
		$appraisal_ratings = new Zend_Form_Element_Select('appraisal_ratings');
		$appraisal_ratings->setLabel("Appraisal Ratings");
        $appraisal_ratings->setAttrib('class', 'selectoption');
        $appraisal_ratings->addMultiOptions(array('' => 'Select ratings',
        										'1'=> '1-5',
        										'2'=> '1-10'));
		$appraisal_ratings->setRegisterInArrayValidator(false);
		$appraisal_ratings->setRequired(true);
		$appraisal_ratings->addValidator('NotEmpty', false, array('messages' => 'Please select appraisal ratings.'));
	
        $submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		$submit->setLabel('Save');

		 $this->addElements(array($id,$businessunit_id,$performance_app_flag,$department_id,$appraisal_mode,$appraisal_ratings,$submit));
         $this->setElementDecorators(array('ViewHelper')); 
		
	}
}