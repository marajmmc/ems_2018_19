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
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_YEAR'); ?>
                    <span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-xs-4">
                <select id="year" name="item[year]" class="form-control">
                    <?php
                    for ($year = (date('Y') + 1); $year >= (date('Y') - 1); $year--)
                    {
                        ?>
                        <option value="<?php echo $year; ?>" <?php echo ($year == $item['year']) ? 'selected' : ''; ?>><?php echo $year; ?></option>
                    <?php
                    }
                    ?>
                </select>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_SEASON'); ?>
                    <span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-xs-4">
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

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_OUTLET_NAME'); ?>
                    <span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-xs-4">
                <?php
                if ($outlets)
                {
                    if (sizeof($outlets) > 1)
                    {
                        ?>
                        <select id="outlet_id" name="item[outlet_id]" class="form-control">
                            <option value=""><?php echo $CI->lang->line('SELECT'); ?></option>
                            <?php foreach ($outlets as $outlet)
                            {
                                ?>
                                <option value="<?php echo $outlet['value'] ?>" <?php echo ($outlet['value'] == $item['outlet_id']) ? 'selected' : ''; ?>><?php echo $outlet['text']; ?></option>
                            <?php
                            }
                            ?>
                        </select>
                    <?php
                    }
                    else
                    {
                        ?>
                        <label class="control-label"><?php echo $outlets[0]['text']; ?></label>
                        <input type="hidden" name="item[outlet_id]" value="<?php echo $outlets[0]['value'] ?>"/>
                    <?php
                    }

                }
                ?>
            </div>
        </div>

        <div style="<?php echo (!($item['id'] > 0) && (sizeof($outlets) > 1)) ? 'display:none' : ''; ?>" class="row show-grid" id="growing_area_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_GROWING_AREA'); ?>
                    <span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-xs-4">
                <select id="growing_area_id" name="item[growing_area_id]" class="form-control">
                    <option value=""><?php echo $CI->lang->line('SELECT'); ?></option>
                    <?php
                    if ($growing_area)
                    {
                        foreach ($growing_area as $area)
                        {
                            ?>
                            <option value="<?php echo $area['value'] ?>" <?php echo ($area['value'] == $item['growing_area_id']) ? 'selected' : ''; ?>><?php echo $area['text']; ?></option>
                        <?php
                        }
                    }
                    ?>
                </select>
            </div>
        </div>

        <div style="<?php echo (!($item['id'] > 0)) ? 'display:none' : ''; ?>" id="lead_farmer_id_container">
            <div class="row show-grid">
                <div class="col-xs-4">
                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_LEAD_FARMER_NAME'); ?>
                        <span style="color:#FF0000">*</span></label>
                </div>
                <div class="col-xs-4">
                    <select id="lead_farmer_id" name="item[lead_farmer_id]" class="form-control">
                        <option value=""><?php echo $CI->lang->line('SELECT'); ?></option>
                        <?php
                        if ($lead_farmer)
                        {
                            foreach ($lead_farmer as $farmer)
                            {
                                ?>
                                <option value="<?php echo $farmer['value'] ?>" <?php echo ($farmer['value'] == $item['lead_farmer_id']) ? 'selected' : ''; ?>><?php echo $farmer['text']; ?></option>
                            <?php
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>

            <div id="other_farmer_container" style="<?php echo (($item['id'] > 0) && ($item['lead_farmer_id'] > 0)) ? 'display:none' : ''; ?>">
                <div class="row show-grid">
                    <div class="col-xs-4">
                        <label class="control-label pull-right">OR, &nbsp;</label>
                    </div>
                    <div class="col-xs-4"><label class="control-label">
                            <span style="text-decoration:underline"><?php echo $CI->lang->line('LABEL_OTHER_FARMER_NAME'); ?> Information:</span> </label></div>
                </div>

                <div class="row show-grid">
                    <div class="col-xs-4"> &nbsp; </div>
                    <div class="col-xs-4">
                        <table class="new_farmer table table-bordered">
                            <tr>
                                <td><label class="control-label pull-right">Name <span style="color:#FF0000">*</span></label></td>
                                <td>
                                    <input type="text" name="item[name_other_farmer]" class="form-control other_farmer" value="<?php echo $item['name_other_farmer']; ?>"/>
                                </td>
                            </tr>
                            <tr>
                                <td><label class="control-label pull-right">Phone No.<span style="color:#FF0000">*</span></label></td>
                                <td>
                                    <input type="text" name="item[phone_other_farmer]" class="form-control other_farmer" value="<?php echo $item['phone_other_farmer']; ?>"/>
                                </td>
                            </tr>
                            <tr>
                                <td><label class="control-label pull-right">Address <span style="color:#FF0000">*</span></label></td>
                                <td>
                                    <textarea name="item[address_other_farmer]" class="form-control other_farmer"><?php echo nl2br($item['address_other_farmer']); ?></textarea>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

        </div>

        <div class="row show-grid" id="crop_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_NAME'); ?>
                    <span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-xs-4">
                <select name="item[crop_id]" id="crop_id" class="form-control">
                    <option value=""><?php echo $CI->lang->line('SELECT'); ?></option>
                </select>
            </div>
        </div>

        <div style="<?php echo (!($item['id'] > 0)) ? 'display:none' : ''; ?>" class="row show-grid" id="crop_type_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_TYPE'); ?>
                    <span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-xs-4">
                <select name="item[crop_type_id]" id="crop_type_id" class="form-control">
                    <option value=""><?php echo $CI->lang->line('SELECT'); ?></option>
                </select>
            </div>
        </div>

        <div id="variety_container" style="<?php echo (!($item['id'] > 0)) ? 'display:none' : ''; ?>">

            <div class="row show-grid" id="variety1_id_container">
                <div class="col-xs-4">
                    <label class="control-label pull-right">Variety Comparison &nbsp;</label>
                </div>
                <div class="col-xs-6">
                    <table class="table table-bordered">
                        <tr>
                            <th style="width:110px"> &nbsp; </th>
                            <th style="text-align:center"><?php echo $CI->lang->line('LABEL_VARIETY1_NAME'); ?>
                                <span style="color:#FF0000">*</span></th>
                            <th style="text-align:center"><?php echo $CI->lang->line('LABEL_VARIETY2_NAME'); ?></th>
                        </tr>
                        <tr>
                            <td>
                                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_VARIETY_NAME'); ?></label>
                            </td>
                            <td>
                                <select id="variety1_id" name="item[variety1_id]" class="form-control">
                                    <option value=""><?php echo $CI->lang->line('SELECT'); ?></option>
                                    <?php
                                    if ($crop_varieties1)
                                    {
                                        foreach ($crop_varieties1 as $variety1)
                                        {
                                            ?>
                                            <option value="<?php echo $variety1['value'] ?>" <?php echo ($variety1['value'] == $item['variety1_id']) ? 'selected' : ''; ?>><?php echo $variety1['text']; ?></option>
                                        <?php
                                        }
                                    }
                                    ?>
                                </select>
                            </td>
                            <td>
                                <select id="variety2_id" name="item[variety2_id]" class="form-control">
                                    <option value=""><?php echo $CI->lang->line('SELECT'); ?></option>
                                    <?php
                                    if ($crop_varieties2)
                                    {
                                        foreach ($crop_varieties2 as $variety2)
                                        {
                                            ?>
                                            <option value="<?php echo $variety2['value'] ?>" <?php echo ($variety2['value'] == $item['variety2_id']) ? 'selected' : ''; ?>><?php echo $variety2['text']; ?></option>
                                        <?php
                                        }
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_SOWING'); ?></label>
                            </td>
                            <td>
                                <input type="text" name="item[date_sowing_variety1]" id="date_sowing_variety1" class="form-control datepicker" value="<?php echo System_helper::display_date($item['date_sowing_variety1']); ?>" readonly/>
                            </td>
                            <td>
                                <input type="text" name="item[date_sowing_variety2]" id="date_sowing_variety2" class="form-control datepicker" value="<?php echo System_helper::display_date($item['date_sowing_variety2']); ?>" readonly/>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_EXPECTED_EVALUATION'); ?>
                    <span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-xs-4">
                <input type="text" name="item[date_expected_evaluation]" id="date_expected_evaluation" class="form-control datepicker" value="<?php echo System_helper::display_date($item['date_expected_evaluation']); ?>" readonly/>
            </div>
        </div>

    </div>

</form>

<script type="text/javascript">
    // Load Growing Area
    function load_growing_area(outlet_id) {
        $('#growing_area_id').val('');
        $('#lead_farmer_id').val('');
        $('.other_farmer').val('');

        $('#growing_area_id_container').hide();
        $('#lead_farmer_id_container').hide();
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
    }

    jQuery(document).ready(function ($) {
        system_off_events(); // Triggers

        $(".datepicker").datepicker({dateFormat: display_date_format});

        var selected_crop_id = parseInt(<?php echo $item['crop_id']; ?>);
        var selected_crop_type_id = parseInt(<?php echo $item['crop_type_id']; ?>);

        $("#crop_id").html(get_dropdown_with_select(system_crops, selected_crop_id));
        if (selected_crop_id > 0) {
            $("#crop_type_id").html(get_dropdown_with_select(system_types[selected_crop_id], selected_crop_type_id));
        }

        $(document).on("change", "#outlet_id", function () {
            $('#growing_area_id').val('');
            $('#lead_farmer_id').val('');
            $('.other_farmer').val('');

            var outlet_id = parseInt($(this).val());
            $('#growing_area_id_container').hide();
            $('#lead_farmer_id_container').hide();
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

        $(document).on("change", "#growing_area_id", function () {
            $('#lead_farmer_id').val('');
            $('.other_farmer').val('');

            var ga_id = parseInt($(this).val());
            $('#lead_farmer_id_container').hide();
            if (ga_id > 0) {
                $.ajax({
                    url: "<?php echo site_url($CI->controller_url.'/index/get_lead_farmer_by_growing_area/') ?>",
                    type: 'POST',
                    datatype: "JSON",
                    data: {
                        html_container_id: '#lead_farmer_id',
                        id: ga_id
                    },
                    success: function (data, status) {
                        if (data.status) {
                            $('#lead_farmer_id_container').show();
                            $('#other_farmer_container').show();
                        }
                    },
                    error: function (xhr, desc, err) {
                        console.log("error");
                    }
                });
            }
        });

        $("#lead_farmer_id").on('change', function (event) {
            var options = $(this).val();
            if (options.trim() != '') {
                $(".other_farmer").val('');
                $('#other_farmer_container').hide();
            } else {
                $('#other_farmer_container').show();
            }
        });

        /*--------------------- CROP RELATED DROPDOWN ---------------------*/
        $(document).on("change", "#crop_id", function () {
            $("#crop_type_id").val('');
            $("#variety1_id").val('');
            $("#variety2_id").val('');

            var crop_id = $('#crop_id').val();
            $('#crop_type_id_container').hide();
            $('#variety_container').hide();
            if (crop_id > 0) {
                $('#crop_type_id_container').show();
                if (system_types[crop_id] !== undefined) {
                    $("#crop_type_id").html(get_dropdown_with_select(system_types[crop_id]));
                }
            }
        });

        $(document).on("change", "#crop_type_id", function () {
            $("#variety1_id").val('');
            $("#variety2_id").val('');
            var crop_type_id = $('#crop_type_id').val();
            if (crop_type_id > 0) {
                $.ajax({
                    url: "<?php echo site_url($CI->controller_url.'/index/get_arm_competitor_varieties/') ?>",
                    type: 'POST',
                    datatype: "JSON",
                    data: { id: crop_type_id },
                    success: function (data, status) {

                    },
                    error: function (xhr, desc, err) {
                        console.log("error");
                    }
                });
                $('#variety_container').show();
            }
            else {
                $('#variety_container').hide();
            }
        });
        /*--------------------- CROP RELATED DROPDOWN ( END )-------------*/
    });
</script>
