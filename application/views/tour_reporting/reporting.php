<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();
$action_buttons = array();
$action_buttons[] = array(
    'label' => $CI->lang->line("ACTION_BACK"),
    'href' => site_url($CI->controller_url . '/index/list_reporting/' . $item['tour_id'])
);
$action_buttons[] = array(
    'type' => 'button',
    'label' => $CI->lang->line("ACTION_CLEAR"),
    'id' => 'button_action_clear',
    'data-form' => '#save_form'
);
$CI->load->view('action_buttons', array('action_buttons' => $action_buttons));

$img_width = 300;
?>
<style>
    .integer_type_positive {
        text-align: left !important;
    }

    label {
        margin-top: 5px
    }

    .panel {
        border: none
    }

    .delete-btn-wrap {
        text-align: right;
        padding: 0
    }

    .delete-btn-wrap button {
        font-size: 1.5em;
    }

    .badge {
        display: none !important
    }

    .blob img {
        display:inline-block;
        border:2px dashed #8c8c8c;
        padding:2px;
    }

    .reporting {
        background: #d3d3d3;
        padding-top: 15px;
        padding-bottom: 15px;
        margin-bottom: 10px
    }

    .wrap-additional {
        position: relative
    }

    .wrap-additional span {
        display: none;
        color: #FF0000;
        position: absolute;
        top: 35px;
        left: 20px;
    }

    .no-padding-left {
        padding-left: 0 !important;
    }

    .no-padding-right {
        padding-right: 0 !important;
    }

    div.reporting {
        position: relative
    }

    div.blob {
        position: absolute;
        bottom: 105px;
        right: 0
    }

</style>

<div class="row widget">

<div class="widget-header" style="margin:0">
    <div class="title">
        <?php echo $title; ?>
    </div>
    <div class="clearfix"></div>
</div>

<div class="panel panel-default" style="margin:0">
    <div class="panel-heading">
        <h4 class="panel-title">
            <label class=""><a class="external text-danger" data-toggle="collapse" data-target="#collapse1" href="#"> + Tour Information</a></label>
        </h4>
    </div>
    <div id="collapse1" class="panel-collapse collapse">

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
                <label class="control-label pull-right">Tour Title:</label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $item['title'] ?></label>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo 'Tour ' . $CI->lang->line('LABEL_DATE'); ?>:</label>
            </div>
            <div class="col-sm-4 col-xs-8">
                From &nbsp;<label class="control-label"><?php echo System_helper::display_date($item['date_from']) ?></label> &nbsp; To &nbsp;<label class="control-label"><?php echo System_helper::display_date($item['date_to']) ?></label>
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

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo 'Reporting ' . $CI->lang->line('LABEL_DATE'); ?>:</label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo System_helper::display_date($reporting_date); ?></label>
            </div>
        </div>

        <div class="clearfix"></div>
    </div>
</div>

<div>
<form class="form_valid" id="save_form" action="<?php echo site_url($CI->controller_url . '/index/save_reporting'); ?>" method="post">

<input type="hidden" id="id" name="id" value="<?php echo $item['id']; ?>"/>
<input type="hidden" id="system_save_new_status" name="system_save_new_status" value="0"/>
<input type="hidden" name="item[date_reporting]" value="<?php echo $reporting_date; ?>"/>

<div id="tour_setup_container" style="overflow-x:auto;">
<div class="col-xs-12 widget-header" style="font-size:1.2em; margin-bottom:0; border-top:1px solid #cfcfcf">
    <label class="control-label" style="margin:0">Reporting ( <?php echo System_helper::display_date($reporting_date); ?> )</label>
</div>

