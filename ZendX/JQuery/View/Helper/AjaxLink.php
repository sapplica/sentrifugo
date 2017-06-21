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
 * @version     $Id: AjaxLink.php 21865 2010-04-16 07:26:51Z beberlei $
 */

/**
 * @see Zend_View_Helper_HtmlElement
 */
include_once "Zend/View/Helper/HtmlElement.php";

/**
 * jQuery Accordion Pane, goes with Accordion Container
 *
 * @uses 	   Zend_Json
 * @package    ZendX_JQuery
 * @subpackage View
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ZendX_JQuery_View_Helper_AjaxLink extends Zend_View_Helper_HtmlElement
{
    /**
     * Static because multiple instances accross views of AjaxLink could reset the counter and a
     * subcontainer because of this single private class variable seems too much overhead.
     *
     * @staticvar Integer
     */
    private static $currentLinkCallbackId = 1;

    /**
     * Create an anchor that enables ajax-based requests and handling of the response.
     *
     * This helper creates links that make XmlHttpRequests to the server. It allows to
     * inject the response into the DOM. Fancy effects going with the links can be enabled
     * via simple callback shortnames. The functionality is mostly controlled by the $options
     * array:
     *
     * $options
     *  Key				Behaviour
     *  =================================================================================
     *  'update'        Update a container with the content fetched from $url
     *  'method'        Explicit Requesting method mimicing the jQuery functionality: GET, POST
     *  'inline'        True or false, wheater to inline the javascript in onClick=""
     * 					atttribute or append it to jQuery onLoad Stack.
     *  'complete'      String specifies javascript called after successful request or a
     * 					shortname of a jQuery effect that should be applied to the 'update' element.
     *  'beforeSend'	String specifies javascript called before the request is sent, or a
     * 					shortname of a jQuery effect that should be applied to the link clicked.
     *  'noscript'		True/false, include a noscript variant that directly requests
     * 					the given $url (make sure to check $request->isXmlHttpRequest())
     *  'dataType'		What type of data is the response returning? text, html, json?
     *  'title'			HTML Attribute title of the Anchor
     *  'class'			HTML Attribute class of the Anchor
     *  'id'			HTML Attribute id of the Anchor
     *  'attribs'		Array of Key-Value pairs with HTML Attribute names and their content.
     *
     * BeforeSend Callback:
     * Can include shortcuts as a string assignment to fire of effects before sending of request.
     * Possible shortcuts are 'fadeOut', 'fadeOutSlow', 'hide', 'hideSlow', 'slideUp', 'flash',
     * @example $options = array('beforeSend' => 'hideSlow', 'complete' => 'show');
     *
     * @link   http://docs.jquery.com/Ajax
     * @param  String $label Urls Title
     * @param  String $url Link to Point to
     * @param  Array $options
     * @param  Array $params Key Value Pairs of GET/POST Parameters
     * @return String
     */
    public function ajaxLink($label, $url, $options=null, $params=null)
    {
        $jquery = $this->view->jQuery();
        $jquery->enable();

        $jqHandler = (ZendX_JQuery_View_Helper_JQuery::getNoConflictMode()==true)?'$j':'$';

        $attribs = array();
        if(isset($options['attribs']) && is_array($options['attribs'])) {
            $attribs = $options['attribs'];
        }

        //
        // The next following 4 conditions check for html attributes that the link might need
        //
        if(empty($options['noscript']) || $options['noscript'] == false) {
            $attribs['href'] = "#";
        } else {
            $attribs['href'] = $url;
        }

        if(!empty($options['title'])) {
            $attribs['title'] = $options['title'];
        }

        // class value is an array because the jQuery CSS selector
        // click event needs its own classname later on
        if(!isset($attribs['class'])) {
            $attribs['class'] = array();
        } elseif(is_string($attribs['class'])) {
            $attribs['class'] = explode(" ", $attribs['class']);
        }
        if(!empty($options['class'])) {
            $attribs['class'][] = $options['class'];
        }

        if(!empty($options['id'])) {
            $attribs['id'] = $options['id'];
        }

        //
        // Execute Javascript inline?
        //
        $inline = false;
        if(!empty($options['inline']) && $options['inline'] == true) {
            $inline = true;
        }

        //
        // Detect the callbacks:
        // Just those two callbacks, beforeSend and complete can be defined for the $.get and $.post options.
        // Pick all the defined callbacks and put them on their respective stacks.
        //
        $callbacks = array('beforeSend' => null, 'complete' => null);
        if(isset($options['beforeSend'])) {
            $callbacks['beforeSend'] = $options['beforeSend'];
        }
        if(isset($options['complete'])) {
            $callbacks['complete'] = $options['complete'];
        }

        $updateContainer = false;
        if(!empty($options['update']) && is_string($options['update'])) {
            $updateContainer = $options['update'];

            // Additionally check if there is a callback complete that is a shortcut to be executed
            // on the specified update container
            if(!empty($callbacks['complete'])) {
                switch(strtolower($callbacks['complete'])) {
                    case 'show':
                        $callbacks['complete'] = sprintf("%s('%s').show();", $jqHandler, $updateContainer);
                        break;
                    case 'showslow':
                        $callbacks['complete'] = sprintf("%s('%s').show('slow');", $jqHandler, $updateContainer);
                        break;
                    case 'shownormal':
                        $callbacks['complete'] = sprintf("%s('%s').show('normal');", $jqHandler, $updateContainer);
                        break;
                    case 'showfast':
                        $callbacks['complete'] = sprintf("%s('%s').show('fast');", $jqHandler, $updateContainer);
                        break;
                    case 'fadein':
                        $callbacks['complete'] = sprintf("%s('%s').fadeIn('normal');", $jqHandler, $updateContainer);
                        break;
                    case 'fadeinslow':
                        $callbacks['complete'] = sprintf("%s('%s').fadeIn('slow');", $jqHandler, $updateContainer);
                        break;
                    case 'fadeinfast':
                        $callbacks['complete'] = sprintf("%s('%s').fadeIn('fast');", $jqHandler, $updateContainer);
                        break;
                    case 'slidedown':
                        $callbacks['complete'] = sprintf("%s('%s').slideDown('normal');", $jqHandler, $updateContainer);
                        break;
                    case 'slidedownslow':
                        $callbacks['complete'] = sprintf("%s('%s').slideDown('slow');", $jqHandler, $updateContainer);
                        break;
                    case 'slidedownfast':
                        $callbacks['complete'] = sprintf("%s('%s').slideDown('fast');", $jqHandler, $updateContainer);
                        break;
                }
            }
        }

        if(empty($options['dataType'])) {
            $options['dataType'] = "html";
        }

        $requestHandler = $this->_determineRequestHandler($options, (count($params)>0)?true:false);

        $callbackCompleteJs = array();
        if($updateContainer != false) {
            if($options['dataType'] == "text") {
                $callbackCompleteJs[] = sprintf("%s('%s').text(data);", $jqHandler, $updateContainer);
            } else {
                $callbackCompleteJs[] = sprintf("%s('%s').html(data);", $jqHandler, $updateContainer);
            }
        }
        if($callbacks['complete'] != null) {
            $callbackCompleteJs[] = $callbacks['complete'];
        }

        if(isset($params) && count($params) > 0) {
            $params = ZendX_JQuery::encodeJson($params);
        } else {
            $params = '{}';
        }

        $js = array();
        if($callbacks['beforeSend'] != null) {
            switch(strtolower($callbacks['beforeSend'])) {
                case 'fadeout':
                    $js[] = sprintf("%s(this).fadeOut();", $jqHandler);
                    break;
                case 'fadeoutslow':
                    $js[] = sprintf("%s(this).fadeOut('slow');", $jqHandler);
                    break;
                case 'fadeoutfast':
                    $js[] = sprintf("%s(this).fadeOut('fast');", $jqHandler);
                    break;
                case 'hide':
                    $js[] = sprintf("%s(this).hide();", $jqHandler);
                    break;
                case 'hideslow':
                    $js[] = sprintf("%s(this).hide('slow');", $jqHandler);
                    break;
                case 'hidefast':
                    $js[] = sprintf("%s(this).hide('fast');", $jqHandler);
                    break;
                case 'slideup':
                    $js[] = sprintf("%s(this).slideUp(1000);", $jqHandler);
                    break;
                default:
                    $js[] = $callbacks['beforeSend'];
                    break;
            }
        }

        switch($requestHandler) {
            case 'GET':
                $js[] = sprintf("%s.get('%s', %s, function(data, textStatus) { %s }, '%s');return false;",
                    $jqHandler, $url, $params, implode(" ", $callbackCompleteJs), $options['dataType']);
                break;
            case 'POST':
                $js[] = sprintf("%s.post('%s', %s, function(data, textStatus) { %s }, '%s');return false;",
                    $jqHandler, $url, $params, implode(" ", $callbackCompleteJs), $options['dataType']);
                break;
        }

        $js = implode($js);

        if($inline == true) {
            $attribs['onclick'] = $js;
        } else {
            if(!isset($attribs['id'])) {
                $clickClass = sprintf("ajaxLink%d", ZendX_JQuery_View_Helper_AjaxLink::$currentLinkCallbackId);
                ZendX_JQuery_View_Helper_AjaxLink::$currentLinkCallbackId++;

                $attribs['class'][] = $clickClass;
                $onLoad = sprintf("%s('a.%s').click(function() { %s });", $jqHandler, $clickClass, $js);
            } else {
                $onLoad = sprintf("%s('a#%s').click(function() { %s });", $jqHandler, $attribs['id'], $js);
            }

            $jquery->addOnLoad($onLoad);
        }

        if(count($attribs['class']) > 0) {
            $attribs['class'] = implode(" ", $attribs['class']);
        } else {
            unset($attribs['class']);
        }

        $html = '<a'
            . $this->_htmlAttribs($attribs)
            . '>'
            . $label
            . '</a>';
        return $html;
    }

    /**
     * Determine which request method (GET or POST) should be used.
     *
     * Normally the request method is determined implicitly by the rule,
     * if addiotional params are sent, POST, if not GET. You can overwrite
     * this behaviiour by implicitly setting $options['method'] = "POST|GET";
     *
     * @param  Array   $options
     * @param  Boolean $hasParams
     * @return String
     */
    protected function _determineRequestHandler($options, $hasParams)
    {
        if(isset($options['method']) && in_array(strtoupper($options['method']), array('GET', 'POST'))) {
            return strtoupper($options['method']);
        }
        $requestHandler = "GET";
        if($hasParams == true) {
            $requestHandler = "POST";
        }
        return $requestHandler;
    }
}