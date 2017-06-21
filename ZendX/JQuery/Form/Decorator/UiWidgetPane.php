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

require_once "Zend/Form/Decorator/Abstract.php";

/**
 * Abstract Form Decorator for all jQuery UI Pane View Helpers
 *
 * @package    ZendX_JQuery
 * @subpackage Form
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class ZendX_JQuery_Form_Decorator_UiWidgetPane extends Zend_Form_Decorator_Abstract
{
    /**
     * View helper
     * @var string
     */
    protected $_helper;

    /**
     * Element attributes
     * @var array
     */
    protected $_attribs;

    /**
     * jQuery option parameters
     * @var array
     */
    protected $_jQueryParams;

    /**
     * Container title
     * @var string
     */
    protected $_title;

    /**
     * Get view helper for rendering container
     *
     * @return string
     */
    public function getHelper()
    {
        if (null === $this->_helper) {
            require_once 'Zend/Form/Decorator/Exception.php';
            throw new Zend_Form_Decorator_Exception('No view helper specified fo UiWidgetContainer decorator');
        }
        return $this->_helper;
    }

    /**
     * Get element attributes
     *
     * @return array
     */
    public function getAttribs()
    {
        if (null === $this->_attribs) {
            $attribs = $this->getElement()->getAttribs();
            if (array_key_exists('jQueryParams', $attribs)) {
                $this->getJQueryParams();
                unset($attribs['jQueryParams']);
            }
            $this->_attribs = $attribs;
        }
        return $this->_attribs;
    }

    /**
     * Get jQuery option parameters
     *
     * @return array
     */
    public function getJQueryParams()
    {
        if (null === $this->_jQueryParams) {
            $attribs = $this->getElement()->getAttribs();
            $this->_jQueryParams = array();
            if (array_key_exists('jQueryParams', $attribs)) {
                $this->_jQueryParams = $attribs['jQueryParams'];
            }

            $options = $this->getOptions();
            if (array_key_exists('jQueryParams', $options)) {
                $this->_jQueryParams = array_merge($this->_jQueryParams, $options['jQueryParams']);
                $this->removeOption('jQueryParams');
            }
        }

        // Ensure we have a title param
        if (!array_key_exists('title', $this->_jQueryParams)) {
            require_once "Zend/Form/Decorator/Exception.php";
            throw new Zend_Form_Decorator_Exception("UiWidgetPane Decorators have to have a jQueryParam 'title' to render. This title can been set via setJQueryParam('title') on the parent element.");
        }

        return $this->_jQueryParams;
    }

    /**
     * Render an jQuery UI Widget Pane using its associated view helper
     *
     * @throws Zend_Form_Decorator_Exception
     * @param  string $content
     * @return string
     * @throws Zend_Form_Decorator_Exception if element or view are not registered
     */
    public function render($content)
    {
        $element = $this->getElement();
        $view    = $element->getView();
        if (null === $view) {
            return $content;
        }

        $jQueryParams = $this->getJQueryParams();
        $attribs     = array_merge($this->getAttribs(), $this->getOptions());

        if(isset($jQueryParams['title']) && !empty($jQueryParams['title'])) {
            if (null !== ($translator = $element->getTranslator())) {
                $jQueryParams['title'] = $translator->translate($jQueryParams['title']);
            }
        }

        if(isset($jQueryParams['containerId'])) {
            $id = $jQueryParams['containerId']."-container";
        } else {
            require_once "Zend/Form/Decorator/Exception.php";
            throw new Zend_Form_Decorator_Exception("UiWidgetPane Decorators have to have a jQueryParam 'containerId', to point at their parent container. This containerId has been set via setAttrib('id') on the parent element.");
        }

        $helper = $this->getHelper();

        return $view->$helper($id, $content, $jQueryParams, $attribs);
    }
}