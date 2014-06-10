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

class sapp_DateLessThanToday extends Zend_Validate_Abstract
{
    const DATE_INVALID = 'dateInvalid';

    protected $_messageTemplates = array(
        self::DATE_INVALID => "Date should be less than current date."
    );

    public function isValid($value)
    {
        $this->_setValue($value);

        $today = date('Y-m-d');
        $final_val = sapp_Global::change_date($value, 'database');

        // expecting $value to be YYYY-MM-DD
        if ($final_val == $today)
		{
            $this->_error(self::DATE_INVALID);
            return false;
        }

        return true;
    }
}
?>