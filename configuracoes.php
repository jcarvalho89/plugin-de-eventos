<?php
// Exit if accessed directly
if (!defined('ABSPATH'))
    exit;
?>


<div class="wrap">

    <h2>Shortcode</h2>
    <p>
        Use o shortcode <b>[sma_mostrar_eventos]</b> na página ou post que deseja visualizar a lista de eventos.
    </p>
    <hr>
    <h2>Configure a exibição dos eventos</h2>
    <form method="post" action="<?php echo esc_url(admin_url('options.php')); ?>" enctype="multipart/form-data">
        <?php settings_fields('sma_register_fields_group'); ?>
        <?php do_settings_sections('sma_register_fields_group'); ?>
       	<table class="form-table" width="100%">
            <tr valign="top">
                <th scope="row">Informe a quantidade máxima de eventos a serem mostrados na tela:</th>
                <td><input type="text" name="sma_quant_eventos_por_tela" value="<?php echo get_option('sma_quant_eventos_por_tela'); ?>"/></td>

            </tr>
        </table>
        <?php submit_button(); ?>
    </form>

</div>