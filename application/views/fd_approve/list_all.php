<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();

$action_buttons = array();
if (isset($CI->permissions['action0']) && ($CI->permissions['action0'] == 1))
{
    $action_buttons[] = array(
        'label' => 'Pending List',
        'href' => site_url($CI->controller_url . '/index/list')
    );
    $action_buttons[] = array(
        'type' => 'button',
        'label' => $CI->lang->line("ACTION_DETAILS"),
        'class' => 'button_jqx_action',
        'data-action-link' => site_url($CI->controller_url . '/index/details')
    );
}
if (isset($CI->permissions['action4']) && ($CI->permissions['action4'] == 1))
{
    $action_buttons[] = array(
        'type' => 'button',
        'label' => $CI->lang->line("ACTION_PRINT"),
        'class' => 'button_action_download',
        'data-title' => "Print",
        'data-print' => true
    );
}
if (isset($CI->permissions['action5']) && ($CI->permissions['action5'] == 1))
{
    $action_buttons[] = array(
        'type' => 'button',
        'label' => $CI->lang->line("ACTION_DOWNLOAD"),
        'class' => 'button_action_download',
        'data-title' => "Download"
    );
}
if (isset($CI->permissions['action6']) && ($CI->permissions['action6'] == 1))
{
    $action_buttons[] = array
    (
        'label' => 'Preference',
        'href' => site_url($CI->controller_url . '/index/set_preference_list_all')
    );
}
$action_buttons[] = array(
    'label' => $CI->lang->line("ACTION_REFRESH"),
    'href' => site_url($CI->controller_url . '/index/list_all')

);
$action_buttons[] = array(
    'type' => 'button',
    'label' => $CI->lang->line("ACTION_LOAD_MORE"),
    'id' => 'button_jqx_load_more'
);
$CI->load->view('action_buttons', array('action_buttons' => $action_buttons));
?>
<div class="row widget">
    <div class="widget-header">
        <div class="title">
            <?php echo $title; ?>
        </div>
        <div class="clearfix"></div>
    </div>
    <?php
    if (isset($CI->permissions['action6']) && ($CI->permissions['action6'] == 1))
    {
        $CI->load->view('preference', array('system_preference_items' => $system_preference_items));
    }
    ?>
    <div class="col-xs-12" id="system_jqx_container">

    </div>
</div>
<div class="clearfix"></div>

<script type="text/javascript">
    $(document).ready(function () {
        system_off_events(); // Triggers

        var url = "<?php echo site_url($CI->controller_url.'/index/get_items_all'); ?>";
        // prepare the data
        var source =
        {
            dataType: "json",
            dataFields: [
                <?php
                foreach($system_preference_items as $key => $value)
                {
                    if($key=='id')
                    {
                    ?> { name: '<?php echo $key; ?>', type: 'number' }, <?php
                    }
                    else
                    {
                    ?> { name: '<?php echo $key; ?>', type: 'string' }, <?php
                    }
                }
                ?>
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
                pagesize: 50,
                pagesizeoptions: ['50', '100', '200', '300', '500', '1000', '5000'],
                selectionmode: 'singlerow',
                altrows: true,
                height: '350px',
                enablebrowserselection: true,
                columnsreorder: true,
                columns: [
                    { text: '<?php echo $CI->lang->line('LABEL_ID'); ?>', dataField: 'id', pinned: true, width: '60', cellsalign: 'right', hidden: <?php echo $system_preference_items['id']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_OUTLET_NAME'); ?>', dataField: 'outlet_name', pinned: true, width: '160', rendered: tooltiprenderer, filtertype: 'list', hidden: <?php echo $system_preference_items['outlet_name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_DATE_PROPOSAL'); ?>', dataField: 'date_proposal', pinned: true, width: '100', rendered: tooltiprenderer, hidden: <?php echo $system_preference_items['date_proposal']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_DATE_EXPECTED'); ?>', dataField: 'date_expected', width: '100', rendered: tooltiprenderer, hidden: <?php echo $system_preference_items['date_expected']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_AMOUNT_BUDGET_TOTAL'); ?>', dataField: 'amount_budget_total', width: '100', rendered: tooltiprenderer, cellsalign: 'right', hidden: <?php echo $system_preference_items['amount_budget_total']?0:1;?>,
                        cellsrenderer: function (row, column, value, defaultHtml, columnSettings, record) {
                            var element = $(defaultHtml);
                            element.html(get_string_amount(value));
                            return element[0].outerHTML;
                        }
                    },
                    { text: '<?php echo $CI->lang->line('LABEL_CROP_NAME'); ?>', dataField: 'crop_name', width: '120', rendered: tooltiprenderer, filtertype: 'list', hidden: <?php echo $system_preference_items['crop_name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_CROP_TYPE'); ?>', dataField: 'crop_type_name', width: '120', rendered: tooltiprenderer, hidden: <?php echo $system_preference_items['crop_type_name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_VARIETY1_NAME'); ?>', dataField: 'variety1_name', width: '120', rendered: tooltiprenderer, hidden: <?php echo $system_preference_items['variety1_name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_VARIETY2_NAME'); ?>', dataField: 'variety2_name', width: '120', rendered: tooltiprenderer, hidden: <?php echo $system_preference_items['variety2_name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_DIVISION_NAME'); ?>', dataField: 'division_name', width: '120', rendered: tooltiprenderer, filtertype: 'list', hidden: <?php echo $system_preference_items['division_name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_ZONE_NAME'); ?>', dataField: 'zone_name', width: '120', rendered: tooltiprenderer, hidden: <?php echo $system_preference_items['zone_name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_TERRITORY_NAME'); ?>', dataField: 'territory_name', width: '120', rendered: tooltiprenderer, hidden: <?php echo $system_preference_items['territory_name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_DISTRICT_NAME'); ?>', dataField: 'district_name', width: '120', rendered: tooltiprenderer, hidden: <?php echo $system_preference_items['district_name']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_STATUS_BUDGET_APPROVE'); ?>', dataField: 'status_approve', width: '100', rendered: tooltiprenderer, filtertype: 'list', hidden: <?php echo $system_preference_items['status_approve']?0:1;?>}
                ]
            });
    });
</script>
