<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();

$action_buttons=array();
if(isset($CI->permissions['action0'] ) && ($CI->permissions['action0'] ==1))
{
    $action_buttons[]=array(
        'label'=>'All List',
        'href'=>site_url($CI->controller_url.'/index/list_all')
    );
}
if(isset($CI->permissions['action1'] )&&($CI->permissions['action1'] ==1))
{
    $action_buttons[]=array(
        'label'=>$CI->lang->line("ACTION_NEW"),
        'href'=>site_url($CI->controller_url.'/index/add')
    );
}
if(isset($CI->permissions['action2'] )&&($CI->permissions['action2'] ==1))
{
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_EDIT"),
        'class'=>'button_jqx_action',
        'data-action-link'=>site_url($CI->controller_url.'/index/edit')
    );
}
/* if(isset($CI->permissions['action2'] )&&($CI->permissions['action2'] ==1))
{
    $action_buttons[]=array
    (
        'type'=>'button',
        'label'=>'Reporting',
        'class'=>'button_jqx_action',
        'data-action-link'=>site_url($CI->controller_url.'/index/list_reporting')
    );
} */
if(isset($CI->permissions['action2'] )&&($CI->permissions['action2'] ==1))
{
    $action_buttons[]=array
    (
        'type'=>'button',
        'label'=>'Forward',
        'class'=>'button_jqx_action',
        'data-action-link'=>site_url($CI->controller_url.'/index/forward')
    );
}
/* if(isset($CI->permissions['action2'] )&&($CI->permissions['action2'] ==1))
{
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_DETAILS"),
        'class'=>'button_action_batch',
        'id'=>'button_action_edit',
        'data-action-link'=>site_url($CI->controller_url.'/index/details')
    );
}
if(isset($CI->permissions['action4'] ) && ($CI->permissions['action4'] ==1))
{
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>'Print View',
        'class'=>'button_jqx_action',
        'data-action-link'=>site_url($CI->controller_url.'/index/details_print')
    );
} */
if(isset($CI->permissions['action4'] )&&($CI->permissions['action4'] ==1))
{
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_PRINT"),
        'class'=>'button_action_download',
        'data-title'=>"Print",
        'data-print'=>true
    );
}
if(isset($CI->permissions['action5'] )&&($CI->permissions['action5'] ==1))
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
    if(isset($CI->permissions['action6'] )&&($CI->permissions['action6'] ==1))
    {
        ?>
        <div class="col-xs-12" style="margin-bottom: 20px;">
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="name">Name</label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="employee_id">Employee ID</label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="department_name">Department</label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="designation">Designation</label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="title">Title</label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="date_from">Date From</label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="date_to">Date To</label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="amount_iou"><?php echo $CI->lang->line('LABEL_IOU_AMOUNT'); ?></label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="iou_details"><?php echo $CI->lang->line('LABEL_IOU_DETAILS'); ?></label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="remarks">Remarks</label>
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
        var url = "<?php echo base_url($CI->controller_url.'/index/get_items'); ?>";

        // prepare the data
        var source =
        {
            dataType: "json",
            dataFields: [
                { name: 'id', type: 'int' },
                { name: 'name', type: 'string' },
                { name: 'employee_id', type: 'string' },
                { name: 'department_name', type: 'string' },
                { name: 'designation', type: 'string' },
                { name: 'title', type: 'string' },
                { name: 'date_from', type: 'string' },
                { name: 'date_to', type: 'string' },
                { name: 'amount_iou', type: 'string' },
                { name: 'iou_details', type: 'string' },
                { name: 'remarks', type: 'string' }
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
                    { text: 'Name',pinned:true, dataField: 'name',width:'180',rendered:tooltiprenderer},
                    { text: 'Employee ID',pinned:true, dataField: 'employee_id',filtertype: 'list',width:'80',rendered:tooltiprenderer},
                    { text: 'Department',pinned:true, dataField: 'department_name',filtertype: 'list',width:'80',rendered:tooltiprenderer},
                    { text: 'Designation',pinned:true, dataField: 'designation',filtertype: 'list',width:'100',rendered:tooltiprenderer},
                    { text: 'Title',dataField: 'title',rendered:tooltiprenderer},
                    { text: 'Date From', dataField: 'date_from',width:'100',rendered:tooltiprenderer},
                    { text: 'Date To', dataField: 'date_to',width:'100',rendered:tooltiprenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_IOU_AMOUNT'); ?>', dataField: 'amount_iou',width:'100',rendered:tooltiprenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_IOU_DETAILS'); ?>', dataField: 'iou_details',width:'100',rendered:tooltiprenderer},
                    { text: 'Remarks', dataField: 'remarks',width:'160',rendered:tooltiprenderer}

                ]
            });
    });
</script>