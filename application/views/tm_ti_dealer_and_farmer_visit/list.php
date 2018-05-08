<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();
$action_buttons=array();
if(isset($CI->permissions['action0']) && ($CI->permissions['action0']==1))
{
    $action_buttons[]=array(
        'label'=>'All List',
        'href'=>site_url($CI->controller_url.'/index/list_all')
    );
}
if(isset($CI->permissions['action1'])&&($CI->permissions['action1']==1))
{
    $action_buttons[]=array(
        'label'=>$CI->lang->line("ACTION_NEW"),
        'href'=>site_url($CI->controller_url.'/index/add')
    );
}

if(isset($CI->permissions['action2']) && ($CI->permissions['action2']==1))
{
    $action_buttons[]=array
    (
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_EDIT"),
        'class'=>'button_jqx_action',
        'data-action-link'=>site_url($CI->controller_url.'/index/edit')
    );
}

if(isset($CI->permissions['action0'])&&($CI->permissions['action0']==1))
{
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_DETAILS"),
        'class'=>'button_jqx_action',
        'data-action-link'=>site_url($CI->controller_url.'/index/details')
    );
}

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

$action_buttons[]=array(
    'label'=>$CI->lang->line("ACTION_REFRESH"),
    'href'=>site_url($CI->controller_url.'/index/list')

);
$action_buttons[]=array(
    'type'=>'button',
    'label'=>$CI->lang->line("ACTION_LOAD_MORE"),
    'id'=>'button_jqx_load_more'
);
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
    if(isset($CI->permissions['action6'])&&($CI->permissions['action6']==1))
    {

        ?>
        <div class="col-xs-12" style="margin-bottom: 20px;">
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="date"><?php echo $CI->lang->line('LABEL_DATE'); ?></label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="outlet">Outlet</label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="farmer_name">Farmer</label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="dealer_visit_activities">Dealer Visit Activities</label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="lead_farmer_visit_activities_one">Lead Farmer Visit Activities (1)</label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="lead_farmer_visit_activities_two">Lead Farmer Visit Activities (2)</label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="lead_farmer_visit_activities_three">Lead Farmer Visit Activities (3)</label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="farmer_visit_activities">Farmer Visit Activities</label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="other_activities">Other Activities</label>
        </div>
    <?php
    }
    ?>
    <div class="col-xs-12" id="system_jqx_container">

    </div>
</div>
<div class="clearfix"></div>
<script type="text/javascript">
    $(document).ready(function ()
    {
        system_preset({controller:'<?php echo $CI->router->class; ?>'});
        var url = "<?php echo site_url($CI->controller_url.'/index/get_items');?>";
        // prepare the data
        var source =
        {
            dataType: "json",
            dataFields: [
                { name: 'id', type: 'int' },
                { name: 'date', type: 'string' },
                { name: 'outlet', type: 'string' },
                { name: 'farmer_name', type: 'string' },
                { name: 'dealer_visit_activities', type: 'string' },
                { name: 'lead_farmer_visit_activities_one', type: 'string' },
                { name: 'lead_farmer_visit_activities_two', type: 'string' },
                { name: 'lead_farmer_visit_activities_three', type: 'string' },
                { name: 'farmer_visit_activities', type: 'string' },
                { name: 'other_activities', type: 'string' }

            ],
            id: 'id',
            type: 'POST',
            url: url
        };
        var tooltiprenderer = function (element) {
            $(element).jqxTooltip({position: 'mouse', content: $(element).text() });
        };
        var dataAdapter = new $.jqx.dataAdapter(source);
        // create jqxgrid.
        $("#system_jqx_container").jqxGrid(
            {
                width: '100%',
                source: dataAdapter,
                pageable: true,
                filterable: true,
                sortable: true,
                showfilterrow: true,
                columnsresize: true,
                pagesize:50,
                pagesizeoptions: ['50', '100', '200','300','500','1000','5000'],
                selectionmode: 'singlerow',
                altrows: true,
                height: '350px',
                enablebrowserselection:true,
                columnsreorder: true,
                columns: [
                    { text: '<?php echo $CI->lang->line('LABEL_DATE'); ?>',pinned:true, dataField: 'date',width:'100',rendered:tooltiprenderer},
                    { text: 'Outlet', dataField: 'outlet',filtertype: 'list',width:'180',rendered:tooltiprenderer},
                    { text: 'Farmer', dataField: 'farmer_name',width:'160',rendered:tooltiprenderer},
                    { text: 'Dealer Visit Activities', dataField: 'dealer_visit_activities',rendered:tooltiprenderer},
                    { text: 'Lead Farmer Visit Activities (1)', dataField: 'lead_farmer_visit_activities_one',rendered:tooltiprenderer},
                    { text: 'Lead Farmer Visit Activities (2)', dataField: 'lead_farmer_visit_activities_two',rendered:tooltiprenderer},
                    { text: 'Lead Farmer Visit Activities (3)', dataField: 'lead_farmer_visit_activities_three',rendered:tooltiprenderer},
                    { text: 'Farmer Visit Activities', dataField: 'farmer_visit_activities',rendered:tooltiprenderer},
                    { text: 'Other Activities', dataField: 'other_activities',rendered:tooltiprenderer}
                ]
            });
    });
</script>