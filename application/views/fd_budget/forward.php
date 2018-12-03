<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();

$action_buttons = array();
$action_buttons[] = array
(
    'label' => $CI->lang->line("ACTION_BACK"),
    'href' => site_url($CI->controller_url)
);
$CI->load->view("action_buttons", array('action_buttons' => $action_buttons));

$show_variety2 = ($item['variety2_id'] > 0) ? TRUE : FALSE;
$image_base_path = $CI->config->item('system_base_url_picture');
$image_style = FD_IMAGE_DISPLAY_STYLE;
?>
<div class="row widget">

<div class="widget-header">
    <div class="title">
        <?php echo $title; ?>
    </div>
    <div class="clearfix"></div>
</div>

<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_PROPOSAL'); ?> :</label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label class="control-label"><?php echo System_helper::display_date($item['date_proposal']); ?></label>
    </div>
</div>

<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_EXPECTED'); ?> :</label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label class="control-label"><?php echo System_helper::display_date($item['date_expected']); ?></label>
    </div>
</div>

<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_NAME'); ?> :</label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label class="control-label"><?php echo $item['crop_name']; ?></label>
    </div>
</div>

<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_TYPE'); ?> :</label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label class="control-label"><?php echo $item['crop_type_name']; ?></label>
    </div>
</div>

<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_VARIETY1_NAME'); ?> :</label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label class="control-label"><?php echo $item['variety1_name']; ?></label>
    </div>
</div>

<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_VARIETY2_NAME'); ?> :</label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label class="control-label"><?php echo ($item['variety2_name']) ? $item['variety2_name'] : '<i style="font-weight:normal">- No Variety Selected -</i>'; ?>
    </div>
</div>

<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PRESENT_CONDITION'); ?> :</label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label class="control-label" style="font-weight:normal"><?php echo nl2br($item['present_condition']); ?></label>
    </div>
</div>

<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DEALERS_EVALUATION'); ?> :</label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label class="control-label" style="font-weight:normal"><?php echo nl2br($item['farmers_evaluation']); ?></label>
    </div>
</div>

<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DIVISION_NAME'); ?> :</label>
    </div>
    <div class="col-sm-4 col-xs-4">
        <label class="control-label"><?php echo $item['division_name']; ?></label>
    </div>
</div>

<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ZONE_NAME'); ?> :</label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label class="control-label"><?php echo $item['zone_name']; ?></label>
    </div>
</div>

<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_TERRITORY_NAME'); ?> :</label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label class="control-label"><?php echo $item['territory_name']; ?></label>
    </div>
</div>

<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DISTRICT_NAME'); ?> :</label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label class="control-label"><?php echo $item['district_name']; ?></label>
    </div>
</div>

<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_OUTLET_NAME'); ?> :</label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label class="control-label"><?php echo $item['outlet_name']; ?></label>
    </div>
</div>

<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ADDRESS'); ?> :</label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label class="control-label" style="font-weight:normal"><?php echo nl2br($item['address']); ?></label>
    </div>
</div>

<div class="row show-grid">
    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PARTICIPANT_THROUGH_DEALER'); ?> :</label>
        </div>
    </div>
    <div class="row show-grid">
        <div class="col-xs-7">
            <table style="width:100%">
                <?php
                $sub_total_participant = $total_participant = 0;
                $init_ga_id = -1;
                $index=0;
                foreach ($dealers as &$dealer)
                {
                    $dealer['participant'] = (isset($dealer['participant'])) ? $dealer['participant'] : 0;
                    if($init_ga_id != $dealer['ga_id']){
                        ?>
                        <tr>
                            <td style="text-align:right">
                                <label style="font-style:italic; text-decoration:underline;  padding:5px; font-size:1.1em" class="control-label pull-right"><?php echo $dealer['ga_name']; ?>:</label>
                            </td>
                            <td style="text-align:right; width:35%;">&nbsp;</td>
                            <td style="text-align:right; width:5%; padding:5px">&nbsp;</td>
                        </tr>
                        <?php
                        $init_ga_id=$dealer['ga_id'];
                        $index++;
                    }
                    ?>
                    <tr>
                        <td style="text-align:right" colspan="2"><?php echo $dealer['dealer_name'] . ' ( ' . $dealer['phone_no'] . ' )'; ?> :</td>
                        <td style="text-align:right; padding:5px"><?php echo $dealer['participant']; ?></td>
                    </tr>
                    <?php
                    $total_participant += $dealer['participant'];
                    $sub_total_participant += $dealer['participant'];
                }
                ?>
                <tr>
                    <td style="text-align:right; font-weight:bold" colspan="2">Sub Total :</td>
                    <td style="text-align:right; padding:5px; font-weight:bold"><?php echo $sub_total_participant; ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>

