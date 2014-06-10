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
 * @version     $Id: DialogContainer.php 20744 2010-01-29 09:55:19Z beberlei $
 */

/**
 * @see ZendX_JQuery_View_Helper_UiWidget
 */
require_once "ZendX/JQuery/View/Helper/UiWidget.php";

/**
 * jQuery Dialog View Helper
 *
 * @package    ZendX_JQuery
 * @subpackage View
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ZendX_JQuery_View_Helper_DialogContainer extends ZendX_JQuery_View_Helper_UiWidget
{
    /**
     * Create a jQuery UI Dialog filled with the given content
     *
     * @link   http://docs.jquery.com/UI/Dialog
     * @param  string $id
     * @param  string $content
     * @param  array $params
     * @param  array $attribs
     * @return string
     */
    public function dialogContainer($id, $content, $params=array(), $attribs=array())
    {
        if (!array_key_exists('id', $attribs)) {
            $attribs['id'] = $id;
        }

        if(count($params) > 0) {
            $params = ZendX_JQuery::encodeJson($params);
        } else {
            $params = "{}";
        }

        $js = sprintf('%s("#%s").dialog(%s);',
                ZendX_JQuery_View_Helper_JQuery::getJQueryHandler(),
                $attribs['id'],
                $params
        );
        $this->jquery->addOnLoad($js);

        $html = '<div'
                . $this->_htmlAttribs($attribs)
                . '>'
                . $content
                . '</div>';
        return $html;
    }
}