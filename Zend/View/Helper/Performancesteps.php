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
 * Performancesteps View Helper
 *
 * A View Helper that helps in performance appraisal initialisation.
 *
 *
 */
class Zend_View_Helper_Performancesteps extends Zend_View_Helper_Abstract 
{
    public function performancesteps()
    {
?>
    <div class="perf_steps">
        <div>Step-1</div>
        <div>Step-2</div>
        <div>Step-3</div>
    </div>
<?php 
    }
}//end of class
?>