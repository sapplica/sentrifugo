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
 * @category   ZendX
 * @package    ZendX_Application
 * @subpackage Resource
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Jquery.php 20240 2010-01-13 04:51:56Z matthew $
 */

/**
 * JQuery application resource
 *
 * Example configuration:
 * <pre>
 *   resources.Jquery.noconflictmode = false        ; default
 *   resources.Jquery.version = 1.7.1               ; <null>
 *   resources.Jquery.localpath = "/foo/bar"
 *   resources.Jquery.uienable = true;
 *   resources.Jquery.ui_enable = true;
 *   resources.Jquery.uiversion = 0.7.7;
 *   resources.Jquery.ui_version = 0.7.7;
 *   resources.Jquery.uilocalpath = "/bar/foo";
 *   resources.Jquery.ui_localpath = "/bar/foo";
 *   resources.Jquery.cdn_ssl = false
 *   resources.Jquery.render_mode = 255 ; default
 *   resources.Jquery.rendermode = 255 ; default
 *
 *   resources.Jquery.javascriptfile = "/some/file.js"
 *   resources.Jquery.javascriptfiles.0 = "/some/file.js"
 *   resources.Jquery.stylesheet = "/some/file.css"
 *   resources.Jquery.stylesheets.0 = "/some/file.css"
 * </pre>
 *
 * Resource for settings JQuery options
 *
 * @uses       Zend_Application_Resource_ResourceAbstract
 * @category   ZendX
 * @package    ZendX_Application
 * @subpackage Resource
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ZendX_Application_Resource_Jquery
    extends Zend_Application_Resource_ResourceAbstract
{
    /**
     * @var ZendX_JQuery_View_Helper_JQuery_Container
     */
    protected $_jquery;

    /**
     * @var Zend_View
     */
    protected $_view;

    /**
     * Defined by Zend_Application_Resource_Resource
     *
     * @return ZendX_JQuery_View_Helper_JQuery_Container
     */
    public function init()
    {
        return $this->getJquery();
    }

    /**
     * Retrieve JQuery View Helper
     *
     * @return ZendX_JQuery_View_Helper_JQuery_Container
     */
    public function getJquery()
    {
        if (null === $this->_jquery) {
            $this->getBootstrap()->bootstrap('view');
            $this->_view = $this->getBootstrap()->view;

            ZendX_JQuery::enableView($this->_view);
            $this->_parseOptions($this->getOptions());

            $this->_jquery = $this->_view->jQuery();
        }

        return $this->_jquery;
    }

    /**
     * Parse options to find those pertinent to jquery helper and invoke them
     *
     * @param  array $options
     * @return void
     */
    protected function _parseOptions(array $options)
    {
        $options = array_merge($options, array('cdn_ssl' => false));

        foreach ($options as $key => $value) {
            switch(strtolower($key)) {
                case 'noconflictmode':
                    if (!(bool)$value) {
                        ZendX_JQuery_View_Helper_JQuery::disableNoConflictMode();
                    } else {
                        ZendX_JQuery_View_Helper_JQuery::enableNoConflictMode();
                    }
                    break;
                case 'version':
                    $this->_view->JQuery()->setVersion($value);
                    break;
                case 'localpath':
                    $this->_view->JQuery()->setLocalPath($value);
                    break;
                case 'uiversion':
                case 'ui_version':
                    $this->_view->JQuery()->setUiVersion($value);
                    break;
                case 'uilocalpath':
                case 'ui_localpath':
                    $this->_view->JQuery()->setUiLocalPath($value);
                    break;
                case 'cdn_ssl':
                    $this->_view->JQuery()->setCdnSsl($value);
                    break;
                case 'render_mode':
                case 'rendermode':
                    $this->_view->JQuery()->setRenderMode($value);
                    break;
                case 'javascriptfile':
                    $this->_view->JQuery()->addJavascriptFile($value);
                    break;
                case 'javascriptfiles':
                    foreach($options['javascriptfiles'] as $file) {
                        $this->_view->JQuery()->addJavascriptFile($file);
                    }
                    break;
                case 'stylesheet':
                    $this->_view->JQuery()->addStylesheet($value);
                    break;
                case 'stylesheets':
                    foreach ($value as $stylesheet) {
                        $this->_view->JQuery()->addStylesheet($stylesheet);
                    }
                    break;
            }
        }

        if ((isset($options['uienable']) && (bool) $options['uienable'])
            || (isset($options['ui_enable']) && (bool) $options['ui_enable'])
            || (!isset($options['ui_enable']) && !isset($options['uienable'])))
        {
            $this->_view->JQuery()->uiEnable();
        } else {
            $this->_view->JQuery()->uiDisable();
        }
    }
}
