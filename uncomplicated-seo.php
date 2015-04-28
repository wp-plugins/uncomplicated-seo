<?php
/**
 * Plugin Name: Uncomplicated SEO
 * Description: Add the most important attributes to your website to have a proper SEO
 * Version: 1.1.9
 * Author: Antonio Sanchez
 * Author URI: http://antsanchez.com
 * Text Domain: uncomplicated-seo
 * Domain Path: uncomplicated-seo
 * License: GPL2 v2.0

    Copyright 2014  Antonio Sanchez (email : antonio@antsanchez.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// Load Domain
load_plugin_textdomain('uncomplicated-seo', false, basename( dirname( __FILE__ ) ) . '/languages' );

// Required files
require_once('inc/meta-box.php');
require_once('inc/form.php');

// Get and Print Saved Options into header
function uncomplicated_seo_print_header(){
    
    /*-----------------------------
    / Retrive General Info
    /-----------------------------*/

    // Creates Options Array
    $opciones = array(  'title' => '',
                        'url' => '',
                        'description' => '',
                        'twitter' => '',
                        'facebook' => '',
                        'author' => '',
                        'type' => '',
                        'fbimage' => '',
                        'published_time' => '',
                        'modified_time' => '',
                        'google' => '',
                        'webmastergoogle' => '',
                        'webmasterbing' => '',
                        'metadata' => '',
                        'opengraph' => '',
                        'twittercard' => '',
                        'socialicons' => '',
                        'image' => '',
                        'headerscripts' => '',
                        'favicon' => '');

    // Makes sure all options exist in $uc_options;
    $uc_options = $opciones; 


    // General Options
    $web_info = get_queried_object();
    $idpost = get_the_ID();
    $uc_options['title'] = wp_title('-', false, 'right');

    // Saved Options
    $saved_options = get_option("uncomplicated_seo_saved");
    if($saved_options){
        $uc_options['twitter'] = esc_attr($saved_options['twitter']);
        $uc_options['post_author'] = esc_url($saved_options['facebook']);
        $uc_options['author'] = esc_attr($saved_options['author']);
        $uc_options['type'] = esc_attr($saved_options['type']);
        $uc_options['metadata'] = esc_attr($saved_options['metadata']);
        $uc_options['google'] = esc_url($saved_options['google']);
        $uc_options['opengraph'] = esc_attr($saved_options['opengraph']);
        $uc_options['twittercard'] = esc_attr($saved_options['twittercard']);
        $uc_options['webmastergoogle'] = esc_attr($saved_options['webmastergoogle']);
        $uc_options['webmasterbing'] = esc_attr($saved_options['webmasterbing']);

        // Use of isset to avoid PHP notices when updating from old plugin versions which didn't include this variable
        if(isset($saved_options['headerscripts'])){
            $uc_options['headerscripts'] = $saved_options['headerscripts'];
        }
        if(isset($saved_options['favicon'])){
            $uc_options['favicon'] = esc_url($saved_options['favicon']);
        }
        if(isset($saved_options['socialicons'])){
            $uc_options['socialicons'] = esc_attr($saved_options['socialicons']);
        }
        if(isset($saved_options['fbimage'])){
            $uc_options['fbimage'] = esc_url($saved_options['fbimage']);
        }
        if(isset($saved_options['description'])){
            $uc_options['description'] = esc_attr($saved_options['description']);
        }
    }

    // Author
    if(empty($uc_options['post_author']) && (is_single() or is_page())){
        $uc_options['post_author'] = get_author_posts_url($web_info->post_author);
    }

    // Post_author
    if(empty($uc_options['post_author']) && (is_single() or is_page())){
        if(empty($uc_options['post_author']) && !empty($uc_options['author'])){
            $uc_options['post_author'] = $uc_options['author'];
        }else{
            $uc_options['post_author'] = get_author_posts_url($web_info->post_author);
        }
    }

    // Featured Image
    if(is_home() || is_front_page()){
        if(!empty($uc_options['fbimage'])){
            $uc_options['image'] = $uc_options['fbimage'];
        }else{
            $uc_options['image'] = get_header_image();
        }
    }else{
        $image = wp_get_attachment_image_src( get_post_thumbnail_id( $idpost, 'full'), 'full');
        $uc_options['image'] = $image[0];
    }

    if(empty($uc_options['image'])){
        if(!empty($uc_options['fbimage'])){
            $uc_options['image'] = $uc_options['fbimage'];
        }else{
            $uc_options['image'] = get_header_image();
        }
    }
    
    if(is_home() || is_front_page()){

        $uc_options['type'] = 'website';
        if(empty($uc_options['description'])){
            $uc_options['description'] = get_bloginfo('description');
        }
        $uc_options['url'] = get_bloginfo('url');

    }else if(is_category()){

        $uc_options['type'] = 'website';
        $categories = get_terms( 'category' );
        foreach($categories as $valor){
            if(is_category($valor->name)){
                $id = $valor->name;
                $id = get_cat_id($id);
            }
        }
        $uc_options['description'] = sanitize_text_field(category_description($id));
        if(empty($uc_options['description'])){
            $uc_options['description'] = get_bloginfo('description');
        }
        
        $uc_options['url'] = get_category_link($id);

    }else if(is_tag()){

        $uc_options['type'] = 'website';
        $categories = get_terms( 'post_tag' );
        foreach($categories as $valor){
            if(is_tag($valor->name)){
                $id = $valor->term_id;
            }
        }
        $uc_options['description'] = sanitize_text_field(tag_description($id));
        if(empty($uc_options['description'])){
            $uc_options['description'] = get_bloginfo('description');
        }
        
        $uc_options['url'] = get_tag_link($id);

    }else{
        $uc_options['description'] = get_post_meta($idpost, 'uncomplicated_seo_post_class', true);
        $uc_options['url'] = get_permalink();
        if(is_single()){
            $uc_options['published_time'] = date('c', strtotime($web_info->post_date_gmt));
            $uc_options['modified_time'] = date('c', strtotime($web_info->post_modified_gmt));
        }
        
        if(empty($uc_options['description'])){
            if(!empty($web_info->post_excerpt)){
                $uc_options['description'] = $web_info->post_excerpt;
            }else{
                if(empty($uc_options['description'])){
                    $uc_options['description'] = get_bloginfo('description');
                }
            }
        }

        if(empty($uc_options['url'])){
            $uc_options['url'] = get_bloginfo('url');
        }

        if(empty($uc_options['type'])){
            $uc_options['type'] = "article";
        }
    }

    /*-------------------------
    / Printing Functions
    /-------------------------*/

    echo "<!-- Uncomplicated SEO WordPress Plugin -->\n";

    /*if(!empty($uc_options['url'])){
        $url = $uc_options['url'];
        echo "<link rel='canonical' href='$url' />\n";
    }*/

    if(!empty($uc_options['favicon'])){
        $content = $uc_options['favicon'];
        echo "<link rel='icon' href='$content'>\n";
    }

    if(!empty($uc_options['google'])){
        $publisher = $uc_options['google'];
        echo "<link href='$publisher' rel='publisher' />\n";;
    }

    if(!empty($uc_options['webmastergoogle'])){
        $content = $uc_options['webmastergoogle'];
        echo "<meta name='google-site-verification' content='$content' />\n";
    }

    if(!empty($uc_options['webmasterbing'])){
        $content = $uc_options['webmasterbing'];
        echo "<meta name='msvalidate.01' content='$content' />\n";
    }
    
    // Meta Tags Printing Function
    if($uc_options["metadata"] == '1'){
        if($uc_options['post_author']){
             uncomplicated_seo_meta_tags($uc_options['description'],
                                    $uc_options['post_author']);
        }else{
            uncomplicated_seo_meta_tags($uc_options['description'],
                                    $uc_options['author']);
        }
    }

    // Open Graph Metadata Printing Function
    if($uc_options["opengraph"] == '1'){
        uncomplicated_seo_open_graph($uc_options['title'],
                                     $uc_options['description'],
                                     $uc_options['type'],
                                     $uc_options['image'],
                                     $uc_options['url'],
                                     $uc_options['image'],
                                     $uc_options['post_author'],
                                     $uc_options['published_time'],
                                     $uc_options['modified_time']);
    }

    // Twitter Card Printing Function
    if($uc_options["twittercard"] == '1'){
        uncomplicated_seo_twitter_card( $uc_options['twitter'],
                                        $uc_options['title'],
                                        $uc_options['description'],
                                        $uc_options['url'],
                                        $uc_options['image']);
    }

    if($uc_options["socialicons"] == '1' && is_single()){
        echo "<style>
        #uc-seo-list{
            list-style:none;
            margin: 0;
            padding: 0;
        }
        #uc-seo-list li{
            float: left;
            height: 74px;
            margin: 0.5em 1em;
            overflown: hidden;
        }
        </style>";
        echo "<script>(function(d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) return;
            js = d.createElement(s); js.id = id;
            js.src = '//connect.facebook.net/es_LA/sdk.js#xfbml=1&version=v2.0';
            fjs.parentNode.insertBefore(js, fjs);
          }(document, 'script', 'facebook-jssdk'));
          </script>
          <script type='text/javascript'>
            (function() {
              var li = document.createElement('script'); li.type = 'text/javascript'; li.async = true;
              li.src = ('https:' == document.location.protocol ? 'https:' : 'http:') + '//platform.stumbleupon.com/1/widgets.js';
              var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(li, s);
            })();
          </script><script src='https://apis.google.com/js/platform.js' async defer>{lang: 'es'}</script>";
        add_filter('the_content', 'ep_after_content');
    }
   
    // Google Analytics Script
    if(!empty($uc_options['headerscripts'])){
        echo htmlspecialchars_decode($uc_options['headerscripts']);
    }

    
}
add_action('wp_head', 'uncomplicated_seo_print_header');

