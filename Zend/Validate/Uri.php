<?php
require_once 'Zend/Validate/Abstract.php';
require_once 'Zend/Uri.php';


class Zend_Validate_Uri extends Zend_Validate_Abstract
{
    
		const INVALID_URL = '';
     
        protected $_messageTemplates = array(
            //self::INVALID_URL => 'Invalid URL'
            self::INVALID_URL => 'Please enter valid URL.' // custom message to maintain uniform
        );
     
        public function isValid($value, $context = null)
        {
            $value = (string) $value;
            $this->_setValue($value);
     		
			if(!preg_match('@^(http\:\/\/|https\:\/\/)?([a-z0-9][a-z0-9\-]*\.)+[a-z0-9][a-z0-9\-]*$@i', $value)){
				$this->_error(self::INVALID_URL);
	            return false;				
            }
   	        //|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i
			return true;	
        }
}

?>