<?php

namespace LBanas\Components;

use Monolog\Logger;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class Display, should be final and result should be send to client.
 * @category Theme
 * @package LBanasComponents
 * @author Lukasz Banas <lukaszbanas4@gmail.com>
 * @license MIT <http://opensource.org/licenses/MIT>
 * @link https://github.com/lukaszbanas/Theme
 * @since 0.0.1
 */
class Display extends Page implements \LBanas\Components\HttpResponse
{
    /**
     * Menus collection, declared in Config
     * @var array|null
     * @since 0.0.1
     */
    private $menus;

    /**
     * Sidebars collection, declared in Config
     * @var array|null
     * @since 0.0.1
     */
    private $sidebars;

    /**
     * Stylesheets collection, declared in Config
     * @var array|null
     * @since 0.0.1
     */
    private $stylesheets;

    /**
     * Scripts collection, declared in Config
     * @var array|null
     * @since 0.0.1
     */
    private $scripts;

    /**
     * Arguments globally passed to Template engine
     * @var array
     * @since 0.0.1
     */
    private $globalArguments = array();

    /**
     * Is template ready for sending to client flag
     * @var bool
     * @since 0.0.1
     */
    private static $isInitialized = false;

    /**
     * \HttpResponse object
     * @var null
     * @since 0.0.1
     */
    private $response = null;

    /**
    * Object constructor
     * @since 0.0.1
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Manual initialization of all theme components.
     * @return bool
     * @since 0.0.1
     */
    public function init()
    {
        add_editor_style();
        load_theme_textdomain(
            $this->getConfig()->getThemeName(),
            $this->getConfig()->getPathToLang()
        );

        foreach(['Menus','Sidebars','Stylesheets','Scripts'] as $collections) {
            $method = 'get' . $collections;
            if(method_exists(self::instance()->getConfig(), $method)) {
                $collectionName = strtolower($collections);
                $factoryName = str_split($collections, (strlen($collections) - 1));
                $factoryName = __NAMESPACE__ .'\\'. $factoryName[0];
                foreach (self::instance()->getConfig()->{$method}() as $element) {
                    if(class_exists( $factoryName )) {
                        self::instance()->addCollection(
                            $collectionName,
                            $factoryName::create($element['name'], $element['extra'])
                        );
                    }
                }
            }
        }

        try {
            self::instance()->registerMenus();
            self::instance()->registerSidebars();
            add_action('wp_enqueue_scripts', array(__CLASS__,'registerStylesheets'));
        } catch (\Exception $e) {
            Page::log(
                'Cannot initialize Wordpress components. '.'Error message: ' . $e->getMessage(),
                500,
                ['Display', 'init']
            );
            Display::terminate();
        }

        try {
            add_action('wp_enqueue_scripts', array(__CLASS__,'enqueueStylesheet'));
            add_action('wp_enqueue_scripts', array(__CLASS__,'enqueueScripts'));
        } catch (\Exception $e) {
            Page::log(
                'Cannot declare dependencies as closures. Msg: '.$e->getMessage(),
                500,
                ['Display', 'init']
            );
            Display::terminate();
        }

        self::$isInitialized = true;
        return self::$isInitialized;
    }

    /**
     * Add object into array collection
     * @param $target
     * @param $element
     * @return $this
     * @since 0.0.1
     */
    public function addCollection($target, $element)
    {
        $this->{$target}[] = $element;
        return $this;
    }

    /**
     * Get selected object collection from populated previously array
     * @param $target
     * @return mixed
     * @throws \Exception
     * @since 0.0.1
     */
    public function getCollection($target)
    {
        if (property_exists($this, $target)) {
            return $this->{$target};
        } else {
            $errorMsg = 'Trying to get undefined collection `'.$target.'`';
            Page::log($errorMsg, 500, ['Display', 'getCollection']);
            throw new \Exception($errorMsg);
        }
    }

    /**
     * Register into wordpress each of Menus collection
     * @return void;
     * @since 0.0.1
    */
    private function registerMenus()
    {
        if ($this->getCollection('menus') != null) {
            foreach ($this->getCollection('menus') as $menu) {
                $single = $menu->getData();

                register_nav_menu($single['name'], $single['description']);

            }
        }
    }

