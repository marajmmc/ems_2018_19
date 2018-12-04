<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();

$count = sizeof($items);
if ($count > 0)
{
    ?>
    <div class="row widget">

        <div class="widget-header">
            <div class="title">Edit History</div>
            <div class="clearfix"></div>
        </div>

        <div class="row show-grid">

            <div class="col-xs-12">
                <div class="panel panel-default">

                <?php
                $i = 1;
                foreach ($items as $key => $item)
                {
                    if ($item['user_rollback_zi'] > 0)
                    {
                        $roll_back = true;
                        $roll_back_title = ' - <i>Rollback</i>';
                        $roll_back_date = $item['date_rollback_zi'];
                        $roll_back_user = $item['user_rollback_zi'];
                        $rolled_back_from = 'Recommendation';
                        $roll_back_reason = $item['remarks_rollback_zi'];
                    }
                    elseif ($item['user_rollback_di'] > 0)
                    {
                        $roll_back = true;
                        $roll_back_title = ' - <i>Rollback</i>';
                        $roll_back_date = $item['date_rollback_di'];
                        $roll_back_user = $item['user_rollback_di'];
                        $rolled_back_from = 'Approval';
                        $roll_back_reason = $item['remarks_rollback_di'];
                    }
                    else
                    {
                        $roll_back = false;
                        $roll_back_title = '';
                    }
                    ?>
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <label>
                                <a class="external text-danger" data-toggle="collapse" data-target="#collapse<?php echo $key; ?>" href="#"> + Revision:
                                    <?php
                                    echo ($count - $item['revision'] + 1) . ' (' . $item['revision'] . ')';
                                    echo $roll_back_title;
                                    echo ($i == 1) ? " - LATEST" : "";
                                    ?>
                                </a>
                            </label>
                        </h4>
                    </div>

                    <div id="collapse<?php echo $key; ?>" class="panel-collapse collapse">
                        <div class="row widget" style="margin:0; padding:20px 0; border:none">

                            <div class="row show-grid">
                                <div class="col-xs-4">
                                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CREATED_BY'); ?> :</label>
                                </div>
                                <div class="col-xs-8">
                                    <label class="control-label">
                                        <?php echo $item['user_info'][$item['user_created']]['name'] . ' ( ID: ' . $item['user_info'][$item['user_created']]['employee_id'] . ' )'; ?>
                                    </label>
                                </div>
                            </div>

                            <div class="row show-grid">
                                <div class="col-xs-4">
                                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CREATED_TIME'); ?> :</label>
                                </div>
                                <div class="col-xs-8">
                                    <label class="control-label"><?php echo System_helper::display_date_time($item['date_created']); ?></label>
                                </div>
                            </div>

                            <?php if($roll_back){ ?>

                                <div class="row show-grid">
                                    <div class="col-xs-4">
                                        <label class="control-label pull-right text-danger"><?php echo $CI->lang->line('LABEL_ROLLBACK_BY'); ?> :</label>
                                    </div>
                                    <div class="col-xs-8">
                                        <label class="control-label text-danger">
                                            <?php echo $item['user_info'][$roll_back_user]['name'] . ' ( ID: ' . $item['user_info'][$roll_back_user]['employee_id'] . ' )'; ?>
                                        </label>
                                    </div>
                                </div>

                                <div class="row show-grid">
                                    <div class="col-xs-4">
                                        <label class="control-label pull-right text-danger"><?php echo $CI->lang->line('LABEL_DATE_ROLLBACK_TIME'); ?> :</label>
                                    </div>
                                    <div class="col-xs-8">
                                        <label class="control-label text-danger">
                                            <?php
                                            echo System_helper::display_date_time($roll_back_date);
                                            echo '<span style="font-weight:normal"> ( Rollback from '.$rolled_back_from.' )</span>';
                                            ?>
                                        </label>
                                    </div>
                                </div>

                                <div class="row show-grid">
                                    <div class="col-xs-4">
                                        <label class="control-label pull-right text-danger">Rollback <?php echo $CI->lang->line('LABEL_REMARKS'); ?> :</label>
                                    </div>
                                    <div class="col-xs-8">
                                        <label class="control-label text-danger" style="font-weight:normal"><?php echo nl2br($roll_back_reason); ?></label>
                                    </div>
                                </div>

                            <?php } ?>

                            <div class="row show-grid">
                                <div class="col-xs-4">
                                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_PROPOSAL'); ?> :</label>
                                </div>
                                <div class="col-xs-8">
                                    <label class="control-label"><?php echo System_helper::display_date($item['date_proposal']); ?></label>
                                </div>
                            </div>

                            <div class="row show-grid">
                                <div class="col-xs-4">
                                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_EXPECTED'); ?> :</label>
                                </div>
                                <div class="col-xs-8">
                                    <label class="control-label"><?php echo System_helper::display_date($item['date_expected']); ?></label>
                                </div>
                            </div>

                            <div class="row show-grid">
                                <div class="col-xs-4">
                                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_NAME'); ?> :</label>
                                </div>
                                <div class="col-xs-8">
                                    <label class="control-label"><?php echo $item['crop_name']; ?></label>
                                </div>
                            </div>

                            <div class="row show-grid">
                                <div class="col-xs-4">
                                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_TYPE'); ?> :</label>
                                </div>
                                <div class="col-xs-8">
                                    <label class="control-label"><?php echo $item['crop_type_name']; ?></label>
                                </div>
                            </div>

                            <div class="row show-grid">
                                <div class="col-xs-4">
                                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_VARIETY1_NAME'); ?> :</label>
                                </div>
                                <div class="col-xs-8">
                                    <label class="control-label"><?php echo $item['variety1_name']; ?></label>
                                </div>
                            </div>

                            <div class="row show-grid">
                                <div class="col-xs-4">
                                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_VARIETY2_NAME'); ?> :</label>
                                </div>
                                <div class="col-xs-8">
                                    <label class="control-label"><?php echo ($item['variety2_name']) ? $item['variety2_name'] : '<i style="font-weight:normal">- No Variety Selected -</i>'; ?>
                                </div>
                            </div>

                            <div class="row show-grid">
                                <div class="col-xs-4">
                                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PRESENT_CONDITION'); ?> :</label>
                                </div>
                                <div class="col-xs-8">
                                    <label class="control-label" style="font-weight:normal"><?php echo nl2br($item['present_condition']); ?></label>
                                </div>
                            </div>

                            <div class="row show-grid">
                                <div class="col-xs-4">
                                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DEALERS_EVALUATION'); ?> :</label>
                                </div>
                                <div class="col-xs-8">
                                    <label class="control-label" style="font-weight:normal"><?php echo nl2br($item['farmers_evaluation']); ?></label>
                                </div>
                            </div>

                            <div class="row show-grid">
                                <div class="col-xs-4">
                                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DIVISION_NAME'); ?> :</label>
                                </div>
                                <div class="col-xs-8">
                                    <label class="control-label"><?php echo $item['division_name']; ?></label>
                                </div>
                            </div>

                            <div class="row show-grid">
                                <div class="col-xs-4">
                                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ZONE_NAME'); ?> :</label>
                                </div>
                                <div class="col-xs-8">
                                    <label class="control-label"><?php echo $item['zone_name']; ?></label>
                                </div>
                            </div>

                            <div class="row show-grid">
                                <div class="col-xs-4">
                                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_TERRITORY_NAME'); ?> :</label>
                                </div>
                                <div class="col-xs-8">
                                    <label class="control-label"><?php echo $item['territory_name']; ?></label>
                                </div>
                            </div>

                            <div class="row show-grid">
                                <div class="col-xs-4">
                                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DISTRICT_NAME'); ?> :</label>
                                </div>
                                <div class="col-xs-8">
                                    <label class="control-label"><?php echo $item['district_name']; ?></label>
                                </div>
                            </div>

                            <div class="row show-grid">
                                <div class="col-xs-4">
                                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_OUTLET_NAME'); ?> :</label>
                                </div>
                                <div class="col-xs-8">
                                    <label class="control-label"><?php echo $item['outlet_name']; ?></label>
                                </div>
                            </div>

                            <div class="row show-grid">
                                <div class="col-xs-4">
                                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_GROWING_AREA'); ?> :</label>
                                </div>
                                <div class="col-xs-8">
                                    <label class="control-label"><?php echo ($item['growing_area_name']) ? $item['growing_area_name'] : '<i style="font-weight:normal">- No Growing Area Selected -</i>'; ?>
                                </div>
                            </div>

                            <div class="row show-grid">
                                <div class="col-xs-4">
                                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ADDRESS'); ?> :</label>
                                </div>
                                <div class="col-xs-8">
                                    <label class="control-label" style="font-weight:normal"><?php echo nl2br($item['address']); ?></label>
                                </div>
                            </div>

                            <div class="row show-grid">
                                <div class="row show-grid">
                                    <div class="col-xs-4">
                                        <label style="font-size:1.2em" class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PARTICIPANT_THROUGH_DEALER'); ?> :</label>
                                    </div>
                                </div>
                                <div class="row show-grid">
                                    <div class="col-xs-7">
                                        <table style="width:100%">
                                            <?php
                                            $sub_total_participant = $total_participant = 0;
                                            $init_ga_id = -1;
                                            $index = 0;
                                            foreach ($item['dealers'] as $dealer)
                                            {
                                                $participant = ($dealer['participant'] > 0) ? '<b>' . $dealer['participant'] . '</b>' : 0;
                                                if ($init_ga_id != $dealer['ga_id'])
                                                {
                                                    ?>
                                                    <tr>
                                                        <td style="text-align:right">
                                                            <label style="font-style:italic; text-decoration:underline;  padding:5px; font-size:1.1em" class="control-label pull-right"><?php echo $dealer['ga_name']; ?>:</label>
                                                        </td>
                                                        <td style="text-align:right; width:35%;">&nbsp;</td>
                                                        <td style="text-align:right; width:5%; padding:5px">&nbsp;</td>
                                                    </tr>
                                                    <?php
                                                    $init_ga_id = $dealer['ga_id'];
                                                    $index++;
                                                }
                                                ?>
                                                <tr>
                                                    <td style="text-align:right" colspan="2"><?php echo $dealer['dealer_name'] . ' ( ' . $dealer['phone_no'] . ' )'; ?> :</td>
                                                    <td style="text-align:right; padding:5px"><?php echo $participant; ?></td>
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
                                            $index = 0;
                                            foreach ($item['lead_farmers'] as $farmer)
                                            {
                                                $participant = ($farmer['participant'] > 0) ? '<b>' . $farmer['participant'] . '</b>' : 0;
                                                if ($init_ga_id != $farmer['ga_id'])
                                                {
                                                    ?>
                                                    <tr>
                                                        <td style="text-align:right">
                                                            <label style="font-style:italic; text-decoration:underline;  padding:5px; font-size:1.1em" class="control-label pull-right"><?php echo $farmer['ga_name']; ?>:</label>
                                                        </td>
                                                        <td style="text-align:right; width:35%;">&nbsp;</td>
                                                        <td style="text-align:right; width:5%; padding:5px">&nbsp;</td>
                                                    </tr>
                                                    <?php
                                                    $init_ga_id = $farmer['ga_id'];
                                                    $index++;
                                                }
                                                ?>
                                                <tr>
                                                    <td style="text-align:right" colspan="2"><?php echo $farmer['lead_farmers_name'] . ' ( ' . $farmer['phone_no'] . ' )'; ?> :</td>
                                                    <td style="text-align:right; padding:5px"><?php echo $participant; ?></td>
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
                                <div class="col-xs-8">
                                    <label class="control-label"><?php echo $item['participant_others']; ?></label>
                                    <?php $total_participant += $item['participant_others']; ?>
                                </div>
                            </div>

                            <div class="row show-grid">
                                <div class="col-xs-4">
                                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_TOTAL_PARTICIPANT'); ?> :</label>
                                </div>
                                <div class="col-xs-8">
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
                                            foreach ($item['expense_items'] as $expense)
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
                                <div class="col-xs-8">
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
                                <div class="col-xs-8">
                                    <label class="control-label"><?php echo System_helper::get_string_kg($item['quantity_market_size_showroom_total']); ?></label>
                                </div>
                            </div>

                            <div class="row show-grid">
                                <div class="col-xs-4">
                                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_TOTAL_GA_MARKET_SIZE'); ?> :</label>
                                </div>
                                <div class="col-xs-8">
                                    <label class="control-label"><?php echo System_helper::get_string_kg($item['quantity_market_size_ga_total']); ?></label>
                                </div>
                            </div>

                            <div class="row show-grid">
                                <div class="col-xs-4">
                                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ARM_MARKET_SIZE'); ?> :</label>
                                </div>
                                <div class="col-xs-8">
                                    <label class="control-label"><?php echo System_helper::get_string_kg($item['quantity_market_size_showroom_arm']); ?></label>
                                </div>
                            </div>

                            <div class="row show-grid">
                                <div class="col-xs-4">
                                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ARM_GA_MARKET_SIZE'); ?> :</label>
                                </div>
                                <div class="col-xs-8">
                                    <label class="control-label"><?php echo System_helper::get_string_kg($item['quantity_market_size_ga_arm']); ?></label>
                                </div>
                            </div>

                            <div class="row show-grid">
                                <div class="col-xs-4">
                                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_NEXT_SALES_TARGET'); ?> :</label>
                                </div>
                                <div class="col-xs-8">
                                    <label class="control-label"><?php echo System_helper::get_string_kg($item['quantity_sales_target']); ?></label>
                                </div>
                            </div>

                        </div>
                    </div>

                    <?php
                    $i++;
                }
                ?>

                </div>
            </div>

            <div class="clearfix"></div>

        </div>

    </div>
<?php } ?>
