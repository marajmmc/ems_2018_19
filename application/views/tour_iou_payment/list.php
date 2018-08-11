<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();

/*  $flag :-
        1 = Payment
        2 = Approve
        0 = SuperAdmin & others
*/
$flag = 0;

$PAYMENT_ACCESS = TRUE;
if (($user_group != 1) && (isset($CI->permissions['action7']) && ($CI->permissions['action7'] == 1)))
{
    $PAYMENT_ACCESS = FALSE;
}

$action_buttons = array();
if (isset($CI->permissions['action0']) && ($CI->permissions['action0'] == 1))
{
    $action_buttons[] = array(
        'label' => 'All List',
        'href' => site_url($CI->controller_url . '/index/list_all')
    );
}
if ((isset($CI->permissions['action2']) && ($CI->permissions['action2'] == 1)) && $PAYMENT_ACCESS)
{
    $flag = 1; // 1 - for Payment
    $action_buttons[] = array
    (
        'type' => 'button',
        'label' => 'Payment',
        'class' => 'button_jqx_action',
        'data-action-link' => site_url($CI->controller_url . '/index/payment')
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
        'label' => 'Print Requisition',
        'class' => 'button_jqx_action',
        'data-action-link' => site_url($CI->controller_url . '/index/print_requisition')
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
if (isset($CI->permissions['action7']) && ($CI->permissions['action7'] == 1))
{
    $flag = 2; // 2 - for Approve
    $action_buttons[] = array
    (
        'type' => 'button',
        'label' => 'Approve',
        'class' => 'button_jqx_action',
        'data-action-link' => site_url($CI->controller_url . '/index/approve')
    );
}
$action_buttons[] = array(
    'label' => $CI->lang->line("ACTION_REFRESH"),
    'href' => site_url($CI->controller_url . '/index/list')

);
$CI->load->view('action_buttons', array('action_buttons' => $action_buttons));

if ($user_group == 1)
{
    $flag = 0; // 0 - for SuperAdmin
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
        var url = "<?php echo site_url($CI->controller_url.'/index/get_items/'.$flag);?>";

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
                    { text: '<?php echo $CI->lang->line('LABEL_ID'); ?>', pinned: true, dataField: 'id', width: '50', hidden: <?php echo $system_preference_items['id']?0:1;?>},
                    { text: 'Name', pinned: true, dataField: 'name', width: '180', rendered: tooltiprenderer, hidden: <?php echo $system_preference_items['name']?0:1;?>},
                    { text: 'Employee ID', pinned: true, dataField: 'employee_id', filtertype: 'list', width: '80', rendered: tooltiprenderer, hidden: <?php echo $system_preference_items['employee_id']?0:1;?>},
                    { text: 'Department', pinned: true, dataField: 'department_name', filtertype: 'list', width: '80', rendered: tooltiprenderer, hidden: <?php echo $system_preference_items['department_name']?0:1;?>},
                    { text: 'Designation', pinned: true, dataField: 'designation', filtertype: 'list', width: '100', rendered: tooltiprenderer, hidden: <?php echo $system_preference_items['designation']?0:1;?>},
                    { text: 'Title', dataField: 'title', rendered: tooltiprenderer, hidden: <?php echo $system_preference_items['title']?0:1;?>},
                    { text: 'Date From', dataField: 'date_from', width: '100', rendered: tooltiprenderer, hidden: <?php echo $system_preference_items['date_from']?0:1;?>},
                    { text: 'Date To', dataField: 'date_to', width: '100', rendered: tooltiprenderer, hidden: <?php echo $system_preference_items['date_to']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_AMOUNT_IOU_REQUEST'); ?>', dataField: 'amount_iou_request', width: '100', cellsalign: 'right', hidden: <?php echo $system_preference_items['amount_iou_request']?0:1;?>}
                    <?php
                    $user = User_helper::get_user();
                    if($user->user_id == 1){ ?>
                        , { text: 'IOU Approve Status', dataField: 'status_approved_payment',filtertype: 'list',width:'160',rendered:tooltiprenderer, hidden: <?php echo $system_preference_items['status_approved_payment']?0:1;?>}
                        , { text: 'Payment Status', dataField: 'status_paid_payment',filtertype: 'list',width:'160',rendered:tooltiprenderer, hidden: <?php echo $system_preference_items['status_paid_payment']?0:1;?>}
                    <?php } ?>
                ]
            });
    });
</script>