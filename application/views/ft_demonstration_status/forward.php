<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();

$action_buttons = array();
$action_buttons[] = array
(
    'label' => $CI->lang->line("ACTION_BACK"),
    'href' => site_url($CI->controller_url)
);

$CI->load->view('action_buttons', array('action_buttons' => $action_buttons));
?>

<form class="form_valid" id="save_form" action="<?php echo site_url($CI->controller_url . '/index/save_forward'); ?>" method="post">
    <input type="hidden" id="id" name="id" value="<?php echo $item['id']; ?>"/>

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
                <div id="info_<?php echo $file_type; ?>" class="panel-collapse collapse out">
                    <table class="table table-bordered">
                        <tr>
                            <th rowspan="2" style="vertical-align:bottom"><?php echo $CI->lang->line('LABEL_SL_NO'); ?></th>
                            <th colspan="2" style="text-align:center"><?php echo $CI->lang->line('LABEL_VARIETY1_NAME'); ?></th>
                            <?php if ($item['variety2_id'] > 0)
                            {
                                ?>
                                <th colspan="2" style="text-align:center"><?php echo $CI->lang->line('LABEL_VARIETY2_NAME'); ?></th>
                            <?php } ?>
                        </tr>
                        <tr>
                            <th><?php echo $file_type; ?></th>
                            <th>Remarks</th>
                            <?php if ($item['variety2_id'] > 0)
                            {
                                ?>
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
        <?php /*if (!$item['date_actual_evaluation']){ ?>
            <div class="row show-grid">
                <div class="col-xs-12 bg-danger text-danger" style="text-align:center; padding:10px 0 5px; font-size:1.2em; font-weight:normal">
                    '<label class="control-label"><?php echo $CI->lang->line('LABEL_DATE_ACTUAL_EVALUATION'); ?></label>' must be Set.
                </div>
            </div>
        <?php }*/ ?>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_FORWARD'); ?>
                    <span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-xs-4">
                <select name="item[status_forward]" class="form-control status-combo">
                    <option value=""><?php echo $CI->lang->line('SELECT'); ?></option>
                    <option value="<?php echo $CI->config->item('system_status_forwarded'); ?>"><?php echo $CI->lang->line('LABEL_FORWARD'); ?></option>
                </select>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_FARMERS_COMMENT'); ?>
                    <span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-xs-4">
                <textarea id="remarks" name="item[remarks_farmer]" class="form-control"></textarea>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_TMPOS_COMMENT'); ?>
                    <span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-xs-4">
                <textarea id="remarks" name="item[remarks_forward]" class="form-control"></textarea>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4"> &nbsp; </div>
            <div class="col-xs-4">
                <div class="action_button pull-right">
                    <button id="button_action_save" type="button" class="btn" data-form="#save_form">Save</button>
                </div>
            </div>
        </div>

    </div>

    <div class="clearfix"></div>

</form>

<script type="text/javascript">
    $(document).ready(function () {
        system_off_events(); // Triggers

        $(".status-combo").on('change', function (event) {
            var options = $(this).val();
            if (options == '<?php echo $CI->config->item('system_status_forwarded'); ?>') {
                $("#button_action_save").attr('data-message-confirm', '<?php echo $CI->lang->line('MSG_CONFIRM_FORWARD'); ?>');
            } else {
                $("#button_action_save").removeAttr('data-message-confirm');
            }
        });
    });
</script>
