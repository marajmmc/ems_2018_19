<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();

$action_buttons = array();
$action_buttons[] = array
(
    'label' => $CI->lang->line("ACTION_BACK"),
    'href' => site_url($CI->controller_url)
);
if ((isset($CI->permissions['action1']) && ($CI->permissions['action1'] == 1)) || (isset($CI->permissions['action2']) && ($CI->permissions['action2'] == 1)))
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

$total_participant = 0;
?>
<style>label {
        margin-top: 5px;
    }</style>
<form class="form_valid" id="save_form" action="<?php echo site_url($CI->controller_url . '/index/save'); ?>" method="post">

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
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_PROPOSAL'); ?>
                    <span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <?php if ($item['id'] > 0)
                {
                    ?>
                    <label class="control-label"><?php echo System_helper::display_date($item['date_proposal']); ?></label>
                <?php
                }
                else
                {
                    ?>
                    <input type="text" name="item[date_proposal]" id="date_proposal" class="form-control datepicker" value="<?php echo System_helper::display_date($item['date_proposal']); ?>" readonly/>
                <?php } ?>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_EXPECTED'); ?>
                    <span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="item_info[date_expected]" id="date_expected" class="form-control datepicker" value="<?php echo System_helper::display_date($item_info['date_expected']); ?>" readonly/>
            </div>
        </div>

        <div class="row show-grid" id="crop_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_NAME'); ?>
                    <span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="crop_id" class="form-control">
                    <option value=""><?php echo $CI->lang->line('SELECT'); ?></option>
                    <?php
                    if ($crops)
                    {
                        foreach ($crops as $crop)
                        {
                            ?>
                            <option value="<?php echo $crop['value'] ?>" <?php echo ($crop['value'] == $item_info['crop_id']) ? "selected" : ""; ?>><?php echo $crop['text']; ?></option>
                        <?php
                        }
                    }
                    ?>
                </select>
            </div>
        </div>

        <div style="<?php echo (!($item['id'] > 0)) ? 'display:none' : ''; ?>" class="row show-grid" id="crop_type_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_TYPE'); ?>
                    <span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="crop_type_id" class="form-control">
                    <option value=""><?php echo $CI->lang->line('SELECT'); ?></option>
                    <?php
                    if ($crop_types)
                    {
                        foreach ($crop_types as $type)
                        {
                            ?>
                            <option value="<?php echo $type['value'] ?>" <?php echo ($type['value'] == $item_info['crop_type_id']) ? "selected" : ""; ?>><?php echo $type['text']; ?></option>
                        <?php
                        }
                    }
                    ?>
                </select>
            </div>
        </div>

        <div style="<?php echo (!($item['id'] > 0)) ? 'display:none' : ''; ?>" class="row show-grid" id="variety1_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_VARIETY1_NAME'); ?>
                    <span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="variety1_id" name="item_info[variety1_id]" class="form-control">
                    <option value=""><?php echo $CI->lang->line('SELECT'); ?></option>
                    <?php
                    if ($crop_varieties1)
                    {
                        foreach ($crop_varieties1 as $variety1)
                        {
                            ?>
                            <option value="<?php echo $variety1['value'] ?>" <?php echo ($variety1['value'] == $item_info['variety1_id']) ? "selected" : ""; ?>><?php echo $variety1['text']; ?></option>
                        <?php
                        }
                    }
                    ?>
                </select>
            </div>
        </div>

        <div style="<?php echo (!($item['id'] > 0)) ? 'display:none' : ''; ?>" class="row show-grid" id="variety2_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_VARIETY2_NAME'); ?> &nbsp;</label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="variety2_id" name="item_info[variety2_id]" class="form-control">
                    <option value=""><?php echo $CI->lang->line('SELECT'); ?></option>
                    <?php
                    if ($crop_varieties2)
                    {
                        foreach ($crop_varieties2 as $variety2)
                        {
                            ?>
                            <option value="<?php echo $variety2['value'] ?>" <?php echo ($variety2['value'] == $item_info['variety2_id']) ? "selected" : ""; ?>><?php echo $variety2['text']; ?></option>
                        <?php
                        }
                    }
                    ?>
                </select>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PRESENT_CONDITION'); ?>
                    <span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <textarea class="form-control" id="present_condition" name="item_info[present_condition]"><?php echo $item_info['present_condition']; ?></textarea>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DEALERS_EVALUATION'); ?>
                    <span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <textarea class="form-control" id="farmers_evaluation" name="item_info[farmers_evaluation]"><?php echo $item_info['farmers_evaluation']; ?></textarea>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DIVISION_NAME'); ?>
                    <span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <?php
                if ($item['id'] > 0)
                {
                    ?>
                    <label class="control-label"><?php echo $item_info['division_name']; ?></label>
                <?php
                }
                else if ($CI->locations['division_id'] > 0)
                {
                    ?>
                    <label class="control-label"><?php echo $CI->locations['division_name']; ?></label>
                <?php
                }
                else
                {
                    ?>
                    <select id="division_id" class="form-control">
                        <option value=""><?php echo $CI->lang->line('SELECT'); ?></option>
                        <?php
                        foreach ($divisions as $division)
                        {
                            ?>
                            <option value="<?php echo $division['value'] ?>"><?php echo $division['text']; ?></option>
                        <?php
                        }
                        ?>
                    </select>
                <?php
                }
                ?>
            </div>
        </div>

        <div style="<?php echo (!($item['id'] > 0) && !($CI->locations['division_id'] > 0)) ? 'display:none' : '' ?>" class="row show-grid" id="zone_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ZONE_NAME'); ?>
                    <span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <?php
                if ($item['id'] > 0)
                {
                    ?>
                    <label class="control-label"><?php echo $item_info['zone_name']; ?></label>
                <?php
                }
                else if ($CI->locations['zone_id'] > 0)
                {
                    ?>
                    <label class="control-label"><?php echo $CI->locations['zone_name']; ?></label>
                <?php
                }
                else
                {
                    ?>
                    <select id="zone_id" class="form-control">
                        <option value=""><?php echo $CI->lang->line('SELECT'); ?></option>
                        <?php
                        foreach ($zones as $zone)
                        {
                            ?>
                            <option value="<?php echo $zone['value'] ?>"><?php echo $zone['text']; ?></option>
                        <?php
                        }
                        ?>
                    </select>
                <?php
                }
                ?>
            </div>
        </div>

        <div style="<?php echo (!($item['id'] > 0) && !($CI->locations['zone_id'] > 0)) ? 'display:none' : '' ?>" class="row show-grid" id="territory_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_TERRITORY_NAME'); ?>
                    <span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <?php
                if ($item['id'] > 0)
                {
                    ?>
                    <label class="control-label"><?php echo $item_info['territory_name']; ?></label>
                <?php
                }
                else if ($CI->locations['territory_id'] > 0)
                {
                    ?>
                    <label class="control-label"><?php echo $CI->locations['territory_name']; ?></label>
                <?php
                }
                else
                {
                    ?>
                    <select id="territory_id" class="form-control">
                        <option value=""><?php echo $CI->lang->line('SELECT'); ?></option>
                        <?php
                        foreach ($territories as $territory)
                        {
                            ?>
                            <option value="<?php echo $territory['value'] ?>"><?php echo $territory['text']; ?></option>
                        <?php
                        }
                        ?>
                    </select>
                <?php
                }
                ?>
            </div>
        </div>

        <div style="<?php echo (!($item['id'] > 0) && !($CI->locations['territory_id'] > 0)) ? 'display:none' : '' ?>" class="row show-grid" id="district_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DISTRICT_NAME'); ?>
                    <span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <?php
                if ($item['id'] > 0)
                {
                    ?>
                    <label class="control-label"><?php echo $item_info['district_name']; ?></label>
                <?php
                }
                elseif ($CI->locations['district_id'] > 0)
                {
                    ?>
                    <label class="control-label"><?php echo $CI->locations['district_name']; ?></label>
                <?php
                }
                else
                {
                    ?>
                    <select id="district_id" class="form-control">
                        <option value=""><?php echo $CI->lang->line('SELECT'); ?></option>
                        <?php
                        foreach ($districts as $district)
                        {
                            ?>
                            <option value="<?php echo $district['value'] ?>"><?php echo $district['text']; ?></option>
                        <?php
                        }
                        ?>
                    </select>
                <?php
                }
                ?>
            </div>
        </div>

        <div style="<?php echo (!($item['id'] > 0) && !($CI->locations['district_id'] > 0)) ? 'display:none' : '' ?>" class="row show-grid" id="outlet_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_OUTLET_NAME'); ?>
                    <span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <?php
                if ($item['id'] > 0)
                {
                    ?>
                    <label class="control-label"><?php echo $item_info['outlet_name']; ?></label>
                <?php
                }
                else
                {
                    ?>
                    <select id="outlet_id" name="item_info[outlet_id]" class="form-control">
                        <option value=""><?php echo $CI->lang->line('SELECT'); ?></option>
                        <?php
                        foreach ($outlets as $outlet)
                        {
                            ?>
                            <option value="<?php echo $outlet['value'] ?>"><?php echo $outlet['text']; ?></option>
                        <?php
                        }
                        ?>
                    </select>
                <?php
                }
                ?>
            </div>
        </div>


        <div style="<?php echo (!($item['id'] > 0)) ? 'display:none' : ''; ?>" class="row show-grid" id="growing_area_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right">Growing Area</label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="growing_area_id" class="form-control">
                    <option value=""><?php echo $CI->lang->line('SELECT'); ?></option>
                    <?php
                    if($growing_area){
                        foreach ($growing_area as $area)
                        {
                            ?>
                            <option value="<?php echo $area['value'] ?>"><?php echo $area['text']; ?></option>
                        <?php
                        }
                    }
                    ?>
                </select>
            </div>
        </div>

