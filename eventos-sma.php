<?php
/*
  Plugin Name: Eventos SMA
  Plugin URI: https://github.com/jcarvalho89/EventosSMA
  Description: Plugin para eventos
  Version: 1.0.0
  Author: Josiano Carvalho
  Author URI:
  License: GPLv2
 */

if (!defined('ABSPATH')) {
    exit;
}

class SmaEventos
{

    public function __construct()
    {
        // Constantes
        define('SMA_PLUGIN', __FILE__);
        define('SMA_VERSION', '1.0.0');
        define('SMA_SLUG', plugin_basename(SMA_PLUGIN));
        define('SMA_PLUGIN_DIR', untrailingslashit(plugin_dir_path(SMA_PLUGIN)));
        define('SMA_PLUGIN_URL', untrailingslashit(plugins_url(basename(plugin_dir_path(SMA_PLUGIN)), basename(SMA_PLUGIN))));

        //Ações
        add_action('admin_menu', array($this, 'sma_setup_menu'), 10);
        add_action('add_meta_boxes', array($this, 'campos_eventos_meta_box'));
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
        add_action('init', array($this, 'cria_post_eventos'));
        add_action('save_post', array($this, 'save_sma_campos_meta'));

        $this->sma_includes();
    }

    /**
     * Scripts e css
     * @param type $hook
     * @global type $post
     */
    public function admin_enqueue_scripts($hook)
    {
        global $post;
        if (isset($post->post_type) && $post->post_type == 'eventos') {
            //scripts js
            wp_enqueue_script('admin-datepicker-js', SMA_PLUGIN_URL . '/assets/js/bootstrap-datetimepicker.min.js', array(), rand(0, 999));
            wp_enqueue_script('locale-js', SMA_PLUGIN_URL . '/assets/js/locales/bootstrap-datetimepicker.pt-BR.js', array(), rand(0, 999));
            wp_enqueue_script('init-js', SMA_PLUGIN_URL . '/assets/js/init.js', array(), rand(0, 999));

            //styles
            wp_enqueue_style('admin-datepicker', SMA_PLUGIN_URL . '/assets/css/bootstrap-datetimepicker.min.css', array(), rand(0, 999));
            wp_enqueue_style('admin-datepickercss-css', SMA_PLUGIN_URL . '/assets/css/datetimepicker.css', array(), rand(0, 999));
        }
    }

    private function sma_includes()
    {
        //Shortcode
        require_once SMA_PLUGIN_DIR . '/includes/shortcode.php';
    }

    public function sma_setup_menu()
    {
        add_submenu_page('options-general.php', 'Configurações de Eventos SMA', 'Eventos SMA', 'manage_options', 'configuracoes', array($this, 'sma_init'));
        add_action('admin_init', array($this, 'sma_register_fields'));
    }

    public function sma_register_fields()
    {
        register_setting('sma_register_fields_group', 'sma_quant_eventos_por_tela');
    }

    public function sma_init()
    {
        if (is_admin()) {
            require_once SMA_PLUGIN_DIR . '/configuracoes.php';
        }
    }

    /**
     * local para exibir os campos customizados de eventos dentro do admin 
     * para os tipos de post eventos
     */
    public function campos_eventos_meta_box()
    {
        add_meta_box('fields_eventos', 'Detalhes do Evento', array($this, 'sma_campos_meta_box'), 'eventos', 'normal', 'high');
    }

    /**
     * cria os campos data/hora e local do evento para exibir dentro meta box
     * @global type $post
     */
    public function sma_campos_meta_box()
    {
        global $post;
        $meta = get_post_meta($post->ID, 'sma_fields', true);
        ?>
        <input type="hidden" name="sma_fields_box_nonce" value="<?php echo wp_create_nonce(basename(__FILE__)); ?>">

        <p>
            <label for="sma_fields[sma_data_hora]">Data e Hora Evento</label>
            <br>
            <input  type="text" name="sma_fields[sma_data_hora]" class="regular-text datetimepicker" value="<?php echo isset($meta['sma_data_hora']) ? $meta['sma_data_hora'] : ''; ?>">
        </p>
        <p>
            <label for="sma_fields[sma_local]">Local</label>
            <br>
            <input  type="text" name="sma_fields[sma_local]" class="regular-text " value="<?php echo isset($meta['sma_local']) ? $meta['sma_local'] : ''; ?>">
        </p>
        <?php
    }

    function save_sma_campos_meta($post_id)
    {
        if (isset($_POST['sma_fields_box_nonce'])) {
            if (!wp_verify_nonce($_POST['sma_fields_box_nonce'], basename(__FILE__))) {
                return $post_id;
            }
            if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
                return $post_id;
            }

            if ('page' === $_POST['post_type']) {
                if (!current_user_can('edit_page', $post_id)) {
                    return $post_id;
                } elseif (!current_user_can('edit_post', $post_id)) {
                    return $post_id;
                }
            }

            $old = get_post_meta($post_id, 'sma_fields', true);
            $new = $_POST['sma_fields'];
            $sma_data_hora = $_POST['sma_fields']['sma_data_hora'];
            $sma_hora_evento = new DateTime($_POST['sma_fields']['sma_data_hora']);

            if ($new && $new !== $old) {
                update_post_meta($post_id, 'sma_fields', $new);
                update_post_meta($post_id, 'data_hora_evento', $sma_data_hora);
                update_post_meta($post_id, 'hora_evento', $sma_hora_evento->format('H:i'));
            } elseif ('' === $new && $old) {
                delete_post_meta($post_id, 'sma_fields', $old);
            }
        }
    }

    /**
     * Cria um custom post de eventos
     */
    public function cria_post_eventos()
    {
        register_post_type('eventos', array(
            'labels' => array(
                'name' => __('Eventos'),
                'singular_name' => __('Evento'),
                'add_new' => __('Novo Evento'),
                'edit_item' => __('Editar Evento'),
                'view_item' => __('Ver Evento'),
            ),
            'public' => true,
            'hierarchical' => true,
            'has_archive' => true,
            'supports' => array(
                'title',
                'editor',
                'excerpt',
                'thumbnail',
            ),
            'taxonomies' => array(
                'post_tag',
                'category',
            )
                )
        );
        register_taxonomy_for_object_type('category', 'eventos');
        register_taxonomy_for_object_type('post_tag', 'eventos');
    }

}
//cria o plugin
$GLOBALS['sma_eventos_futuros'] = new SmaEventos();
