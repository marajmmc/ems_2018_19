<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<div class="row show-grid">
    <div class="col-xs-4">
        <label style="font-size:1.3em" class="control-label pull-right"><?php echo (isset($label)) ? $label : "FIELD LABEL"; ?>: </label>
    </div>
</div>

<?php
$init_ga_id = -1;
$index=0;
foreach ($items as $item)
{
    if($init_ga_id != $item['ga_id']){
        echo ($index > 0)? '<hr/>':'';
        ?>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label style="font-style:italic;text-decoration:underline; font-size:1.1em" class="control-label pull-right"><?php echo $item['ga_name']; ?>:</label>
            </div>
        </div>
        <?php
        $init_ga_id=$item['ga_id'];
        $index++;
    }
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
