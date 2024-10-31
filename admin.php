<?php
add_action('admin_init', 'my_ebay_admin_init');
add_action('admin_menu', 'my_ebay_add_sub_menu_page' );

function my_ebay_admin_init() {
	wp_register_style('my_ebay_back.css', MY_EBAY_PLUGIN_URL . 'back.css');
	wp_enqueue_style('my_ebay_back.css');
}

function my_ebay_admin_configuration() {
  $page_content = "";
  $page_content .= '<div class="my_ebay">';
  $page_content .=  '<h2>'.__("My EBay - Configuration.").'</h2>';
  $data = array();
  if(isset($_POST['submit']) && isset($_POST['ebay_configuration'])){
    $data = $_POST['ebay_configuration'];

    update_option(MY_EBAY_PLUGIN_VAR_NAME, base64_encode(serialize($data)));
    $page_content .= '<div class="announce">'.__("Successfully updated").'</div>';
  } else {
    $data = get_option(MY_EBAY_PLUGIN_VAR_NAME, array());
    if(is_string($data))
      $data = unserialize(base64_decode($data));
  }

  $page_content .=  my_ebay_get_form($data);
  $page_content .= "</div>";

  echo $page_content;
}

function my_ebay_add_sub_menu_page(){
  if ( function_exists('add_submenu_page') )
    add_submenu_page('plugins.php', __('My EBay Configuration'), __('My EBay'), 'manage_options', 'my-ebay-config', 'my_ebay_admin_configuration');
}

