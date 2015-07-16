<?php

namespace LBanas\Components;

/**
 * Class Script, used for serialization of Wordpress script array
 * @category Theme
 * @package LBanasComponents
 * @author Lukasz Banas <lukaszbanas4@gmail.com>
 * @license MIT <http://opensource.org/licenses/MIT>
 * @link link.to.git
 * @since 0.0.1
 */
class Script extends \LBanas\Components\Factory
{
    /**
     * @param $name
     * @param $extra
     */
    public function __construct($name, array $extra)
    {
        $this->data['name'] = $name;

        foreach ($extra as $name => $val) {
            $this->data[ $name ] = $val;
        }
    }
}
