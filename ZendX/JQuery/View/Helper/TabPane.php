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
 * @version     $Id: TabPane.php 20165 2010-01-09 18:57:56Z bkarwin $
 */

require_once "UiWidgetPane.php";

/**
 * jQuery Tabs Pane View Helper, goes with Tab Container
 *
 * @uses 	   Zend_Json, ZendX_JQuery_View_Helper_TabContainer
 * @package    ZendX_JQuery
 * @subpackage View
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ZendX_JQuery_View_Helper_TabPane extends ZendX_JQuery_View_Helper_UiWidgetPane
{
    /**
     * Add a tab pane to the tab container with the given $id.
     *
     * @param  string $id
     * @param  string $content
     * @param  array  $options
     * @return string always empty
     */
    public function tabPane($id=null, $content='', array $options=array())
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
     * Register new tab pane with tabContainer view helper.
     *
     * @see    ZendX_JQuery_View_Helper_TabContainer::addPane
     * @param  string $id
     * @param  string $name
     * @param  string $content
     * @param  array  $options
     * @return void
     */
    protected function _addPane($id, $name, $content, array $options=array())
    {
        $this->view->tabContainer()->addPane($id, $name, $content, $options);
    }
}