<?php

namespace LBanas\Components;

/**
 * Class Config, contains configuration variables.
 * @category Theme
 * @package LBanasComponents
 * @author Lukasz Banas <lukaszbanas4@gmail.com>
 * @license MIT <http://opensource.org/licenses/MIT>
 * @link https://github.com/lukaszbanas/Theme
 * @since 0.0.1
 * @TODO implement \Serializable and conversion from / to xml, yml, json
 */
class Config implements \Serializable
{
    /**
     * @var string
     */
    private $themeName = 'develop';

    /**
     * @var string
     */
    private $path = '/wp-content/themes/';

    /**
     * @var string
     */
    private $pathToJs = '/js/';

    /**
     * @var string
     */
    private $pathToCss = '/css/';

    /**
     * @var string
     */
    private $pathToTmp = '/page-templates/';

    /**
     * @var string
     */
    private $pathToClass = '/class/';

    /**
     * @var string
     */
    private $pathToLang = '/lang/';

    /**
     * @var string
     */
    private $pathToImg = '/img/';

    /**
     * @var bool
     */
    private $debug = true;

    /**
     * @var bool
     */
    private $ajax = false;

    /**
     * @var string
     */
    private $templateProd = 'Develop';

    /**
     * @var string
     */
    private $templateDev = 'Develop';

    /**
     * @var bool
     */
    private $twigCache = false;

    /**
     * @var string
     */
    private $twigCacheDir = 'cache';

    /**
     * @var bool
     */
    private $twigDebug = false;

    /**
     * @var bool
     */
    private $showAdminOptions = false;

    /**
     * @var bool
     */
    private $disableRss = false;

    /**
     * @var array
     */
    private $menus = array(
        array(
            'name' => 'main_menu_1',
            'extra' => array(
                'description' => 'Glowne menu'
            )
        ),
        array(
            'name' => 'sub_menu_1',
            'extra' => array(
                'description' => 'Menu w stopce'
            )
        ),
    );

    /**
     * @var array
     */
    private $sidebars = array(
        array(
            'name'  => 'Widget - O nas',
            'extra' => array(
                'id'          => 'sidebar-1',
                'description' => 'Miejsce na widgety z lewej strony panelu `O Nas`',
                'before_widget' => '<li id="%1$s" class="list-group-item widget %2$s">',
                'after_widget' => '</li>',
            )),
    );

    /**
     * @var array
     */
    private $stylesheets = array(
        array(
            'name'  => 'reset',
            'extra' => array(
                'filename' => 'reset.css',
                'version'  => '1.0')),
        array(
            'name'  => 'animate',
            'extra' => array(
                'filename' => 'animate.css',
                'version'  => '1.0')),
        array(
            'name'  => 'screen',
            'extra' => array(
                'filename' => 'screen.css',
                'version'  => '1.0')),
    );

    /**
     * @var array
     */
    private $scripts = array(
        array(
            'name'  => 'jquery',
            'extra' => array(
                'filename'     => 'jquery.js',
                'dependencies' => false,
                'version'      => '1.0',
                'inFooter'     => false
            ),
        ),
        array(
            'name'  => 'scrollTo',
            'extra' => array(
                'filename'     => 'jquery.scrollTo.min.js',
                'dependencies' => array('jquery'),
                'version'      => '1.0',
                'inFooter'     => true
            ),
        ),
        array(
            'name'  => 'scripts',
            'extra' => array(
                'filename'     => 'scripts.js',
                'dependencies' => array('jquery'),
                'version'      => '1.1',
                'inFooter'     => true
            ),
        ),
    );

    public function __get($property)
    {
        if (property_exists($this, $property)) {
            return $this->{$property};
        }
    }

    /**
     * @return array
     */
    public function getScripts()
    {
        return $this->scripts;
    }


    /**
     * @return array
     */
    public function getStylesheets()
    {
        return $this->stylesheets;
    }


    /**
     * @return array
     */
    public function getSidebars()
    {
        return $this->sidebars;
    }


    /**
     * @return bool
     */
    public function isAjax()
    {
        return $this->ajax;
    }


    /**
     * @param $ajax
     * @return $this
     */
    public function setAjax($ajax)
    {
        $this->ajax = $ajax;

        return $this;
    }


    /**
     * @return bool
     */
    public function isDebug()
    {
        return $this->debug;
    }


    /**
     * @param $debug
     * @return $this
     */
    public function setDebug($debug)
    {
        $this->debug = $debug;

        return $this;
    }


    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getPathToClass()
    {
        return $this->pathToClass;
    }

    /**
     * @return string
     */
    public function getPathToCss()
    {
        return $this->pathToCss;
    }

    /**
     * @return string
     */
    public function getPathToImg()
    {
        return $this->pathToImg;
    }

    /**
     * @return string
     */
    public function getPathToJs()
    {
        return $this->pathToJs;
    }

    /**
     * @return string
     */
    public function getPathToLang()
    {
        return $this->pathToLang;
    }

    /**
     * @return string
     */
    public function getPathToTmp()
    {
        return $this->pathToTmp;
    }

    /**
     * @return bool
     */
    public function isShowAdminOptions()
    {
        return $this->showAdminOptions;
    }

    /**
     * @param $showAdminOptions
     * @return $this
     */
    public function setShowAdminOptions($showAdminOptions)
    {
        $this->showAdminOptions = $showAdminOptions;

        return $this;
    }

    /**
     * @return string
     */
    public function getTemplateDev()
    {
        return $this->templateDev;
    }

    /**
     * @return string
     */
    public function getTemplateProd()
    {
        return $this->templateProd;
    }

    /**
     * @return string
     */
    public function getThemeName()
    {
        return $this->themeName;
    }

    /**
     * @return bool
     */
    public function isTwigCache()
    {
        return $this->twigCache;
    }

    /**
     * @param $twigCache
     * @return $this
     */
    public function setTwigCache($twigCache)
    {
        $this->twigCache = $twigCache;

        return $this;
    }

    /**
     * @return string
     */
    public function getTwigCacheDir()
    {
        return $this->twigCacheDir;
    }

    /**
     * @return bool
     */
    public function isTwigDebug()
    {
        return $this->twigDebug;
    }

    /**
     * @param $twigDebug
     * @return $this
     */
    public function setTwigDebug($twigDebug)
    {
        $this->twigDebug = $twigDebug;

        return $this;
    }

    /**
     * @return array
     */
    public function getMenus()
    {
        return $this->menus;
    }

    public function isDisableRss()
    {
        return $this->disableRss;
    }

    public function serialize()
    {

    }

    public function unserialize($serialized)
    {

    }
}
