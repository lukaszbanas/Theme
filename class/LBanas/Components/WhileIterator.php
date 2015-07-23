<?php

namespace LBanas\Components;

/**
 * Class WhileIterator, enable usage of Wordpress `while` loop
 * @category Theme
 * @package LBanasComponents
 * @author Lukasz Banas <lukaszbanas4@gmail.com>
 * @license MIT <http://opensource.org/licenses/MIT>
 * @link https://github.com/lukaszbanas/Theme
 * @since 0.0.1
 */
class WhileIterator implements \Iterator
{
    /**
     * @param $valid
     * @param $current
     */
    public function __construct($valid, $current)
    {
        $this->validClosure = $valid;
        $this->currentClosure = $current;
    }

    /**
     * @return mixed
     */
    public function valid()
    {
        return call_user_func($this->validClosure);
    }

    /**
     * @return mixed
     */
    public function current()
    {
        return call_user_func($this->currentClosure);
    }

    public function next()
    {

    }

    public function rewind()
    {

    }

    public function key()
    {

    }
}