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
 * @version     $Id: UiWidgetElement.php 20165 2010-01-09 18:57:56Z bkarwin $
 */

/**
 * @see Zend_Form_Decorator_ViewHelper
 */
require_once "Zend/Form/Decorator/ViewHelper.php";

/**
 * @see ZendX_JQuery_Form_Decorator_UiWidgetElementMarker
 */
require_once "ZendX/JQuery/Form/Decorator/UiWidgetElementMarker.php";

/**
 * Abstract Form Decorator for all jQuery UI Form Elements
 *
 * @package    ZendX_JQuery
 * @subpackage Form
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ZendX_JQuery_Form_Decorator_UiWidgetElement
    extends Zend_Form_Decorator_ViewHelper
    implements ZendX_JQuery_Form_Decorator_UiWidgetElementMarker
{
    /**
     * Element attributes
     *
     * @var array
     */
    protected $_attribs;

    /**
     * jQuery UI View Helper
     *
     * @var ZendX_JQuery_View_Helper_UiWidget
     */
    public $helper;

    /**
     * jQuery related attributes/options
     *
     * @var array
     */
    protected $_jQueryParams = array();

    /**
     * Get element attributes
     *
     * @return array
     */
    public function getElementAttribs()
    {
        if (null === $this->_attribs) {
            if($this->_attribs = parent::getElementAttribs()) {
                if (array_key_exists('jQueryParams', $this->_attribs)) {
                    $this->setJQueryParams($this->_attribs['jQueryParams']);
                    unset($this->_attribs['jQueryParams']);
                }
            }
        }

        return $this->_attribs;
    }

    /**
     * Set a single jQuery option parameter
     *
     * @param  string $key
     * @param  mixed $value
     * @return ZendX_JQuery_Form_Decorator_UiWidgetElement
     */
    public function setJQueryParam($key, $value)
    {
        $this->_jQueryParams[(string) $key] = $value;
        return $this;
    }

    /**
     * Set jQuery option parameters
     *
     * @param  array $params
     * @return ZendX_JQuery_Form_Decorator_UiWidgetElement
     */
    public function setJQueryParams(array $params)
    {
        $this->_jQueryParams = array_merge($this->_jQueryParams, $params);
        return $this;
    }

    /**
     * Retrieve a single jQuery option parameter
     *
     * @param  string $key
     * @return mixed|null
     */
    public function getJQueryParam($key)
    {
        $this->getElementAttribs();
        $key = (string) $key;
        if (array_key_exists($key, $this->_jQueryParams)) {
            return $this->_jQueryParams[$key];
        }

        return null;
    }

    /**
     * Get jQuery option parameters
     *
     * @return array
     */
    public function getJQueryParams()
    {
        $this->getElementAttribs();
        return $this->_jQueryParams;
    }

    /**
     * Render an jQuery UI Widget element using its associated view helper
     *
     * @param  string $content
     * @return string
     * @throws Zend_Form_Decorator_Exception if element or view are not registered
     */
    public function render($content)
    {
        $element = $this->getElement();
        $view = $element->getView();
        if (null === $view) {
            require_once 'Zend/Form/Decorator/Exception.php';
            throw new Zend_Form_Decorator_Exception('UiWidgetElement decorator cannot render without a registered view object');
        }

        if(method_exists($element, 'getJQueryParams')) {
            $this->setJQueryParams($element->getJQueryParams());
        }
        $jQueryParams = $this->getJQueryParams();

        $helper    = $this->getHelper();
        $separator = $this->getSeparator();
        $value     = $this->getValue($element);
        $attribs   = $this->getElementAttribs();
        $name      = $element->getFullyQualifiedName();

        $id = $element->getId();
        $attribs['id'] = $id;

        $elementContent = $view->$helper($name, $value, $jQueryParams, $attribs);
        switch ($this->getPlacement()) {
            case self::APPEND:
                return $content . $separator . $elementContent;
            case self::PREPEND:
                return $elementContent . $separator . $content;
            default:
                return $elementContent;
        }
    }
}