function my_ebay_get_form($form_values = array()){
    // Prevent invalid $_POST .
    if(!is_array($form_values))
      exit(__("Invalid form values"));

    $ebay_proxy_language_list = array(
        "en" => "en - GB",
        "es" => "es - ES",
        "fr" => "fr - FR",
        "it" => "it - IT",
        "de" => "de - DE",
    );
    
    $ebay_source = array(
      "com" => "ebay.com",
      "uk"  => "UK",
      "au"  => "AU",
      "de"  => "DE",
      "fr"  => "FR",
      "ca"  => "CA",
      "it"  => "IT",
    );

    $ebay_search_type = array(
      "1"   => "title only",
      "2"   => "title and description",
    );

    $ebay_picture = array(
      "1"   => "Yes",
      "0"   => "No",
    );

    $ebay_sort_design = array(
    "vertical"   => "vertical",
    "horizontal" => "horizontal"
    );

    $ebay_sort_order = array(
    "efirst"     => "Auctions Ending First",
    "hprice"     => "Highest Price First",
    "bmatch"     => "Best Match",
    "nlisted"    => "Newly Listed Auctions",
    "priceaslow" => "Price and Shipping with Lowest First"
    );

    $ebay_auction_type = array(
    "all"         => "All",
    "auctiononly" => "Auction only",
    "binonly"     => "Buy it now only",
    );

    $ret = "";
    $ret .= '<form class="my_ebay" method="post">';
    $ret .= '<div class="field">';
    $ret .=   '<label>'.__("Source Ebay").'</label>';
    $ret .=   my_ebay_generateSelectFromArray($ebay_source, 'ebay_configuration[source]', isset($form_values['source']) ? $form_values['source'] : "");
    $ret .=   '<div class="description">'.__('Source EBay ').'</div>';
    $ret .= '</div>';
    $ret .= '<div class="field">';
    $ret .=   '<label>'.__("Search terms").'</label>';
    $ret .=   '<input type="text" value="%%%tags%%%" name="ebay_configuration[tags]">';
    $ret .=   '<div class="description">'.__('Search tags').'</div>';
    $ret .= '</div>';
    $ret .= '<div class="field">';
    $ret .=   '<label>'.__("Search type").'</label>';
    $ret .=   my_ebay_generateRadioFromArray($ebay_search_type, 'ebay_configuration[searchtype]', isset($form_values['searchtype']) ? $form_values['searchtype'] : "1");
    $ret .=   '<div class="description">'.__('Tags should be searched in title or title and description').'</div>';
    $ret .= '</div>';
    $ret .= '<div class="field">';
    $ret .=   '<label>'.__("Number of auctions").'</label>';
    $ret .=   '<input type="text" value="%%%limitAuction%%%" name="ebay_configuration[limitAuction]">';
    $ret .=   '<div class="description">'.__('Number of auctions').'</div>';
    $ret .= '</div>';
    $ret .= '<div class="field">';
    $ret .=   '<label>'.__("Main category ID").'</label>';
    $ret .=   '<input type="text" value="%%%category%%%" name="ebay_configuration[category]">';
    $ret .=   '<div class="description">'.__('Main category ID (optional)-please refer to your category tree in your country').'</div>';
    $ret .= '</div>';
    $ret .= '<div class="field">';
    $ret .=   '<label>'.__("Maximum of number of characters").'</label>';
    $ret .=   '<input type="text" value="%%%title_length%%%" name="ebay_configuration[title_length]">';
    $ret .=   '<div class="description">'.__('maximum of characters in auction title').'</div>';
    $ret .= '</div>';
    $ret .= '<div class="field">';
    $ret .=   '<label>'.__("Auction Image").'</label>';
    $ret .=   my_ebay_generateRadioFromArray($ebay_picture, 'ebay_configuration[picture]', isset($form_values['picture']) ? $form_values['picture'] : "1");
    $ret .=   '<div class="description">'.__('Show the image associated with each auction').'</div>';
    $ret .= '</div>';
    $ret .= '<div class="field">';
    $ret .=   '<label>'.__("Disposition (2 or 1 column)").'</label>';
    $ret .=   my_ebay_generateSelectFromArray($ebay_sort_design, 'ebay_configuration[sortdesign]', isset($form_values['sortdesign']) ? $form_values['sortdesign'] : "");
    $ret .=   '<div class="description">'.__('horizontal (2 columns) or vertical').'</div>';
    $ret .= '</div>';
    $ret .= '<div class="field">';
    $ret .=   '<label>'.__("Sort order").'</label>';
    $ret .=   my_ebay_generateSelectFromArray($ebay_sort_order, 'ebay_configuration[sortorder]', isset($form_values['sortorder']) ? $form_values['sortorder'] : "");
    $ret .=   '<div class="description">'.__('which order should be used to sort auctions').'</div>';
    $ret .= '</div>';
    $ret .= '<div class="field">';
    $ret .=   '<label>'.__("Type of auction").'</label>';
    $ret .=   my_ebay_generateSelectFromArray($ebay_auction_type, 'ebay_configuration[auctiontype]', isset($form_values['auctiontype']) ? $form_values['auctiontype'] : "");
    $ret .=   '<div class="description">'.__('Which type of auctions in the listing').'</div>';
    $ret .= '</div>';
    $ret .= '<div class="field">';
    $ret .=   '<label>'.__("Display language").'</label>';
    $ret .=   my_ebay_generateSelectFromArray($ebay_proxy_language_list, 'ebay_configuration[proxy_display_language]', isset($form_values['proxy_display_language']) ? $form_values['proxy_display_language'] : "");
    $ret .= '</div>';
    $ret .= '<div class="clear"></div>';
    $ret .= '<input type="submit" name="submit" value="Save"/>';
    $ret .= '</form>';

    $ret = str_replace("%%%tags%%%", isset($form_values['tags']) ? $form_values['tags'] : "", $ret);
    $ret = str_replace("%%%limitAuction%%%", isset($form_values['limitAuction']) ? $form_values['limitAuction'] : "10", $ret);
    $ret = str_replace("%%%category%%%", isset($form_values['category']) ? $form_values['category'] : "", $ret);
    $ret = str_replace("%%%title_length%%%", isset($form_values['title_length']) ? $form_values['title_length'] : "50", $ret);

    return $ret;
}

function my_ebay_generateSelectFromArray($options , $select_name , $selected_option = null){
    $return = "";
    $return .= '<select id="'.$select_name.'" name="'.$select_name.'">';
    foreach($options as $value=>$name){
        $return .= '<option value="'.$value.'"';

        if($value == $selected_option)
            $return .= 'selected="selected"';

        $return .= '>'.$name.'</option>';
    }
    $return .= '</select>';

    return $return;
}

function my_ebay_generateRadioFromArray($options , $radio_name , $selected_option = null){
    $return = "";
    $i = 1;
    $return .= '<div class="radio_group">';
    foreach($options as $value=>$label){
      $return .= '<input id="'.$radio_name.'-'.$i.'" name="'.$radio_name.'" type="radio" value="'.$value.'"';

      if($selected_option == $value)
        $return .= ' checked="checked"';

      $return .= '>';
      $return .= '<label for="'.$radio_name.'-'.$i.'">'.$label.'</label>';
      $i++;
    }
    $return .= '</div>';
    return $return;
}