<div class="row show-grid">
    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PARTICIPANT_THROUGH_LEAD_FARMER'); ?> :</label>
        </div>
    </div>
    <div class="row show-grid">
        <div class="col-xs-7">
            <table style="width:100%">
                <?php
                $sub_total_participant = 0;
                $init_ga_id = -1;
                $index=0;
                foreach ($lead_farmers as &$farmer)
                {
                    $farmer['participant'] = (isset($farmer['participant'])) ? $farmer['participant'] : 0;
                    if($init_ga_id != $farmer['ga_id']){
                        ?>
                        <tr>
                            <td style="text-align:right">
                                <label style="font-style:italic; text-decoration:underline;  padding:5px; font-size:1.1em" class="control-label pull-right"><?php echo $farmer['ga_name']; ?>:</label>
                            </td>
                            <td style="text-align:right; width:35%;">&nbsp;</td>
                            <td style="text-align:right; width:5%; padding:5px">&nbsp;</td>
                        </tr>
                        <?php
                        $init_ga_id=$farmer['ga_id'];
                        $index++;
                    }
                    ?>
                    <tr>
                        <td style="text-align:right" colspan="2"><?php echo $farmer['lead_farmers_name'] . ' ( ' . $farmer['phone_no'] . ' )'; ?> :</td>
                        <td style="text-align:right; padding:5px"><?php echo $farmer['participant']; ?></td>
                    </tr>
                    <?php
                    $total_participant += $farmer['participant'];
                    $sub_total_participant += $farmer['participant'];
                }
                ?>
                <tr>
                    <td style="text-align:right; font-weight:bold" colspan="2">Sub Total :</td>
                    <td style="text-align:right; padding:5px; font-weight:bold"><?php echo $sub_total_participant; ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>

<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PARTICIPANT_THROUGH_OTHERS'); ?> :</label>
    </div>
    <div class="col-xs-4">
        <label class="control-label"><?php echo $item['participant_others']; ?></label>
        <?php $total_participant += $item['participant_others']; ?>
    </div>
</div>

<div class="row show-grid" id="total_participant_container">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_TOTAL_PARTICIPANT'); ?> :</label>
    </div>
    <div class="col-xs-4">
        <label class="control-label"><?php echo $total_participant; ?></label>
    </div>
</div>

<div class="row show-grid">
    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_FIELD_DAY_BUDGET'); ?> :</label>
        </div>
    </div>
    <div class="row show-grid">
        <div class="col-xs-6">
            <table style="width:100%">
                <?php
                $total_budget = 0;
                foreach ($expense_items as $expense)
                {
                    ?>
                    <tr>
                        <td style="text-align:right"><?php echo $expense['name']; ?> :</td>
                        <td style="text-align:right; width:15%; padding:5px; font-weight:bold"><?php echo System_helper::get_string_amount($expense['amount']); ?></td>
                    </tr>
                    <?php
                    $total_budget += $expense['amount'];
                }
                ?>
            </table>
        </div>
    </div>
</div>

<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_TOTAL_FIELD_DAY_BUDGET'); ?> :</label>
    </div>
    <div class="col-xs-4">
        <label class="control-label"><?php echo System_helper::get_string_amount($total_budget); ?></label>
    </div>
</div>

<div class="row show-grid">
    <div class="col-xs-4">
        <label style="text-decoration:underline;" class="control-label pull-right"><?php echo $CI->lang->line('LABEL_MARKET_SIZE_TITLE'); ?>:</label>
    </div>
</div>

<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_TOTAL_MARKET_SIZE'); ?> :</label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label class="control-label"><?php echo System_helper::get_string_kg($item['quantity_market_size_total']); ?></label>
    </div>
</div>

<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_TOTAL_GA_MARKET_SIZE'); ?> :</label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label class="control-label"><?php echo System_helper::get_string_kg($item['quantity_market_size_ga_total']); ?></label>
    </div>
</div>

<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ARM_MARKET_SIZE'); ?> :</label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label class="control-label"><?php echo System_helper::get_string_kg($item['quantity_market_size_arm']); ?></label>
    </div>
</div>

<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ARM_GA_MARKET_SIZE'); ?> :</label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label class="control-label"><?php echo System_helper::get_string_kg($item['quantity_market_size_ga_arm']); ?></label>
    </div>
</div>

<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_NEXT_SALES_TARGET'); ?> :</label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label class="control-label"><?php echo System_helper::get_string_kg($item['quantity_sales_target']); ?></label>
    </div>
</div>

