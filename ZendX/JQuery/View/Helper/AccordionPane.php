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
 * @version     $Id: AccordionPane.php 20165 2010-01-09 18:57:56Z bkarwin $
 */

/**
 * @see ZendX_JQuery_View_Helper_UiWidgetPane
 */
require_once "UiWidgetPane.php";

/**
 * jQuery Accordion Pane, goes with Accordion Container
 *
 * @uses 	   ZendX_JQuery_View_Helper_AccordionContainer
 * @package    ZendX_JQuery
 * @subpackage View
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
  */
class ZendX_JQuery_View_Helper_AccordionPane extends ZendX_JQuery_View_Helper_UiWidgetPane
{
    /**
     * Add accordion pane to the accordion with $id
     *
     * Directly add an additional pane to the accordion with $id. The title
     * is to be given in the $options array as 'title' key. Additionally when
     * specified with no arguments, the helper returns itsself as object making
     * it possible to use {@link captureStart()} and {@link captureEnd()} methods.
     *
     * @param  string $id
     * @param  string $content
     * @param  array  $options
     * @return string|ZendX_JQuery_View_Helper_AccordionPane
     */
    public function accordionPane($id=null, $content='', array $options=array())
    {
        if(0 === func_num_args()) {
            return $this;
        }

        $name = '';
        if(isset($options['title'])) {
            $name = $options['title'];
            unset($options['title']);
        }

        $this->_addPane($id, $name, $content, $options);
        return '';
    }

    /**
     * Method hooks into Accordion Container and registeres new pane
     *
     * @param string $id
     * @param string $name
     * @param string $content
     * @param array  $options
     */
    protected function _addPane($id, $name, $content, array $options=array())
    {
        $this->view->accordionContainer()->addPane($id, $name, $content, $options);
    }
}