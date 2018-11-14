<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"> <?php echo (isset($label)) ? $label : "FIELD LABEL"; ?> : </label>
    </div>
</div>
<?php
foreach ($items as $item)
{
    ?>
    <div class="row show-grid">
        <div class="col-xs-6">
            <label style="font-weight:normal" class="control-label pull-right"><?php echo $item['text'] . ' ( ' . $item['phone_no'] . ' )'; ?>
                <span style="color:#FF0000">*</span></label>
        </div>
        <div class="col-xs-2">
            <input type="text" name="<?php echo $name_index . '[' . $item['value'] . ']'; ?>" class="form-control integer_type_positive participant_budget" value="0"/>
        </div>
    </div>
<?php
}
?>
