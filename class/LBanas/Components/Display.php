<?php

namespace LBanas\Components;

use Monolog\Logger;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class Display, should be final and result should be send to client.
 * @category Theme
 * @package LBanasComponents
 * @author Lukasz Banas <lukaszbanas4@gmail.com>
 * @license MIT <http://opensource.org/licenses/MIT>
 * @link link.to.git
 * @since 0.0.1
 */
class Display extends Page implements \LBanas\Components\HttpResponse
{
    /**
     * Menus collection
     * @var array|null
     */
    private $menus;

    /**
     * Sidebars collection
     * @var array|null
     */
    private $sidebars;

    /**
     * Stylesheets collection
     * @var array|null
     */
    private $stylesheets;

    /**
     * Scripts collection
     * @var array|null
     */
    private $scripts;

    /**
     * Arguments globally passed to Template engine
     * @var array
     */
    private $globalArguments = array();

    /**
     * Is template ready for sending to client flag
     * @var bool
     */
    private static $isInitialized = false;

    /**
     * \HttpResponse object
     * @var null
     */
    private $response = null;

    /**
    * Object constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Manual initialization of all theme components
     * @return void
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
            if(method_exists($this->getConfig(), $method)) {
                $collectionName = strtolower($collections);
                $factoryName = str_split($collections, (strlen($collections) - 1));
                $factoryName = __NAMESPACE__ .'\\'. $factoryName[0];
                foreach ($this->getConfig()->{$method}() as $element) {
                    if(class_exists( $factoryName )) {
                        $this->addCollection(
                            $collectionName,
                            $factoryName::create($element['name'], $element['extra'])
                        );
                    }
                }
            }
        }

        try {
            $this->registerMenus();
            $this->registerSidebars();
            add_action('wp_enqueue_scripts', array($this,'registerStylesheets'));
        } catch (\Exception $e) {
            $this->log('cannot initialize Wordpress components. '.'Error message: ' . $e->getMessage());
            Display::terminate();
        }

        //@TODO logical error -in register sript / style function
        // create registration, in enqueue (below) add enqueue
        try {
            add_action('wp_enqueue_scripts', array($this,'enqueueStylesheet'));
            add_action('wp_enqueue_scripts', array($this,'enqueueScripts'));
        } catch (\Exception $e) {
            $this->log('cannot declare dependencies as closures. Msg: '.$e->getMessage());
            Display::terminate();
        }

        self::$isInitialized = true;
    }

    /**
     * Add object into array collection
     * @param $target
     * @param $element
     * @return $this
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
     */
    public function getCollection($target)
    {
        if (property_exists($this, $target)) {
            return $this->{$target};
        } else {
            $errorMsg = 'Trying to get undefined collection `'.$target.'`';
            self::$logger->addCritical($errorMsg);
            throw new \Exception($errorMsg);
        }
    }

    /**
     * Register into wordpress each of Menus collection
     * @return void;
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
     */
    private function enqueueStylesheet()
    {
        if ($this->getCollection('stylesheets') != null) {
            foreach ($this->getCollection('stylesheets') as $stylesheet) {
                $css = $stylesheet->getData();
                wp_enqueue_style(
                    $css['name'],
                    $this->getPath('Css') . $css['filename'],
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
     */
    private function enqueueScripts()
    {
        if ($this->getCollection('scripts') != null) {
            foreach ($this->getCollection('scripts') as $script) {
                $js = $script->getData();
                wp_enqueue_script(
                    $js['name'],
                    $this->getPath('Js') . $js['filename'],
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
     */
    private function registerStylesheets()
    {
        if ($this->getCollection('stylesheets') != null) {
            foreach ($this->getCollection('stylesheets') as $stylesheet) {
                $css = $stylesheet->getData();
                wp_register_style(
                    $css['name'],
                    $this->getPath('Css') . $css['filename'],
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
     */
    public function render($templatePage = '', $args = array())
    {
        try {
            $finalArgs = array_merge($this->getGlobalArguments(), $args);
            $renderedData = $this->getTemplateEngine()->render($templatePage, $finalArgs);
            $this->response = new Response();
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
}
