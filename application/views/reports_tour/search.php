<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();
$employee_info_for_division=array();
foreach($user_info as $user)
{
    $employee_info_for_division[$user['division_id']][]=$user;
}
$employee_info_for_zone=array();
foreach($user_info as $user)
{
    $employee_info_for_zone[$user['zone_id']][]=$user;
}
$employee_info_for_territory=array();
foreach($user_info as $user)
{
    $employee_info_for_territory[$user['territory_id']][]=$user;
}
?>
<form class="form_valid" id="save_form" action="<?php echo site_url($CI->controller_url.'/index/list');?>" method="post">
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
                    <label class="control-label pull-right">Tour Approve Status</label>
                </div>
                <div class="col-xs-6">
                    <select name="report[status_approve]" class="form-control">
                        <option value=""><?php echo $this->lang->line('LABEL_SELECT_ALL');?></option>
                        <option value="<?php echo $CI->config->item('system_status_approved')?>">Approve</option>
                        <option value="<?php echo $CI->config->item('system_status_pending')?>">Pending</option>
                        <option value="<?php echo $CI->config->item('system_status_rollback') ?>">Roll Back</option>
                    </select>
                </div>
            </div>
            <div class="row show-grid">
                <div class="col-xs-6">
                    <label class="control-label pull-right">Date Type</label>
                </div>
                <div class="col-xs-6">
                    <select name="report[date_type]" class="form-control">
                        <option value=""><?php echo $this->lang->line('LABEL_SELECT_ALL');?></option>
                        <option value="tour_created_time">Tour Created Time</option>
                        <option value="approve_date_time">Approve Date Time</option>
                        <option value="reporting_date">Reporting Date</option>
                    </select>
                </div>
            </div>
            <div class="row show-grid">
                <div class="col-xs-6">
                    <label class="control-label pull-right">From Date</label>
                </div>
                <div class="col-xs-6">
                    <input type="text" id="date_start" name="report[date_from]" class="form-control date_large" value="<?php echo $date_from; ?>">
                </div>
            </div>
            <div class="row show-grid">
                <div class="col-xs-6">
                    <label class="control-label pull-right">To Date</label>
                </div>
                <div class="col-xs-6">
                    <input type="text" id="date_end" name="report[date_to]" class="form-control date_large" value="<?php echo $date_to; ?>">
                </div>
            </div>
        </div>

        <div class="col-xs-6">
            <div class="row show-grid">
                <div class="col-xs-6">
                    <?php
                    if($CI->locations['division_id']>0)
                    {
                        ?>
                        <label class="control-label"><?php echo $CI->locations['division_name'];?></label>
                        <input type="hidden" name="report[division_id]" value="<?php echo $CI->locations['division_id'];?>">
                    <?php
                    }
                    else
                    {
                        ?>
                        <select id="division_id" name="report[division_id]" class="form-control">
                            <option value=""><?php echo $this->lang->line('SELECT');?></option>
                            <?php
                            foreach($divisions as $division)
                            {?>
                                <option value="<?php echo $division['value']?>"><?php echo $division['text'];?></option>
                            <?php
                            }
                            ?>
                        </select>
                    <?php
                    }
                    ?>
                </div>
                <div class="col-xs-6">
                    <label class="control-label"><?php echo $CI->lang->line('LABEL_DIVISION_NAME');?></label>
                </div>
            </div>
            <div style="<?php if(!(sizeof($zones)>0)){echo 'display:none';} ?>" class="row show-grid" id="zone_id_container">
                <div class="col-xs-6">
                    <?php
                    if($CI->locations['zone_id']>0)
                    {
                        ?>
                        <label class="control-label"><?php echo $CI->locations['zone_name'];?></label>
                        <input type="hidden" name="report[zone_id]" value="<?php echo $CI->locations['zone_id'];?>">
                    <?php
                    }
                    else
                    {
                        ?>
                        <select id="zone_id" class="form-control" name="report[zone_id]">
                            <option value=""><?php echo $this->lang->line('SELECT');?></option>
                            <?php
                            foreach($zones as $zone)
                            {?>
                                <option value="<?php echo $zone['value']?>"><?php echo $zone['text'];?></option>
                            <?php
                            }
                            ?>
                        </select>
                    <?php
                    }
                    ?>
                </div>
                <div class="col-xs-6">
                    <label class="control-label"><?php echo $CI->lang->line('LABEL_ZONE_NAME');?></label>
                </div>
            </div>
            <div style="<?php if(!(sizeof($territories)>0)){echo 'display:none';} ?>" class="row show-grid" id="territory_id_container">
                <div class="col-xs-6">
                    <?php
                    if($CI->locations['territory_id']>0)
                    {
                        ?>
                        <label class="control-label"><?php echo $CI->locations['territory_name'];?></label>
                        <input type="hidden" name="report[territory_id]" value="<?php echo $CI->locations['territory_id'];?>">
                    <?php
                    }
                    else
                    {
                        ?>
                        <select id="territory_id" class="form-control" name="report[territory_id]">
                            <option value=""><?php echo $this->lang->line('SELECT');?></option>
                            <?php
                            foreach($territories as $territory)
                            {?>
                                <option value="<?php echo $territory['value']?>"><?php echo $territory['text'];?></option>
                            <?php
                            }
                            ?>
                        </select>
                    <?php
                    }
                    ?>
                </div>
                <div class="col-xs-6">
                    <label class="control-label"><?php echo $CI->lang->line('LABEL_TERRITORY_NAME');?></label>
                </div>
            </div>
            <div class="row show-grid">
                <div class="col-xs-6">
                    <select id="employee_info_id" name="report[user_id]" class="form-control">
                        <option value=""><?php echo $this->lang->line('SELECT');?></option>
                        <?php foreach($user_info as $user){?>
                            <option value="<?php echo $user['value']?>"><?php echo $user['text'];?></option>
                        <?php } ?>

                    </select>
                </div>
                <div class="col-xs-6">
                    <label class="control-label"><?php echo $CI->lang->line('LABEL_EMPLOYEE');?></label>
                </div>
            </div>
            <div class="row show-grid text-center">
                <div class="col-xs-6">
                    <label class=" control-label">OR</label>
                </div>
            </div>
            <div class="row show-grid">
                <div class="col-xs-6">
                    <input type="text" name="report[employee_id]" class="form-control text-right float_type_positive" value=""/>
                </div>
                <div class="col-xs-6">
                    <label class="control-label"><?php echo $CI->lang->line('LABEL_EMPLOYEE_ID');?></label>
                </div>
            </div>
        </div>
    </div>

    <div class="row show-grid">
        <div class="col-xs-4">

        </div>
        <div class="col-xs-4">
            <div class="action_button pull-right">
                <button id="button_action_report" type="button" class="btn" data-form="#save_form"><?php echo $CI->lang->line("ACTION_REPORT_VIEW"); ?></button>
            </div>
        </div>
        <div class="col-xs-4">

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
        system_preset({controller:'<?php echo $CI->router->class; ?>'});
        $(".date_large").datepicker({dateFormat : display_date_format,changeMonth: true,changeYear: true,yearRange: "2015:+0"});
        $(document).off('change', '#division_id');
        $(document).off('change', '#zone_id');
        $(document).off('change', '#territory_id');

        $(document).on('change','#division_id',function()
        {
            $('#zone_id').val('');
            var division_id=$('#division_id').val();
            $('#zone_id_container').hide();
            if(division_id>0)
            {
                if(system_zones[division_id]!==undefined)
                {
                    $('#zone_id_container').show();
                    $('#zone_id').html(get_dropdown_with_select(system_zones[division_id]));
                }
                if(employee_info_list_division[division_id]!==undefined)
                {
                    $('#employee_info_id').html('');
                    $('#employee_info_id').html(get_dropdown_with_select(employee_info_list_division[division_id]));
                }
            }else
            {
                $('#zone_id_container').hide();
                $('#territory_id_container').hide();
                $('#employee_info_id').html('');
                $.ajax({
                    url:"<?php echo site_url($CI->controller_url.'/get_employee_info_list/');?>",
                    type: 'POST',
                    datatype: "JSON",
                    success: function (data, status)
                    {

                    },
                    error: function (xhr, desc, err)
                    {
                        console.log("error");

                    }
                });
            }

        });

        $(document).on('change','#zone_id',function()
        {
            $('#territory_id').val('');
            $('#employee_info_id').val('');
            $('#territory_id_container').hide();
            var division_id=$('#division_id').val();
            var zone_id=$('#zone_id').val();
            if(zone_id>0)
            {
                if(system_territories[zone_id]!==undefined)
                {
                    $('#territory_id_container').show();
                    $('#territory_id').html(get_dropdown_with_select(system_territories[zone_id]));
                }
                if(employee_info_list_zone[zone_id]!==undefined)
                {
                    $('#employee_info_id').html('');
                    $('#employee_info_id').html(get_dropdown_with_select(employee_info_list_zone[zone_id]));
                }
                else
                {
                    $('#employee_info_id').html('<?php echo '<option value="">'.$this->lang->line('SELECT').'</option>';?>');
                }
            }
            else
            {
                $('#territory_id_container').hide();
                if(division_id==undefined)
                {
                    var division_id='<?php echo $CI->locations['division_id']?>';
                }
                $('#employee_info_id').html('');
                $('#employee_info_id').html(get_dropdown_with_select(employee_info_list_division[division_id]));
            }
        });

        $(document).on('change','#territory_id',function()
        {
            $('#employee_info_id').val('');
            var territory_id=$('#territory_id').val();
            var zone_id=$('#zone_id').val();
            if(territory_id>0)
            {
                if(employee_info_list_territory[territory_id]!==undefined)
                {
                    $('#employee_info_id').html('');
                    $('#employee_info_id').html(get_dropdown_with_select(employee_info_list_territory[territory_id]));
                }
                else
                {
                    $('#employee_info_id').html('<?php echo '<option value="">'.$this->lang->line('SELECT').'</option>';?>');
                }
            }
            else
            {
                $('#area_id_container').hide();
                if(zone_id==undefined)
                {
                    var zone_id='<?php echo $CI->locations['zone_id']?>';
                }
                $('#employee_info_id').html('');
                $('#employee_info_id').html(get_dropdown_with_select(employee_info_list_zone[zone_id]));
            }
        });
    });
</script>
<script type="text/javascript">
    var employee_info_list_division=JSON.parse('<?php echo json_encode($employee_info_for_division);?>');
    var employee_info_list_zone=JSON.parse('<?php echo json_encode($employee_info_for_zone);?>');
    var employee_info_list_territory=JSON.parse('<?php echo json_encode($employee_info_for_territory);?>');
</script>