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
        'label' => 'Waiting List',
        'href' => site_url($CI->controller_url . '/index/list_waiting')
    );
}
if (isset($CI->permissions['action0']) && ($CI->permissions['action0'] == 1))
{
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
        'href' => site_url($CI->controller_url . '/index/set_preference')
    );
}
$action_buttons[] = array(
    'label' => $CI->lang->line("ACTION_REFRESH"),
    'href' => site_url($CI->controller_url . '/index/list_all')

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
                    {
                        text: '<?php echo $CI->lang->line('LABEL_SL_NO'); ?>', datafield: '', pinned: true, width: '50', columntype: 'number', cellsalign: 'right', sortable: false, filterable: false,
                        cellsrenderer: function (row, column, value, defaultHtml, columnSettings, record) {
                            var element = $(defaultHtml);
                            element.html(value + 1);
                            return element[0].outerHTML;
                        }
                    },
                    { text: '<?php echo $CI->lang->line('LABEL_FDB_NO'); ?>', dataField: 'id', width: '100', cellsalign: 'right', hidden: true, pinned: true},
                    { text: '<?php echo $CI->lang->line('LABEL_BUDGET_PROPOSAL_DATE'); ?>', dataField: 'date', width: '120', cellsalign: 'right', pinned: true},
                    { text: '<?php echo $CI->lang->line('LABEL_EXPECTED_DATE'); ?>', dataField: 'expected_date', width: '120', cellsalign: 'right', pinned: true},
                    { text: '<?php echo $CI->lang->line('LABEL_TOTAL_BUDGET'); ?>', dataField: 'total_budget', width: '130', cellsalign: 'right', pinned: true},
                    { text: '<?php echo $CI->lang->line('LABEL_CROP_NAME'); ?>', dataField: 'crop_name', width: '120', cellsalign: 'right'},
                    { text: '<?php echo $CI->lang->line('LABEL_CROP_TYPE'); ?>', dataField: 'crop_type_name', filtertype: 'list', width: '120', cellsalign: 'right'},
                    { text: '<?php echo $CI->lang->line('LABEL_VARIETY_NAME'); ?>', dataField: 'variety_name', width: '120', cellsalign: 'right'},
                    { text: '<?php echo $CI->lang->line('LABEL_COMPETITOR_VARIETY'); ?>', dataField: 'com_variety_name', width: '120', cellsalign: 'right'},
                    { text: '<?php echo $CI->lang->line('LABEL_DIVISION_NAME'); ?>', dataField: 'division_name', width: '100', cellsalign: 'right', hidden: true},
                    { text: '<?php echo $CI->lang->line('LABEL_ZONE_NAME'); ?>', dataField: 'zone_name', width: '100', cellsalign: 'right', hidden: true},
                    { text: '<?php echo $CI->lang->line('LABEL_TERRITORY_NAME'); ?>', dataField: 'territory_name', width: '100', cellsalign: 'right', hidden: true},
                    { text: '<?php echo $CI->lang->line('LABEL_DISTRICT_NAME'); ?>', dataField: 'district_name', width: '130', cellsalign: 'right', hidden: true},
                    { text: '<?php echo $CI->lang->line('LABEL_UPAZILLA_NAME'); ?>', dataField: 'upazilla_name', width: '130', cellsalign: 'right'},
                    { text: '<?php echo $CI->lang->line('LABEL_STATUS_BUDGET'); ?>', dataField: 'status_budget', width: '110', cellsalign: 'right'},
                    { text: '<?php echo $CI->lang->line('LABEL_STATUS_REQUESTED'); ?>', dataField: 'status_requested', width: '100', cellsalign: 'right', filtertype: 'list'},
                    { text: '<?php echo $CI->lang->line('LABEL_STATUS_APPROVAL'); ?>', dataField: 'status_approved', width: '100', cellsalign: 'right', filtertype: 'list'}
                ]
            });
    });
</script>