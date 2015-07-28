<?php

$attachments = array_values(
    get_children(array(
        'post_parent' => WP_POST::get_instance(get_the_id())->post_parent,
        'post_status' => 'inherit',
        'post_type' => 'attachment',
        'post_mime_type' => 'image',
        'order' => 'ASC',
        'orderby' => 'menu_order ID'
    ))
);

foreach ($attachments as $k => $attachment) {
    if ($attachment->ID == get_the_ID()) {
        break;
    }
}

if (count($attachments) > 1) {
    $next_attachment_url = get_attachment_link($attachments[ 0 ]->ID);
} else {
    $next_attachment_url = wp_get_attachment_url();
}


$display->render('image.html.twig', array(
    'next_attachment_url' => $next_attachment_url
));
$display->send();
