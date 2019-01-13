<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();
$action_buttons=array();
if(isset($CI->permissions['action4'])&&($CI->permissions['action4']==1))
{
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_PRINT"),
        'class'=>'button_action_download',
        'data-title'=>"Print",
        'data-print'=>true
    );
}
if(isset($CI->permissions['action5'])&&($CI->permissions['action5']==1))
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
            url: url,
            type: 'POST',
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
                height:'350px',
                source: dataAdapter,
                sortable: true,
                filterable: true,
                showfilterrow: true,
                columnsresize: true,
                columnsreorder: true,
                altrows: true,
                enabletooltips: true,
                enablebrowserselection: true,
                rowsheight: 45,
                columns:
                    [
                        { text: '<?php echo $CI->lang->line('LABEL_GROWING_AREA_NAME'); ?>', dataField: 'growing_area_name',filtertype: 'list',pinned:true,width:'200',hidden: <?php echo $system_preference_items['growing_area_name']?0:1;?>},
                        { columngroup:'dealer_info',text: '<?php echo $CI->lang->line('LABEL_DEALER_NAME'); ?>', dataField: 'dealer_name',width:'200',hidden: <?php echo $system_preference_items['dealer_name']?0:1;?>},
                        { columngroup:'dealer_info',text: '<?php echo $CI->lang->line('LABEL_DEALER_MOBILE_NO'); ?>', dataField: 'dealer_mobile_no',width:'100',hidden: <?php echo $system_preference_items['dealer_mobile_no']?0:1;?>},
                        { columngroup:'lead_farmer_info',text: '<?php echo $CI->lang->line('LABEL_LEAD_FARMER_NAME'); ?>', dataField: 'lead_farmer_name',width:'200',hidden: <?php echo $system_preference_items['lead_farmer_name']?0:1;?>},
                        { columngroup:'lead_farmer_info',text: '<?php echo $CI->lang->line('LABEL_LEAD_FARMER_MOBILE_NO'); ?>', dataField: 'lead_farmer_mobile_no',width:'100',hidden: <?php echo $system_preference_items['lead_farmer_mobile_no']?0:1;?>},
                        { columngroup:'lead_farmer_info',text: '<?php echo $CI->lang->line('LABEL_LEAD_FARMER_CREATED_DATE'); ?>', dataField: 'lead_farmer_created_date',width:'100',hidden: <?php echo $system_preference_items['lead_farmer_created_date']?0:1;?>}
                    ],
                columngroups:
                    [
                        { text: 'Dealer', align: 'center', name: 'dealer_info' },
                        { text: 'Lead Farmer', align: 'center', name: 'lead_farmer_info' }
                    ]
            });
    });
</script>