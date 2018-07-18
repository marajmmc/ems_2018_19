<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();

$action_buttons = array();
$action_buttons[] = array(
    'label' => $CI->lang->line("ACTION_BACK"),
    'href' => site_url($CI->controller_url . '/index/list')
);
if (isset($CI->permissions['action0']) && ($CI->permissions['action0'] == 1))
{
    $action_buttons[] = array(
        'type' => 'button',
        'label' => 'View',
        'class' => 'scroll_down'
    );
}
if (isset($CI->permissions['action2']) && ($CI->permissions['action2'] == 1))
{
    $action_buttons[] = array
    (
        'type' => 'button',
        'label' => 'Report Entry',
        'class' => 'button_jqx_action',
        'data-action-link' => site_url($CI->controller_url . '/index/reporting/'.$item['tour_setup_id'])
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
$action_buttons[] = array(
    'label' => $CI->lang->line("ACTION_REFRESH"),
    'href' => site_url($CI->controller_url . '/index/list_reporting/' . $item['id'])
);
$CI->load->view('action_buttons', array('action_buttons' => $action_buttons));
?>
<style>
    label{margin-top:5px}
    label.normal{font-weight:normal !important}
</style>

<div class="row widget">
    <div class="widget-header" style="margin-bottom:5px">
        <div class="title tour-title">
            <?php echo $title; ?>
        </div>
        <div class="clearfix"></div>
    </div>


    <div class="row show-grid">
        <div class="col-xs-12" id="system_jqx_container">
        <!--
         -------------- DATA TABLE LOADS HERE ----------------
        -->
        </div>
    </div>


    <div class="row show-grid" style="margin-top:30px">
        <div class="col-xs-4">
            <label class="control-label pull-right">Name:</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label class="control-label"><?php echo $item['name'] ?></label>
        </div>
    </div>

    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right">Designation:</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label class="control-label">
                <?php if ($item['designation'])
                {
                    echo $item['designation'];
                }
                else
                {
                    echo 'N/A';
                }
                ?>
            </label>
        </div>
    </div>

    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right">Department:</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label class="control-label">
                <?php if ($item['department_name'])
                {
                    echo $item['department_name'];
                }
                else
                {
                    echo 'N/A';
                }
                ?>
            </label>
        </div>
    </div>

    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right">Title:</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label class="control-label"><?php echo $item['title'] ?></label>
        </div>
    </div>

    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE'); ?>:</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            From &nbsp;<label class="control-label"><?php echo System_helper::display_date($item['date_from']) ?></label> &nbsp;
            To &nbsp;<label class="control-label"><?php echo System_helper::display_date($item['date_to']) ?></label>
        </div>
    </div>

    <?php echo Tour_helper::tour_purpose_view($item['tour_setup_id']); ?>

    <?php echo Tour_helper::iou_items_summary_view('', $item); ?>

    <?php if($item['remarks']){ ?>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Remarks:</label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label normal"><?php echo nl2br($item['remarks']); ?></label>
            </div>
        </div>
        <div class="clearfix"></div>
    <?php } ?>

    <div class="clearfix"></div>
</div>


<script type="text/javascript">
    $(document).ready(function () {
        var url = "<?php echo site_url($CI->controller_url.'/index/get_reporting_items');?>";
        // prepare the data
        var source =
        {
            dataType: "json",
            dataFields: [
                { name: 'id', type: 'string' },
                { name: 'sl_no', type: 'string' },
                { name: 'date_reporting', type: 'string' },
                { name: 'purpose', type: 'string' }
            ],
            /* id: 'id', */
            type: 'POST',
            url: url,
            data: {id:<?php echo $item['id']; ?> } // id sent to `get_reporting_items()`
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
                    { text: '<?php echo $this->lang->line('LABEL_SL_NO'); ?>', pinned: true, dataField: 'sl_no', width: '80', rendered: tooltiprenderer },
                    { text: 'Reporting Date', dataField: 'date_reporting', filtertype: 'list', width: '150', rendered: tooltiprenderer },
                    { text: 'Reported Purpose(s)', dataField: 'purpose', rendered: tooltiprenderer }
                ]
            }
        );
    });

    $(document).on('click', '.scroll_down', function(){
        $("html, body").stop().animate(
            { scrollTop: $(document).height() }, { duration: "slow" }
        );
    });
</script>