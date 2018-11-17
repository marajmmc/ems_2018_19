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
    <div class="col-xs-12 well well-sm">
        <button class="btn btn-success" type="button">
            Number of Visit Day's <span class="badge"><?php echo $number_of_days?></span>
        </button>
        <button class="btn btn-warning" type="button">
            Number of Friday's <span class="badge bg-danger"><?php echo $number_of_friday?></span>
        </button>
    </div>
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
        var cellsrenderer = function(row, column, value, defaultHtml, columnSettings, record)
        {
            var element = $(defaultHtml);
            element.css({'margin': '0px','width': '100%', 'height': '100%',padding:'5px'});
            console.log(record);
            if(column=='details_view')
            {
                if(record.visit_id)
                {
                    element.html('<div><button class="btn btn-primary pop_up" data-action-link="<?php echo site_url($CI->controller_url.'/index/details'); ?>/'+record.visit_id+'">View Details</button></div>');
                }
                else
                {
                    element.html('');
                }
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
                        { text: '<?php echo $CI->lang->line('LABEL_EMPLOYEE_NAME'); ?>', dataField: 'employee_name',filtertype: 'list',pinned:true,width:'200',hidden: <?php echo $system_preference_items['employee_name']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_OUTLET_NAME'); ?>', dataField: 'outlet_name',filtertype: 'list',pinned:true,width:'200',hidden: <?php echo $system_preference_items['outlet_name']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_NUMBER_OF_DAY'); ?>', dataField: 'number_of_day',width:'150',hidden: <?php echo $system_preference_items['number_of_day']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_NUMBER_OF_FRIDAY'); ?>', dataField: 'number_of_friday',width:'150',hidden: <?php echo $system_preference_items['number_of_friday']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_NUMBER_OF_AREA_VISIT'); ?>', dataField: 'number_of_area_visit',width:'150',hidden: <?php echo $system_preference_items['number_of_area_visit']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_NUMBER_OF_PRESENT'); ?>', dataField: 'number_of_present',width:'150',hidden: <?php echo $system_preference_items['number_of_present']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_NUMBER_OF_ABSENT'); ?>', dataField: 'number_of_absent',width:'150',hidden: <?php echo $system_preference_items['number_of_absent']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_NUMBER_OF_LEAVE'); ?>', dataField: 'number_of_leave',width:'150',hidden: <?php echo $system_preference_items['number_of_leave']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_NUMBER_OF_ND'); ?>', dataField: 'number_of_nd',width:'150',hidden: <?php echo $system_preference_items['number_of_nd']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_NUMBER_OF_EXTRA_DAYS'); ?>', dataField: 'number_of_extra_days',width:'150',hidden: <?php echo $system_preference_items['number_of_extra_days']?0:1;?>}
                    ]
            });
    });
</script>