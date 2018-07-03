<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();
$action_buttons = array();
$action_buttons[] = array(
    'label' => $CI->lang->line("ACTION_BACK"),
    'href' => site_url($CI->controller_url . '/index/list_reporting/' . $item['tour_id']));
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
    .datepicker {cursor: pointer !important; }
    label{margin-top:5px}
    .delete-btn-wrap{text-align:right; padding:0}
    .delete-btn-wrap button{font-size:1.5em;}
    .reporting{background:lightgrey; padding-top:10px; margin-bottom:20px}
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
                <label class="control-label pull-right"><?php echo 'Tour '.$CI->lang->line('LABEL_DATE'); ?>:</label>
            </div>
            <div class="col-sm-4 col-xs-8">
                From &nbsp;<label class="control-label"><?php echo System_helper::display_date($item['date_from']) ?></label> &nbsp;
                To &nbsp;<label class="control-label"><?php echo System_helper::display_date($item['date_to']) ?></label>
            </div>
        </div>

        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo 'Reporting ' . $CI->lang->line('LABEL_DATE'); ?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo System_helper::display_date($reporting_date); ?></label>
                <input type="hidden" name="item[date_reporting]" value="<?php echo $reporting_date; ?>"/>
            </div>
        </div>

        <div id="tour_setup_container" style="overflow-x: auto;">
            <div class="col-xs-12 widget-header" style="font-size:1.2em; margin-bottom:0; border-top:1px solid #cfcfcf">
                <label class="control-label" style="margin:0">Reporting ( <?php echo System_helper::display_date($reporting_date); ?> )</label>
            </div>

            <div class="col-xs-12 reporting">
                <div class="row show-grid">
                    <div class="col-xs-4">
                        <label class="control-label pull-right">Purpose<span style="color:#FF0000">*</span></label>
                    </div>
                    <div class="col-xs-3">
                        <select class="form-control content-purpose" name="items[0][purpose]">
                            <option value=""><?php echo $this->lang->line('SELECT'); ?></option>
                            <?php
                            if ($item['purposes'])
                            {
                                foreach ($item['purposes'] as $row)
                                {
                                    ?><option value="<?php echo $row['id']; ?>"><?php echo $row['purpose']; ?></option><?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-xs-1" style="text-align:center">
                        <label class="control-label">- OR -</label>
                    </div>
                    <div class="col-xs-3">
                        <input type="text" class="form-control content-purpose-additional" name="items[0][purpose_additional]" placeholder="Enter New Purpose" />
                    </div>
                    <div class="col-xs-1 delete-btn-wrap">
                        <button class="btn btn-sm btn-danger system_button_add_delete" title="Delete">X</button>
                    </div>
                </div>

                <div class="row show-grid">
                    <div class="col-xs-4">
                        <label class="control-label pull-right">Report (Description)<span style="color:#FF0000">*</span></label>
                    </div>
                    <div class="col-xs-4">
                        <textarea class="form-control content-report-description" name="items[0][report_description]"></textarea>
                    </div>
                </div>

                <div class="row show-grid">
                    <div class="col-xs-4">
                        <label class="control-label pull-right">Recommendation<span style="color:#FF0000">*</span></label>
                    </div>
                    <div class="col-xs-4">
                        <textarea class="form-control content-recommendation" name="items[0][recommendation]"></textarea>
                    </div>
                </div>

                <div class="row show-grid">
                    <div class="col-xs-4">
                        <label class="control-label pull-right">Contact person (If any)</label>
                    </div>
                    <div class="col-xs-4">
                        <input type="text" class="form-control content-other-name" name="items[0][other_name]"/>
                    </div>
                </div>

                <div class="row show-grid">
                    <div class="col-xs-4">
                        <label class="control-label pull-right">Contact No. (If any)</label>
                    </div>
                    <div class="col-xs-4">
                        <input type="text" class="form-control content-other-contact" name="items[0][other_contact]"/>
                    </div>
                </div>

                <div class="row show-grid">
                    <div class="col-xs-4">
                        <label class="control-label pull-right">Profession (If any)</label>
                    </div>
                    <div class="col-xs-4">
                        <input type="text" class="form-control content-other-profession" name="items[0][other_profession]"/>
                    </div>
                </div>

                <div class="row show-grid">
                    <div class="col-xs-4">
                        <label class="control-label pull-right">Discussion (If any)</label>
                    </div>
                    <div class="col-xs-4">
                        <textarea class="form-control content-other-discussion" name="items[0][other_discussion]"></textarea>
                    </div>
                </div>
            </div>

        </div>

        <div class="row show-grid">
            <div class="col-xs-12">
                <button type="button" class="btn btn-warning system_button_add_more pull-right" data-current-id="0"><?php echo $CI->lang->line('LABEL_ADD_MORE'); ?></button>
            </div>
        </div>
    </div>

    <div class="clearfix"></div>
</form>

<!-------------------------------------------JUST FOR COPYING----------------------------------------------------->
<div id="system_content_add_more" style="display: none;">

    <div class="col-xs-12 reporting">
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Purpose<span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-xs-3">
                <select class="form-control content-purpose">
                    <option value=""><?php echo $this->lang->line('SELECT'); ?></option>
                    <?php
                    if ($item['purposes'])
                    {
                        foreach ($item['purposes'] as $row)
                        {
                            ?><option value="<?php echo $row['id']; ?>"><?php echo $row['purpose']; ?></option><?php
                        }
                    }
                    ?>
                </select>
            </div>
            <div class="col-xs-1" style="text-align:center">
                <label class="control-label">- OR -</label>
            </div>
            <div class="col-xs-3">
                <input type="text" class="form-control content-purpose-additional" placeholder="Enter New Purpose" />
            </div>
            <div class="col-xs-1 delete-btn-wrap">
                <button class="btn btn-sm btn-danger system_button_add_delete" title="Delete">X</button>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Report (Description)<span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-xs-4">
                <textarea class="form-control content-report-description"></textarea>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Recommendation<span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-xs-4">
                <textarea class="form-control content-recommendation"></textarea>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Contact person (If any)</label>
            </div>
            <div class="col-xs-4">
                <input type="text" class="form-control content-other-name" />
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Contact No. (If any)</label>
            </div>
            <div class="col-xs-4">
                <input type="text" class="form-control content-other-contact" />
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Profession (If any)</label>
            </div>
            <div class="col-xs-4">
                <input type="text" class="form-control content-other-profession" />
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Discussion (If any)</label>
            </div>
            <div class="col-xs-4">
                <textarea class="form-control content-other-discussion"></textarea>
            </div>
        </div>
    </div>

</div>
<!----------------------------------------JUST FOR COPYING (END)-------------------------------------------------->


<script type="text/javascript">

    jQuery(document).ready(function () {
        $(".datepicker").datepicker({dateFormat: display_date_format});

        $(document).off("click", ".system_button_add_more");
        $(document).off("click", ".system_button_add_delete");

        $(document).on("click", ".system_button_add_more", function (event) {
            var content_id = '#system_content_add_more';

            var current_id = parseInt($(this).attr('data-current-id'));
            current_id = current_id + 1;
            $(this).attr('data-current-id', current_id);

            $(content_id + ' .content-purpose').attr('name', 'items['+ current_id +'][purpose]');
            $(content_id + ' .content-purpose-additional').attr('name', 'items['+ current_id +'][purpose_additional]');
            $(content_id + ' .content-report-description').attr('name', 'items['+ current_id +'][report_description]');
            $(content_id + ' .content-recommendation').attr('name', 'items['+ current_id +'][recommendation]');
            $(content_id + ' .content-other-name').attr('name', 'items['+ current_id +'][other_name]');
            $(content_id + ' .content-other-contact').attr('name', 'items['+ current_id +'][other_contact]');
            $(content_id + ' .content-other-profession').attr('name', 'items['+ current_id +'][other_profession]');
            $(content_id + ' .content-other-discussion').attr('name', 'items['+ current_id +'][other_discussion]');


            var content = $(content_id).html();
            $("#tour_setup_container").append(content);
        });
        $(document).on("click", ".system_button_add_delete", function (event) {
            event.preventDefault();
            $(this).closest('div.reporting').remove();
        });
    });

    /* jQuery(document).ready(function () {
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
    }); */

</script>
