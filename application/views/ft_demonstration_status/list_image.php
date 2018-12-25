<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();

$action_buttons = array();
$action_buttons[] = array(
    'label' => $CI->lang->line("ACTION_BACK"),
    'href' => site_url($CI->controller_url . '/index/list/')
);
if ((isset($CI->permissions['action1']) && ($CI->permissions['action1'] == 1)) || (isset($CI->permissions['action2']) && ($CI->permissions['action2'] == 1)))
{
    $action_buttons[] = array(
        'label' => 'Upload Picture',
        'href' => site_url($CI->controller_url . '/index/add_image/' . $id)
    );
}
if (isset($CI->permissions['action4']) && ($CI->permissions['action4'] == 1))
{
    $action_buttons[] = array(
        'type' => 'button',
        'label' => $CI->lang->line("ACTION_PRINT"),
        'class' => 'button_action_download',
        'data-title' => "Print",
        'data-print' => true
    );
}
if (isset($CI->permissions['action5']) && ($CI->permissions['action5'] == 1))
{
    $action_buttons[] = array(
        'type' => 'button',
        'label' => $CI->lang->line("ACTION_DOWNLOAD"),
        'class' => 'button_action_download',
        'data-title' => "Download"
    );
}
$action_buttons[] = array(
    'label' => $CI->lang->line("ACTION_REFRESH"),
    'href' => site_url($CI->controller_url . '/index/list_image')
);

$CI->load->view('action_buttons', array('action_buttons' => $action_buttons));

?>
<style>
    .blob {
        display: inline-block;
        padding: 3px;
        border: 3px solid #8c8c8c
    }

    .blob:hover {
        border: 3px solid #3693CF
    }
</style>
<div class="row widget">
    <div class="widget-header">
        <div class="title">
            <?php echo $title; ?>
        </div>
        <div class="clearfix"></div>
    </div>

    <?php echo $CI->load->view("info_basic", '', true); ?>

    <div class="row show-grid">
        <div class="col-xs-12">
            <div style="overflow-x:scroll">
                <table class="table table-bordered">
                    <tr>
                        <th style="width:5%">ID</th>
                        <th style="width:40%">Image</th>
                        <th><?php echo $CI->lang->line('LABEL_REMARKS'); ?></th>
                    </tr>
                    <?php
                    if ($uploaded_images)
                    {
                        foreach ($uploaded_images as $image)
                        {
                            ?>
                            <tr>
                                <th><?php echo $image['id']; ?></th>
                                <th>
                                    <a href="<?php echo $CI->config->item('system_base_url_picture') . $image['file_location']; ?>" target="_blank" class="external blob">
                                        <img src="<?php echo $CI->config->item('system_base_url_picture') . $image['file_location']; ?>" style="max-height:200px" alt="Picture Missing"/>
                                    </a>
                                </th>
                                <th><?php echo nl2br($image['remarks']); ?></th>
                            </tr>
                        <?php
                        }
                    }
                    else
                    {
                        ?>
                        <tr>
                            <th colspan="3" style="text-align:center;font-style:italic; font-size:1.3em">- No Image Found -</th>
                        </tr>
                    <?php
                    }
                    ?>
                </table>
            </div>
        </div>
    </div>

</div>
<div class="clearfix"></div>

<script type="text/javascript">
    $(document).ready(function () {
        system_off_events(); // Triggers

    });
</script>
