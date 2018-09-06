<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();
if (isset($CI->permissions['action0']) && ($CI->permissions['action0'] == 1))
{
    $action_buttons[] = array(
        'label' => 'All List',
        'href' => site_url($CI->controller_url . '/index/list_all')
    );
}
if((isset($CI->permissions['action1']) && ($CI->permissions['action1']==1)) || (isset($CI->permissions['action2']) && ($CI->permissions['action2']==1)))
{
    $action_buttons[]=array
    (
        'type'=>'button',
        'label'=>'Attendance',
        'class'=>'button_jqx_action',
        'data-action-link'=>site_url($CI->controller_url.'/index/attendance/')
    );
}
if(isset($CI->permissions['action0']) && ($CI->permissions['action0']==1))
{
    $action_buttons[]=array
    (
        'type'=>'button',
        'label'=>$CI->lang->line('ACTION_DETAILS'),
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
    'href'=>site_url($CI->controller_url)
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
    <div class="col-xs-12" id="system_jqx_container" style="z-index: 0">

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
                <?php
                 foreach($system_preference_items as $key=>$item)
                 {
                 ?>
                { name: '<?php echo $key ?>', type: 'string' },
                <?php
             }
            ?>
            ],
            id: 'id',
            type: 'POST',
            url: url
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
                columnsreorder: true,
                enablebrowserselection: true,
                columns:
                    [
                        { text: '<?php echo $CI->lang->line('LABEL_ID'); ?>',dataField: 'id',width:'50',hidden: <?php echo $system_preference_items['id']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_OUTLET'); ?>',dataField: 'outlet',width:'250',filtertype: 'list',hidden: <?php echo $system_preference_items['outlet']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_DATE_VISIT'); ?>',dataField: 'date_visit',width:'100',cellsalign: 'right',hidden: <?php echo $system_preference_items['date_visit']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_AREA_NAME'); ?>',dataField: 'area_name',width:'200',hidden: <?php echo $system_preference_items['area_name']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_AREA_ADDRESS'); ?>',dataField: 'area_address',width:'200',hidden: <?php echo $system_preference_items['area_address']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_DIVISION_NAME'); ?>',dataField: 'division_name',width:'100',filtertype: 'list',hidden: <?php echo $system_preference_items['division_name']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_ZONE_NAME'); ?>',dataField: 'zone_name',width:'100',hidden: <?php echo $system_preference_items['zone_name']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_TERRITORY_NAME'); ?>',dataField: 'territory_name',width:'100',hidden: <?php echo $system_preference_items['territory_name']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_DISTRICT_NAME'); ?>',dataField: 'district_name',width:'150',hidden: <?php echo $system_preference_items['district_name']?0:1;?>}
                    ]
            });
    });
</script>
