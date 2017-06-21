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
 * @version     $Id: AccordionContainer.php 20165 2010-01-09 18:57:56Z bkarwin $
 */

/**
 * @see ZendX_JQuery_View_Helper_UiWidget
 */
require_once "ZendX/JQuery/View/Helper/UiWidget.php";

/**
 * jQuery Accordion View Helper
 *
 * @uses 	   Zend_Json
 * @package    ZendX_JQuery
 * @subpackage View
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
  */
class ZendX_JQuery_View_Helper_AccordionContainer extends ZendX_JQuery_View_Helper_UiWidget
{
    /**
     * @var array
     */
    protected $_panes = array();

    /**
     * @var string
     */
    protected $_elementHtmlTemplate = null;

    /**
     * Add Accordion Pane for the Accordion-Id
     *
     * @param  string $id
     * @param  string $name
     * @param  string $content
     * @return ZendX_JQuery_View_Helper_AccordionContainer
     */
    public function addPane($id, $name, $content, array $options=array())
    {
        if(!isset($this->_panes[$id])) {
            $this->_panes[$id] = array();
        }
        if(strlen($name) == 0 && isset($options['title'])) {
            $name = $options['title'];
        }
        $this->_panes[$id][] = array('name' => $name, 'content' => $content, 'options' => $options);
        return $this;
    }

    /**
     * Render Accordion with the currently registered elements.
     *
     * If no arguments are given, the accordion object is returned so that
     * chaining the {@link addPane()} function allows to register new elements
     * for an accordion.
     *
     * @link   http://docs.jquery.com/UI/Accordion
     * @param  string $id
     * @param  array  $params
     * @param  array  $attribs
     * @return string|ZendX_JQuery_View_Helper_AccordionContainer
     */
    public function accordionContainer($id=null, array $params=array(), array $attribs=array())
    {
        if(0 === func_num_args()) {
            return $this;
        }

        if(!isset($attribs['id'])) {
            $attribs['id'] = $id;
        }

        $html = "";
        if(isset($this->_panes[$id])) {
            foreach($this->_panes[$id] AS $element) {
                $html .= sprintf($this->getElementHtmlTemplate(), $element['name'], $element['content']).PHP_EOL;
            }

            if(count($params) > 0) {
                $params = ZendX_JQuery::encodeJson($params);
            } else {
                $params = "{}";
            }

            $js = sprintf('%s("#%s").accordion(%s);',
                ZendX_JQuery_View_Helper_JQuery::getJQueryHandler(),
                $attribs['id'],
                $params
            );
            $this->jquery->addOnLoad($js);

            $html = $this->getAccordionTemplate($attribs, $html);
        }
        return $html;
    }

    /**
     * @param  array $attribs
     * @param  string $html
     * @return string
     */
    protected function getAccordionTemplate($attribs, $html)
    {
        if(version_compare($this->jquery->getUiVersion(), "1.7.0") >= 0) {
            $html = '<div'
                  . $this->_htmlAttribs($attribs)
                  . '>'.PHP_EOL
                  . $html
                  . '</div>'.PHP_EOL;
        } else {
            $html = '<ul'
                  . $this->_htmlAttribs($attribs)
                  . '>'.PHP_EOL
                  . $html
                  . '</ul>'.PHP_EOL;
        }
        return $html;
    }

    /**
     * @return string
     */
    protected function getElementHtmlTemplate()
    {
        if($this->_elementHtmlTemplate == null) {
            if(version_compare($this->jquery->getUiVersion(), "1.7.0") >= 0) {
                $this->_elementHtmlTemplate = '<h3><a href="#">%s</a></h3><div>%s</div>';
            } else {
                $this->_elementHtmlTemplate = '<li class="ui-accordion-group"><a href="#" class="ui-accordion-header">%s</a><div class="ui-accordion-content">%s</div></li>';
            }
        }
        return $this->_elementHtmlTemplate;
    }

    /**
     * Set the accordion element template
     *
     * @param  string $htmlTemplate
     * @return ZendX_JQuery_View_Helper_AccordionContainer
     */
    public function setElementHtmlTemplate($htmlTemplate)
    {
        if(substr_count($htmlTemplate, '%s') != 2) {
            require_once "ZendX/JQuery/View/Exception.php";
            throw new ZendX_JQuery_View_Exception(
                "Accordion Container HTML Template requires two sprintf() string replace markers '%s'."
            );
        }
        $this->_elementHtmlTemplate = $htmlTemplate;
        return $this;
    }
}