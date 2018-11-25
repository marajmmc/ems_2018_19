<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();

$action_buttons = array();
$action_buttons[] = array
(
    'label' => $CI->lang->line("ACTION_BACK"),
    'href' => site_url($CI->controller_url)
);
if (isset($CI->permissions['action2']) && ($CI->permissions['action2'] == 1))
{
    $action_buttons[] = array
    (
        'type' => 'button',
        'label' => $CI->lang->line("ACTION_SAVE"),
        'id' => 'button_action_save',
        'data-form' => '#save_form'
    );
}
$action_buttons[] = array(
    'type' => 'button',
    'label' => $CI->lang->line("ACTION_CLEAR"),
    'id' => 'button_action_clear',
    'data-form' => '#save_form'
);
$CI->load->view("action_buttons", array('action_buttons' => $action_buttons));
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
<form class="form_valid" id="save_form" action="<?php echo site_url($CI->controller_url . '/index/save_image'); ?>" method="post">
    <input type="hidden" id="id" name="id" value="<?php echo $item['id']; ?>"/>

    <div class="row widget">
        <div class="widget-header">
            <div class="title">
                <?php echo $title; ?>
            </div>
            <div class="clearfix"></div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_PROPOSAL'); ?> : </label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo System_helper::display_date($item['date_proposal']); ?>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_NAME'); ?> : </label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $item['crop_name']; ?>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_TYPE'); ?> : </label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $item['crop_type_name']; ?>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_VARIETY1_NAME'); ?> : </label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $item['variety1_name']; ?>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_VARIETY2_NAME'); ?> : </label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $item['variety2_name']; ?>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-12">
                <div style="overflow-x:scroll">
                    <table class="table table-bordered">
                    <tr>
                        <th style="width:26%">Picture Category</th>
                        <th style="width:37%" colspan="2"><?php echo $CI->lang->line('LABEL_VARIETY1_NAME'); ?></th>
                        <th style="width:37%" colspan="2"><?php echo $CI->lang->line('LABEL_VARIETY2_NAME'); ?></th>
                    </tr>
                    <?php
                    if (isset($picture_categories) && (sizeof($picture_categories) > 0))
                    {
                        $image_style = "max-height:220px";
                        $base_path = $CI->config->item('system_base_url_picture');
                        foreach ($picture_categories as $picture_category)
                        {
                            if (isset($file_details[$picture_category['value']]))
                            {
                                $file_location_variety1 = $file_details[$picture_category['value']]['file_location_variety1'];
                                $file_location_variety2 = $file_details[$picture_category['value']]['file_location_variety2'];
                                $remarks_variety1 = $file_details[$picture_category['value']]['remarks_variety1'];
                                $remarks_variety2 = $file_details[$picture_category['value']]['remarks_variety2'];
                                if (($picture_category['status'] == $CI->config->item('system_status_inactive')) && (trim($file_location_variety1) == "") && (trim($remarks_variety1) == "")
                                    && (trim($file_location_variety2) == "") && (trim($remarks_variety2) == ""))
                                {
                                    continue; // If no Data found regarding In-Active picture category.
                                }
                            }
                            else if (($picture_category['status'] == $CI->config->item('system_status_inactive')))
                            {
                                continue;
                            }
                            ?>
                            <tr>
                                <td rowspan="2">
                                    <?php
                                    if ($picture_category['status'] == $CI->config->item('system_status_inactive'))
                                    {
                                        $picture_category['text'] .= ' <br/>( <b class="text-danger">' . $CI->config->item('system_status_inactive') . '</b> )';
                                    }
                                    echo $picture_category['text'];
                                    ?>
                                </td>

                                <td id="image_variety1_<?php echo $picture_category['value']; ?>">
                                    <?php
                                    if ((isset($file_details[$picture_category['value']])) && (strlen($file_details[$picture_category['value']]['file_location_variety1']) != ""))
                                    {
                                        $img_src = $base_path . $file_details[$picture_category['value']]['file_location_variety1'];
                                        ?>
                                        <a href="<?php echo $img_src; ?>" target="_blank" class="external blob">
                                            <img style="<?php echo $image_style; ?>" src="<?php echo $img_src; ?>" alt="Picture Missing"/>
                                        </a>
                                    <?php
                                    }
                                    else
                                    {
                                        $img_src = $base_path . 'images/no_image.jpg';
                                        ?>
                                        <img style="<?php echo $image_style; ?>" src="<?php echo $img_src; ?>" alt="No Image Found" /><?php
                                    }
                                    ?>
                                </td>

                                <td style="width:1%">
                                    <input type="file" class="browse_button" data-preview-container="#image_variety1_<?php echo $picture_category['value']; ?>" name="variety_1_category_<?php echo $picture_category['value']; ?>">
                                </td>

                                <td id="image_variety2_<?php echo $picture_category['value']; ?>">
                                    <?php
                                    if ((isset($file_details[$picture_category['value']])) && (strlen($file_details[$picture_category['value']]['file_location_variety2']) != ""))
                                    {
                                        $img_src = $base_path . $file_details[$picture_category['value']]['file_location_variety2'];
                                        ?>
                                        <a href="<?php echo $img_src; ?>" target="_blank" class="external blob">
                                            <img style="<?php echo $image_style; ?>" src="<?php echo $img_src; ?>" alt="Picture Missing"/>
                                        </a>
                                    <?php
                                    }
                                    else
                                    {
                                        $img_src = $base_path . 'images/no_image.jpg';
                                        ?>
                                        <img style="<?php echo $image_style; ?>" src="<?php echo $img_src; ?>" alt="No Image Found" /><?php
                                    }
                                    ?>
                                </td>

                                <td style="width:1%">
                                    <input type="file" class="browse_button" data-preview-container="#image_variety2_<?php echo $picture_category['value']; ?>" name="variety_2_category_<?php echo $picture_category['value']; ?>">
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <label>Remarks :</label>
                                    <?php $txt = (isset($file_details[$picture_category['value']])) ? $file_details[$picture_category['value']]['remarks_variety1'] : ""; ?>
                                    <textarea class="form-control" name="item_info[<?php echo $picture_category['value']; ?>][remarks_variety1]"><?php echo $txt; ?></textarea>
                                </td>
                                <td colspan="2">
                                    <label>Remarks :</label>
                                    <?php $txt = (isset($file_details[$picture_category['value']])) ? $file_details[$picture_category['value']]['remarks_variety2'] : ""; ?>
                                    <textarea class="form-control" name="item_info[<?php echo $picture_category['value']; ?>][remarks_variety2]"><?php echo $txt; ?></textarea>
                                </td>
                            </tr>
                        <?php
                        }
                    } ?>
                </table>
                </div>
            </div>
        </div>

        <div class="clearfix"></div>
    </div>

    <div class="clearfix"></div>
</form>

<script type="text/javascript">
    jQuery(document).ready(function ($) {
        system_off_events(); // Triggers
        system_preset({controller:'<?php echo $CI->router->class; ?>'});
        $(".browse_button").filestyle({input: false, icon: false, buttonText: "Upload", buttonName: "btn-primary"});
    });
</script>
