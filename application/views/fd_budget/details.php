<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI =& get_instance();

$action_buttons = array();
$action_buttons[] = array(
    'label' => $CI->lang->line("ACTION_BACK") . ' to Pending List',
    'href' => site_url($CI->controller_url)
);
$action_buttons[] = array(
    'label' => $CI->lang->line("ACTION_BACK") . ' to All List',
    'href' => site_url($CI->controller_url . '/index/list_all')
);
if (isset($CI->permissions['action4']) && ($CI->permissions['action4'] == 1))
{
    $action_buttons[] = array(
        'type' => 'button',
        'label' => $CI->lang->line("ACTION_PRINT"),
        'onClick' => "window.print()"
    );
}
$CI->load->view('action_buttons', array('action_buttons' => $action_buttons));

//--------Image Settings-------
$show_variety2 = ($item['variety2_id'] > 0) ? TRUE : FALSE;
$image_base_path = $CI->config->item('system_base_url_picture');
$image_style = FD_IMAGE_DISPLAY_STYLE;

//--------Accordion View Settings----------
$show_basic_info = (isset($show_basic_info)) ? $show_basic_info : TRUE;
$show_participant_info = (isset($show_participant_info)) ? $show_participant_info : TRUE;
$show_expense_info = (isset($show_expense_info)) ? $show_expense_info : TRUE;
$show_image_info = (isset($show_image_info)) ? $show_image_info : TRUE;

?>

<div class="row widget">
<div class="widget-header" style="margin-bottom:20px">
    <div class="title"><?php echo $title; ?></div>
    <div class="clearfix"></div>
</div>

