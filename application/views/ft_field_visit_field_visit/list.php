<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();

$action_buttons=array();
if((isset($CI->permissions['action1'])&&($CI->permissions['action1']==1))||(isset($CI->permissions['action2'])&&($CI->permissions['action2']==1)))
{
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>$CI->lang->line('ACTION_REPORTING'),
        'class'=>'button_jqx_action',
        'data-action-link'=>site_url($CI->controller_url.'/index/edit')
    );
}
if(isset($CI->permissions['action0'])&&($CI->permissions['action0']==1))
{
    $action_buttons[]=array(
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
                { name: 'id', type: 'int' },
                <?php
                foreach($system_preference_items as $key => $value){ ?>
                { name: '<?php echo $key; ?>', type: 'string' },
                <?php } ?>
            ],
            id: 'id',
            type: 'POST',
            url: url
        };

        var cellsrenderer = function(row, column, value, defaultHtml, columnSettings, record)
        {
            var element = $(defaultHtml);
            // console.log(defaultHtml);
            element.css({'margin': '0px','width': '100%', 'height': '100%',padding:'5px','line-height':'25px'});
            if(record['color_background'])
            {
                element.css({ 'background-color': record['color_background']});
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
                rowsheight: 30,
                columns: [
                    { text: '<?php echo $CI->lang->line('LABEL_ID'); ?>',pinned:true,dataField: 'id',width:'40',cellsalign: 'right',rendered:tooltiprenderer,hidden: <?php echo $system_preference_items['id']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_FARMER_NAME'); ?>',pinned:true,dataField: 'farmer_name',width:'200',rendered:tooltiprenderer,hidden: <?php echo $system_preference_items['farmer_name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_YEAR'); ?>',pinned:true,dataField: 'year',width:'80',filtertype: 'list',rendered:tooltiprenderer,hidden: <?php echo $system_preference_items['year']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_SEASON'); ?>',pinned:true,dataField: 'season',filtertype: 'list',width:'100',cellsrenderer: cellsrenderer,rendered:tooltiprenderer,hidden: <?php echo $system_preference_items['season']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_UPAZILLA_NAME'); ?>', dataField: 'upazilla_name',width:'100',cellsrenderer: cellsrenderer,rendered:tooltiprenderer,hidden: <?php echo $system_preference_items['upazilla_name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_DISTRICT_NAME'); ?>', dataField: 'district_name',width:'100',cellsrenderer: cellsrenderer,rendered:tooltiprenderer,hidden: <?php echo $system_preference_items['district_name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_TERRITORY_NAME'); ?>', dataField: 'territory_name',width:'100',cellsrenderer: cellsrenderer,rendered:tooltiprenderer,hidden: <?php echo $system_preference_items['territory_name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_ZONE_NAME'); ?>',dataField: 'zone_name',width:'100',cellsrenderer: cellsrenderer,rendered:tooltiprenderer,hidden: <?php echo $system_preference_items['zone_name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_DIVISION_NAME'); ?>',dataField: 'division_name',filtertype: 'list',width:'100',cellsrenderer: cellsrenderer,rendered:tooltiprenderer,hidden: <?php echo $system_preference_items['division_name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_CONTACT_NO'); ?>', dataField: 'contact_no',width:'100',cellsrenderer: cellsrenderer,rendered:tooltiprenderer,hidden: <?php echo $system_preference_items['contact_no']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_DATE_SOWING'); ?>', dataField: 'date_sowing',width:'100',cellsrenderer: cellsrenderer,rendered:tooltiprenderer,hidden: <?php echo $system_preference_items['date_sowing']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_NUM_VISITS'); ?>', dataField: 'num_visits',filtertype: 'list',width:'50',cellsalign: 'right',cellsrenderer: cellsrenderer,rendered:tooltiprenderer,hidden: <?php echo $system_preference_items['num_visits']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_INTERVAL'); ?>', dataField: 'interval',filtertype: 'list',width:'50',cellsalign: 'right',cellsrenderer: cellsrenderer,rendered:tooltiprenderer,hidden: <?php echo $system_preference_items['interval']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_NUM_VISIT_DONE'); ?>', dataField: 'num_visit_done',filtertype: 'list',width:'50',cellsalign: 'right',cellsrenderer: cellsrenderer,rendered:tooltiprenderer,hidden: <?php echo $system_preference_items['num_visit_done']?0:1;?>}
                ]
            });
    });
</script>