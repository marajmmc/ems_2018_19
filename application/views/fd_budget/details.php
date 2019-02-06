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
$show_basic_info        = (isset($show_basic_info))? $show_basic_info : TRUE;
$show_participant_info  = (isset($show_participant_info))? $show_participant_info : TRUE;
$show_expense_info      = (isset($show_expense_info))? $show_expense_info : TRUE;
$show_image_info        = (isset($show_image_info))? $show_image_info : TRUE;

?>
<style>
    .widget-header{background-image: none}
    .bottom-summary > div{padding:20px 0 5px; background:#E8E8E8; text-align:center}
    .participant-wrap > div{font-size:1.3em; padding:10px; background:#E8E8E8}
</style>

<div class="row widget">
    <div class="widget-header" style="margin-bottom:20px">
        <div class="title"><?php echo $title; ?></div>
        <div class="clearfix"></div>
    </div>

    <?php
    if($show_basic_info)
    {
    ?>

    <?php echo $CI->load->view("info_basic", '', true); ?>

    <?php
    }
//    echo '<pre>';
//    print_r($old_reporting);
//    print_r($dealers);
//    print_r($lead_farmers);
//    print_r($expense_items);
//    echo '</pre>';
    if($show_participant_info)
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
                <div class="col-xs-6"><?php echo $CI->lang->line('LABEL_PARTICIPANT_THROUGH_DEALER'); ?></div>
                <div class="col-xs-6" style="border-left:1px solid #cfcfcf"><?php echo $CI->lang->line('LABEL_PARTICIPANT_THROUGH_LEAD_FARMER'); ?></div>
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
                        $init_ga_id = -1;
                        foreach ($dealers as &$dealer)
                        {
                            $dealer['participant'] = (isset($dealer['participant'])) ? $dealer['participant'] : 0;
                            if($init_ga_id != $dealer['ga_id']){
                                ?>
                                <tr>
                                    <td style="text-align:right">
                                        <label style="font-style:italic; text-decoration:underline; padding:5px;" class="control-label pull-right"><?php echo $dealer['ga_name']; ?>:</label>
                                    </td>
                                    <td style="text-align:right; width:35%;">&nbsp;</td>
                                    <td colspan="2">&nbsp;</td>
                                </tr>
                                <?php
                                $init_ga_id=$dealer['ga_id'];
                            }
                            ?>
                            <tr>
                                <td style="text-align:right" colspan="2"><?php echo $dealer['dealer_name'] . ' ( ' . $dealer['phone_no'] . ' )'; ?> :</td>
                                <td style="text-align:right; padding:5px"><?php echo $dealer['participant']; ?></td>
                                <td style="text-align:right; padding:5px"><?php echo (isset($old_reporting['reporting_participants_dealer'][$dealer['dealer_id']]))? $old_reporting['reporting_participants_dealer'][$dealer['dealer_id']] : "-"; ?></td>
                            </tr>
                            <?php
                            $total_participant += $dealer['participant'];
                            $sub_total_dealer += $dealer['participant'];
                        }
                        ?>
                        <tr>
                            <td style="text-align:right; font-weight:bold" colspan="2">Sub Total :</td>
                            <td style="text-align:right; padding:5px; font-weight:bold"><?php echo $sub_total_dealer; ?></td>
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
                        $init_ga_id = -1;
                        foreach ($lead_farmers as &$farmer)
                        {
                            $farmer['participant'] = (isset($farmer['participant'])) ? $farmer['participant'] : 0;
                            if($init_ga_id != $farmer['ga_id']){
                                ?>
                                <tr>
                                    <td style="text-align:right">
                                        <label style="font-style:italic; text-decoration:underline; padding:5px;" class="control-label pull-right"><?php echo $farmer['ga_name']; ?>:</label>
                                    </td>
                                    <td style="text-align:right; width:35%;">&nbsp;</td>
                                    <td colspan="2">&nbsp;</td>
                                </tr>
                                <?php
                                $init_ga_id=$farmer['ga_id'];
                            }
                            ?>
                            <tr>
                                <td style="text-align:right" colspan="2"><?php echo $farmer['lead_farmers_name'] . ' ( ' . $farmer['phone_no'] . ' )'; ?> :</td>
                                <td style="text-align:right; padding:5px"><?php echo $farmer['participant']; ?></td>
                                <td style="text-align:right; padding:5px"><?php echo (isset($old_reporting['reporting_participants_farmer'][$farmer['lead_farmers_id']]))? $old_reporting['reporting_participants_farmer'][$farmer['lead_farmers_id']] : "-"; ?></td>
                            </tr>
                            <?php
                            $total_participant += $farmer['participant'];
                            $sub_total_farmer += $farmer['participant'];
                        }
                        ?>
                        <tr>
                            <td style="text-align:right; font-weight:bold" colspan="2">Sub Total :</td>
                            <td style="text-align:right; padding:5px; font-weight:bold"><?php echo $sub_total_farmer; ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="row show-grid bottom-summary">
                <div class="col-xs-3">
                    <label class="control-label">
                        <?php echo $CI->lang->line('LABEL_PARTICIPANT_THROUGH_DEALER'); ?> : <?php echo $sub_total_dealer; ?>
                    </label>
                </div>
                <div class="col-xs-3">
                    <label class="control-label">
                        <?php echo $CI->lang->line('LABEL_PARTICIPANT_THROUGH_LEAD_FARMER'); ?> : <?php echo $sub_total_farmer; ?>
                    </label>
                </div>
                <div class="col-xs-3">
                    <label class="control-label">
                        <?php echo $CI->lang->line('LABEL_PARTICIPANT_THROUGH_OTHERS'); ?> : <?php echo $item['participant_others'];  $total_participant += $item['participant_others']; ?>
                    </label>
                </div>
                <div class="col-xs-3">
                    <label class="control-label">
                        <?php echo $CI->lang->line('LABEL_TOTAL_PARTICIPANT'). " : ({$sub_total_dealer} + {$sub_total_farmer} + {$item['participant_others']})"?> = <?php echo $total_participant; ?>
                    </label>
                </div>
            </div>

        </div>
    </div>

    <?php
    }
    if($show_expense_info)
    {
    ?>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <label><a class="external text-danger" data-toggle="collapse" data-target="#collapse2" href="#"> + Budget Expense &amp; Payment Summary</a></label>
            </h4>
        </div>
        <div id="collapse2" class="panel-collapse collapse out">
            <table class="table table-bordered table-responsive system_table_details_view">
                <tr>
                    <td class="widget-header header_caption">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_STATUS_PAYMENT_APPROVE'); ?></label>
                    </td>
                    <td  style="width:120px">
                        <label class="control-label"><?php echo $item['status_payment_approve']; ?></label>
                    </td>
                    <td class="widget-header header_caption" style="width:100px">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_REMARKS'); ?></label>
                    </td>
                    <td>
                        <label class="control-label" style="font-weight:normal"><?php echo ($item['remarks_payment_approve'])? nl2br($item['remarks_payment_approve']):'-'; ?></label>
                    </td>
                    <td rowspan="4" style="width:35%">
                        <div class="row show-grid">
                            <div class="col-xs-12" style="padding:0">
                                <table style="width:100%">
                                    <tr>
                                        <td>&nbsp;</td>
                                        <th style="text-align:right; width:20%">Budgeted</th>
                                        <th style="text-align:right; width:20%">Actual</th>
                                    </tr>
                                    <?php
                                    $total_budget = 0;
                                    foreach ($expense_items as $expense)
                                    {
                                        ?>
                                        <tr>
                                            <td style="text-align:right"><?php echo $expense['name']; ?> :</td>
                                            <td style="text-align:right"><?php echo System_helper::get_string_amount($expense['amount']); ?></td>
                                            <td style="text-align:right"><?php echo (isset($old_reporting['reporting_amount_expense_items'][$expense['id']]))? System_helper::get_string_amount($old_reporting['reporting_amount_expense_items'][$expense['id']]) : "-"; ?></td>
                                        </tr>
                                        <?php
                                        $total_budget += $expense['amount'];
                                    }
                                    ?>
                                    <tr>
                                        <td style="border-top:1px solid #000; text-align:right; font-weight:bold"><?php echo $CI->lang->line('LABEL_TOTAL_FIELD_DAY_BUDGET'); ?> :</td>
                                        <td style="border-top:1px solid #000; text-align:right; font-weight:bold"><?php echo System_helper::get_string_amount($total_budget); ?></td>
                                        <td style="border-top:1px solid #000; text-align:right; font-weight:bold">&nbsp;</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="widget-header header_caption">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_STATUS_PAYMENT_PAY'); ?></label>
                    </td>
                    <td>
                        <label class="control-label"><?php echo $item['status_payment_pay']; ?></label>
                    </td>
                    <td class="widget-header header_caption" style="width:100px">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_REMARKS'); ?></label>
                    </td>
                    <td>
                        <label class="control-label" style="font-weight:normal"><?php echo ($item['remarks_payment_pay'])? nl2br($item['remarks_payment_pay']):'-'; ?></label>
                    </td>
                </tr>
                <tr>
                    <td class="widget-header header_caption">
                        <label class="control-label pull-right">Adjustment Forward Status</label></td>
                    <td>
                        <label class="control-label">Pending</label>
                    </td>
                    <td class="widget-header header_caption" style="width:100px">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_REMARKS'); ?></label>
                    </td>
                    <td>
                        <label class="control-label" style="font-weight:normal">-</label>
                    </td>
                </tr>
                <tr>
                    <td class="widget-header header_caption">
                        <label class="control-label pull-right">Adjustment Approval Status</label></td>
                    <td>
                        <label class="control-label">Pending</label>
                    </td>
                    <td class="widget-header header_caption" style="width:100px">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_REMARKS'); ?></label>
                    </td>
                    <td>
                        <label class="control-label" style="font-weight:normal">-</label>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <?php
    }
    if($show_image_info)
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

        /* $(document).on('click', 'a.external', function(){
            var element_id = '#'+$(this).closest('.panel-default').children('.panel-collapse').attr('id');
            //alert(element_id);
            if($(element_id).hasClass('in'))
            {
                var height = $(element_id+" #dealer-wrap").height();
                $("#farmer-wrap").css("max-height", height+"px");
            }
        }); */
    });
</script>
