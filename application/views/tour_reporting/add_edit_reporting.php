<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();
$action_buttons = array();
$action_buttons[] = array(
    'label' => $CI->lang->line("ACTION_BACK"),
    'href' => site_url($CI->controller_url . '/index/list_reporting/' . $item['tour_setup_id']));
$action_buttons[] = array(
    'type' => 'button',
    'label' => $CI->lang->line("ACTION_SAVE"),
    'id' => 'button_action_save',
    'data-form' => '#save_form'
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
    .datepicker {
        cursor: pointer !important;
    }
</style>
<form class="form_valid" id="save_form" action="<?php echo site_url($CI->controller_url . '/index/save_reporting'); ?>" method="post">

    <input type="hidden" id="id" name="id" value="<?php echo $item['id']; ?>"/>
    <input type="hidden" id="system_save_new_status" name="system_save_new_status" value="0"/>

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
                <label class="control-label">
                    <?php if ($item['designation'])
                    {
                        echo $item['designation'];
                    }
                    else
                    {
                        echo 'N/A';
                    } ?>
                </label>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Department:</label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label">
                    <?php if ($item['department_name'])
                    {
                        echo $item['department_name'];
                    }
                    else
                    {
                        echo 'N/A';
                    } ?>
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
                <label class="control-label">From: <?php echo System_helper::display_date($item['date_from']) ?>
                    To: <?php echo System_helper::display_date($item['date_to']) ?></label>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Purpose:</label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $item['purpose'] ?></label>
            </div>
        </div>

        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo 'Reporting ' . $CI->lang->line('LABEL_DATE'); ?><span
                        style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="item[date_reporting]" class="form-control datepicker"
                       value="<?php echo System_helper::display_date($item['date_reporting']); ?>" readonly/>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Report (Description)<span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-xs-4">
                <textarea id="report_description" name="item[report_description]"
                          class="form-control"><?php echo $item['report_description'] ?></textarea>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Recommendation<span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-xs-4">
                <textarea id="recommendation" name="item[recommendation]"
                          class="form-control"><?php echo $item['recommendation'] ?></textarea>
            </div>
        </div>

        <div id="tour_setup_container">
            <div style="overflow-x: auto;" class="row show-grid">
                <div class="col-xs-4">

                </div>
                <div class="col-sm-12 col-xs-12">
                    <table class="table table-bordered">

                        <thead>
                        <tr>
                            <th class="widget-header text-center" colspan="5">Others Information</th>
                        </tr>
                        <tr>
                            <th style="min-width: 150px;">Name <span style="color:#FF0000">*</span></th>
                            <th style="min-width: 150px;">Contact No.</th>
                            <th style="min-width: 150px;">Profession</th>
                            <th colspan="2" style="min-width: 150px;">Discussion</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        foreach ($items as $item_reporting)
                        {
                            ?>
                            <tr>
                                <td>
                                    <input type="text" name="old_items[<?php echo $item_reporting['id']; ?>][name]"
                                           class="form-control name" value="<?php echo $item_reporting['name']; ?>"/>
                                </td>
                                <td>
                                    <input type="text"
                                           name="old_items[<?php echo $item_reporting['id']; ?>][contact_no]"
                                           class="form-control float_type_positive contact_no"
                                           value="<?php echo $item_reporting['contact_no']; ?>"/>
                                </td>
                                <td>
                                    <input type="text"
                                           name="old_items[<?php echo $item_reporting['id']; ?>][profession]"
                                           class="form-control profession"
                                           value="<?php echo $item_reporting['profession']; ?>"/>
                                </td>
                                <td>
                                    <textarea rows="1" class="form-control discussion"
                                              name="old_items[<?php echo $item_reporting['id']; ?>][discussion]"><?php echo $item_reporting['discussion']; ?></textarea>
                                </td>
                                <td>
                                    <button type="button"
                                            class="btn btn-danger btn-sm system_button_add_delete"><?php echo $CI->lang->line('DELETE'); ?></button>
                                </td>
                            </tr>
                        <?php
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">

            </div>
            <div class="col-xs-4 col-xs-8">
                <button type="button" class="btn btn-warning system_button_add_more pull-right"
                        data-current-id="0"><?php echo $CI->lang->line('LABEL_ADD_MORE'); ?></button>
            </div>
            <div class="col-xs-4">

            </div>
        </div>
    </div>

    <div class="clearfix"></div>
</form>

<div id="system_content_add_more" style="display: none;">
    <table>
        <tbody>
        <tr>
            <td>
                <input type="text" class="form-control name"/>
            </td>
            <td>
                <input type="text" class="form-control float_type_positive contact_no"/>
            </td>
            <td>
                <input type="text" class="form-control profession"/>
            </td>
            <td>
                <textarea rows="1" class="form-control discussion"></textarea>
            </td>
            <td>
                <button type="button"
                        class="btn btn-danger btn-sm system_button_add_delete"><?php echo $CI->lang->line('DELETE'); ?></button>
            </td>
        </tr>
        </tbody>
    </table>
</div>

<script type="text/javascript">

    jQuery(document).ready(function () {
        $(".datepicker").datepicker({dateFormat: display_date_format});

        $(document).off("click", ".system_button_add_more");
        $(document).off("click", ".system_button_add_delete");

        $(document).on("click", ".system_button_add_more", function (event) {
            var current_id = parseInt($(this).attr('data-current-id'));
            current_id = current_id + 1;
            $(this).attr('data-current-id', current_id);
            var content_id = '#system_content_add_more table tbody';
            $(content_id + ' .name').attr('name', 'items[' + current_id + '][name]');
            $(content_id + ' .contact_no').attr('name', 'items[' + current_id + '][contact_no]');
            $(content_id + ' .profession').attr('name', 'items[' + current_id + '][profession]');
            $(content_id + ' .discussion').attr('name', 'items[' + current_id + '][discussion]');
            var html = $(content_id).html();
            $("#tour_setup_container tbody").append(html);
        });
        $(document).on("click", ".system_button_add_delete", function (event) {
            $(this).closest('tr').remove();
        });

    });
</script>
