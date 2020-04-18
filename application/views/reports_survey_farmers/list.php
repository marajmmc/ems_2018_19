<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();
if(isset($CI->permissions['action4']) && ($CI->permissions['action4']==1))
{
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_PRINT"),
        'class'=>'button_action_download',
        'data-title'=>"Print",
        'data-print'=>true
    );
}
if(isset($CI->permissions['action5']) && ($CI->permissions['action5']==1))
{
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_DOWNLOAD"),
        'class'=>'button_action_download',
        'data-title'=>"Download"
    );
}
if(isset($CI->permissions['action6']) && ($CI->permissions['action6']==1))
{
    $action_buttons[]=array
    (
        'label'=>'Preference',
        'href'=>site_url($CI->controller_url.'/index/set_preference')
    );
}
$CI->load->view('action_buttons',array('action_buttons'=>$action_buttons));
?>
<div class="row widget">
    <div class="widget-header">
        <div class="title">
            <?php echo $title; ?>
        </div>
        <div class="clearfix"></div>
    </div>

    <?php
    /*if(isset($CI->permissions['action6']) && ($CI->permissions['action6']==1))
    {
        $CI->load->view('preference',array('system_preference_items'=>$system_preference_items));
    }*/
    ?>
    <?php
    if(isset($CI->permissions['action6']) && ($CI->permissions['action6']==1))
    {

        ?>
        <div class="col-xs-2 "><div class="checkbox"><label><input type="checkbox" class="system_jqx_column" value="sl_no" <?php if($system_preference_items['sl_no']){echo 'checked';}?>><span class=""><?php echo $CI->lang->line('LABEL_SL_NO'); ?></span></label></div></div>
        <div class="col-xs-2 "><div class="checkbox"><label><input type="checkbox" class="system_jqx_column" value="farmer_name" <?php if($system_preference_items['farmer_name']){echo 'checked';}?>><span class=""><?php echo $CI->lang->line('LABEL_FARMER_NAME'); ?></span></label></div></div>
        <div class="col-xs-2 "><div class="checkbox"><label><input type="checkbox" class="system_jqx_column" value="mobile_no" <?php if($system_preference_items['mobile_no']){echo 'checked';}?>><span class=""><?php echo $CI->lang->line('LABEL_MOBILE_NO'); ?></span></label></div></div>
        <div class="col-xs-2 "><div class="checkbox"><label><input type="checkbox" class="system_jqx_column" value="district_name" <?php if($system_preference_items['district_name']){echo 'checked';}?>><span class=""><?php echo $CI->lang->line('LABEL_DISTRICT_NAME'); ?></span></label></div></div>
        <div class="col-xs-2 "><div class="checkbox"><label><input type="checkbox" class="system_jqx_column" value="upazilla_name" <?php if($system_preference_items['upazilla_name']){echo 'checked';}?>><span class=""><?php echo $CI->lang->line('LABEL_UPAZILLA_NAME'); ?></span></label></div></div>
        <div class="col-xs-2 "><div class="checkbox"><label><input type="checkbox" class="system_jqx_column" value="union_name" <?php if($system_preference_items['union_name']){echo 'checked';}?>><span class=""><?php echo $CI->lang->line('LABEL_UNION_NAME'); ?></span></label></div></div>
        <div class="col-xs-2 "><div class="checkbox"><label><input type="checkbox" class="system_jqx_column_cultivated_area value="cultivated_area" <?php if($system_preference_items['cultivated_area']){echo 'checked';}?>><span class=""><?php echo $CI->lang->line('SURVEY_FARMER_TITLE_CULTIVATED_AREA'); ?></span></label></div></div>
        <div class="col-xs-2 "><div class="checkbox"><label><input type="checkbox" class="system_jqx_column" value="have_vegetables_training" <?php if($system_preference_items['have_vegetables_training']){echo 'checked';}?>><span class=""><?php echo $CI->lang->line('SURVEY_FARMER_TITLE_HAVE_VEGETABLES_TRAINING'); ?></span></label></div></div>
        <div class="col-xs-2 "><div class="checkbox"><label><input type="checkbox" class="system_jqx_column_seeds_collect" value="seeds_collect" <?php if($system_preference_items['seeds_collect']){echo 'checked';}?>><span class=""><?php echo $CI->lang->line('SURVEY_FARMER_TITLE_SEEDS_COLLECT'); ?></span></label></div></div>
        <div class="col-xs-2 "><div class="checkbox"><label><input type="checkbox" class="system_jqx_column_sell_vegetables" value="sell_vegetables" <?php if($system_preference_items['sell_vegetables']){echo 'checked';}?>><span class=""><?php echo $CI->lang->line('SURVEY_FARMER_TITLE_SELL_VEGETABLES_TO'); ?></span></label></div></div>

    <?php
    }
    ?>
    <div style="" class="row show-grid">
        <div class="col-xs-12 ">
            <input type="button" value="X Remove Filter" id="clearfilteringbutton" class="btn btn-danger pull-right" />
        </div>
    </div>

    <div class="col-xs-12" id="system_jqx_container">

    </div>
