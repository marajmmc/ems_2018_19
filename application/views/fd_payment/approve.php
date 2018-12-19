<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();

$action_buttons = array();
$action_buttons[] = array
(
    'label' => $CI->lang->line("ACTION_BACK"),
    'href' => site_url($CI->controller_url)
);
$CI->load->view("action_buttons", array('action_buttons' => $action_buttons));

?>
<div class="row widget">

    <div class="widget-header">
        <div class="title">
            <?php echo $title; ?>
        </div>
        <div class="clearfix"></div>
    </div>

    <div class="row show-grid">

        <div class="col-xs-12">

            <?php echo $CI->load->view("info_basic", '', true); ?>

            <div class="panel panel-default">

                <div class="panel-heading">
                    <h4 class="panel-title">
                        <label>
                            <a class="external text-danger" data-toggle="collapse" data-target="#collapse1" href="#"> + Participants Information</a>
                        </label>
                    </h4>
                </div>

                <div id="collapse1" class="panel-collapse collapse">
                    <div class="row" style="padding:20px 0; border:none">

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
                                    $index = 0;
                                    foreach ($dealers as &$dealer)
                                    {
                                        $dealer['participant'] = (isset($dealer['participant'])) ? $dealer['participant'] : 0;
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
                                    foreach ($lead_farmers as &$farmer)
                                    {
                                        $farmer['participant'] = (isset($farmer['participant'])) ? $farmer['participant'] : 0;
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
                                    <tr>
                                        <td style="text-align:right; font-weight:bold" colspan="2"><?php echo $CI->lang->line('LABEL_PARTICIPANT_THROUGH_OTHERS'); ?> :</td>
                                        <td style="text-align:right; padding:5px; font-weight:bold"><?php echo $item['participant_others']; $total_participant += $item['participant_others']; ?></td>
                                    </tr>
                                    <tr>
                                        <td style="text-align:right; font-weight:bold" colspan="2"><?php echo $CI->lang->line('LABEL_TOTAL_PARTICIPANT'); ?> :</td>
                                        <td style="text-align:right; padding:5px; font-weight:bold"><?php echo $total_participant; ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>

            </div>

            <div class="panel panel-default">

                <div class="panel-heading">
                    <h4 class="panel-title">
                        <label>
                            <a class="external text-danger" data-toggle="collapse" data-target="#collapse2" href="#"> + Expense Information</a>
                        </label>
                    </h4>
                </div>

                <div id="collapse2" class="panel-collapse collapse in">
                    <div class="row" style="margin:0; padding:20px 0; border:none">

                        <div class="row show-grid">
                            <div class="col-xs-4"> &nbsp; </div>
                            <div class="col-xs-4">
                                <label class="control-label" style="text-decoration:underline; font-size:1.3em; font-weight:normal">
                                    <?php echo $CI->lang->line('LABEL_FIELD_DAY_BUDGET'); ?>:
                                </label>
                            </div>
                        </div>

                        <div class="row show-grid">
                            <div class="col-xs-4"> &nbsp; </div>
                            <div class="col-xs-4">
                                <table style="width:100%">
                                    <?php
                                    $total_budget = 0;
                                    foreach ($expense_items as $expense)
                                    {
                                        ?>
                                        <tr>
                                            <td style="text-align:right"><?php echo $expense['name']; ?> :</td>
                                            <td style="text-align:right; padding:10px; font-weight:bold"><?php echo System_helper::get_string_amount($expense['amount']); ?></td>
                                        </tr>
                                        <?php
                                        $total_budget += $expense['amount'];
                                    }
                                    ?>

                                    <tr>
                                        <td style="border-top:1px solid #000; text-align:right; font-weight:bold"><?php echo $CI->lang->line('LABEL_TOTAL_FIELD_DAY_BUDGET'); ?> :</td>
                                        <td style="border-top:1px solid #000; text-align:right; font-weight:bold; width:25%; padding:10px"><?php echo System_helper::get_string_amount($total_budget); ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>

        <div class="col-xs-12">
            <form class="form_valid" id="save_form" action="<?php echo site_url($CI->controller_url . '/index/save_approve'); ?>" method="post">
                <input type="hidden" id="id" name="id" value="<?php echo $item['budget_id']; ?>"/>

                <div class="row show-grid">
                    <div class="col-xs-4">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_APPROVE'); ?>
                            <span style="color:#FF0000">*</span></label>
                    </div>
                    <div class="col-xs-4">
                        <select name="item[status_payment_approve]" class="form-control status-combo">
                            <option value=""><?php echo $CI->lang->line('SELECT'); ?></option>
                            <option value="<?php echo $CI->config->item('system_status_approved'); ?>"><?php echo $CI->lang->line('LABEL_APPROVE'); ?></option>
                        </select>
                    </div>
                </div>

                <div class="row show-grid">
                    <div class="col-xs-4">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_REMARKS'); ?> &nbsp;</label>
                    </div>
                    <div class="col-xs-4">
                        <textarea name="item[remarks_payment_approve]" class="form-control"></textarea>
                    </div>
                </div>

                <div class="row show-grid">
                    <div class="col-xs-4">
                        &nbsp;
                    </div>
                    <div class="col-xs-4">
                        <div class="action_button pull-right">
                            <button id="button_action_save" type="button" class="btn" data-form="#save_form">Save</button>
                        </div>
                    </div>
                    <div class="col-xs-4">
                        &nbsp;
                    </div>
                </div>
            </form>
        </div>

        <div class="clearfix"></div>

    </div>

</div>

<script type="text/javascript">
    jQuery(document).ready(function ($) {
        system_off_events(); // Triggers

        $(".status-combo").on('change', function (event) {
            var options = $(this).val();
            if (options == '<?php echo $CI->config->item('system_status_approved'); ?>') {
                $("#button_action_save").attr('data-message-confirm', '<?php echo $CI->lang->line('MSG_CONFIRM_APPROVE'); ?>');
            }
            else {
                $("#button_action_save").removeAttr('data-message-confirm');
            }
        });
    });
</script>
