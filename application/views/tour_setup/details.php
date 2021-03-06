<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI =& get_instance();

$action_buttons = array();
if (isset($tour_extension_page) && $tour_extension_page)
{
    $action_buttons[] = array(
        'label' => $CI->lang->line("ACTION_BACK") . ' to List',
        'href' => site_url($CI->controller_url)
    );
}
else
{
    $action_buttons[] = array(
        'label' => $CI->lang->line("ACTION_BACK") . ' to Pending List',
        'href' => site_url($CI->controller_url)
    );
    $action_buttons[] = array(
        'label' => $CI->lang->line("ACTION_BACK") . ' to All list',
        'href' => site_url($CI->controller_url . '/index/list_all')
    );
}
if (isset($CI->permissions['action4']) && ($CI->permissions['action4'] == 1))
{
    $action_buttons[] = array(
        'type' => 'button',
        'label' => $CI->lang->line("ACTION_PRINT"),
        'onClick' => "window.print()"
    );
}
$CI->load->view('action_buttons', array('action_buttons' => $action_buttons));

$purposes = array();
/*---------------------Purpose Array---------------------*/
$CI->db->from($CI->config->item('table_ems_tour_purpose'));
$CI->db->select('id, purpose, type');
$CI->db->where('tour_id', $item['id']);
$CI->db->where('status !=', $CI->config->item('system_status_delete'));
$result = $CI->db->get()->result_array();
if ($result)
{
    foreach ($result as $row)
    {
        $purposes[$row['id']] = $row;
    }
}
/*--------------------Reporting Array--------------------*/
$CI->db->from($CI->config->item('table_ems_tour_reporting'));
$CI->db->select('*');
$CI->db->where('tour_id', $item['id']);
$CI->db->where('status !=', $CI->config->item('system_status_delete'));
$CI->db->order_by('date_reporting', 'ASC');
$all_reporting = $CI->db->get()->result_array();

if ($all_reporting)
{
    foreach ($all_reporting as $reporting)
    {
        $purposes[$reporting['purpose_id']]['reporting'][$reporting['id']] = array(
            "date_reporting" => $reporting['date_reporting'],
            "report_description" => $reporting['report_description'],
            "recommendation" => $reporting['recommendation'],
            "name" => $reporting['name'],
            "contact_no" => $reporting['contact_no'],
            "profession" => $reporting['profession'],
            "discussion" => $reporting['discussion'],
            "image_name" => $reporting['image_name'],
            "image_location" => $reporting['image_location'],
            "date_created" => $reporting['date_created']
        );
    }
}

$is_tour_rollback = $is_reporting_rollback = FALSE;
if (($item['revision_count_rollback_tour'] > 0) && ($item['status_approved_tour'] != $CI->config->item('system_status_approved'))) // Flag for Tour Rollback
{
    $is_tour_rollback = TRUE;
}
if (($item['revision_count_rollback_reporting'] > 0) && ($item['status_approved_reporting'] != $CI->config->item('system_status_approved'))) // Flag for Reporting Rollback
{
    $is_reporting_rollback = TRUE;
}

//  For Tour Extension Details
$has_extended = '';
if ($item['status_extended_tour'] == $CI->config->item('system_status_extended'))
{
    $ext_revision_count = 0;
    $ext_data = array();
    $ext_result = $CI->db->order_by('revision_count', 'ASC')->get_where($CI->config->item('table_ems_tour_extension'), array('tour_id' => $item['tour_setup_id']))->result_array();

    $ext_revision_count = sizeof($ext_result);
    if ($ext_revision_count > 0)
    {
        foreach ($ext_result as &$data)
        {
            if ($data['revision_count'] == 1)
            {
                $ext_data = $data;
            }
            $oldest_data = $data;
        }
        $item['date_from'] = $oldest_data['date_from'];
        $item['date_to'] = $oldest_data['date_to'];
        $has_extended = " &nbsp;( <a class='ext_details text-danger' href='javascript:void()'><i>Extended</i></a> )";
    }
    $ext_user = System_helper::get_users_info(array($ext_data['user_updated']));
}

