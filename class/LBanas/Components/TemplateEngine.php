<?php

namespace LBanas\Components;

/**
 * interface TemplateEngine. Main idea for this interface is to enable future switch into different template engine
 * @category Theme
 * @package LBanasComponents
 * @author Lukasz Banas <lukaszbanas4@gmail.com>
 * @license MIT <http://opensource.org/licenses/MIT>
 * @link https://github.com/lukaszbanas/Theme
 * @since 0.0.1
 */
interface TemplateEngine
{
    /**
     * @param $templateDir
     * @param $options
     * @return mixed
     */
    public static function create($templateDir, $options);
}
