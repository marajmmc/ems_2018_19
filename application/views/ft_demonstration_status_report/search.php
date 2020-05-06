<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();
$action_buttons = array();
$action_buttons[] = array
(
    'label' => $CI->lang->line("ACTION_REFRESH"),
    'href' => site_url($CI->controller_url . '/index')
);

$CI->load->view('action_buttons', array('action_buttons' => $action_buttons));
?>
<form id="save_form" action="<?php echo site_url($CI->controller_url . '/index/list'); ?>" method="post">
    <div class="row widget">
        <div class="widget-header">
            <div class="title">
                <?php echo $title; ?>
            </div>
            <div class="clearfix"></div>
        </div>

        <div class="row show-grid">

            <div class="col-md-6">
                <div class="row show-grid">
                    <div class="col-xs-4">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_YEAR'); ?></label>
                    </div>
                    <div class="col-xs-8">
                        <select id="year" name="item[year]" class="form-control">
                            <option value=""><?php echo $CI->lang->line('SELECT'); ?></option>
                            <?php
                            for ($year = (date('Y') + 1); $year >= (date('Y') - 1); $year--)
                            {
                                ?>
                                <option value="<?php echo $year; ?>"><?php echo $year; ?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="row show-grid">
                    <div class="col-xs-4">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_SEASON'); ?></label>
                    </div>
                    <div class="col-xs-8">
                        <select id="season_id" name="item[season_id]" class="form-control">
                            <option value=""><?php echo $CI->lang->line('SELECT'); ?></option>
                            <?php
                            if ($seasons)
                            {
                                foreach ($seasons as $season)
                                {
                                    ?>
                                    <option value="<?php echo $season['value'] ?>"><?php echo $season['text']; ?></option>
                                <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="row show-grid" id="crop_id_container">
                    <div class="col-xs-4">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_NAME'); ?></label>
                    </div>
                    <div class="col-xs-8">
                        <select id="crop_id" name="item[crop_id]" class="form-control">
                            <option value=""><?php echo $CI->lang->line('SELECT'); ?></option>
                        </select>
                    </div>
                </div>
                <div class="row show-grid">
                    <div class="col-xs-4">
                        <label class="control-label pull-right">Search Date Type</label>
                    </div>
                    <div class="col-xs-8">
                        <select id="date_type" name="item[date_type]" class="form-control">
                            <option value=""><?php echo $CI->lang->line('SELECT'); ?></option>
                            <option value="date_sowing_variety1"><?php echo $CI->lang->line('LABEL_DATE_SOWING_VARIETY1'); ?></option>
                            <option value="date_sowing_variety2"><?php echo $CI->lang->line('LABEL_DATE_SOWING_VARIETY2'); ?></option>
                            <option value="date_transplanting_variety1"><?php echo $CI->lang->line('LABEL_DATE_TRANSPLANTING_VARIETY1'); ?></option>
                            <option value="date_transplanting_variety1"><?php echo $CI->lang->line('LABEL_DATE_TRANSPLANTING_VARIETY2'); ?></option>
                            <option value="date_actual_evaluation"><?php echo $CI->lang->line('LABEL_DATE_ACTUAL_EVALUATION'); ?></option>
                        </select>
                    </div>
                </div>
                <div class="row show-grid" id="date_range_container" style="display:none; margin:0">
                    <div class="col-xs-4">
                        <label class="control-label pull-right">Date Range <span style="color:#FF0000">*</span></label>
                    </div>
                    <div class="col-xs-4" style="padding-right:5px">
                        <label class="control-label">From</label>

                        <div class="form-group">
                            <div class='input-group date'>
                                <input type="text" name="item[start_date]" class="form-control datepicker" value="" readonly/>
                                    <span class="input-group-addon">
                                        <i class="glyphicon glyphicon-calendar"></i>
                                    </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-4" style="padding-left:5px">
                        <label class="control-label">To</label>

                        <div class="form-group">
                            <div class='input-group date'>
                                <input type="text" name="item[end_date]" class="form-control datepicker" value="" readonly/>
                                    <span class="input-group-addon">
                                        <i class="glyphicon glyphicon-calendar"></i>
                                    </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">

                <div class="row show-grid">
                    <div class="col-xs-4">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DIVISION_NAME'); ?></label>
                    </div>
                    <div class="col-xs-8">
                        <?php
                        if ($CI->locations['division_id'] > 0)
                        {
                            ?>
                            <label class="control-label"><?php echo $CI->locations['division_name']; ?></label>
                            <input type="hidden" name="item[division_id]" value="<?php echo $CI->locations['division_id']; ?>" />
                        <?php
                        }
                        else
                        {
                            ?>
                            <select name="item[division_id]" id="division_id" class="form-control">
                                <option value=""><?php echo $CI->lang->line('SELECT'); ?> </option>
                                <?php
                                foreach ($divisions as $division)
                                {
                                    ?>
                                    <option value="<?php echo $division['value'] ?>"><?php echo $division['text']; ?></option>
                                <?php
                                }
                                ?>
                            </select>
                        <?php
                        }
                        ?>
                    </div>
                </div>
                <div class="row show-grid" id="zone_id_container" style="<?php echo (!($CI->locations['division_id'] > 0)) ? 'display:none' : '' ?>">
                    <div class="col-xs-4">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ZONE_NAME'); ?></label>
                    </div>
                    <div class="col-xs-8">
                        <?php
                        if ($CI->locations['zone_id'] > 0)
                        {
                            ?>
                            <label class="control-label"><?php echo $CI->locations['zone_name']; ?></label>
                            <input type="hidden" name="item[zone_id]" value="<?php echo $CI->locations['zone_id']; ?>" />
                        <?php
                        }
                        else
                        {
                            ?>
                            <select name="item[zone_id]" id="zone_id" class="form-control">
                                <option value=""><?php echo $CI->lang->line('SELECT'); ?></option>
                                <?php
                                foreach ($zones as $zone)
                                {
                                    ?>
                                    <option value="<?php echo $zone['value'] ?>"><?php echo $zone['text']; ?></option>
                                <?php
                                }
                                ?>
                            </select>
                        <?php
                        }
                        ?>
                    </div>
                </div>
                <div class="row show-grid" id="territory_id_container" style="<?php echo (!($CI->locations['zone_id'] > 0)) ? 'display:none' : '' ?>">
                    <div class="col-xs-4">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_TERRITORY_NAME'); ?></label>
                    </div>
                    <div class="col-xs-8">
                        <?php
                        if ($CI->locations['territory_id'] > 0)
                        {
                            ?>
                            <label class="control-label"><?php echo $CI->locations['territory_name']; ?></label>
                            <input type="hidden" name="item[territory_id]" value="<?php echo $CI->locations['territory_id']; ?>" />
                        <?php
                        }
                        else
                        {
                            ?>
                            <select name="item[territory_id]" id="territory_id" class="form-control">
                                <option value=""><?php echo $CI->lang->line('SELECT'); ?></option>
                                <?php
                                foreach ($territories as $territory)
                                {
                                    ?>
                                    <option value="<?php echo $territory['value'] ?>"><?php echo $territory['text']; ?></option>
                                <?php
                                }
                                ?>
                            </select>
                        <?php
                        }
                        ?>
                    </div>
                </div>
                <div class="row show-grid" id="district_id_container" style="<?php echo (!($CI->locations['territory_id'] > 0)) ? 'display:none' : '' ?>">
                    <div class="col-xs-4">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DISTRICT_NAME'); ?></label>
                    </div>
                    <div class="col-xs-8">
                        <?php
                        if ($CI->locations['district_id'] > 0)
                        {
                            ?>
                            <label class="control-label"><?php echo $CI->locations['district_name']; ?></label>
                            <input type="hidden" name="item[district_id]" value="<?php echo $CI->locations['district_id']; ?>" />
                        <?php
                        }
                        else
                        {
                            ?>
                            <select name="item[district_id]" id="district_id" class="form-control">
                                <option value=""><?php echo $CI->lang->line('SELECT'); ?></option>
                                <?php
                                foreach ($districts as $district)
                                {
                                    ?>
                                    <option value="<?php echo $district['value'] ?>"><?php echo $district['text']; ?></option>
                                <?php
                                }
                                ?>
                            </select>
                        <?php
                        }
                        ?>
                    </div>
                </div>
                <div class="row show-grid" id="upazilla_id_container" style="<?php echo (!($CI->locations['district_id'] > 0)) ? 'display:none' : ''; ?>">
                    <div class="col-xs-4">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_UPAZILLA_NAME'); ?></label>
                    </div>
                    <div class="col-xs-8">
                        <?php
                        if ($CI->locations['upazilla_id'] > 0)
                        {
                            ?>
                            <label class="control-label"><?php echo $CI->locations['upazilla_name']; ?></label>
                            <input type="hidden" name="item[upazilla_id]" value="<?php echo $CI->locations['upazilla_id']; ?>" />
                        <?php
                        }
                        else
                        {
                            ?>
                            <select name="item[upazilla_id]" id="upazilla_id" class="form-control">
                                <option value=""><?php echo $CI->lang->line('SELECT'); ?> </option>
                                <?php
                                foreach ($upazillas as $upazilla)
                                {
                                    ?>
                                    <option value="<?php echo $upazilla['value'] ?>"><?php echo $upazilla['text']; ?></option>
                                <?php
                                }
                                ?>
                            </select>
                        <?php
                        }
                        ?>
                    </div>
                </div>
                <div class="row show-grid" id="union_id_container" style="<?php echo (!($CI->locations['upazilla_id'] > 0)) ? 'display:none' : ''; ?>">
                    <div class="col-xs-4">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_UNION_NAME'); ?></label>
                    </div>
                    <div class="col-xs-8">
                        <?php
                        if ($CI->locations['union_id'] > 0)
                        {
                            ?>
                            <label class="control-label"><?php echo $CI->locations['union_name']; ?></label>
                            <input type="hidden" name="item[union_id]" value="<?php echo $CI->locations['union_id']; ?>" />
                        <?php
                        }
                        else
                        {
                            ?>
                            <select name="item[union_id]" id="union_id" class="form-control">
                                <option value=""><?php echo $CI->lang->line('SELECT'); ?> </option>
                                <?php
                                foreach ($unions as $union)
                                {
                                    ?>
                                    <option value="<?php echo $union['value'] ?>"><?php echo $union['text']; ?></option>
                                <?php
                                }
                                ?>
                            </select>
                        <?php
                        }
                        ?>
                    </div>
                </div>

            </div>

        </div>

        <hr style="margin-top:0"/>

        <div class="row show-grid" style="margin:0">
            <div class="col-md-6">
                <div class="action_button pull-right" style="margin:0; padding:0 15px">
                    <button id="button_action_report" type="button" class="btn" data-form="#save_form"><?php echo $CI->lang->line("ACTION_REPORT"); ?></button>
                </div>
            </div>
        </div>
    </div>

    <div class="clearfix"></div>
</form>

<div id="system_report_container">

</div>
<script type="text/javascript">

    function clear_child(union=false, upazilla=false, district=false, territory=false, zone=false, division=false)
    {
        if(union)
        {
            $("#union_id").val('');
            $('#union_id_container').hide();
        }
        if(upazilla)
        {
            $("#upazilla_id").val('');
            $('#upazilla_id_container').hide();
        }
        if(district)
        {
            $("#district_id").val('');
            $('#district_id_container').hide();
        }
        if(territory)
        {
            $("#territory_id").val('');
            $('#territory_id_container').hide();
        }
        if(zone)
        {
            $("#zone_id").val('');
            $('#zone_id_container').hide();
        }
        if(division)
        {
            $("#division_id").val('');
            $('#division_id_container').hide();
        }
    }

    jQuery(document).ready(function ($) {
        system_off_events();
        system_preset({controller:'<?php echo $CI->router->class; ?>'});

        $(".datepicker").datepicker({dateFormat: display_date_format});
        $('.input-group-addon').click(function () {
            $(this).siblings('input.datepicker').focus();
        });

        $('#crop_id').html(get_dropdown_with_select(system_crops));

        $(document).on("change", "#date_type", function () {
            var date_type = $(this).val();
            if(date_type.trim() != ''){
                $('#date_range_container').show();
            }else{
                $('#date_range_container').hide();
            }
        });

        /*--------------------- LOCATION RELATED DROPDOWN -----------------------------*/
        var system_upazillas = JSON.parse('<?php echo json_encode($system_upazillas); ?>');
        var system_unions = JSON.parse('<?php echo json_encode($system_unions); ?>');
        $(document).on("change", "#division_id", function () {
            clear_child(true, true, true, true, true)

            var division_id = $(this).val();
            if (division_id > 0) {
                $('#zone_id_container').show();
                if (system_zones[division_id] !== undefined) {
                    $("#zone_id").html(get_dropdown_with_select(system_zones[division_id]));
                }
            }
        });
        $(document).on("change", "#zone_id", function () {
            clear_child(true, true, true, true)

            var zone_id = $(this).val();
            if (zone_id > 0) {
                $('#territory_id_container').show();
                if (system_territories[zone_id] !== undefined) {
                    $("#territory_id").html(get_dropdown_with_select(system_territories[zone_id]));
                }
            }
        });
        $(document).on("change", "#territory_id", function () {
            clear_child(true, true, true)

            var territory_id = $(this).val();
            if (territory_id > 0) {
                $('#district_id_container').show();
                if (system_districts[territory_id] !== undefined) {
                    $("#district_id").html(get_dropdown_with_select(system_districts[territory_id]));
                }
            }
        });
        $(document).on("change", "#district_id", function () {
            clear_child(true, true)

            var district_id = $(this).val();
            if (district_id > 0) {
                $('#upazilla_id_container').show();
                if (system_upazillas[district_id] !== undefined) {
                    $("#upazilla_id").html(get_dropdown_with_select(system_upazillas[district_id]));
                }
            }
        });
        $(document).on("change", "#upazilla_id", function () {
            clear_child(true);

            var upazilla_id = $(this).val();
            if (upazilla_id > 0) {
                $('#union_id_container').show();
                if (system_unions[upazilla_id] !== undefined) {
                    $("#union_id").html(get_dropdown_with_select(system_unions[upazilla_id]));
                }
            }
        });
        /*--------------------- LOCATION RELATED DROPDOWN ( END ) ---------------------*/
    });
</script>
