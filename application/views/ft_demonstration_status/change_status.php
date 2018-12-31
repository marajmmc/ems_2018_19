<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();

$action_buttons = array();

$action_buttons[] = array
(
    'label' => $CI->lang->line("ACTION_BACK"),
    'href' => site_url($CI->controller_url)
);

$CI->load->view('action_buttons', array('action_buttons' => $action_buttons));

?>

<form class="form_valid" id="save_form" action="<?php echo site_url($CI->controller_url . '/index/save_status'); ?>" method="post">
    <input type="hidden" id="id" name="id" value="<?php echo $item['id']; ?>"/>

    <div class="row widget">
        <div class="widget-header">
            <div class="title">
                <?php echo $title; ?>
            </div>
            <div class="clearfix"></div>
        </div>

        <?php echo $CI->load->view("info_basic", '', true); ?>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Change Status <span style="color:#FF0000">*</span></label></div>
            <div class="col-xs-4">
                <select id="status" name="item[status]" class="form-control">
                    <option value=""><?php echo $CI->lang->line('SELECT'); ?></option>
                    <option value="<?php echo $CI->config->item('system_status_inactive'); ?>"><?php echo $CI->lang->line('INACTIVE'); ?></option>
                    <option value="<?php echo $CI->config->item('system_status_deleted'); ?>"><?php echo $CI->lang->line('DELETE'); ?></option>
                </select>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4"> &nbsp; </div>
            <div class="col-xs-4">
                <div class="action_button pull-right">
                    <button id="button_action_save" type="button" class="btn" data-form="#save_form">Save</button>
                </div>
            </div>
        </div>
    </div>

    <div class="clearfix"></div>

</form>

<script type="text/javascript">
    $(document).ready(function () {
        system_off_events(); // Triggers
        $(".datepicker").datepicker({dateFormat: display_date_format});
    });
</script>
