<?php

namespace LBanas\Components;

use Twig_Autoloader;

/**
 * Class Twig, used for initialization of template engine
 * @category Theme
 * @package LBanasComponents
 * @author Lukasz Banas <lukaszbanas4@gmail.com>
 * @license MIT <http://opensource.org/licenses/MIT>
 * @link https://github.com/lukaszbanas/Theme
 * @since 0.0.1
 */
class Twig implements TemplateEngine
{
    /**
     * @var null|\Twig_Loader_Filesystem
     */
    private $loader = null;

    /**
     * @var null|\Twig_Environment
     */
    private $env = null;

    /**
     * @param $templateDir
     * @param $options
     * @return Twig
     */
    public static function create($templateDir, $options)
    {
        return new self($templateDir, $options);
    }

    /**
     * @param $templateDir
     * @param $options
     */
    public function __construct($templateDir, $options)
    {
        ini_set('unserialize_callback_func', 'spl_autoload_call');
        \Twig_Autoloader::register();
        $this->loader = new \Twig_Loader_Filesystem($templateDir);
        $this->env = new \Twig_Environment($this->loader, $options);

        try {
            Page::log('initialization twig. TemplateDir: '.$templateDir, 100, ['Twig','__construct']);
        } catch (\Exception $e) {
            return;
        }
    }

    /**
     * @param $name
     * @param $args
     * @return bool|string
     * @throws \Exception
     */
    public function render($name, $args)
    {
        if (empty($this->env)) {
            throw new \Exception('Twig engine not initialized but trying display instead');
        }

        Page::log('Rendering template `'.$name.'`', 100, ['Twig','render']);

        return $this->env->render($name, $args);
    }
}
