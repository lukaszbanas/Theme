<?php
var_dump( get_registered_nav_menus() );
$display->render('front-page.html.twig', array());
$display->send();