    /**
     * Register into wordpress each of Sidebar collection
     * @return void;
     * @since 0.0.1
     */
    private function registerSidebars()
    {
        if ($this->getCollection('sidebars') != null) {
            foreach ($this->getCollection('sidebars') as $sidebar) {
                register_sidebar($sidebar->getData());
            }
        }
    }

    /**
     * Register into wordpress each of Stylesheet collection
     * @return void;
     * @since 0.0.1
     */
    public static function enqueueStylesheet()
    {
        if (self::instance()->getCollection('stylesheets') != null) {
            foreach (self::instance()->getCollection('stylesheets') as $stylesheet) {

                $css = $stylesheet->getData();
                wp_enqueue_style(
                    $css['name'],
                    self::instance()->getPath('Css') . $css['filename'],
                    array(),
                    $css['version'],
                    'screen'
                );
            }
        }
    }

    /**
     * Enqueue previously registeres script
     * @return void;
     * @since 0.0.1
     */
    public static function enqueueScripts()
    {
        if (self::instance()->getCollection('scripts') != null) {
            foreach (self::instance()->getCollection('scripts') as $script) {
                $js = $script->getData();
                wp_enqueue_script(
                    $js['name'],
                    self::instance()->getPath('Js') . $js['filename'],
                    $js['dependencies'],
                    $js['version'],
                    $js['inFooter']
                );
            }
        }
    }

    /**
     * Enqueue previously registeres stylesheets
     * @return void;
     * @since 0.0.1
     */
    public static function registerStylesheets()
    {
        if (self::instance()->getCollection('stylesheets') != null) {
            foreach (self::instance()->getCollection('stylesheets') as $stylesheet) {
                $css = $stylesheet->getData();
                wp_register_style(
                    $css['name'],
                    self::instance()->getPath('Css') . $css['filename'],
                    array(),
                    $css['version'],
                    'screen'
                );
            }
        }
    }

    /**
     * @param string $templatePage
     * @param array $args
     * @return bool|string
     * @throws \Exception
     * @since 0.0.1
     */
    public function render($templatePage = '', $args = array())
    {
        try {
            $this->response = new Response();

            $finalArgs = array_merge($this->getGlobalArguments(), $args);
            $renderedData = $this->getTemplateEngine()->render($templatePage, $finalArgs);

            $this->response
                ->sendHeaders(200)
                ->setContent($renderedData);
        } catch (\Twig_Error_Syntax $twigError) {
            $this->log(
                'TWIG error during rendering '.$twigError->getTemplateFile().'. Error: '.$twigError->getMessage()
            );
            $this->terminate();
        } catch (\HttpResponseException $responseException) {
            $this->log('Error during sending HttpResponse, message: '.$responseException->getMessage());
            echo($renderedData);
        } catch (\Exception $e) {
            $this->log('Critical error during rendering template: '.$e->getMessage());
            $this->response = new RedirectResponse(home_url());
        }

        return $renderedData;
    }

    
    public function send()
    {
        $this->response->send();
    }

    /*
     * @return mixed
     */
    public function getGlobalArguments()
    {
        return $this->globalArguments;
    }

    /**
     * @param mixed $globalArguments
     */
    public function setGlobalArguments(array $globalArguments)
    {
        $this->globalArguments = $globalArguments;
    }

    /**
     * @param $name
     * @param $val
     */
    public function addGlobalArguments($name, $val)
    {
        $this->globalArguments[$name] = $val;
    }

    //backward compatibility
    /**
     * @param string $to
     * @return string
     */
    public function getPath($to = '')
    {
        $goto = 'getPathTo'.$to;
        try {
            return get_template_directory_uri() . $this->getConfig()->{$goto}();
        } catch (\Exception $e) {
            self::$logger->addAlert('Failed execution of getPath - unknown method `' . $to . '`');
        }
        return get_template_directory_uri() . $this->getConfig()->getPath();
    }

    /**
     * @return bool
     */
    public function getOption()
    {
        return false;
    }

    public static function terminate()
    {
        //echo(self::$logger->toMonologLevel(100));
        //$this->log('Soft - terminated.', 900);
        exit();
    }

    public static function instance()
    {
        if (empty(self::$self)) {
            self::$self = new self;
        }
        return self::$self;
    }
}
