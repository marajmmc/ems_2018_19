<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();
$action_buttons=array();
if(isset($CI->permissions['action4'])&&($CI->permissions['action4']==1))
{
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_PRINT"),
        'id'=>'button_action_print',
        'data-title'=>'PO LIST'
    );
}
if(isset($CI->permissions['action5'])&&($CI->permissions['action5']==1))
{
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_DOWNLOAD"),
        'id'=>'button_action_csv',
        'data-title'=>'PO LIST'
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
            <label class="control-label pull-right">Starting Date :</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <?php echo $employee_info['date_start']?>
        </div>
    </div>

    <div style="" class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right">Ending Date :</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <?php echo $employee_info['date_end']?>
        </div>
    </div>

    <?php
    if(isset($CI->permissions['action6'])&&($CI->permissions['action6']==1))
    {
        ?>
        <div class="col-xs-12" style="margin-bottom: 20px;">
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="sl_no">Sl</label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="date">Date</label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="division_name">Division</label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="zone_name">Zone</label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="territory_name">Territory</label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="dealer">Dealer</label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="username">Username</label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="created_time">Task Created Time </label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="status_attendance">Attendance</label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="attendance_taken_time">Attendance Taken Time</label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="details_button"><?php echo $CI->lang->line('ACTION_DETAILS'); ?></label>
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
                { name: 'date', type: 'string' },
                { name: 'division_name', type: 'string' },
                { name: 'zone_name', type: 'string' },
                { name: 'territory_name', type: 'string' },
                { name: 'dealer', type: 'string' },
                { name: 'username', type: 'string' },
                { name: 'created_time', type: 'string' },
                { name: 'status_attendance', type: 'string' },
                { name: 'attendance_taken_time', type: 'string' },
                { name: 'details', type: 'string' }

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
                source: dataAdapter,
                filterable: true,
                sortable: true,
                showfilterrow: true,
                columnsresize: true,
                pagesize:20,
                pagesizeoptions: ['20', '50', '100', '200','300','500'],
                selectionmode: 'singlerow',
                altrows: true,
                rowsheight: 45,
                columns: [
                    { text: 'ID',pinned:true,dataField: 'id',width:'110',cellsrenderer: cellsrenderer,rendered:tooltiprenderer, hidden: true},
                    {
                        text: '<?php echo $CI->lang->line('LABEL_SL_NO'); ?>',datafield: 'sl_no',pinned:true,width:'30', columntype: 'number',cellsalign: 'right', sortable: false, menu: false,
                        cellsrenderer: function(row, column, value, defaultHtml, columnSettings, record)
                        {
                            var element = $(defaultHtml);
                            element.html(value+1);
                            return element[0].outerHTML;
                        }
                    },
                    { text: '<?php echo $CI->lang->line('LABEL_DATE'); ?>',pinned:true,dataField: 'date',width:'110',rendered:tooltiprenderer},
                    { text: 'Division',pinned:true,dataField: 'division_name',width:'80',filtertype: 'list',rendered:tooltiprenderer},
                    { text: 'Zone',pinned:true,dataField: 'zone_name',width:'90',filtertype: 'list',rendered:tooltiprenderer},
                    { text: 'Territory',pinned:true,dataField: 'territory_name',width:'130',filtertype: 'list',rendered:tooltiprenderer},
                    { text: 'Dealer',dataField: 'dealer',width:'130',filtertype: 'list',rendered:tooltiprenderer},
                    { text: 'Username',dataField: 'username',width:'230',filtertype: 'list',rendered:tooltiprenderer},
                    { text: 'Task Created Time',dataField: 'created_time',width:'200',filtertype: 'list',rendered:tooltiprenderer},
                    { text: 'Attendance',dataField: 'status_attendance',width: '65',filtertype: 'list',rendered:tooltiprenderer},
                    { text: 'Attendance Taken Time',dataField: 'attendance_taken_time',width:'200',filtertype: 'list',rendered:tooltiprenderer},
                    { text: 'Details', dataField: 'details_button',width: '85',cellsrenderer: cellsrenderer,rendered: tooltiprenderer}

                ]
            });
    });
</script>