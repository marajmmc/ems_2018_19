<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();
$action_data=array();
if(isset($CI->permissions['print'])&&($CI->permissions['print']==1))
{
    $action_data["action_print"]='print';
}
if(isset($CI->permissions['download'])&&($CI->permissions['download']==1))
{
    $action_data["action_csv"]='csv';
}
if(sizeof($action_data)>0)
{
    $CI->load->view("action_buttons",$action_data);
}

?>

<div class="row widget">
    <div class="widget-header">
        <div class="title">
            <?php echo $title; ?>
        </div>
        <div class="clearfix"></div>
    </div>
    <?php
    if(isset($CI->permissions['column_headers'])&&($CI->permissions['column_headers']==1))
    {

        ?>
        <div class="col-xs-12" style="margin-bottom: 20px;">
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="area">Outlet</label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"   value="sale_total">Total Sale</label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"   value="discount_total">Total Discount</label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"   value="payable_total">Total Payable</label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"   value="sale_canceled">Canceled Sale</label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"   value="discount_canceled">Canceled Discount</label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"   value="payable_canceled">Canceled Payable</label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="sale_actual">Actual Sale</label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="discount_actual">Actual Discount</label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="payable_actual">Actual Payable</label>

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
        //var grand_total_color='#AEC2DD';
        var grand_total_color='#AEC2DD';

        var url = "<?php echo base_url($CI->controller_url.'/index/get_items_outlets_sales');?>";

        // prepare the data
        var source =
        {
            dataType: "json",
            dataFields: [
                { name: 'id', type: 'int' },
                { name: 'outlet_name', type: 'string' },
                { name: 'sale_total', type: 'string' },
                { name: 'payable_total', type: 'string' },
                { name: 'discount_total', type: 'string' },
                { name: 'sale_canceled', type: 'string' },
                { name: 'payable_canceled', type: 'string' },
                { name: 'discount_canceled', type: 'string' },
                { name: 'sale_actual', type: 'string' },
                { name: 'payable_actual', type: 'string' },
                { name: 'discount_actual', type: 'string' }
            ],
            url: url,
            type: 'POST',
            data:JSON.parse('<?php echo json_encode($options);?>')
        };
        var cellsrenderer = function(row, column, value, defaultHtml, columnSettings, record)
        {
            var element = $(defaultHtml);
           // console.log(defaultHtml);
            if (record.outlet_name=="Grand Total")
            {

                element.css({ 'background-color': grand_total_color,'margin': '0px','width': '100%', 'height': '100%',padding:'5px','line-height':'25px'});

            }
            else
            {
                element.css({'margin': '0px','width': '100%', 'height': '100%',padding:'5px','line-height':'25px'});
            }
            return element[0].outerHTML;

        };
        var tooltiprenderer = function (element) {
            $(element).jqxTooltip({position: 'mouse', content: $(element).text() });
        };
        var aggregates=function (total, column, element, record)
        {
            if(record.outlet_name=="Grand Total")
            {
                //console.log(element);
                return record[element];

            }
            return total;
            //return grand_starting_stock;
        };
        var aggregatesrenderer=function (aggregates)
        {
            return '<div style="position: relative; margin: 0px;padding: 5px;width: 100%;height: 100%; overflow: hidden;background-color:'+grand_total_color+';">' +aggregates['total']+'</div>';

        };

        var dataAdapter = new $.jqx.dataAdapter(source);
        // create jqxgrid.
        $("#system_jqx_container").jqxGrid(
            {
                width: '100%',
                height:'350px',
                source: dataAdapter,
                columnsresize: true,
                columnsreorder: true,
                altrows: true,
                enabletooltips: true,
                showaggregates: true,
                showstatusbar: true,
                rowsheight: 40,
                columns: [
                    { text: 'Outlet', dataField: 'outlet_name',width:'200',cellsrenderer: cellsrenderer},
                    { text: 'Total Sale', dataField: 'sale_total',hidden:true,width:'120',cellsAlign:'right',cellsrenderer: cellsrenderer,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                    { text: 'Total Discount', dataField: 'discount_total',hidden:true,width:'120',cellsAlign:'right',cellsrenderer: cellsrenderer,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                    { text: 'Total Payable', dataField: 'payable_total',hidden:true,width:'120',cellsAlign:'right',cellsrenderer: cellsrenderer,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                    { text: 'Canceled Sale', dataField: 'sale_canceled',hidden:true,width:'120',cellsAlign:'right',cellsrenderer: cellsrenderer,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                    { text: 'Canceled Discount', dataField: 'discount_canceled',hidden:true,width:'120',cellsAlign:'right',cellsrenderer: cellsrenderer,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                    { text: 'Canceled Payable', dataField: 'payable_canceled',hidden:true,width:'120',cellsAlign:'right',cellsrenderer: cellsrenderer,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                    { text: 'Actual Sale', dataField: 'sale_actual',width:'120',cellsAlign:'right',cellsrenderer: cellsrenderer,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                    { text: 'Actual Discount', dataField: 'discount_actual',width:'120',cellsAlign:'right',cellsrenderer: cellsrenderer,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                    { text: 'Actual Payable', dataField: 'payable_actual',width:'120',cellsAlign:'right',cellsrenderer: cellsrenderer,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer}


                ]
            });
    });
</script>