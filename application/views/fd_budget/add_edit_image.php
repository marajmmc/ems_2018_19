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
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_FDB_PROPOSAL_DATE'); ?> : </label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label class="control-label"><?php echo System_helper::display_date($item['fdb_proposal_date']); ?>
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

<?php //pr($picture_categories, 0); ?>

<div class="row show-grid">
    <div class="col-xs-12">
        <table class="table table-bordered">
            <thead>
            <tr>
                <th style="width:20%">Picture Category</th>
                <th style="width:40%" colspan="2"><?php echo $CI->lang->line('LABEL_VARIETY1_NAME'); ?></th>
                <th style="width:40%" colspan="2"><?php echo $CI->lang->line('LABEL_VARIETY2_NAME'); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php
            if (isset($picture_categories) && (sizeof($picture_categories) > 0))
            {
                foreach ($picture_categories as $picture_category)
                {
                    ?>
                    <tr>
                        <td rowspan="2"><?php echo $picture_category['text']; ?></td>
                        <td id="image_variety1_<?php echo $picture_category['value']; ?>">
                            <?php
                            if ((isset($file_details[$picture_category['value']])) && (strlen($file_details[$picture_category['value']]['variety1_file_location']) > 0))
                            {
                                $image = $file_details[$picture_category['value']]['variety1_file_location'];
                                ?>
                                <a href="<?php echo $img_src; ?>" target="_blank" class="blob">
                                    <img src="<?php echo $img_src; ?>" alt="Picture Missing"/> </a>
                            <?php
                            }
                            else
                            {
                                $image = 'images/no_image.jpg';
                                ?>
                                <img style="max-width:250px;max-height:250px;" src="<?php echo $CI->config->item('system_base_url_picture') . $image; ?>" alt="No Image Found"><?php
                            }
                            ?>
                        </td>

                        <td style="width:1%">
                            <input type="file" class="browse_button" data-preview-container="#image_variety1_<?php echo $picture_category['value']; ?>" name="variety_1_category_<?php echo $picture_category['value']; ?>">
                        </td>

                        <td id="image_variety2_<?php echo $picture_category['value']; ?>">
                            <?php
                            if ((isset($file_details[$picture_category['value']])) && (strlen($file_details[$picture_category['value']]['variety2_file_location']) > 0))
                            {
                                $image = $file_details[$picture_category['value']]['variety2_file_location'];
                                ?>
                                <a href="<?php echo $img_src; ?>" target="_blank" class="blob">
                                    <img src="<?php echo $img_src; ?>" alt="Picture Missing"/> </a>
                            <?php
                            }
                            else
                            {
                                $image = 'images/no_image.jpg';
                                ?>
                                <img style="max-width:250px;max-height:250px;" src="<?php echo $CI->config->item('system_base_url_picture') . $image; ?>" alt="No Image Found"><?php
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
                            <textarea class="form-control" name="variety1_file_remarks[<?php echo $picture_category['value']; ?>]">
                                <?php $txt = (isset($file_details[$picture_category['value']])) ? $file_details[$picture_category['value']]['variety1_file_remarks'] : "";
                                echo nl2br($txt); ?>
                            </textarea>
                        </td>
                        <td colspan="2">
                            <label>Remarks :</label>
                            <textarea class="form-control" name="variety2_file_remarks[<?php echo $picture_category['value']; ?>]">
                                <?php $txt = (isset($file_details[$picture_category['value']])) ? $file_details[$picture_category['value']]['variety2_file_remarks'] : "";
                                echo nl2br($txt); ?>
                            </textarea>
                        </td>
                    </tr>
                <?php
                }
            } ?>
            </tbody>
        </table>
    </div>
</div>

<?php /* <div id="image" class="panel-collapse">
    <div id="files_container" class="panel-collapse">
        <div style="overflow-x: auto;" class="row show-grid">

            <table class="table table-bordered">
                <thead>
                <tr>
                    <th style="min-width:60px;">Image Type</th>
                    <th style="max-width:350px;" colspan="2">ARM</th>
                    <th style="max-width:350px;" colspan="2">Competitor</th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($picture_categories as $pic_cat)
                {
                    ?>
                    <tr>
                        <td style="min-width:60px;" ><b><?php echo $pic_cat['text']; ?></b></td>
                        <td style="max-width:270px; max-height:200px;">
                            <div class="col-xs-4" id="image_arm_<?php echo $pic_cat['value']; ?>">
                                <?php
                                $image = 'images/no_image.jpg';

                                if ((isset($file_details[$pic_cat['value']])) && (strlen($file_details[$pic_cat['value']]['arm_file_location']) > 0))
                                {
                                    $image = $file_details[$pic_cat['value']]['arm_file_location'];
                                }
                                ?>
                                <img style="max-width:270px;max-height:200px;" src="<?php echo $CI->config->item('system_base_url_picture') . $image; ?>">
                            </div>
                        </td>
                        <td style="max-width:80px; ">
                            <input type="file" class="browse_button" data-preview-container="#image_arm_<?php echo $pic_cat['value']; ?>" name="arm_<?php echo $pic_cat['value']; ?>">
                            <?php if ($item['id'] > 0)
                            {
                                ?>
                                <input type="hidden" name="image_info[<?php echo $pic_cat['value']; ?>][arm_file_name]" value="<?php echo $file_details[$pic_cat['value']]['arm_file_name'] ?>">
                                <input type="hidden" name="image_info[<?php echo $pic_cat['value']; ?>][arm_file_location]" value="<?php echo $file_details[$pic_cat['value']]['arm_file_location'] ?>">
                            <?php } ?>
                        </td>

                        <td style="max-width:270px;max-height:200px;">
                            <div class="col-xs-4" id="image_com_<?php echo $pic_cat['value']; ?>">
                                <?php
                                $image = 'images/no_image.jpg';
                                if ((isset($file_details[$pic_cat['value']])) && (strlen($file_details[$pic_cat['value']]['competitor_file_location']) > 0))
                                {
                                    $image = $file_details[$pic_cat['value']]['competitor_file_location'];
                                }
                                ?>
                                <img style="max-width:270px;max-height:200px;" src="<?php echo $CI->config->item('system_base_url_picture') . $image; ?>">
                            </div>
                        </td>
                        <td style="min-width:80px;">
                            <input type="file" class="browse_button" data-preview-container="#image_com_<?php echo $pic_cat['value']; ?>" name="competitor_<?php echo $pic_cat['value']; ?>">
                            <?php if ($item['id'] > 0)
                            {
                                ?>
                                <input type="hidden" name="image_info[<?php echo $pic_cat['value']; ?>][competitor_file_name]" value="<?php echo $file_details[$pic_cat['value']]['competitor_file_name'] ?>">
                                <input type="hidden" name="image_info[<?php echo $pic_cat['value']; ?>][competitor_file_location]" value="<?php echo $file_details[$pic_cat['value']]['competitor_file_location'] ?>">
                            <?php } ?>
                        </td>
                    </tr>

                    <tr>
                        <td style="min-width:60px; border: none;"></td>
                        <td style="min-width:210px;border: none;">
                            <label>Remarks :</label>
                            <textarea class="form-control arm_remarks" name="arm_file_details_remarks[<?php echo $pic_cat['value']; ?>]"><?php if (isset($file_details[$pic_cat['value']]))
                                {
                                    echo $file_details[$pic_cat['value']]['arm_file_remarks'];
                                } ?></textarea>
                        </td>
                        <td style="min-width:60px;border: none;"></td>
                        <td style="min-width:210px;border: none;">
                            <label>Remarks :</label>
                            <textarea class="form-control com_remarks" name="com_file_details_remarks[<?php echo $pic_cat['value']; ?>]"><?php if (isset($file_details[$pic_cat['value']]))
                                {
                                    echo $file_details[$pic_cat['value']]['competitor_file_remarks'];
                                } ?></textarea>
                        </td>
                        <td style="min-width:60px;border: none;"></td>

                    </tr>

                <?php } ?>

                </tbody>
            </table>
        </div>
    </div>
</div>  */
?>

</div>
<div class="clearfix"></div>

</form>

<script type="text/javascript">
    jQuery(document).ready(function ($) {
        system_off_events(); // Triggers
        $(".browse_button").filestyle({input: false, icon: false, buttonText: "Upload", buttonName: "btn-primary"});
    });
</script>
