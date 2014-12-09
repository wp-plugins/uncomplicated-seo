<?php
/*------------------------------------
/ Uncomplicated SEO plugin
/ Add Metabox and saves the info
/------------------------------------*/

// Add the Custom meta box
function uncomplicated_seo_post_meta_boxes_setup() {

  /* Add meta boxes on the 'add_meta_boxes' hook. */
  add_action( 'add_meta_boxes', 'uncomplicated_seo_add_post_meta_boxes' );

  /* Save post meta on the 'save_post' hook. */
  add_action( 'save_post', 'uncomplicated_seo_save_post_class_meta', 10, 2 );
}

// Clase de la metabox
function uncomplicated_seo_add_post_meta_boxes() {

  add_meta_box(
    'uncomplicated-seo-box',      // Unique ID
    esc_html__( 'SEO Metatag Description', 'example' ),    // Title
    'uncomplicated_seo_post_class_meta_box',   // Callback function
    'post',         // Admin page (or post type)
    'normal',         // Context
    'high'         // Priority
  );

  add_meta_box(
    'uncomplicated-seo-box',      // Unique ID
    esc_html__( 'SEO Metatag Description', 'example' ),    // Title
    'uncomplicated_seo_post_class_meta_box',   // Callback function
    'page',         // Admin page (or post type)
    'normal',         // Context
    'high'         // Priority
  );
}

// HTML of the meta box
function uncomplicated_seo_post_class_meta_box( $object, $box ) { ?>

  <?php wp_nonce_field( basename( __FILE__ ), 'uncomplicated_seo_post_class_nonce' ); ?>

  <p>
    <label for="uncomplicated-seo-post-class"><?php _e( "Add a description for the metatag. It is best to keep meta descriptions between 150 and 160 characters. It should contain some of the most relevants keywords for the post.", 'uncomplicated-seo' ); ?></label>
    <br />
    <textarea style="width: 99%;" name="uncomplicated-seo-post-class" id="uncomplicated-seo-post-class"><?php echo esc_textarea( get_post_meta( $object->ID, 'uncomplicated_seo_post_class', true ) ); ?></textarea>
  </p>

<?php }


add_action( 'load-post.php', 'uncomplicated_seo_post_meta_boxes_setup' );
add_action( 'load-post-new.php', 'uncomplicated_seo_post_meta_boxes_setup' );

/* Save the meta box's post metadata. */
function uncomplicated_seo_save_post_class_meta( $post_id, $post ) {

  /* Verify the nonce before proceeding. */
  if ( !isset( $_POST['uncomplicated_seo_post_class_nonce'] ) || !wp_verify_nonce( $_POST['uncomplicated_seo_post_class_nonce'], basename( __FILE__ ) ) )
    return $post_id;

  /* Get the post type object. */
  $post_type = get_post_type_object( $post->post_type );

  /* Check if the current user has permission to edit the post. */
  if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
    return $post_id;

  /* Get the posted data and sanitize it for use as an HTML class. */
  $new_meta_value = ( isset( $_POST['uncomplicated-seo-post-class'] ) ?  esc_textarea($_POST['uncomplicated-seo-post-class']) : '' );

  /* Get the meta key. */
  $meta_key = 'uncomplicated_seo_post_class';

  /* Get the meta value of the custom field key. */
  $meta_value = get_post_meta( $post_id, $meta_key, true );

  /* If a new meta value was added and there was no previous value, add it. */
  if ( $new_meta_value && '' == $meta_value )
    add_post_meta( $post_id, $meta_key, $new_meta_value, true );

  /* If the new meta value does not match the old value, update it. */
  elseif ( $new_meta_value && $new_meta_value != $meta_value )
    update_post_meta( $post_id, $meta_key, $new_meta_value );

  /* If there is no new meta value but an old value exists, delete it. */
  elseif ( '' == $new_meta_value && $meta_value )
    delete_post_meta( $post_id, $meta_key, $meta_value );
}

add_filter( 'post_class', 'uncomplicated_seo_post_class' );

function uncomplicated_seo_post_class( $classes ) {

  /* Get the current post ID. */
  $post_id = get_the_ID();

  /* If we have a post ID, proceed. */
  if ( !empty( $post_id ) ) {

    /* Get the custom post class. */
    $post_class = get_post_meta( $post_id, 'uncomplicated_seo_post_class', true );

    /* If a post class was input, sanitize it and add it to the post class array. */
    if ( !empty( $post_class ) )
      $classes[] = sanitize_html_class( $post_class );
  }

  return $classes;
}
?>