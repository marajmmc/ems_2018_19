<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();
$action_buttons = array();
$action_buttons[] = array(
    'label' => $CI->lang->line("ACTION_BACK"),
    'href' => site_url($CI->controller_url . '/index/list/')
);
$action_buttons[] = array(
    'type' => 'button',
    'label' => $CI->lang->line("ACTION_CLEAR"),
    'id' => 'button_action_clear',
    'data-form' => '#save_form'
);
$CI->load->view('action_buttons', array('action_buttons' => $action_buttons));
?>
<style>
    .purpose-list table tr td:first-child {
        width: 50px
    }
    label{margin-top:5px}
    label.normal{font-weight:normal !important}
</style>

<div class="row widget">
    <div class="widget-header">
        <div class="title">
            <?php echo $title; ?>
        </div>
        <div class="clearfix"></div>
    </div>

    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right">Name:</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label class="control-label"><?php echo $item['name'] ?></label>
        </div>
    </div>

    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right">Designation:</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label class="control-label"><?php echo ($item['designation'])? $item['designation'] :'N/A'; ?></label>
        </div>
    </div>

    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right">Department:</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label class="control-label"><?php echo ($item['department_name']) ? $item['department_name'] :'N/A'; ?></label>
        </div>
    </div>

    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right">Title:</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label class="control-label"><?php echo $item['title'] ?></label>
        </div>
    </div>

    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE'); ?>:</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            From &nbsp;<label class="control-label"><?php echo System_helper::display_date($item['date_from']) ?></label> &nbsp;
            To &nbsp;<label class="control-label"><?php echo System_helper::display_date($item['date_to']) ?></label>
        </div>
    </div>

    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right">Purpose(s):</label>
        </div>
        <div class="col-sm-4 col-xs-8 purpose-list">
            <table class="table table-bordered table-striped table-hover">
                <thead>
                <tr>
                    <th><?php echo $this->lang->line('LABEL_SL_NO'); ?></th>
                    <th>Purpose</th>
                </tr>
                </thead>
                <tbody>
                <?php
                if ($items)
                {
                    $serial = 0;
                    foreach ($items as $row)
                    {
                        ++$serial;
                        ?>
                        <tr>
                            <td><?php echo $serial . '.'; ?></td>
                            <td><?php echo $row['purpose']; ?></td>
                        </tr>
                    <?php
                    }
                }
                else
                {
                    ?>
                    <div class="alert alert-danger text-center"> Tour purpose(s) has not been setup yet.</div>
                <?php
                }
                ?>
                </tbody>
            </table>

        </div>
    </div>

    <?php
    if ($iou_items)
    {
        $i = 0;
        $amount_iou_items = array();
        $total_iou_amount = 0.0;
        if($item['amount_iou_items'] && ($item['amount_iou_items'] != '')){
            $amount_iou_items = json_decode($item['amount_iou_items'], TRUE);
        }
        foreach ($iou_items as $iou_item)
        {
            ?>
            <div class="row show-grid">
                <div class="col-xs-4">
                    <?php if ($i == 0)
                    {
                    ?>
                        <label class="control-label pull-right"><?php echo 'IOU Items'; ?>:</label>
                    <?php
                    }
                    else
                    {
                        echo '';
                    }
                    ?>
                </div>
                <div class="col-xs-3">
                    <label class="control-label pull-right normal"><?php echo to_label($iou_item); ?>:</label>
                </div>
                <div class="col-xs-1" style="padding-left:0;">
                    <label class="control-label pull-right"><?php echo System_helper::get_string_amount( (isset($amount_iou_items[$iou_item]))? $amount_iou_items[$iou_item]: 0 ); ?></label>
                </div>
            </div>
            <?php
            $total_iou_amount += $amount_iou_items[$iou_item];
            $i++;
        }
    }
    ?>


    <div class="row show-grid" style="margin-bottom:30px">
        <div class="col-xs-4">
            &nbsp;
        </div>
        <div class="col-xs-3" style="border-top:1px solid #000; padding-top:5px">
            <label class="control-label pull-right">Total <?php echo $CI->lang->line('LABEL_AMOUNT_IOU'); ?>:</label>
        </div>
        <div class="col-xs-1" style="border-top:1px solid #000; padding-top:5px; padding-left:0; text-align:right">
            <label class="control-label"><?php echo System_helper::get_string_amount($total_iou_amount); ?></label>
        </div>
    </div>


    <?php if($item['remarks']){ ?>
    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right">Remarks:</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label class="control-label"><?php echo nl2br($item['remarks']); ?></label>
        </div>
    </div>
    <?php } ?>

    <form class="form_valid" id="save_form" action="<?php echo site_url($CI->controller_url . '/index/save_approve'); ?>" method="post">
        <input type="hidden" id="id" name="id" value="<?php echo $item['tour_setup_id']; ?>"/>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Approve<span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select name="item[status_approve]" class="form-control status-combo">
                    <option value=""><?php echo $this->lang->line('SELECT'); ?></option>
                    <option value="<?php echo $CI->config->item('system_status_approved') ?>">Approve</option>
                    <option value="<?php echo $CI->config->item('system_status_rollback') ?>">Roll Back</option>
                </select>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Supervisors Comment<span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <textarea name="item[superior_comment]" class="form-control"><?php echo $item['superior_comment'] ?></textarea>
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

            </div>
        </div>
    </form>
</div>
<div class="clearfix"></div>

<script type="text/javascript">

    jQuery(document).ready(function () {

        $(".status-combo").on('change', function (event) {
            var options = $(this).val();
            if (options == 'Approved') {
                $("#button_action_save").attr('data-message-confirm', '<?php echo $this->lang->line('MSG_CONFIRM_APPROVE'); ?>');
            } else if (options == 'Rollback') {
                $("#button_action_save").attr('data-message-confirm', '<?php echo $this->lang->line('MSG_CONFIRM_ROLLBACK'); ?>');
            } else {
                $("#button_action_save").removeAttr('data-message-confirm');
            }
        });
    });
</script>
