<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();

$action_buttons = array();
$action_buttons[] = array(
    'label' => $CI->lang->line("ACTION_BACK"),
    'href' => site_url($CI->controller_url)
);

if ($file_type == $CI->config->item('system_file_type_image'))
{
    $action_buttons[] = array(
        'label' => 'Upload Picture',
        'href' => site_url($CI->controller_url . '/index/add_image/' . $id)
    );
    $action_buttons[] = array(
        'label' => $CI->lang->line("ACTION_REFRESH"),
        'href' => site_url($CI->controller_url . '/index/list_image/' . $id)
    );
}
else
{
    $action_buttons[] = array(
        'label' => 'Upload Video',
        'href' => site_url($CI->controller_url . '/index/add_video/' . $id)
    );
    $action_buttons[] = array(
        'label' => $CI->lang->line("ACTION_REFRESH"),
        'href' => site_url($CI->controller_url . '/index/list_video/' . $id)
    );
}

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
                        <th style="width:30%"><?php echo $file_type ?></th>
                        <th><?php echo $CI->lang->line('LABEL_REMARKS'); ?></th>
                        <th style="width:15%">Action</th>
                    </tr>
                    <?php
                    if ($uploaded_files)
                    {
                        foreach ($uploaded_files as $file)
                        {
                            ?>
                            <tr>
                                <td><?php echo $file['id']; ?></td>
                                <td>
                                    <a href="<?php echo $CI->config->item('system_base_url_picture') . $file['file_location']; ?>" target="_blank" class="external blob">
                                        <img src="<?php echo $CI->config->item('system_base_url_picture') . $file['file_location']; ?>" style="max-height:150px" alt="Picture Missing"/>
                                    </a>
                                </td>
                                <td><?php echo nl2br($file['remarks']); ?></td>
                                <td>
                                    <?php if ($file_type == $CI->config->item('system_file_type_image'))
                                    {
                                        ?>
                                        <a href="<?php echo site_url($CI->controller_url . '/index/edit_image/' . $file['id']); ?>" class="btn btn-md btn-primary">Edit</a>
                                    <?php
                                    }
                                    else
                                    {
                                        ?>
                                        <a href="<?php echo site_url($CI->controller_url . '/index/edit_video/' . $file['id']); ?>" class="btn btn-md btn-primary">Edit</a>
                                    <?php
                                    }
                                    ?>
                                    <a href="<?php echo site_url($CI->controller_url . '/index/delete_file/' . $file['id']); ?>" class="btn btn-md btn-danger" data-message-confirm="Are You Sure?">Delete</a>
                                </td>
                            </tr>
                        <?php
                        }
                    }
                    else
                    {
                        ?>
                        <tr>
                            <th colspan="4" style="text-align:center; font-style:italic; font-size:1.3em">- No <?php echo $file_type ?> Found -</th>
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
