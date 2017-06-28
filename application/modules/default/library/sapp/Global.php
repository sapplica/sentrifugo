<?php
/********************************************************************************* 
 *  This file is part of Sentrifugo.
 *  Copyright (C) 2015 Sapplica
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

class sapp_Global
{
	private function __construct() {

		// private - should not be used
	}
        /**
         * This function is used to read session.
         * @param String $index = this is optional,if we want specific session value we will pass it.
         * @return Object   Session object
         */
	public static function _readSession($index = ''){
		$session=new Zend_Auth_Storage_Session();
		$data=$session->read();
		if($index == ''){
			return $data;
		}else{
			if(isset($data[$index]))
			return $data[$index];
			else
			return '';
		}
	}
	
	public static function _checkstatus()
	{
		 $auth = Zend_Auth::getInstance();
		 $usersmodel = new Default_Model_Users();	
		 $flag = 'all';
		 $isactivestatus = 1;
		 $temporarylock = 0;
		 
			if($auth->hasIdentity()){
				$loginUserId = $auth->getStorage()->read()->id;
				if($loginUserId)
				   $loggedinEmpId = $usersmodel->getUserDetailsByID($loginUserId,$flag);
			}
		 
	     
		 if(!empty($loggedinEmpId))
		 {
			$isactivestatus = $loggedinEmpId[0]['isactive'];
			$temporarylock = $loggedinEmpId[0]['emptemplock'];
		 }	
		 
		 if($isactivestatus == 1 && $temporarylock == 0)
		 {
		 	return 'true';
		 }else 
		 {
		 	return 'false';
		 }
	}
	
	public static function _logout() {

		$sessionData = sapp_Global::_readSession();
		Zend_Session::namespaceUnset('recentlyViewed');
		Zend_Session::namespaceUnset('organizationinfo');
			
		$auth = Zend_Auth::getInstance();
		$auth->clearIdentity();
		
		$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
		$redirector->gotoUrl('/default')
				  ->redirectAndExit();
	}
        /**
         * This function is used to get all parent menus which are used in mobile application
         * @return array Array of parent menus.
         */
        public static function mobile_parent_menus()
        {
            $parent_menu = array(
                ORGANIZATION,HUMANRESOURCE,EMPLOYEESELFSERVICE,BGCHECKS,REPORTS
            );
            return $parent_menu;
        }
        /**
         * This function is used to get all child menus which are used in mobile application
         * @return array Array of child menus.
         */
        public static function mobile_child_menus()
        {
            $child_menu = array(
                MYDETAILS,LEAVES,MYEMPLOYEES,MYHOLIDAYCALENDAR,SCHEDULEINTERVIEWS,EMPLOYEE,
                EMPSCREENING,ORGANISATIONINFO,BUSINESSUNITS
            );
            return $child_menu;
        }

        /**
         * This function is common for jq pagination,it will provide requiered HTML for jq pagination.
         * @return HTML Returns pagination div.
         */
        public static function pagination_html()
        {
?>
            <div class="pagination">
                <a href="#" class="first" data-action="first"><span class="sprite first-1"></span></a>
                <a href="#" class="previousNew" data-action="previous"><span class="sprite prev-1"></span></a>
                <input type="text" readonly="readonly" id="pagenotext" data-max-page="40" />
                <a href="#" class="nextNew" data-action="next"><span class="sprite next-1"></span></a>
                <a href="#" class="last" data-action="last"><span class="sprite last-1"></span></a>
            </div>
<?php 
        }
        /**
         * This is common function for export report to excel and mainly usefull in reports.
         * @param Array $final_array   = array of data to be displayed in excel. 
         * @param Array $column_array  = array of column names to be displayed in excel.
         * @param String $filename     = name of the excel file.
         */
        public static function export_to_excel($final_array,$column_array,$filename)
        {
            require_once 'Classes/PHPExcel.php';
            require_once 'Classes/PHPExcel/IOFactory.php';
            $objPHPExcel = new PHPExcel();

            $letters = range('A','Z');
            $count =0;
            
            $cell_name="";
           
            // Make first row Headings bold and highlighted in Excel.
            foreach ($column_array as $names)
            {
                $i = 1; 
                $cell_name = $letters[$count].$i;
                $names = html_entity_decode($names,ENT_QUOTES,'UTF-8');
			  
                $objPHPExcel->getActiveSheet()->SetCellValue($cell_name, $names);
                // Make bold cells
                $objPHPExcel->getActiveSheet()->getStyle($cell_name)->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->getStyle($cell_name)->applyFromArray( array(
									        'fill' => array(
									            'type' => PHPExcel_Style_Fill::FILL_SOLID,
									            'color' => array('rgb' => '82CAFF')
									        )
									    )
									);
                $objPHPExcel->getActiveSheet()->getColumnDimension($letters[$count])->setAutoSize(true);
                $i++;
                $count++;
            }
            
            // Display field/column values in Excel.   
            $i = 2;
            foreach($final_array as $data)
            {
                $count1 =0; 
                foreach ($column_array as $column_key => $column_name)
                {
                    // display field/column values  
                    $cell_name = $letters[$count1].$i;	                                        
                    $value = isset($data[$column_key])?(trim($data[$column_key]) == ''?"--":$data[$column_key]):"--";                    
                    $value = html_entity_decode($value,ENT_QUOTES,'UTF-8');
                    $objPHPExcel->getActiveSheet()->SetCellValue($cell_name, $value);
                    
                    $count1++;	
                }
                $i++;
            }

            self::clean_output_buffer();
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header("Content-Disposition: attachment; filename=\"$filename\"");
            header('Cache-Control: max-age=0');
            self::clean_output_buffer();
			
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $objWriter->save('php://output');
            
        }
	/**
	 * This function is used to send mail when we delete any configurations.
	 * @param String $config_menu_name  = name of configuration screen (Ex: Positions)
	 * @param String $config_name       = name of configuration (Ex: "Developer one")
	 */
	public static function send_configuration_mail($config_menu_name,$config_name)
	{
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity())
		{
			$sess_values = $auth->getStorage()->read();
		}

		$users_model = new Default_Model_Usermanagement();
		$emp_arr = $users_model->getEmpForConfigMail();

		foreach($emp_arr as $empdata)
		{
			$text = "<div style='padding: 0; text-align: left; font-size:14px; font-family:Arial, Helvetica, sans-serif;'>
                    <span style='color:#3b3b3b;'>Dear ".ucfirst($empdata['userfullname']).",</span><br />

                        <div style='padding:20px 0 0 0;color:#3b3b3b;'><b>".ucfirst($config_name)."</b> ".$config_menu_name." in configurations has been deleted by ".$sess_values->userfullname.". </div>    
                        <div style='padding:20px 0 10px 0;'>Please <a href='".BASE_URL."index/popup' target='_blank' style='color:#b3512f;'>click here</a> to login and check the details.</div>		

                    </div>  ";
			$options['subject'] = APPLICATION_NAME.': '.ucfirst($config_menu_name).' is deleted';
			$options['header'] = ucfirst($config_menu_name).' is deleted';
			$options['toEmail'] = $empdata['emailaddress'];
			$options['toName'] = $empdata['userfullname'];
			$options['message'] = $text;

			$options['cron'] = 'yes';
			sapp_Global::_sendEmail($options);
			
		}
	}
	/**
	 * This function is used to apply filters in grid like date picker,select box.
	 * @param type $search_filters      = array of filter types and values
	 * @param type $key                 = name of field/column
	 * @param type $name                = name of select box/input box
	 * @param type $display             = css code
	 * @param type $sText               = search text
	 * @param type $tabindx             = tab index
	 * @param type $search_function     = name of the search function
	 * @return string                   = HTML code of filter type.
	 */
	public static function grid_data($search_filters,$key,$name,$display,$sText,$tabindx,$search_function,$projectId = '',$otherAction = '',$start_date = '',$end_date = '',$emp_id = '')
	{
		$output = '';
		if($search_filters[$key]['type'] == 'select')
		{
             /*$projectId check used in timemanagement*/
			if($projectId != '' && $otherAction != ''){
				$output .= "<select name='$name' tabIndex=$tabindx id='$key' style='$display' class='searchtxtbox_$name table_inputs not_appli clearall fltleft' onchange = '".$search_function.",\"select\",\"$projectId\",\"$otherAction\",\"$start_date\",\"$end_date\",\"$emp_id\")"."' >";
			}else if($projectId != ''){
				$output .= "<select name='$name' tabIndex=$tabindx id='$key' style='$display' class='searchtxtbox_$name table_inputs not_appli clearall fltleft' onchange = '".$search_function.",\"select\",\"$projectId\")"."' >";
			}else{
				$output .= "<select name='$name' tabIndex=$tabindx id='$key' style='$display' class='searchtxtbox_$name table_inputs not_appli clearall fltleft' onchange = '".$search_function.",\"select\")"."' >";
			}
			foreach($search_filters[$key]['filter_data'] as $skey => $svalue)
			{
				$sselcted = ($sText!='')?(($sText == $skey)?"selected":""):"";
				$output .= "<option value='".$skey."' ".$sselcted.">".$svalue."</option>";
			}

		}
		else if($search_filters[$key]['type'] == 'datepicker')
		{
			$key_split = preg_split('/\./', $key);
			if(isset($key_split[1]))
			$key_class = "src_".$key_split[1];
			else
			$key_class = "src_".$key_split[0];
		//$yearrange =   date("Y", strtotime("-20 years")).':'.(date('Y')+10);
			$yearrange =   YEAR.':'.(date('Y')+20);
			if(isset($search_filters[$key]['yearrange']) && $search_filters[$key]['yearrange'] == 'yearrange')
			 $yearrange =   date("Y", strtotime("-70 years")).':'.(date('Y')+20); 
			 
			$output .= "<input readonly tabIndex=$tabindx  type='text' name='$name' data-focus='no' id='$key' style='$display' class='searchtxtbox_$name table_inputs grid_search_inputs ".$key_class."' value='$sText' onchange='".$search_function.",\"date\")"."' />";
			$output .= "<script type='text/javascript' language='javascript'>
                    $(document).ready(function(){
                                $( '.".$key_class."' ).datepicker({
                                    showOn: 'focus',
                                    buttonImage: '".MEDIA_PATH."images/cal.png',
                                    buttonImageOnly: true,
									yearRange: '".$yearrange."',
                                    buttonText: '',
                                    changeMonth: true,
                                    changeYear: true,
                                    dateFormat:'".DATEFORMAT_JS."',
                                    showButtonPanel: true ,
                                    onSelect:function(){
                                     $( '.".$key_class."' ).trigger('change');
                                    }    
                                    });
                                    
                                    
                                    
                                    if($('.searchtxtbox_".$name."').is(':visible'))
                                    {
                                        $('.ui-datepicker-trigger').css('display','inline');
                                        	
                                    }
                                    else 
                                        $('.ui-datepicker-trigger').css('display','none');
                                       	
                                    });
                            </script>";
		}

		return $output;
	}
        /**
         * This function is used to write values into session
         * @param String $index  = string name in session to change
         * @param Mixed $value   = value to be write in string.
         */
	public static function _writeSession($index = '',$value = ''){
		$session=new Zend_Auth_Storage_Session();
		$data=$session->read();

		$data[$index] = $value;

	}

        /**
         * This function is used to base url of project.
         * @return String  Base url of project.
         */
	public static function _getBaseURL(){
		$request = Zend_Controller_Front::getInstance()->getRequest();
		
		return $request->getBaseUrl().'/';
	}
        /**
         * This function is used to host base url of project.
         * @return String  HostBase url of project.
         */
	public static function _getHostBaseURL(){
		$request = Zend_Controller_Front::getInstance()->getRequest();
		return "http://".$request->getHttpHost() . $request->getBaseUrl().'/';
		
	}
        /**
         * This function is used to encrypt any value
         * @param String $string = string to be encrypt
         * @return String Encrypted string
         */
	public static function _encrypt($string){
		$key = "chitgoks_hrms";
		$result = '';
		for($i=0; $i<strlen($string); $i++) {
			$char = substr($string, $i, 1);
			$keychar = substr($key, ($i % strlen($key))-1, 1);
			$char = chr(ord($char)+ord($keychar));
			$result.=$char;
		}

		return base64_encode($result);
	}
        /**
         * This function is used to decrypt any encrypted value
         * @param String $string = string to be decrypt
         * @return String Decrypted string
         */
	public static function _decrypt($string)
        {
		$key = "chitgoks_hrms";
		$result = '';
		$string = base64_decode($string);

		for($i=0; $i<strlen($string); $i++) {
			$char = substr($string, $i, 1);
			$keychar = substr($key, ($i % strlen($key))-1, 1);
			$char = chr(ord($char)-ord($keychar));
			$result.=$char;
		}

		return $result;
	}
	/**
	 * This function is used to build html select option tag.
	 * @param String $value  =  value of option tag.
	 * @param String $text   =  text of option tag.
	 * @param String $title  =  title of option tag.
	 * @return String  Option tag of selection box.
	 */
	public static function selectOptionBuilder($value,$text,$title = '',$jobtitle_id='',$jobtitlename='',$employee_id='',$reporting_manager_id='',$reporting_manager_name='')
	{
		return "<option value='".$value."' title='".$title."' jobtitle_id='".$jobtitle_id."' jobtitlename='".$jobtitlename."' employee_id='".$employee_id."' reporting_manager_id='".$reporting_manager_id."' reporting_manager_name='".$reporting_manager_name."'>".$text."</option>";
	}
        /**
         * This function is used to generate password.
         * @param Integer $length    = required password length
         * @param Integer $strength  = strength of the password.
         * @return string   generated password
         */
	public static function generatePassword($length=9, $strength=0) {
		$vowels = 'aeuy';
		$consonants = 'bdghjmnpqrstvz';
		if ($strength & 1) {
			$consonants .= 'BDGHJLMNPQRSTVWXZ';
		}
		if ($strength & 2) {
			$vowels .= "AEUY";
		}
		if ($strength & 4) {
			$consonants .= '23456789';
		}
		if ($strength & 8) {
			$consonants .= '@#$%';
		}

		$password = '';
		$alt = time() % 2;
		for ($i = 0; $i < $length; $i++) {
			if ($alt == 1) {
				$password .= $consonants[(rand() % strlen($consonants))];
				$alt = 0;
			} else {
				$password .= $vowels[(rand() % strlen($vowels))];
				$alt = 1;
			}
		}
		return $password;
	}
        /**
         * This function is common to display error message.
         * @param String $field  = name of field.
         * @param String $type   = type of field(ex: select)
         * @return String  Formatted error message.
         */
	public static function _requiredfielderrorMessage($field,$type){
		if($type == ''){
			return "Please enter $field.";
		}else{
			return "Please $type $field.";
		}
	}
        /**
         * This function is common to display error message.
         * @param String $field  = name of field.
         * @param String $type   = type of field(ex: select)
         * @return String  Formatted error message.
         */
	public static function _secondrequiredfielderrorMessage($field,$type){
		if($type == ''){

			return "Please enter $field.";

		}else{
			return "Please $type a $field.";
		}
	}
        /**
         * This function is used to trim and add slashes to any string.
         * @param String $value = string to be trimmed.
         * @return String Formatted string.
         */
	public static function trimandescape($value){
		return trim(addslashes($value));
	}	

        /**
         * This function is used to escape any string.
         * @param String $inputString  = string to be escaped.
         * @return String  Formatted string.
         */
	public static function escapeString($inputString)
	{
            return htmlentities(trim($inputString), ENT_QUOTES, "UTF-8");
	}
        /**
         * This function is used to unescape any string.
         * @param String $inputString  = string to be unescaped.
         * @return String  Formatted string.
         */
	public static function unescapeString($inputString)
	{
            return html_entity_decode($inputString, ENT_QUOTES, 'UTF-8');
	}
        /**
         * This function is used to convert any object to array.
         * @param Object $obj  = object to be changed.
         * @return Array  Formatted object to array.
         */
	public static function toArray($obj)
	{

		if(is_object($obj)) $obj = (array) $obj;
		if(is_array($obj)) {
			$new = array();
			foreach($obj as $key => $val) {
				$new[$key] = self::toArray($val);
			}
		}
		else {
			$new = $obj;
		}
		return $new;
	}
        /**
         * This function is used to get object properties in an array.
         * @param Object $object  = object to be changed.
         * @return Array  Array of properties of an object.
         */
	public static function parseObjectToArray($object) {
		$array = array();
		if (is_object($object)) {
			$array = get_object_vars($object);
		}
		return $array;
	}

        /**
         * This function is used to upload an image to server.
         * @return String  On success file name.On error it will return error message.
         */
	public static function  _uploadImage(){
		try{
			$uploaddir = USER_UPLOAD_PREVIEW_PATH;
			$fileExt = $this->getFileExtenction(basename($_FILES['uploadfile']['name']));

			
			

			$file = $uploaddir.'_'.time().'.'.$fileExt;
			$filename = 'newuser_'.time().'.'.$fileExt;
			$size = $_FILES['uploadfile']['size'];

			if($size  >= 1024000)
			{
				
					
				echo "Image size is more";
				return;
			}

			if (move_uploaded_file($_FILES['uploadfile']['tmp_name'], $file)) {
				
					
				if(($fileExt != "bmp") && ($fileExt != "BMP")){
					$newimage =  $uploaddir.$filename;
					$image = new Zend_Resize($newimage.'/'.$filename);
					$image-> resizeImage(50, 50, 'crop');
					$image->saveImage($newimage.'/'.$logo, 100);

					
				}
					
							
				echo $filename;
			}
			else {					
				echo "Image upload failed";			}
		}
		catch(Exception $e){
			echo $e->getMessage();
		}
	}

        /**
         * This function to get extension of any file.
         * @param String $fileRef  = name of file to get extension.
         * @return String   Extension of file.
         */
	public static function getFileExtenction($fileRef){
		$fileexte = substr(strrchr($fileRef,'.'),1);
		return $fileexte;
	}
        /**
         * This function is used to save image.
         * @param String $savePath      = path that image to be saved.
         * @param Integer $imageQuality = quality of the image which we want to store.
         */
	public function saveImage($savePath, $imageQuality="100")
	{
		// *** Get extension
		$extension = strrchr($savePath, '.');
		$extension = strtolower($extension);

		switch($extension)
		{
			case '.jpg':
			case '.jpeg':
				if (imagetypes() & IMG_JPG) {
					imagejpeg($this->imageResized, $savePath, $imageQuality);
				}
				break;

			case '.gif':
				if (imagetypes() & IMG_GIF) {
					imagegif($this->imageResized, $savePath);
				}
				break;

			case '.png':
				// *** Scale quality from 0-100 to 0-9
				$scaleQuality = round(($imageQuality/100) * 9);

				// *** Invert quality setting as 0 is best, not 9
				$invertScaleQuality = 9 - $scaleQuality;

				if (imagetypes() & IMG_PNG) {
					imagepng($this->imageResized, $savePath, $invertScaleQuality);
				}
				break;

				// ... etc

			default:
				// *** No extension - No save.
				break;
		}

		imagedestroy($this->imageResized);
	}
        /**
         * This function is used to resize image.
         * @param Integer $newWidth    = new width of image
         * @param Integer $newHeight   = new height of image
         * @param String $option       = options like auto,crop
         */
	public function resizeImage($newWidth, $newHeight, $option="auto")
	{
		// *** Get optimal width and height - based on $option
		$optionArray = $this->getDimensions($newWidth, $newHeight, $option);

		$optimalWidth  = $optionArray['optimalWidth'];
		$optimalHeight = $optionArray['optimalHeight'];


		// *** Resample - create image canvas of x, y size
		$this->imageResized = imagecreatetruecolor($optimalWidth, $optimalHeight);
		imagecopyresampled($this->imageResized, $this->image, 0, 0, 0, 0, $optimalWidth, $optimalHeight, $this->width, $this->height);


		// *** if option is 'crop', then crop too
		if ($option == 'crop') {
			$this->crop($optimalWidth, $optimalHeight, $newWidth, $newHeight);
		}
	}
        /**
         * This function is used to upload an image.
         * @return string On success file name,on failure error message
         */
	public static function imageupload()
	{

		$savefolder = USER_PREVIEW_UPLOAD_PATH;		// folder for upload
			
			
			
		$max_size = 1024;			// maxim size for image file, in KiloBytes

		// Allowed image types
		
		$allowtype = array('gif', 'jpg', 'jpeg', 'png');

		/** Uploading the image **/

		$rezultat = '';
		$result_status = '';
		$result_msg = '';
		// if is received a valid file
		if (isset ($_FILES['profile_photo'])) {
			// checks to have the allowed extension
			$type = end(explode(".", strtolower($_FILES['profile_photo']['name'])));
			
			if (in_array($type, $allowtype)) {
				// check its size
				if ($_FILES['profile_photo']['size']<=$max_size*1024) {
					// if no errors
					if ($_FILES['profile_photo']['error'] == 0) {

						$newname = 'preview_'.date("His").'.'.$type;

						$thefile = $savefolder . "/" . $_FILES['profile_photo']['name'];
							
						$newfilename = $savefolder . "/" . $newname;
							
							
						$filename = $newname;
						
						// if the file can`t be uploaded, return a message
							
						if (!move_uploaded_file ($_FILES['profile_photo']['tmp_name'], $newfilename)) {
					  $rezultat = '';
					  $result_status = 'error';
					  $result_msg = 'The file cannott be uploaded, try again.';
						}
						else {
					  // Return the img tag with uploaded image.
					  
					  
					  $rezultat = $filename;

					  $image = new Zend_Resize($newfilename);
					  $image-> resizeImage(84, 84, 'crop');
					  $image->saveImage($newfilename, 100);

					  $result_status = 'success';
					  $result_msg = '';
					  
						}
					}
				}
				else
				{
					$rezultat = '';
					$result_status = 'error';
					$result_msg = 'The file '. $filename . ' exceeds the maximum permitted size '. $max_size. ' KB.';
				}
			}
			else
			{
				$rezultat = '';
				$result_status = 'error';
				$result_msg = 'The file '. $filename . ' has not an allowed extension.';
					
			}
		}

		// encode with 'urlencode()' the $rezultat and return it in 'onload', inside a BODY tag
		

		$result = array(
				'result'=>$result_status,
				'img'=>$rezultat,
				'msg'=>$result_msg
		);
		return $result;
	}
        /**
         * This function is used to upload an image after resizing it.
         * @return string On success file name,on failure error message
         */
	public static function imageUploadAfterResize()
	{
		$max_size = 250;			// maxim size for image file, in KiloBytes

		$allowtype = array('pdf', 'docx', 'rtf', 'odx', 'doc', 'txt', 'odt');

		/** Uploading the image **/

		$rezultat = '';
		$result_status = '';
		$result_msg = '';

		$type = end(explode(".", strtolower($_FILES['form_attachment']['name'])));
		
		if (in_array($type, $allowtype))
		{
			if ($_FILES['form_attachment']['size']<=$max_size*1000)
			{
				//File Upload
				$logo = "";
				$path = FORM_ATTACHMENT_PREVIEW_PATH;
				$upload = new Zend_File_Transfer_Adapter_Http();
				$upload->setDestination($path);
				//Constructor needs one parameter, the destination path is a good idea
				$renameFilter = new Zend_Filter_File_Rename($path);
				$files = $upload->getFileInfo();
				$logo = "";
				foreach ($files as $fileID => $fileInfo) {
					if (! $fileInfo['name'] == '') {
						
						$varaible = explode('.',$fileInfo['name']);
						$extension = end($varaible);
						
						$logo = md5(uniqid(rand(), true)).'.'.$extension;
						$renameFilter->addFile(
						array('source' => $fileInfo['tmp_name'],
						'target' => $logo, 'overwrite' => true));
					}
					// add filters to Zend_File_Transfer_Adapter_Http
					$upload->addFilter($renameFilter);
					// receive all files
					try {
						$upload->receive();
						//Image Resize
						$frontobj = Zend_Controller_Front::getInstance();
						if($frontobj->getRequest()->getParam('form_attachment') != ''){
							$image = new Zend_Resize($path.'/'.$logo);
							$image-> resizeImage(84, 84, 'crop');
							$image->saveImage($path.'/'.$logo, 100);
						}
						//End Image Resize
					} catch (Zend_File_Transfer_Exception $e) {
						
						$rezultat = '';
						$result_status = 'error';
						$result_msg = $e->getMessage();
					}
					//upto here

					$rezultat = $logo;
					$result_status = 'success';
					$result_msg = 'Uploaded Succesfully.';
				}
				//End File Upload
			}
			else
			{
				$rezultat = '';
				$result_status = 'error';
				$result_msg = 'The file exceeds the maximum permitted size '. $max_size. ' KB.';
			}
		}
		else
		{
			$rezultat = '';
			$result_status = 'error';
			$result_msg = 'The file '. $filename . ' has not an allowed extension.';
		}

		$result = array(
				'result'=>$result_status,
				'img'=>$rezultat,
				'msg'=>$result_msg
		);
		return $result;
	}
        /**
         * This function is used to save a record in log table.
         * @param Integer $menuID         = id of menu name
         * @param Integer $actionflag     = flag represents add,update,delete
         * @param Integer $loginUserId    = id of logged user
         * @param Integer $recordId       = id of changed record  
         * @param String $childrecordId   = if any child record exists it will concatenate as string
         * @param Array $defined_str      = this is mainly used for employees/users active & inactive actions
         * @return Integer  Id of saved record in log table.
         */
	public static function logManager($menuID,$actionflag,$loginUserId,$recordId,$childrecordId = '',$defined_str = '')
	{
		$date = new Zend_Date(); // initializing Zend Date Object
		$logmanagermodel = new Default_Model_Logmanager();
		$jsonlogDataArr = array();
		if($childrecordId != '')
		{
			$childrecordArr = explode(',',$childrecordId);
			for($i=0;$i<sizeof($childrecordArr);$i++)
			{
				$logarr[$i] = array('userid' => $loginUserId,
                       		'recordid' =>$recordId,
							'childrecordid' => $childrecordArr[$i],
				
							'date' => gmdate("Y-m-d H:i:s")
				);

			}
			$jsonlogarr = json_encode($logarr);
			$jsonlogarr = rtrim($jsonlogarr,"]");$jsonlogarr = ltrim($jsonlogarr,"[");
		}
		else
		{
			$logarr = array('userid' => $loginUserId,
								'recordid' =>$recordId,
			
			
								'date' => gmdate("Y-m-d H:i:s")
			);
			$jsonlogarr = json_encode($logarr);	// Building Json String of loggedinUser,DatabaseID of UserAction,Date
		}
		if(is_array($defined_str) && count($defined_str) >0)
		{
			$logarr = $defined_str;
			$jsonlogarr = json_encode($logarr);
		}
		//Saving Or Updating to DB using On Duplicate Key by setting menuID(ControllerID) and Useraction as Unique Fields In DB
		$id = $logmanagermodel->addOrUpdateLogManager($menuID,$actionflag,$jsonlogarr,$loginUserId,$recordId);
		return $id;

	}
	/**
	 * This function is used in roles/edit.html, this is used to reuse radio buttons html.
	 * @parameters
	 * @param Integer $menu_id         = id of menu item.
	 * @param Integer $parent_menu_id1 = id of parent one.
	 * @param Integer $parent_menu_id2 = id of parent two.
	 * @param String $display         = condition to display or not.
	 * @param Array $permission_data = data of menus from privileges after save.
	 * @param String $chk_disabled    = check whether to disable or not.
	 * @param Array $default_permissions = data from DB to display default privileges of group.
	 * @return string  HTML of radio buttons.
	 */
	public static function usermenu_html($menu_id,$parent_menu_id1,$parent_menu_id2,$display,$permission_data,$chk_disabled = null,$default_permissions)
	{
		$parent_class_div1 = '';$parent_class_div2 = '';
		$parent_class_radio1 = '';$parent_class_radio2 = '';
		if($parent_menu_id1 > 0)
		{
			$parent_class_div1 = "cls_radiobuttons_div".$parent_menu_id1;
			$parent_class_radio1 = "cls_radiobuttons".$parent_menu_id1;
		}
		if($parent_menu_id2 > 0)
		{
			$parent_class_div2 = "cls_radiobuttons_div".$parent_menu_id2;
			$parent_class_radio2 = "cls_radiobuttons".$parent_menu_id2;
		}
		if(count($permission_data) == 0)
		{
			$permission_data['addpermission'] = '';
			$permission_data['editpermission'] = '';
			$permission_data['deletepermission'] = '';
			$permission_data['viewpermission'] = '';
			$permission_data['uploadattachments'] = '';
			$permission_data['viewattachments'] = '';
		}
		$chk_disabled_val = "";
		if($chk_disabled != '')
		{
			$chk_disabled_val = "disabled=disabled";
		}
		$i=0;
		?>
<div id="idcls_checkboxes_<?php echo $menu_id;?>" class="cls_radiobuttons_div cls_radiobuttons_div<?php echo $menu_id;?> <?php echo $parent_class_div1;?> <?php echo $parent_class_div2;?>" style="display:<?php echo $display;?>">
	<div class="arrow_img_cls"></div>

	<?php
	if($default_permissions['addpermission'] == 'Yes')
	{
		$i++;
		?>
	<div
		class="permission_div <?php echo ($i ==1 || $i == 3 || $i == 5)?"permission_div_brdr":"";?>">
		<div class="permission_radio_div">
			<input type="checkbox" <?php echo $chk_disabled_val;?>
			<?php echo ($permission_data['addpermission']=='Yes')?"checked='checked'":"";?>
				onclick="checkradio_child_roles('cls_radio_add_yes<?php echo $menu_id;?>',this);"
				class="cls_radiobuttons_rd cls_radiomenu_yes_<?php echo $menu_id;?> <?php echo $parent_menu_id1>0?"cls_radio_add_yes".$parent_menu_id1:"";?> <?php echo $parent_menu_id2>0?"cls_radio_add_yes".$parent_menu_id2:"";?>"
				name="rd_addpermission<?php echo $menu_id;?>" value="Yes"
				id="idaddpermi_yes_<?php echo $menu_id;?>"
				title="Assign add permission." data-parent = "<?php echo $menu_id;?>" />
		</div>
		<span class="radio_titles">Add</span>
	</div>
	<?php
	}
	?>
	<?php
	if($default_permissions['editpermission'] == 'Yes')
	{
		$i++;
		?>
	<div
		class="permission_div <?php echo ($i ==1 || $i == 3 || $i == 5)?"permission_div_brdr":"";?>">
		<div class="permission_radio_div">
			<input type="checkbox" <?php echo $chk_disabled_val;?>
			<?php echo ($permission_data['editpermission']=='Yes')?"checked='checked'":"";?>
				onclick="checkradio_child_roles('cls_radio_edit_yes<?php echo $menu_id;?>',this);"
				class="cls_radiobuttons_rd cls_radiomenu_yes_<?php echo $menu_id;?> <?php echo $parent_menu_id1>0?"cls_radio_edit_yes".$parent_menu_id1:"";?> <?php echo $parent_menu_id2>0?"cls_radio_edit_yes".$parent_menu_id2:"";?>"
				name="rd_editpermission<?php echo $menu_id;?>" value="Yes"
				id="ideditpermi_yes_<?php echo $menu_id;?>"
				title="Assign edit permission." data-parent = "<?php echo $menu_id;?>" />
		</div>
		<span class="radio_titles">Edit</span>
	</div>
	<?php
	}
	?>
	<?php
	if($default_permissions['deletepermission'] == 'Yes')
	{
		$i++;
		?>
	<div
		class="permission_div <?php echo ($i ==1 || $i == 3 || $i == 5)?"permission_div_brdr":"";?>">
		<div class="permission_radio_div">
			<input type="checkbox" <?php echo $chk_disabled_val;?>
			<?php echo ($permission_data['deletepermission']=='Yes')?"checked='checked'":"";?>
				onclick="checkradio_child_roles('cls_radio_delete_yes<?php echo $menu_id;?>',this);"
				class="cls_radiobuttons_rd cls_radiomenu_yes_<?php echo $menu_id;?> <?php echo $parent_menu_id1>0?"cls_radio_delete_yes".$parent_menu_id1:"";?> <?php echo $parent_menu_id2>0?"cls_radio_delete_yes".$parent_menu_id2:"";?>"
				name="rd_deletepermission<?php echo $menu_id;?>" value="Yes"
				id="iddeletepermi_yes_<?php echo $menu_id;?>"
				title="Assign delete permission." data-parent = "<?php echo $menu_id;?>" />
		</div>
		<span class="radio_titles">
                    <?php 
                    if($menu_id == PENDINGLEAVES || $menu_id == 31)
                    {
                        echo "Cancel";
                    }
                    else 
                    {
                        echo "Delete";
                    }
                    ?>
                    
                </span>
	</div>
	<?php
	}
	?>
	<?php
	if($default_permissions['viewpermission'] == 'Yes')
	{
		$i++;
		?>
	<div
		class="permission_div <?php echo ($i ==1 || $i == 3 || $i == 5)?"permission_div_brdr":"";?>">
		<div class="permission_radio_div">
			<input type="checkbox" <?php echo $chk_disabled_val;?>
			<?php echo ($permission_data['viewpermission']=='Yes')?"checked='checked'":"";?>
				onclick="checkradio_child_roles('cls_radio_view_yes<?php echo $menu_id;?>',this);"
				class="cls_radiobuttons_rd cls_radiomenu_yes_<?php echo $menu_id;?> <?php echo $parent_menu_id1>0?"cls_radio_view_yes".$parent_menu_id1:"";?> <?php echo $parent_menu_id2>0?"cls_radio_view_yes".$parent_menu_id2:"";?>"
				name="rd_viewpermission<?php echo $menu_id;?>" value="Yes"
				id="idviewpermi_yes_<?php echo $menu_id;?>"
				title="Assign view permission." data-parent = "<?php echo $menu_id;?>" />
		</div>
		<span class="radio_titles">View</span>
	</div>
	<?php
	}
	?>
	
	
	<?php
	
	if($i == 1)
	{
		?>
	<script type="text/javascript" language="javascript">
                $('#idcls_checkboxes_<?php echo $menu_id;?>').find('div').removeClass('permission_div_brdr')
            </script>
            <?php
	}
        if($i == 0)
        {
            //for removing div for reports like menu items
?>
            <script type="text/javascript" language="javascript">
                $('#idcls_checkboxes_<?php echo $menu_id;?>').remove();
            </script>
<?php 
        }
	?>

</div>
<!-- end of cls_radiobuttons-->
	<?php
	}

	/**
	 * This function gives jquery functions which acts as client validators.
	 * @parameters
	 * @param object $form_obj    =  Zend_Form object
	 *
	 * @return string     Jquery events for validations.
	 */
	public static function generateClientValidations($form_obj)
	{
		?>
<script type="text/javascript" language="javascript">
            $(document).ready(function(){
			
<?php       
        $form_elements = $form_obj->getElements();
        foreach($form_elements as $element)
        {
            
            $ele_name = $element->getName();
            $element_id = $element->getId();
            $ele_validators = $element->getValidators();
            $element_type = $element->getType();
            if(count($ele_validators) > 0)
            {
                $ele_validators_cpy = $ele_validators;
                
                foreach($ele_validators as $validator)
                {
                    $validator_name = get_class($validator);
                    if($validator_name == 'Zend_Validate_NotEmpty')
                    {
					    
                        $messages_arr = $validator->getValidatorMessages();
                        $isempty_msg = $messages_arr['isEmpty'];
                        if($element_type == 'Zend_Form_Element_Select' || $element_type == 'Zend_Form_Element_Multiselect')
                        {
 ?>            
	
                        $('#s2id_<?php echo $element_id;?>').blur(function(){ 
                            $('#errors-<?php echo $element_id;?>').remove();
                            
                            if($.trim($('#<?php echo $element_id;?>').val()) == '')
                             {

                                // To place error messages after Add Link
                                $('#<?php echo $element_id;?>').after("<span class='errors' id='errors-<?php echo $element_id;?>'><?php echo $isempty_msg;?></span>");
                             }
                             else 
                             {
                                 $('#errors-<?php echo $element_id;?>').remove();   

                             }
                            });
						
                           
        <?php                           
                        }
                        else
                        {
?>                      

                        $('#<?php echo $element_id;?>').blur(function(){                                   
                            $('#errors-<?php echo $element_id;?>').remove();                                    
                            if($.trim($(this).val()) == '')
                             { 
							    $(this).parent().append("<span class='errors' id='errors-<?php echo $element_id;?>'><?php echo $isempty_msg;?></span>");
                                
                                $(this).val('');								
                             }
                             else 
                             {
                                 $('#errors-<?php echo $element_id;?>').remove();   
                                 
<?php
                                if(array_key_exists('Zend_Validate_Regex', $ele_validators_cpy))
                                {
?>                                  $('#<?php echo $element_id;?>').trigger('keyup');
<?php                
                                }
                                
?>
                                $('#<?php echo $element_id;?>').trigger('change'); 
                             }
                             
                            });
							
							
                           
        <?php         }
                    }
                    elseif($validator_name == 'Zend_Validate_Regex')
                    {
                        $pattern = $validator->getPattern();
                        $messages_arr = $validator->getValidatorMessages();
                        $notmatch_msg = $messages_arr['regexNotMatch'];
?>                      
                        $('#<?php echo $element_id;?>').keyup(function(){
						     var expr = <?php echo $pattern;?>;
                             $('#errors-<?php echo $element_id;?>').remove(); 
                             $('.errors-<?php echo $element_id;?>').remove(); 
                            
                             if($(this).val() != '')
                             { 
                                 if(!expr.test($(this).val()))
                                  {
                                      $('#errors-<?php echo $element_id;?>').remove();                                    
                                      $(this).parent().append("<span class='errors' id='errors-<?php echo $element_id;?>'><?php echo $notmatch_msg;?></span>");
                                      
                                  }
                             }
                             else
                             {
                                 $('#errors-<?php echo $element_id;?>').remove();                                    
                             }
                        }); 
                        
<?php 
                    }
                    elseif($validator_name == 'Zend_Validate_EmailAddress')
                    {
?>                      
                        $('#<?php echo $element_id;?>').blur(function(){                                                               
                            var expr = /^(?!.*\.{2})[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/;
<?php                       if(!array_key_exists('Zend_Validate_NotEmpty', $ele_validators))   
                            {
?>                              $('#errors-<?php echo $element_id;?>').remove(); 
<?php             
                            }
?>
                            if($.trim($(this).val()) != '')
                             {                                                            
                                 $('#errors-<?php echo $element_id;?>').remove();                                                                 
                                 if(!expr.test($(this).val()))                             
                                    $(this).parent().append("<span class='errors' id='errors-<?php echo $element_id;?>'>Please enter valid email.</span>");
                             }                             
                        });                           
<?php                        
                    }
                    elseif($validator_name == 'Zend_Validate_Uri')
                    {
  ?>                      
                        $('#<?php echo $element_id;?>').blur(function(){                                                               
                            var expr = /^(http\:\/\/|https\:\/\/)?([a-z0-9][a-z0-9\-]*\.)+[a-z0-9][a-z0-9\-]*$/;
<?php                       if(!array_key_exists('Zend_Validate_NotEmpty', $ele_validators))   
                            {
?>                              $('#errors-<?php echo $element_id;?>').remove(); 
<?php             
                            }
?>
                            if($.trim($(this).val()) != '')
                             {                                                            
                                 $('#errors-<?php echo $element_id;?>').remove();                                                                 
                                 if(!expr.test($(this).val()))                             
                                    $(this).parent().append("<span class='errors' id='errors-<?php echo $element_id;?>'>Please enter valid URL.</span>");
                             }                             
                        });                           
<?php                       
                    }
					elseif($validator_name == 'Zend_Validate_StringLength')
                    {
?>					
                           $('#<?php echo $element_id;?>').blur(function(){
                         
					        if($.trim($(this).val()) != '')
                             {
							    var minlength = <?php echo $validator->getMin();?>;
						        var maxlength = <?php echo $validator->getMax();?>;	  
							    var label = $(this).parent().parent().find('label').text();	
								label = $.trim(label);
								
								if(label== 'Secondary Phone') 
								  label = 'Secondary phone number';
								if(label== 'Mobile') 
								   label = 'Mobile number';
								if(label == 'Primary Phone') 
								   label = 'Primary phone number';
								   
						        label = label.substr(0, 1).toUpperCase() + label.substr(1,label.length).toLowerCase();
								
							    if(minlength != '')
								{
									if($.trim($(this).val().length) < minlength)
									{ 								
													 
										$('#errors-<?php echo $element_id;?>').remove();                                                                 
										$(this).parent().append("<span class='errors' id='errors-<?php echo $element_id;?>'>"+label+" must contain at least "+minlength+" characters.</span>");
									}
								}
                                if(maxlength != '')
								{ 								
									if($.trim($(this).val().length) > maxlength)
									{
										$('#errors-<?php echo $element_id;?>').remove();                                                                 
										$(this).parent().append("<span class='errors' id='errors-<?php echo $element_id;?>'>"+label+" must contain at most "+maxlength+" characters.</span>");
									} 
                                }									
                             } 
						});	 
<?php					   
					}
                }//end of validator loop.
            }
        }//end of element loop.        
?>
        }); //end of ready function.
    </script>
<?php
	}// end of generateClientValidations function.

	/**
	 * This function will change date format from mm-dd-yy to yy-mm-dd (database format) and vice versa.
	 * @parameters
	 * @param date     =  date to be modified
	 * @param type     =  type of modification(database,view)
	 *
	 * @return  Date in modified format.
	 */
	public static function change_date($date,$type)
	{
		$new_date = null;
		if($type == 'database')
		{
			if($date !='')
			{ 
				$date_obj = new DateTime($date);
				$new_date = $date_obj->format('Y-m-d');
			}
		}
		else if($type == 'birthday')
		{
			if($date !='')
			{
				$date_obj = new DateTime($date);
				$new_date = $date_obj->format('M d');
			}
		}
		else if($type == 'announcement')
		{
			if($date !='')
			{
				$date_obj = new DateTime($date);
				$new_date = $date_obj->format('D, M d');
			}
		}
		else
		{
			if($date !='')
			{
				$date_obj = new DateTime($date);
				$new_date = $date_obj->format(DATEFORMAT_PHP);
				
			}
		}
		return $new_date;
	}
	/**
	 * This function will change time format ,
	 * @parameters
	 * @param time     =  time to be modified
	 * @param type     =  type of modification(database,view)
	 *
	 * @return  Time in modified format.
	 */
	public static function change_time($time,$type)
	{
		$new_time = null;
		if($type == 'database')
		{
			if($time !='')
			{
				$new_time = date('H:i',  strtotime($time));
			}
		}
		else
		{
			if($time !='')
			{
				$new_time = date(TIME_FORMAT,  strtotime($time));
			}
		}
		return $new_time;
	}
	/**
	 * This function is used to genarate new encrypted password.
	 *
	 * @returns {String}  Encrypted password.
	 */
	public static function getNewPassword()
	{
		$generatedPswd = uniqid();
		$encodedPswd = md5($generatedPswd);

		return $encodedPswd;
	}//end of getNewPassword function.
	
	public static function writeEMailSettingsconstants($tls,$auth,$port,$username,$password,$server_name)
	{
			$filename = Zend_Registry::get('emailconfig_file_path');
			if(file_exists($filename))
			{
				$db_content = "<?php
		           defined('MAIL_SMTP') || define('MAIL_SMTP','".$server_name."');
		           defined('MAIL_USERNAME') || define('MAIL_USERNAME','".$username."');
		           defined('MAIL_PASSWORD') || define('MAIL_PASSWORD','".$password."');
		           defined('MAIL_PORT') || define('MAIL_PORT','".$port."');
		           defined('MAIL_AUTH') || define('MAIL_AUTH','".$auth."');
		           defined('MAIL_TLS') || define('MAIL_TLS','".$tls."');
		         ?>";
				try{
					$handle = fopen($filename, "w+");
					fwrite($handle,trim($db_content));
					fclose($handle);
					return true;
				}
				catch (Exception $e)
				{
					
				}
			}	
	}
	
public static function writeApplicationConstants($email,$app_name)
	{
			$filename = Zend_Registry::get('application_file_path');
			if(file_exists($filename))
			{
				$db_content = "<?php
			       defined('SUPERADMIN_EMAIL') || define('SUPERADMIN_EMAIL','".$email."');
			       defined('APPLICATION_NAME') || define('APPLICATION_NAME','".$app_name."');
			     ?>";
				try{
					$handle = fopen($filename, "w+");
					fwrite($handle,trim($db_content));
					fclose($handle);
					return true;
				}
				catch (Exception $e)
				{
				
				}
			}	
	}
	/**
	 * This function is used to write site constants to a php file.
	 */
	public static function generateSiteConstants()
	{
		$filename = Zend_Registry::get('siteconstant_file_path');
		$site_model = new Default_Model_Sitepreference();
		$site_data = $site_model->getActiveRecord();
		if(!empty($site_data)){
		$site_data = $site_data[0];
                
                $utc = new DateTimeZone('UTC');
                $dt = new DateTime('now', $utc);                                                
                $current_tz = new DateTimeZone($site_data['tz_value']);
                $tzoffset =  $current_tz->getOffset($dt);
                $offset = self::formatOffset($tzoffset);
                                                                                                                    
                
		$site_content = "<?php
           defined('DATEFORMAT_PHP') || define('DATEFORMAT_PHP','".$site_data['date_format']."');
           defined('DATEFORMAT_MYSQL') || define('DATEFORMAT_MYSQL','".$site_data['mysql_dateformat']."');
           defined('DATEFORMAT_JS') || define('DATEFORMAT_JS','".$site_data['js_dateformat']."');
           defined('DATE_DESCRIPTION') || define('DATE_DESCRIPTION','".$site_data['date_description']."');
           defined('TIME_FORMAT') || define('TIME_FORMAT','".$site_data['time_format']."');   
           defined('TIMEZONE_OFFSET') || define('TIMEZONE_OFFSET','".$offset."');   
           defined('CURRENCY_FORMAT') || define('CURRENCY_FORMAT','".$site_data['currency']."');
           defined('PASSWORD_FORMAT') || define('PASSWORD_FORMAT','".$site_data['passwordtype']."');
        ?>";
		try{
			$handle = fopen($filename, "w+");
			fwrite($handle,trim($site_content));
			fclose($handle);
		}
		catch (Exception $e)
		{
			
		}
		}
		
			
	}
        /**
         * This will help to find offset by providing minutes.
         * @param string $offset  = offset in minutes
         * @return string Offset after calculation
         */
        public static function formatOffset($offset) 
        {
            $hours = $offset / 3600;
            $remainder = $offset % 3600;
            $sign = $hours > 0 ? '+' : '-';
            $hour = (int) abs($hours);
            $minutes = (int) abs($remainder / 60);

            if ($hour == 0 AND $minutes == 0) 
            {
                $sign = ' ';
            }
            return $sign . str_pad($hour, 2, '0', STR_PAD_LEFT) .':'. str_pad($minutes,2, '0');
        } 
        /**
         * This function is used to write employee tabs into a php file.
         * @param Array $emptabArray = array consisting of selected employee tabs .
         * @return string Success/failure message.
         */
	public static function generateEmpTabConstants($emptabArray)
	{
		$filename = Zend_Registry::get('emptab_file_path');
		$successflag = "";

		$commaseparatedArray = "";
		$empconfigure = "<?php defined('EMPTABCONFIGS') || define('EMPTABCONFIGS','";
		if(!empty($emptabArray)){
			foreach($emptabArray as $emptab){
				$commaseparatedArray .= $emptab.",";
			}
			$commaseparatedArray = substr($commaseparatedArray, 0, -1);
		}
		$empconfigure .= $commaseparatedArray."');?>";

		try{
			$handle = fopen($filename, "w+");
			fwrite($handle,trim($empconfigure));
			fclose($handle);
			$successflag = "success";
		}
		catch (Exception $e)
		{
			$successflag = "error";
			
		}
		return $successflag;
	}
	/**
	 * This function is used to write email constants to a php file.
	 */
	public static function generateEmailConstants()
	{
		$filename = Zend_Registry::get('emailconstant_file_path');
		$email_model = new Default_Model_Emailcontacts();
		$egroups_data = $email_model->getContactsForConstants();

		$egroup_content = "<?php ";
		foreach($egroups_data as $egroups)
		{
			$egroup_content .= "\ndefined('".preg_replace('/\s/','_',$egroups['group_code'])."_".$egroups['business_unit_id']."') || define('".preg_replace('/\s/','_',$egroups['group_code'])."_".$egroups['business_unit_id']."','".$egroups['groupEmail']."');";
		}
		$egroup_content .= "?>";
		
		$handle = fopen($filename, "w+");
		fwrite($handle,trim($egroup_content));
		fclose($handle);
		
	}
	/**
	 * This function is used to create access control dynamically.
	 */
	public static function generateAccessControl()
	{
        //$filename = Zend_Registry::get('acess_file_path');
		$filename = ACCESS_CONTROL_PATH.SEPARATOR."application".SEPARATOR."modules".SEPARATOR."default".SEPARATOR."plugins".SEPARATOR."AccessControl.php";
		$menu_model = new Default_Model_Menu();
		$role_model = new Default_Model_Roles();
		$storage = new Zend_Auth_Storage_Session();
		$data = $storage->read();

		$controllers = $menu_model->getControllersByRole('1');
		$roles_arr = $role_model->getRoleTypesForAccess();
		
		$acl = self::generateAccessControl_helper($controllers, '1');
		$role_str = "";
		$role_str1 = "";
		foreach($roles_arr as $role_id => $roles)
		{
			$role_str .= "else if(\$role == ".$role_id.")\n\t \$role = '".$roles['roletype']."';\n\t";
			$role_str1 .= "\n\t \$acl->addRole('".$roles['roletype']."');";
		}
		$acl_str = self::generateAccessControl_helper1($acl, $controllers,'admin');
        $acl_str .= self::generateAccessControl_helper5('', SUPERADMINROLE, 'admin');
		$rcontent_roles = self::generateAccessControl_helper2($roles_arr,$menu_model);
		$time_management_str = self::generateAccessControl_helper6($roles_arr);		
		$access_content = "<?php
class Default_Plugin_AccessControl extends Zend_Controller_Plugin_Abstract
{
  private \$_acl,\$id_param;
          
  public function preDispatch(Zend_Controller_Request_Abstract \$request)
  {
	\$storage = new Zend_Auth_Storage_Session();
	\$data = \$storage->read();
	\$role = \$data['emprole'];
	if(\$role == 1)
		\$role = 'admin';
	".$role_str."
  	\$request->getModuleName();
        \$request->getControllerName();
        \$request->getActionName();
    	
        
        \$module = \$request->getModuleName();
	\$resource = \$request->getControllerName();
	\$privilege = \$request->getActionName();
	\$this->id_param = \$request->getParam('id');
	\$allowed = false;
        \$acl = \$this->_getAcl();
	\$moduleResource = \"\$module:\$resource\";
	
	if(\$resource == 'profile')
            \$role = 'viewer';
		
	if(\$resource == 'services')
            \$role = 'services';
		
	if(\$role != '') 
        {
            if (\$acl->has(\$moduleResource)) 
            {						
                \$allowed = \$acl->isAllowed(\$role, \$moduleResource, \$privilege);	
			    	 
            }	 
            if (!\$allowed)//  && \$role !='admin') 
            {				
                \$request->setControllerName('error');
	        \$request->setActionName('error');
            }
	}
  }
  
protected function _getAcl()
{
    if (\$this->_acl == null ) 
    {
	   \$acl = new Zend_Acl();

	   \$acl->addRole('admin');            
	   \$acl->addRole('viewer');            
	   ".$role_str1."
	   \$storage = new Zend_Auth_Storage_Session();
	   \$data = \$storage->read();
	   \$role = \$data['emprole'];
		".$time_management_str."
	   \$acl->addResource(new Zend_Acl_Resource('login:index'));	
	   \$acl->allow('viewer', 'login:index', array('index','confirmlink','forgotpassword','forgotsuccess','login','pass','browserfailure','forcelogout','useractivation'));

	   if(\$role == 1 ) 
	   {				 		    	
			   ".$acl_str."			   		  	   				   
	   }  
	   ".$rcontent_roles."

     // setup acl in the registry for more
           Zend_Registry::set('acl', \$acl);
           \$this->_acl = \$acl;
    }
   return \$this->_acl;
}
  }
  
  ?>";

		$handle = fopen($filename, "w+");
		if(fwrite($handle,$access_content))
                {
                    fclose($handle);
                }
                else 
                {
                    throw new Exception('file permission');
                }
		
	}
        /**
         * This function helps generate access control by providing static employee controllers based on group id.
         * @param integer $group_id  = id of group
         * @param integer $role_id   = id of role
         * @param string $role_type  = role type
         * @return string  Access content of static controllers of employees based on group/role id.
         */
        public static function generateAccessControl_helper5($group_id,$role_id,$role_type)
        {
            $roles['roletype'] = $role_type;
            $rcontent = "";
            if($group_id == HR_GROUP || $group_id == MANAGEMENT_GROUP || $role_id == SUPERADMINROLE)//for HR group and management group
            {
                    $process_controllers = array('processescontroller.php'=>array('url'=>'processes','modulename'=>'default','actions'=>array()));
                    $rcontent .= self::generateAccessControl_helper3($process_controllers, $role_id, $roles['roletype']);
                    //adding interviewrounds
                    $irounds_controllers = array('interviewroundscontroller.php'=>array('url'=>'interviewrounds','modulename'=>'default','actions'=>array()));
                    $rcontent .= self::generateAccessControl_helper3($irounds_controllers, $role_id, $roles['roletype']);
                    //end of adding interview rounds

                    //start of employee related controllers
                    $empperformanceappraisal_controllers = array('empperformanceappraisalcontroller.php'=>array('url'=>'empperformanceappraisal','modulename'=>'default','actions'=>array()));
                    $rcontent .= self::generateAccessControl_helper3($empperformanceappraisal_controllers, $role_id, $roles['roletype']);

                    $emppayslips_controllers = array('emppayslipscontroller.php'=>array('url'=>'emppayslips','modulename'=>'default','actions'=>array()));
                    $rcontent .= self::generateAccessControl_helper3($emppayslips_controllers, $role_id, $roles['roletype']);

                    $empbenefits_controllers = array('empbenefitscontroller.php'=>array('url'=>'empbenefits','modulename'=>'default','actions'=>array()));
                    $rcontent .= self::generateAccessControl_helper3($empbenefits_controllers, $role_id, $roles['roletype']);

                    $emprequisitiondetails_controllers = array('emprequisitiondetailscontroller.php'=>array('url'=>'emprequisitiondetails','modulename'=>'default','actions'=>array()));
                    $rcontent .= self::generateAccessControl_helper3($emprequisitiondetails_controllers, $role_id, $roles['roletype']);

                    $emprenumerationdetails_controllers = array('empremunerationdetailscontroller.php'=>array('url'=>'empremunerationdetails','modulename'=>'default','actions'=>array()));
                    $rcontent .= self::generateAccessControl_helper3($emprenumerationdetails_controllers, $role_id, $roles['roletype']);

                    $empsecuritycredentials_controllers = array('empsecuritycredentialscontroller.php'=>array('url'=>'empsecuritycredentials','modulename'=>'default','actions'=>array()));
                    $rcontent .= self::generateAccessControl_helper3($empsecuritycredentials_controllers, $role_id, $roles['roletype']);

                    $apprreqcandidates_controllers = array('apprreqcandidatescontroller.php'=>array('url'=>'apprreqcandidates','modulename'=>'default','actions'=>array()));
                    $rcontent .= self::generateAccessControl_helper3($apprreqcandidates_controllers, $role_id, $roles['roletype']);

                    
                    
                    //end of employee related controllers
            }
            if($group_id == EMPLOYEE_GROUP || $group_id == HR_GROUP || $group_id == MANAGEMENT_GROUP  || $role_id == SUPERADMINROLE)//for Employee,management ,HR groups
            {
                    //start of emloyee related controllers
                    $personal_controllers = array('emppersonaldetailscontroller.php'=>array('url'=>'emppersonaldetails','modulename'=>'default','actions'=>array()));
                    $rcontent .= self::generateAccessControl_helper3($personal_controllers, $role_id, $roles['roletype']);

                    $employeedocs_controllers = array('employeedocscontroller.php'=>array('url'=>'employeedocs','modulename'=>'default','actions'=>array()));
                    $rcontent .= self::generateAccessControl_helper3($employeedocs_controllers, $role_id, $roles['roletype']);
                    
                    $communication_controllers = array('empcommunicationdetailscontroller.php'=>array('url'=>'empcommunicationdetails','modulename'=>'default','actions'=>array()));
                    $rcontent .= self::generateAccessControl_helper3($communication_controllers, $role_id, $roles['roletype']);

                    $training_controllers = array('trainingandcertificationdetailscontroller.php'=>array('url'=>'trainingandcertificationdetails','modulename'=>'default','actions'=>array()));
                    $rcontent .= self::generateAccessControl_helper3($training_controllers, $role_id, $roles['roletype']);

                    $experience_controllers = array('experiencedetailscontroller.php'=>array('url'=>'experiencedetails','modulename'=>'default','actions'=>array()));
                    $rcontent .= self::generateAccessControl_helper3($experience_controllers, $role_id, $roles['roletype']);

                    $education_controllers = array('educationdetailscontroller.php'=>array('url'=>'educationdetails','modulename'=>'default','actions'=>array()));
                    $rcontent .= self::generateAccessControl_helper3($education_controllers, $role_id, $roles['roletype']);

                    $medical_controllers = array('medicalclaimscontroller.php'=>array('url'=>'medicalclaims','modulename'=>'default','actions'=>array()));
                    $rcontent .= self::generateAccessControl_helper3($medical_controllers, $role_id, $roles['roletype']);

                    $leaves_controllers = array('empleavescontroller.php'=>array('url'=>'empleaves','modulename'=>'default','actions'=>array()));
                    $rcontent .= self::generateAccessControl_helper3($leaves_controllers, $role_id, $roles['roletype']);

                    $skills_controllers = array('empskillscontroller.php'=>array('url'=>'empskills','modulename'=>'default','actions'=>array()));
                    $rcontent .= self::generateAccessControl_helper3($skills_controllers, $role_id, $roles['roletype']);

                    $disability_controllers = array('disabilitydetailscontroller.php'=>array('url'=>'disabilitydetails','modulename'=>'default','actions'=>array()));
                    $rcontent .= self::generateAccessControl_helper3($disability_controllers, $role_id, $roles['roletype']);

                    $weligibility_controllers = array('workeligibilitydetailscontroller.php'=>array('url'=>'workeligibilitydetails','modulename'=>'default','actions'=>array()));
                    $rcontent .= self::generateAccessControl_helper3($weligibility_controllers, $role_id, $roles['roletype']);

                    $additionaldetails_controllers = array('empadditionaldetailscontroller.php'=>array('url'=>'empadditionaldetails','modulename'=>'default','actions'=>array()));
                    $rcontent .= self::generateAccessControl_helper3($additionaldetails_controllers, $role_id, $roles['roletype']);

                    $visa_controllers = array('visaandimmigrationdetailscontroller.php'=>array('url'=>'visaandimmigrationdetails','modulename'=>'default','actions'=>array()));
                    $rcontent .= self::generateAccessControl_helper3($visa_controllers, $role_id, $roles['roletype']);

                    $creditcard_controllers = array('creditcarddetailscontroller.php'=>array('url'=>'creditcarddetails','modulename'=>'default','actions'=>array()));
                    $rcontent .= self::generateAccessControl_helper3($creditcard_controllers, $role_id, $roles['roletype']);

                    $dependency_controllers = array('dependencydetailscontroller.php'=>array('url'=>'dependencydetails','modulename'=>'default','actions'=>array()));
                    $rcontent .= self::generateAccessControl_helper3($dependency_controllers, $role_id, $roles['roletype']);

                    $empholidays_controllers = array('empholidayscontroller.php'=>array('url'=>'empholidays','modulename'=>'default','actions'=>array()));
                    $rcontent .= self::generateAccessControl_helper3($empholidays_controllers, $role_id, $roles['roletype']);

                    $empjobhistory_controllers = array('empjobhistorycontroller.php'=>array('url'=>'empjobhistory','modulename'=>'default','actions'=>array()));
                    $rcontent .= self::generateAccessControl_helper3($empjobhistory_controllers, $role_id, $roles['roletype']);
                    
                    $empassetdetails_controllers = array('assetdetailscontroller.php'=>array('url'=>'assetdetails','modulename'=>'default','actions'=>array()));
                    $rcontent .= self::generateAccessControl_helper3($empassetdetails_controllers, $role_id, $roles['roletype']);
                    
                    if($group_id != EMPLOYEE_GROUP)
                    {
                            $empsalarydetails_controllers = array('empsalarydetailscontroller.php'=>array('url'=>'empsalarydetails','modulename'=>'default','actions'=>array()));
                            $rcontent .= self::generateAccessControl_helper3($empsalarydetails_controllers, $role_id, $roles['roletype']);
                    }
                    //end of emloyee related controllers
            }
            if($group_id == SYSTEMADMIN_GROUP)//for system admin group
            {
                    $managemenu_controllers = array('managemenuscontroller.php'=>array('url'=>'managemenus','modulename'=>'default','actions'=>array()));
                    $rcontent .= self::generateAccessControl_helper3($managemenu_controllers, $role_id, $roles['roletype']);
            }
            if($group_id == USERS_GROUP)//for users group
            {
                    $process_controllers = array('processescontroller.php'=>array('url'=>'processes','modulename'=>'default','actions'=>array()));
                    $process_acl = self::generateAccessControl_helper($process_controllers, $role_id);
                    $process_acl['processescontroller.php'] = array_combine($process_acl['processescontroller.php'],$process_acl['processescontroller.php']);
                    
                    unset($process_acl['processescontroller.php']['addpopup']);
                    unset($process_acl['processescontroller.php']['delete']);

                    $process_acl_str = self::generateAccessControl_helper1($process_acl, $process_controllers,$roles['roletype']);
                    $rcontent .= $process_acl_str;

            }
            if($group_id == SYSTEMADMIN_GROUP  || $group_id == MANAGER_GROUP)//for system admin,manager groups
            {
                    //start of emloyee related controllers
                    $personal_controllers = array('emppersonaldetailscontroller.php'=>array('url'=>'emppersonaldetails','modulename'=>'default','actions'=>array()));
                    $rcontent .= self::generateAccessControl_helper4($personal_controllers, $role_id, $roles['roletype']);
                    
                    $employeedocs_controllers = array('employeedocscontroller.php'=>array('url'=>'employeedocs','modulename'=>'default','actions'=>array()));
                    $rcontent .= self::generateAccessControl_helper3($employeedocs_controllers, $role_id, $roles['roletype']);

                    $communication_controllers = array('empcommunicationdetailscontroller.php'=>array('url'=>'empcommunicationdetails','modulename'=>'default','actions'=>array()));
                    $rcontent .= self::generateAccessControl_helper4($communication_controllers, $role_id, $roles['roletype']);

                    $training_controllers = array('trainingandcertificationdetailscontroller.php'=>array('url'=>'trainingandcertificationdetails','modulename'=>'default','actions'=>array()));
                    $rcontent .= self::generateAccessControl_helper3($training_controllers, $role_id, $roles['roletype']);

                    $experience_controllers = array('experiencedetailscontroller.php'=>array('url'=>'experiencedetails','modulename'=>'default','actions'=>array()));
                    $rcontent .= self::generateAccessControl_helper3($experience_controllers, $role_id, $roles['roletype']);

                    $education_controllers = array('educationdetailscontroller.php'=>array('url'=>'educationdetails','modulename'=>'default','actions'=>array()));
                    $rcontent .= self::generateAccessControl_helper3($education_controllers, $role_id, $roles['roletype']);

                    $medical_controllers = array('medicalclaimscontroller.php'=>array('url'=>'medicalclaims','modulename'=>'default','actions'=>array()));
                    $rcontent .= self::generateAccessControl_helper3($medical_controllers, $role_id, $roles['roletype']);

                    $leaves_controllers = array('empleavescontroller.php'=>array('url'=>'empleaves','modulename'=>'default','actions'=>array()));
                    $rcontent .= self::generateAccessControl_helper4($leaves_controllers, $role_id, $roles['roletype']);

                    $skills_controllers = array('empskillscontroller.php'=>array('url'=>'empskills','modulename'=>'default','actions'=>array()));
                    $rcontent .= self::generateAccessControl_helper3($skills_controllers, $role_id, $roles['roletype']);

                    $disability_controllers = array('disabilitydetailscontroller.php'=>array('url'=>'disabilitydetails','modulename'=>'default','actions'=>array()));
                    $rcontent .= self::generateAccessControl_helper4($disability_controllers, $role_id, $roles['roletype']);

                    $weligibility_controllers = array('workeligibilitydetailscontroller.php'=>array('url'=>'workeligibilitydetails','modulename'=>'default','actions'=>array()));
                    $rcontent .= self::generateAccessControl_helper4($weligibility_controllers, $role_id, $roles['roletype']);

                    $visa_controllers = array('visaandimmigrationdetailscontroller.php'=>array('url'=>'visaandimmigrationdetails','modulename'=>'default','actions'=>array()));
                    $rcontent .= self::generateAccessControl_helper3($visa_controllers, $role_id, $roles['roletype']);

                    $creditcard_controllers = array('creditcarddetailscontroller.php'=>array('url'=>'creditcarddetails','modulename'=>'default','actions'=>array()));
                    $rcontent .= self::generateAccessControl_helper4($creditcard_controllers, $role_id, $roles['roletype']);

                    $dependency_controllers = array('dependencydetailscontroller.php'=>array('url'=>'dependencydetails','modulename'=>'default','actions'=>array()));
                    $rcontent .= self::generateAccessControl_helper3($dependency_controllers, $role_id, $roles['roletype']);

                    $empholidays_controllers = array('empholidayscontroller.php'=>array('url'=>'empholidays','modulename'=>'default','actions'=>array()));
                    $rcontent .= self::generateAccessControl_helper4($empholidays_controllers, $role_id, $roles['roletype']);

                    $empjobhistory_controllers = array('empjobhistorycontroller.php'=>array('url'=>'empjobhistory','modulename'=>'default','actions'=>array()));
                    $rcontent .= self::generateAccessControl_helper3($empjobhistory_controllers, $role_id, $roles['roletype']);

                    $empadditionaldetails_controllers = array('empadditionaldetailscontroller.php'=>array('url'=>'empadditionaldetails','modulename'=>'default','actions'=>array()));
                    $rcontent .= self::generateAccessControl_helper3($empadditionaldetails_controllers, $role_id, $roles['roletype']);
                    
                    $empassetdetails_controllers = array('assetdetailscontroller.php'=>array('url'=>'assetdetails','modulename'=>'default','actions'=>array()));
                    $rcontent .= self::generateAccessControl_helper4($empassetdetails_controllers, $role_id, $roles['roletype']);

                    //end of emloyee related controllers
            }
            if($group_id == MANAGER_GROUP || $group_id == EMPLOYEE_GROUP || $group_id == SYSTEMADMIN_GROUP)//for manager,management groups
            {
                    //adding interviewrounds
                    $irounds_controllers = array('interviewroundscontroller.php'=>array('url'=>'interviewrounds','modulename'=>'default','actions'=>array()));
                    $rcontent .= self::generateAccessControl_helper3($irounds_controllers, $role_id, $roles['roletype']);
                    //end of adding interview rounds
            }
            if($group_id == MANAGER_GROUP || $group_id == EMPLOYEE_GROUP || $group_id == SYSTEMADMIN_GROUP)
            {
                    $apprreqcandidates_controllers = array('apprreqcandidatescontroller.php'=>array('url'=>'apprreqcandidates','modulename'=>'default','actions'=>array()));
                    $rcontent .= self::generateAccessControl_helper4($apprreqcandidates_controllers, $role_id, $roles['roletype']);
            }
            if($group_id == MANAGEMENT_GROUP  || $role_id == SUPERADMINROLE)//for enabling logs for management
            {
                $logmanager_controllers = array('logmanagercontroller.php'=>array('url'=>'logmanager','modulename'=>'default','actions'=>array()));
                $rcontent .= self::generateAccessControl_helper3($logmanager_controllers, $role_id, $roles['roletype']);

                $userloginlog_controllers = array('userloginlogcontroller.php'=>array('url'=>'userloginlog','modulename'=>'default','actions'=>array()));
                $rcontent .= self::generateAccessControl_helper3($userloginlog_controllers, $role_id, $roles['roletype']);
            }
            return $rcontent;
        }
	/**
	 * @author : K.Rama Krishna
	 * This will generate resource content of all roles except admin.
	 *
	 * @parameters
	 * @param array $roles_arr     = array of all roles except admin(i,e 1)
	 * @param object $menu_model    = menu model object.
	 *
	 * @return string   String of resource content that will write to AccessControl.php
	 */
	public static function generateAccessControl_helper2($roles_arr,$menu_model)
	{
		$rcontent = "";
		foreach($roles_arr as $role_id => $roles)
		{
			$group_id = $roles['group_id'];
			$rcontent .= "if(\$role == ".$role_id." )
           {";
			$controllers = $menu_model->getControllersByRole($role_id,$group_id);
			
			$acl = self::generateAccessControl_helper($controllers, $role_id);
			$acl_str = self::generateAccessControl_helper1($acl, $controllers,$roles['roletype']);
			$rcontent .= $acl_str;
			//start of adding static controllers to accesscontrol
			$rcontent .= self::generateAccessControl_helper5($group_id,$role_id,$roles['roletype']);
			//end of adding static controllers to accesscontrol
			$rcontent .= "}";
		}
		return $rcontent;
	}
	/**
	 * This will help generateAccesscontrol function that will give resource content
	 * of static controllers.
	 * @param Array $controllers  =  array of controllers.
	 * @param String $role_id     =  id of role.
	 * @param String $roletype    =  type of role.
	 * @return String Resource content.
	 */
	public static function generateAccessControl_helper4($controllers,$role_id,$roletype)
	{
		$acl_str = '';
		$acl = self::generateAccessControl_helper($controllers, $role_id);
		$controllers[key($acl)]['actions'] = array_combine($acl[key($acl)],$acl[key($acl)]);
		unset($controllers[key($acl)]['actions']['edit']);
		unset($controllers[key($acl)]['actions']['delete']);
		unset($controllers[key($acl)]['actions']['addpopup']);
		unset($controllers[key($acl)]['actions']['editpopup']);
		unset($controllers[key($acl)]['actions']['add']);
		
		$acl_str = self::generateAccessControl_helper1($acl, $controllers,$roletype);
		return $acl_str;
	}
	/**
	 * This will help generateAccesscontrol function that will give resource content
	 * of static controllers.
	 * @param Array $controllers  =  array of controllers.
	 * @param String $role_id     =  id of role.
	 * @param String $roletype    =  type of role.
	 * @return String Resource content.
	 */
	public static function generateAccessControl_helper3($controllers,$role_id,$roletype)
	{
		$acl = self::generateAccessControl_helper($controllers, $role_id);
                if(count($acl) > 0)
                {
                    $controllers[key($acl)]['actions'] = $acl[key($acl)];
                    $acl_str = self::generateAccessControl_helper1($acl, $controllers,$roletype);
                    return $acl_str;
                }
                else 
                    return "";
	}
	/**
	 * This will help generateAccessControl function that will give resource
	 * content to write in AccessControl.php
	 *
	 * @parameters
	 * @param array $acl            =  array of generated controllers and permissions using helper function.
	 * @param array $controllers    =  array of controllers and permissions assigned to a role
	 * @param string $role_type      =  role type.
	 *
	 * @return  string   String of resource content.
	 */
	public static function generateAccessControl_helper1($acl,$controllers,$role_type)
	{
		$acl_str = "";
		foreach($acl as $con_name => $act_arr)
		{
			$moduleName = isset($controllers[$con_name]['modulename'])?$controllers[$con_name]['modulename']:'default';
			if($role_type != 'admin')
			{
				if(($controllers[$con_name]['url'] != 'index') && ($controllers[$con_name]['url'] != 'dashboard'))
				{
                                    
					$diff_arr = array_diff($act_arr,$controllers[$con_name]['actions']);
					if(count($diff_arr) == 0)
					$diff_arr = $act_arr;
					$diff_arr = array_combine($diff_arr, $diff_arr);
					unset($diff_arr['edit']);
                                        unset($diff_arr['add']);
					unset($diff_arr['delete']);
					$final_act_arr = $diff_arr+$controllers[$con_name]['actions'];
					
                                        
                                    
					if(in_array('add', $controllers[$con_name]['actions']) && !in_array('edit',$controllers[$con_name]['actions']))
					{
						
						$action_str = implode("','", $final_act_arr);

						$acl_str .= "\n\t\t \$acl->addResource(new Zend_Acl_Resource('".$moduleName.":".$controllers[$con_name]['url']."'));
                            \$".$controllers[$con_name]['url']."_add = 'yes';
                                if(\$this->id_param == '' && \$".$controllers[$con_name]['url']."_add == 'yes')
                                    \$acl->allow('".$role_type."','".$moduleName.":".$controllers[$con_name]['url']."', array('".$action_str."','edit'));\n
                                else
                                    \$acl->allow('".$role_type."','".$moduleName.":".$controllers[$con_name]['url']."', array('".$action_str."'));\n
                                ";
					}
					else
					{
						$action_str = implode("','", $final_act_arr);
						$acl_str .= "\n\t\t \$acl->addResource(new Zend_Acl_Resource('".$moduleName.":".$controllers[$con_name]['url']."'));
                            \$acl->allow('".$role_type."', '".$moduleName.":".$controllers[$con_name]['url']."', array('".$action_str."'));\n";
					}
				}
				else
				{
					$action_str = implode("','", $act_arr);
					$acl_str .= "\n\t\t \$acl->addResource(new Zend_Acl_Resource('".$moduleName.":".$controllers[$con_name]['url']."'));
                        \$acl->allow('".$role_type."', '".$moduleName.":".$controllers[$con_name]['url']."', array('".$action_str."'));\n";
				}
			}
			else
			{
				$action_str = implode("','", $act_arr);
				$acl_str .= "\n\t\t \$acl->addResource(new Zend_Acl_Resource('".$moduleName.":".$controllers[$con_name]['url']."'));
                    \$acl->allow('".$role_type."', '".$moduleName.":".$controllers[$con_name]['url']."', array('".$action_str."'));\n";
			}
			
		}
		
		return $acl_str;
	}
	/**
	 * This will help generateAccessControl function that will give filtered controllers
	 * and actions related to particular role.
	 *
	 * @parameters
	 * @param Array $controllers    =  array of controllers and permissions assigned to a role
	 * @param Integer $role_id        =  id of role
	 *
	 *
	 * @return array    Array of filtered controllers and actions.
	 */
	public static function generateAccessControl_helper($controllers,$role_id)
	{
		$front = Zend_Controller_Front::getInstance()->getControllerDirectory();
		$acl = array();
		unset($front['services']);
		
		foreach ($front as $module => $path)
		{
			foreach (scandir($path) as $file)
			{

				if(array_key_exists(strtolower($file),$controllers))
				{

					include_once $path . DIRECTORY_SEPARATOR . $file;

					foreach (get_declared_classes() as $class)
					{

						
						if (is_subclass_of($class, 'Zend_Controller_Action') && (strtolower($class) == strtolower($module)."_".$controllers[strtolower($file)]['url']."controller"))
						{
							$controller = strtolower(substr($class, 0, strpos($class, "Controller")));

							$actions = array();

							foreach (get_class_methods($class) as $action)
							{
								if (strstr($action, "Action") !== false)
								{
									$actions[] = substr($action, 0, -6);
								}
							}
							if($role_id == 1)
							{

								$acl[$module][strtolower($file)] = $actions;

							}
							else
							{
								$acl[$module][strtolower($file)] = $actions;
								
							}
						}
					}
				}
			}
		}
		
		
		
		//if(isset($acl['default']))
			//$acl = $acl['default'];
		
		$aacl=array();
		if(isset($acl['default']))
		$aacl = $acl['default'];
		if(isset($acl['assets'])) {
		unset($acl['assets']['indexcontroller.php']);
		$aacl = $aacl+$acl['assets'];
		}
		if(isset($acl['expenses'])) {
			unset($acl['expenses']['indexcontroller.php']);
			$aacl = $aacl+$acl['expenses'];
		}
		if(isset($acl['exit'])) {
			unset($acl['exit']['indexcontroller.php']);
			$aacl = $aacl+$acl['exit'];
		}

		//return $acl;
		return $aacl;
	}
        /**
         * This function is used to send mail.
         * @param Array $options  = data that needed to send email
         * @return string On success id ,on failure error message.
         */
	public static function _sendEmail($options)
	{
		$email_model = new Default_Model_EmailLogs();
		$date = new Zend_Date();
		if(is_array($options['toEmail'])) $toemailData = implode(',',$options['toEmail']);
		else
		$toemailData = $options['toEmail'];
		$data = array(
			'toEmail' 			=> 		$toemailData,
            'toName' =>isset($options['toName'])?$options['toName']:NULL,
			'emailsubject' 		=> 		$options['subject'],
			'header' 			=> 		$options['header'],
			'message' 			=> 		$options['message'],
			'createddate' 		=> 		$date->get('yyyy-MM-dd HH:mm:ss'),
			'modifieddate'		=>		$date->get('yyyy-MM-dd HH:mm:ss')
		);
		$empArrList = '';
		if(isset($options['cc'])) $data['cc'] 		= 	$options['cc'];
		if(isset($options['bcc'])) 
		{
			if(!empty($options['bcc']))
			{
				$empArrList = implode(',',$options['bcc']);
			}
			$data['bcc']		= 	$empArrList;
			//$options['bcc']     =  $empArrList;
		}
		
		 $id = $email_model->SaveorUpdateEmailData($data,''); 
		if(!isset($options['cron']))
		{ 
			
			//echo "<pre>";print_r($options);
			$mail_status = sapp_Mail::_email($options);

			$where = array('id=?'=>$id);
			$newdata['modifieddate'] = $date->get('yyyy-MM-dd HH:mm:ss');
			$newdata['is_sent'] = 1;
			if($mail_status === true)
			{
				$id = $email_model->SaveorUpdateEmailData($newdata,$where);
				return $id;
			}
			else
			return "fail";
		}
	}
        /**
         * This function is used to get all week days in an array.
         * @return Array Array of week days.
         */
	public static function _weekdays()
	{
		$weekArr = array(
			'0'   => 'Sunday',
			'1'   => 'Monday',
			'2'   => 'Tuesday',
			'3'   => 'Wednesday',
			'4'   => 'Thursday',
			'5'   => 'Friday',
			'6'	  => 'Saturday'
			);
			return $weekArr;
	}
        /**
         * This function is used to check privileges of a menu item of a particular login.
         * @param Integer $objectId = id of the menu item.
         * @param Integer $groupId  = group id
         * @param Integer $roleId   = role id
         * @param String $action    = action like add,edit,view ...
         * @return string  Returns Yes/No
         */
	public static function _checkprivileges($objectId,$groupId='',$roleId,$action)
	{
            $menuID = $objectId;
            $privilege_model = new Default_Model_Privileges();
            $privilegesofObj = $privilege_model->getObjPrivileges($menuID,$groupId,$roleId);
            if($action == 'add') return $privilegesofObj['addpermission'];
            else if($action == 'edit') return $privilegesofObj['editpermission'];
            else if($action == 'view') return $privilegesofObj['viewpermission'];
            else if($action == 'delete') return $privilegesofObj['deletepermission'];
            else if($action == 'uploadattachments') return $privilegesofObj['uploadattachments'];
            else if($action == 'viewattachments') return $privilegesofObj['viewattachments'];
            else return 'No';
	}
        /**
         * This function is used to check privileges of a menu item of a particular login.
         * @param Integer $objectId = id of the menu item.
         * @param Integer $groupId  = group id
         * @param Integer $roleId   = role id
         * @return string  Returns Yes/No
         */
        public static function _check_menu_access($objectId,$groupId='',$roleId)
        {            
            $privilege_model = new Default_Model_Privileges();
            $privilegesofObj = $privilege_model->getObjPrivileges($objectId,$groupId,$roleId);
            $result = "No";
            if(!empty($privilegesofObj) && count($privilegesofObj) > 0)
            {
                $result = "Yes";
            }
            return $result;
        }
        /**
         * This function is used to get global offset of logined user.
         * @return string Offset of logged user.
         */
	public static function _getGlobalOffset()
	{
		/*to get the client ip*/

		if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
		{
			$v=$_SERVER['HTTP_CLIENT_IP'];
		}
		elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
		{
			$v=$_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		else
		{
			$v=$_SERVER['REMOTE_ADDR'];
		}

		$DATE_CONSTANT =  gmdate("Y-m-d H:i:s");

		

		$intTemp  = 0;
		// Temporarily assigned . Need to work on it. 14032014
		$gmt['TIMEZONE'] = DEFAULT_GMT_OFFSET;
		if(!empty($gmt)){
			if(!empty($gmt['TIMEZONE'])){
				$intTemp = $gmt['TIMEZONE'];
			}else{
				$intTemp = DEFAULT_GMT_OFFSET;
			}
		}else{
			$intTemp = DEFAULT_GMT_OFFSET;
		}
		return $intTemp;
	}
        /**
         * This function is used to get the date w.r.t offset and format.
         * @param Date $orgDate   = date to be changed.
         * @param string $format  = format of the date.
         * @return Date  Formatted date.
         */
	public static function getDisplayDate($orgDate,$format='Y-m-d H:i:s')
	{
            $format = DATEFORMAT_PHP;

            
            $gmtOffset = !defined('TIMEZONE_OFFSET')?DEFAULT_GMT_OFFSET:TIMEZONE_OFFSET;
            $orgDateInSec = strtotime($orgDate);
            $offsetInMin = sapp_Global::hoursToMinutes($gmtOffset);

            $gmtOffsetInSec = $offsetInMin * 60;

            $totalOrgDate = $orgDateInSec + $gmtOffsetInSec;
            $dateFormat = $format.' \a\\t '.TIME_FORMAT;

            $finalDateTime = date($dateFormat,$totalOrgDate);
            return $finalDateTime;
	}
        
        /**
         * This function used to convert gmt time to offset based time.
         * @param string $orgDate = time to be converted.
         * @return string  Converted time
         */
        public static function getDisplaySDTime($orgDate)
	{                       
            $gmtOffset = !defined('TIMEZONE_OFFSET')?DEFAULT_GMT_OFFSET:TIMEZONE_OFFSET;
            $orgDateInSec = strtotime($orgDate);
            $offsetInMin = sapp_Global::hoursToMinutes($gmtOffset);

            $gmtOffsetInSec = $offsetInMin * 60;

            $totalOrgDate = $orgDateInSec + $gmtOffsetInSec;
            $dateFormat = TIME_FORMAT;

            $finalDateTime = date($dateFormat,$totalOrgDate);
            return $finalDateTime;
	}
        /**
         * This function is used to change the date into database format with offset.
         * @param Date $orgDate  = date to be changed.
         * @return Date  Formatted date.
         */
        public static function getGMTformatdate($orgDate)
        {
            $gmtOffset = sapp_Global::_getGlobalOffset();			
            $orgDateInSec = strtotime($orgDate);
            $offsetInMin = sapp_Global::hoursToMinutes($gmtOffset);
            $gmtOffsetInSec = $offsetInMin * 60;
            $totalOrgDate = $orgDateInSec + $gmtOffsetInSec;
            $finalDateTime = date('Y-m-d',$totalOrgDate);
            return $finalDateTime;
        }


	public static function hoursToMinutes($hours)
	{
		if (strstr($hours, ':'))
		{
			# Split hours and minutes.
			$separatedData = explode(':', $hours);

			$minutesInHours    = $separatedData[0] * 60;
			$minutesInDecimals = $separatedData[1];

			$totalMinutes = $minutesInHours + $minutesInDecimals;
		}
		else
		{
			
			$totalMinutes = ceil($hours * 60);
		}
		return $totalMinutes;
	}

	public static function getTimeSummary($time, $timeBase = false) {

		$timeBase = strtotime( sapp_Global::getDisplayDate(gmdate("Y-m-d H:i:s")));
		
		if ($time <= $timeBase) {
			$dif = $timeBase - $time;

			if ($dif < 60) {
				if ($dif < 2) {
					return "1 second ago";
				}

				return $dif." seconds ago";
			}

			if ($dif < 3600) {
				if (floor($dif / 60) < 2) {
					return "A minute ago";
				}

				return floor($dif / 60)." minutes ago";
			}

			if (date("d n Y", $timeBase) == date("d n Y", $time)) {
				return "Today at ".date("g:i A", $time);
			}

			if (date("n Y", $timeBase) == date("n Y", $time) && date("d", $timeBase) - date("d", $time) == 1) {
				return "Yesterday at ".date("g:i A", $time);
			}

			if (date("Y", $time) == date("Y", $timeBase)) {
				
				//changed by rakesh for making pm to PM
				$finalDateFormat = date('F d, Y',$time)." at ".date('h:i A',$time);
				return  $finalDateFormat;
			}
		}
		else {
			$dif = $time - $timeBase;

			if ($dif < 60) {
				if ($dif < 2) {
					return "1 second";
				}

				return $dif." seconds";
			}

			if ($dif < 3600) {
				if (floor($dif / 60) < 2) {
					return "Less than a minute";
				}

				return floor($dif / 60)." minutes";
			}

			if (date("d n Y", ($timeBase + 86400)) == date("d n Y", ($time))) {
				return "Tomorrow, at ".date("g:i A", $timeBase);
			}
		}

		
		return date("F j, Y \a\\t g:i A", $time);
	}

	public static function _getPageShortcutFlag($controllerName)
	{
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$loginUserId = $auth->getStorage()->read()->id;
		}
		$request = Zend_Controller_Front::getInstance();
		$module = $request->getRequest()->getModuleName();
		$menumodel = new Default_Model_Menu();
		$settingsmodel = new Default_Model_Settings();
		$settingsdiv = '';
		
		if($module=='default') {
			$class = '';
			$menuidArr = $menumodel->getMenuObjID('/'.$controllerName);
		}else{
			$class="moduleclass";
			$menuidArr = $menumodel->getMenuObjID('/'.$module.'/'.$controllerName);
		}	
		
		if(!empty($menuidArr) && $controllerName != 'servicerequests')
		{
			$menuID = $menuidArr[0]['id'];
			if($menuID !='')
			{
				$settingsmenuArr = $settingsmodel->getMenuIds($loginUserId,2);
				
				if(!empty($settingsmenuArr))
				{
					$settingsmenustring = $settingsmenuArr[0]['menuid'];
					$settingsmenuArray = explode(",",$settingsmenustring);

					if(sizeof($settingsmenuArray) <= 16)
					{
						if(in_array($menuID,$settingsmenuArray))
						{
							
                                                        $settingsdiv = '<div id="pageshortcut" class = "sprite remove-shortcut-icon '.$class.'" onclick="createorremoveshortcut('.$menuID.',2)">Unpin from shortcuts';
							$settingsdiv .='</div>';

						}
						else
						{
							
                                                        $settingsdiv = '<div id="pageshortcut" class ="sprite shortcut-icon '.$class.'" onclick="createorremoveshortcut('.$menuID.',1)">Pin to shortcuts';
							$settingsdiv .='</div>';
						}
					}
				}
				else
				{
					
                                        $settingsdiv = '<div id="pageshortcut" class ="sprite shortcut-icon '.$class.'" onclick="createorremoveshortcut('.$menuID.',3)">Pin to shortcuts';
					$settingsdiv .='</div>';
				}
			}

		}
		return $settingsdiv;
		
	}

	// To download a file
	public static function downloadFile($file=''){
		if(file_exists($file)){
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename='.basename($file));
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: '.filesize($file));
			ob_clean();
			flush();
			readfile($file);
			exit;
		}else{
			return array('message' => 'File not found. Please check the path is correct and file exists.');
		}
	}

	// To download reports
	public static function downloadReport($file=''){
		if(file_exists($file)){
			// jQuery File Download - START
		    header('Set-Cookie: fileDownload=true; path=/');
			header('Cache-Control: max-age=60, must-revalidate');
			header("Content-type: application/pdf");
			header('Content-Disposition: attachment; filename='.basename($file));
			// jQuery File Download - END
			
			header('Content-Description: File Transfer');
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Pragma: public');
			header('Content-Length: '.filesize($file));
			ob_clean();
			flush();
			readfile($file);
			exit;
		}else{
			return array('message' => 'File not found. Please check the path is correct and file exists.');
		}
	}
	
	public static function getbudeptname($appraisalid)
    {
    	$appInitModel = new Default_Model_Appraisalinit();
    	$businessunitmodel = new Default_Model_Businessunits();
    	$deptmodel = new Default_Model_Departments();
    	$buname = '';
    	$deptname ='';
    	$perf_impl_flag = '';
    	$appraisaldataArr = array();
    	if($appraisalid)
    	{
    		$appraisaldataArr = $appInitModel->getAppDataById($appraisalid);
    		if(!empty($appraisaldataArr))
    		{
    			if($appraisaldataArr['businessunit_id']!='')
    			{
					$buDataArr = $businessunitmodel->getSingleUnitData($appraisaldataArr['businessunit_id']);
					// $perfimplementation = $appInitModel->check_performance_implmentation($appraisaldataArr['businessunit_id']);
					if(!empty($buDataArr))
					{
						$buname = $buDataArr['unitname'];
					}
					/*if(!empty($perfimplementation))
					{
						$perf_impl_flag = $perfimplementation['performance_app_flag'];
					}*/
					$perf_impl_flag = isset($appraisaldataArr['performance_app_flag'])?$appraisaldataArr['performance_app_flag']:1;
    			}
    			if($perf_impl_flag == 0)
    			{	
					if($appraisaldataArr['department_id']!='')
						$deptArr = $deptmodel->getSingleDepartmentData($appraisaldataArr['department_id']);

					if(!empty($deptArr))
					{
						$deptname = $deptArr['deptname'];
					}	
    			}		
    		}
    	}
    	
    	return array('buname' => $buname,'deptname'=>$deptname,'perf_app_flag'=>$perf_impl_flag,'appdata'=>$appraisaldataArr);
    
    }
	
	public static function smartresizeimage($file,
	$width              = 0,
	$height             = 0,
	$proportional       = false,
	$output             = 'file',
	$delete_original    = false,
	$use_linux_commands = false )
	{

		if ( $height <= 0 && $width <= 0 ) return false;

		# Setting defaults and meta
		$info                         = getimagesize($file);
		$image                        = '';
		$final_width                  = 0;
		$final_height                 = 0;
		list($width_old, $height_old) = $info;

		# Calculating proportionality
		if ($proportional)
		{
			if      ($width  == 0)  $factor = $height/$height_old;
			elseif  ($height == 0)  $factor = $width/$width_old;
			else                    $factor = min( $width / $width_old, $height / $height_old );

			$final_width  = round( $width_old * $factor );
			$final_height = round( $height_old * $factor );
		}
		else
		{
			$final_width = ( $width <= 0 ) ? $width_old : $width;
			$final_height = ( $height <= 0 ) ? $height_old : $height;
		}

		# Loading image to memory according to type
		switch ( $info[2] ) {
			case IMAGETYPE_GIF:   $image = imagecreatefromgif($file);   break;
			case IMAGETYPE_JPEG:  $image = imagecreatefromjpeg($file);  break;
			case IMAGETYPE_PNG:   $image = imagecreatefrompng($file);   break;
			default: return false;
		}


		# This is the resizing/resampling/transparency-preserving magic
		$image_resized = imagecreatetruecolor( $final_width, $final_height );
		if ( ($info[2] == IMAGETYPE_GIF) || ($info[2] == IMAGETYPE_PNG) )
		{
			$transparency = imagecolortransparent($image);

			if ($transparency >= 0) {
				$transparent_color  = imagecolorsforindex($image, $trnprt_indx);
				$transparency       = imagecolorallocate($image_resized, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);
				imagefill($image_resized, 0, 0, $transparency);
				imagecolortransparent($image_resized, $transparency);
			}
			elseif ($info[2] == IMAGETYPE_PNG) {
				imagealphablending($image_resized, false);
				$color = imagecolorallocatealpha($image_resized, 0, 0, 0, 127);
				imagefill($image_resized, 0, 0, $color);
				imagesavealpha($image_resized, true);
			}
		}
		imagecopyresampled($image_resized, $image, 0, 0, 0, 0, $final_width, $final_height, $width_old, $height_old);

		# Taking care of original, if needed
		if ( $delete_original ) {
			if ( $use_linux_commands ) exec('rm '.$file);
			else @unlink($file);
		}

		# Preparing a method of providing result
		switch ( strtolower($output) ) {
			case 'browser':
				$mime = image_type_to_mime_type($info[2]);
				header("Content-type: $mime");
				$output = NULL;
				break;
			case 'file':
				$output = $file;
				break;
			case 'return':
				return $image_resized;
				break;
			default:
				break;
		}

		# Writing image according to type to the output destination
		switch ( $info[2] ) {
			case IMAGETYPE_GIF:   imagegif($image_resized, $output);    break;
			case IMAGETYPE_JPEG:  imagejpeg($image_resized, $output);   break;
			case IMAGETYPE_PNG:   imagepng($image_resized, $output);    break;
			default: return false;
		}

		return true;
	}
	
	public static function aasort(&$array, $key)
	{
		$sorter=array();
		$ret=array();
		reset($array);
		foreach ($array as $ii => $va)
		{
			$sorter[$ii]=$va[$key];
		}
		asort($sorter);
		foreach ($sorter as $ii => $va) 
		{
			$ret[$ii]=$array[$ii];
		}
		return $array=$ret;
	}
	public static function removeElementWithValue($array, $key, $value)
	{
		foreach($array as $subKey => $subArray)
		{
			if($subArray[$key] == $value){
				unset($array[$subKey]);
			}
		}
		return $array;
	}

	public static function clean_output_buffer(){
		ob_end_clean();
	} 
	
	 /**
	 * This function is used to check whether a menu is activated or inactivated.
	 * @param Integer $objectId = id of the menu item.
	 * @return string  Returns true if isactive = 1 and false if isactive != 1
	 */
	public static function _isactivemodule($objectId)
	{
            $menuID = $objectId;
            $menu_model = new Default_Model_Menu();
            $menuobj = $menu_model->checkmenustatus($menuID);            
			if($menuobj['isactive'] == '1')  return true;
			else return false;
	}
	
	public static function buildlocations($form,$wizardData)
	{
		$countriesModel = new Default_Model_Countries();
        $statesmodel = new Default_Model_States();
        $citiesmodel = new Default_Model_Cities();
    	$countryId = '';
    	$stateId = '';
    	$cityId = '';
    	$new_stateId = '';
    	$new_cityId = '';
    	
    				if(isset($wizardData['country']) && $wizardData['country'] !='null')
                	  $countryId = $wizardData['country'];
                    if(isset($wizardData['state']) && $wizardData['state'] !='null')
                	  $stateId = $wizardData['state'];
                	if(isset($wizardData['city']) && $wizardData['city'] !='null')
                	  $cityId = $wizardData['city'];	
                	  
    			if(count($_POST) > 0)
                {
                    $countryId = isset($_POST['country'])?$_POST['country']:"";
                    $stateId = isset($_POST['state'])?$_POST['state']:"";
                    $cityId = isset($_POST['city'])?$_POST['city']:"";                                    
                }
                if($countryId != '')
                {
                    $statesData = $statesmodel->getBasicStatesList((int)$countryId);
                    foreach($statesData as $res)
                    {
                        if($stateId == $res['state_id_org'])
                            $new_stateId = $res['state_id_org'].'!@#'.utf8_encode($res['state']);
                        $form->state->addMultiOption($res['state_id_org'].'!@#'.utf8_encode($res['state']),utf8_encode($res['state']));
                    }
                    if(count($_POST) == 0)
                        $stateId = $new_stateId;
                }
                if($stateId != '')
                {
                    $citiesData = $citiesmodel->getBasicCitiesList((int)$stateId);

                    foreach($citiesData as $res)
                    {
                        if($cityId == $res['city_org_id'])
                            $new_cityId = $res['city_org_id'].'!@#'.utf8_encode($res['city']);
                        $form->city->addMultiOption($res['city_org_id'].'!@#'.utf8_encode($res['city']),utf8_encode($res['city']));
                    }
                    if(count($_POST) == 0)
                        $cityId = $new_cityId;
                }
                
                $form->setDefault('country',$countryId);
                $form->setDefault('state',$stateId);
                $form->setDefault('city',$cityId);
	}
	
	/* Arrary created to use for title,text for anchor tag,empty message text
	 *    for Dashboard widget format functions(format1 to format 7).
	 */
	public static function titleArr($id='',$con='')
	{
	$idsTitleArr = 
	array(57  => array('title'=>'Scheduled Interviews','btnText'=>'Schedules','emptyText'=>'No interviews scheduled for today','addText'=>'Add'),
      10  =>  array('title'=>'Business Units','btnText'=>'View All','emptyText'=>'No business units','addText'=>'Add Units'),
      11  => array('title'=>'Departments','btnText'=>'View All','emptyText'=>'No deparments' ,'addText'=>'Add departments'),
      20  => array('title'=>'Roles & Privileges','btnText'=>'View All','emptyText'=>'No active roles','addText'=>'Add roles'),
      21  => array('title'=>'Manage External Users','btnText'=>'View All','emptyText'=>'No external users','addText'=>'Add'),
      14  => array('title'=>'Employees','btnText'=>'View All','emptyText'=>'','addText'=>'Add')	,
      23  => array('title'=>'Employee/Candidate Screening','btnText'=>'View All','emptyText'=>'','addText'=>'Add')  ,
      32  => array('title'=>'My Details','btnText'=>'View','emptyText'=>'','addText'=>'Add')  ,
      34  => array('title'=>'My Team','btnText'=>'View My Team','emptyText'=>'','addText'=>'Add')  ,
      41  => array('title'=>'Manage Holiday Group','btnText'=>'View All','emptyText'=>'Not configured yet','addText'=>'')  ,
      42  => array('title'=>'Manage Holidays','btnText'=>'View All','emptyText'=>'Not configured yet','addText'=>'')  ,
      45  => array('title'=>'Employee Leaves Summary','btnText'=>'View Leaves','emptyText'=>'','addText'=>'Add')  ,
      54  => array('title'=>'Openings/Positions','btnText'=>'View','emptyText'=>'','addText'=>'Add')  ,
      55  => array('title'=>'CV Management','btnText'=>'View All','emptyText'=>'No data','addText'=>'')  ,
      56  => array('title'=>'Shortlisted & Selected Candidates','btnText'=>'View Candidates','emptyText'=>'','addText'=>'Add')  ,
      61  => array('title'=>'Leaves Available','btnText'=>'Apply Leave','emptyText'=>'','addText'=>'Add')  ,
      65  => array('title'=>'Leaves Pending For Approval','btnText'=>'Approve Leaves','emptyText'=>'','addText'=>'Add')  ,
      44  => array('title'=>'Leave Management Options','btnText'=>'View','emptyText'=>'','addText'=>'Add')  ,
      43  => array('title'=>'My Holiday Calendar','btnText'=>'View Holidays','emptyText'=>'No holidays added yet','addText'=>'Add')  ,
      80  => array('title'=>'Time Zones','btnText'=>'View All','emptyText'=>'Not configured yet','addText'=>'Add')  ,
      86  => array('title'=>'Gender','btnText'=>'View All','emptyText'=>'Not configured yet','addText'=>'Add')  ,
      87  => array('title'=>'Marital Status','btnText'=>'View All','emptyText'=>'Not configured yet','addText'=>'Add')  ,
      88  => array('title'=>'Prefixes','btnText'=>'View All','emptyText'=>'Not configured yet','addText'=>'Add')  ,
      89  => array('title'=>'Race Codes','btnText'=>'View All','emptyText'=>'Not configured yet','addText'=>'Add')  ,
      90  => array('title'=>'Nationality Context Codes','btnText'=>'View All','emptyText'=>'Not configured yet','addText'=>'Add')  ,
      91  => array('title'=>'Nationalities','btnText'=>'View All','emptyText'=>'Not configured yet','addText'=>'Add')  ,
      92  => array('title'=>'Account Class Types ','btnText'=>'View All','emptyText'=>'Not configured yet','addText'=>'Add')  ,
      93  => array('title'=>'License Types','btnText'=>'View All','emptyText'=>'Not configured yet','addText'=>'Add')  ,
      100  => array('title'=>'Countries','btnText'=>'View All','emptyText'=>'Not configured yet','addText'=>'Add')  ,
      101  => array('title'=>'States','btnText'=>'View All','emptyText'=>'Not configured yet','addText'=>'Add')  ,
      102  => array('title'=>'Cities','btnText'=>'View All','emptyText'=>'Not configured yet','addText'=>'Add')  ,
      103  => array('title'=>'Geo Groups','btnText'=>'View All','emptyText'=>'Not configured yet','addText'=>'Add')  ,
      107  => array('title'=>'Veteran Status','btnText'=>'View All','emptyText'=>'Not configured yet','addText'=>'Add')  ,
      108  => array('title'=>'Military Service Types','btnText'=>'View All','emptyText'=>'Not configured yet','addText'=>'Add')  ,
      110  => array('title'=>'Currencies','btnText'=>'View All','emptyText'=>'Not configured yet','addText'=>'Add')  ,
      111  => array('title'=>'Currency Conversions','btnText'=>'View All','emptyText'=>'Not configured yet','addText'=>'Add')  ,
      114  => array('title'=>'Employment Status','btnText'=>'View All','emptyText'=>'','addText'=>'Add')  ,
      115  => array('title'=>'EEOC Categories','btnText'=>'View All','emptyText'=>'Not configured yet','addText'=>'Add')  ,
      116  => array('title'=>'Job Titles','btnText'=>'View All','emptyText'=>'Not configured yet','addText'=>'Add')  ,
      117  => array('title'=>'Pay Frequency','btnText'=>'View All','emptyText'=>'Not configured yet','addText'=>'Add')  ,
      118  => array('title'=>'Remuneration Basis','btnText'=>'View All','emptyText'=>'Not configured yet','addText'=>'Add')  ,
      120  => array('title'=>'Positions','btnText'=>'View All','emptyText'=>'Not configured yet','addText'=>'Add')  ,
      121  => array('title'=>'Languages','btnText'=>'View All','emptyText'=>'Not configured yet','addText'=>'Add')  ,
      123  => array('title'=>'Bank Account Types','btnText'=>'View All','emptyText'=>'Not configured yet','addText'=>'Add')  ,
      124  => array('title'=>'Competency Levels','btnText'=>'View All','emptyText'=>'Not configured yet','addText'=>'Add')  ,
      125  => array('title'=>'Education Levels','btnText'=>'View All','emptyText'=>'Not configured yet','addText'=>'Add')  ,
      126  => array('title'=>'Attendance Status','btnText'=>'View All','emptyText'=>'Not configured yet','addText'=>'Add')  ,
      127  => array('title'=>'Work Eligibility Document Types','btnText'=>'View All','emptyText'=>'Not configured yet','addText'=>'Add')  ,
      128  => array('title'=>'Leave Types','btnText'=>'View All','emptyText'=>'Not configured yet','addText'=>'Add')  ,
	  131  => array('title'=>'Site Configuration','btnText'=>'View','emptyText'=>'Not configured yet','addText'=>'Add'),
      132  => array('title'=>'Number Formats','btnText'=>'View All','emptyText'=>'Not configured yet','addText'=>'Add')  ,
      136  => array('title'=>'Email Contacts','btnText'=>'View All','emptyText'=>'Not configured yet','addText'=>'Add')  ,
      140  => array('empTabTitle'=> array('title'=>'Employee Tabs','btnText'=>'View All','emptyText'=>'Not configured yet','addText'=>'Add'),
      		  'empTabConfigurations' =>array('employeedocs' => 'Employee Documents','emp_leaves' => 'Employee Leaves','emp_leaves' => 'Employee Leaves','emp_salary' => 'Salary Details','emppersonaldetails'=>'Personal Details',
								'empcommunicationdetails'=>'Contact Details','emp_skills' => 'Employee Skills','emp_jobhistory' => 'Employee Job History','experience_details' => 'Experience Details',
								   'education_details' => 'Education  Details','trainingandcertification_details' => 'Training & Certification  Details','medical_claims' => 'Medical Claims',
								   'disabilitydetails' => 'Disability Details','dependency_details' => 'Dependency Details','visadetails' => 'Visa and Immigration Details',
								   'creditcarddetails' => 'Corporate Card Details','workeligibilitydetails' => 'Work Eligibility Details','emp_additional' => 'Additional Details',
								   'emp_performanceappraisal' => 'Performance Appraisal','emp_payslips' => 'Pay slips','emp_benifits' => 'Benefits','emp_renumeration' => 'Remuneration Details',
								   'emp_security' => 'Security Credentials' )),
      144  => array('title'=>'Categories','btnText'=>'View All','emptyText'=>'Not configured yet','addText'=>'Add')  ,
      145  => array('title'=>'Request Types','btnText'=>'View All','emptyText'=>'Not configured yet','addText'=>'Add')  ,
      146  => array('title'=>'Settings','btnText'=>'View All','emptyText'=>'Not configured yet','addText'=>'Add')  ,
      148  => array('title'=>'','btnText'=>'','emptyText'=>'','addText'=>'Add')  ,
      150  => array('title'=>'Parameters','btnText'=>'View All','emptyText'=>'Not configured yet','addText'=>'Add')  ,
      151  => array('title'=>'Skills','btnText'=>'View All','emptyText'=>'Not configured yet','addText'=>'Add')  ,
      152  => array('title'=>'Appraisal Questions','btnText'=>'View All','emptyText'=>'Not configured yet','addText'=>'Add')  ,
      154  => array('title'=>'Initialize Appraisal','btnText'=>'Initialize','emptyText'=>'Not configured yet','addText'=>'Add')  ,
      155  => array('title'=>'Appraisal Settings','btnText'=>'View All','emptyText'=>'','addText'=>'Add')  ,
      165  => array('title'=>'Parameters','btnText'=>'View All','emptyText'=>'Not configured yet','addText'=>'Add')  ,
      166  => array('title'=>'Feedforward Questions','btnText'=>'View All','emptyText'=>'Not configured yet','addText'=>'Add')  ,
      62  => array('title'=>'Pending Leave(s)','btnText'=>'View All','emptyText'=>'','addText'=>'Add')  ,
      63  => array('title'=>'Approved Leaves','btnText'=>'View All','emptyText'=>'','addText'=>'Add')  ,
      64  => array('title'=>'Cancelled Leaves','btnText'=>'View All','emptyText'=>'','addText'=>'Add')  ,
      68  => array('title'=>'Screening Types','btnText'=>'View All','emptyText'=>'Not configured yet','addText'=>'Add')  ,
      69  => array('title'=>'Agencies','btnText'=>'View All','emptyText'=>'','addText'=>'Add')  ,
      85  => array('title'=>'Ethnic Codes','btnText'=>'View All','emptyText'=>'Ethnic codes not added','addText'=>'Add')  ,
      134  => array('title'=>'Approved Requisitions','btnText'=>'View All','emptyText'=>'','addText'=>'Add')  ,
      135  => array('title'=>'Rejected Leaves','btnText'=>'View All','emptyText'=>'','addText'=>'Add')  ,
      138  => array('title'=>'Rejected Requisitions','btnText'=>'View All','emptyText'=>'','addText'=>'Add')  ,
      139  => array('title'=>'Identity Documents','btnText'=>'View All','emptyText'=>'','addText'=>'Add')  ,
      142  =>  array('title'=>'Manage Modules','btnText'=>'View All','emptyText'=>'Not configured yet','addText'=>'Add')  ,
      158  => array('title'=>'Manager Status','btnText'=>'View','emptyText'=>'No data','addText'=>'')  ,
      159  => array('title'=>'Appraisal Employee Status','btnText'=>'View','emptyText'=>'No data','addText'=>''),
      35  => array('title'=>'My Team Appraisal','btnText'=>'View','emptyText'=>'No data','addText'=>''),
      160  =>  '',
      161  => array('title'=>'Self Appraisal','btnText'=>'View','emptyText'=>'No data','addText'=>''),
      167  =>  '',
      168  =>  '',
      169  =>  array('title'=>'Manager Appraisal','btnText'=>'View','emptyText'=>'No data','addText'=>''), 
      170  =>  array('title'=>'Appraise Your Manager','btnText'=>'View','emptyText'=>'No data','addText'=>''), 
      172  => array('title'=>'Feedforward Employee Status','btnText'=>'View','emptyText'=>'No data','addText'=>''),
      174  => array('title'=>'My Team Appraisal','btnText'=>'View','emptyText'=>'No data','addText'=>''),
	  182  => array('title'=>'Categories','btnText'=>'View','emptyText'=>'No data','addText'=>''),
      );
      //echo "<pre>";print_r($idsTitleArr[140]['empTabTitle']);exit;
	return $idsTitleArr[$id][$con];
	}
	
	
	public static function format1($url='')
	{
		try {
			$widgetsModel = new Default_Model_Widgets();
			$format1 = $widgetsModel->format1();
			if(!empty($url))
			{
			  $url = substr($url,1);
			}
			
			// Get login user data
			$auth = Zend_Auth::getInstance();
	        $session = $auth->getStorage()->read();
	        $loginUserId = $session->id;
	        $login_user_name = $session->userfullname;
	        $login_user_profile_image = $session->profileimg;
	        $job_title='';
			$job_title_name='';
	        // Get job title name
	        if ($session->emprole == 1) {
	        	$job_title_name = "Super Admin";
	        } else {
				if(!empty($session->jobtitle_id))
				{	
					$result = $widgetsModel->getJobTitleName($session->jobtitle_id);
					$job_title_name = $result["jobtitlename"];
				}	
	        }
	        $login_user_name1 = (strlen($login_user_name) > 13) ? substr($login_user_name,0,13).'..':$login_user_name;
			$job_title =(!empty($job_title_name) && strlen($job_title_name) > 13) ? substr($job_title_name,0,13).'..':$job_title_name;
	       
	        if(empty($format1))
			{ 
				$htmlContent = '<div id="format1_div" class="interview_shed_block no_interview_shed_block" >
					<div class="left_block_shed">
						<div class="users_left_list_div users_list">
							<span class="values">
								<div class="profile_img">
									<img src="'.DOMAIN.'public/uploads/profile/'.$login_user_profile_image.'" width="80px" height="80px" onerror=\'this.src="'.DOMAIN.'public/media/images/default-profile-pic.jpg"\'>
								</div> 
							</span>
							<div class="member_name" title="'.$login_user_name.'">'.$login_user_name1.'</div>		
							<div class="member_jname" title="'.$job_title_name.'">'.$job_title.'</div>
						</div>
					</div>
			  <div class="interview_shed_box" style="display:none;"><h4><div>Interview Schedules, <span>'.date("D j, M Y").'</span></div> ';
			  $htmlContent .=  "<div class='no_interview'>No interviews scheduled for today.</div> </h4>";
			}
			else{
				$htmlContent = '<div id="format1_div" class="interview_shed_block" >
					<div class="left_block_shed">
						<div class="users_left_list_div users_list">
							<span class="values">
								<div class="profile_img">
									<img src="'.DOMAIN.'public/uploads/profile/'.$login_user_profile_image.'" width="80px" height="80px" onerror=\'this.src="'.DOMAIN.'public/media/images/default-profile-pic.jpg"\'>
								</div> 
							</span>
							<div class="member_name" title="'.$login_user_name.'">'.$login_user_name1.'</div>		
							<div class="member_jname" title="'.$job_title_name.'">'.$job_title.'</div>
						</div>
						<h4><div>Interview Schedules</div><span>'.date("D j, M Y").'</span></h4>
					</div>
					<div class="interview_shed_box" style="display:none;">';
				if(!empty($url))
				$htmlContent .= '<div class="box_link view_link"><a href="'.BASE_URL.$url.'" >All</a></div>';				
				$htmlContent.='<ul>';
			
				foreach($format1 as $interview_scheduled ) 
				{
					$candidate_id = $interview_scheduled['id'];					
					 $candidate_name = $interview_scheduled['candidate_name'];
					 $interview_type = $interview_scheduled['interview_mode'];
					 $interview_time = sapp_Global::change_time($interview_scheduled['interview_time'],'interview_time');
					 $contact_number = $interview_scheduled['contact_number'];
					 $interview_id = $interview_scheduled['interview_id'];
					 $emailid = $interview_scheduled['emailid'];
					 $cand_resume = $interview_scheduled['cand_resume']; 
					 $candidate_name_shrt=(strlen($candidate_name) > 10) ? substr($candidate_name,0,10):$candidate_name; 
					 $htmlContent .= '<li>
					 <span class="emp_lable me_label">Candidate</span>
					 <span class="can_name_lable" title="'.$candidate_name.'">'.$candidate_name_shrt. '('.$interview_type.'),</span>
					 <span class="txt_lable">At:</span>
					 <span class="txt_block">'. $interview_time.',</span>';
					 
					 if (!empty($contact_number)) {
					 	$htmlContent .= '<div style="display: inline-block;"><span class="txt_lable">Phone:</span>
					 <span class="txt_block">'. $contact_number.',</span></div>';	
					 }
					 
					 if (!empty($emailid)) {
					 	$htmlContent .= '<div style="display: inline-block;"><span class="txt_lable">Email:</span>
					 <span class="txt_block">'. $emailid .',</span></div>';
					 }	
					 
					 $htmlContent .= '<span class="emp_resume_link"><a href="'.BASE_URL.'scheduleinterviews/downloadresume/id/'.$candidate_id.'/int_id/'.$interview_id.'" title="'.$cand_resume.'">';
					 if(!empty($cand_resume)){
					 	$htmlContent .= 'Resume';
					 } else {
					 	$htmlContent .= 'View';
					 }
					 $htmlContent .= '</a></span></li>';
				}
				$htmlContent .= '</ul>';
			} 
			
	        return $htmlContent .= '</div><div class="clear"></div></div>';
		} 	catch(Expection $e) {
			echo $e->getMessage();
		}
	}
	public static function format2($id='',$i=0,$url='')
	{ 
		$widgetsModel = new Default_Model_Widgets();
		$format2 = $widgetsModel->format2($id);
		$title = self::titleArr($id,'title');
		$btnText = self::titleArr($id,'btnText');
		$append_url1 = BASE_URL.'empscreening/con/pQ==';
		$append_url2 = BASE_URL.'empscreening/con/pA==';
		if(!empty($url))
		{
		 $url = substr($url,1);
		}
		$htmlContent = '<div id="format1_div" class="dashboard_wid_box colour_'.$i.'  emp_total"><h4 >'.$title.'</h4>';
		if(empty($format2))
		{
			
		$htmlContent .= '<a href="'.$append_url1.'" class ="cls_redirect"><div class="dashboard_wid_box_inner">Candidates<span class="box_count">0</span></div></a>
			<a href="'.$append_url2.'" class ="cls_redirect"><div class="dashboard_wid_box_inner last-child">Employees<span class="box_count">0</span></div></a>';
		if(!empty($url))
		$htmlContent .= '<a href="'.BASE_URL.$url.'"class="box_link view_link">'.$btnText.'</a>';
		}
		else{
			$htmlContent .= '<a href="'.$append_url1.'" class ="cls_redirect"><div class="dashboard_wid_box_inner">Candidates<span class="box_count">'.(isset($format2[0])? $format2[0]:0).'</span></div>
			<a href="'.$append_url2.'" class ="cls_redirect"><div class="dashboard_wid_box_inner last-child">Employees<span class="box_count">'.(isset($format2[1])? $format2[1]:0).'</span></div>';
			if(!empty($url))
			$htmlContent .= '<a href="'.BASE_URL.$url.'"class="box_link view_link">'.$btnText.'</a>';
			}
		return $htmlContent .='</div>';
	}
	
	
	public static function format4($id='',$i=0,$url='')
	{
		$widgetsModel = new Default_Model_Widgets();
		$format4 = $widgetsModel->format4($id);															
		$title = self::titleArr($id,'title');
		$btnText = self::titleArr($id,'btnText');
		$count = 0;
		if(!empty($url))
		{
		 $url = substr($url,1);
		}
		 $class = '';
		 $append_url1 = '';
		 $append_url2 = '';
	      if($id == 14 || $id == 34)
		   {
				$title1='Active';
				$title2='Inactive';
				if(!empty($format4))
				{
					$count = $format4['param1'];
					$format4['param3'] = $count-(int)$format4['param2'];
				}
		   }
		   
		  else if($id == 54)
		   {
				$title1='Approved';
				$title2='Rejected';
				$append_url1 = BASE_URL.'approvedrequisitions'; // $append_url for redirecting to specific page
				$append_url2 = BASE_URL.'rejectedrequisitions';
				if(!empty($format4))
				$count = $format4['param1'];
				
		   }
		   else
		   {
				$title1='ShortList';
				$title2='Selected';
				$class = ' shortlist_cls';// class added for the shortlist candidate menu widget because of overlapping to view all Button.
				$append_url1 = BASE_URL.'shortlistedcandidates/pA==';
				$append_url2 = BASE_URL.'shortlistedcandidates/pQ==';
				if(!empty($format4))
				$count = $format4['param1'] + $format4['param2'] + $format4['param3'];
		   }
			
		   		$htmlContent = '<div class="dashboard_wid_box '.$class.' colour_'.$i.' emp_total">
						<h4 >
						<div class="box_count_tol emp_total">'.$count .'</div>'.$title.'</h4>';
		   	if(!empty($format4))
			{
				// Avoid hand symbol for Employee widget tabs
				$href1 = !empty($append_url1) ? "href='$append_url1'" : "";
				$href2 = !empty($append_url1) ? "href='$append_url2'" : "";
				
				$htmlContent .='<a '.$href1.' class ="cls_redirect"><div class="dashboard_wid_box_inner">'.$title1.'<span class="box_count">'.$format4['param2'] .'</span></div></a>
						<a '.$href2.' class ="cls_redirect"><div class="dashboard_wid_box_inner last-child">'.$title2.'<span class="box_count">'. $format4['param3'].'</span></div></a>';
			}
			else
			{
				$htmlContent .='<a href="'.$append_url1.'" class ="cls_redirect"><div class="dashboard_wid_box_inner">'.$title1.'<span class="box_count">0</span></div></a>
						<a href="'.$append_url2.'" class ="cls_redirect"><div class="dashboard_wid_box_inner last-child">'.$title2.'<span class="box_count">0</span></div></a>';
			}	
				if(!empty($url)) 
				$htmlContent .='<a href="'.BASE_URL.$url.'"class="box_link view_link">'.$btnText.'</a>';
				$htmlContent .='</div>';
		
		return $htmlContent;
	}
	
	public static function format5($id='',$i=0,$url='')
	{
		$widgetsModel = new Default_Model_Widgets();
		$format5 = $widgetsModel->format5($id);
		$size = sizeof($format5);
		if($id == 140)
		{
			$config_arr = self::titleArr($id,'empTabConfigurations');
			$title_arr = self::titleArr($id,'empTabTitle');
			$title = $title_arr['title'];
			$btnText = $title_arr['btnText'];
			$format5 = array();
			foreach( array_rand($config_arr,5) as $k ) {
	  		$format5[] = $config_arr[$k];
			}
			$size = sizeof($config_arr);
		}
		else{
		$title = self::titleArr($id,'title');
		$btnText = self::titleArr($id,'btnText');
		$emptyText = self::titleArr($id,'emptyText');
		
		}
		$total =0;
		
		
		if(!empty($url))
		{
		 $url = substr($url,1);
		}
		$htmlContent = '<div class="dashboard_bottom_box" >';
		
		if($id == 21)
		{ 
			if(!empty($format5))
			{
				$total = (int)$format5['backgroundagency']+(int)$format5['users']+(int)$format5['vendors']+(int)$format5['staffing'];
				$htmlContent .= '<h4 >'.$title.'</h4><div id="cnt_div" class ="tot_cnt num_color_'.$i.'">'.$total.'</div><div class="dashboard_bottom_div" ><ul>';
				foreach($format5 as $k=>$v)
				{	
					$shrt_key = (strlen($k) > 30) ? substr($k,0,30):$k;
					$htmlContent .= '<li title="'.$k.'">'.$shrt_key." "."(".$v.")"."</li>";
				} 
			 
				$htmlContent .= "</ul>";
				if(!empty($url)) 
				$htmlContent .='<a href="'.BASE_URL.$url.'"class="box_link view_link">'.$btnText.'</a>';
				$htmlContent .='</div>';
			}
			else 
			{
				$htmlContent .= '<h4 >'.$title.'</h4><div id="cnt_div" class ="tot_cnt num_color_'.$i.'">0</div><div class="dashboard_bottom_div" >';
				$htmlContent .= "<span class='no_text no_data'>$emptyText</span>"; 
				if(!empty($url))
				$htmlContent .='<a href="'.BASE_URL.$url.'"class="box_link view_link">'.$btnText.'</a>';
				$htmlContent .='</div>';
			}
		}
		
		else if($id == 159 || $id == 174)
		{
			
			if(!empty($format5))
			{
			   $total = (int)$format5['ratings']['pending_employee_ratings']+(int)$format5['ratings']['completed']+(int)$format5['ratings']['pending_manager_ratings'];	
			   $htmlContent .= '<h4 >'.$title.'</h4><div id="cnt_div" class ="tot_cnt num_color_'.$i.'">'.$total.'</div><div class="dashboard_bottom_div" >';
			   $htmlContent .= '<span class="businessunit_title" title="BusinessUnit">Business Unit : '.$format5['businessUnit']."</span>";
			   if(isset($format5['department']))
			   {
			   $htmlContent .= '<span class="department_txt" title="Department">Department : '.$format5['department']."</span>";
			   }
			    $htmlContent .= "<span class='department_txt' title='Appraisal Mode'>".$format5['appraisal_period']." Appraisal (".$format5['from_year']." - ".$format5['to_year'].")"  
			    					."</span><ul> ";
			   $htmlContent .= '<li title="Pending employee ratings">Pending employee ratings'." (".$format5['ratings']['pending_employee_ratings'].")"."</li>";
			   $htmlContent .= '<li title="Pending manager ratings">Pending manager ratings'." (".$format5['ratings']['pending_manager_ratings'].")"."</li>";
			   $htmlContent .= '<li title="Completed">Completed'." (".$format5['ratings']['completed'].")"."</li></ul>";
			   if(!empty($url)) 
				$htmlContent .='<a href="'.BASE_URL.$url.'"class="box_link view_link">'.$btnText.'</a>';
				$htmlContent .='</div>';
			   
			}
			else 
			{
				$htmlContent .= '<h4 >'.$title.'</h4><div id="cnt_div" class ="tot_cnt num_color_'.$i.'">0</div><div class="dashboard_bottom_div" >';
				$htmlContent .= "<span class='no_text no_data'>$emptyText</span>"; 
				if(!empty($url))
				$htmlContent .='<a href="'.BASE_URL.$url.'"class="box_link view_link">'.$btnText.'</a>';
				$htmlContent .='</div>';
			}
			
			
		}
		else if($id == 172)
		{
			
			if(!empty($format5))
			{
			   $total = (int)$format5['ratings']['pending_employee_ratings']+(int)$format5['ratings']['completed'];	
			   $htmlContent .= '<h4 >'.$title.'</h4><div id="cnt_div" class ="tot_cnt num_color_'.$i.'">'.$total.'</div><div class="dashboard_bottom_div" >';
			   $htmlContent .= '<span class="businessunit_title" title="BusinessUnit">Business Unit : '.$format5['businessUnit']."</span>";
			   if(isset($format5['department']))
			   {
			   $htmlContent .= '<span class="department_txt" title="Department">Department : '.$format5['department']."</span>";
			   }
			    $htmlContent .= "<span  class='department_txt' title='Appraisal Mode'>".$format5['ff_period']." Feedforward (".$format5['ff_from_year']." - ".$format5['ff_to_year'].")"  
			    					."</span><ul> ";
			   $htmlContent .= '<li title="Pending employee ratings">Pending employee ratings'." (".$format5['ratings']['pending_employee_ratings'].")"."</li>";
			   $htmlContent .= '<li title="Completed">Completed'." (".$format5['ratings']['completed'].")"."</li></ul>";
			   if(!empty($url)) 
				$htmlContent .='<a href="'.BASE_URL.$url.'"class="box_link view_link">'.$btnText.'</a>';
				$htmlContent .='</div>';
			   
			}
			else 
			{
				$htmlContent .= '<h4 >'.$title.'</h4><div id="cnt_div" class ="tot_cnt num_color_'.$i.'">0</div><div class="dashboard_bottom_div" >';
				$htmlContent .= "<span class='no_text no_data'>$emptyText</span>"; 
				if(!empty($url))
				$htmlContent .='<a href="'.BASE_URL.$url.'"class="box_link view_link">'.$btnText.'</a>';
				$htmlContent .='</div>';
			}
			
			
		}
	  else if($id == 158)
		{
			if(!empty($format5))
			{  
				$completedMngrCnt = isset($format5['compltedmgrIdArr'])? $format5['compltedmgrIdArr']:0;
				$notCompletedMngrCnt = isset($format5['notCompltedmgrIdArr'])? $format5['notCompltedmgrIdArr']:0;
			   $total = (int)$completedMngrCnt+(int)$notCompletedMngrCnt;	
			   $htmlContent .= '<h4 >'.$title.'</h4><div id="cnt_div" class ="tot_cnt num_color_'.$i.'">'.$total.'</div><div class="dashboard_bottom_div" >';
			   $htmlContent .= '<span class="businessunit_title" title="BusinessUnit">Business Unit : '.$format5['businessUnit']."</span>";
			   if(isset($format5['department']))
			   {
			   $htmlContent .= '<span class="department_txt" title="Department">Department : '.$format5['department']."</span>";
			   }
			    $htmlContent .= "<span  class='department_txt' title='Appraisal Mode'>".$format5['appraisal_period']." Appraisal (".$format5['from_year']." - ".$format5['to_year'].")"  
			    					."</span><ul>";
			   $htmlContent .= '<li title="Pending manager ratings">Pending manager ratings'." (".$notCompletedMngrCnt.")"."</li>";
			   $htmlContent .= '<li title="Completed manager ratings">Completed manager ratings'." (".$completedMngrCnt.")"."</li></ul>";
			   if(!empty($url)) 
				$htmlContent .='<a href="'.BASE_URL.$url.'"class="box_link view_link">'.$btnText.'</a>';
				$htmlContent .='</div>';
			   
			}
			else 
			{
				$htmlContent .= '<h4 >'.$title.'</h4><div id="cnt_div" class ="tot_cnt num_color_'.$i.'">0</div><div class="dashboard_bottom_div" >';
				$htmlContent .= "<span class='no_text no_data'>$emptyText</span>"; 
				if(!empty($url))
				$htmlContent .='<a href="'.BASE_URL.$url.'"class="box_link view_link">'.$btnText.'</a>';
				$htmlContent .='</div>';
			}
			
			
		}
		else if($id == 131)
		{
			if(!empty($format5))
			{
				$time_ex = strlen($format5['timezone']) > 27? substr($format5['timezone'],0,25).'..':$format5['timezone'];
				$currency_ex = strlen($format5['currency']) > 27? substr($format5['currency'],0,25).'..':$format5['currency'];
				$passwordtype = strlen($format5['passwordtype']) > 27? substr($format5['passwordtype'],0,25).'..':$format5['passwordtype'];
				 $htmlContent = '<div class="dashboard_bottom_box my_details_box" ><h4 >'.$title.'</h4><div class="dashboard_bottom_div" ><ul class="leave_mana">';
				 $htmlContent .= '<li><span>Date</span>:<span class="ul_span_2" title = "'.$format5['date_example'].'">'.$format5['date_example']."</span></li>";
				 $htmlContent .= '<li><span>Time</span>:<span class="ul_span_2" title = "'.$format5['time_example'].'">'.$format5['time_example']."</span></li>";
				 $htmlContent .= '<li><span>Currency</span>:<span  class="ul_span_2" title = "'.$format5['currency'].'">'.$currency_ex."</span></li>";
				 $htmlContent .= '<li><span>Timezone</span>:<span class="ul_span_2" title = "'.$format5['timezone'].'">'.$time_ex."</span></li>";
				 $htmlContent .= '<li><span>Password</span>:<span class="ul_span_2"  title = "'.$format5['passwordtype'].'">'.$passwordtype."</span></li></ul>";
				  if(!empty($url)) 
				$htmlContent .='<a href="'.BASE_URL.$url.'"class="box_link view_link">'.$btnText.'</a>';
				$htmlContent .='</div>';
			}
		}
	else if($id == 140)
		{
			if(!empty($format5))
			{
				 $htmlContent = '<div class="dashboard_bottom_box" ><h4 >'.$title.'</h4><div id="cnt_div" class ="tot_cnt num_color_'.$i.'">'. $size.'</div><div class="dashboard_bottom_div" >';
				 $htmlContent .= "<ul>";
				 foreach($format5 as $val)
				 $htmlContent .= "<li title ='".$val."' >".$val."</li>";
				  $htmlContent .= "</ul>";
				  if(!empty($url)) 
				$htmlContent .='<a href="'.BASE_URL.$url.'"class="box_link view_link">'.$btnText.'</a>';
				$htmlContent .='</div>';
			}
		}
		
	 else if($id == 55)
	 {
	 	  
				$htmlContent .= '<h4 >'.$title.'</h4><div id="cnt_div" class ="tot_cnt num_color_'.$i.'">'.$format5['count']['cnt'].'</div><div class="dashboard_bottom_div" >';
			   $htmlContent .= "<ul>";
			   $htmlContent .= '<li title="Scheduled">Scheduled'." (".$format5['scheduled'].")"."</li>";
			   $htmlContent .= '<li title="Not Scheduled">Not Scheduled'." (".$format5['not_scheduled'].")"."</li>";
			   $htmlContent .= '<li title="On Hold">On Hold'." (".$format5['on_hold'].")"."</li>";
			   $htmlContent .= '<li title="Shortlisted">Shortlisted'." (".$format5['shortlisted'].")"."</li>";
			   $htmlContent .= '<li title="Selected">Selected'." (".$format5['selected'].")"."</li></ul>";
			   if(!empty($url)) 
				$htmlContent .='<a href="'.BASE_URL.$url.'"class="box_link view_link">'.$btnText.'</a>';
				$htmlContent .='</div>';
		
	 }
	 else if($id = 182)
		{
			if($format5['count']['count'] > 0) 
			{
				$htmlContent .= '<h4 >'.$title.'</h4><div id="cnt_div" class ="tot_cnt num_color_'.$i.'">'. $format5['count']['count'].'</div><div class="dashboard_bottom_div mana_mod_box" >';
					unset($format5['count']);
						$htmlContent .= "<ul>";
						$a = 0;
						foreach($format5 as $val)
						{  
							if(isset($val['param2']))
							{ 
									$shrt_key = (strlen($val['param1']) > 30) ? substr($val['param1'],0,30):$val['param1'];
									if($id != 111)
									  $htmlContent .= '<li title="'.$val['param1'].'">'.$shrt_key." "."(".$val['param2'].")"."</li>";
									else
									  $htmlContent .= '<li title="'.$val['param1'].'">'.$shrt_key." to ".$val['param2']."</li>";
								
							}
							else
							{ 
								$shrt_key = (strlen($val['param1']) > 30) ? substr($val['param1'],0,30):$val['param1'];
								$htmlContent .= '<li title="'.$val['param1'].'">'.$shrt_key."</li>";
								
							}
							
							$a++;							
							if($a>=5)
								break;
						}

				$htmlContent .= "</ul>"; 
				if(!empty($url))
				{
					$htmlContent .='<a href="'.BASE_URL.$url.'"class="box_link view_link">'.$btnText.'</a>';
				}
				$htmlContent .='</div>';
			}
			else 
			{
				$htmlContent .= '<h4 >'.$title.'</h4><div id="cnt_div" class ="tot_cnt num_color_'.$i.'">0</div><div class="dashboard_bottom_div" >';
				$htmlContent .= "<span class='no_text no_data'>$emptyText</span>"; 
				if(!empty($url)) 
				$htmlContent .='<a href="'.BASE_URL.$url.'"class="box_link view_link">'.$btnText.'</a>';
				$htmlContent .='</div>';
			}
		}
	 	else
		{  
			if($format5['count']['count'] > 0) 
			{
				$htmlContent .= '<h4 >'.$title.'</h4><div id="cnt_div" class ="tot_cnt num_color_'.$i.'">'. $format5['count']['count'].'</div><div class="dashboard_bottom_div mana_mod_box" >';
					unset($format5['count']);
						$htmlContent .= "<ul>";
						foreach($format5 as $val)
						{  
							if(isset($val['param2']))
							{ 
								$shrt_key = (strlen($val['param1']) > 30) ? substr($val['param1'],0,30):$val['param1'];
								if($id != 111)
								  $htmlContent .= '<li title="'.$val['param1'].'">'.$shrt_key." "."(".$val['param2'].")"."</li>";
							    else
							      $htmlContent .= '<li title="'.$val['param1'].'">'.$shrt_key." to ".$val['param2']."</li>";
							
							}
							else
							{ 
								$shrt_key = (strlen($val['param1']) > 30) ? substr($val['param1'],0,30):$val['param1'];
								$htmlContent .= '<li title="'.$val['param1'].'">'.$shrt_key."</li>";
								
							}
							
						}
				$htmlContent .= "</ul>"; 
				if(!empty($url)) 
				$htmlContent .='<a href="'.BASE_URL.$url.'"class="box_link view_link">'.$btnText.'</a>';
				$htmlContent .='</div>';
			}
			else 
			{
				$htmlContent .= '<h4 >'.$title.'</h4><div id="cnt_div" class ="tot_cnt num_color_'.$i.'">0</div><div class="dashboard_bottom_div" >';
				$htmlContent .= "<span class='no_text no_data'>$emptyText</span>"; 
				if(!empty($url)) 
				$htmlContent .='<a href="'.BASE_URL.$url.'"class="box_link view_link">'.$btnText.'</a>';
				$htmlContent .='</div>';
			}
			
		}
				
		return $htmlContent .='</div>';
	}
	
	
	public static function format3($id='',$i=0,$url='')
	{
		$widgetsModel = new Default_Model_Widgets();
		$format3 = $widgetsModel->format3($id);
		$title = self::titleArr($id,'title');
		$btnText = self::titleArr($id,'btnText');
		if(!empty($url))
		{
		 $url = substr($url,1);
		}
		if(!empty($format3))
		{
			if($id == 161 )
			$htmlContent = '<div class="dashboard_wid_box colour_'.$i.' single_txt"><h4 >'.$format3['cnt'].' '.$title.'</h4>';
			else if($id == 170)
			{
				$htmlContent = '<div class="dashboard_wid_box colour_'.$i.' single_txt"><h4 >'.$title.' '.$format3['cnt'].'</h4>';
			}
			else 
			$htmlContent = '<div class="dashboard_wid_box colour_'.$i.' single_txt"><h4 ><span>'.$format3['cnt'].'&nbsp</span>'.$title.'</h4>';
			
			
		}
		else 
		{	
			if($id == 161 || $id == 170)
			$htmlContent = '<div class="dashboard_wid_box colour_'.$i.' single_txt"><h4 ><div class="colour_not_conf">'.$title.' <div class="no_info_txt">Not Configured</div></div></h4>';
			else if($id == 61)
			$htmlContent = '<div class="dashboard_wid_box colour_'.$i.' single_txt"><h4 ><div class="colour_not_conf ">Leaves <div class="no_info_txt">Not allotted yet</div></div></h4>';
			else
			$htmlContent = '<div class="dashboard_wid_box colour_'.$i.' single_txt"><h4 ><span>0 </span> '.$title.'</h4>';
			
		}
		if(!empty($url)) 
				$htmlContent .='<a href="'.BASE_URL.$url.'"class="box_link view_link">'.$btnText.'</a>';
				$htmlContent .='</div>';
		return $htmlContent ;//.='<a href="'.BASE_URL.$url.'"class="box_link view_link">'.$btnText.'</a></div>';
	}
	
	public static function format6($id='',$url='')
	{
		$widgetsModel = new Default_Model_Widgets();
		$format6 = $widgetsModel->format6($id);
		$title = self::titleArr($id,'title');
		$btnText = self::titleArr($id,'btnText');
		$emptyText = self::titleArr($id,'emptyText');
		if(!empty($url))
		{
		 $url = substr($url,1);
		}
		 $htmlContent = '<div class="dashboard_bottom_box" ><h4 >'.$title.'</h4>';
			if(!empty($format6))
			{	
		 		foreach($format6 as $format6)
						{ 
							$dept_name = (strlen($format6['deptname']) > 30) ? substr($format6['deptname'],0,30):$format6['deptname'];
							$htmlContent .= "<div class='dashboard_bottom_div'><ul class='leave_mana'><li><span>Department</span>:<span class='ul_span_2' title=".$format6['deptname'].">".$dept_name."</span></li>";
							$htmlContent .= "<li><span>Calender start month</span>:<span class='ul_span_2'>".$format6['month_name']."</span></li>";
							$htmlContent .= "<li><span>Weekend</span>:<span class='ul_span_2'>".$format6['weekend_start']." to ".$format6['weekend_end']."</span></li>";
							$htmlContent .= "<li><span>Half day</span>:<span class='ul_span_2'>".$format6['is_halfday']."</span></li>";
							$htmlContent .= "<li><span>Leave transferable</span>:<span class='ul_span_2'>".$format6['is_leavetransfer']."</span></li></ul></div>";
							
						}
			}
			else 
			{
				$htmlContent .= "<div class='dashboard_bottom_div' ><span class='no_text no_data'>No leave management options</span></div>"; 
			}
				if(!empty($url)) 
				$htmlContent .='<a href="'.BASE_URL.$url.'"class="box_link view_link">'.$btnText.'</a>';
				$htmlContent .='</div>';		
        	return $htmlContent ;//.='<a href="'.BASE_URL.$url.'"class="box_link view_link">'.$btnText.'</a></div>';					
	}
	
	public static function format7($id='',$url='')
	{
		// Get login user data
			$auth = Zend_Auth::getInstance();
	        $session = $auth->getStorage()->read();
	        $loginUserId = $session->id;
		
		$widgetsModel = new Default_Model_Widgets();
		$format7 = $widgetsModel->format7($id);
		$title = self::titleArr($id,'title');
		$btnText = self::titleArr($id,'btnText');
		$emptyText = self::titleArr($id,'emptyText');
		if(!empty($url))
		{
		 $url = substr($url,1);
		}
		 $htmlContent = '<div class="dashboard_bottom_box my_details_box" ><h4 >'.$title.'</h4>';
			if(!empty($format7))
			{	
		 		
				$jobtitlename = strlen($format7['jobtitlename']) > 27? substr($format7['jobtitlename'],0,25).'..':$format7['jobtitlename'];
				$emailaddress = strlen($format7['emailaddress']) > 27? substr($format7['emailaddress'],0,25).'..':$format7['emailaddress'];
				$empname = strlen($format7['empname']) > 27? substr($format7['empname'],0,25).'..':$format7['empname'];
						
				$htmlContent .= '<div class="tot_cnt"><div class="profile_img ">
						<img src="'.DOMAIN.'public/uploads/profile/'.$format7['profileimg'].'" width="53px" height="53px" onerror=\'this.src="'.DOMAIN.'public/media/images/default-profile-pic.jpg"\'>
					</div></div><div class="dashboard_bottom_div"> ';
				$htmlContent .= "<ul class='leave_mana'><li><span>Name</span>:<span class='ul_span_2' title = '".$format7['empname']."'>".$empname."</span></li>";
				$htmlContent .= "<li><span>ID</span>:<span class='ul_span_2' title = '".$format7['employeeId']."'>".$format7['employeeId']."</span></li>";
				$htmlContent .= "<li><span>Job Title</span>:<span class='ul_span_2' title = '".$format7['jobtitlename']."'>".$jobtitlename."</span></li>";
				$htmlContent .= "<li><span>Email</span>:<span class='ul_span_2' title = '".$format7['emailaddress']."'>".$emailaddress."</span></li>";
				if(strlen($format7['contact']) >1 )
				$htmlContent .= "<li><span>Contact</span>:<span class='ul_span_2' title = '".$format7['contact']."'>".$format7['contact']."</span></li>";
				$htmlContent .= "</ul></div>";
			}
			else 
			{
				$htmlContent .= "<div class='dashboard_bottom_div' ><span class='no_text no_data'>No data</span></div>"; 
			}
				if(!empty($url)) 
				$htmlContent .='<a href="'.BASE_URL.$url.'"class="box_link view_link">'.$btnText.'</a>';
				$htmlContent .='</div>';		
        	return $htmlContent ;					
	}
	//function to generate access control string for time management
	public static function generateAccessControl_helper6($roles_arr = array())
	{
		$front = Zend_Controller_Front::getInstance()->getControllerDirectory();
		$acl = array();
		unset($front['services']);
		unset($front['default']);
		$tm_classes = array();
		$current_module = '';
		foreach ($front as $module => $path)
		{
			$current_module = $module;
			foreach (scandir($path) as $file)
			{
				if(!empty($file) && strlen($file) > 5)
				{
					include_once $path . DIRECTORY_SEPARATOR . $file;
				}
				foreach (get_declared_classes() as $class)
				{
					if (is_subclass_of($class, 'Zend_Controller_Action') && substr($class,0,14) == 'Timemanagement')
					{
						if(!in_array($class,$tm_classes))
						{
							array_push($tm_classes,$class);
						}

					}
				}
			}
		}
		if(!empty($tm_classes))
		{			
			foreach($tm_classes as $tm)
			{
				$actions = array();
				foreach (get_class_methods($tm) as $action)
				{
					if (strstr($action, "Action") !== false)
					{
						$actions[] = substr($action, 0, -6);
					}
				}
				$tm = substr($tm,15);
				$acl[$current_module][strtolower($tm)] = $actions;
			}
		}
		//statically add the roles, controllers and methods for time management
		$tm_roles = array('Admin','Manager','Employee');
		$tm_roles_controllers = array('Admin' => array('index' => array('index','week','edit','view','getstates','converdate'),'reports' => array('index','employeereports','projectsreports','getempduration','getprojecttaskduration','tmreport'),'clients' => array('index','edit','view','delete','addpopup'),'configuration' => array('index','add'),'currency' => array('index'),'defaulttasks' => array('index','edit','view','delete','checkduptask'),'emptimesheets' => array('index','displayweeks','getmonthlyspan','accordion','employeetimesheet','empdisplayweeks','emptimesheetmonthly','emptimesheetweekly','enabletimesheet','approvetimesheet','rejecttimesheet','approvedaytimesheet','rejectdaytimesheet','getweekstartenddates'),'expenses' => array('index','edit','view','delete','download','uploadpreview','getprojectbyclientid','getfilename','submitexpense','expensereports','viewexpenses','viewexpensereports','updateexpensestatus','updateexpensestatus','updateexpensestatus','updateexpensestatus','updateexpensestatus','updateexpensestatus','updateexpensestatus','updateexpensestatus','updateexpensestatus'),'expensecategory' => array('index','edit','view','delete'),'projectresources' => array('index','resources','view','addresourcesproject','viewemptasks','addresources','deleteprojectresource','assigntasktoresources','taskassign','resourcetaskdelete','resourcetaskassigndelete'),'projects' => array('index','edit','view','add','tasks','addtasksproject','addtasks','delete','checkempforprojects'),'projecttasks' => array('index','viewtasksresources','deletetask','assignresourcestotask','saveresources','edittaskname')),
		'Manager' => array('index' => array('index','week','save','submit','eraseweek','getstates','getapprovedtimesheet','closeapprovealert','converdate'),'notifications' => array('pendingsubmissions','pendingsubmissionsweeklyview','weeklymonthlyview'),'clients' => array('index','edit','view','delete','addpopup'),'defaulttasks' => array('index','edit','view','delete','checkduptask'),'projects' => array('index','edit','view','add','tasks','addtasksproject','addtasks','delete','checkempforprojects'),'projectresources' => array('index','resources','view','addresourcesproject','viewemptasks','addresources','deleteprojectresource','assigntasktoresources','taskassign','resourcetaskdelete','resourcetaskassigndelete'),'projecttasks' => array('index','viewtasksresources','deletetask','assignresourcestotask','saveresources','edittaskname'),'reports' => array('index','employeereports','projectsreports','getempduration','getprojecttaskduration','tmreport'),'emptimesheets' => array('index','displayweeks','getmonthlyspan','accordion','employeetimesheet','empdisplayweeks','emptimesheetmonthly','emptimesheetweekly','enabletimesheet','approvetimesheet','rejecttimesheet','approvedaytimesheet','rejectdaytimesheet','getweekstartenddates'),'expenses' => array('index','edit','view','delete','download','uploadpreview','getprojectbyclientid','getfilename','submitexpense','expensereports','viewexpenses','viewexpensereports','updateexpensestatus','updateexpensestatus','updateexpensestatus','updateexpensestatus','updateexpensestatus','updateexpensestatus','updateexpensestatus','updateexpensestatus','updateexpensestatus')),
		'Employee' => array('index' => array('index','week','save','submit','eraseweek','getstates','getapprovedtimesheet','closeapprovealert','converdate'),'employeeprojects' => array('index','view','emptasksgrid'),'notifications' => array('getnotifications','index'),'expenses' => array('index','edit','view','delete','download','uploadpreview','getprojectbyclientid','getfilename','submitexpense','expensereports','viewexpenses','viewexpensereports','updateexpensestatus','updateexpensestatus','updateexpensestatus','updateexpensestatus','updateexpensestatus','updateexpensestatus','updateexpensestatus','updateexpensestatus','updateexpensestatus'),'reports' => array('index','employeereports','projectsreports','getempduration','getprojecttaskduration','tmreport')));
		$acl_new = array();
		$access_str = '';
		foreach($tm_roles as $tm_role)
		{
			$new_tm_final_array = !empty($tm_roles_controllers[$tm_role])?$tm_roles_controllers[$tm_role]:array();
			if($tm_role == 'Admin')
			{
				$access_str .= "if(!empty(\$tm_role) && \$tm_role == '$tm_role') { ";
				$access_str .= "\n\tif(!isset(\$role))
								\$tmroleText[\$role] = 'admin';";
			}
			else
			{
				$access_str .= "elseif(!empty(\$tm_role) && \$tm_role == '$tm_role') { ";
			}
			if(!empty($new_tm_final_array))
			{
				foreach($new_tm_final_array as $key=>$value)
				{
					$str_methods = '';
					foreach($value as $val)
					{
						$str_methods .= "'".$val."',";
					}
					$str_methods = rtrim($str_methods,',');
					$access_str .= "\n\t\t \$acl->addResource(new Zend_Acl_Resource('timemanagement:".trim($key)."'));
									\$acl->allow(\$tmroleText[\$role], 'timemanagement:".trim($key)."', array(".$str_methods."));\n";
				}
			}
			$access_str .= " } ";
		}
		
		$time_management_string = "\n\t\$auth = Zend_Auth::getInstance(); \n\t\$tmroleText=array();";
		$array_str = "'1'=>'admin',";
		if(!empty($roles_arr))
		{
			foreach($roles_arr as $key=>$val)
			{
				$array_str .= "'$key'"."=>'".$val['roletype']."',";
			}
		}
		if(!empty($array_str))
		{
			$array_str = rtrim($array_str,',');
		}
		$time_management_string .= "\n\t\$tmroleText = array($array_str);";
		$time_management_string .= "\n\t
		if(\$auth->hasIdentity())
		{
			\$tm_role = Zend_Registry::get('tm_role');
			\$timeManagementRole = new Zend_Session_Namespace('tm_role');
			if(empty(\$timeManagementRole->tmrole))
			{
				\$tm_role = \$timeManagementRole->tmrole;
			}				
		}
			$access_str
		";		
		return $time_management_string;
	}
	//for time management
	public static function createDateRangeArray($strDateFrom,$strDateTo)
	{
		// takes two dates formatted as YYYY-MM-DD and creates an
		// inclusive array of the dates between the from and to dates.

		// could test validity of dates here but I'm already doing
		// that in the main script

		$aryRange=array();

		$iDateFrom=mktime(1,0,0,substr($strDateFrom,5,2), substr($strDateFrom,8,2),substr($strDateFrom,0,4));
		$iDateTo=mktime(1,0,0,substr($strDateTo,5,2), substr($strDateTo,8,2),substr($strDateTo,0,4));

		if ($iDateTo>=$iDateFrom)
		{
			array_push($aryRange,date('Y-m-d',$iDateFrom)); // first entry
			while ($iDateFrom<$iDateTo)
			{
				$iDateFrom+=86400; // add 24 hours
				array_push($aryRange,date('Y-m-d',$iDateFrom));
			}
		}
		return $aryRange;
	}	
	
		public static function object_to_array($obj) {
        $_arr = is_object($obj) ? get_object_vars($obj) : $obj;
        foreach ($_arr as $key => $val) {
                $val = (is_array($val) || is_object($val)) ? sapp_Global::object_to_array($val) : $val;
                $arr[$key] = $val;
        }
        return $arr;
	}
}

		//Asset Validations..








//end of class
?>