<?php /*

        <div style="<?php echo (!($item['id'] > 0) && !($CI->locations['district_id'] > 0)) ? 'display:none' : '' ?>" class="row show-grid" id="outlet_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_OUTLET_NAME'); ?>
                    <span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <?php
                if ($item['id'] > 0)
                {
                    ?>
                    <label class="control-label"><?php echo $item_info['outlet_name']; ?></label>
                <?php
                }
                else
                {
                    ?>
                    <select id="outlet_id" name="item_info[outlet_id]" class="form-control">
                        <option value=""><?php echo $CI->lang->line('SELECT'); ?></option>
                        <?php
                        foreach ($outlets as $outlet)
                        {
                            ?>
                            <option value="<?php echo $outlet['value'] ?>"><?php echo $outlet['text']; ?></option>
                        <?php
                        }
                        ?>
                    </select>
                <?php
                }
                ?>
            </div>
        </div> */ ?>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ADDRESS'); ?>
                    <span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <textarea class="form-control" id="address" name="item_info[address]"><?php echo $item_info['address']; ?></textarea>
            </div>
        </div>

        <div style="<?php echo (!(sizeof($dealers) > 0)) ? 'display:none;' : ''; ?>" class="row show-grid" id="dealer_container">

            <div id="dealer_id" class="row show-grid">
                <div class="row show-grid">
                    <div class="col-xs-4">
                        <label style="font-size:1.3em" class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PARTICIPANT_THROUGH_DEALER'); ?> : </label>
                    </div>
                </div>

                <?php
                $init_ga_id = -1;
                $index=0;
                foreach ($dealers as $dealer)
                {
                    $value = 0;
                    if (isset($participants[$dealer['value']]))
                    {
                        $total_participant += $participants[$dealer['value']];
                        $value = (int)$participants[$dealer['value']];
                    }

                    if($init_ga_id != $dealer['ga_id']){
                        echo ($index > 0)? '<hr/>':'';
                        ?>
                        <div class="row show-grid">
                            <div class="col-xs-4">
                                <label style="font-style:italic;text-decoration:underline; font-size:1.1em" class="control-label pull-right"><?php echo $dealer['ga_name']; ?>:</label>
                            </div>
                        </div>
                        <?php
                        $init_ga_id=$dealer['ga_id'];
                        $index++;
                    }
                    ?>
                    <div class="row show-grid">
                        <div class="col-xs-6">
                            <label style="font-weight:normal" class="control-label pull-right"><?php echo $dealer['text'] . ' (' . $dealer['phone_no'] . ')'; ?>
                                <span style="color:#FF0000">*</span></label>
                        </div>
                        <div class="col-xs-2">
                            <input type="text" name="dealer_participant[<?php echo $dealer['value']; ?>]" class="form-control integer_type_positive participant_budget" value="<?php echo $value; ?>"/>
                        </div>
                    </div>
                <?php
                }
                ?>
            </div>
        </div>

        <div style="<?php echo (!(sizeof($leading_farmers) > 0)) ? 'display:none;' : ''; ?>" class="row show-grid" id="leading_farmer_container">

            <div id="leading_farmer_id" class="row show-grid">
                <div class="row show-grid">
                    <div class="col-xs-4">
                        <label style="font-size:1.3em" class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PARTICIPANT_THROUGH_LEAD_FARMER'); ?> : </label>
                    </div>
                </div>

                <?php
                $init_ga_id = -1;
                $index=0;
                foreach ($leading_farmers as $lead_farmer)
                {
                    $value = 0;
                    if (isset($participants[$lead_farmer['value']]))
                    {
                        $total_participant += $participants[$lead_farmer['value']];
                        $value = (int)$participants[$lead_farmer['value']];
                    }

                    if($init_ga_id != $lead_farmer['ga_id']){
                        echo ($index > 0)? '<hr/>':'';
                        ?>
                        <div class="row show-grid">
                            <div class="col-xs-4">
                                <label style="font-style:italic;text-decoration:underline; font-size:1.1em" class="control-label pull-right"><?php echo $lead_farmer['ga_name']; ?>:</label>
                            </div>
                        </div>
                        <?php
                        $init_ga_id=$lead_farmer['ga_id'];
                        $index++;
                    }
                    ?>
                    <div class="row show-grid">
                        <div class="col-xs-6">
                            <label style="font-weight:normal" class="control-label pull-right"><?php echo $lead_farmer['text'] . ' (' . $lead_farmer['phone_no'] . ')'; ?>
                                <span style="color:#FF0000">*</span></label>
                        </div>
                        <div class="col-xs-2">
                            <input type="text" name="farmer_participant[<?php echo $lead_farmer['value']; ?>]" class="form-control integer_type_positive participant_budget" value="<?php echo $value; ?>"/>
                        </div>
                    </div>
                <?php
                }
                ?>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PARTICIPANT_THROUGH_OTHERS'); ?>
                    <span style="color:#FF0000;">*</span></label>
            </div>
            <div class="col-xs-4">
                <?php
                $value = 0;
                if (isset($item_info['participant_others']))
                {
                    $total_participant += $item_info['participant_others'];
                    $value = (int)$item_info['participant_others'];
                }
                ?>
                <input type="text" name="item_info[participant_others]" class="participant_budget form-control integer_type_positive" value="<?php echo $value; ?>"/>
            </div>
        </div>

        <div class="row show-grid" id="total_participant_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_TOTAL_PARTICIPANT'); ?> &nbsp;</label>
            </div>
            <div class="col-xs-4">
                <label id="no_of_participant"><?php echo $total_participant; ?></label>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_FIELD_DAY_BUDGET'); ?>
                    <span style="color:#FF0000">*</span></label>
            </div>

            <div class="col-xs-4">
                <table class="table table-bordered">
                    <?php
                    $total_budget = 0;
                    if (sizeof($expense_items) > 0)
                    {
                        foreach ($expense_items as $expense)
                        {
                            $amount = 0;
                            if (isset($expense_budget[$expense['value']]))
                            {
                                $amount = $expense_budget[$expense['value']];
                                $total_budget += $amount;
                            }

                            if (($expense['status'] == $this->config->item('system_status_inactive')) && !($amount > 0))
                            {
                                continue;
                            }
                            elseif (($expense['status'] == $this->config->item('system_status_inactive')))
                            {
                                $expense['text'] .= ' <b>(' . $this->config->item('system_status_inactive') . ')</b>';
                            }
                            ?>
                            <tr>
                                <td class="right-align" style="width:60%">
                                    <label class="control-label" style="font-weight:normal"><?php echo $expense['text']; ?> :</label>
                                </td>
                                <td>
                                    <input type="text" name="expense_budget[<?php echo $expense['value']; ?>]" class="expense_budget form-control float_type_positive" value="<?php echo $amount; ?>"/>
                                </td>
                            </tr>
                        <?php
                        }
                    }
                    else
                    {
                        ?>
                        <tr>
                            <td colspan="2">- No Data Found -</td>
                        </tr>
                    <?php
                    }
                    ?>
                </table>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_TOTAL_FIELD_DAY_BUDGET'); ?> &nbsp;</label>
            </div>
            <div class="col-xs-4">
                <label id="total_budget" class="amount_iou_label"><?php echo System_helper::get_string_amount($total_budget); ?></label>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_TOTAL_MARKET_SIZE'); ?>
                    <span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="item_info[quantity_market_size_total]" id="quantity_market_size_total" class="form-control float_type_positive" value="<?php echo ($item_info['quantity_market_size_total']) ? $item_info['quantity_market_size_total'] : 0; ?>"/>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_TOTAL_GA_MARKET_SIZE'); ?>
                    <span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="item_info[quantity_market_size_ga_total]" id="quantity_market_size_ga_total" class="form-control float_type_positive" value="<?php echo ($item_info['quantity_market_size_ga_total']) ? $item_info['quantity_market_size_ga_total'] : 0; ?>"/>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ARM_MARKET_SIZE'); ?>
                    <span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="item_info[quantity_market_size_arm]" id="quantity_market_size_arm" class="form-control float_type_positive" value="<?php echo ($item_info['quantity_market_size_arm']) ? $item_info['quantity_market_size_arm'] : 0; ?>"/>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ARM_GA_MARKET_SIZE'); ?>
                    <span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="item_info[quantity_market_size_ga_arm]" id="quantity_market_size_ga_arm" class="form-control float_type_positive" value="<?php echo ($item_info['quantity_market_size_ga_arm']) ? $item_info['quantity_market_size_ga_arm'] : 0; ?>"/>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_NEXT_SALES_TARGET'); ?>
                    <span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="item_info[quantity_sales_target]" id="quantity_sales_target" class="form-control float_type_positive" value="<?php echo ($item_info['quantity_sales_target']) ? $item_info['quantity_sales_target'] : 0; ?>"/>
            </div>
        </div>

        <div class="clearfix"></div>
    </div>

