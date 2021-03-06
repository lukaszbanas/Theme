<?php

namespace LBanas\Components;

/**
 * Abstract Class Page, containing all core functions
 * @category Theme
 * @package LBanasComponents
 * @author Lukasz Banas <lukaszbanas4@gmail.com>
 * @license MIT <http://opensource.org/licenses/MIT>
 * @link https://github.com/lukaszbanas/Theme
 * @since 0.0.1
 */
abstract class Page
{
    /**
     * @var \Monolog\Logger
     */
    public static $logger;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Twig|null
     */
    private $templateEngine = null;

    public static function getLogger()
    {
        if (empty(self::$logger)) {
            if (!class_exists('\Monolog\Logger')) {
                throw new \Exception('Monolog`s Logger class doesn`t exists, exiting');
            }

            try {
                $logDirectory = get_template_directory() . DIRECTORY_SEPARATOR . 'logs'. DIRECTORY_SEPARATOR;
                self::$logger = new \Monolog\Logger('system');
                self::$logger->pushHandler(
                    new \Monolog\Handler\StreamHandler($logDirectory . 'system.log', \Monolog\Logger::ERROR)
                );
            } catch (\Exception $e) {
                exit('Critical error during initialization of Logger class');
            }
        }

        return self::$logger;
    }

    /**
     * @throws \Exception
     */
    public function __construct()
    {
        if (!function_exists('get_template_directory')) {
            throw new \Exception('Wordpress not present, exiting');
        }

        if (!class_exists('\LBanas\Components\Config')) {
            throw new \Exception('Configuration class doesn`t exists, exiting');
        }

        try {
            //logger init
            $logDirectory = get_template_directory() . DIRECTORY_SEPARATOR . 'logs'. DIRECTORY_SEPARATOR;
            self::$logger = new \Monolog\Logger('system');
            self::$logger->pushHandler(
                new \Monolog\Handler\StreamHandler($logDirectory . 'system.log', \Monolog\Logger::ERROR)
            );

            //config init
            $this->config = new Config();

            if ($this->getConfig()->isDebug()) {
                self::$logger->pushHandler(
                    new \Monolog\Handler\StreamHandler($logDirectory . 'dev.log', 100)
                );
            }

            self::log('Successfully created new \Display instance', 100, ['Page','__construct']);

            //TWIG init
            //@TODO add environment capability
            $cacheDir = false;
            if ($this->getConfig()->isTwigCache()) {
                $cacheDir = $this->getConfig()->getPathToTmp() . $this->getConfig()->getTwigCacheDir();
                self::log('Setting Twig cache as ENABLED', 100, ['Page','__construct']);
            } else {
                self::log('Setting Twig cache as DISABLED', 100, ['Page','__construct']);
            }

            $this->templateEngine = \LBanas\Components\Twig::create(
                get_template_directory() . $this->getConfig()->getPathToTmp() . $this->getConfig()->getTemplateDev(),
                array(
                    'cache' => $cacheDir,
                    'debug' => $this->getConfig()->isDebug()
                )
            );

        } catch (\Twig_Error $e) {
            exit('Critical error during initialization of Twig class. Details: '.$e->getMessage());
        } catch (\Exception $e) {
            exit('Critical error during initialization of Display class');
        }

        //Class sucessfully initialized, begin wordpress integration
        if ($this->getConfig()->isDisableRss()) {
            $this->disableRss();
            self::log('Setting RSS channels as DISABLED', 100, ['Page','__construct']);
        } else {
            self::log('Setting RSS channels as ENABLED', 100, ['Page','__construct']);
        }

        //Polylang support
        if (defined('POLYLANG_VERSION')) {
            try {
                $pathToLang = get_template_directory(). DIRECTORY_SEPARATOR . $this->getConfig()->getPathToLang();
                if (file_exists($pathToLang)) {
                    include_once $pathToLang . DIRECTORY_SEPARATOR . 'polylang.php';
                    $this->log('Polylang string file sucessfully loaded.', 100, ['Page','__construct']);
                }
            } catch (\Exception $e) {
                self::log('Failed to load polylang string file, error: ' . $e->getMessage(), 500, ['Page','__construct']);
            }
        }
    }

    /**
     * @param $function
     * @param $arguments
     * @return mixed|null
     */
    public function __call($function, $arguments)
    {
        if (!function_exists($function)) {
            self::log('Call to undefined function `'.$function.'` through __call magic method.', 500, ['Page', '__call']);
            return null;
        }
        return call_user_func_array($function, $arguments);
    }

    /**
     * @return Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @return Twig|null
     */
    public function getTemplateEngine()
    {
        return $this->templateEngine;
    }

    public function disableRss()
    {
        $channels = array(
            'do_feed',
            'do_feed_rdf',
            'do_feed_rss',
            'do_feed_rss2',
            'do_feed_atom',
            'do_feed_rss2_comments',
            'do_feed_atom_comments',
        );
        foreach ($channels as $channel) {
            add_action($channel, array($this,'disableFeed'), 1);
        }
    }

    private function disableFeed()
    {
        try {
            $msg = 'Feeds are currently disabled, please visit <a href="' . get_bloginfo('url') . '">our website</a>!';
            wp_die(__($msg));
        } catch (\Exception $e) {
            exit('Feeds are currently disabled');
        }
    }

    public static function log( $msg, $lvl=500, $context = array('prod'))
    {
        self::getLogger()->log($lvl, $msg, $context);
    }

}
