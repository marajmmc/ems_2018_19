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

    label {
        margin-top: 5px
    }

    label.normal {
        font-weight: normal !important
    }
</style>

<div class="row widget">

<div class="widget-header">
    <div class="title">
        <?php echo $title; ?>
    </div>
    <div class="clearfix"></div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">
        <h4 class="panel-title">
            <label class=""><a class="external text-danger" data-toggle="collapse" data-target="#collapse1" href="#"> + Tour Information</a></label>
        </h4>
    </div>
    <div id="collapse1" class="panel-collapse collapse">
        <table class="table table-bordered table-responsive system_table_details_view">
            <tr>
                <th class="widget-header header_caption"><label class="control-label pull-right">Name</label></th>
                <th colspan="3"><label class="control-label"><?php echo $item['name']; ?></label></th>
            </tr>
            <tr>
                <th class="widget-header header_caption"><label class="control-label pull-right">Designation</label>
                </th>
                <th>
                    <label class="control-label"><?php echo ($item['designation']) ? $item['designation'] : 'N/A'; ?></label>
                </th>
                <th class="widget-header header_caption"><label class="control-label pull-right">Department</label></th>
                <th>
                    <label class="control-label"><?php echo ($item['department_name']) ? $item['department_name'] : 'N/A'; ?></label>
                </th>
            </tr>
            <tr>
                <th class="widget-header header_caption"><label class="control-label pull-right">Title</label></th>
                <th colspan="3"><label class="control-label"><?php echo $item['title']; ?></label></th>
            </tr>
            <tr>
                <th class="widget-header header_caption">
                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE'); ?></label></th>
                <th colspan="3">
                    <label class="control-label"> From <?php echo System_helper::display_date($item['date_from']) ?>
                        To <?php echo System_helper::display_date($item['date_to']) ?>
                    </label>
                </th>
            </tr>
            <tr>
                <th class="widget-header header_caption">
                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_AMOUNT_IOU_REQUEST'); ?></label>
                </th>
                <th colspan="3">
                    <label class="control-label"><?php echo number_format($item['amount_iou_request'], 2); ?></label>
                </th>
            </tr>
            <tr>
                <th class="widget-header header_caption">
                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CREATED_BY'); ?></label>
                </th>
                <th><label class="control-label"><?php echo $users[$item['user_created']]['name']; ?></label></th>
                <th class="widget-header header_caption">
                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_CREATED_TIME'); ?></label>
                </th>
                <th>
                    <label class="control-label"><?php echo System_helper::display_date_time($item['date_created']); ?></label>
                </th>
            </tr>
            <?php
            if ($item['user_updated'])
            {
                ?>
                <tr>
                    <th class="widget-header header_caption">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_UPDATED_BY'); ?></label>
                    </th>
                    <th><label class="control-label"><?php echo $users[$item['user_updated']]['name']; ?></label></th>
                    <th class="widget-header header_caption">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_UPDATED_TIME'); ?></label>
                    </th>
                    <th>
                        <label class="control-label"><?php echo System_helper::display_date_time($item['date_updated']); ?></label>
                    </th>
                </tr>
            <?php
            }
            ?>
            <tr>
                <th class="widget-header header_caption">
                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_FORWARD'); ?> Status</label>
                </th>
                <th><label class="control-label"><?php echo $item['status_forwarded_tour']; ?></label></th>
                <th class="widget-header header_caption">
                    <label class="control-label pull-right">(Tour Setup) Number of Edit</label></th>
                <th colspan="3"><label class="control-label"><?php echo($item['revision_count'] - 1); ?></label></th>
            </tr>
            <?php
            if ($item['user_forwarded_tour'])
            {
                ?>
                <tr>
                    <th class="widget-header header_caption">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_FORWARDED_BY'); ?></label>
                    </th>
                    <th><label class="control-label"><?php echo $users[$item['user_forwarded_tour']]['name']; ?></label>
                    </th>
                    <th class="widget-header header_caption">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_FORWARDED_TIME'); ?></label>
                    </th>
                    <th>
                        <label class="control-label"><?php echo System_helper::display_date_time($item['date_forwarded_tour']); ?></label>
                    </th>
                </tr>
            <?php
            }
            if ($item['status_approved_tour'] == $CI->config->item('system_status_approved'))
            {
                ?>
                <tr>
                    <th colspan="4" class="bg-info">Tour Approval Information</th>
                </tr>
                <tr>
                    <th class="widget-header header_caption">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_APPROVE'); ?> Status</label>
                    </th>
                    <th><label class="control-label"><?php echo $item['status_approved_tour']; ?></label></th>
                    <th class="widget-header header_caption">
                        <label class="control-label pull-right">(Tour Setup) Number of Rollback</label>
                    </th>
                    <th><label class="control-label"><?php echo $item['revision_count_rollback_tour']; ?></label></th>
                </tr>
                <tr>
                    <th class="widget-header header_caption">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_APPROVED_BY'); ?></label>
                    </th>
                    <th><label class="control-label"><?php echo $users[$item['user_approved_tour']]['name']; ?></label>
                    </th>
                    <th class="widget-header header_caption">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_APPROVED_TIME'); ?></label>
                    </th>
                    <th>
                        <label class="control-label"><?php echo System_helper::display_date_time($item['date_approved_tour']); ?></label>
                    </th>
                </tr>
            <?php } ?>
        </table>
    </div>
    <div class="clearfix"></div>
