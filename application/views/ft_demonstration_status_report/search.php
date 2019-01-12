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
                            <option value="date_expected_evaluation"><?php echo $CI->lang->line('LABEL_DATE_EXPECTED_EVALUATION'); ?></option>
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
                    <div class="col-xs-3">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DIVISION_NAME'); ?></label>
                    </div>
                    <div class="col-xs-8">
                        <select id="division_id" name="item[division_id]" class="form-control">
                            <option value=""><?php echo $CI->lang->line('SELECT'); ?></option>
                            <?php
                            if ($divisions)
                            {
                                foreach ($divisions as $division)
                                {
                                    ?>
                                    <option value="<?php echo $division['value'] ?>"><?php echo $division['text']; ?></option>
                                <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="row show-grid" id="zone_id_container" style="display:none">
                    <div class="col-xs-3">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ZONE_NAME'); ?></label>
                    </div>
                    <div class="col-xs-8">
                        <select id="zone_id" name="item[zone_id]" class="form-control">
                            <option value=""><?php echo $CI->lang->line('SELECT'); ?></option>
                        </select>
                    </div>
                </div>

                <div class="row show-grid" id="territory_id_container" style="display:none">
                    <div class="col-xs-3">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_TERRITORY_NAME'); ?></label>
                    </div>
                    <div class="col-xs-8">
                        <select id="territory_id" name="item[territory_id]" class="form-control">
                            <option value=""><?php echo $CI->lang->line('SELECT'); ?></option>
                        </select>
                    </div>
                </div>

                <div class="row show-grid" id="district_id_container" style="display:none">
                    <div class="col-xs-3">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DISTRICT_NAME'); ?></label>
                    </div>
                    <div class="col-xs-8">
                        <select id="district_id" name="item[district_id]" class="form-control">
                            <option value=""><?php echo $CI->lang->line('SELECT'); ?></option>
                        </select>
                    </div>
                </div>

                <div class="row show-grid" id="outlet_id_container" style="display:none">
                    <div class="col-xs-3">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_OUTLET_NAME'); ?></label>
                    </div>
                    <div class="col-xs-8">
                        <select id="outlet_id" name="item[outlet_id]" class="form-control">
                            <option value=""><?php echo $CI->lang->line('SELECT'); ?></option>
                        </select>
                    </div>
                </div>

                <div class="row show-grid" id="growing_area_id_container" style="display:none">
                    <div class="col-xs-3">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_GROWING_AREA'); ?></label>
                    </div>
                    <div class="col-xs-8">
                        <select id="growing_area_id" name="item[growing_area_id]" class="form-control">
                            <option value=""><?php echo $CI->lang->line('SELECT'); ?></option>
                        </select>
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
        $(document).on("change", "#division_id", function () {
            $("#zone_id").val('');
            $("#territory_id").val('');
            $("#district_id").val('');
            $("#outlet_id").val('');
            $('#growing_area_id').val('');

            var division_id = $('#division_id').val();
            $('#zone_id_container').hide();
            $('#territory_id_container').hide();
            $('#district_id_container').hide();
            $('#outlet_id_container').hide();
            $('#growing_area_id_container').hide();
            if (division_id > 0) {
                $('#zone_id_container').show();
                if (system_zones[division_id] !== undefined) {
                    $("#zone_id").html(get_dropdown_with_select(system_zones[division_id]));
                }
            }
        });
        $(document).on("change", "#zone_id", function () {
            $("#territory_id").val('');
            $("#district_id").val('');
            $("#outlet_id").val('');
            $('#growing_area_id').val('');

            var zone_id = $('#zone_id').val();
            $('#territory_id_container').hide();
            $('#district_id_container').hide();
            $('#outlet_id_container').hide();
            $('#growing_area_id_container').hide();
            $('#dealer_container').hide();
            $('#leading_farmer_container').hide();
            if (zone_id > 0) {
                $('#territory_id_container').show();
                if (system_territories[zone_id] !== undefined) {
                    $("#territory_id").html(get_dropdown_with_select(system_territories[zone_id]));
                }
            }
        });
        $(document).on("change", "#territory_id", function () {
            $("#district_id").val('');
            $("#outlet_id").val('');
            $('#growing_area_id').val('');

            var territory_id = $('#territory_id').val();
            $('#district_id_container').hide();
            $('#outlet_id_container').hide();
            $('#growing_area_id_container').hide();
            $('#dealer_container').hide();
            $('#leading_farmer_container').hide();
            if (territory_id > 0) {
                $('#district_id_container').show();
                if (system_districts[territory_id] !== undefined) {
                    $("#district_id").html(get_dropdown_with_select(system_districts[territory_id]));
                }
            }
        });
        $(document).on("change", "#district_id", function () {
            $('#outlet_id').val('');
            $('#growing_area_id').val('');

            var district_id = $('#district_id').val();
            $('#outlet_id_container').hide();
            $('#growing_area_id_container').hide();
            $('#dealer_container').hide();
            $('#leading_farmer_container').hide();
            if (district_id > 0) {
                if (system_outlets[district_id] !== undefined) {
                    $('#outlet_id_container').show();
                    $('#outlet_id').html(get_dropdown_with_select(system_outlets[district_id]));
                }
            }
        });
        $(document).on("change", "#outlet_id", function () {
            $('#growing_area_id').val('');

            var outlet_id = parseInt($(this).val());
            $('#growing_area_id_container').hide();
            if (outlet_id > 0) {
                $.ajax({
                    url: "<?php echo site_url($CI->controller_url.'/index/get_growing_area/') ?>",
                    type: 'POST',
                    datatype: "JSON",
                    data: {
                        html_container_id: '#growing_area_id',
                        id: outlet_id
                    },
                    success: function (data, status) {
                        if (data.status) {
                            $('#growing_area_id_container').show();
                        }
                    },
                    error: function (xhr, desc, err) {
                        console.log("error");
                    }
                });
            }
        });
        /*--------------------- LOCATION RELATED DROPDOWN ( END ) ---------------------*/
    });
</script>
