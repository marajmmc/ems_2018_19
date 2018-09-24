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
<style>
    .datepicker {
        cursor: pointer !important;
    }

    label {
        margin-top: 5px
    }

    #purpose-wrap tr td:last-child {
        width: 1% !important;
    }

    .right-align {
        text-align: right !important;
    }

    .iou-table tr td {
        padding: 5px;
    }
    label.normal{font-weight:normal !important}
    .label-danger{
        font-size: 14px !important;
        font-weight: normal !important;
        line-height: 20px !important;
    }
</style>
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
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo ($item['designation']) ? $item['designation'] : 'N/A'; ?></label>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Department:</label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo ($item['department_name']) ? $item['department_name'] : 'N/A'; ?></label>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Title:</label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo ($item['title']) ? $item['title'] : 'N/A'; ?></label>
            </div>
        </div>

        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE') . ' From'; ?>
                    <span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" id="from_date" name="item[date_from_new]" class="form-control datepicker" value="<?php echo System_helper::display_date($item['date_from']); ?>" readonly/>
            </div>
        </div>

        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE') . ' To'; ?>
                    <span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" id="to_date" name="item[date_to_new]" class="form-control datepicker" value="<?php echo System_helper::display_date($item['date_to']); ?>" readonly/>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Current Duration:</label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo Tour_helper::tour_duration($item['date_from'], $item['date_to']); ?></label>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">New Duration:</label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label new_duration">
                    <?php echo Tour_helper::tour_duration($item['date_from'], $item['date_to']); ?>
                </label>
            </div>
        </div>

        <?php echo Tour_helper::tour_purpose_view($item['tour_setup_id']); ?>

        <?php //echo Tour_helper::iou_items_summary_view('', $item); ?>

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

    </div>

    <div class="clearfix"></div>
</form>

<div id="system_content_add_more" style="display:none;">
    <table>
        <tbody>
        <tr>
            <td>
                <input type="text" class="form-control purpose"/>
            </td>
            <td style="width:1%">
                <button type="button" class="btn btn-danger btn-sm system_button_add_delete"><?php echo $CI->lang->line('DELETE'); ?></button>
            </td>
        </tr>
        </tbody>
    </table>
</div>

<script type="text/javascript">

    jQuery(document).ready(function ($) {
        $(".datepicker").datepicker({dateFormat: display_date_format});

        $(document).on("change keyup", ".datepicker", function (event) {
            var date1 = new Date($("#from_date").val());
            var date2 = new Date($("#to_date").val());
            var timeDiff = date2.getTime() - date1.getTime();
            var diffDays = Math.ceil(timeDiff / (1000 * 3600 * 24));
            if(timeDiff < 0){
                $(".new_duration").html('<span class="label label-danger">'+(diffDays-1) + " Day(s)</span>");
            }else{
                $(".new_duration").html((diffDays+1) + " Day(s)");
            }
        });

        $(document).on("click", ".system_button_add_more", function (event) {
            var current_id = parseInt($(this).attr('data-current-id'));
            current_id = current_id + 1;
            $(this).attr('data-current-id', current_id);
            var content_id = '#system_content_add_more table tbody';
            $(content_id + ' .purpose').attr('name', 'items[' + current_id + ']');
            var html = $(content_id).html();
            $("#tour_setup_container tbody tr.purpose-addMore").before(html);
        });

        $(document).on("click", ".system_button_add_delete", function (event) {
            $(this).closest('tr').remove();
        });

        $(document).on("change keyup", ".iou_item_input", function (event) {
            var sum = parseFloat(0);
            var item_amount = parseFloat(0);
            $(".iou_item_input").each(function (e) {
                item_amount = parseFloat($(this).val());
                if (!isNaN(item_amount) && (item_amount > 0)) {
                    sum += item_amount;
                }
            });
            $(".amount_iou_label").text(get_string_amount(sum));
        });

        $(document).on("blur", ".iou_item_input", function (event) { // Puts a Zero if blank
            var iou_value = parseFloat($(this).val());
            if (iou_value == '' || isNaN(iou_value)) {
                $(this).val('0');
            }
        });
    });

</script>
