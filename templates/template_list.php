<?php
// Exit if accessed directly
if (!defined('ABSPATH'))
    exit;

/**
 * Busca post do tipo "eventos" ordenados por data e hora. 
 * Lista Eventos futuro na página. 
 * Quantidade de eventos para exibição é configurada na página do plguin no admin do WordPress
 * Quantidade Padrão é 10
 */
date_default_timezone_set('America/Sao_Paulo');

$total_eventos = get_option('sma_quant_eventos_por_tela');
$hoje = new DateTime(); //data e hora atual

$args = array(
    'post_type' => 'eventos',
    'posts_per_page' => is_numeric($total_eventos) ? $total_eventos : '10',
    'meta_query' => array(
        'relation' => 'AND',
        'data_evento_field' => array(
            'key' => 'data_hora_evento',
            'compare' => '>=',
            'value' => $hoje->format('Y-m-d H:i'),
            'type' => 'DATE'
        ),
        'hora_evento_field' => array(
            'key' => 'hora_evento',
            'compare' => 'EXIST',
        )
    ),
    'orderby' => array(
        'data_evento_field' => 'ASC',
        'hora_evento_field' => 'ASC',
    ),
);

$query = new WP_Query($args);
?>


<!--estilo básico-->
<style>
    table{
        border: 1px solid #000;
    }

    thead tr{
        background: #ccc;
    }
    thead tr td {
        border-color: #000;
    }
    tr td {
        text-align: center;
    }

</style>

<!--lista eventos-->

<h2>
    Próximos eventos
</h2>
<table class="lista-eventos">
    <thead>
        <tr>
            <td>#</td>
            <td>Nome</td>
            <td>Data</td>
            <td>Horário</td>
            <td>Local</td>
        </tr>
    </thead>
    <tbody>
        <?php
        $i = 0;
        if ($query->have_posts()) {
            while ($query->have_posts()) : $query->the_post();
                $data_evento = get_post_meta(get_the_ID(), 'data_hora_evento', true);
                $data_hora_evento = new DateTime($data_evento);
                $sma_fields = get_post_meta(get_the_ID(), 'sma_fields');
                $i++;
                ?>
                <tr id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <td><?php echo $i; ?></td>
                    <td><?php the_title() ?></td>
                    <td><?php echo $data_hora_evento->format('d/m/Y') ?></td>
                    <td><?php echo $data_hora_evento->format('H:i') ?></td>
                    <td><?php echo $sma_fields[0]['sma_local'] ?></td>
                </tr>
                <?php
            endwhile;
        }else {
            ?>
            <tr>
                <td colspan="5">
                    Nenhum evento disponível no momento!'
                </td>
            </tr>
            <?php
        }

        wp_reset_postdata();
        ?>
    </tbody>
</table>

<?php ?>