<?php
if ($items) // OLD ITEMS
{
    foreach ($items as $info)
    {
        ?>
        <div class="col-xs-12 reporting">
            <div class="row show-grid">
                <div class="col-xs-4">
                    <label class="control-label pull-right">Purpose <span style="color:#FF0000">*</span></label>
                </div>
                <div class="col-xs-4">
                    <label class="control-label"><?php echo $info['purpose']; ?></label>
                    <input type="hidden" name="old_items[<?php echo $info['report_id']; ?>][purpose]" value="<?php echo $info['purpose_id']; ?>"/>
                </div>
                <div class="col-xs-4 delete-btn-wrap">
                    <button class="btn btn-sm btn-danger system_button_add_delete" title="Delete">X</button>
                </div>
            </div>

            <div class="row show-grid">
                <div class="col-xs-4">
                    <label class="control-label pull-right">Report (Description)
                        <span style="color:#FF0000">*</span></label>
                </div>
                <div class="col-xs-4">
                    <textarea class="form-control content-report-description" name="old_items[<?php echo $info['report_id']; ?>][report_description]"><?php echo $info['report_description']; ?></textarea>
                </div>
            </div>

            <div class="row show-grid">
                <div class="col-xs-4">
                    <label class="control-label pull-right">Recommendation <span style="color:#FF0000">*</span></label>
                </div>
                <div class="col-xs-4">
                    <textarea class="form-control content-recommendation" name="old_items[<?php echo $info['report_id']; ?>][recommendation]"><?php echo $info['recommendation']; ?></textarea>
                </div>
            </div>

            <div class="row show-grid">
                <div class="col-xs-4">
                    <label class="control-label pull-right">Contact person (If any)</label>
                </div>
                <div class="col-xs-4">
                    <input type="text" class="form-control content-other-name" value="<?php echo $info['name']; ?>" name="old_items[<?php echo $info['report_id']; ?>][other_name]"/>
                </div>
            </div>

            <div class="row show-grid">
                <div class="col-xs-4">
                    <label class="control-label pull-right">Contact No. (If any)</label>
                </div>
                <div class="col-xs-4">
                    <input type="text" class="form-control integer_type_positive content-other-contact" value="<?php echo $info['contact_no']; ?>" name="old_items[<?php echo $info['report_id']; ?>][other_contact]"/>
                </div>
            </div>

            <div class="row show-grid">
                <div class="col-xs-4">
                    <label class="control-label pull-right">Profession (If any)</label>
                </div>
                <div class="col-xs-4">
                    <input type="text" class="form-control content-other-profession" value="<?php echo $info['profession']; ?>" name="old_items[<?php echo $info['report_id']; ?>][other_profession]"/>
                </div>
            </div>

            <div class="row show-grid">
                <div class="col-xs-4">
                    <label class="control-label pull-right">Discussion (If any)</label>
                </div>
                <div class="col-xs-4">
                    <textarea class="form-control content-other-discussion" name="old_items[<?php echo $info['report_id']; ?>][other_discussion]"><?php echo $info['discussion']; ?></textarea>
                </div>
            </div>

            <div class="row show-grid">
                <div class="col-xs-4">
                    <label class="control-label pull-right">Picture (If any)</label>
                    <br/><p style="clear:both; text-align:right; font-size:0.85em">Allowed Image types(.<?php echo str_replace('|', ', .', $CI->custom_image_types); ?>)</p>
                </div>
                <div class="col-xs-4">
                    <input type="file" class="form-control old_browse_button" name="old_image_<?php echo $info['id']; ?>" data-preview-container="#old_image_reporting_<?php echo $info['id']; ?>" data-preview-width="<?php echo $img_width; ?>"/>                </div>
            </div>

            <div class="row show-grid">
                <div class="col-xs-4">&nbsp;</div>
                <div class="col-xs-4 blob" id="old_image_reporting_<?php echo $info['id']; ?>">
                    <div style="width:<?php echo $img_width; ?>px">
                        <?php
                        $img_src = $this->config->item('system_base_url_picture') . $info['image_location'];
                        ?>
                        <img width="100%" src="<?php echo $img_src; ?>" alt="No Image Found" />
                    </div>
                </div>
            </div>
        </div>
    <?php
    }
}
else
{
    ?>

    <div class="col-xs-12 reporting">
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Purpose <span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-xs-3 no-padding-right">
                <select class="form-control content-purpose" name="items[0][purpose]">
                    <option value=""><?php echo $this->lang->line('SELECT'); ?></option>
                    <?php
                    if ($item['purposes'])
                    {
                        foreach ($item['purposes'] as $row)
                        {
                            ?>
                            <option value="<?php echo $row['id']; ?>"><?php echo $row['purpose']; ?></option><?php
                        }
                    }
                    ?>
                </select>
            </div>
            <div class="col-xs-1" style="text-align:center">
                <label class="control-label">- OR -</label>
            </div>
            <div class="col-xs-3 no-padding-left wrap-additional">
                <input type="text" class="form-control content-purpose-additional" name="items[0][purpose_additional]" placeholder="Enter New Purpose"/>
                <span>Already in Purpose List.</span>
            </div>
            <div class="col-xs-1 delete-btn-wrap">
                <button class="btn btn-sm btn-danger system_button_add_delete" title="Delete">X</button>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Report (Description)
                    <span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-xs-4">
                <textarea class="form-control content-report-description" name="items[0][report_description]"></textarea>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Recommendation <span style="color:#FF0000">*</span></label>
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
                <input type="text" class="form-control integer_type_positive content-other-contact" name="items[0][other_contact]"/>
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

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Picture (If any)</label>
            </div>
            <div class="col-xs-4">
                <input type="file" class="form-control content-image old_browse_button" name="new_image_0" data-preview-container="#image_reporting_0" data-preview-width="<?php echo $img_width; ?>"/>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">&nbsp;</div>
            <div class="col-xs-4 blob" id="image_reporting_0">
                <!----- Image blob here ----->
            </div>
        </div>
    </div>
<?php } ?>
</div>

