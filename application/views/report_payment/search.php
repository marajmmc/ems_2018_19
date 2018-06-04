<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();

?>
<div class="row widget">
    <div class="widget-header">
        <div class="title">
            <?php echo $title; ?>
        </div>
        <div class="clearfix"></div>
    </div>
    <form class="form_valid" id="save_form" action="<?php echo site_url($CI->controller_url.'/index/list');?>" method="post">
            <div class="row show-grid">
                <div class="col-xs-6">
                    <div class="row show-grid">
                        <div class="col-xs-6">
                            <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_FISCAL_YEAR');?></label>
                        </div>
                        <div class="col-xs-6">
                            <select id="fiscal_year_id" name="report[fiscal_year_id]" class="form-control">
                                <option value=""><?php echo $this->lang->line('SELECT');?></option>
                                <?php
                                foreach($fiscal_years as $year)
                                {?>
                                    <option value="<?php echo $year['value']?>"><?php echo $year['text'];?></option>
                                <?php
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="row show-grid">
                        <div class="col-xs-6">
                            <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE_START');?></label>
                        </div>
                        <div class="col-xs-6">
                            <input type="text" id="date_start" name="report[date_start]" class="form-control date_large" value="<?php echo System_helper::display_date(time()); ?>">
                        </div>

                    </div>
                    <div class="row show-grid">
                        <div class="col-xs-6">
                            <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE_END');?></label>
                        </div>
                        <div class="col-xs-6">
                            <input type="text" id="date_end" name="report[date_end]" class="form-control date_large" value="<?php echo System_helper::display_date(time()); ?>">
                        </div>
                    </div>
                    <div class="row show-grid">
                        <div class="col-xs-6">
                            <label class="control-label pull-right">Search By</label>
                        </div>
                        <div class="col-xs-6">
                            <select id="search_by" name="report[search_by]" class="form-control">
                                <option value="search_by_sale_date">Sale Date</option>
                                <option value="search_by_payment_date">Payment Date</option>
                            </select>
                        </div>
                    </div>
                    <div class="row show-grid">
                        <div class="col-xs-6">
                            <label class="control-label pull-right">Payment Forward Status</label>
                        </div>
                        <div class="col-xs-6">
                            <select id="status_deposit_forward" name="report[status_deposit_forward]" class="form-control">
                                <option value="<?php echo $this->config->item('system_status_forwarded')?>">Forwarded</option>
                                <option value="<?php echo $this->config->item('system_status_pending')?>">Pending</option>

                            </select>
                        </div>
                    </div>
                    <div class="row show-grid">
                        <div class="col-xs-6">
                            <label class="control-label pull-right">Payment Receive Status</label>
                        </div>
                        <div class="col-xs-6">
                            <select id="status_payment_receive" name="report[status_payment_receive]" class="form-control">
                                <option value="<?php echo $this->config->item('system_status_received')?>">Received</option>
                                <option value="<?php echo $this->config->item('system_status_pending')?>">Pending</option>
                                <option value="">ALL</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-xs-6">
                    <!-- Location Section-->
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
                                    <option value=""><?php echo $CI->lang->line('SELECT');?></option>
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
                            <label class="control-label"><?php echo $CI->lang->line('LABEL_DIVISION_NAME');?><span style="color:#FF0000">*</span></label>
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
                                <select id="zone_id" name="report[zone_id]" class="form-control">
                                    <option value=""><?php echo $CI->lang->line('SELECT');?></option>
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
                            <label class="control-label"><?php echo $CI->lang->line('LABEL_ZONE_NAME');?><span style="color:#FF0000">*</span></label>
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
                                <select id="territory_id" name="report[territory_id]" class="form-control">
                                    <option value=""><?php echo $CI->lang->line('SELECT');?></option>
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
                            <label class="control-label"><?php echo $CI->lang->line('LABEL_TERRITORY_NAME');?><span style="color:#FF0000">*</span></label>
                        </div>
                    </div>
                    <div style="<?php if(!(sizeof($districts)>0)){echo 'display:none';} ?>" class="row show-grid" id="district_id_container">
                        <div class="col-xs-6">
                            <?php
                            if($CI->locations['district_id']>0)
                            {
                                ?>
                                <label class="control-label"><?php echo $CI->locations['district_name'];?></label>
                                <input type="hidden" name="report[district_id]" value="<?php echo $CI->locations['district_id'];?>">
                            <?php
                            }
                            else
                            {
                                ?>
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
                            <?php
                            }
                            ?>
                        </div>
                        <div class="col-xs-6">
                            <label class="control-label"><?php echo $CI->lang->line('LABEL_DISTRICT_NAME');?><span style="color:#FF0000">*</span></label>
                        </div>
                    </div>
                    <div style="<?php if(!(sizeof($outlets)>0)){echo 'display:none';} ?>" class="row show-grid" id="outlet_id_container">
                        <div class="col-xs-6">
                            <select id="outlet_id" name="report[outlet_id]" class="form-control">
                                <option value=""><?php echo $CI->lang->line('SELECT');?></option>
                                <?php
                                foreach($outlets as $outlet)
                                {?>
                                    <option value="<?php echo $outlet['value']?>"><?php echo $outlet['text'];?></option>
                                <?php
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-xs-6">
                            <label class="control-label"><?php echo $CI->lang->line('LABEL_OUTLET_NAME');?><span style="color:#FF0000">*</span></label>
                        </div>
                    </div>
                </div>

            </div>
            <div class="row show-grid">
                <div class="col-xs-4">

                </div>
                <div class="col-xs-4">
                    <div class="action_button pull-right">
                        <button id="button_action_report" type="button" class="btn" data-form="#save_form"><?php echo $CI->lang->line("ACTION_REPORT"); ?></button>
                    </div>

                </div>
                <div class="col-xs-4">

                </div>
            </div>
    </form>
</div>
<div class="clearfix"></div>


<div id="system_report_container">

</div>
<script type="text/javascript">

    jQuery(document).ready(function()
    {
        $(".date_large").datepicker({dateFormat : display_date_format,changeMonth: true,changeYear: true,yearRange: "2015:+0"});
        $(document).off("change", "#division_id");
        $(document).on('change','#division_id',function()
        {
            $('#zone_id').val('');
            $('#territory_id').val('');
            $('#district_id').val('');
            $('#outlet_id').val('');
            var division_id=$('#division_id').val();
            $('#zone_id_container').hide();
            $('#territory_id_container').hide();
            $('#district_id_container').hide();
            $('#outlet_id_container').hide();
            $("#items_container").html('');
            if(division_id>0)
            {
                if(system_zones[division_id]!==undefined)
                {
                    $('#zone_id_container').show();
                    $('#zone_id').html(get_dropdown_with_select(system_zones[division_id]));
                }
            }

        });
        $(document).off("change", "#zone_id");
        $(document).on('change','#zone_id',function()
        {
            $('#territory_id').val('');
            $('#district_id').val('');
            $('#outlet_id').val('');
            var zone_id=$('#zone_id').val();
            $('#territory_id_container').hide();
            $('#district_id_container').hide();
            $('#outlet_id_container').hide();
            $("#items_container").html('');
            if(zone_id>0)
            {
                if(system_territories[zone_id]!==undefined)
                {
                    $('#territory_id_container').show();
                    $('#territory_id').html(get_dropdown_with_select(system_territories[zone_id]));
                }
            }
        });
        $(document).off("change", "#territory_id");
        $(document).on('change','#territory_id',function()
        {
            $('#district_id').val('');
            $('#outlet_id').val('');
            $('#outlet_id_container').hide();
            $('#district_id_container').hide();
            $("#items_container").html('');
            var territory_id=$('#territory_id').val();
            if(territory_id>0)
            {
                if(system_districts[territory_id]!==undefined)
                {
                    $('#district_id_container').show();
                    $('#district_id').html(get_dropdown_with_select(system_districts[territory_id]));
                }

            }
        });
        $(document).off("change", "#district_id");
        $(document).on('change','#district_id',function()
        {
            $('#outlet_id').val('');
            $('#upazilla_id').val('');
            $("#items_container").html('');
            var district_id=$('#district_id').val();
            $('#outlet_id_container').hide();
            if(district_id>0)
            {
                if(system_outlets[district_id]!==undefined)
                {
                    $('#outlet_id_container').show();
                    $('#outlet_id').html(get_dropdown_with_select(system_outlets[district_id]));
                }
            }
        });
        $(document).off("change", "#fiscal_year_id");
        $(document).on("change","#fiscal_year_id",function()
        {

            var fiscal_year_ranges=$('#fiscal_year_id').val();
            if(fiscal_year_ranges!='')
            {
                var dates = fiscal_year_ranges.split("/");
                $("#date_start").val(dates[0]);
                $("#date_end").val(dates[1]);

            }
        });
    });
</script>
