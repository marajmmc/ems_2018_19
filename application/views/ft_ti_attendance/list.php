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
if(isset($CI->permissions['action2']) && ($CI->permissions['action2']==1))
{
    $action_buttons[]=array
    (
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_ATTENDANCE"),
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
if(isset($CI->permissions['action6']) && ($CI->permissions['action6']==1))
{
    $action_buttons[]=array
    (
        'label'=>'Preference',
        'href'=>site_url($CI->controller_url.'/index/set_preference')
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
    if(isset($CI->permissions['action6']) && ($CI->permissions['action6']==1))
    {
        $CI->load->view('preference',array('system_preference_items'=>$system_preference_items));
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
                { name: 'employee_id', type: 'string' },
                { name: 'name', type: 'string' },
                { name: 'id', type: 'int' },
                { name: 'date', type: 'string' },
                { name: 'customer_name', type: 'string' },
                { name: 'farmer_name', type: 'string' },
                { name: 'dealer_visit_activities', type: 'string' },
                { name: 'lead_farmer_visit_activities_one', type: 'string' },
                { name: 'lead_farmer_visit_activities_two', type: 'string' },
                { name: 'lead_farmer_visit_activities_three', type: 'string' },
                { name: 'farmer_visit_activities', type: 'string' },
                { name: 'other_activities', type: 'string' },
                { name: 'status_attendance', type: 'string' }

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
                    { text: '<?php echo $CI->lang->line('LABEL_EMPLOYEE_ID'); ?>',pinned:true,dataField: 'employee_id',width:'80',rendered:tooltiprenderer,hidden: <?php echo $system_preference_items['employee_id']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_NAME'); ?>',pinned:true,dataField: 'name',width:'160',rendered:tooltiprenderer,hidden: <?php echo $system_preference_items['name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_DATE'); ?>',pinned:true, dataField: 'date',width:'100',rendered:tooltiprenderer,hidden: <?php echo $system_preference_items['date']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_OUTLET'); ?>', dataField: 'customer_name',filtertype: 'list',width:'180',rendered:tooltiprenderer,hidden: <?php echo $system_preference_items['customer_name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_DEALER'); ?>', dataField: 'farmer_name',width:'160',rendered:tooltiprenderer,hidden: <?php echo $system_preference_items['farmer_name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_DEALER_VISIT_ACTIVITIES'); ?>', dataField: 'dealer_visit_activities',rendered:tooltiprenderer,hidden: <?php echo $system_preference_items['dealer_visit_activities']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_LEAD_FARMER_VISIT_ACTIVITIES_ONE'); ?>', dataField: 'lead_farmer_visit_activities_one',rendered:tooltiprenderer,hidden: <?php echo $system_preference_items['lead_farmer_visit_activities_one']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_LEAD_FARMER_VISIT_ACTIVITIES_TWO'); ?>', dataField: 'lead_farmer_visit_activities_two',rendered:tooltiprenderer,hidden: <?php echo $system_preference_items['lead_farmer_visit_activities_two']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_LEAD_FARMER_VISIT_ACTIVITIES_THREE'); ?>', dataField: 'lead_farmer_visit_activities_three',rendered:tooltiprenderer,hidden: <?php echo $system_preference_items['lead_farmer_visit_activities_three']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_FARMER_VISIT_ACTIVITIES'); ?>', dataField: 'farmer_visit_activities',rendered:tooltiprenderer,hidden: <?php echo $system_preference_items['farmer_visit_activities']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_OTHER_ACTIVITIES'); ?>', dataField: 'other_activities',rendered:tooltiprenderer,hidden: <?php echo $system_preference_items['other_activities']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_STATUS_ATTENDANCE'); ?>', dataField: 'status_attendance',filtertype: 'list',width:'160',rendered:tooltiprenderer,hidden: <?php echo $system_preference_items['status_attendance']?0:1;?>}
                ]
            });
    });
</script>