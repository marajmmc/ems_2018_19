<?php
if ($iou_items)
{
    if ($item['amount_iou_items'])
    {
        $amount_iou_items = json_decode($item['amount_iou_items'], TRUE);
    }
    else
    {
        $amount_iou_items = array();
    }

    $i = 0;
    $total_iou_amount = 0;
    // EACH IOU Items
    foreach ($iou_items as $key => $iou_item)
    {
        if (!isset($amount_iou_items[$key]) || ($amount_iou_items[$key] <= 0))
        {
            continue;
        }
        $current_iou_amount = $amount_iou_items[$key];
        ?>
        <div class="row show-grid">
            <div class="col-xs-<?php echo $col_1; ?>">
                <?php
                if ($i == 0)
                {
                ?>
                    <label class="control-label pull-right">IOU Items:</label>
                <?php
                }
                ?>
            </div>
            <div class="col-xs-<?php echo $col_2; ?>">
                <label class="control-label pull-right normal" style="font-weight:normal !important;"><?php echo $iou_item['name']; ?>:</label>
            </div>
            <div class="col-xs-<?php echo $col_3; ?>" style="padding-left:0">
                <label class="control-label pull-right"><?php echo System_helper::get_string_amount($current_iou_amount); ?></label>
            </div>
        </div>
        <?php
        $total_iou_amount += $current_iou_amount;
        $i++;
    }
    ?>
    <!-----SUMMATION of the IOU Items----->
    <div class="row show-grid" style="margin-bottom:30px">
        <div class="col-xs-<?php echo $col_1; ?>"> &nbsp; </div>
        <div class="col-xs-<?php echo $col_2; ?>" style="border-top:1px solid #000; padding-top:5px">
            <label class="control-label pull-right">Total <?php echo $iou_rqst_label; ?>:</label>
        </div>
        <div class="col-xs-<?php echo $col_3; ?>" style="border-top:1px solid #000; padding-top:5px; padding-left:0; text-align:right">
            <label class="control-label"><?php echo System_helper::get_string_amount($total_iou_amount) ?></label>
        </div>
    </div>
<?php
}
?>