// Get a print saved options into footer
function uncomplicated_seo_print_footer(){
    $saved_options = get_option("uncomplicated_seo_saved");
    if(isset($saved_options['footerscripts'])){
        echo htmlspecialchars_decode($saved_options['footerscripts']);
    }
}
add_action('wp_footer', 'uncomplicated_seo_print_footer');

/*------------------------------------
/ Printing Functions
/------------------------------------*/

// Print Meta Tags
function uncomplicated_seo_meta_tags($description, $author){
    
    echo "<meta name='description' content='$description' />\n";
    echo "<meta name='author' content='$author' />\n";
}

// Print Open Graph Metadata
function uncomplicated_seo_open_graph($title, $description, $type, $image, $url, $image, $post_author, $published_time, $modified_time){
   
    echo "<meta property='og:title' content='$title' />\n";
    echo "<meta property='og:description' content='$description' />\n";
    echo "<meta property='og:type' content='$type' />\n";
    echo "<meta property='og:image' content='$image' />\n";
    echo "<meta property='og:url' content='$url' />\n";

    if ( is_single() ){
        echo "<meta property='article:author' content='$post_author' />\n";
        echo "<meta property='article:published_time' content='$published_time' />\n";
        echo "<meta property='article:modified_time' content='$modified_time' />\n";
    }
}