</div>

<div class="row show-grid">
    <div class="col-xs-2">
        &nbsp;
    </div>
    <div class="col-sm-8 col-xs-8">
        <table class="table table-bordered">
            <thead>
            <tr>
                <th><?php echo $this->lang->line('LABEL_SL_NO'); ?></th>
                <th>Purpose(s)</th>
                <th>Reporting Date</th>
                <th>Status</th>
            </tr>
            </thead>
            <tbody>
            <?php if ($items)
            {
                $i = 0;
                foreach ($items as $row)
                {
                    ?>
                    <tr>
                        <td><?php echo ++$i; ?></td>
                        <td><?php echo $row['purpose']; ?></td>
                        <td>
                            <?php
                            if ($row['reporting_dates'])
                            {
                                $reporting_dates = explode(', ', $row['reporting_dates']);
                                foreach ($reporting_dates as $reporting_date)
                                {
                                    echo '<button class="btn btn-sm btn-info pop_up" data-purpose="' . $row['p_id'] . '" data-date="' . $reporting_date . '">' . (System_helper::display_date($reporting_date)) . '</button> &nbsp;';
                                }
                            }
                            ?>
                        </td>
                        <td>
                            <select name="items[purpose_status][rep_date]" class="form-control">
                                <option>In-complete</option>
                                <option>Complete</option>
                            </select>
                        </td>
                    </tr>
                <?php
                }
            } ?>
            </tbody>
        </table>
    </div>
</div>

<div class="row">
    <form class="form_valid" id="save_form" action="<?php echo site_url($CI->controller_url . '/index/save_forward'); ?>" method="post">
        <input type="hidden" id="id" name="id" value="<?php echo $item['tour_setup_id']; ?>"/>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Forward <span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-xs-4">
                <select name="item[status_forwarded_tour]" class="form-control status-combo">
                    <option value=""><?php echo $this->lang->line('SELECT'); ?></option>
                    <option value="<?php echo $this->config->item('system_status_forwarded'); ?>">Forward</option>
                </select>
            </div>
            <div class="col-xs-4">
                <div class="action_button" style="margin:0">
                    <button id="button_action_save" type="button" class="btn" data-form="#save_form">Save</button>
                </div>
            </div>
        </div>

        <div class="clearfix"></div>
    </form>
</div>

</div>

<script type="text/javascript">
    jQuery(document).ready(function () {
        $(document).off("click", ".pop_up");
        $(document).on("click", ".pop_up", function (event) {
            var id =<?php echo $item['tour_setup_id']; ?>;
            var p_id = $(this).attr('data-purpose');
            var r_date = $(this).attr('data-date');
            $.ajax(
                {
                    url: "<?php echo site_url($CI->controller_url.'/index/reporting_details') ?>",
                    type: 'POST',
                    datatype: "JSON",
                    data: {
                        html_container_id: '#popup_content',
                        id: id,
                        p_id: p_id,
                        r_date: r_date
                    },
                    success: function (data, status) {

                    },
                    error: function (xhr, desc, err) {
                        console.log("error");
                    }
                });
            $("#popup_window").jqxWindow('open');
        });

        $(".status-combo").on('change', function (event) {
            var options = $(this).val();
            if (options == '<?php echo $this->config->item('system_status_forwarded'); ?>') {
                $("#button_action_save").attr('data-message-confirm', '<?php echo $this->lang->line('MSG_CONFIRM_FORWARD'); ?>');
            } else {
                $("#button_action_save").removeAttr('data-message-confirm');
            }
        });
    });
</script>
