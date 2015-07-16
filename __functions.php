<?php

//composer autoload
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'vendor/autoload.php';
//core
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'class/LBanas/Components/Page.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'class/LBanas/Components/WhileIterator.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'class/LBanas/Components/Factory.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'class/LBanas/Components/HttpResponse.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'class/LBanas/Components/TemplateEngine.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'class/LBanas/Components/Twig.php';
//additional
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'class/LBanas/Components/Config.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'class/LBanas/Components/Menu.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'class/LBanas/Components/Sidebar.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'class/LBanas/Components/Stylesheet.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'class/LBanas/Components/Script.php';
//required
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'class/LBanas/Components/Display.php';

use \LBanas\Components as Theme;

try {
    $display = new Theme\Display();
    $display->init();

    $display->addGlobalArguments(
        'mainMenu',
        wp_nav_menu(array(
            'theme_location' => 'main_menu_1',
            'menu_class' => 'nav-menu',
            'echo'=>false
        ))
    );

    $display->addGlobalArguments('Wordpress', $display);
    $display->addGlobalArguments('whilePosts', new Theme\WhileIterator(
        create_function('', "return have_posts();"),
        create_function('', "the_post(); return true;")
    ));
} catch (\Exception $e) {
    echo( $e->getMessage() );
    Theme\Display::terminate();
}
//Theme\Display::$logger->info('y');