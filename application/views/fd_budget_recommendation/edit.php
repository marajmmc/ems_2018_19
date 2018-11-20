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
?>
<style> label { margin-top:5px; } </style>

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
                <label class="control-label"><?php echo System_helper::display_date($item['date_proposal']); ?></label>
            </div>
        </div>

        <div style="" class="row show-grid" id="crop_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_NAME'); ?>
                    <span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $item_info['crop_name']; ?></label>
            </div>
        </div>

        <div class="row show-grid" id="crop_type_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_TYPE'); ?>
                    <span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $item_info['crop_type_name']; ?></label>
            </div>
        </div>

        <div class="row show-grid" id="variety1_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_VARIETY1_NAME'); ?>
                    <span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $item_info['variety1_name']; ?></label>
            </div>
        </div>

        <div class="row show-grid" id="variety2_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_VARIETY2_NAME'); ?>
                    <span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $item_info['variety2_name']; ?></label>
            </div>
        </div>

        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DIVISION_NAME'); ?>
                    <span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-4">
                <label class="control-label"><?php echo $item_info['division_name']; ?></label>
            </div>
        </div>

        <div class="row show-grid" id="zone_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ZONE_NAME'); ?>
                    <span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $item_info['zone_name']; ?></label>
            </div>
        </div>

        <div class="row show-grid" id="territory_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_TERRITORY_NAME'); ?>
                    <span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $item_info['territory_name']; ?></label>
            </div>
        </div>

        <div class="row show-grid" id="district_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DISTRICT_NAME'); ?>
                    <span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $item_info['district_name']; ?></label>
            </div>
        </div>

        <div class="row show-grid" id="outlet_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_OUTLET_NAME'); ?>
                    <span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $item_info['outlet_name']; ?></label>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ADDRESS'); ?>
                    <span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label" style="font-weight:normal"><?php echo nl2br($item_info['address']); ?></label>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PRESENT_CONDITION'); ?>
                    <span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label" style="font-weight:normal"><?php echo nl2br($item_info['present_condition']); ?></label>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DEALERS_EVALUATION'); ?>
                    <span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label" style="font-weight:normal"><?php echo nl2br($item_info['farmers_evaluation']); ?></label>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_SPECIFIC_DIFFERENCE'); ?>
                    <span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label" style="font-weight:normal"><?php echo nl2br($item_info['diff_between_varieties']); ?></label>
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

        <?php $total_participant = 0; ?>

        <div style="<?php echo (!(sizeof($dealers) > 0) && !($item['id'] > 0)) ? 'display:none;' : ''; ?>" class="row show-grid" id="dealer_container">

            <div id="dealer_id" class="row show-grid">
                <div class="row show-grid">
                    <div class="col-xs-4">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PARTICIPANT_THROUGH_DEALER'); ?> : </label>
                    </div>
                </div>

                <?php
                foreach ($dealers as $dealer)
                {
                    ?>
                    <div class="row show-grid">
                        <div class="col-xs-6">
                            <label style="font-weight:normal" class="control-label pull-right"><?php echo $dealer['text'] . ' (' . $dealer['phone_no'] . ')'; ?>
                                <span style="color:#FF0000">*</span></label>
                        </div>
                        <div class="col-xs-2">
                            <input type="text" name="dealer_participant[<?php echo $dealer['value']; ?>]" class="form-control integer_type_positive participant_budget" value="<?php
                            if (isset($participants[$dealer['value']]))
                            {
                                $total_participant += $participants[$dealer['value']];
                                echo ((int) $participants[$dealer['value']]);
                            }?>"/>
                        </div>
                    </div>
                <?php
                }
                ?>
            </div>
        </div>

        <div style="<?php echo (!(sizeof($leading_farmers) > 0) && !($item['id'] > 0)) ? 'display:none;' : ''; ?>" class="row show-grid" id="leading_farmer_container">

            <div id="leading_farmer_id" class="row show-grid">
                <div class="row show-grid">
                    <div class="col-xs-4">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PARTICIPANT_THROUGH_LEAD_FARMER'); ?> : </label>
                    </div>
                </div>

                <?php
                foreach ($leading_farmers as $lead_farmer)
                {
                    ?>
                    <div class="row show-grid">
                        <div class="col-xs-6">
                            <label style="font-weight:normal" class="control-label pull-right"><?php echo $lead_farmer['text'] . ' (' . $lead_farmer['phone_no'] . ')'; ?>
                                <span style="color:#FF0000">*</span></label>
                        </div>
                        <div class="col-xs-2">
                            <input type="text" name="farmer_participant[<?php echo $lead_farmer['value']; ?>]" class="form-control integer_type_positive participant_budget" value="<?php
                            if (isset($participants[$lead_farmer['value']]))
                            {
                                $total_participant += $participants[$lead_farmer['value']];
                                echo ((int) $participants[$lead_farmer['value']]);
                            }?>"/>
                        </div>
                    </div>
                <?php
                }
                ?>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PARTICIPANT_THROUGH_CUSTOMER'); ?>
                    <span style="color:#FF0000;">*</span></label>
            </div>
            <div class="col-xs-4">
                <input type="text" name="item_info[participant_customers]" class="participant_budget form-control integer_type_positive" value="<?php if (isset($item_info['participant_customers'])){
                    $total_participant += $item_info['participant_customers'];
                    echo $item_info['participant_customers'];
                } else {
                    echo 0;
                }?>"/>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PARTICIPANT_THROUGH_OTHERS'); ?>
                    <span style="color:#FF0000;">*</span></label>
            </div>
            <div class="col-xs-4">
                <input type="text" name="item_info[participant_others]" class="participant_budget form-control integer_type_positive" value="<?php if (isset($item_info['participant_others'])){
                    $total_participant += $item_info['participant_others'];
                    echo $item_info['participant_others'];
                } else {
                    echo 0;
                }?>"/>
            </div>
        </div>

        <div class="row show-grid" id="total_participant_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_EXPECTED_PARTICIPANT'); ?></label>
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
                    foreach ($expense_items as $expense)
                    {
                        ?>
                        <tr>
                            <td class="right-align" style="width:60%">
                                <label class="control-label" style="font-weight:normal"><?php echo $expense['text']; ?> :</label>
                            </td>
                            <td>
                                <input type="text" name="expense_budget[<?php echo $expense['value']; ?>]" class="expense_budget form-control float_type_positive" value="<?php if (isset($expense_budget[$expense['value']]))
                                {
                                    $total_budget += $expense_budget[$expense['value']];
                                    echo $expense_budget[$expense['value']];
                                }
                                else
                                {
                                    echo '0';
                                }?>"/>
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                </table>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_TOTAL_FIELD_DAY_BUDGET'); ?></label>
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
                <input type="text" name="item_info[quantity_market_size_total]" id="quantity_market_size_total" class="form-control float_type_positive" value="<?php if ($item_info['quantity_market_size_total'])
                {
                    echo $item_info['quantity_market_size_total'];
                } ?>"/>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ARM_MARKET_SIZE'); ?>
                    <span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="item_info[quantity_market_size_arm]" id="quantity_market_size_arm" class="form-control float_type_positive" value="<?php if ($item_info['quantity_market_size_arm'])
                {
                    echo $item_info['quantity_market_size_arm'];
                } ?>"/>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_NEXT_SALES_TARGET'); ?>
                    <span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="item_info[quantity_sales_target]" id="quantity_sales_target" class="form-control float_type_positive" value="<?php if ($item_info['quantity_sales_target'])
                {
                    echo $item_info['quantity_sales_target'];
                } ?>"/>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_RECOMMENDATION'); ?>
                    <span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label" style="font-weight:normal"><?php echo nl2br($item_info['remarks']); ?></label>
            </div>
        </div>

        <div class="clearfix"></div>
    </div>
</form>

<!--------Shows Previous Update History, when EDIT Mode-------->

<?php echo $CI->load->view($CI->controller_url . "/history", $items_history, true); ?>

<!-----Shows Previous Update History, when EDIT Mode(END)------>

<script type="text/javascript">
    jQuery(document).ready(function ($) {
        system_off_events(); // Triggers
        $(".datepicker").datepicker({dateFormat: display_date_format});

        $(document).off("input", ".expense_budget");
        $(document).off("input", ".participant_budget");

        /* Calculate Total Participant */
        $(document).on("input", ".participant_budget", function () {
            calculate_total_participants('');
        });

        function calculate_total_participants(action){
            if(action=='reset'){
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
            if(value == ""){
                $(this).val(0)
            }
        });
    });
</script>
