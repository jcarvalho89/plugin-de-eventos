<?php

// Exit if accessed directly
if (!defined('ABSPATH'))
    exit;

/*
 * Cria shortcode para usar nas páginas ou posts
 */

function sma_eventos_shortcode($atts, $content = null)
{
    ob_start();

    //define o template
    $template_name = 'template_list.php';
    $default_path = SMA_PLUGIN_DIR . '/templates/';

    include $default_path . $template_name;

    return ob_get_clean();
}

add_shortcode('sma_mostrar_eventos', 'sma_eventos_shortcode');
