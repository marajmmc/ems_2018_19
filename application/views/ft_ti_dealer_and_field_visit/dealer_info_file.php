<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();
?>
<?php foreach($dealer_info_file as $key=>$file){$key++;?>
    <a href="<?php echo $CI->config->item('system_base_url_dealer_and_farmer_visit').$file['image_location']; ?>" class="external btn btn-danger" target="_blank"><?php echo 'File '.$key;?></a>
<?php } ?>
