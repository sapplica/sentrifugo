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
 * 
 * Create or Edit Assets Form.
 * @author Sagarsoft
 *
 */
class Assets_Form_Assets extends Zend_Form
{
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('action',BASE_URL.'assets/assets/edit');
		$this->setAttrib('id', 'formid');
		$this->setAttrib('name', 'addOrEditAssets');
        
		$id = new Zend_Form_Element_Hidden('id');          
        
		
		$category = new Zend_Form_Element_Select('category');
		$category->setLabel('Category');
		$category->setRequired(TRUE);
		$category->addMultiOption('','Select Asset Category');
		$category->setAttrib('onchange', 'getSubCategories();');
		$category->addValidator('NotEmpty', true, array('messages' => 'Please select Asset category.')); 
		$AssetCategoriesModel = new Assets_Model_AssetCategories();	
		$catData = $AssetCategoriesModel->fetchAll('is_active=1 && parent = 0','name');
		foreach ($catData as $cat){
			$category->addMultiOption($cat['id'],utf8_encode($cat['name']));
	    }
		$category->setRegisterInArrayValidator(false);
      
		
		
		$sub_category = new Zend_Form_Element_Select('sub_category');
		$sub_category->setLabel('Sub Category');
		$sub_category->addMultiOption('','Select Sub Category');
	    $sub_category->setRegisterInArrayValidator(false);
        $sub_category->setRequired(false);
		
		$company_asset_code = new Zend_Form_Element_Text('company_asset_code');
		$company_asset_code->setLabel('Company Asset Code');
        $company_asset_code->addFilter(new Zend_Filter_StringTrim());
		$company_asset_code->setAttrib('maxLength', 50);
		$company_asset_code->addValidator('NotEmpty', true, array('messages' => 'Please enter Company asset code.')); 
		$company_asset_code->addValidator("regex",true,array(                           
						'pattern'=>'/^[0-9]+$/',
                           'messages'=>array(
                               'regexNotMatch'=>'Enter numbers only.'
                           )
        			));
		$company_asset_code->setRequired(TRUE);	
		

        $name = new Zend_Form_Element_Text('name');
        $name->setLabel("Asset Name");   
		$name->setAttrib('maxLength', 20);
        $name->addFilter(new Zend_Filter_StringTrim());
		$name->addValidator('NotEmpty', false, array('messages' => 'Please enter Asset name.'));
		$name->addValidator("regex",true,array(                           
                          'pattern'=>'/^(?![0-9]*$)[a-zA-Z0-9.,&\(\)\/\-_\' ?]+$/',
                           'messages'=>array(
                               'regexNotMatch'=>'Enter valid Asset name.'
                           )
        			));	
		$name->setRequired(TRUE);
		$name->addValidator(new Zend_Validate_Db_NoRecordExists(
				array(  'table'=>'assets',
						'field'=>'name',
						'exclude'=>'id!="'.Zend_Controller_Front::getInstance()->getRequest()->getParam('id').'" and isactive=1',
				) ) );
		$name->getValidator('Db_NoRecordExists')->setMessage('Name already exists.');
		
	
		
        $location = new Zend_Form_Element_Select('location');
        $location->setLabel('Business Unit');
        $location->setAttrib('onchange', 'displayempnames(this.value);');
        $locationModel = new Assets_Model_Assets();
		$locData = $locationModel->getLocation();
		$location->addMultiOption('','Select Business Unit');
		foreach ($locData as $data){
			$location->addMultiOption($data['id'],utf8_encode($data['unitname']));
			//$location->addMultiOption($data['id'],utf8_encode($data['address1']));
        }
		$location->setRequired(true);
		$location->addValidator('NotEmpty', false, array('messages' => 'Please select Business unit.'));
		
		
		
		$allocated_to = new Zend_Form_Element_Select('allocated_to');
		$allocated_to->setLabel('Allocate To');	
		$allocated_to->setAttrib('onchange', 'getName();');
	    $allocated_to->addMultiOption('','Select Allocate to');
        //$allocated_to->setAttrib('class', 'selectoption');
        $allocated_to->setRegisterInArrayValidator(false);
    
        
		$responsible_technician = new Zend_Form_Element_Select('responsible_technician');
		$responsible_technician->setLabel('Responsible Techinician');

		$userModel = new Default_Model_Users();
	    $userData = $userModel->fetchAll('isactive=1','userfullname');
	    $responsible_technician->addMultiOption('','responsible_technician');
	    foreach ($userData->toArray() as $data){
			$responsible_technician->addMultiOption($data['id'],utf8_encode($data['userfullname']));
	    }

		
        $responsible_technician->setAttrib('class', 'selectoption');
		$responsible_technician->setLabel('Responsible Technician');
        $responsible_technician->setRegisterInArrayValidator(false);
		$responsible_technician->addMultiOption('',' Select Responsible Technician');
        $responsible_technician->setRequired(FALSE);
		
		
		/* $vendor = new Zend_Form_Element_Select('vendor');
		//Accessing Vendors Data From Employee ModelDefault.
        $vendor->setLabel('Vendor');
		$assetModel = new Assets_Model_Assets();
		$vendorName = $assetModel->getvendorsname();
		$vendor->addMultiOption('',' Select Vendor');
		foreach($vendorName as $vendorN)
			{
				$vendor->addMultiOption($vendorN['id'],utf8_encode($vendorN['name']));
			}		
        $vendor->setRequired(FALSE); */
	
		
		$asset_classification = new Zend_Form_Element_Select('asset_classification');
        $asset_classification->setLabel('Asset Classification ')	
				->addMultiOptions(array(''=>'Select Classification ',
														'Department' => 'Department',
													    'Business Unit' => 'Business Unit',
													    'Employee' => 'Employee',
														 ));
	   $asset_classification->addValidator('NotEmpty', true, array('messages' => 'Please select Asset classification.'));
		$asset_classification->setRequired(TRUE);
														 
														 
                 
