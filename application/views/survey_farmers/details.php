<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();

$action_buttons = array();
$action_buttons[] = array(
    'label' => $CI->lang->line("ACTION_BACK"),
    'href' => site_url($CI->controller_url . '/index/list')
);
if (isset($CI->permissions['action4']) && ($CI->permissions['action4'] == 1)) {
    $action_buttons[] = array(
        'type' => 'button',
        'label' => $CI->lang->line("ACTION_PRINT"),
        'onClick' => "window.print()"
    );
}
$CI->load->view('action_buttons', array('action_buttons' => $action_buttons));

// Check Mark Indicate
$unchecked = "<span style='font-size:1.5em'>&#9744;</span>";
$checked = "<span style='font-size:1.5em'>&#9745;</span>";

?>

<div class="row widget">
    <div class="widget-header" style="margin:0">
        <div class="title">
            <?php echo $title; ?>
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="row show-grid">
        <div style="width:100%">

            <table border="1" bgcolor="red" style="width:100%">
                <tr>
                    <th style="width:30%"><?php echo $CI->lang->line('SURVEY_FARMER_TITLE_FARMER_NAME'); ?></th>
                    <td><?php echo $item['farmer_name']; ?></td>
                    <th><?php echo $CI->lang->line('SURVEY_FARMER_TITLE_FATHER_HUSBAND_NAME'); ?></th>
                    <td><?php echo $item['father_husband_name']; ?></td>
                </tr>
                <tr>
                    <th colspan="4"><?php echo $CI->lang->line('SURVEY_FARMER_TITLE_ADDRESS'); ?></th>
                </tr>
                <tr>
                    <td><b><?php echo $CI->lang->line('SURVEY_FARMER_DISTRICT_NAME'); ?></b> <?php echo $item['district_name']; ?></td>
                    <td><b><?php echo $CI->lang->line('SURVEY_FARMER_UPAZILLA_NAME'); ?></b> <?php echo $item['upazilla_name']; ?></td>
                    <td><b><?php echo $CI->lang->line('SURVEY_FARMER_UNION_NAME'); ?></b> <?php echo $item['union_name']; ?></td>
                    <td><b><?php echo $CI->lang->line('SURVEY_FARMER_VILLAGE_NAME'); ?></b> <?php echo $item['village_name']; ?></td>
                </tr>
                <tr>
                    <td><b><?php echo $CI->lang->line('SURVEY_FARMER_MOBILE_NO'); ?></b> <?php echo $item['mobile_no']; ?></td>
                    <td><b><?php echo $CI->lang->line('SURVEY_FARMER_NID_NO'); ?></b> <?php echo $item['nid_no']; ?></td>
                    <td colspan="2"><b><?php echo $CI->lang->line('SURVEY_FARMER_GROWING_AREA'); ?></b> <?php echo $item['growing_area']; ?></td>
                </tr>
                <tr>
                    <th><?php echo $CI->lang->line('SURVEY_FARMER_TITLE_FAMILY_MEMBER'); ?></th>
                    <td colspan="3">
                        <?php echo '<b>'.$CI->lang->line('SURVEY_FARMER_FAMILY_MEMBER_FEMALE').'</b> '.$item['family_member_female']; ?>
                        &nbsp;&nbsp; | &nbsp;&nbsp;
                        <?php echo '<b>'.$CI->lang->line('SURVEY_FARMER_FAMILY_MEMBER_MALE').'</b> '.$item['family_member_male']; ?>
                        &nbsp;&nbsp; | &nbsp;&nbsp;
                        <?php echo '<b>'.$CI->lang->line('SURVEY_FARMER_FAMILY_MEMBER_OTHERS').'</b> '.$item['family_member_others']; ?>
                        &nbsp;&nbsp; | &nbsp;&nbsp;
                        <?php echo '<b>'.$CI->lang->line('SURVEY_FARMER_FAMILY_MEMBER_TOTAL').'</b> '; ?>
                        <?php echo ($item['family_member_female'] + $item['family_member_male'] + $item['family_member_others']); ?>
                    </td>
                </tr>
                <tr>
                    <th><?php echo $CI->lang->line('SURVEY_FARMER_TITLE_LAND_SIZE'); ?></th>
                    <td colspan="2">
                        <?php echo '<b>'.$CI->lang->line('SURVEY_FARMER_LAND_SIZE_CULTIVABLE').'</b> '.$item['land_size_cultivable']; ?>
                        &nbsp;&nbsp; | &nbsp;&nbsp;
                        <?php echo '<b>'.$CI->lang->line('SURVEY_FARMER_LAND_SIZE_RESIDENTIAL').'</b> '.$item['land_size_residential']; ?>
                        &nbsp;&nbsp; | &nbsp;&nbsp;
                        <?php echo '<b>'.$CI->lang->line('SURVEY_FARMER_OTHERS').'</b> '.$item['land_size_others']; ?>
                    </td>
                    <td>
                        <?php echo '<b>'.$CI->lang->line('SURVEY_FARMER_REMARKS_OTHERS').'</b> '.$item['land_size_others_remarks']; ?>
                    </td>
                </tr>
                <tr>
                    <th><?php echo $CI->lang->line('SURVEY_FARMER_TITLE_CULTIVATED_AREA'); ?></th>
                    <td colspan="2">
                        <?php echo '<b>'.$CI->lang->line('SURVEY_FARMER_CULTIVATED_AREA_VEGETABLES').'</b> '.$item['cultivated_area_vegetables']; ?>
                        &nbsp;&nbsp; | &nbsp;&nbsp;
                        <?php echo '<b>'.$CI->lang->line('SURVEY_FARMER_OTHERS').'</b> '.$item['cultivated_area_others']; ?>
                    </td>
                    <td>
                        <?php echo '<b>'.$CI->lang->line('SURVEY_FARMER_REMARKS_OTHERS').'</b> '.$item['cultivated_area_others_remarks']; ?>
                    </td>
                </tr>
                <tr>
                    <th><?php echo $CI->lang->line('SURVEY_FARMER_TITLE_LAST_YEAR_CULTIVATED'); ?></th>
                    <td colspan="2">
                        <?php echo ($item['last_year_cultivated_paddy']==1)? $checked:$unchecked;?> <?php echo $CI->lang->line('SURVEY_FARMER_LAST_YEAR_CULTIVATED_PADDY') ?>
                        &nbsp; &nbsp;
                        <?php echo ($item['last_year_cultivated_jute']==1)? $checked:$unchecked;?> <?php echo $CI->lang->line('SURVEY_FARMER_LAST_YEAR_CULTIVATED_JUTE') ?>
                        &nbsp; &nbsp;
                        <?php echo ($item['last_year_cultivated_wheat']==1)? $checked:$unchecked;?> <?php echo $CI->lang->line('SURVEY_FARMER_LAST_YEAR_CULTIVATED_WHEAT') ?>
                        &nbsp; &nbsp;
                        <?php echo ($item['last_year_cultivated_mustard']==1)? $checked:$unchecked;?> <?php echo $CI->lang->line('SURVEY_FARMER_LAST_YEAR_CULTIVATED_MUSTARD') ?>
                        &nbsp; &nbsp;
                        <?php echo ($item['last_year_cultivated_maize']==1)? $checked:$unchecked;?> <?php echo $CI->lang->line('SURVEY_FARMER_LAST_YEAR_CULTIVATED_MAIZE') ?>
                        &nbsp; &nbsp;
                        <?php echo ($item['last_year_cultivated_others']==1)? $checked:$unchecked;?> <?php echo $CI->lang->line('SURVEY_FARMER_OTHERS') ?>
                    </td>
                    <td>
                        <?php echo '<b>'.$CI->lang->line('SURVEY_FARMER_REMARKS_OTHERS').'</b> '.$item['last_year_cultivated_others_remarks']; ?>
                    </td>
                </tr>
                <tr>
                    <th><?php echo $CI->lang->line('SURVEY_FARMER_TITLE_CROPPING_INTENSITY'); ?></th>
                    <td colspan="3">
                        <?php echo ($item['cropping_intensity_single']==1)? $checked:$unchecked;?> <?php echo $CI->lang->line('SURVEY_FARMER_CROPPING_INTENSITY_SINGLE') ?>
                        &nbsp; &nbsp;
                        <?php echo ($item['cropping_intensity_double']==1)? $checked:$unchecked;?> <?php echo $CI->lang->line('SURVEY_FARMER_CROPPING_INTENSITY_DOUBLE') ?>
                        &nbsp; &nbsp;
                        <?php echo ($item['cropping_intensity_triple']==1)? $checked:$unchecked;?> <?php echo $CI->lang->line('SURVEY_FARMER_CROPPING_INTENSITY_TRIPLE') ?>
                        &nbsp; &nbsp;
                        <?php echo ($item['cropping_intensity_multiple']==1)? $checked:$unchecked;?> <?php echo $CI->lang->line('SURVEY_FARMER_CROPPING_INTENSITY_MULTIPLE') ?>
                    </td>
                </tr>
                <tr>
                    <th colspan="4">৯. গত বছরের সবজি চাষের বিবরণ:</th>
                </tr>
                <tr>
                    <td colspan="4">
                        <div style="width:100%">
                            <table border="1" style="width:100%;">
                                <tr>
                                    <th class="text-center" rowspan="2">সবজির নাম ও জাত</th>
                                    <th class="text-center" rowspan="2">জমির পরিমান (শতাংশ)</th>
                                    <th class="text-center" rowspan="2">মোট উৎপাদন (কেজি/টন)</th>
                                    <th class="text-center" rowspan="2">মোট উৎপাদিত পণ্যের বিক্রয় মূল্য (টাকা)</th>
                                    <th class="text-center" colspan="6" style="width:30%"> খরচের বিবরণ (টাকা)</th>
                                    <th class="text-center" rowspan="2">মোট খরচ (টাকা)</th>
                                    <th class="text-center" rowspan="2"> আয় (টাকা)</th>
                                </tr>
                                <tr>
                                    <th class="text-center">জমি তৈরী</th>
                                    <th class="text-center">মজুরী</th>
                                    <th class="text-center">সেচ</th>
                                    <th class="text-center">সার</th>
                                    <th class="text-center">কীটনাশক</th>
                                    <th class="text-center">অন্যান্য</th>
                                </tr>
                                <?php
                                foreach ($items as $info)
                                {
                                    ?>
                                    <tr>
                                        <td><?php echo $info['vegetable_variety_name']; ?></td>
                                        <td style="text-align:right"><?php echo $info['area_size']; ?></td>
                                        <td style="text-align:right"><?php echo $info['production_total_kg']; ?></td>
                                        <td style="text-align:right"><?php echo $info['cost_total_produced']; ?></td>
                                        <td style="text-align:right"><?php echo $info['production_cost_land_preparation']; ?></td>
                                        <td style="text-align:right"><?php echo $info['production_cost_wages']; ?></td>
                                        <td style="text-align:right"><?php echo $info['production_cost_irrigation']; ?></td>
                                        <td style="text-align:right"><?php echo $info['production_cost_fertilizers']; ?></td>
                                        <td style="text-align:right"><?php echo $info['production_cost_pesticide']; ?></td>
                                        <td style="text-align:right"><?php echo $info['production_cost_others']; ?></td>
                                        <td style="text-align:right"><?php echo $info['cost_total']; ?></td>
                                        <td style="text-align:right"><?php echo $info['net_profit']; ?></td>
                                    </tr>
                                <?php
                                }
                                ?>
                            </table>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th><?php echo $CI->lang->line('SURVEY_FARMER_TITLE_HAVE_VEGETABLES_TRAINING'); ?></th>
                    <td>
                        <?php echo ($item['have_vegetables_training']==1)? $checked:$unchecked;?> <?php echo $CI->lang->line('SURVEY_FARMER_YES') ?>
                        &nbsp; &nbsp;
                        <?php echo ($item['have_vegetables_training']==0)? $checked:$unchecked;?> <?php echo $CI->lang->line('SURVEY_FARMER_NO') ?>
                    </td>
                    <td>
                        <b><?php echo $CI->lang->line('SURVEY_FARMER_HAVE_VEGETABLES_TRAINING_MEDIA'); ?>:</b> <?php echo $item['have_vegetables_training_media']; ?>
                        <br/>
                        <b><?php echo $CI->lang->line('SURVEY_FARMER_HAVE_VEGETABLES_TRAINING_INSTITUTE'); ?>:</b> <?php echo $item['have_vegetables_training_institute']; ?>
                    </td>
                    <td>
                        <b><?php echo $CI->lang->line('SURVEY_FARMER_HAVE_VEGETABLES_TRAINING_SUBJECT'); ?>:</b> <?php echo $item['have_vegetables_training_subject']; ?>
                    </td>
                </tr>
                <tr>
                    <th><?php echo $CI->lang->line('SURVEY_FARMER_TITLE_NEED_TECHNICAL_KNOWLEDGE_CULTIVATION'); ?></th>
                    <td colspan="3">
                        <?php echo ($item['need_technical_knowledge_cultivation']==1)? $checked:$unchecked;?> <?php echo $CI->lang->line('SURVEY_FARMER_YES') ?>
                        &nbsp; &nbsp;
                        <?php echo ($item['need_technical_knowledge_cultivation']==0)? $checked:$unchecked;?> <?php echo $CI->lang->line('SURVEY_FARMER_NO') ?>
                    </td>
                </tr>
                <tr>
                    <th><?php echo $CI->lang->line('SURVEY_FARMER_TITLE_TECHNICAL_KNOWLEDGE_VEGETABLES_CULTIVATION'); ?></th>
                    <td colspan="2">
                        <?php echo ($item['technical_knowledge_vegetables_cultivation']==1)? $checked:$unchecked;?> <?php echo $CI->lang->line('SURVEY_FARMER_TECHNICAL_KNOWLEDGE_VEGETABLES_CULTIVATION') ?>
                        &nbsp; &nbsp;
                        <?php echo ($item['technical_knowledge_quality_seeds']==1)? $checked:$unchecked;?> <?php echo $CI->lang->line('SURVEY_FARMER_TECHNICAL_KNOWLEDGE_QUALITY_SEEDS') ?>
                        &nbsp; &nbsp;
                        <?php echo ($item['technical_knowledge_pest_management']==1)? $checked:$unchecked;?> <?php echo $CI->lang->line('SURVEY_FARMER_TECHNICAL_KNOWLEDGE_PEST_MANAGEMENT') ?>
                        &nbsp; &nbsp;
                        <?php echo ($item['technical_knowledge_others']==1)? $checked:$unchecked;?> <?php echo $CI->lang->line('SURVEY_FARMER_OTHERS') ?>
                    </td>
                    <td>
                        <?php echo '<b>'.$CI->lang->line('SURVEY_FARMER_REMARKS_OTHERS').'</b> '.$item['technical_knowledge_others_remarks']; ?>
                    </td>
                </tr>
                <tr>
                    <th><?php echo $CI->lang->line('SURVEY_FARMER_TITLE_SEEDS_COLLECT'); ?></th>
                    <td colspan="2">
                        <?php echo ($item['seeds_collect_dealers']==1)? $checked:$unchecked;?> <?php echo $CI->lang->line('SURVEY_FARMER_SEEDS_COLLECT_DEALERS') ?>
                        &nbsp; &nbsp;
                        <?php echo ($item['seeds_collect_retailers']==1)? $checked:$unchecked;?> <?php echo $CI->lang->line('SURVEY_FARMER_SEEDS_COLLECT_RETAILERS') ?>
                        &nbsp; &nbsp;
                        <?php echo ($item['seeds_collect_leadfarmers']==1)? $checked:$unchecked;?> <?php echo $CI->lang->line('SURVEY_FARMER_SEEDS_COLLECT_LEADFARMERS') ?>
                        &nbsp; &nbsp;
                        <?php echo ($item['seeds_collect_hatbazar']==1)? $checked:$unchecked;?> <?php echo $CI->lang->line('SURVEY_FARMER_SEEDS_COLLECT_HATBAZAR') ?>
                        &nbsp; &nbsp;
                        <?php echo ($item['seeds_collect_ownseeds']==1)? $checked:$unchecked;?> <?php echo $CI->lang->line('SURVEY_FARMER_SEEDS_COLLECT_OWNSEEDS') ?>
                        &nbsp; &nbsp;
                        <?php echo ($item['seeds_collect_others']==1)? $checked:$unchecked;?> <?php echo $CI->lang->line('SURVEY_FARMER_OTHERS') ?>
                    </td>
                    <td>
                        <?php echo '<b>'.$CI->lang->line('SURVEY_FARMER_REMARKS_OTHERS').'</b> '.$item['seeds_collect_others_remarks']; ?>
                    </td>
                </tr>
                <tr>
                    <th><?php echo $CI->lang->line('SURVEY_FARMER_TITLE_ENSURE_SEED_QUALITY'); ?></th>
                    <td colspan="2">
                        <?php echo ($item['ensure_seed_quality_germination']==1)? $checked:$unchecked;?> <?php echo $CI->lang->line('SURVEY_FARMER_ENSURE_SEED_QUALITY_GERMINATION') ?>
                        &nbsp; &nbsp;
                        <?php echo ($item['ensure_seed_quality_faith']==1)? $checked:$unchecked;?> <?php echo $CI->lang->line('SURVEY_FARMER_ENSURE_SEED_QUALITY_FAITH') ?>
                        &nbsp; &nbsp;
                        <?php echo ($item['ensure_seed_quality_others']==1)? $checked:$unchecked;?> <?php echo $CI->lang->line('SURVEY_FARMER_OTHERS') ?>
                    </td>
                    <td>
                        <?php echo '<b>'.$CI->lang->line('SURVEY_FARMER_REMARKS_OTHERS').'</b> '.$item['ensure_seed_quality_others_remarks']; ?>
                    </td>
                </tr>
                <tr>
                    <th><?php echo $CI->lang->line('SURVEY_FARMER_TITLE_GOOD_SEED_PURCHASE'); ?></th>
                    <td>
                        <?php echo ($item['good_seed_purchase']==1)? $checked:$unchecked;?> <?php echo $CI->lang->line('SURVEY_FARMER_YES') ?>
                        &nbsp; &nbsp;
                        <?php echo ($item['good_seed_purchase']==0)? $checked:$unchecked;?> <?php echo $CI->lang->line('SURVEY_FARMER_NO') ?>
                    </td>
                    <td colspan="2">
                        <?php echo '<b>'.$CI->lang->line('SURVEY_FARMER_REMARKS_NO').'</b> '.$item['good_seed_purchase_remarks']; ?>
                    </td>
                </tr>
                <tr>
                    <th><?php echo $CI->lang->line('SURVEY_FARMER_TITLE_SELL_VEGETABLES_TO'); ?></th>
                    <td colspan="2">
                        <?php echo ($item['sell_vegetables_to_artodar_paikar']==1)? $checked:$unchecked;?> <?php echo $CI->lang->line('SURVEY_FARMER_SELL_VEGETABLES_TO_ARTODAR_PAIKAR') ?>
                        &nbsp; &nbsp;
                        <?php echo ($item['sell_vegetables_to_hatbazar']==1)? $checked:$unchecked;?> <?php echo $CI->lang->line('SURVEY_FARMER_SELL_VEGETABLES_TO_HATBAZAR') ?>
                        &nbsp; &nbsp;
                        <?php echo ($item['sell_vegetables_in_group']==1)? $checked:$unchecked;?> <?php echo $CI->lang->line('SURVEY_FARMER_SELL_VEGETABLES_IN_GROUP') ?>
                        &nbsp; &nbsp;
                        <?php echo ($item['sell_vegetables_others']==1)? $checked:$unchecked;?> <?php echo $CI->lang->line('SURVEY_FARMER_OTHERS') ?>
                    </td>
                    <td>
                        <?php echo '<b>'.$CI->lang->line('SURVEY_FARMER_REMARKS_OTHERS').'</b> '.$item['sell_vegetables_others_remarks']; ?>
                    </td>
                </tr>
                <tr>
                    <th><?php echo $CI->lang->line('SURVEY_FARMER_TITLE_ADDRESS_SELLING_HATBAZAR'); ?></th>
                    <td colspan="3">
                        <?php echo nl2br($item['address_selling_hatbazar']); ?>
                    </td>
                </tr>
                <tr>
                    <th><?php echo $CI->lang->line('SURVEY_FARMER_TITLE_DOES_ARTODAR_PAIKAR'); ?></th>
                    <td colspan="3">
                        <?php echo ($item['does_artodar_paikar_helps']==1)? $checked:$unchecked;?> <?php echo $CI->lang->line('SURVEY_FARMER_YES') ?>
                        &nbsp; &nbsp;
                        <?php echo ($item['does_artodar_paikar_helps']==0)? $checked:$unchecked;?> <?php echo $CI->lang->line('SURVEY_FARMER_NO') ?>
                    </td>
                </tr>
                <tr>
                    <th> &nbsp;&nbsp;&nbsp;&nbsp; - <?php echo $CI->lang->line('SURVEY_FARMER_TITLE_DOES_ARTODAR_PAIKAR_HELPS'); ?></th>
                    <td colspan="2">
                        <?php echo ($item['does_artodar_paikar_helps_supplying_seeds']==1)? $checked:$unchecked;?> <?php echo $CI->lang->line('SURVEY_FARMER_DOES_ARTODAR_PAIKAR_HELPS_SUPPLYING_SEEDS') ?>
                        &nbsp; &nbsp;
                        <?php echo ($item['does_artodar_paikar_helps_credit_facilities']==1)? $checked:$unchecked;?> <?php echo $CI->lang->line('SURVEY_FARMER_DOES_DOES_ARTODAR_PAIKAR_HELPS_CREDIT_FACILITIES') ?>
                        &nbsp; &nbsp;
                        <?php echo ($item['does_artodar_paikar_helps_others']==1)? $checked:$unchecked;?> <?php echo $CI->lang->line('SURVEY_FARMER_OTHERS') ?>
                    </td>
                    <td>
                        <?php echo '<b>'.$CI->lang->line('SURVEY_FARMER_REMARKS_OTHERS').'</b> '.$item['does_artodar_paikar_helps_remarks']; ?>
                    </td>
                </tr>
                <tr>
                    <th><?php echo $CI->lang->line('SURVEY_FARMER_TITLE_SEED_FACILITIES'); ?></th>
                    <td>
                        <?php echo ($item['seed_facilities']==1)? $checked:$unchecked;?> <?php echo $CI->lang->line('SURVEY_FARMER_YES') ?>
                        &nbsp; &nbsp;
                        <?php echo ($item['seed_facilities']==0)? $checked:$unchecked;?> <?php echo $CI->lang->line('SURVEY_FARMER_NO') ?>
                    </td>
                    <td colspan="2">
                        <?php echo '<b>'.$CI->lang->line('SURVEY_FARMER_REMARKS_NO').'</b> '.$item['seed_facilities_remarks']; ?>
                    </td>
                </tr>
                <tr>
                    <th><?php echo $CI->lang->line('SURVEY_FARMER_TITLE_DO_KNOW_ARM'); ?></th>
                    <td>
                        <?php echo ($item['do_know_arm']==1)? $checked:$unchecked;?> <?php echo $CI->lang->line('SURVEY_FARMER_YES') ?>
                        &nbsp; &nbsp;
                        <?php echo ($item['do_know_arm']==0)? $checked:$unchecked;?> <?php echo $CI->lang->line('SURVEY_FARMER_NO') ?>
                    </td>
                    <td colspan="2">
                        <?php echo '<b>'.$CI->lang->line('SURVEY_FARMER_REMARKS_YES').'</b> '.$item['do_know_arm_remarks']; ?>
                    </td>
                </tr>
                <tr>
                    <th><?php echo $CI->lang->line('SURVEY_FARMER_TITLE_REMARKS'); ?></th>
                    <td colspan="3">
                        <?php echo nl2br($item['remarks']); ?>
                    </td>
                </tr>
            </table>

        </div>
    </div>
</div>

<script type="text/javascript">
    jQuery(document).ready(function ($) {
        system_preset({controller: '<?php echo $CI->router->class; ?>'});
        system_off_events(); // Triggers
    });
</script>