// Print Sumary Twitter Card
function uncomplicated_seo_twitter_card($twitter_user, $title, $description, $url, $image){

    echo "<meta name='twitter:card' content='summary' />\n";
    echo "<meta name='twitter:site' content='$twitter_user' />\n";
    echo "<meta name='twitter:title' content='$title' />\n";
    echo "<meta name='twitter:description' content='$description' />\n";
    echo "<meta name='twitter:image' content='$image' />\n";
    echo "<meta name='twitter:url' concept='$url' />\n";
}

// Print Social Icons
function ep_after_content( $content ) {
	global $post;
  
  	if( $post && $post->post_type == 'post' && is_singular( 'post' ) && is_main_query() ) {
  		$id = $post->ID;

        $saved_options = get_option("uncomplicated_seo_saved");
        $twitter_user = esc_attr($saved_options['twitter']);
        $twitter_user = preg_replace("/@/", "", $twitter_user);
        $link = get_permalink($id);
		ob_start(); ?>

        <div id="fb-root"></div>

		<div style="width:100%;float:left;">
			<ul id="uc-seo-list">
                <li>
               <a href="https://twitter.com/share" class="twitter-share-button" data-via="<?php echo $twitter_user; ?>" data-size="large" data-count="none">Tweet</a>
    <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
                </li>            
                <li><div class="fb-share-button" data-href="<?php echo $link; ?>" data-layout="button_count"></div></li>
                <li><div class="g-plusone" data-annotation="none"></div></li>
                <li><a href="//www.reddit.com/submit" onclick="window.location = '//www.reddit.com/submit?url=' + encodeURIComponent(window.location); return false"> <img src="//www.redditstatic.com/spreddit7.gif" alt="submit to reddit" border="0" /></a></li>
                <li><su:badge layout="2"></su:badge></li>
            </ul>
		</div>

		<?php $content .= ob_get_clean();
	}
	return $content;
}

?>