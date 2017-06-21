<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category    ZendX
 * @package     ZendX_JQuery
 * @subpackage  View
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license     http://framework.zend.com/license/new-bsd     New BSD License
 * @version     $Id: AutoComplete.php 20165 2010-01-09 18:57:56Z bkarwin $
 */

require_once "Zend/Controller/Action/Helper/AutoComplete/Abstract.php";

class ZendX_JQuery_Controller_Action_Helper_AutoComplete
extends Zend_Controller_Action_Helper_AutoComplete_Abstract
{
    /**
     * Validate autocompletion data
     *
     * @param  mixed $data
     * @return boolean
     */
    public function validateData($data)
    {
        if (!is_array($data)) {
            return false;
        }

        return true;
    }

    /**
     * Prepare autocompletion data
     *
     * @param  mixed   $data
     * @param  boolean $keepLayouts
     * @return mixed
     */
    public function prepareAutoCompletion($data, $keepLayouts = false)
    {
        if (!$this->validateData($data)) {
            /**
             * @see Zend_Controller_Action_Exception
             */
            require_once 'Zend/Controller/Action/Exception.php';
            throw new Zend_Controller_Action_Exception('Invalid data passed for autocompletion');
        }

        $data = (array) $data;
        $output = "";
        foreach($data AS $k => $v) {
            if(is_numeric($k)) {
                $output .= $v."\n";
            } else {
                $output .= $k."|".$v."\n";
            }
        }

        if (!$keepLayouts) {
            $this->disableLayouts();
        }

        return $output;
    }
}