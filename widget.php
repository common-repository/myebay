<?php
/**
 * @package EBay Plugins
 */

function widget_my_ebay_register() {
  function widget_my_ebay($args) {
      $params = get_option(MY_EBAY_PLUGIN_VAR_NAME, array());
      if(is_string($params))
        $params = unserialize(base64_decode($params));
    
      if(!is_array($params) || empty($params)){
        echo __("My EBay is not properly configured");
        return;
      }
      if(trim($params['tags']) == ""){
        echo __("Missing Search tags...");
        return;
      }
      $content = '<div id="my_ebay">';

      if(isset($params['intro']) && trim($params['intro']) != "")
        $content .= "<p>".str_replace(array("\r\n", "\n"),"<br>",$params['intro'])."</p>";
   
      $content .=  my_ebay_generate_javascript($params);
      $content .= '</div>';
      echo $content;
  }

  function my_ebay_generate_javascript($params) {
    $callUrl = my_ebay_get_proxy_url();

    $resp = array(
        'requestType'      => 'MyEBay',
        'satitle'          => str_replace(' ', '+', $params['tags']),
        'fts'              => isset($params['searchtype']) ? $params['searchtype'] : '1',
        'source'           => isset($params['source']) ? $params['source'] : '',
        'auctiontype'      => isset($params['auctiontype']) ? $params['auctiontype'] : 'all',
        'sortorder'        => isset($params['sortorder']) ? $params['sortorder'] : 'efirst',
        'sortdesign'       => isset($params['sortdesign']) ? $params['sortdesign'] : 'vertical',
        'limitAuction'     => isset($params['limitAuction']) ? $params['limitAuction'] : '10',
        'title_length'     => isset($params['title_length']) ? $params['title_length'] : '50',
        'picture'          => isset($params['picture']) ? $params['picture'] : '0',
        'proxy_display_language'  => isset($params['proxy_display_language']) ? $params['proxy_display_language'] : 'en',
    );

    $category = isset($params['category']) ? $params['category'] : false;
    if ($category){
      $resp['sacat'] = $category;
    }
    
    $first = true;
    foreach ($resp as $key=>$param){
        if($first){
           $first = false;
           $callUrl .= $key . '=' . $param;
        } else
           $callUrl .= '&' . $key . '=' . $param;
    }

    return '<script type="text/javascript" src="'.$callUrl.'"></script>';
  }

  function widget_my_ebay_control(){
      $content = "";
      $content .= __('Please configure your widget from');
      $content .= ': <a href="plugins.php?page=my-ebay-config">';
      $content .= __("here");
      $content .= '</a>';

      echo $content;
  }

  function widget_my_ebay_include_css(){
      echo '<style type="text/css">'.file_get_contents(MY_EBAY_PLUGIN_URL."front.css").'</style>';
  }

  if(function_exists('register_sidebar_widget') ){
    if(function_exists('wp_register_sidebar_widget')){
      wp_register_sidebar_widget( 'my_ebay', 'My EBay', 'widget_my_ebay', null, 'my_ebay');
      wp_register_widget_control( 'my_ebay', 'My EBay', 'widget_my_ebay_control', null, 75, 'my_ebay');
    }elseif(function_exists('register_sidebar_widget')){
      register_sidebar_widget('My Ebay', 'widget_my_ebay', null, 'my_ebay');
      register_widget_control('My Ebay', 'widget_my_ebay_control', null, 75, 'my_ebay');
    }
  }
  
  if(is_active_widget('widget_my_ebay'))
    add_action('wp_head', 'widget_my_ebay_include_css');

}

add_action('init', 'widget_my_ebay_register');

