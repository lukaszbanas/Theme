<?php

namespace LBanas\Components;

/**
 * Abstract Class Factory, implements serializable interface to core Wordpress objects.
 * @category Theme
 * @package LBanasComponents
 * @author Lukasz Banas <lukaszbanas4@gmail.com>
 * @license MIT <http://opensource.org/licenses/MIT>
 * @link link.to.git
 * @since 0.0.1
 */
abstract class Factory implements \Serializable
{
    /**
     * Data container for Wordpress objects. Usually it`s an array.
     * @var array
     */
    public $data = array();

    /**
     * @param $name
     * @param $extra
     * @return static
     */
    public static function create($name, $extra)
    {
        return new static($name, $extra);
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return mixed|string|void
     */
    public function serialize()
    {
        return json_encode($this->data);
    }

    /**
     * @param string $data
     * @return $this
     */
    public function unserialize($data)
    {
        $this->data = json_decode($data);
        return $this;
    }
}
