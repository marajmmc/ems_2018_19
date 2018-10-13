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

$purposes = array();
/*---------------------Purpose Array---------------------*/
$CI->db->from($CI->config->item('table_ems_tour_purpose'));
$CI->db->select('id, purpose, type');
$CI->db->where('tour_id', $item['tour_setup_id']);
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
$CI->db->where('tour_id', $item['tour_setup_id']);
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

$is_rollback = FALSE;
if (($item['revision_count_rollback_reporting'] > 0) && ($item['status_approved_reporting'] != $CI->config->item('system_status_approved'))) // Flag for Rollback
{
    $is_rollback = TRUE;
}
?>
<style>
    .purpose-list table tr td:first-child {
        width: 50px
    }

    .panel {
        border: none;
    }

    label.normal {
        font-weight: normal !important
    }

    .tour-list span {
        display: inline-block;
        padding: 0 5px;
        margin-right: 8px;
        background: #d7d7d7;
    }

    span.text-danger {
        font-style: italic;
        color: #FF0000
    }
    .no-wrap {
        width: 1%;
        white-space: nowrap
    }
    .entry_date{font-size:0.85em; white-space:nowrap}
    .blob img{width:300px}
    .blob {
        display:inline-block;
        padding:3px;
        border:3px solid #8c8c8c
    }
    .blob:hover{border:3px solid #3693CF}
</style>

<div class="row widget">

<div class="widget-header" style="margin:0">
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
    <div id="collapse1" class="panel-collapse collapse <?php echo ($is_rollback) ? 'in' : ''; ?>">
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
                <th colspan="3">
                    <label class="control-label"><?php echo $item['title'] . ' ( Tour ID:' . $item['tour_setup_id'] . ' )'; ?></label>
                </th>
            </tr>
            <tr>
                <th class="widget-header header_caption">
                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE'); ?></label></th>
                <th>
                    <label class="control-label"> From <?php echo System_helper::display_date($item['date_from']) ?>
                        To <?php echo System_helper::display_date($item['date_to']) ?>
                    </label>
                </th>
                <th class="widget-header header_caption"><label class="control-label pull-right">Duration</label></th>
                <th colspan="3">
                    <label class="control-label"><?php echo Tour_helper::tour_duration($item['date_from'], $item['date_to']); ?></label>
                </th>
            </tr>
            <tr>
                <th class="widget-header header_caption">
                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_AMOUNT_IOU_REQUEST'); ?></label>
                </th>
                <th colspan="3">
                    <label class="control-label"><?php echo System_helper::get_string_amount($item['amount_iou_request']); ?></label>
                </th>
            </tr>

            <?php if ($item['remarks']) { ?>
            <tr>
                <th class="widget-header header_caption">
                    <label class="control-label pull-right">Remarks</label>
                </th>
                <th colspan="3">
                    <label class="control-label"><?php echo nl2br($item['remarks']); ?></label>
                </th>
            </tr>
            <?php } ?>

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
                    <label class="control-label pull-right">(Tour) Number of Edit</label></th>
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
                    <th colspan="4" class="bg-info text-info">Tour Approval Information</th>
                </tr>
                <tr>
                    <th class="widget-header header_caption">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_APPROVE'); ?> Status</label>
                    </th>
                    <th><label class="control-label"><?php echo $item['status_approved_tour']; ?></label></th>
                    <th class="widget-header header_caption">
                        <label class="control-label pull-right">(Tour) Number of Rollback</label>
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
            <?php
            }
            ?>
            <!--------------------------------- Tour IOU Information ----------------------------------->
            <tr>
                <th colspan="4" class="bg-info text-info">Tour IOU Payment Information</th>
            </tr>
            <tr>
                <th class="widget-header header_caption">
                    <label class="control-label pull-right">IOU Approve Status</label>
                </th>
                <th><label class="control-label"><?php echo $item['status_approved_payment']; ?></label></th>
                <th class="widget-header header_caption">
                    <label class="control-label pull-right">IOU Pay Status</label>
                </th>
                <th><label class="control-label"><?php echo $item['status_paid_payment']; ?></label></th>
            </tr>
            <?php
            if (!empty($item['user_approved_payment']) && !empty($item['date_approved_payment']))
            {
                ?>
                <tr>
                    <th class="widget-header header_caption">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_APPROVED_BY'); ?></label>
                    </th>
                    <th><label class="control-label"><?php echo $users[$item['user_approved_payment']]['name']; ?></label>
                    </th>
                    <th class="widget-header header_caption">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_APPROVED_TIME'); ?></label>
                    </th>
                    <th>
                        <label class="control-label"><?php echo System_helper::display_date_time($item['date_approved_payment']); ?></label>
                    </th>
                </tr>
            <?php
            }
            if (!empty($item['user_paid_payment']) && !empty($item['date_paid_payment']))
            {
                ?>
                <tr>
                    <th class="widget-header header_caption">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PAID_BY'); ?></label>
                    </th>
                    <th><label class="control-label"><?php echo $users[$item['user_paid_payment']]['name']; ?></label>
                    </th>
                    <th class="widget-header header_caption">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_PAID_TIME'); ?></label>
                    </th>
                    <th>
                        <label class="control-label"><?php echo System_helper::display_date_time($item['date_paid_payment']); ?></label>
                    </th>
                </tr>
            <?php
            }
            if ($is_rollback)
            {
                ?>
                <tr>
                    <th colspan="4" class="bg-danger text-danger">Tour Reporting Rollback Information</th>
                </tr>
                <tr>
                    <th class="widget-header header_caption">
                        <label class="control-label pull-right">Remarks</label>
                    </th>
                    <th colspan="3">
                        <label class="control-label"><?php echo nl2br($item['remarks_rollback_reporting']); ?></label>
                    </th>
                </tr>
                <tr>
                    <th class="widget-header header_caption">
                        <label class="control-label pull-right">Status</label>
                    </th>
                    <th><label class="control-label">Rollback</label></th>
                    <th class="widget-header header_caption">
                        <label class="control-label pull-right">(Reporting) Number of Rollback</label>
                    </th>
                    <th><label class="control-label"><?php echo $item['revision_count_rollback_reporting']; ?></label></th>
                </tr>
                <tr>
                    <th class="widget-header header_caption">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ROLLBACK_BY'); ?></label>
                    </th>
                    <th>
                        <label class="control-label"><?php echo $users[$item['user_rollback_reporting']]['name']; ?></label>
                    </th>
                    <th class="widget-header header_caption">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_ROLLBACK_TIME'); ?></label>
                    </th>
                    <th>
                        <label class="control-label"><?php echo System_helper::display_date_time($item['date_rollback_reporting']); ?></label>
                    </th>
                </tr>
            <?php
            }
            ?>
        </table>
    </div>


    <div class="panel-heading">
        <h4 class="panel-title">
            <label><a class="external text-danger" data-toggle="collapse" data-target="#collapse5" href="#"> + Report Details</a></label>
        </h4>
    </div>
    <div id="collapse5" class="panel-collapse collapse">
        <table class="table table-bordered table-responsive system_table_details_view">
            <tr>
                <td class="center-align" style="width:20%"><label class="control-label"> Purpose </label></td>
                <td class="center-align"><label class="control-label"> Report </label></td>
            </tr>
            <?php
            foreach ($purposes as $purpose)
            {
                ?>
                <tr>
                    <td><label class="control-label"> <?php echo $purpose['purpose']; ?> </label></td>
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
                                    $img_src = $this->config->item('system_base_url_picture') . $report['image_location'];
                                    ?>
                                    <tr>
                                        <td><label class="control-label"> Picture </label></td>
                                        <td colspan="3"><a href="<?php echo $img_src; ?>" target="_blank" class="external blob"><img src="<?php echo $img_src; ?>" alt="Image Missing" /></a></td>
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

    <div class="clearfix"></div>
</div>

<div class="row show-grid">
    <div class="col-xs-12">
        <table class="table table-bordered">
            <thead>
            <tr>
                <th><?php echo $this->lang->line('LABEL_SL_NO'); ?></th>
                <th>Reporting Date</th>
                <th>Purpose(s)</th>
            </tr>
            </thead>
            <tbody class="tour-list">
            <?php
            if ($items)
            {
                $i = 0;
                foreach ($items as $row)
                {
                    ?>
                    <tr>
                        <td><?php echo ++$i; ?></td>
                        <td><?php echo $row['date_reporting']; ?></td>
                        <td>
                            <?php
                            $purpose_list = explode(';', $row['purpose']);
                            foreach ($purpose_list AS $key => $purpose)
                            {
                                if (trim($purpose) != "-")
                                {
                                    $purpose = '<span>' . $purpose . '</span>';
                                }
                                echo $purpose;
                            }
                            ?>
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
                <select name="item[status_forwarded_reporting]" class="form-control status-combo">
                    <option value=""><?php echo $this->lang->line('SELECT'); ?></option>
                    <option value="<?php echo $this->config->item('system_status_forwarded'); ?>">Forward</option>
                </select>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Remarks</label>
            </div>
            <div class="col-xs-4">
                <textarea name="item[remarks_forwarded_reporting]" class="form-control"><?php echo $item['remarks_forwarded_reporting'] ?></textarea>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-8">
                <div class="action_button pull-right" style="margin:0">
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
        system_off_events(); // Triggers

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
