<?php

namespace LBanas\Components;

/**
 * trait Singleton
 * @category Theme
 * @package LBanasComponents
 * @author Lukasz Banas <lukaszbanas4@gmail.com>
 * @license MIT <http://opensource.org/licenses/MIT>
 * @link https://github.com/lukaszbanas/Theme
 * @since 0.0.2
 */
trait Singleton
{
    /**
     * @var self
     * @since 0.0.2
     */
    private static $self = null;

    /**
     * late binding constructor
     * @return mixed
     * @since 0.0.2
     */
    public static function instance()
    {
        if (empty(self::$self)) {
            self::$self = new static;
        }
        return self::$self;
    }
}