</form>

<!--------Shows Previous Update History, when EDIT Mode-------->
<?php
if(($item['id'] > 0))
{
    echo $CI->load->view($CI->controller_url . "/history", array('items' => $items_history), true);
}
?>
<!-----Shows Previous Update History, when EDIT Mode(END)------>

<script type="text/javascript">
jQuery(document).ready(function ($) {
    system_off_events(); // Triggers
    $(".datepicker").datepicker({dateFormat: display_date_format});

    $(document).off("input", ".expense_budget");
    $(document).off("input", ".participant_budget");

    /*--------------------- CROP RELATED DROPDOWN ---------------------*/
    $(document).on("change", "#crop_id", function () {
        $("#crop_type_id").val('');
        $("#variety1_id").val('');
        $("#variety2_id").val('');

        var crop_id = $('#crop_id').val();
        $('#crop_type_id_container').hide();
        $('#variety1_id_container').hide();
        $('#variety2_id_container').hide();
        if (crop_id > 0) {
            $('#crop_type_id_container').show();
            if (system_types[crop_id] !== undefined) {
                $("#crop_type_id").html(get_dropdown_with_select(system_types[crop_id]));
            }
        }
    });

    $(document).on("change", "#crop_type_id", function () {
        $("#variety1_id").val('');
        $("#variety2_id").val('');
        var crop_type_id = $('#crop_type_id').val();
        if (crop_type_id > 0) {
            $.ajax({
                url: "<?php echo site_url($CI->controller_url.'/index/get_fd_budget_varieties/') ?>",
                type: 'POST',
                datatype: "JSON",
                data: { id: crop_type_id },
                success: function (data, status) {

                },
                error: function (xhr, desc, err) {
                    console.log("error");
                }
            });
            $('#variety1_id_container').show();
            $('#variety2_id_container').show();
        }
        else {
            $('#variety1_id_container').hide();
            $('#variety2_id_container').hide();
        }
    });
    /*--------------------- CROP RELATED DROPDOWN ( END )-------------*/


    /*--------------------- LOCATION RELATED DROPDOWN -----------------------------*/
    $(document).on("change", "#division_id", function () {
        $("#zone_id").val('');
        $("#territory_id").val('');
        $("#district_id").val('');
        $("#outlet_id").val('');
        $('#growing_area_id').val('');

        var division_id = $('#division_id').val();
        $('#zone_id_container').hide();
        $('#territory_id_container').hide();
        $('#district_id_container').hide();
        $('#outlet_id_container').hide();
        $('#growing_area_id_container').hide();
        $('#dealer_container').hide();
        $('#leading_farmer_container').hide();
        if (division_id > 0) {
            $('#zone_id_container').show();
            if (system_zones[division_id] !== undefined) {
                $("#zone_id").html(get_dropdown_with_select(system_zones[division_id]));
            }
        }
        calculate_total_participants('reset');
    });
    $(document).on("change", "#zone_id", function () {
        $("#territory_id").val('');
        $("#district_id").val('');
        $("#outlet_id").val('');

        var zone_id = $('#zone_id').val();
        $('#territory_id_container').hide();
        $('#district_id_container').hide();
        $('#outlet_id_container').hide();
        $('#growing_area_id_container').hide();
        $('#dealer_container').hide();
        $('#leading_farmer_container').hide();
        if (zone_id > 0) {
            $('#territory_id_container').show();
            if (system_territories[zone_id] !== undefined) {
                $("#territory_id").html(get_dropdown_with_select(system_territories[zone_id]));
            }
        }
        calculate_total_participants('reset');
    });
    $(document).on("change", "#territory_id", function () {
        $("#district_id").val('');
        $("#outlet_id").val('');

        var territory_id = $('#territory_id').val();
        $('#district_id_container').hide();
        $('#outlet_id_container').hide();
        $('#growing_area_id_container').hide();
        $('#dealer_container').hide();
        $('#leading_farmer_container').hide();
        if (territory_id > 0) {
            $('#district_id_container').show();
            if (system_districts[territory_id] !== undefined) {
                $("#district_id").html(get_dropdown_with_select(system_districts[territory_id]));
            }
        }
        calculate_total_participants('reset');
    });
    $(document).on("change", "#district_id", function () {
        $('#outlet_id').val('');

        var district_id = $('#district_id').val();
        $('#outlet_id_container').hide();
        $('#growing_area_id_container').hide();
        $('#dealer_container').hide();
        $('#leading_farmer_container').hide();
        if (district_id > 0) {
            if (system_outlets[district_id] !== undefined) {
                $('#outlet_id_container').show();
                $('#outlet_id').html(get_dropdown_with_select(system_outlets[district_id]));
            }
        }
        calculate_total_participants('reset');
    });
    $(document).on("change", "#outlet_id", function () {
        var outlet_id = parseInt($(this).val());
        if (outlet_id > 0) {
            $.ajax({
                url: "<?php echo site_url($CI->controller_url.'/index/get_growing_area/') ?>",
                type: 'POST',
                datatype: "JSON",
                data: {
                    html_container_id: '#growing_area_id',
                    id: outlet_id
                },
                success: function (data, status) {
                    if (data.status) {
                        $('#growing_area_id_container').show();
                    }
                },
                error: function (xhr, desc, err) {
                    console.log("error");
                }
            });

            $.ajax({
                url: "<?php echo site_url($CI->controller_url.'/index/get_dealers/') ?>",
                type: 'POST',
                datatype: "JSON",
                data: {
                    html_container_id: '#dealer_id',
                    id: outlet_id
                },
                success: function (data, status) {
                    if (data.status) {
                        $('#dealer_container').show();
                    }
                },
                error: function (xhr, desc, err) {
                    console.log("error");
                }
            });

            $.ajax({
                url: "<?php echo site_url($CI->controller_url.'/index/get_lead_farmers/') ?>",
                type: 'POST',
                datatype: "JSON",
                data: {
                    html_container_id: '#leading_farmer_id',
                    id: outlet_id
                },
                success: function (data, status) {
                    if (data.status) {
                        $('#leading_farmer_container').show();
                    }
                },
                error: function (xhr, desc, err) {
                    console.log("error");
                }
            });
        } else {
            $('#dealer_container').hide();
            $('#leading_farmer_container').hide();
        }

        calculate_total_participants('reset');
    });
    /*--------------------- LOCATION RELATED DROPDOWN ( END ) ---------------------*/

    /* Calculate Total Participant */
    $(document).on("input", ".participant_budget", function () {
        calculate_total_participants('');
    });

    function calculate_total_participants(action) {
        if (action == 'reset') {
            $(".participant_budget").val(0);
        }
        var total = parseInt(0);
        var item = parseInt(0);
        $(".participant_budget").each(function (index, element) {
            item = parseInt($(this).val());
            if (!isNaN(item) && (item > 0)) {
                total += item;
            }
        });
        $('#no_of_participant').text(total);
    }

    /* Calculate Total Budget Expense */
    $(document).on("input", ".expense_budget", function () {
        var total = parseFloat(0);
        var item = parseFloat(0);
        $(".expense_budget").each(function (index, element) {
            item = parseFloat($(this).val());
            if (!isNaN(item) && (item > 0)) {
                total += item;
            }
        });
        $('#total_budget').text(get_string_amount(total));
    });

    $(document).on("blur", ".integer_type_positive, .float_type_positive", function () {
        var value = $(this).val();
        if (value == "") {
            $(this).val(0)
        }
    });
});
</script>
