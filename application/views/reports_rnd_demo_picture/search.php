<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();
$action_buttons=array();
$action_buttons[]=array(
    'label'=>$CI->lang->line("ACTION_REFRESH"),
    'href'=>site_url($CI->controller_url.'/index')
);
$CI->load->view('action_buttons',array('action_buttons'=>$action_buttons));

?>
<form class="form_valid" id="search_form" action="<?php echo site_url($CI->controller_url.'/index/list_variety');?>" method="post">
    <div class="row widget">
        <div class="widget-header">
            <div class="title">
                <?php echo $title; ?>
            </div>
            <div class="clearfix"></div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-8">
                <div class="row show-grid">
                    <div class="col-xs-6">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_YEAR');?></label>
                    </div>
                    <div class="col-xs-6">
                        <select id="year" name="report[year]" class="form-control">
                            <option value=""><?php echo $this->lang->line('SELECT');?></option>
                            <?php
                            foreach($years as $year)
                            {?>
                                <option value="<?php echo $year['year'];?>"><?php echo $year['year'];?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="row show-grid">
                    <div class="col-xs-6">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_SEASON');?></label>
                    </div>
                    <div class="col-xs-6">
                        <select id="season_id" name="report[season_id]" class="form-control">
                            <option value=""><?php echo $this->lang->line('SELECT');?></option>
                            <?php
                            foreach($seasons as $season)
                            {?>
                                <option value="<?php echo $season['value'];?>"><?php echo $season['text'];?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="row show-grid">
                    <div class="col-xs-6">

                    </div>
                    <div class="col-xs-6">
                        <div class="action_button pull-right">
                            <button type="button" class="btn" id="but_load_crop">Load Crop</button>
                        </div>
                    </div>
                    <div class="col-xs-4">

                    </div>
                </div>
                <div class="row show-grid" id="crop_id_container">
                    <div class="col-xs-6">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_NAME');?></label>
                    </div>
                    <div class="col-xs-6">
                        <select id="crop_id" name="report[crop_id]" class="form-control">
                            <option value=""><?php echo $this->lang->line('SELECT');?></option>
                            <?php
                            foreach($crops as $crop)
                            {?>
                                <option value="<?php echo $crop['value']?>"><?php echo $crop['text'];?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div style="display: none;" class="row show-grid" id="crop_type_id_container">
                    <div class="col-xs-6">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_TYPE_NAME');?></label>
                    </div>
                    <div class="col-xs-6">
                        <select id="crop_type_id" name="report[crop_type_id]" class="form-control">
                            <option value=""><?php echo $this->lang->line('SELECT');?></option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-xs-4">

            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">

            </div>
            <div class="col-xs-4">
                <div class="action_button pull-right">
                    <button type="submit" class="btn" data-form="#search_form"><?php echo $CI->lang->line("LABEL_LOAD_VARIETY"); ?></button>
                </div>
            </div>
            <div class="col-xs-4">

            </div>
        </div>
    </div>
    <div class="clearfix"></div>
</form>
<div id="variety_list_container">

</div>

<div id="system_report_container">

</div>

<script type="text/javascript">

    jQuery(document).ready(function()
    {
        system_preset({controller:'<?php echo $CI->router->class; ?>'});
        $(document).off("click", ".pop_up");
        $(document).off("change", "#select_all_arm");
        $(document).off("change", "#select_all_competitor");
        $(document).off("change", "#select_all_upcoming");
        $(document).off("click", "#but_load_crop");
        $(document).on("click", ".pop_up", function(event)
        {
            var left=((($(window).width() - 550) / 2) +$(window).scrollLeft());
            var top=((($(window).height() - 550) / 2) +$(window).scrollTop());
            //$("#popup_window").jqxWindow({width: 630,height:550,position: { x: 60, y: 60  }});to change position always
            $('#popup_window').jqxWindow({position: { x: left, y: top  }});
            var row=$(this).attr('data-item-no');
            var key=$(this).attr('data-details-key');
            var row_info = $("#system_jqx_container").jqxGrid('getrowdata', row);
            var pop_up_content='';

            pop_up_content+='<div>';
            pop_up_content+='<div><img src="'+row_info['details'][key]['image_url']+'" style="max-width: 100%;"></div>';
            pop_up_content+='<div style="text-align:center;margin-bottom:5px;">Date: '+row_info['details'][key]['day_visit']+'</div>';
            pop_up_content+='<div>';
            pop_up_content+='<div> Remarks: <div  style="font-size: 15px;font-weight:bold;">'+row_info['details'][key]['remarks']+'</div></div>';
            pop_up_content+='<div>Remarks Entry time: <div>'+row_info['details'][key]['remarks_time']+'</div></div>';
            pop_up_content+='<div>Remarks By: <div>'+row_info['details'][key]['remarks_by']+'</div></div>';
            pop_up_content+='</div>';

            pop_up_content+='</div>';

            $('#popup_content').html(pop_up_content);
            $('#popup_window').jqxWindow('open');
        });
        $(document).on("change","#select_all_arm",function()
        {
            if($(this).is(':checked'))
            {
                $('.setup_arm').prop('checked', true);
            }
            else
            {
                $('.setup_arm').prop('checked', false);
            }

        });
        $(document).on("change","#select_all_competitor",function()
        {
            if($(this).is(':checked'))
            {
                $('.setup_competitor').prop('checked', true);
            }
            else
            {
                $('.setup_competitor').prop('checked', false);
            }

        });
        $(document).on("change","#select_all_upcoming",function()
        {
            if($(this).is(':checked'))
            {
                $('.setup_upcoming').prop('checked', true);
            }
            else
            {
                $('.setup_upcoming').prop('checked', false);
            }

        });
        $(document).on("click","#but_load_crop",function()
        {
            $('#crop_type_id_container').hide();
            $('#variety_id_container').hide();
            $("#crop_type_id").val("");
            $("#variety_id").val("");
            $.ajax({
                url: '<?php echo site_url($CI->controller_url.'/index/load_crops');?>',
                type: 'post',
                dataType: "JSON",
                data: new FormData(document.getElementById('search_form')),
                processData: false,
                contentType: false,
                success: function (data, status)
                {

                },
                error: function (xhr, desc, err)
                {


                }
            });

        });
        $(document).on("change","#year",function()
        {
            $('#variety_list_container').html('');
        });
        $(document).on("change","#season_id",function()
        {
            $('#variety_list_container').html('');
        });
        $(document).on("change","#crop_id",function()
        {
            $("#crop_type_id").val("");
            $("#variety_id").val("");
            $('#variety_list_container').html('');
            $('#system_report_container').html('');
            var crop_id=$('#crop_id').val();
            $('#variety_id_container').hide();
            if(crop_id>0)
            {
                if(system_types[crop_id]!==undefined)
                {
                    $('#crop_type_id_container').show();
                    $('#crop_type_id').html(get_dropdown_with_select(system_types[crop_id]));
                }
                else
                {
                    $('#crop_type_id_container').hide();
                }
            }
            else
            {
                $('#crop_type_id_container').hide();
            }
        });
    });
</script>