<div class="row show-grid" style="margin:5px 0 0">
    <div class="col-xs-12">
        <div class="pull-right" style="display:inline-block">
            <button type="button" class="btn btn-warning system_button_add_more" data-current-id="0"><?php echo $CI->lang->line('LABEL_ADD_MORE'); ?></button>
        </div>
        <div class="clearfix"></div>
    </div>
</div>

<div class="row show-grid" style="margin:0">
    <div class="col-xs-12">
        <div class="action_button" style="width:100%; text-align:center">
            <button id="button_action_save" type="button" class="btn" data-form="#save_form">Save</button>
        </div>
    </div>
    <div class="clearfix"></div>
</div>

</form>
</div>

<div class="clearfix"></div>

</div>


<!-------------------------------------------JUST FOR COPYING----------------------------------------------------->
<div id="system_content_add_more" style="display:none;">

    <div class="col-xs-12 reporting">
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Purpose <span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-xs-3 no-padding-right">
                <select class="form-control content-purpose">
                    <option value=""><?php echo $this->lang->line('SELECT'); ?></option>
                    <?php
                    if ($item['purposes'])
                    {
                        foreach ($item['purposes'] as $row)
                        {
                            ?>
                            <option value="<?php echo $row['id']; ?>"><?php echo $row['purpose']; ?></option><?php
                        }
                    }
                    ?>
                </select>
            </div>
            <div class="col-xs-1" style="text-align:center">
                <label class="control-label">- OR -</label>
            </div>
            <div class="col-xs-3 no-padding-left wrap-additional">
                <input type="text" class="form-control content-purpose-additional" placeholder="Enter New Purpose"/>
                <span>Already in Purpose List.</span>
            </div>
            <div class="col-xs-1 delete-btn-wrap">
                <button class="btn btn-sm btn-danger system_button_add_delete" title="Delete">X</button>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Report (Description)
                    <span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-xs-4">
                <textarea class="form-control content-report-description"></textarea>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Recommendation <span style="color:#FF0000">*</span></label>
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
                <input type="text" class="form-control content-other-name"/>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Contact No. (If any)</label>
            </div>
            <div class="col-xs-4">
                <input type="text" class="form-control integer_type_positive content-other-contact"/>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Profession (If any)</label>
            </div>
            <div class="col-xs-4">
                <input type="text" class="form-control content-other-profession"/>
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

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Picture (If any)</label>
            </div>
            <div class="col-xs-4">
                <input type="file" class="form-control content-image browse_button" data-preview-width="<?php echo $img_width; ?>"/>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">&nbsp;</div>
            <div class="col-xs-4 blob">
                <!----- Image blob here ----->
            </div>
        </div>
    </div>

