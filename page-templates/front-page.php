<?php
/**
 * Template Name: Home site
 *
 */

$lastPosts = get_posts();

$display->render('front-page.html.twig', array(
    'lastPosts' => $lastPosts
));
$display->send();
