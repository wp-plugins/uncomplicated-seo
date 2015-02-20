<?php
add_action( 'admin_init', 'uncomplicated_seo_setup' );
add_action('admin_menu', 'uncomplicated_seo_menu');

/*------------
/ Setup
/-----------*/
function uncomplicated_seo_setup(){
    wp_register_style( 'uncomplicatedseostyle', plugins_url('css/useo-style.css', dirname(__FILE__)) );
}

function uncomplicated_seo_menu(){
    $page = add_options_page( 'Uncomplicated SEO', 'Uncomplicated SEO', 'manage_options', 'uncomplicated_seo', 'uncomplicated_seo_options');
    add_action( 'admin_print_styles-' . $page, 'uncomplicated_seo_styles' );
}

function uncomplicated_seo_styles() {
       /*
        * It will be called only on your plugin admin page, enqueue our stylesheet here
        */
       wp_enqueue_style( 'uncomplicatedseostyle' );
}
/*------------
/ End Setup
/-----------*/

/*------------
/ Form
/-----------*/
function uncomplicated_seo_options(){
    if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.', 'uncomplicated_seo' ) );
	}

    $opciones = array('twitter' => '',
						'facebook' => '',
                        'author' => '',
                        'type' => '',
                        'google' => '',
                        'webmastergoogle' => '',
                        'webmasterbing' => '',
                        'metadata' => '',
                        'opengraph' => '',
                        'twittercard' => '',
						'socialicons' => '',
						'headerscripts' =>'',
						'footerscripts' => '',
						'favicon' => '');

    if(isset($_POST['enviar'])){
    
        echo "<h2>" . __('Saved options: ', 'uncomplicated_seo') . "</h2>";
    
        // Recolecting and showing saved options
        $namearray = array( __('Twitter Username', 'uncomplicated_seo'),
							__('Facebook Username', 'uncomplicated_seo'),
                            __('Author', 'uncomplicated_seo'),
                            __('Open Graph Type', 'uncomplicated_seo'),
                            __('Google Publisher', 'uncomplicated_seo'),
                            __('Google Webmaster Verification Tag', 'uncomplicated_seo'),
                            __('Bing Webmaster Verification Tag', 'uncomplicated_seo'),
                            __('Show Meta Tags', 'uncomplicated_seo'),
                            __('Show Open Graph Metadata', 'uncomplicated_seo'),
                            __('Show Twitter Card', 'uncomplicated_seo'),
							__('Show Social Icons', 'uncomplicated_seo'),
							__('Scripts inside head', 'uncomplicated_seo'),
							__('Scripts inside body', 'uncomplicated_seo'),
							__('Favicon', 'uncomplicated_seo')
                            );
        $a = 0;
        foreach($opciones as $id => $valor){
            if(isset($_POST[$id]) && !empty($_POST[$id])){
				if($id == 'headerscripts' or $id == 'footerscripts'){
					$opciones[$id] = stripslashes_deep($_POST[$id]);
					$mostrar = $opciones[$id];
				}else{
					$opciones[$id] = $_POST[$id];
					$mostrar = esc_html($opciones[$id]);
				}
                if($mostrar == "1"){
                    $mostrar = __('Yes', 'uncomplicated_seo');
                }
                echo "<br><b>" . $namearray[$a] . ":</b> " . $mostrar;
            }else{
                $opciones[$id] = '';
            }
            $a++;
        }

        update_option('uncomplicated_seo_saved', $opciones);
    }else{

        $opciones_saved = get_option('uncomplicated_seo_saved');
        
        foreach($opciones as $id => $valor){
            if(!isset($opciones_saved[$id])){
                $opciones_saved[$id] = '';
            }
        }
?>
    <div class="wrap">
        <h1><?php echo __('Uncomplicated SEO Options', 'uncomplicated_seo'); ?></h1>
        <form method="post">
    
            <!-- Open Graph -->
            <div class="caja">
            <div class="form-box-check">
                <label for="opengraph"><?php echo __('Add Open Graph Metadata?', 'uncomplicated_seo'); ?></label>
                <input type="checkbox" name="opengraph" id="opengraph" value='1' <?php if($opciones_saved['opengraph'] == 1){ echo "checked";} ?> />
            </div>
            <div class="form-box">
                <label for="type"><?php echo __('Type for Open Graph Meta for post and pages. (Recomended for blogs and info websites: <em>article</em>)', 'uncomplicated_seo'); ?><br>
                <?php echo __('More info at:', 'uncomplicated_seo'); ?> <a href="https://developers.facebook.com/docs/reference/opengraph" target="_blank">Open Graph Reference Documentation</a></label>
                <input type="text" name="type" id="type" value="<?php echo $opciones_saved["type"]; ?>" />
            </div>
			<div class="form-box">
                <label for="facebook"><?php echo __('Facebook Site (URL)', 'uncomplicated_seo'); ?></label>
                <input type="url" name="facebook" id="facebook" value="<?php echo $opciones_saved["facebook"]; ?>" />
            </div>
            </div>

            <!-- Twitter -->
            <div class="caja">
            <div class="form-box-check">
                <label for="twittercard"><?php echo __('Add Twitter Card?', 'uncomplicated_seo'); ?></label>
                <input type="checkbox" name="twittercard" id="twittercard" value='1' <?php if($opciones_saved['twittercard'] == 1){ echo "checked";} ?> />
            </div>
            <div class="form-box">
                <label for="twitter"><?php echo __('Twitter User: (including @)', 'uncomplicated_seo'); ?></label>
                <input type="text" name="twitter" id="twitter" value="<?php echo $opciones_saved["twitter"]; ?>" />
            </div>
            </div>

            <!-- Meta Tags -->
            <div class="caja">
            <div class="form-box-check">
                <label for="metadata"><?php echo __('Add Meta Data?', 'uncomplicated_seo'); ?></label>
                <input type="checkbox" name="metadata" id="metadata" value='1' <?php if($opciones_saved['metadata'] == 1){ echo "checked";} ?> />
            </div>
            <div class="form-box">
                <label for="author"><?php echo __('Author: (for meta tag)', 'uncomplicated_seo'); ?></label>
                <input type="text" name="author" id="author" value="<?php echo $opciones_saved["author"]; ?>" />
            </div>
            </div>

            <!-- Verification Tags -->
            <div class="caja">
            <div class="form-box">
                <label for="google"><?php echo __('Google+ Publisher: (URL)', 'uncomplicated_seo'); ?></label>
                <input type="text" name="google" id="google" value="<?php echo $opciones_saved["google"]; ?>" />
            </div>
            <div class="form-box">
                <label for="webmastergoogle"><?php echo __('Google Webmaster Verification Code', 'uncomplicated_seo'); ?></label>
                <input type="text" name="webmastergoogle" id="webmastergoogle" value="<?php echo $opciones_saved["webmastergoogle"]; ?>" />
            </div>
            <div class="form-box">
                <label for="webmasterbing"><?php echo __('Bing Webmaster Verification Code', 'uncomplicated_seo'); ?></label>
                <input type="text" name="webmasterbing" id="webmasterbing" value="<?php echo $opciones_saved["webmasterbing"]; ?>" />
            </div>
            </div>

			<!-- Social Icons -->
			<div class="caja">
            <div class="form-box-check">
                <label for="socialicons"><?php echo __('Add Social Sharing Buttons?', 'uncomplicated_seo'); ?></label>
                <input type="checkbox" name="socialicons" id="socialicons" value='1' <?php if($opciones_saved['socialicons'] == 1){ echo "checked";} ?> />
            </div>
			</div>

			<!-- Scripts Inside Head -->
			<div class="caja">
			<div class="form-box">
                <label for="headerscripts"><?php echo __('Scripts that you would like to insert into HEAD.<br>PLEASE, TAKE CARE. DO NOT DO IT IF YOU ARE NOT SURE ABOUT WHAT YOU ARE DOING!!!', 'uncomplicated_seo'); ?></label>
                <textarea rows="5" name="headerscripts" id="headerscripts"><?php echo $opciones_saved["headerscripts"]; ?></textarea>
			</div>

			<!-- Scripts Inside Head -->
			<div class="form-box">
                <label for="footerscripts"><?php echo __('Scripts that you would like to insert before closing BODY.<br>PLEASE, TAKE CARE. DO NOT DO IT IF YOU ARE NOT SURE ABOUT WHAT YOU ARE DOING!!!', 'uncomplicated_seo'); ?></label>
                <textarea rows="5" name="footerscripts" id="footerscripts"><?php echo $opciones_saved["footerscripts"]; ?></textarea>
			</div>
			</div>

			<!-- Favicon -->
			<div class="caja">
			<div class="form-box">
                <label for="favicon"><?php echo __('Favicon (URL)', 'uncomplicated_seo'); ?></label>
                <input type="text" name="favicon" id="favicon" value="<?php echo $opciones_saved["favicon"]; ?>" />
            </div>
			</div>

            <br><input type="submit" id="enviar" name="enviar" />
        </form>
    </div>
<?php
    }
/*------------
/ End Form
/-----------*/
}
?>