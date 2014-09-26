<?php

/* * ******************************************************************************* 
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
 * ****************************************************************************** */

/**
 * Description of Htmlhelper
 *
 * @author ramakrishna
 */
class sapp_Htmlhelper 
{    
    /**
     * This function will help in service request view page.
     * @param string $class    = css class to div tag
     * @param string $heading  = heading of the div
     * @param string $value    = value to display.
     */
    public static function request_view_helper($class,$heading,$value)
    {
?>
        <div class="<?php echo $class;?>">
            <label><?php echo $heading;?> <b>:</b> </label>
            <span><?php echo $value; ?></span>
        </div>
<?php 
    }
    
	public static function getExtension($fileName)
	{
		$i = strrpos($fileName,".");
		if (!$i) { return ""; }
		$l = strlen($fileName) - $i;
		$extension = substr($fileName,$i+1,$l);
		return $extension;
	}
}//end of class
