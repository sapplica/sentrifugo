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
 * @version     $Id: UiWidget.php 20165 2010-01-09 18:57:56Z bkarwin $
 */

/**
 * @see ZendX_JQuery_View_Helper_UiWidget
 */
require_once "Zend/View/Helper/HtmlElement.php";

/**
 * @see ZendX_JQuery
 */
require_once "ZendX/JQuery.php";

/**
 * jQuery Ui Widget Base class
 *
 * @uses 	   ZendX_JQuery_View_Helper_JQuery_Container
 * @package    ZendX_JQuery
 * @subpackage View
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class ZendX_JQuery_View_Helper_UiWidget extends Zend_View_Helper_HtmlElement
{
    /**
     * Contains reference to the jQuery view helper
     *
     * @var ZendX_JQuery_View_Helper_JQuery_Container
     */
    protected $jquery;

    /**
     * Set view and enable jQuery Core and UI libraries
     *
     * @param  Zend_View_Interface $view
     * @return ZendX_JQuery_View_Helper_Widget
     */
    public function setView(Zend_View_Interface $view)
    {
        parent::setView($view);
        $this->jquery = $this->view->jQuery();
        $this->jquery->enable()
                     ->uiEnable();
        return $this;
    }

    /**
     * Helps with building the correct Attributes Array structure.
     *
     * @param String $id
     * @param String $value
     * @param Array $attribs
     * @return Array $attribs
     */
	protected function _prepareAttributes($id, $value, $attribs)
	{
        if(!isset($attribs['id'])) {
            $attribs['id'] = $id;
        }
        $attribs['name']  = $id;
        $attribs['value'] = (string) $value;

        return $attribs;
	}
}