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
            <label class="control-label"><?php echo $item['name'] ?> (<?php echo $item['employee_id'] ?>)</label>
        </div>
    </div>

    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right">Designation:</label>
        </div>
        <div class="col-sm-5 col-xs-8">
            <label class="control-label"><?php echo ($item['designation']) ? $item['designation'] : 'N/A'; ?></label>
        </div>
    </div>

    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right">Department:</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label class="control-label">
                <?php echo ($item['department_name']) ? $item['department_name'] : 'N/A'; ?>
            </label>
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
            <label class="control-label pull-right">Duration:</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label class="control-label"><?php echo Tour_helper::tour_duration($item['date_from'], $item['date_to']); ?></label>
        </div>
    </div>

    <?php echo Tour_helper::tour_purpose_view($item['tour_setup_id']); ?>

    <?php echo Tour_helper::iou_items_summary_view('', $item); ?>

    <?php if ($item['remarks']) { ?>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Remarks:</label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label normal"><?php echo nl2br($item['remarks']); ?></label>
            </div>
        </div>
    <?php } ?>

    <?php if($item['revision_count_rollback_tour'] > 0){ ?>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Total Rollback:</label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label normal text-danger"><?php echo $item['revision_count_rollback_tour']; ?> Time(s)</label>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Supervisors Remarks:</label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label normal text-danger"><?php echo nl2br($item['remarks_rollback_tour']); ?></label>
            </div>
        </div>
    <?php } ?>

    <form class="form_valid" id="save_form" action="<?php echo site_url($CI->controller_url . '/index/save_forward'); ?>" method="post">
        <input type="hidden" id="id" name="id" value="<?php echo $item['tour_setup_id']; ?>"/>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Forward <span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select name="item[status_forwarded_tour]" class="form-control status-combo">
                    <option value=""><?php echo $CI->lang->line('SELECT'); ?></option>
                    <option value="<?php echo $CI->config->item('system_status_forwarded'); ?>">Forward</option>
                </select>
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
    jQuery(document).ready(function () {
        system_off_events(); // Triggers

        $(".status-combo").on('change', function (event) 
        {
            var options = $(this).val();
            if (options == '<?php echo $CI->config->item('system_status_forwarded'); ?>') {
                $("#button_action_save").attr('data-message-confirm', '<?php echo $CI->lang->line('MSG_CONFIRM_FORWARD'); ?>');
            } else {
                $("#button_action_save").removeAttr('data-message-confirm');
            }
        });
    });
</script>
