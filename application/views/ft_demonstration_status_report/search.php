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
                                    <option value="<?php echo $season['value'] ?>" <?php echo ($season['value'] == $item['season_id']) ? 'selected' : ''; ?>><?php echo $season['text']; ?></option>
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
                        <select name="item[crop_id]" id="crop_id" class="form-control">
                            <option value=""><?php echo $CI->lang->line('SELECT'); ?></option>
                        </select>
                    </div>
                </div>
                <div class="row show-grid">
                    <div class="col-xs-4">
                        <label class="control-label pull-right">Search Date Type</label>
                    </div>
                    <div class="col-xs-8">
                        <select id="season_id" name="item[season_id]" class="form-control">
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
                <div class="row show-grid" id="date_range_container">
                    <div class="col-xs-4">
                        <label class="control-label pull-right">Date Range <span style="color:#FF0000">*</span></label>
                    </div>
                    <div class="col-xs-4">
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
                    <div class="col-xs-4">
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
                        <select id="division_id" class="form-control">
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

                <div class="row show-grid" id="zone_id_container">
                    <div class="col-xs-3">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ZONE_NAME'); ?></label>
                    </div>
                    <div class="col-xs-8">
                        <select id="zone_id" class="form-control">
                            <option value=""><?php echo $CI->lang->line('SELECT'); ?></option>
                        </select>
                    </div>
                </div>

                <div class="row show-grid" id="territory_id_container">
                    <div class="col-xs-3">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_TERRITORY_NAME'); ?></label>
                    </div>
                    <div class="col-xs-8">
                        <select id="territory_id" class="form-control">
                            <option value=""><?php echo $CI->lang->line('SELECT'); ?></option>
                        </select>
                    </div>
                </div>

                <div class="row show-grid" id="district_id_container">
                    <div class="col-xs-3">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DISTRICT_NAME'); ?></label>
                    </div>
                    <div class="col-xs-8">
                        <select id="district_id" class="form-control">
                            <option value=""><?php echo $CI->lang->line('SELECT'); ?></option>
                        </select>
                    </div>
                </div>

                <div class="row show-grid" id="outlet_id_container">
                    <div class="col-xs-3">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_OUTLET_NAME'); ?></label>
                    </div>
                    <div class="col-xs-8">
                        <select id="outlet_id" class="form-control">
                            <option value=""><?php echo $CI->lang->line('SELECT'); ?></option>
                        </select>
                    </div>
                </div>

                <div class="row show-grid" id="growing_area_id_container">
                    <div class="col-xs-3">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_GROWING_AREA'); ?></label>
                    </div>
                    <div class="col-xs-8">
                        <select id="growing_area_id" class="form-control">
                            <option value=""><?php echo $CI->lang->line('SELECT'); ?></option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <hr style="margin-top:0"/>

        <div class="row show-grid">
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

    jQuery(document).ready(function () {
        system_off_events();
        system_preset({controller: '<?php echo $CI->router->class; ?>'});

        $(".datepicker").datepicker({dateFormat: display_date_format});
        $('.input-group-addon').click(function () {
            $(this).siblings('input.datepicker').focus();
        });

        $('#crop_id').html(get_dropdown_with_select(system_crops));


    });
</script>