</div>
<div id="purpose-list-content" style="display:none">
    <?php if ($item['purposes'])
    {
        foreach ($item['purposes'] as $row)
        {
            echo '<span>' . $row['purpose'] . '</span>';
        }
    }?>
</div><!----------------------------------------JUST FOR COPYING (END)-------------------------------------------------->

<script type="text/javascript">

    jQuery(document).ready(function ($) {
        system_off_events(); // Triggers

        $(".datepicker").datepicker({dateFormat: display_date_format});
        $(".old_browse_button").filestyle({input: false, icon: false, buttonText: "Upload Picture", buttonName: "btn-primary"});

        $(document).on("click", ".system_button_add_more", function (event) {
            var current_id = parseInt($(this).attr('data-current-id'));
            current_id = current_id + 1;
            $(this).attr('data-current-id', current_id);

            var content_id = '#system_content_add_more';
            $(content_id + ' .content-purpose').attr('name', 'items[' + current_id + '][purpose]');
            $(content_id + ' .content-purpose-additional').attr('name', 'items[' + current_id + '][purpose_additional]');
            $(content_id + ' .content-report-description').attr('name', 'items[' + current_id + '][report_description]');
            $(content_id + ' .content-recommendation').attr('name', 'items[' + current_id + '][recommendation]');
            $(content_id + ' .content-other-name').attr('name', 'items[' + current_id + '][other_name]');
            $(content_id + ' .content-other-contact').attr('name', 'items[' + current_id + '][other_contact]');
            $(content_id + ' .content-other-profession').attr('name', 'items[' + current_id + '][other_profession]');
            $(content_id + ' .content-other-discussion').attr('name', 'items[' + current_id + '][other_discussion]');
            $(content_id + ' .content-image').attr('name', 'new_image_' + current_id);

            $(content_id + ' .content-image').attr('data-preview-container', '#image_reporting_' + current_id);
            $(content_id + ' .blob').attr('id', 'image_reporting_' + current_id);
            $(content_id + ' .browse_button').attr('id', 'browse_button_' + current_id);

            var content = $(content_id).html();
            $("#tour_setup_container").append(content);
            $("#browse_button_" + current_id).filestyle({input: false, icon: false, buttonText: "Upload Picture", buttonName: "btn-primary"});
        });
        $(document).on("click", ".system_button_add_delete", function (event) {
            event.preventDefault();
            $(this).closest('div.reporting').remove();
        });
        $(document).on("change", "select.content-purpose", function (event) {
            var text_val = $(this).val();
            if (!isNaN(text_val) && (text_val != '')) {
                $(this).parent().siblings().children('.content-purpose-additional').attr('disabled', '').val('');
                $(this).parent().siblings().children('.content-purpose-additional').siblings('span').css('display', 'none');
            } else {
                $(this).parent().siblings().children('.content-purpose-additional').removeAttr('disabled', '');
            }
        });

        /*--------Checking Duplicate Purpose at New Entry-------*/
        var typingTimer;
        $(document).on("keyup", '.content-purpose-additional', function (event) {
            clearTimeout(typingTimer);
            var COUNT = 0;
            var Txt1 = convert_to_lower($(this).val());
            var Txt2;
            var element = $(this);

            typingTimer = setTimeout(function () {
                $("#purpose-list-content span").each(function (index) {
                    console.log(Txt1 + ": " + $(this).text());
                    Txt2 = convert_to_lower($(this).text());
                    if (Txt1 === Txt2) {
                        COUNT++;
                    }
                });
                if (COUNT > 0) {
                    element.siblings('span').css('display', 'inline-block');
                } else {
                    element.siblings('span').css('display', 'none');
                }
            }, 2000);
        });

        function convert_to_lower(str) {
            str = str.trim().toLowerCase();
            return str
        }
    });

</script>
