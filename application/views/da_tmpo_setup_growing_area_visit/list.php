<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();
if (isset($CI->permissions['action0']) && ($CI->permissions['action0'] == 1))
{
    $action_buttons[] = array(
        'label' => 'Previous Visit',
        'href' => site_url($CI->controller_url . '/index/list_previous')
    );
}
if((isset($CI->permissions['action1']) && ($CI->permissions['action1']==1)) || (isset($CI->permissions['action2']) && ($CI->permissions['action2']==1)))
{
    $action_buttons[]=array
    (
        'type'=>'button',
        'label'=>'Add/Edit Visit',
        'class'=>'button_jqx_action',
        'data-action-link'=>site_url($CI->controller_url.'/index/add_edit/'.$options['date_visit'])
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
    'href'=>site_url($CI->controller_url.'/index/list/'.$options['date_visit'])
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
    if(isset($CI->permissions['action7']) && ($CI->permissions['action7']==1))
    {
        ?>
        <div class="row show-grid">
            <div class="col-xs-12">
                <div class="col-xs-1">
                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE');?> </label>
                </div>
                <div class="col-sm-2 col-xs-2">
                    <input type="text" name="report[date_visit]" id="date_visit" class="form-control datepicker" value="<?php echo System_helper::display_date($options['date_visit']);?>" readonly />
                </div>
            </div>
        </div>
        <hr/>
    <?php
    }
    ?>
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
        $(".datepicker").datepicker({dateFormat : display_date_format});
        //$(".datepicker").datepicker({dateFormat : "ddmmyy"});

        $(".datepicker").on('change', function(){
            var date_selected=$('#date_visit').val();
            $.ajax({
                url: '<?php echo site_url($CI->controller_url.'/index/list/');?>',
                type: 'POST',
                dataType: "JSON",
                data:{date_selected:date_selected},
                success: function (data, status)
                {

                },
                error: function (xhr, desc, err)
                {
                    console.log("error");

                }
            });
        });


        var url = "<?php echo site_url($CI->controller_url.'/index/get_items');?>";

        var source =
        {
            dataType: "json",
            dataFields: [
                { name: 'id', type: 'int' },
                <?php
                foreach($system_preference_items as $key => $value){ ?>
                { name: '<?php echo $key; ?>', type: 'string' },
                <?php } ?>
            ],
            id: 'id',
            type: 'POST',
            url: url,
            data:JSON.parse('<?php echo json_encode($options);?>')
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
                pagesizeoptions: ['20', '50', '100', '200','300','500'],
                selectionmode: 'singlerow',
                altrows: true,
                height: '350px',
                enablebrowserselection:true,
                columnsreorder: true,
                columns:[
                    { text: '<?php echo $CI->lang->line('LABEL_ID'); ?>',pinned:true,dataField: 'id',width:'50',rendered:tooltiprenderer,hidden: <?php echo $system_preference_items['id']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_OUTLET'); ?>',pinned:true,dataField: 'outlet',width:'250',filtertype: 'list',rendered:tooltiprenderer,hidden: <?php echo $system_preference_items['outlet']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_AREA_NAME'); ?>',pinned:true,dataField: 'area_name',width:'200',rendered:tooltiprenderer,hidden: <?php echo $system_preference_items['area_name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_AREA_ADDRESS'); ?>',pinned:true,dataField: 'area_address',width:'200',rendered:tooltiprenderer,hidden: <?php echo $system_preference_items['area_address']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_DIVISION_NAME'); ?>',dataField: 'division_name',width:'100',filtertype: 'list',rendered:tooltiprenderer,hidden: <?php echo $system_preference_items['division_name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_ZONE_NAME'); ?>',dataField: 'zone_name',width:'100',rendered:tooltiprenderer,hidden: <?php echo $system_preference_items['zone_name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_TERRITORY_NAME'); ?>',dataField: 'territory_name',width:'100',rendered:tooltiprenderer,hidden: <?php echo $system_preference_items['territory_name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_DISTRICT_NAME'); ?>',dataField: 'district_name',width:'150',rendered:tooltiprenderer,hidden: <?php echo $system_preference_items['district_name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_STATUS_VISIT_AREA'); ?>',dataField: 'status_visit_area',width:'100',filtertype: 'list',cellsalign: 'right',rendered:tooltiprenderer,hidden: <?php echo $system_preference_items['status_visit_area']?0:1;?>}
                ]
            });


    });
</script>
