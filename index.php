<?php 
  
/*
Plugin Name: Похожие записи
Plugin URI: http://страница_с_описанием_плагина_и_его_обновлений
Description: Краткое описание плагина.
Version: Номер версии плагина, например: 1.0
Author: Имя автора плагина
Author URI: http://страница_автора_плагина
*/

add_filter('the_content', 'show_recent');
add_action( 'wp_enqueue_scripts', 'spl_register_styles_scripts' );

function spl_register_styles_scripts() {
  wp_register_script( 'spl_recent_main_script', plugins_url('assets/js/spl_recent_main.js', __FILE__), array ('jquery') );
  wp_register_style( 'spl_recent_style', plugins_url('assets/css/spl_recent_main.css', __FILE__) );

  wp_enqueue_script( 'spl_recent_main_script' );
  wp_enqueue_style( 'spl_recent_style' );
}

function show_recent ($content) {
  
  if (!is_single() ) {
    return $content;
  }

  $id = get_the_ID();
  $categories = get_the_category( $id );


  foreach ($categories as $category) {
    $cats_id[] = $category->cat_ID;
  }

  $related_posts = new WP_Query ( 
    array (
      'posts_per_page' => 5,
      'category__in'   => $cats_id,
      'orderby'       => 'rand',
      'post__not_in'   => array ($id)
    )
  );

  if ( $related_posts->have_posts() ) {
    $content .= '<div class="spl-recent-posts"><h3>Возможно вас заинтересуют эти записи:</h3>';

    while ( $related_posts->have_posts() ) {
      $related_posts->the_post();
      
      if ( has_post_thumbnail() ) {
        $img =  get_the_post_thumbnail( get_the_ID(), array (100, 100), array ('alt' => get_the_title() ) );
      } else {
        $img = '<img src="'.plugins_url('assets/img/thumbnail-no-image.jpg', __FILE__).'" alt="'.get_the_title().'" width="100" height="100">';
      }

      $content .= '<a class="spl-recent-plugin-link" href="'.get_permalink().'">'.$img.'<div class="spl-recent-tool-box">'.get_the_title().'</div></a>';
    }

    $content .= '</div';
    wp_reset_query();
  }
  return $content;
}