		$purchase_date = new ZendX_JQuery_Form_Element_DatePicker('purchase_date');
		$purchase_date->setLabel('Purchase Date');
		$purchase_date->setOptions(array('class' => 'brdr_none'));
        //$date_of_leaving->setAttrib('onchange', 'validatejoiningdate(this)');  		
		$purchase_date->setAttrib('readonly', 'true');
		$purchase_date->setAttrib('onfocus', 'this.blur()');
		
			
		
		$invoice_number = new Zend_Form_Element_Text('invoice_number');
        $invoice_number->setAttrib('maxLength', 50);
        $invoice_number->setLabel("Invoice Number");
        $invoice_number->addFilter(new Zend_Filter_StringTrim());
		$invoice_number->addValidator("regex",true,array(                           
                          'pattern'=>'/^[0-9a-zA-Z]+\d+[a-zA-Z0-9.,&\(\)\/\-_\' ?]+$/',
                           'messages'=>array(
                               'regexNotMatch'=>'Enter Valid Invoice Number.'
                           )
        			));
		
		$manufacturer= new Zend_Form_Element_Text('$manufacturer');
        $manufacturer->setAttrib('maxLength', 50);
        $manufacturer->setLabel("Manufacturer");
        $manufacturer->addFilter(new Zend_Filter_StringTrim());
		$manufacturer->addValidator("regex",true,array(                           
                          'pattern'=>'/^(?![0-9]*$)[a-zA-Z0-9.,&\(\)\/\-_\' ?]+$/',
                           'messages'=>array(
                               'regexNotMatch'=>'Enter Valid Manufacturer name.'
                           )
        			));
		
		
		$key_number = new Zend_Form_Element_Text('key_number');
        $key_number->setAttrib('maxLength', 50);
        $key_number->setLabel("Serial Number");
        $key_number->addFilter(new Zend_Filter_StringTrim());
		$key_number->addValidator("regex",true,array(                           
                          'pattern'=>'/^[0-9a-zA-Z]+\d+[a-zA-Z0-9.,&\(\)\/\-_\' ?]+$/',
                           'messages'=>array(
                               'regexNotMatch'=>'Enter Valid Key Number.'
                           )
        			));			
		 
		$warenty_status = new Zend_Form_Element_Radio('warenty_status');
		$warenty_status->setSeparator('');
		$warenty_status->setValue("Yes");
		$warenty_status->setLabel('Warranty');
		$warenty_status->setRequired(True); 		
		$warenty_status->setAttrib('onchange', 'getWarrantyStatus();');
		$warenty_status->setRegisterInArrayValidator(false); 
		$warenty_status->addValidator('NotEmpty', false, array('messages' => 'Please select Warranty.'));
		$warenty_status->addMultiOptions(array(
				'Yes' => 'Yes',
				'No' => 'No',
		));
		
		
		
		$warenty_end_date = new ZendX_JQuery_Form_Element_DatePicker('warenty_end_date');
		$warenty_end_date->setLabel('Warranty / AMC End Date');
		$warenty_end_date->setOptions(array('class' => 'brdr_none'));		
		$warenty_end_date->setAttrib('readonly', 'true');
		$warenty_end_date->setAttrib('onfocus', 'this.blur()'); 
		
		$is_working = new Zend_Form_Element_Radio('is_working');
		$is_working->setAttrib('onchange', 'getIsWorkingStatus();');
        $is_working->setLabel('Is Working');
		$is_working->setValue("Yes");
		$is_working->setSeparator('');  
		$is_working->setRegisterInArrayValidator(false);
		$is_working->setRequired(TRUE);
		$is_working->addValidator('NotEmpty', false, array('messages' => 'Please select Asset working condition.'));
		$is_working->addMultiOptions(array(
				'Yes' => 'Yes',
				'No' => 'No',
		));
		
		
		$notes = new Zend_Form_Element_Textarea('notes');
		$notes->setLabel("Notes");
		$notes->setAttrib('rows', 10);
        $notes->setAttrib('cols', 50);
		$notes ->setAttrib('maxlength',200);
       
		
		$image = new Zend_Form_Element_Hidden('image');	
		$image->setAttrib('maxLength', 50);
		$image->setLabel('Asset Image');
		$imgerr = new Zend_Form_Element_Hidden('imgerr');
		$imgerrmsg = new Zend_Form_Element_Hidden('imgerrmsg');
		
		
		
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton');
		$submit->setLabel('Save');

		$this->addElements(
						array
								(
								$id,$category, $sub_category,$company_asset_code,
								$name,$location,$allocated_to,$responsible_technician,
								$asset_classification,$purchase_date,$invoice_number,
								$manufacturer,$key_number,$warenty_status,$warenty_end_date,
								$is_working,$notes,$image,$submit
								)
							);
        $this->setElementDecorators(array('ViewHelper')); 
		$this->setElementDecorators(array(
                    'UiWidgetElement',
        ),array('purchase_date','warenty_end_date'));
		$this->setElementDecorators(array('File'),array('image'));
		
	}
	
}