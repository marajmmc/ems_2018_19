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

    <div style="" class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right">From Date :</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <?php echo $employee_info['date_start']?>
        </div>
    </div>

    <div style="" class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right">To Date :</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <?php echo $employee_info['date_end']?>
        </div>
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
        var url = "<?php echo base_url($CI->controller_url.'/index/get_items');?>";
        // prepare the data
        var source =
        {
            dataType: "json",
            dataFields: [
                { name: 'id', type: 'string' },
                { name: 'sl_no', type: 'string' },
                { name: 'division_name', type: 'string' },
                { name: 'zone_name', type: 'string' },
                { name: 'territory_name', type: 'string' },
                { name: 'date_setup', type: 'string' },
                { name: 'date_approve', type: 'string' },
                { name: 'employee', type: 'string' },
                { name: 'department_name', type: 'string' },
                { name: 'designation_name', type: 'string' },
                { name: 'title', type: 'string' },
                { name: 'iou_amount', type: 'string' },
                { name: 'status', type: 'string' },
                { name: 'no_of_purpose', type: 'string' },
                { name: 'complete_reporting', type: 'string' },
                { name: 'incomplete_reporting', type: 'string' },
                { name: 'details_button', type: 'string' }
            ],
            url: url,
            type: 'POST',
            data:JSON.parse('<?php echo json_encode($options);?>')
        };

        var cellsrenderer = function(row, column, value, defaultHtml, columnSettings, record)
        {
            var element = $(defaultHtml);
            element.css({'margin': '0px','width': '100%', 'height': '100%',padding:'5px'});
            if(column=='details_button')
            {
                element.html('');
                if(record.id!=undefined && record.id!='')
                {
                    console.log(record.id);
                    element.html('<div><button class="btn btn-primary pop_up" data-item-no="'+row+'">Details</button></div>');
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
                filterable: true,
                sortable: true,
                showfilterrow: true,
                columnsresize: true,
                columnsreorder: true,
                altrows: true,
                enabletooltips: true,
                showstatusbar: true,
                rowsheight: 40,
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
                    { text: '<?php echo $CI->lang->line('LABEL_DIVISION_NAME'); ?>',pinned:true,dataField: 'division_name',width:'80',filtertype: 'list',rendered:tooltiprenderer,hidden: <?php echo $system_preference_items['division_name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_ZONE_NAME'); ?>',pinned:true,dataField: 'zone_name',width:'90',rendered:tooltiprenderer,hidden: <?php echo $system_preference_items['zone_name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_TERRITORY_NAME'); ?>',pinned:true,dataField: 'territory_name',width:'130',rendered:tooltiprenderer,hidden: <?php echo $system_preference_items['territory_name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_DATE_SETUP'); ?>',dataField: 'date_setup',width:'130',rendered:tooltiprenderer,hidden: <?php echo $system_preference_items['date_setup']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_DATE_APPROVE'); ?>',dataField: 'date_approve',width:'130',rendered:tooltiprenderer,hidden: <?php echo $system_preference_items['date_approve']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_EMPLOYEE'); ?>',dataField: 'employee',width:'230',filtertype: 'list',rendered:tooltiprenderer,hidden: <?php echo $system_preference_items['employee']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_DEPARTMENT_NAME'); ?>',dataField: 'department_name',width:'230',filtertype: 'list',rendered:tooltiprenderer,hidden: <?php echo $system_preference_items['department_name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_DESIGNATION_NAME'); ?>',dataField: 'designation_name',width:'230',filtertype: 'list',rendered:tooltiprenderer,hidden: <?php echo $system_preference_items['designation_name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_TITLE'); ?>',dataField: 'title',width:'230',filtertype: 'list',rendered:tooltiprenderer,hidden: <?php echo $system_preference_items['title']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_IOU_AMOUNT'); ?>',dataField: 'iou_amount',width:'130',rendered:tooltiprenderer,hidden: <?php echo $system_preference_items['iou_amount']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_STATUS'); ?>',dataField: 'status',width:'130',filtertype: 'list',rendered:tooltiprenderer,hidden: <?php echo $system_preference_items['status']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_NO_OF_PURPOSE'); ?>',dataField: 'no_of_purpose',width:'130',rendered:tooltiprenderer,hidden: <?php echo $system_preference_items['no_of_purpose']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_COMPLETE_REPORTING'); ?>',dataField: 'complete_reporting',width:'130',rendered:tooltiprenderer,hidden: <?php echo $system_preference_items['complete_reporting']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_INCOMPLETE_REPORTING'); ?>',dataField: 'incomplete_reporting',width:'130',rendered:tooltiprenderer,hidden: <?php echo $system_preference_items['incomplete_reporting']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_DETAILS_BUTTON'); ?>', dataField: 'details_button',width: '85',cellsrenderer: cellsrenderer,rendered: tooltiprenderer},

                ]
            });
    });
</script>