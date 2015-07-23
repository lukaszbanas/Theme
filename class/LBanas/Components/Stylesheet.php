<?php

namespace LBanas\Components;

/**
 * Class Stylesheet, used for serialization of Wordpress stylesheet array
 * @category Theme
 * @package LBanasComponents
 * @author Lukasz Banas <lukaszbanas4@gmail.com>
 * @license MIT <http://opensource.org/licenses/MIT>
 * @link https://github.com/lukaszbanas/Theme
 * @since 0.0.1
 */
class Stylesheet extends \LBanas\Components\Factory
{
    /**
     * @param $name
     * @param $extra
     */
    public function __construct($name, array $extra)
    {
        $this->data['name'] = $name;

        foreach ($extra as $key => $val) {
            $this->data[ $key ] = $val;
        }
    }
}