</div>
<div class="clearfix"></div>
<script type="text/javascript">
    $(document).ready(function ()
    {
        system_preset({controller:'<?php echo $CI->router->class; ?>'});
        $(document).off("click", ".system_jqx_column_cultivated_area");
        $(document).on("click", ".system_jqx_column_cultivated_area", function(event)
        {
            var jqx_grid_id='#system_jqx_container';
            $(jqx_grid_id).jqxGrid('beginupdate');
            if($(this).is(':checked'))
            {
                $(jqx_grid_id).jqxGrid('showcolumn', 'cultivated_area_vegetables');
                $(jqx_grid_id).jqxGrid('showcolumn', 'cultivated_area_others');
            }
            else
            {
                $(jqx_grid_id).jqxGrid('hidecolumn', 'cultivated_area_vegetables');
                $(jqx_grid_id).jqxGrid('hidecolumn', 'cultivated_area_others');
            }
            $(jqx_grid_id).jqxGrid('endupdate');
        });
        $(document).off("click", ".system_jqx_column_seeds_collect");
        $(document).on("click", ".system_jqx_column_seeds_collect", function(event)
        {
            var jqx_grid_id='#system_jqx_container';
            $(jqx_grid_id).jqxGrid('beginupdate');
            if($(this).is(':checked'))
            {
                $(jqx_grid_id).jqxGrid('showcolumn', 'seeds_collect_dealers');
                $(jqx_grid_id).jqxGrid('showcolumn', 'seeds_collect_retailers');
                $(jqx_grid_id).jqxGrid('showcolumn', 'seeds_collect_leadfarmers');
                $(jqx_grid_id).jqxGrid('showcolumn', 'seeds_collect_hatbazar');
                $(jqx_grid_id).jqxGrid('showcolumn', 'seeds_collect_ownseeds');
                $(jqx_grid_id).jqxGrid('showcolumn', 'seeds_collect_others');
            }
            else
            {
                $(jqx_grid_id).jqxGrid('hidecolumn', 'seeds_collect_dealers');
                $(jqx_grid_id).jqxGrid('hidecolumn', 'seeds_collect_retailers');
                $(jqx_grid_id).jqxGrid('hidecolumn', 'seeds_collect_leadfarmers');
                $(jqx_grid_id).jqxGrid('hidecolumn', 'seeds_collect_hatbazar');
                $(jqx_grid_id).jqxGrid('hidecolumn', 'seeds_collect_ownseeds');
                $(jqx_grid_id).jqxGrid('hidecolumn', 'seeds_collect_others');
            }
            $(jqx_grid_id).jqxGrid('endupdate');
        });
        $(document).off("click", ".system_jqx_column_sell_vegetables");
        $(document).on("click", ".system_jqx_column_sell_vegetables", function(event)
        {
            var jqx_grid_id='#system_jqx_container';
            $(jqx_grid_id).jqxGrid('beginupdate');
            if($(this).is(':checked'))
            {
                $(jqx_grid_id).jqxGrid('showcolumn', 'sell_vegetables_to_artodar_paikar');
                $(jqx_grid_id).jqxGrid('showcolumn', 'sell_vegetables_to_hatbazar');
                $(jqx_grid_id).jqxGrid('showcolumn', 'sell_vegetables_in_group');
                $(jqx_grid_id).jqxGrid('showcolumn', 'sell_vegetables_others');
            }
            else
            {
                $(jqx_grid_id).jqxGrid('hidecolumn', 'sell_vegetables_to_artodar_paikar');
                $(jqx_grid_id).jqxGrid('hidecolumn', 'sell_vegetables_to_hatbazar');
                $(jqx_grid_id).jqxGrid('hidecolumn', 'sell_vegetables_in_group');
                $(jqx_grid_id).jqxGrid('hidecolumn', 'sell_vegetables_others');
            }
            $(jqx_grid_id).jqxGrid('endupdate');
        });
        $(document).off("click", ".pop_up");
        $(document).on("click", ".pop_up", function(event)
        {
            var left=((($(window).width()-550)/2)+$(window).scrollLeft());
            var top=((($(window).height()-550)/2)+$(window).scrollTop());
            $("#popup_window").jqxWindow({width: 1200,height:550,position:{x:left,y:top}}); //to change position always
            //$("#popup_window").jqxWindow({position:{x:left,y:top}});
            var row=$(this).attr('data-item-no');
            var id=$("#system_jqx_container").jqxGrid('getrowdata',row).id;
            $.ajax(
                {
                    url: "<?php echo site_url($CI->controller_url.'/index/details') ?>",
                    type: 'POST',
                    datatype: "JSON",
                    data:
                    {
                        html_container_id:'#popup_content',
                        id:id
                    },
                    success: function (data, status)
                    {

                    },
                    error: function (xhr, desc, err)
                    {
                        console.log("error");
                    }
                });
            $("#popup_window").jqxWindow('open');
        });
        var url = "<?php echo site_url($CI->controller_url.'/index/get_items');?>";

        // prepare the data
        var source =
        {
            dataType: "json",
            dataFields: [
                { name: 'id', type: 'int' },
                <?php
                foreach($system_preference_items as $key => $value)
                {
                    if($key=='cultivated_area_vegetables' || $key=='cultivated_area_others')
                    {
                        ?>
                        { name: '<?php echo $key; ?>', type: 'number' },
                        <?php
                    }
                    else
                    {
                        ?>
                        { name: '<?php echo $key; ?>', type: 'string' },
                        <?php
                    }

                } ?>
            ],
            id: 'id',
            type: 'POST',
            url: url,
            data:JSON.parse('<?php echo json_encode($options);?>')
        };
        var cellsrenderer = function(row, column, value, defaultHtml, columnSettings, record)
        {
            var element = $(defaultHtml);
            element.css({'margin': '0px','width': '100%', 'height': '100%',padding:'5px'});
            if(column=='details_button')
            {
                element.html('<div><button class="btn btn-primary pop_up" data-item-no="'+row+'">Details</button></div>');
            }
            return element[0].outerHTML;

        };
        var tooltiprenderer = function (element) {
            $(element).jqxTooltip({position: 'mouse', content: $(element).text() });
        };
        var dataAdapter = new $.jqx.dataAdapter(source);
        // create jqxgrid.
        $("#system_jqx_container").jqxGrid(
        {
            width: '100%',
            height:'350px',
            source: dataAdapter,
            filterable: true,
            sortable: true,
            showfilterrow: true,
            columnsresize: true,
            columnsreorder: true,
            altrows: true,
            enabletooltips: true,
            showstatusbar: true,
            rowsheight: 45,
            enablebrowserselection:true,
            columns: [
                { text: 'ID',pinned:true,dataField: 'id',width:'110',cellsrenderer: cellsrenderer,rendered:tooltiprenderer, hidden: true},
                {
                    text: '<?php echo $CI->lang->line('LABEL_SL_NO'); ?>',datafield: 'sl_no',pinned:true,width:'30', hidden: <?php echo $system_preference_items['sl_no']?0:1;?>, columntype: 'number',cellsalign: 'right', sortable: false, menu: false,
                    cellsrenderer: function(row, column, value, defaultHtml, columnSettings, record)
                    {
                        var element = $(defaultHtml);
                        element.html(value+1);
                        return element[0].outerHTML;
                    }
                },
                { text: '<?php echo $CI->lang->line('LABEL_FARMER_NAME'); ?>',pinned:true,dataField: 'farmer_name',width:'200',rendered:tooltiprenderer,hidden: <?php echo $system_preference_items['farmer_name']?0:1;?>},
                { text: '<?php echo $CI->lang->line('SURVEY_FARMER_MOBILE_NO'); ?>',pinned:true, dataField: 'mobile_no',width:'100',rendered:tooltiprenderer,hidden: <?php echo $system_preference_items['mobile_no']?0:1;?>},
                { text: '<?php echo $CI->lang->line('LABEL_DISTRICT_NAME'); ?>', dataField: 'district_name',filtertype: 'list',width:'100',rendered:tooltiprenderer,hidden: <?php echo $system_preference_items['district_name']?0:1;?>},
                { text: '<?php echo $CI->lang->line('LABEL_UPAZILLA_NAME'); ?>', dataField: 'upazilla_name',width:'100',rendered:tooltiprenderer,hidden: <?php echo $system_preference_items['upazilla_name']?0:1;?>},
                { text: '<?php echo $CI->lang->line('LABEL_UNION_NAME'); ?>', dataField: 'union_name',width:'100',rendered:tooltiprenderer,hidden: <?php echo $system_preference_items['union_name']?0:1;?>},
                { columngroup: 'cultivated_area',text: '<?php echo $CI->lang->line('SURVEY_FARMER_CULTIVATED_AREA_VEGETABLES'); ?>', dataField: 'cultivated_area_vegetables', filtertype: 'number',width:'100',rendered:tooltiprenderer,hidden: <?php echo $system_preference_items['cultivated_area']?0:1;?>},
                { columngroup: 'cultivated_area',text: '<?php echo $CI->lang->line('SURVEY_FARMER_OTHERS'); ?>', dataField: 'cultivated_area_others', filtertype: 'number',width:'100',rendered:tooltiprenderer,hidden: <?php echo $system_preference_items['cultivated_area']?0:1;?>},
                { text: '<?php echo $CI->lang->line('SURVEY_FARMER_TITLE_HAVE_VEGETABLES_TRAINING'); ?>', dataField: 'have_vegetables_training',filtertype: 'list',width:'100',cellsalign: 'center',rendered:tooltiprenderer,hidden: <?php echo $system_preference_items['have_vegetables_training']?0:1;?>},
                { columngroup: 'seeds_collect',text: '<?php echo $CI->lang->line('SURVEY_FARMER_SEEDS_COLLECT_DEALERS'); ?>', dataField: 'seeds_collect_dealers',filtertype: 'list',width:'100',cellsalign: 'center',rendered:tooltiprenderer,hidden: <?php echo $system_preference_items['seeds_collect_dealers']?0:1;?>},
                { columngroup: 'seeds_collect',text: '<?php echo $CI->lang->line('SURVEY_FARMER_SEEDS_COLLECT_RETAILERS'); ?>', dataField: 'seeds_collect_retailers',filtertype: 'list',width:'100',cellsalign: 'center',rendered:tooltiprenderer,hidden: <?php echo $system_preference_items['seeds_collect_retailers']?0:1;?>},
                { columngroup: 'seeds_collect',text: '<?php echo $CI->lang->line('SURVEY_FARMER_SEEDS_COLLECT_LEADFARMERS'); ?>', dataField: 'seeds_collect_leadfarmers',filtertype: 'list',width:'100',cellsalign: 'center',rendered:tooltiprenderer,hidden: <?php echo $system_preference_items['seeds_collect_leadfarmers']?0:1;?>},
                { columngroup: 'seeds_collect',text: '<?php echo $CI->lang->line('SURVEY_FARMER_SEEDS_COLLECT_HATBAZAR'); ?>', dataField: 'seeds_collect_hatbazar',filtertype: 'list',width:'100',cellsalign: 'center',rendered:tooltiprenderer,hidden: <?php echo $system_preference_items['seeds_collect_hatbazar']?0:1;?>},
                { columngroup: 'seeds_collect',text: '<?php echo $CI->lang->line('SURVEY_FARMER_SEEDS_COLLECT_OWNSEEDS'); ?>', dataField: 'seeds_collect_ownseeds',filtertype: 'list',width:'100',rendered:tooltiprenderer,hidden: <?php echo $system_preference_items['seeds_collect_ownseeds']?0:1;?>},
                { columngroup: 'seeds_collect',text: '<?php echo $CI->lang->line('SURVEY_FARMER_SELL_VEGETABLES_TO_ARTODAR_PAIKAR'); ?>', dataField: 'seeds_collect_others',filtertype: 'list',width:'100',cellsalign: 'center',rendered:tooltiprenderer,hidden: <?php echo $system_preference_items['seeds_collect_others']?0:1;?>},

                { columngroup: 'sell_vegetables',text: '<?php echo $CI->lang->line('SURVEY_FARMER_SELL_VEGETABLES_TO_ARTODAR_PAIKAR'); ?>', dataField: 'sell_vegetables_to_artodar_paikar',filtertype: 'list',width:'100',cellsalign: 'center',rendered:tooltiprenderer,hidden: <?php echo $system_preference_items['sell_vegetables_to_artodar_paikar']?0:1;?>},
                { columngroup: 'sell_vegetables',text: '<?php echo $CI->lang->line('SURVEY_FARMER_SELL_VEGETABLES_TO_HATBAZAR'); ?>', dataField: 'sell_vegetables_to_hatbazar',filtertype: 'list',width:'100',cellsalign: 'center',rendered:tooltiprenderer,hidden: <?php echo $system_preference_items['sell_vegetables_to_hatbazar']?0:1;?>},
                { columngroup: 'sell_vegetables',text: '<?php echo $CI->lang->line('SURVEY_FARMER_SELL_VEGETABLES_IN_GROUP'); ?>', dataField: 'sell_vegetables_in_group',filtertype: 'list',width:'100',cellsalign: 'center',rendered:tooltiprenderer,hidden: <?php echo $system_preference_items['sell_vegetables_in_group']?0:1;?>},
                { columngroup: 'sell_vegetables',text: '<?php echo $CI->lang->line('SURVEY_FARMER_OTHERS'); ?>', dataField: 'sell_vegetables_others',filtertype: 'list',width:'100',cellsalign: 'center',rendered:tooltiprenderer,hidden: <?php echo $system_preference_items['sell_vegetables_others']?0:1;?>}
            ],
            columngroups:
                [
                    { text: '<?php echo $CI->lang->line('SURVEY_FARMER_TITLE_CULTIVATED_AREA'); ?>', align: 'center', name: 'cultivated_area' },
                    { text: '<?php echo $CI->lang->line('SURVEY_FARMER_TITLE_SEEDS_COLLECT'); ?>', align: 'center', name: 'seeds_collect' },
                    { text: '<?php echo $CI->lang->line('SURVEY_FARMER_TITLE_SELL_VEGETABLES_TO'); ?>', align: 'center', name: 'sell_vegetables' }
                ]
        });
        //$('#clearfilteringbutton').jqxButton({ height: 25});
        $('#clearfilteringbutton').click(function () {
            $("#system_jqx_container").jqxGrid('clearfilters');
        });
    });
</script>