<?php

namespace LBanas\Components;

/**
 * Class Sidebar, used for serialization of Wordpress sidebar array
 * @category Theme
 * @package LBanasComponents
 * @author Lukasz Banas <lukaszbanas4@gmail.com>
 * @license MIT <http://opensource.org/licenses/MIT>
 * @link link.to.git
 * @since 0.0.1
 */
class Sidebar extends \LBanas\Components\Factory
{
    /**
     * @param $name
     * @param array $extra
     */
    public function __construct($name, array $extra)
    {
        $this->data['name'] = $name;

        foreach ($extra as $key => $val) {
            $this->data[ $key ] = $val;
        }
    }
}