//--------Accordion View Settings----------
$show_basic_info = (isset($show_basic_info)) ? $show_basic_info : TRUE;
$show_payment_info = (isset($show_payment_info)) ? $show_payment_info : TRUE;
$show_report_purpose_info = (isset($show_report_purpose_info)) ? $show_report_purpose_info : TRUE;

?>
<style>
    .panel {border: none}

    .normal {font-weight: normal !important}

    .right-align {text-align: right !important}

    .center-align {text-align: center !important}

    span.text-danger {font-style: italic;color: #FF0000}

    .summary-wrap .show-grid, .report-wrap:last-child {margin: 0}

    .summary-wrap .show-grid:nth-child(2) > div {padding-top: 5px}

    .panel-heading {margin-top: 15px;border-top: 1px solid transparent}

    .no-wrap {width: 1%;white-space: nowrap}

    .entry_date {font-size: 0.85em; white-space: nowrap}

    td > span {color: #a94442;font-weight: bold;font-style: italic}

    .blob img {width: 300px}

    .blob {display: inline-block;padding: 3px;border: 3px solid #8c8c8c}

    .blob:hover {border: 3px solid #3693CF}
</style>

<div class="row widget">
    <div class="widget-header" style="margin:0">
        <div class="title"><?php echo $title; ?></div>
        <div class="clearfix"></div>
    </div>

    <div class="panel panel-default">
        <?php
        if($show_basic_info)
        {
        ?>
        <div class="panel-heading">
            <h4 class="panel-title">
                <label><a class="external text-danger" data-toggle="collapse" data-target="#collapse1" href="#"> + Basic Information</a></label>
            </h4>
        </div>
        <div id="collapse1" class="panel-collapse collapse in">
            <table class="table table-bordered table-responsive system_table_details_view">
            <tr>
                <td class="widget-header header_caption"><label class="control-label pull-right">Name</label></td>
                <td colspan="3">
                    <label class="control-label"><?php echo $users[$item['user_id']]['name'] . " (" . $item['employee_id'] . ")"; ?></label>
                </td>
            </tr>
            <tr>
                <td class="widget-header header_caption"><label class="control-label pull-right">Designation</label>
                </td>
                <td>
                    <label class="control-label"><?php echo ($item['designation']) ? $item['designation'] : 'N/A'; ?></label>
                </td>
                <td class="widget-header header_caption"><label class="control-label pull-right">Department</label></td>
                <td>
                    <label class="control-label"><?php echo ($item['department_name']) ? $item['department_name'] : 'N/A'; ?></label>
                </td>
            </tr>
            <tr>
                <td class="widget-header header_caption"><label class="control-label pull-right">Title</label></td>
                <td colspan="3">
                    <label class="control-label"><?php echo $item['title'] . ' ( Tour ID:' . $item['tour_setup_id'] . ' )'; ?></label>
                </td>
            </tr>
            <tr>
                <td class="widget-header header_caption">
                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE'); ?></label></td>
                <td>
                    <label class="control-label"> From <?php echo System_helper::display_date($item['date_from']); ?> &nbsp;&nbsp; To <?php echo System_helper::display_date($item['date_to']) . $has_extended;; ?>
                    </label>
                </td>
                <td class="widget-header header_caption"><label class="control-label pull-right">Duration</label></td>
                <td colspan="3">
                    <label class="control-label"><?php echo Tour_helper::tour_duration($item['date_from'], $item['date_to']); ?></label>
                </td>
            </tr>
            <tr>
                <td class="widget-header header_caption">
                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_AMOUNT_IOU_REQUEST'); ?></label>
                </td>
                <td colspan="3">
                    <label class="control-label"><?php echo System_helper::get_string_amount($item['amount_iou_request']); ?></label>
                </td>
            </tr>
            <?php if ($item['remarks'])
            {
                ?>
                <tr>
                    <td class="widget-header header_caption">
                        <label class="control-label pull-right">Remarks</label>
                    </td>
                    <td colspan="3">
                        <label class="control-label"><?php echo nl2br($item['remarks']); ?></label>
                    </td>
                </tr>
            <?php } ?>
            <tr>
                <td class="widget-header header_caption">
                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CREATED_BY'); ?></label>
                </td>
                <td><label class="control-label"><?php echo $users[$item['user_created']]['name']; ?></label></td>
                <td class="widget-header header_caption">
                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_CREATED_TIME'); ?></label>
                </td>
                <td>
                    <label class="control-label"><?php echo System_helper::display_date_time($item['date_created']); ?></label>
                </td>
            </tr>
            <?php
            if ($item['user_updated'])
            {
                ?>
                <tr>
                    <td class="widget-header header_caption">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_UPDATED_BY'); ?></label>
                    </td>
                    <td><label class="control-label"><?php echo $users[$item['user_updated']]['name']; ?></label></td>
                    <td class="widget-header header_caption">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_UPDATED_TIME'); ?></label>
                    </td>
                    <td>
                        <label class="control-label"><?php echo System_helper::display_date_time($item['date_updated']); ?></label>
                    </td>
                </tr>
            <?php
            }
            ?>
            <tr>
                <td class="widget-header header_caption">
                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_FORWARD'); ?> Status</label>
                </td>
                <td><label class="control-label"><?php echo $item['status_forwarded_tour']; ?></label></td>
                <td class="widget-header header_caption">
                    <label class="control-label pull-right">(Tour) Number of Edit</label></td>
                <td colspan="3"><label class="control-label"><?php echo($item['revision_count'] - 1); ?></label></td>
            </tr>
            <?php
            if ($item['status_forwarded_tour'] == $CI->config->item('system_status_forwarded'))
            {
                ?>
                <tr>
                    <td class="widget-header header_caption">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_FORWARDED_BY'); ?></label>
                    </td>
                    <td><label class="control-label"><?php echo $users[$item['user_forwarded_tour']]['name']; ?></label>
                    </td>
                    <td class="widget-header header_caption">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_FORWARDED_TIME'); ?></label>
                    </td>
                    <td>
                        <label class="control-label"><?php echo System_helper::display_date_time($item['date_forwarded_tour']); ?></label>
                    </td>
                </tr>
            <?php
            }
            if ($is_tour_rollback)
            {
                ?>
                <tr>
                    <td colspan="4" class="bg-danger text-danger">
                        <label class="control-label">Tour Rollback Information</label>
                    </td>
                </tr>
                <tr>
                    <td class="widget-header header_caption">
                        <label class="control-label pull-right">Remarks</label>
                    </td>
                    <td colspan="3">
                        <label class="control-label"><?php echo nl2br($item['remarks_rollback_tour']); ?></label>
                    </td>
                </tr>
                <tr>
                    <td class="widget-header header_caption">
                        <label class="control-label pull-right">Status</label>
                    </td>
                    <td><label class="control-label">Rollback</label></td>
                    <td class="widget-header header_caption">
                        <label class="control-label pull-right">(Tour) Number of Rollback</label>
                    </td>
                    <td><label class="control-label"><?php echo $item['revision_count_rollback_tour']; ?></label></td>
                </tr>
                <tr>
                    <td class="widget-header header_caption">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ROLLBACK_BY'); ?></label>
                    </td>
                    <td>
                        <label class="control-label"><?php echo $users[$item['user_rollback_tour']]['name']; ?></label>
                    </td>
                    <td class="widget-header header_caption">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_ROLLBACK_TIME'); ?></label>
                    </td>
                    <td>
                        <label class="control-label"><?php echo System_helper::display_date_time($item['date_rollback_tour']); ?></label>
                    </td>
                </tr>
            <?php
            }
            if ($item['status_approved_tour'] == $CI->config->item('system_status_approved'))
            {
                ?>
                <tr>
                    <td colspan="4" class="bg-info text-info">
                        <label class="control-label">Tour Approval Information</label>
                    </td>
                </tr>
                <tr>
                    <td class="widget-header header_caption">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_APPROVE'); ?> Status</label>
                    </td>
                    <td><label class="control-label"><?php echo $item['status_approved_tour']; ?></label></td>
                    <td class="widget-header header_caption">
                        <label class="control-label pull-right">(Tour) Number of Rollback</label>
                    </td>
                    <td><label class="control-label"><?php echo $item['revision_count_rollback_tour']; ?></label></td>
                </tr>
                <tr>
                    <td class="widget-header header_caption">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_APPROVED_BY'); ?></label>
                    </td>
                    <td><label class="control-label"><?php echo $users[$item['user_approved_tour']]['name']; ?></label>
                    </td>
                    <td class="widget-header header_caption">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_APPROVED_TIME'); ?></label>
                    </td>
                    <td>
                        <label class="control-label"><?php echo System_helper::display_date_time($item['date_approved_tour']); ?></label>
                    </td>
                </tr>
                <!--------------------------------- Tour IOU Information ----------------------------------->
                <tr>
                    <td colspan="4" class="bg-info text-info">
                        <label class="control-label">Tour IOU Payment Information</label>
                    </td>
                </tr>
                <tr>
                    <td class="widget-header header_caption">
                        <label class="control-label pull-right">IOU Approve Status</label>
                    </td>
                    <td><label class="control-label"><?php echo $item['status_approved_payment']; ?></label></td>
                    <td class="widget-header header_caption">
                        <label class="control-label pull-right">IOU Pay Status</label>
                    </td>
                    <td><label class="control-label"><?php echo $item['status_paid_payment']; ?></label></td>
                </tr>
            <?php
            }
            if (!empty($item['user_approved_payment']) && !empty($item['date_approved_payment']))
            {
                ?>
                <tr>
                    <td class="widget-header header_caption">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_APPROVED_BY'); ?></label>
                    </td>
                    <td><label class="control-label"><?php echo $users[$item['user_approved_payment']]['name']; ?></label>
                    </td>
                    <td class="widget-header header_caption">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_APPROVED_TIME'); ?></label>
                    </td>
                    <td>
                        <label class="control-label"><?php echo System_helper::display_date_time($item['date_approved_payment']); ?></label>
                    </td>
                </tr>
            <?php
            }
            if (!empty($item['user_paid_payment']) && !empty($item['date_paid_payment']))
            {
                ?>
                <tr>
                    <td class="widget-header header_caption">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PAID_BY'); ?></label>
                    </td>
                    <td><label class="control-label"><?php echo $users[$item['user_paid_payment']]['name']; ?></label>
                    </td>
                    <td class="widget-header header_caption">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_PAID_TIME'); ?></label>
                    </td>
                    <td>
                        <label class="control-label"><?php echo System_helper::display_date_time($item['date_paid_payment']); ?></label>
                    </td>
                </tr>
            <?php
            }
            if ($is_reporting_rollback)
            {
                ?>
                <tr>
                    <td colspan="4" class="bg-danger text-danger">
                        <label class="control-label">Tour Reporting Rollback Information</label>
                    </td>
                </tr>
                <tr>
                    <td class="widget-header header_caption">
                        <label class="control-label pull-right">Remarks</label>
                    </td>
                    <td colspan="3">
                        <label class="control-label"><?php echo nl2br($item['remarks_rollback_reporting']); ?></label>
                    </td>
                </tr>
                <tr>
                    <td class="widget-header header_caption">
                        <label class="control-label pull-right">Status</label>
                    </td>
                    <td><label class="control-label">Rollback</label></td>
                    <td class="widget-header header_caption">
                        <label class="control-label pull-right">(Reporting) Number of Rollback</label>
                    </td>
                    <td><label class="control-label"><?php echo $item['revision_count_rollback_reporting']; ?></label></td>
                </tr>
                <tr>
                    <td class="widget-header header_caption">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ROLLBACK_BY'); ?></label>
                    </td>
                    <td>
                        <label class="control-label"><?php echo $users[$item['user_rollback_reporting']]['name']; ?></label>
                    </td>
                    <td class="widget-header header_caption">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_ROLLBACK_TIME'); ?></label>
                    </td>
                    <td>
                        <label class="control-label"><?php echo System_helper::display_date_time($item['date_rollback_reporting']); ?></label>
                    </td>
                </tr>
            <?php
            }
            if ($item['status_approved_reporting'] == $CI->config->item('system_status_approved'))
            {
                ?>
                <tr>
                    <td colspan="4" class="bg-info text-info">
                        <label class="control-label">Tour Reporting Approval Information</label>
                    </td>
                </tr>
                <tr>
                    <td class="widget-header header_caption">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_APPROVE'); ?> Status</label>
                    </td>
                    <td><label class="control-label"><?php echo $item['status_approved_reporting']; ?></label></td>
                    <td class="widget-header header_caption">
                        <label class="control-label pull-right">(Reporting) Number of Rollback</label>
                    </td>
                    <td><label class="control-label"><?php echo $item['revision_count_rollback_reporting']; ?></label></td>
                </tr>
                <tr>
                    <td class="widget-header header_caption">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_APPROVED_BY'); ?></label>
                    </td>
                    <td><label class="control-label"><?php echo $users[$item['user_approved_reporting']]['name']; ?></label>
                    </td>
                    <td class="widget-header header_caption">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_APPROVED_TIME'); ?></label>
                    </td>
                    <td>
                        <label class="control-label"><?php echo System_helper::display_date_time($item['date_approved_reporting']); ?></label>
                    </td>
                </tr>
            <?php
            }
            if ($item['status_approved_adjustment'] != $CI->config->item('system_status_pending'))
            {
                ?>
                <tr>
                    <td colspan="4" class="bg-info text-info">
                        <label class="control-label">Tour IOU Adjustment Information</label>
                    </td>
                </tr>
                <tr>
                    <td class="widget-header header_caption">
                        <label class="control-label pull-right">IOU Adjustment Forward Status</label>
                    </td>
                    <td>
                        <label class="control-label">
                            <?php
                            if ($item['status_approved_adjustment'] != $CI->config->item('system_status_pending'))
                            {
                                echo $CI->config->item('system_status_forwarded');
                            }
                            ?>
                        </label>
                    </td>
                    <td class="widget-header header_caption">
                        <label class="control-label pull-right">IOU Adjustment Approve Status</label>
                    </td>
                    <td>
                        <label class="control-label">
                            <?php
                            if ($item['status_approved_adjustment'] == $CI->config->item('system_status_approved'))
                            {
                                echo $CI->config->item('system_status_approved');
                            }
                            else
                            {
                                echo $CI->config->item('system_status_pending');
                            }
                            ?>
                        </label>
                    </td>
                </tr>

                <tr>
                    <td class="widget-header header_caption">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_FORWARDED_BY'); ?></label>
                    </td>
                    <td><label class="control-label"><?php echo $users[$item['user_updated_adjustment']]['name']; ?></label>
                    </td>
                    <td class="widget-header header_caption">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_FORWARDED_TIME'); ?></label>
                    </td>
                    <td>
                        <label class="control-label"><?php echo System_helper::display_date_time($item['date_updated_adjustment']); ?></label>
                    </td>
                </tr>
                <?php
                if ($item['status_approved_adjustment'] == $CI->config->item('system_status_approved'))
                {
                    ?>
                    <tr>
                        <td class="widget-header header_caption">
                            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_APPROVED_BY'); ?></label>
                        </td>
                        <td><label class="control-label"><?php echo $users[$item['user_approved_adjustment']]['name']; ?></label>
                        </td>
                        <td class="widget-header header_caption">
                            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_APPROVED_TIME'); ?></label>
                        </td>
                        <td>
                            <label class="control-label"><?php echo System_helper::display_date_time($item['date_approved_adjustment']); ?></label>
                        </td>
                    </tr>
                <?php
                }
            }
            if ($item['status_extended_tour'] == $CI->config->item('system_status_extended'))
            {
                ?>
                <tr id="ext_details">
                    <td colspan="4" class="bg-info text-info">
                        <label class="control-label">Tour Extension Information</label>
                    </td>
                </tr>
                <tr>
                    <td class="widget-header header_caption">
                        <label class="control-label pull-right">Modified Tour Date</label>
                    </td>
                    <td>
                        <label class="control-label"> From <?php echo System_helper::display_date($ext_data['date_from_new']); ?> &nbsp;&nbsp; To <?php echo System_helper::display_date($ext_data['date_to_new']); ?>
                        </label>
                    </td>
                    <td class="widget-header header_caption">
                        <label class="control-label pull-right">(Tour Extension) Number of Edit</label>
                    </td>
                    <td>
                        <label class="control-label"><?php echo $oldest_data['revision_count']; ?></label>
                    </td>
                </tr>
                <tr>
                    <td class="widget-header header_caption">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_UPDATED_BY'); ?></label>
                    </td>
                    <td><label class="control-label"><?php echo $ext_user[$ext_data['user_updated']]['name']; ?></label></td>
                    <td class="widget-header header_caption">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_UPDATED_TIME'); ?></label>
                    </td>
                    <td>
                        <label class="control-label"><?php echo System_helper::display_date_time($ext_data['date_updated']); ?></label>
                    </td>
                </tr>
            <?php
            }
            ?>

            </table>
        </div>
        <?php
        }
        if($show_payment_info)
        {
        ?>
        <div class="panel-heading">
            <h4 class="panel-title">
                <label><a class="external text-danger" data-toggle="collapse" data-target="#collapse2" href="#"> + Payment Information</a></label>
            </h4>
        </div>
        <div id="collapse2" class="panel-collapse collapse">
            <table class="table table-bordered table-responsive system_table_details_view">
                <tr>
                    <td colspan="2"><label class="control-label"> Status: </label></td>
                    <td style="width:50%"><label class="control-label"> Summary: </label></td>
                </tr>
                <tr>
                    <td class="widget-header header_caption"><label class="control-label pull-right">IOU Approval</label></td>
                    <td><label class="control-label"><?php echo $item['status_approved_payment']; ?></label></td>
                    <td class="summary-wrap" rowspan="4">
                        <!-------------------------------Tour Payment Summary---------------------------------->
                        <?php
                        $iou_items = Tour_helper::get_iou_items();
                        if ($iou_items)
                        {
                            $i = 0;
                            $amount_iou_items = array();
                            $total_iou_amount = 0.0;
                            $total_voucher_amount = 0.0;

                            $amount_iou_adj_items = $amount_iou_items = json_decode($item['amount_iou_items'], TRUE);
                            if ($item['amount_iou_adjustment_items'] && ($item['amount_iou_adjustment_items'] != ''))
                            {
                                $amount_iou_adj_items = json_decode($item['amount_iou_adjustment_items'], TRUE);
                            }
                            ?>
                            <div class="row show-grid">
                                <div class="col-xs-6 right-align" style="border-bottom:1px solid #000">
                                    <label class="control-label normal"> Item&nbsp;</label>
                                </div>
                                <div class="col-xs-3 right-align" style="border-bottom:1px solid #000">
                                    <label class="control-label normal"> Requested / Paid </label>
                                </div>
                                <div class="col-xs-3 right-align" style="border-bottom:1px solid #000">
                                    <label class="control-label normal">Voucher Amount</label>
                                </div>
                            </div>
                            <?php
                            foreach ($iou_items as $key => $iou_item)
                            {
                                $current_iou_amount = $current_iou_adj_amount = 0;
                                if (isset($amount_iou_items[$key]))
                                {
                                    $current_iou_amount = $amount_iou_items[$key];
                                }
                                if (isset($amount_iou_adj_items[$key]))
                                {
                                    $current_iou_adj_amount = $amount_iou_adj_items[$key];
                                }
                                if (($iou_item['status'] == $CI->config->item('system_status_inactive')) && !($current_iou_amount > 0))
                                {
                                    continue;
                                }
                                ?>
                                <div class="row show-grid">
                                    <div class="col-xs-6">
                                        <label class="control-label pull-right normal"><?php echo $iou_item['name']; ?>:</label>
                                    </div>
                                    <div class="col-xs-3" style="padding-left:0">
                                        <label class="control-label pull-right"><?php echo(System_helper::get_string_amount($current_iou_amount)); ?></label>
                                    </div>
                                    <div class="col-xs-3">
                                        <label class="control-label pull-right"><?php echo(System_helper::get_string_amount($current_iou_adj_amount)); ?></label>
                                    </div>
                                </div>
                                <?php
                                $total_iou_amount += $current_iou_amount;
                                $total_voucher_amount += $current_iou_adj_amount;
                            }
                            ?>
                            <div class="row show-grid">
                                <div class="col-xs-6" style="border-top:1px solid #000; padding-top:5px">
                                    <label class="control-label pull-right normal">Total:</label>
                                </div>
                                <div class="col-xs-3 right-align" style="border-top:1px solid #000; padding-top:5px; padding-left:0;">
                                    <label class="control-label"><?php echo System_helper::get_string_amount($total_iou_amount); ?></label>
                                </div>
                                <div class="col-xs-3 right-align" style="border-top:1px solid #000; padding-top:5px; padding-left:0;">
                                    <label class="control-label voucher_amount"><?php echo System_helper::get_string_amount($total_voucher_amount); ?></label>
                                </div>
                            </div>

                            <?php
                            if ($item['amount_iou_adjustment_items'] && ($item['amount_iou_adjustment_items'] != ''))
                            {
                                ?>
                                <div class="row show-grid">
                                    <div class="col-xs-6">
                                        <label class="control-label pull-right normal">
                                            <?php
                                            if ($item['status_approved_adjustment'] == ($CI->config->item('system_status_approved')))
                                            {
                                                echo '( <b>' . $CI->config->item('system_status_approved') . '</b> )';
                                            }
                                            elseif ($item['status_approved_adjustment'] == ($CI->config->item('system_status_forwarded')))
                                            {
                                                echo '( <b>' . $CI->config->item('system_status_forwarded') . '</b> )';
                                            }
                                            ?>
                                            Adjustment: </label>
                                    </div>
                                    <div class="col-xs-6 right-align" style="padding-left:0;">
                                        <label class="control-label adjustment_amount">
                                            <?php
                                            $adj_amt = $total_voucher_amount - $total_iou_amount;
                                            if ($adj_amt > 0)
                                            {
                                                $note = "(Pay To Employee)";
                                            }
                                            else if ($adj_amt < 0)
                                            {
                                                $note = "(Return To Accounts)";
                                            }
                                            else
                                            {
                                                $note = "(No Adjustment Needed)";
                                            }
                                            $note = '<span class="normal">' . $note . '</span> &nbsp;';
                                            echo $note . System_helper::get_string_amount(abs($adj_amt));
                                            ?>
                                        </label>
                                    </div>
                                </div>
                            <?php
                            }
                        }
                        ?>
                        <!------------------------------------------------------------------------------------->
                    </td>
                </tr>
                <tr>
                    <td class="widget-header header_caption"><label class="control-label pull-right">IOU Payment</label></td>
                    <td><label class="control-label"><?php echo $item['status_paid_payment']; ?></label></td>
                </tr>
                <tr>
                    <td class="widget-header header_caption"><label class="control-label pull-right">IOU Adjustment</label></td>
                    <td>
                        <label class="control-label">
                            <?php
                            if ($item['status_approved_adjustment'] != ($CI->config->item('system_status_pending')))
                            {
                                echo $CI->config->item('system_status_forwarded');
                            }
                            else
                            {
                                echo $CI->config->item('system_status_pending');
                            }
                            ?>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td class="widget-header header_caption">
                        <label class="control-label pull-right">IOU Adjustment Approval</label></td>
                    <td>
                        <label class="control-label">
                            <?php
                            if ($item['status_approved_adjustment'] != ($CI->config->item('system_status_approved')))
                            {
                                echo $CI->config->item('system_status_pending');
                            }
                            else
                            {
                                echo $CI->config->item('system_status_approved');
                            }
                            ?>
                        </label>
                    </td>
                </tr>
            </table>
        </div>
        <?php
        }
        if($show_report_purpose_info)
        {
        ?>
        <div class="panel-heading">
            <h4 class="panel-title">
                <label><a class="external text-danger" data-toggle="collapse" data-target="#collapse3" href="#"> + Report &amp; Purpose Information</a></label>
            </h4>
        </div>
        <div id="collapse3" class="panel-collapse collapse">
            <table class="table table-bordered table-responsive system_table_details_view">
                <tr>
                    <td class="center-align" style="width:20%"><label class="control-label"> Purpose </label></td>
                    <td class="center-align"><label class="control-label"> Report </label></td>
                </tr>
                <?php foreach ($purposes as $purpose)
                {
                    ?>
                    <tr>
                        <td>
                            <label class="control-label"> <?php echo $purpose['purpose']; ?> </label>
                            <?php if ($purpose['type'] && ($purpose['type'] == $CI->config->item('system_status_additional')))
                            {
                                echo '<br/>(<span>Additional</span>)';
                            } ?>
                        </td>
                        <td>
                            <?php
                            if (isset($purpose['reporting']) && !empty($purpose['reporting']))
                            {
                                foreach ($purpose['reporting'] as $report)
                                {
                                    ?>
                                    <table class="table table-bordered report-wrap">
                                        <tr>
                                            <td rowspan="6" style="width:17%">
                                                <b><?php echo System_helper::display_date($report['date_reporting']) ?></b>
                                                <br/><i class="entry_date">( Entry Date &amp; Time:<br/><?php echo System_helper::display_date_time($report['date_created']); ?> )</i>
                                            </td>
                                            <td class="no-wrap"><label class="control-label"> Report (Description) </label></td>
                                            <td colspan="3"><?php echo nl2br($report['report_description']); ?></td>
                                        </tr>

                                        <tr>
                                            <td><label class="control-label"> Recommendation </label></td>
                                            <td colspan="3"><?php echo nl2br($report['recommendation']); ?></td>
                                        </tr>
                                        <?php
                                        if (trim($report['name']) != "")
                                        {
                                            ?>
                                            <tr>
                                                <td><label class="control-label"> Contact Name </label></td>
                                                <td colspan="3"><?php echo $report['name']; ?></td>
                                            </tr>
                                        <?php
                                        }
                                        if ((trim($report['name']) != "") && (trim($report['contact_no']) != "") || (trim($report['profession']) != ""))
                                        {
                                            ?>
                                            <tr>
                                                <td><label class="control-label"> Phone No. </label></td>
                                                <td style="width:22%">
                                                    <?php echo (trim($report['contact_no']) != "") ? $report['contact_no'] : '-'; ?>
                                                </td>
                                                <td class="no-wrap"><label class="control-label"> Profession </label></td>
                                                <td>
                                                    <?php echo (trim($report['profession']) != "") ? $report['profession'] : '-'; ?>
                                                </td>
                                            </tr>
                                        <?php
                                        }
                                        if ((trim($report['name']) != "") && (trim($report['discussion']) != ""))
                                        {
                                            ?>
                                            <tr>
                                                <td><label class="control-label"> Discussion </label></td>
                                                <td colspan="3"><?php echo nl2br($report['discussion']); ?></td>
                                            </tr>
                                        <?php
                                        }
                                        $img_src = $CI->config->item('system_base_url_picture') . $report['image_location'];
                                        ?>
                                        <tr>
                                            <td><label class="control-label"> Picture </label></td>
                                            <td colspan="3">
                                                <a href="<?php echo $img_src; ?>" target="_blank" class="external blob"><img src="<?php echo $img_src; ?>" alt="No Image Found"/></a>
                                            </td>
                                        </tr>
                                    </table>
                                <?php
                                }
                            }
                            else
                            {
                                echo "- <i>No Reporting Done Yet, for this Purpose</i> -";
                            }
                            ?>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </div>
        <?php
        }
        ?>
    </div>
</div>

<script type="application/javascript">
    jQuery(document).ready(function ($) {
        system_off_events(); // Triggers

        $('a.ext_details').click(function (e) {
            e.preventDefault();
            var $anchor = $('tr#ext_details').offset();
            window.scrollTo($anchor.left, $anchor.top);
            return false;
        });
    });
</script>
