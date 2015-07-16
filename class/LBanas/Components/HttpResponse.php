<?php

namespace LBanas\Components;

/**
 * Interface HttpResponse. Main idea for this interface is to categorize responses by this object (json, html, text)
 * @category Theme
 * @package LBanasComponents
 * @author Lukasz Banas <lukaszbanas4@gmail.com>
 * @license MIT <http://opensource.org/licenses/MIT>
 * @link link.to.git
 * @since 0.0.1
 */
interface HttpResponse
{
    /**
     * @param $page
     * @param $args
     * @return mixed
     */
    public function render($page, $args);

    /**
     * @return mixed
     */
    public function send();
}
