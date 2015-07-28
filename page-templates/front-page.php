<?php

/**
 * Template Name: Strona Golwna
 */

$lastPosts = get_posts();

$display->render('front-page.html.twig', array(
    'lastPosts' => $lastPosts
));
$display->send();
