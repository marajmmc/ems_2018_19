<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();

if(isset($no_details_menu)){
    // Don't show Button(s)
}else{
    $action_buttons = array();
    $action_buttons[] = array
    (
        'label' => $CI->lang->line("ACTION_BACK").' to Pending List',
        'href' => site_url($CI->controller_url.'/index/list')
    );
    $action_buttons[] = array
    (
        'label' => $CI->lang->line("ACTION_BACK").' to All List',
        'href' => site_url($CI->controller_url.'/index/list_all')
    );

    $CI->load->view('action_buttons', array('action_buttons' => $action_buttons));
}
?>

<div class="row widget">
    <div class="widget-header">
        <div class="title">
            <?php echo $title; ?>
        </div>
        <div class="clearfix"></div>
    </div>

    <?php echo $CI->load->view("info_basic", "", true); ?>

    <!-----Image & video Accordion----->
    <?php foreach ($info_image as $file_type => $file_info)
    {
        ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <label class=""><a class="external text-danger" data-toggle="collapse" data-target="#info_<?php echo $file_type; ?>" href="#">+ <?php echo $file_type; ?> Information</a></label>
                </h4>
            </div>
            <div id="info_<?php echo $file_type; ?>" class="panel-collapse collapse in">
                <table class="table table-bordered">
                    <tr>
                        <th rowspan="2" style="vertical-align:bottom"><?php echo $CI->lang->line('LABEL_SL_NO'); ?></th>
                        <th colspan="2" style="text-align:center"><?php echo $CI->lang->line('LABEL_VARIETY1_NAME'); ?></th>
                        <?php if ($item['variety2_id'] > 0){ ?>
                            <th colspan="2" style="text-align:center"><?php echo $CI->lang->line('LABEL_VARIETY2_NAME'); ?></th>
                        <?php } ?>
                    </tr>
                    <tr>
                        <th><?php echo $file_type; ?></th>
                        <th>Remarks</th>
                        <?php if ($item['variety2_id'] > 0){ ?>
                            <th><?php echo $file_type; ?></th>
                            <th>Remarks</th>
                        <?php } ?>
                    </tr>
                    <?php
                    $i = 0;
                    foreach ($file_info as $info)
                    {
                        ?>
                        <tr>
                            <td style="width:2%;text-align:right"><?php echo ++$i; ?></td>
                            <td style="width:24%">
                                <?php if (($file_type == $CI->config->item('system_file_type_video')) && ($info['file_location_variety1'] != NO_VIDEO_PATH)){ ?>
                                    <video controls style="<?php echo IMAGE_VIDEO_DISPLAY_STYLE; ?>">
                                        <source src="<?php echo $CI->config->item('system_base_url_picture') . $info['file_location_variety1']; ?>"/>
                                    </video>
                                <?php } else { ?>
                                    <a href="<?php echo $CI->config->item('system_base_url_picture') . $info['file_location_variety1']; ?>" target="_blank" class="external blob">
                                        <img src="<?php echo $CI->config->item('system_base_url_picture') . $info['file_location_variety1']; ?>" style="<?php echo IMAGE_VIDEO_DISPLAY_STYLE; ?>" alt="Picture Missing"/>
                                    </a>
                                <?php } ?>
                            </td>
                            <td style="width:25%"><?php echo nl2br($info['remarks_variety1']); ?></td>

                            <?php if ($item['variety2_id'] > 0){ ?>

                                <td style="width:24%">
                                    <?php if (($file_type == $CI->config->item('system_file_type_video')) && ($info['file_location_variety2'] != NO_VIDEO_PATH)){ ?>
                                        <video controls style="<?php echo IMAGE_VIDEO_DISPLAY_STYLE; ?>">
                                            <source src="<?php echo $CI->config->item('system_base_url_picture') . $info['file_location_variety2']; ?>"/>
                                        </video>
                                    <?php } else { ?>
                                        <a href="<?php echo $CI->config->item('system_base_url_picture') . $info['file_location_variety2']; ?>" target="_blank" class="external blob">
                                            <img src="<?php echo $CI->config->item('system_base_url_picture') . $info['file_location_variety2']; ?>" style="<?php echo IMAGE_VIDEO_DISPLAY_STYLE; ?>" alt="Picture Missing"/>
                                        </a>
                                    <?php } ?>
                                </td>
                                <td style="width:25%"><?php echo nl2br($info['remarks_variety2']); ?></td>

                            <?php } ?>

                        </tr>
                    <?php } ?>
                </table>
            </div>
        </div>
    <?php } ?>
    <!-----Image & video Accordion (ENDS)----->
</div>
