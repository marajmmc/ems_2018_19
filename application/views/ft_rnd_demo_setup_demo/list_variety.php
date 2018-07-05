<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();

?>

<?php
foreach($varieties as $variety)
{
    ?>
    <div class="checkbox">
        <label><input type="checkbox" name="variety_ids[]" value="<?php echo $variety['variety_id']; ?>"><?php echo $variety['variety_name'].' ('.$variety['whose'].')'; ?></label>
    </div>
<?php
}
?>