<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();
?>
<form class="form_valid" id="search_form" action="<?php echo site_url($CI->controller_url.'/index/list');?>" method="post">
    <div class="row widget">
        <div class="widget-header">
            <div class="title">
                <?php echo $title; ?>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-6">
                <div class="row show-grid">
                    <div class="col-xs-6">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_START');?></label>
                    </div>
                    <div class="col-xs-6">
                        <input type="text" id="date_start" name="report[date_start]" class="form-control date_large" value="">
                    </div>
                </div>
                <div class="row show-grid">
                    <div class="col-xs-6">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_END');?></label>
                    </div>
                    <div class="col-xs-6">
                        <input type="text" id="date_end" name="report[date_end]" class="form-control date_large" value="<?php echo System_helper::display_date(time());?>">
                    </div>
                </div>
            </div>
            <div class="col-xs-6">
                <div style="" class="row show-grid">
                    <div class="col-xs-6">
                        <select id="district_id" name="report[district_id]" class="form-control">
                            <option value=""><?php echo $CI->lang->line('SELECT');?></option>
                            <?php
                            foreach($districts as $district)
                            {?>
                                <option value="<?php echo $district['value']?>"><?php echo $district['text'];?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-xs-6">
                        <label class="control-label"><?php echo $CI->lang->line('LABEL_DISTRICT_NAME');?></label>
                    </div>
                </div>
                <div style="display:none" class="row show-grid" id="upazilla_id_container">
                    <div class="col-xs-6">
                        <select id="upazilla_id" class="form-control" name="report[upazilla_id]">
                            <option value=""><?php echo $CI->lang->line('SELECT');?></option>
                        </select>
                    </div>
                    <div class="col-xs-6">
                        <label class="control-label"><?php echo $CI->lang->line('LABEL_UPAZILLA_NAME');?></label>
                    </div>
                </div>
                <div style="display:none" class="row show-grid" id="union_id_container">

                    <div class="col-xs-6">
                        <select id="union_id" class="form-control" name="report[union_id]">
                            <option value=""><?php echo $CI->lang->line('SELECT');?></option>
                        </select>
                    </div>
                    <div class="col-xs-6">
                        <label class="control-label"><?php echo $CI->lang->line('LABEL_UNION_NAME');?></label>
                    </div>
                </div>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
            </div>
            <div class="col-xs-5">
                <div class="action_button pull-right">
                    <button id="button_action_report" type="button" class="btn" data-form="#search_form"><?php echo $CI->lang->line("ACTION_REPORT"); ?></button>
                </div>
            </div>
            <div class="col-xs-3">
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
</form>
<div id="system_report_container">

</div>

<script type="text/javascript">
    jQuery(document).ready(function()
    {
        $(".date_large").datepicker({dateFormat : display_date_format,changeMonth: true,changeYear: true,yearRange: "c-1:c+1"});

        var upazillas=JSON.parse('<?php echo json_encode($upazillas);?>');
        var unions=JSON.parse('<?php echo json_encode($unions);?>');


        $(document).off('change', '#district_id');
        $(document).on("change","#district_id",function()
        {
            $('#system_report_container').html('');
            $('#upazilla_id_container').hide();
            $('#union_id_container').hide();
            $("#upazilla_id").val("");
            $("#union_id").val("");
            var district_id=$('#district_id').val();
            if(district_id>0)
            {
                if(upazillas[district_id]!==undefined)
                {
                    $('#upazilla_id_container').show();
                    $("#upazilla_id").html(get_dropdown_with_select(upazillas[district_id]));
                }
            }
        });
        $(document).off('change', '#upazilla_id');
        $(document).on("change","#upazilla_id",function()
        {
            $('#system_report_container').html('');
            $("#union_id").val("");
            $('#union_id_container').hide();
            var upazilla_id=$('#upazilla_id').val();
            if(upazilla_id>0)
            {
                if(unions[upazilla_id]!==undefined)
                {
                    $('#union_id_container').show();
                    $("#union_id").html(get_dropdown_with_select(unions[upazilla_id]));
                }
            }
        });
        $(document).off('change', '#union_id');
    });
</script>