<div class="row show-grid">
    <div class="col-xs-12">
        <div class="panel panel-default">

            <div class="panel-heading">
                <h4 class="panel-title">
                    <label><a class="external text-danger" data-toggle="collapse" data-target="#collapse1" href="#">+ Uploaded Image</a></label>
                </h4>
            </div>

            <div id="collapse1" class="panel-collapse collapse in">

                <div class="row show-grid">
                    <div class="col-xs-12">
                        <div style="overflow-x:scroll">

                            <table class="table table-bordered">
                                <tr>
                                    <th style="width:25%">Picture Category</th>
                                    <th><?php echo $CI->lang->line('LABEL_VARIETY1_NAME'); ?></th>
                                    <?php
                                    if ($show_variety2)
                                    {
                                        ?>
                                        <th><?php echo $CI->lang->line('LABEL_VARIETY2_NAME'); ?></th>
                                    <?php } ?>
                                </tr>
                                <?php
                                if (isset($picture_categories) && (sizeof($picture_categories) > 0))
                                {
                                    $image_style = "max-height:180px";
                                    $base_path = $CI->config->item('system_base_url_picture');
                                    foreach ($picture_categories as $picture_category)
                                    {
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
                                                $img_src = $base_path . $image_details[$picture_category['value']]['image_location_variety1'];
                                                ?>
                                                <a href="<?php echo $img_src; ?>" target="_blank" class="external blob" style="display:inline-block; padding:3px; border:3px solid #8c8c8c">
                                                    <img style="<?php echo $image_style; ?>" src="<?php echo $img_src; ?>" alt="Picture Missing"/>
                                                </a>
                                            </td>

                                            <?php
                                            if ($show_variety2)
                                            {
                                                ?>

                                                <td id="image_variety2_<?php echo $picture_category['value']; ?>">
                                                    <?php
                                                    $img_src = $base_path . $image_details[$picture_category['value']]['image_location_variety2'];
                                                    ?>
                                                    <a href="<?php echo $img_src; ?>" target="_blank" class="external blob" style="display:inline-block; padding:3px; border:3px solid #8c8c8c">
                                                        <img style="<?php echo $image_style; ?>" src="<?php echo $img_src; ?>" alt="Picture Missing"/>
                                                    </a>
                                                </td>
                                            <?php } ?>
                                        </tr>
                                        <?php
                                        $rem_v1 = $rem_v2 = "&nbsp;";
                                        if (isset($image_details[$picture_category['value']]))
                                        {
                                            if ($image_details[$picture_category['value']]['remarks_variety1'] != "")
                                            {
                                                $rem_v1 = '<label>Remarks:</label> ' . $image_details[$picture_category['value']]['remarks_variety1'];
                                            }
                                            if ($image_details[$picture_category['value']]['remarks_variety2'] != "")
                                            {
                                                $rem_v2 = '<label>Remarks:</label> ' . $image_details[$picture_category['value']]['remarks_variety2'];
                                            }
                                        }
                                        ?>
                                        <tr>
                                            <td>
                                                <?php echo nl2br($rem_v1); ?>
                                            </td>
                                            <?php
                                            if ($show_variety2)
                                            {
                                                ?>
                                                <td>
                                                    <?php echo nl2br($rem_v2); ?>
                                                </td>
                                            <?php } ?>
                                        </tr>
                                    <?php
                                    }
                                } ?>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<form class="form_valid" id="save_form" action="<?php echo site_url($CI->controller_url . '/index/save_forward'); ?>" method="post">
    <input type="hidden" id="id" name="id" value="<?php echo $item['budget_id']; ?>"/>

    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_FORWARD'); ?>
                <span style="color:#FF0000">*</span></label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <select name="item[status_budget_forward]" class="form-control status-combo">
                <option value=""><?php echo $CI->lang->line('SELECT'); ?></option>
                <option value="<?php echo $CI->config->item('system_status_forwarded'); ?>"><?php echo $CI->lang->line('LABEL_FORWARD'); ?></option>
            </select>
        </div>
    </div>

    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_REMARKS'); ?> &nbsp;</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <textarea name="item[remarks_budget_forward]" class="form-control"></textarea>
        </div>
    </div>

    <div class="row show-grid">
        <div class="col-xs-4">
            &nbsp;
        </div>
        <div class="col-sm-4 col-xs-4">
            <div class="action_button pull-right">
                <button id="button_action_save" type="button" class="btn" data-form="#save_form">Save</button>
            </div>
        </div>
        <div class="col-sm-4 col-xs-4">
            &nbsp;
        </div>
    </div>
</form>

<div class="clearfix"></div>
</div>

<script type="text/javascript">
    jQuery(document).ready(function ($) {
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
