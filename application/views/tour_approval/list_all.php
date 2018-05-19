<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();

$action_buttons=array();
$action_buttons[]=array(
    'label'=>'Pending List',
    'href'=>site_url($CI->controller_url.'/index/list')
);
/* if(isset($CI->permissions['action2'])&&($CI->permissions['action2']==1))
{
    $action_buttons[]=array
    (
        'type'=>'button',
        'label'=>'Approve',
        'class'=>'button_jqx_action',
        'data-action-link'=>site_url($CI->controller_url.'/index/approve')
    );
} */
if (isset($CI->permissions['action0']) && ($CI->permissions['action0'] == 1))
{
    $action_buttons[] = array(
        'type' => 'button',
        'label' => $CI->lang->line("ACTION_DETAILS"),
        'class' => 'button_jqx_action',
        'data-action-link' => site_url($CI->controller_url . '/index/details')
    );
}
/* if(isset($CI->permissions['action4']) && ($CI->permissions['action4']==1))
{
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>'Print View',
        'class'=>'button_jqx_action',
        'data-action-link'=>site_url($CI->controller_url.'/index/details_print')
    );
} */
if(isset($CI->permissions['action4'])&&($CI->permissions['action4']==1))
{
    $action_buttons[] = array(
        'type' => 'button',
        'label' => $CI->lang->line("ACTION_PRINT"),
        'class' => 'button_action_download',
        'data-title' => "Print",
        'data-print' => true
    );
}
if(isset($CI->permissions['action5'])&&($CI->permissions['action5']==1))
{
    $action_buttons[] = array(
        'type' => 'button',
        'label' => $CI->lang->line("ACTION_DOWNLOAD"),
        'class' => 'button_action_download',
        'data-title' => "Download"
    );
}
$action_buttons[]=array(
    'label'=>$CI->lang->line("ACTION_REFRESH"),
    'href'=>site_url($CI->controller_url.'/index/list_all')

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
        $CI->load->view('preference', array('system_preference_items' => $system_preference_items));
    }
    ?>
    <div class="col-xs-12" id="system_jqx_container">

    </div>
</div>
<div class="clearfix"></div>
<script type="text/javascript">
    $(document).ready(function ()
    {
        var url = "<?php echo base_url($CI->controller_url.'/index/get_items_all');?>";

        // prepare the data
        var source =
        {
            dataType: "json",
            dataFields: [
                { name: 'id', type: 'int' },
                /* { name: 'name', type: 'string' },
                { name: 'employee_id', type: 'string' },
                { name: 'department_name', type: 'string' },
                { name: 'designation', type: 'string' },
                { name: 'title', type: 'string' },
                { name: 'date_from', type: 'string' },
                { name: 'date_to', type: 'string' },
                { name: 'remarks', type: 'string' },
                { name: 'status_approve', type: 'string' } */
                <?php foreach($system_preference_items as $key => $value){ ?>
                    { name: '<?php echo $key; ?>', type: 'string' },
                <?php } ?>
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
                    { text: 'Name',pinned:true, dataField: 'name',width:'180',rendered:tooltiprenderer, hidden: <?php echo $system_preference_items['name']?0:1;?>},
                    { text: 'Employee ID',pinned:true, dataField: 'employee_id',filtertype: 'list',width:'80',rendered:tooltiprenderer, hidden: <?php echo $system_preference_items['employee_id']?0:1;?>},
                    { text: 'Department',pinned:true, dataField: 'department_name',filtertype: 'list',width:'80',rendered:tooltiprenderer, hidden: <?php echo $system_preference_items['department_name']?0:1;?>},
                    { text: 'Designation',pinned:true, dataField: 'designation',filtertype: 'list',width:'100',rendered:tooltiprenderer, hidden: <?php echo $system_preference_items['designation']?0:1;?>},
                    { text: 'Title',dataField: 'title',rendered:tooltiprenderer, hidden: <?php echo $system_preference_items['title']?0:1;?>},
                    { text: 'Date From', dataField: 'date_from',width:'100',rendered:tooltiprenderer, hidden: <?php echo $system_preference_items['date_from']?0:1;?>},
                    { text: 'Date To', dataField: 'date_to',width:'100',rendered:tooltiprenderer, hidden: <?php echo $system_preference_items['date_to']?0:1;?>},
                    { text: 'Remarks', dataField: 'remarks',width:'160',rendered:tooltiprenderer, hidden: <?php echo $system_preference_items['remarks']?0:1;?>},
                    { text: 'Approve Status', dataField: 'status_approve',filtertype: 'list',width:'160',rendered:tooltiprenderer, hidden: <?php echo $system_preference_items['status_approve']?0:1;?>}

                ]
            });
    });
</script>