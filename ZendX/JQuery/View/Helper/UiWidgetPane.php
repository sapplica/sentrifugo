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
 * @version     $Id: UiWidgetPane.php 20165 2010-01-09 18:57:56Z bkarwin $
 */

/**
 * @see ZendX_JQuery_View_Helper_UiWidget
 */
require_once "UiWidget.php";

/**
 * jQuery Pane Base class, adds captureStart/captureEnd functionality for panes.
 *
 * @uses 	   ZendX_JQuery_View_Helper_JQuery_Container
 * @package    ZendX_JQuery
 * @subpackage View
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class ZendX_JQuery_View_Helper_UiWidgetPane extends ZendX_JQuery_View_Helper_UiWidget
{
    /**
     * Capture Lock information
     *
     * @var array
     */
    protected $_captureLock = array();

    /**
     * Current capture additional information
     *
     * @var array
     */
    protected $_captureInfo = array();

    /**
     * Begin capturing content for layout container
     *
     * @param  string $id
     * @param  string $name
     * @param  array  $options
     * @return void
     */
    public function captureStart($id, $name, array $options=array())
    {
        if (array_key_exists($id, $this->_captureLock)) {
            require_once 'ZendX/JQuery/View/Exception.php';
            throw new ZendX_JQuery_View_Exception(sprintf('Lock already exists for id "%s"', $id));
        }

        $this->_captureLock[$id] = true;
        $this->_captureInfo[$id] = array(
            'name'  => $name,
            'options' => $options,
        );

        return ob_start();
    }

    /**
     * Finish capturing content for layout container
     *
     * @param  string $id
     * @return string
     */
    public function captureEnd($id)
    {
        if (!array_key_exists($id, $this->_captureLock)) {
            require_once 'ZendX/JQuery/View/Exception.php';
            throw new ZendX_JQuery_View_Exception(sprintf('No capture lock exists for id "%s"; nothing to capture', $id));
        }

        $content = ob_get_clean();
        extract($this->_captureInfo[$id]);
        unset($this->_captureLock[$id], $this->_captureInfo[$id]);
        return $this->_addPane($id, $name, $content, $options);
    }

    /**
     * Add an additional pane to the current Widget Container
     *
     * @param string $id
     * @param string $name
     * @param string $content
     * @param array  $options
     */
    abstract protected function _addPane($id, $name, $content, array $options=array());
}