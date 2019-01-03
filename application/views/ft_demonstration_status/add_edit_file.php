<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();

$action_buttons = array();

if ($file_type == $CI->config->item('system_file_type_image'))
{
    $action_buttons[] = array
    (
        'label' => $CI->lang->line("ACTION_BACK"),
        'href' => site_url($CI->controller_url . '/index/list_image/' . $id)
    );
}
else
{
    $action_buttons[] = array
    (
        'label' => $CI->lang->line("ACTION_BACK"),
        'href' => site_url($CI->controller_url . '/index/list_video/' . $id)
    );
}
$action_buttons[] = array
(
    'type' => 'button',
    'label' => $CI->lang->line("ACTION_SAVE"),
    'id' => 'button_action_save',
    'data-form' => '#save_form'
);
$CI->load->view("action_buttons", array('action_buttons' => $action_buttons));

?>

<form class="form_valid" id="save_form" action="<?php echo site_url($CI->controller_url . '/index/save_file'); ?>" method="post">
    <input type="hidden" id="id" name="id" value="<?php echo $id ?>"/>
    <input type="hidden" id="file_id" name="file_id" value="<?php echo $file_id ?>"/>
    <input type="hidden" id="file_type" name="file_type" value="<?php echo $file_type ?>"/>

    <div class="row widget">
        <div class="widget-header">
            <div class="title">
                <?php echo $title; ?>
            </div>
            <div class="clearfix"></div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-12">
                <table class="table table-bordered">
                    <tr>
                        <th style="width:14%">&nbsp;</th>
                        <th colspan="2" style="width:43%;text-align:center"><?php echo $CI->lang->line('LABEL_VARIETY1_NAME') ?></th>
                        <th colspan="2" style="width:43%;text-align:center"><?php echo $CI->lang->line('LABEL_VARIETY2_NAME') ?></th>
                    </tr>
                    <tr>
                        <th style="text-align:right">Select <?php echo $file_type; ?></th>
                        <?php
                        if ($file_type == $CI->config->item('system_file_type_image')) //for IMAGE File Upload Div & Button
                        {
                            ?>
                            <td>
                                <div id="file_variety1">
                                    <a href="<?php echo $CI->config->item('system_base_url_picture') . $item['file_location_variety1']; ?>" target="_blank" class="external blob">
                                        <img src="<?php echo $CI->config->item('system_base_url_picture') . $item['file_location_variety1']; ?>" style="height:200px" alt="Picture Missing"/>
                                    </a>
                                </div>
                            </td>
                            <td style="vertical-align:bottom;width:1%">
                                <input type="file" class="browse_button" data-preview-container="#file_variety1" name="file_variety1" style="text-align:right"/>
                            </td>
                            <td>
                                <div id="file_variety2">
                                    <a href="<?php echo $CI->config->item('system_base_url_picture') . $item['file_location_variety2']; ?>" target="_blank" class="external blob">
                                        <img src="<?php echo $CI->config->item('system_base_url_picture') . $item['file_location_variety2']; ?>" style="height:200px" alt="Picture Missing"/>
                                    </a>
                                </div>
                            </td>
                            <td style="vertical-align:bottom;width:1%">
                                <input type="file" class="browse_button" data-preview-container="#file_variety2" name="file_variety2" style="text-align:right"/>
                            </td>
                        <?php
                        }
                        else //for VIDEO File Upload Div & Button
                        {
                            ?>
                            <td>
                                <video controls id="" style="max-width:300px;height:200px">
                                    <source src="<?php echo $CI->config->item('system_base_url_picture') . $item['file_location_variety1']; ?>" id="file_variety1_video"/>
                                </video>
                            </td>
                            <td style="vertical-align:bottom;width:1%">
                                <input type="file" class="browse_button file_type_video" name="file_variety1" accept="video/*">
                            </td>
                            <td>
                                <video controls id="" style="max-width:300px;height:200px">
                                    <source src="<?php echo $CI->config->item('system_base_url_picture') . $item['file_location_variety2']; ?>" id="file_variety2_video"/>
                                </video>
                            </td>
                            <td style="vertical-align:bottom;width:1%">
                                <input type="file" class="browse_button file_type_video" name="file_variety2" accept="video/*">
                            </td>
                        <?php
                        }
                        ?>
                    </tr>
                    <tr>
                        <th style="text-align:right"><?php echo $CI->lang->line('LABEL_REMARKS'); ?></th>
                        <td colspan="2">
                            <textarea class="form-control" name="item[remarks_variety1]"><?php echo $item['remarks_variety1']; ?></textarea>
                        </td>
                        <td colspan="2">
                            <textarea class="form-control" name="item[remarks_variety2]"><?php echo $item['remarks_variety2']; ?></textarea>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="clearfix"></div>
    </div>
</form>

<script type="text/javascript">
    jQuery(document).ready(function ($) {
        system_off_events(); // Triggers
        system_preset({controller: '<?php echo $CI->router->class; ?>'});
        $(".browse_button").filestyle({input: false, icon: false, buttonText: "Upload", buttonName: "btn-primary"});

        $(document).on("change", ".file_type_video", function (evt) {
            var file_name = $(this).attr('name');
            var $source = $('#'+file_name+'_video');

            $source[0].src = URL.createObjectURL(this.files[0]);
            $source.parent()[0].load();
            var video = document.createElement('video');
            video.src = URL.createObjectURL(this.files[0]);
            video.onloadedmetadata = function () {
                window.URL.revokeObjectURL(this.src);
            }
        });
    });
</script>