<?php
if ($show_basic_info)
{
    ?>

    <?php echo $CI->load->view("info_basic", '', true); ?>

<?php
}
if ($show_participant_info)
{
    ?>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <label><a class="external text-danger" data-toggle="collapse" data-target="#collapse1" href="#"> + Field Day Participants</a></label>
            </h4>
        </div>
        <div id="collapse1" class="panel-collapse collapse out">
            <div class="row show-grid participant-wrap" style="margin:0">
                <div class="col-xs-6" style="font-size:1.3em; padding:10px; background:#E8E8E8"><?php echo $CI->lang->line('LABEL_PARTICIPANT_THROUGH_DEALER'); ?></div>
                <div class="col-xs-6" style="font-size:1.3em; padding:10px; background:#E8E8E8;border-left:1px solid #cfcfcf"><?php echo $CI->lang->line('LABEL_PARTICIPANT_THROUGH_LEAD_FARMER'); ?></div>
            </div>

            <div class="row show-grid" style="margin:0">
                <div class="col-xs-6" id="dealer-wrap">
                    <table style="width:100%">
                        <tr>
                            <td colspan="2">&nbsp;</td>
                            <th style="text-align:right;width:12%">Budgeted</th>
                            <th style="text-align:right;width:12%">Actual</th>
                        </tr>
                        <?php
                        $sub_total_dealer = $total_participant = 0;
                        $reporting_sub_total_dealer = $reporting_total_participant = 0;
                        $init_ga_id = -1;
                        foreach ($dealers as &$dealer)
                        {
                            $dealer['participant'] = (isset($dealer['participant'])) ? $dealer['participant'] : 0;
                            if ($init_ga_id != $dealer['ga_id'])
                            {
                                ?>
                                <tr>
                                    <td style="text-align:right">
                                        <label style="font-style:italic; text-decoration:underline; padding:5px;" class="control-label pull-right"><?php echo $dealer['ga_name']; ?>:</label>
                                    </td>
                                    <td style="text-align:right; width:35%;">&nbsp;</td>
                                    <td colspan="2">&nbsp;</td>
                                </tr>
                                <?php
                                $init_ga_id = $dealer['ga_id'];
                            }
                            ?>
                            <tr>
                                <td style="text-align:right" colspan="2"><?php echo $dealer['dealer_name'] . ' ( ' . $dealer['phone_no'] . ' )'; ?> :</td>
                                <td style="text-align:right; padding:5px"><?php echo $dealer['participant']; ?></td>
                                <td style="text-align:right; padding:5px">
                                    <?php
                                    if (isset($old_reporting['reporting_participants_dealer'][$dealer['dealer_id']]))
                                    {
                                        echo $old_reporting['reporting_participants_dealer'][$dealer['dealer_id']];
                                        $reporting_sub_total_dealer += $old_reporting['reporting_participants_dealer'][$dealer['dealer_id']];
                                        $reporting_total_participant += $old_reporting['reporting_participants_dealer'][$dealer['dealer_id']];
                                    }
                                    else
                                    {
                                        echo "-";
                                    }
                                    ?>
                                </td>
                            </tr>
                            <?php
                            $total_participant += $dealer['participant'];
                            $sub_total_dealer += $dealer['participant'];
                        }
                        ?>
                        <tr>
                            <td style="text-align:right; font-weight:bold" colspan="2">Sub Total :</td>
                            <td style="text-align:right; padding:5px; font-weight:bold"><?php echo $sub_total_dealer; ?></td>
                            <td style="text-align:right; padding:5px; font-weight:bold"><?php echo $reporting_sub_total_dealer; ?></td>
                        </tr>
                    </table>
                </div>

                <div class="col-xs-6" id="farmer-wrap" style="border-left:1px solid #cfcfcf; overflow-y:scroll">
                    <table style="width:100%">
                        <tr>
                            <td colspan="2">&nbsp;</td>
                            <th style="text-align:right;width:12%">Budgeted</th>
                            <th style="text-align:right;width:12%">Actual</th>
                        </tr>
                        <?php
                        $sub_total_farmer = 0;
                        $reporting_sub_total_farmer = 0;
                        $init_ga_id = -1;
                        foreach ($lead_farmers as &$farmer)
                        {
                            $farmer['participant'] = (isset($farmer['participant'])) ? $farmer['participant'] : 0;
                            if ($init_ga_id != $farmer['ga_id'])
                            {
                                ?>
                                <tr>
                                    <td style="text-align:right">
                                        <label style="font-style:italic; text-decoration:underline; padding:5px;" class="control-label pull-right"><?php echo $farmer['ga_name']; ?>:</label>
                                    </td>
                                    <td style="text-align:right; width:35%;">&nbsp;</td>
                                    <td colspan="2">&nbsp;</td>
                                </tr>
                                <?php
                                $init_ga_id = $farmer['ga_id'];
                            }
                            ?>
                            <tr>
                                <td style="text-align:right" colspan="2"><?php echo $farmer['lead_farmers_name'] . ' ( ' . $farmer['phone_no'] . ' )'; ?> :</td>
                                <td style="text-align:right; padding:5px"><?php echo $farmer['participant']; ?></td>
                                <td style="text-align:right; padding:5px">
                                    <?php
                                    if (isset($old_reporting['reporting_participants_farmer'][$farmer['lead_farmers_id']]))
                                    {
                                        echo $old_reporting['reporting_participants_farmer'][$farmer['lead_farmers_id']];
                                        $reporting_sub_total_farmer += $old_reporting['reporting_participants_farmer'][$farmer['lead_farmers_id']];
                                        $reporting_total_participant += $old_reporting['reporting_participants_farmer'][$farmer['lead_farmers_id']];
                                    }
                                    else
                                    {
                                        echo "-";
                                    }
                                    ?>
                                </td>
                            </tr>
                            <?php
                            $total_participant += $farmer['participant'];
                            $sub_total_farmer += $farmer['participant'];
                        }
                        ?>
                        <tr>
                            <td style="text-align:right; font-weight:bold" colspan="2">Sub Total :</td>
                            <td style="text-align:right; padding:5px; font-weight:bold"><?php echo $sub_total_farmer; ?></td>
                            <td style="text-align:right; padding:5px; font-weight:bold"><?php echo $reporting_sub_total_farmer; ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="row show-grid bottom-summary" style="background:#E8E8E8; text-align:right">
                <div class="col-xs-4"> &nbsp; </div>
                <div class="col-xs-4" style="padding:15px">
                    <table class="table" style="width:100%;margin:0">
                        <tr>
                            <th> &nbsp; </th>
                            <th style="width:20%;text-align:right">Budgeted</th>
                            <th style="width:15%;text-align:right">Actual</th>
                        </tr>
                        <tr>
                            <td><?php echo $CI->lang->line('LABEL_PARTICIPANT_THROUGH_DEALER'); ?></td>
                            <td><?php echo $sub_total_dealer; ?></td>
                            <td><?php echo $reporting_sub_total_dealer; ?></td>
                        </tr>
                        <tr>
                            <td><?php echo $CI->lang->line('LABEL_PARTICIPANT_THROUGH_LEAD_FARMER'); ?></td>
                            <td><?php echo $sub_total_farmer; ?></td>
                            <td><?php echo $reporting_sub_total_farmer; ?></td>
                        </tr>
                        <tr>
                            <td><?php echo $CI->lang->line('LABEL_PARTICIPANT_THROUGH_OTHERS'); ?></td>
                            <td><?php echo $item['participant_others']; ?></td>
                            <td><?php echo (isset($old_reporting['reporting_participant_others'])) ? $old_reporting['reporting_participant_others'] : '-'; ?></td>
                        </tr>
                        <?php
                        $total_participant += $item['participant_others'];
                        if (isset($old_reporting['reporting_participant_others']))
                        {
                            $reporting_total_participant += $old_reporting['reporting_participant_others'];
                        }
                        ?>
                        <tr>
                            <th style="border-top:1px solid #000; text-align:right"><?php echo $CI->lang->line('LABEL_TOTAL_PARTICIPANT'); ?></th>
                            <th style="border-top:1px solid #000; text-align:right"><?php echo $total_participant; ?></th>
                            <th style="border-top:1px solid #000; text-align:right"><?php echo $reporting_total_participant; ?></th>
                        </tr>
                    </table>
                </div>
            </div>

        </div>
    </div>

<?php
}
if ($show_expense_info)
{
    ?>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <label><a class="external text-danger" data-toggle="collapse" data-target="#collapse2" href="#"> + Field Day Expense &amp; Market Size </a></label>
            </h4>
        </div>
        <div id="collapse2" class="panel-collapse collapse out">
            <div class="row show-grid">
                <div class="col-xs-6">
                    <table class="table" style="width:100%">
                        <tr>
                            <th colspan="3" style="border-bottom:1px solid #000; text-align:center">Budget Expense Summary</th>
                        </tr>
                        <tr>
                            <th style="border-bottom:1px solid #000; text-align:right">Items &nbsp;</th>
                            <th style="border-bottom:1px solid #000; text-align:right; width:20%">Budgeted</th>
                            <th style="border-bottom:1px solid #000; text-align:right; width:20%">Actual</th>
                        </tr>
                        <?php
                        $total_budget = 0;
                        $reporting_total_budget = 0;
                        foreach ($expense_items as $expense)
                        {
                            ?>
                            <tr>
                                <td style="text-align:right"><?php echo $expense['name']; ?> :</td>
                                <td style="text-align:right"><?php echo System_helper::get_string_amount($expense['amount']); ?></td>
                                <td style="text-align:right">
                                    <?php
                                    if (isset($old_reporting['reporting_amount_expense_items'][$expense['id']]))
                                    {
                                        echo System_helper::get_string_amount($old_reporting['reporting_amount_expense_items'][$expense['id']]);
                                        $reporting_total_budget += $old_reporting['reporting_amount_expense_items'][$expense['id']];
                                    }
                                    else
                                    {
                                        echo "-";
                                    }
                                    ?>
                                </td>
                            </tr>
                            <?php
                            $total_budget += $expense['amount'];
                        }
                        ?>
                        <tr>
                            <td style="border-top:1px solid #000; text-align:right; font-weight:bold"><?php echo $CI->lang->line('LABEL_TOTAL_FIELD_DAY_BUDGET'); ?> :</td>
                            <td style="border-top:1px solid #000; text-align:right; font-weight:bold"><?php echo System_helper::get_string_amount($total_budget); ?></td>
                            <td style="border-top:1px solid #000; text-align:right; font-weight:bold"><?php echo System_helper::get_string_amount($reporting_total_budget); ?></td>
                        </tr>
                    </table>
                </div>


                <div class="col-xs-6">
                    <table class="table" style="width:100%">
                        <tr>
                            <th colspan="3" style="border-bottom:1px solid #000; text-align:center"><?php echo $CI->lang->line('LABEL_MARKET_SIZE_TITLE'); ?></th>
                        </tr>
                        <tr>
                            <th style="border-bottom:1px solid #000">&nbsp;</th>
                            <th style="border-bottom:1px solid #000; text-align:right; width:20%">Budgeted</th>
                            <th style="border-bottom:1px solid #000; text-align:right; width:20%">Actual</th>
                        </tr>
                        <tr>
                            <td style="text-align:right"><?php echo $CI->lang->line('LABEL_TOTAL_MARKET_SIZE'); ?> :</td>
                            <td style="text-align:right"><?php echo System_helper::get_string_amount($item['quantity_market_size_showroom_total']); ?></td>
                            <td style="text-align:right"><?php echo (isset($old_reporting['reporting_quantity_market_size_showroom_total'])) ? System_helper::get_string_amount($old_reporting['reporting_quantity_market_size_showroom_total']) : '-'; ?></td>
                        </tr>
                        <tr>
                            <td style="text-align:right"><?php echo $CI->lang->line('LABEL_TOTAL_GA_MARKET_SIZE'); ?> :</td>
                            <td style="text-align:right"><?php echo System_helper::get_string_amount($item['quantity_market_size_ga_total']); ?></td>
                            <td style="text-align:right"><?php echo (isset($old_reporting['reporting_quantity_market_size_ga_total'])) ? System_helper::get_string_amount($old_reporting['reporting_quantity_market_size_ga_total']) : '-'; ?></td>
                        </tr>
                        <tr>
                            <td style="text-align:right"><?php echo $CI->lang->line('LABEL_ARM_MARKET_SIZE'); ?> :</td>
                            <td style="text-align:right"><?php echo System_helper::get_string_amount($item['quantity_market_size_showroom_arm']); ?></td>
                            <td style="text-align:right"><?php echo (isset($old_reporting['reporting_quantity_market_size_showroom_arm'])) ? System_helper::get_string_amount($old_reporting['reporting_quantity_market_size_showroom_arm']) : '-'; ?></td>
                        </tr>
                        <tr>
                            <td style="text-align:right"><?php echo $CI->lang->line('LABEL_ARM_GA_MARKET_SIZE'); ?> :</td>
                            <td style="text-align:right"><?php echo System_helper::get_string_amount($item['quantity_market_size_ga_arm']); ?></td>
                            <td style="text-align:right"><?php echo (isset($old_reporting['reporting_quantity_market_size_ga_arm'])) ? System_helper::get_string_amount($old_reporting['reporting_quantity_market_size_ga_arm']) : '-'; ?></td>
                        </tr>
                        <tr>
                            <td style="text-align:right"><?php echo $CI->lang->line('LABEL_NEXT_SALES_TARGET'); ?> :</td>
                            <td style="text-align:right"><?php echo System_helper::get_string_amount($item['quantity_sales_target']); ?></td>
                            <td style="text-align:right"><?php echo (isset($old_reporting['reporting_quantity_sales_target'])) ? System_helper::get_string_amount($old_reporting['reporting_quantity_sales_target']) : '-'; ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

<?php
}
if ($show_image_info)
{
    ?>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <label><a class="external text-danger" data-toggle="collapse" data-target="#collapse3" href="#"> + Field Day Image</a></label>
            </h4>
        </div>
        <div id="collapse3" class="panel-collapse collapse out">

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
                                        <td rowspan="2"><?php echo $picture_category['text']; ?></td>

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

<?php } ?>
</div>

<script type="application/javascript">
    jQuery(document).ready(function ($) {
        system_off_events(); // Triggers
    });
</script>
