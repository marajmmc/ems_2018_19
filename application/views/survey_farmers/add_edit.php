<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();

$action_buttons = array();
$action_buttons[] = array
(
    'label' => $CI->lang->line("ACTION_BACK"),
    'href' => site_url($CI->controller_url)
);
if ((isset($CI->permissions['action1']) && ($CI->permissions['action1'] == 1)) || (isset($CI->permissions['action2']) && ($CI->permissions['action2'] == 1))) {
    $action_buttons[] = array
    (
        'type' => 'button',
        'label' => $CI->lang->line("ACTION_SAVE"),
        'id' => 'button_action_save',
        'data-form' => '#save_form'
    );
}
$action_buttons[] = array
(
    'type' => 'button',
    'label' => $CI->lang->line("ACTION_CLEAR"),
    'id' => 'button_action_clear',
    'data-form' => '#save_form'
);
$CI->load->view("action_buttons", array('action_buttons' => $action_buttons));

?>

<form class="form_valid" id="save_form" action="<?php echo site_url($CI->controller_url . '/index/save'); ?>" method="post">
    <input type="hidden" id="id" name="id" value="<?php echo $item['id']; ?>"/>
    <div class="row widget">
        <div class="widget-header">
            <div class="title">
                <?php echo $title; ?>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-12">

                <table class="table table-bordered table-responsive-sm ">
                    <tbody>
                    <tr>
                        <th style="width: 30%">১.	কৃষকের নাম:    </th>
                        <th style="width: 70%"><input type="text" class="form-control" id="crop_type_preference" name="item[crop_type_preference]" /></th>
                    </tr>
                    <tr>
                        <th>২.	পিতা/স্বামীর নাম: </th>
                        <th><input type="text" class="form-control" id="crop_type_preference" name="item[crop_type_preference]" /></th>
                    </tr>
                    <tr>
                        <th colspan="2">৩.	ঠিকানা:  </th>
                    </tr>
                    <tr>
                        <th colspan="2">
                            <div class="form-row">
                                <div class="col-md-3">
                                    <label for="validationTooltip01">জেলা:</label>
                                    <select class="form-control">
                                        <option selected>Open this select menu</option>
                                        <option value="1">One</option>
                                        <option value="2">Two</option>
                                        <option value="3">Three</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="validationTooltip02">উপজেলা:</label>
                                    <select class="form-control">
                                        <option selected>Open this select menu</option>
                                        <option value="1">One</option>
                                        <option value="2">Two</option>
                                        <option value="3">Three</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="validationTooltip02">ইউনিয়ন:</label>
                                    <select class="form-control">
                                        <option selected>Open this select menu</option>
                                        <option value="1">One</option>
                                        <option value="2">Two</option>
                                        <option value="3">Three</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="validationTooltip02">গ্রাম:</label>
                                    <input type="text" class="form-control" id="crop_type_preference" name="item[crop_type_preference]" />
                                </div>
                                <div class="col-md-12">
                                    <hr/>
                                </div>
                                <div class="col-md-3">
                                    <label for="validationTooltip02">মোবাইল:</label>
                                    <input type="text" class="form-control" id="crop_type_preference" name="item[crop_type_preference]" />
                                </div>
                                <div class="col-md-3">
                                    <label for="validationTooltip02">এনআইডি নং:</label>
                                    <input type="text" class="form-control" id="crop_type_preference" name="item[crop_type_preference]" />
                                </div>
                                <div class="col-md-3">
                                    <label for="validationTooltip02">আবাদি এলাকা:</label>
                                    <input type="text" class="form-control" id="crop_type_preference" name="item[crop_type_preference]" />
                                </div>
                            </div>
                        </th>
                    </tr>
                    <tr>
                        <th>৪.	আপনার পরিবারের সদস্য কতজন         </th>
                        <th>
                            <div class="col-md-4">
                                <label for="validationTooltip02">নারী:</label>
                                <input type="text" class="form-control" id="crop_type_preference" name="item[crop_type_preference]" />
                            </div>
                            <div class="col-md-4">
                                <label for="validationTooltip02">পুরুষ:</label>
                                <input type="text" class="form-control" id="crop_type_preference" name="item[crop_type_preference]" />
                            </div>
                            <div class="col-md-4">
                                <label for="validationTooltip02">মোট :</label>
                                <input type="text" class="form-control" id="crop_type_preference" name="item[crop_type_preference]" />
                            </div>
                        </th>
                    </tr>
                    <tr>
                        <th>৫.	জমির বিবরণ (শতাংশ):</th>
                        <th>
                            <div class="col-md-4">
                                <label for="validationTooltip02">চাষযোগ্য জমি</label>
                                <input type="text" class="form-control" id="crop_type_preference" name="item[crop_type_preference]" />
                            </div>
                            <div class="col-md-4">
                                <label for="validationTooltip02">বসতভিটা</label>
                                <input type="text" class="form-control" id="crop_type_preference" name="item[crop_type_preference]" />
                            </div>
                            <div class="col-md-4">
                                <label for="validationTooltip02">অন্যান্য</label>
                                <input type="text" class="form-control" id="crop_type_preference" name="item[crop_type_preference]" />
                            </div>
                        </th>
                    </tr>
                    <tr>
                        <th>৬.	চাষকৃত ফসল (শতাংশ):  </th>
                        <th>
                            <div class="col-md-4">
                                <label for="validationTooltip02">সবজি</label>
                                <input type="text" class="form-control" id="crop_type_preference" name="item[crop_type_preference]" />
                            </div>
                            <div class="col-md-4">
                                <label for="validationTooltip02">অন্যান্য</label>
                                <input type="text" class="form-control" id="crop_type_preference" name="item[crop_type_preference]" />
                            </div>
                        </th>
                    </tr>
                    <tr>
                        <th>৭.	গত বছর সবজি ছাড়া আপনি আর কি কি ফসল চাষ করেছিলেন ? </th>
                        <th>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="inlineCheckbox1" value="option1">
                                <label class="form-check-label" for="inlineCheckbox1">ধান</label>

                                <input class="form-check-input" type="checkbox" id="inlineCheckbox2" value="option2">
                                <label class="form-check-label" for="inlineCheckbox2">পাট</label>

                                <input class="form-check-input" type="checkbox" id="inlineCheckbox2" value="option2">
                                <label class="form-check-label" for="inlineCheckbox2">গম</label>

                                <input class="form-check-input" type="checkbox" id="inlineCheckbox2" value="option2">
                                <label class="form-check-label" for="inlineCheckbox2">সরিষা</label>

                                <input class="form-check-input" type="checkbox" id="inlineCheckbox2" value="option2">
                                <label class="form-check-label" for="inlineCheckbox2">ভূট্টা</label>
                                <br/>
                                <input class="form-check-input" type="checkbox" id="inlineCheckbox2" value="option2">
                                <label class="form-check-label" for="inlineCheckbox2">
                                    অন্যান্য
                                    <input type="text" class=" form-inline" id="crop_type_preference" name="item[crop_type_preference]" placeholder="যদি অন্যান্য হয় তাহলে পূরণ করুন"  style="width: 250px;"/>
                                </label>
                            </div>
                        </th>
                    </tr>
                    <tr>
                        <th>৮.	শস্য নিবিড়তা: </th>
                        <th>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="inlineCheckbox1" value="option1">
                                <label class="form-check-label" for="inlineCheckbox1">এক ফসলি</label>

                                <input class="form-check-input" type="checkbox" id="inlineCheckbox2" value="option2">
                                <label class="form-check-label" for="inlineCheckbox2">দো-ফসলি</label>

                                <input class="form-check-input" type="checkbox" id="inlineCheckbox2" value="option2">
                                <label class="form-check-label" for="inlineCheckbox2">তিন ফসলি</label>

                                <input class="form-check-input" type="checkbox" id="inlineCheckbox2" value="option2">
                                <label class="form-check-label" for="inlineCheckbox2">বহু ফসলি</label>
                            </div>
                        </th>
                    </tr>
                    <tr>
                        <th colspan="2">৯.	গত বছরের সবজি চাষের বিবরণ:</th>
                    </tr>
                    </tbody>
                </table>
                <div class="col-md-12 col-xs-12" style=" overflow: scroll" id="system_add_more_table_container">
                    <table class="table table-bordered table-responsive" style="">
                        <thead>
                        <tr>
                            <th class="text-center" rowspan="2">সবজির নাম ও জাত</th>
                            <th class="text-center" rowspan="2">জমির পরিমান (শতাংশ)</th>
                            <th class="text-center" rowspan="2">মোট উৎপাদন (কেজি/টন)</th>
                            <th class="text-center" rowspan="2">মোট উৎপাদিত পণ্যের বিক্রয় মূল্য (টাকা)</th>
                            <th class="text-center" colspan="6"> খরচের বিবরণ (টাকা)</th>
                            <th class="text-center" rowspan="2">মোট খরচ (টাকা) </th>
                            <th class="text-center" rowspan="2"> আয় (টাকা)</th>
                            <th class="text-center" rowspan="2"> বাতিল/মুছুন</th>
                        </tr>
                        <tr>
                            <th class="text-center">জমি তৈরী</th>
                            <th class="text-center">মজুরী</th>
                            <th class="text-center">সেচ </th>
                            <th class="text-center">সার </th>
                            <th class="text-center">কীটনাশক</th>
                            <th class="text-center">অন্যান্য</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="add_more_tr_last">
                            <td colspan="14">
                                <button type="button" class="btn btn-success btn-sm system_button_add_more pull-right" data-current-id="0">+ যোগ করুন</button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <table class="table table-bordered table-responsive ">
                    <tbody>
                    <tr>
                        <th style="width: 30%">১০.	সবজি চাষে আপনার প্রশিক্ষণ আছে কি না ?</th>
                        <th style="width: 70%">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio1" value="option1">
                                <label class="form-check-label" for="inlineRadio1">হ্যাঁ</label>

                                <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio2" value="option2">
                                <label class="form-check-label" for="inlineRadio2">না</label>
                            </div>
                            <hr/>
                            <div class="col-md-4">
                                <label for="validationTooltip02">কাদের মাধ্যমে? </label>
                                <input type="text" class="form-control" id="crop_type_preference" name="item[crop_type_preference]" />
                            </div>
                            <div class="col-md-4">
                                <label for="validationTooltip02">কোন প্রতিষ্ঠান থেকে? 	</label>
                                <input type="text" class="form-control" id="crop_type_preference" name="item[crop_type_preference]" />
                            </div>
                            <div class="col-md-4">
                                <label for="validationTooltip02">কি বিষয়ে প্রশিক্ষন পেয়েছেন? 	</label>
                                <input type="text" class="form-control" id="crop_type_preference" name="item[crop_type_preference]" />
                            </div>
                        </th>
                    </tr>
                    <tr>
                        <th>১১. সবজি চাষে আপনার কারিগরী জ্ঞানের প্রয়োজন আছে কি ? </th>
                        <th>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio1" value="option1">
                                <label class="form-check-label" for="inlineRadio1">হ্যাঁ</label>

                                <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio2" value="option2">
                                <label class="form-check-label" for="inlineRadio2">
                                    না
                                    <input type="text" class=" form-inline" id="crop_type_preference" name="item[crop_type_preference]" placeholder="যদি না হয় তাহলে পূরণ করুন"  style="width: 250px;"/>
                                </label>
                            </div>
                        </th>
                    </tr>
                    <tr>
                        <th> ১২. যদি হা হয় তাহলে কি ধরনের কারিগরী জ্ঞানের প্রয়োজন  </th>
                        <th>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="inlineCheckbox1" value="option1">
                                <label class="form-check-label" for="inlineCheckbox1">সবজি উৎপাদন কলাকৌশল সম্পর্কে</label>

                                <input class="form-check-input" type="checkbox" id="inlineCheckbox2" value="option2">
                                <label class="form-check-label" for="inlineCheckbox2"> গুণগত মানসম্পন্ন বীজ সর্ম্পকে</label>

                                <input class="form-check-input" type="checkbox" id="inlineCheckbox2" value="option2">
                                <label class="form-check-label" for="inlineCheckbox2">সবজির রোগ-বালাই/পোকা-মাকড় সম্পর্কে</label>
                                <br/>
                                <input class="form-check-input" type="checkbox" id="inlineCheckbox2" value="option2">
                                <label class="form-check-label" for="inlineCheckbox2">
                                    অন্যান্য
                                    <input type="text" class=" form-inline" id="crop_type_preference" name="item[crop_type_preference]" placeholder="যদি অন্যান্য হয় তাহলে পূরণ করুন"  style="width: 250px;"/>
                                </label>
                            </div>
                        </th>
                    </tr>
                    <tr>
                        <th> ১৩.  আপনি সবজির বীজ কোথা হতে সংগ্রহ করেন ? </th>
                        <th>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="inlineCheckbox1" value="option1">
                                <label class="form-check-label" for="inlineCheckbox1">ডিলার </label>

                                <input class="form-check-input" type="checkbox" id="inlineCheckbox2" value="option2">
                                <label class="form-check-label" for="inlineCheckbox2"> রিটেইলার </label>

                                <input class="form-check-input" type="checkbox" id="inlineCheckbox2" value="option2">
                                <label class="form-check-label" for="inlineCheckbox2">লিড ফার্মার</label>

                                <input class="form-check-input" type="checkbox" id="inlineCheckbox2" value="option2">
                                <label class="form-check-label" for="inlineCheckbox2">হাট/বাজার</label>

                                <input class="form-check-input" type="checkbox" id="inlineCheckbox2" value="option2">
                                <label class="form-check-label" for="inlineCheckbox2">নিজস্ব বীজ</label>
                                <br/>
                                <input class="form-check-input" type="checkbox" id="inlineCheckbox2" value="option2">
                                <label class="form-check-label" for="inlineCheckbox2">
                                    অন্যান্য
                                    <input type="text" class=" form-inline" id="crop_type_preference" name="item[crop_type_preference]" placeholder="যদি অন্যান্য হয় তাহলে পূরণ করুন"  style="width: 250px;"/>
                                </label>
                            </div>
                        </th>
                    </tr>
                    <tr>
                        <th> ১৪. আপনার সংগৃহিত সবজি বীজ যে ভাল বীজ তা আপনি কিভাবে নিশ্চিত করেন?</th>
                        <th>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="inlineCheckbox1" value="option1">
                                <label class="form-check-label" for="inlineCheckbox1">     অঙ্কুরোদগম পরীক্ষা করে </label>

                                <input class="form-check-input" type="checkbox" id="inlineCheckbox2" value="option2">
                                <label class="form-check-label" for="inlineCheckbox2"> বিশ্বাসের উপর </label>

                                <input class="form-check-input" type="checkbox" id="inlineCheckbox2" value="option2">
                                <label class="form-check-label" for="inlineCheckbox2">লিড ফার্মার</label>

                                <input class="form-check-input" type="checkbox" id="inlineCheckbox2" value="option2">
                                <label class="form-check-label" for="inlineCheckbox2">হাট/বাজার</label>

                                <input class="form-check-input" type="checkbox" id="inlineCheckbox2" value="option2">
                                <label class="form-check-label" for="inlineCheckbox2">নিজস্ব বীজ</label>
                                <br/>
                                <input class="form-check-input" type="checkbox" id="inlineCheckbox2" value="option2">
                                <label class="form-check-label" for="inlineCheckbox2">
                                    অন্যান্য
                                    <input type="text" class=" form-inline" id="crop_type_preference" name="item[crop_type_preference]" placeholder="যদি অন্যান্য হয় তাহলে পূরণ করুন"  style="width: 250px;"/>
                                </label>
                            </div>
                        </th>
                    </tr>
                    <tr>
                        <th> ১৫. ভাল বীজের নিশ্চয়তা নিয়ে কেউ যদি আপনার কাছে বীজ নিয়ে আসে তাহলে কি আপনি তার কাছ থেকে বীজ ক্রয় করবেন ? </th>
                        <th>
                            <div class="form-check form-check-inline">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio1" value="option1">
                                    <label class="form-check-label" for="inlineRadio1">হ্যাঁ</label>

                                    <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio2" value="option2">
                                    <label class="form-check-label" for="inlineRadio2">
                                        না
                                        <input type="text" class=" form-inline" id="crop_type_preference" name="item[crop_type_preference]" placeholder="যদি না হয় তাহলে পূরণ করুন"  style="width: 250px;"/>
                                    </label>
                                </div>
                            </div>
                        </th>
                    </tr>
                    <tr>
                        <th>১৬. উৎপাদিত সবজি আপনি কিভাবে বিক্রি করেন ?</th>
                        <th>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="inlineCheckbox1" value="option1">
                                <label class="form-check-label" for="inlineCheckbox1"> আড়ৎদার/পাইকার এসে জমি হতে নিয়ে যায়</label>

                                <input class="form-check-input" type="checkbox" id="inlineCheckbox2" value="option2">
                                <label class="form-check-label" for="inlineCheckbox2"> স্থানীয় হাট/বাজারে বিক্রি করেন </label>

                                <input class="form-check-input" type="checkbox" id="inlineCheckbox2" value="option2">
                                <label class="form-check-label" for="inlineCheckbox2">সম্মিলিত ভাবে</label>

                                <br/>
                                <input class="form-check-input" type="checkbox" id="inlineCheckbox2" value="option2">
                                <label class="form-check-label" for="inlineCheckbox2">
                                    অন্যান্য
                                    <input type="text" class=" form-inline" id="crop_type_preference" name="item[crop_type_preference]" placeholder="যদি অন্যান্য হয় তাহলে পূরণ করুন"  style="width: 250px;"/>
                                </label>
                            </div>
                        </th>
                    </tr>
                    <tr>
                        <th>১৭. যদি স্থানীয় হাট/বাজারে বিক্রি করেন তাহলে তার ঠিকানা: </th>
                        <th><input type="text" class="form-control" id="crop_type_preference" name="item[crop_type_preference]" /></th>
                    </tr>
                    <tr>
                        <th>১৮. আড়ৎদার/পাইকার সবজি উৎপাদনে কি কোন প্রকার সহায়তা করেন ? </th>
                        <th>
                            <div class="form-check form-check-inline">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio1" value="option1">
                                    <label class="form-check-label" for="inlineRadio1">হ্যাঁ</label>

                                    <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio2" value="option2">
                                    <label class="form-check-label" for="inlineRadio2">না</label>
                                </div>
                            </div>
                        </th>
                    </tr>
                    <tr>
                        <th>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            যদি হা হয় তহালে কি ধরনের সহায়তা করেন
                        </th>
                        <th>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="inlineCheckbox1" value="option1">
                                <label class="form-check-label" for="inlineCheckbox1"> বীজ সরবরাহ করেন</label>

                                <input class="form-check-input" type="checkbox" id="inlineCheckbox2" value="option2">
                                <label class="form-check-label" for="inlineCheckbox2"> ঋন দেন </label>

                                <br/>
                                <input class="form-check-input" type="checkbox" id="inlineCheckbox2" value="option2">
                                <label class="form-check-label" for="inlineCheckbox2">
                                    অন্যান্য
                                    <input type="text" class=" form-inline" id="crop_type_preference" name="item[crop_type_preference]" placeholder="যদি অন্যান্য হয় তাহলে পূরণ করুন"  style="width: 250px;"/>
                                </label>
                            </div>
                        </th>
                    </tr>
                    <tr>
                        <th> ১৯. আপনাকে যদি ভাল বীজ, কারিগরী জ্ঞান, বাজারজাতকরণ চেইন সহ অন্যান্য সুযোগ সুবিধা করে দেওয়া  হয় তাহলে কি আপনি আরও বেশী লাভবান হবেন বলে মনে করেন ?</th>
                        <th>
                            <div class="form-check form-check-inline">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio1" value="option1">
                                    <label class="form-check-label" for="inlineRadio1">হ্যাঁ</label>

                                    <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio2" value="option2">
                                    <label class="form-check-label" for="inlineRadio2">
                                        না
                                        <input type="text" class=" form-inline" id="crop_type_preference" name="item[crop_type_preference]" placeholder="যদি না হয় তাহলে পূরণ করুন"  style="width: 250px;"/>
                                    </label>
                                </div>
                            </div>
                        </th>
                    </tr>
                    <tr>
                        <th>২০. আপনি ”এআর মালিক সীডস (প্রা:) লিমিটেড” সম্পর্কে  জানেন কি না ?            </th>
                        <th>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio2" value="option2">
                                <label class="form-check-label" for="inlineRadio2">
                                    হ্যাঁ
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <textarea class=" form-control" id="crop_type_preference" name="item[crop_type_preference]" placeholder="যদি হা হয় তাহলে কিভাবে জানেন ? "></textarea>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio1" value="option1">
                                <label class="form-check-label" for="inlineRadio1">না</label>
                            </div>
                        </th>
                    </tr>
                    <tr>
                        <th colspan="2">
                            <div class="col-md-12">
                                <label for="validationTooltip02">মন্তব্য: </label>
                                <textarea class=" form-control" id="crop_type_preference" name="item[crop_type_preference]" rows="5" placeholder="মন্তব্য "></textarea>
                            </div>
                        </th>
                    </tr>
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</form>
<div id="system_content_add_more" style="display:none;">
    <table>
        <tbody>
        <tr>
            <td>
                <input type="text" class="form-control purpose"/>
            </td>
            <td>
                <input type="text" class="form-control purpose"/>
            </td>
            <td>
                <input type="text" class="form-control purpose"/>
            </td>
            <td>
                <input type="text" class="form-control purpose"/>
            </td>
            <td>
                <input type="text" class="form-control purpose"/>
            </td>
            <td>
                <input type="text" class="form-control purpose"/>
            </td>
            <td>
                <input type="text" class="form-control purpose"/>
            </td>
            <td>
                <input type="text" class="form-control purpose"/>
            </td>
            <td>
                <input type="text" class="form-control purpose"/>
            </td>
            <td>
                <input type="text" class="form-control purpose"/>
            </td>
            <td>
                <input type="text" class="form-control purpose"/>
            </td>
            <td>
                <input type="text" class="form-control purpose"/>
            </td>
            <td style="width:1%">
                <button type="button" class="btn btn-danger btn-sm system_button_add_delete"><?php echo $CI->lang->line('DELETE'); ?></button>
            </td>
        </tr>
        </tbody>
    </table>
</div>

<script type="text/javascript">


    jQuery(document).ready(function ($)
    {
        system_preset({controller: '<?php echo $CI->router->class; ?>'});
        system_off_events(); // Triggers

        $(document).on("click", ".system_button_add_more", function (event)
        {
            var current_id = parseInt($(this).attr('data-current-id'));
            current_id = current_id + 1;
            $(this).attr('data-current-id', current_id);
            var content_id = '#system_content_add_more table tbody';
            $(content_id + ' .purpose').attr('name', 'items[' + current_id + ']');
            var html = $(content_id).html();
            $("#system_add_more_table_container tbody tr.add_more_tr_last").before(html);
        });

        $(document).on("click", ".system_button_add_delete", function (event) {
            $(this).closest('tr').remove();
        });

    });


</script>
