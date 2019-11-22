<?php

DW_Comments::init();

class DW_Comments {

  public static function init() {

    add_shortcode( 'dwqa_post_comments', 'DW_Comments::post_questions' );

    add_action( 'dwqa_after_show_content_question', 'DW_Comments::embed_parent', 10, 2 );

    add_action( 'dwqa_before_question_submit_button', 'DW_Comments::before_question_submit_button' );
    add_action( 'dwqa_after_insert_question', 'DW_Comments::after_insert_question', 10, 2 );
  }

  /**
   * Adds the referenced post as an oembed to the end of the question
   * 
   * @global type $wp_embed
   * @param type $question_id
   * @return type
   */
  public static function embed_parent( $question_id ) {
    $post_parent_id = get_post_meta( $question_id, '_dwqa_comments_post_id', true );

    if ( empty( $post_parent_id ) ) {
      return;
    }

    global $wp_embed;
    echo apply_filters( 'dwqa_comments_embedded_post', $wp_embed->run_shortcode( '[embed]' . get_the_permalink( $post_parent_id ) . '[/embed]' ), $question_id, $post_parent_id );
  }

  /**
   * Updates the query when used on a singular page / post to include all
   * questions and only those that are from that page / post. 
   * 
   * @param type $qry
   * @return array
   */
  public static function prepare_archive_query( $qry ) {
    if ( is_singular( apply_filters( 'dwqa_comments_post_types', [ 'post', 'page' ] ) ) ) {
      $qry[ 'posts_per_page' ] = -1;
      $qry[ 'meta_query' ] = [ [
              'key' => '_dwqa_comments_post_id',
              'value' => get_the_ID()
          ] ];
    }
    return $qry;
  }

  /**
   * Adds the current post / page ID to the hidden fields so that it can
   * be added to the question post meta later. 
   */
  public static function before_question_submit_button() {

    if ( is_singular( apply_filters( 'dwqa_comments_post_types', [ 'post', 'page' ] ) ) ) {
      echo '<input type="hidden" name="dwqa_comments_post_id" value="' . get_the_ID() . '">';
    }
  }

  /**
   * Adds the post ID that from where this question was submitted. 
   * @param type $question
   */
  public static function after_insert_question( $question_id ) {
    if ( isset( $_POST[ 'dwqa_comments_post_id' ] ) && ! empty( $question_id ) ) {
      update_post_meta( $question_id, '_dwqa_comments_post_id', $_POST[ 'dwqa_comments_post_id' ] );

      if ( function_exists( 'w3tc_flush_post' ) ) {
        /* Need to clear W3TC cache so the question will show up */
        w3tc_flush_post( $_POST[ 'dwqa_comments_post_id' ] );
      }
    }
  }

  /**
   * Shortcode handler
   * 
   * This turns off some of the actions that DW uses by default that don't 
   * really make sense when using this for comments. 
   * 
   * @param type $atts
   * @return type
   */
  public static function post_questions( $atts = [] ) {
    $atts = shortcode_atts( [
        'hide_options' => true,
        'header_level' => 2
            ], $atts );


    add_filter( 'dwqa_prepare_archive_posts', 'DW_Comments::prepare_archive_query', 10, 2 );

    remove_action( 'dwqa_before_questions_archive', 'dwqa_search_form', 11 );
    remove_action( 'dwqa_before_questions_archive', 'dwqa_archive_question_filter_layout', 12 );

    $html = do_shortcode( '[dwqa-list-questions]' );

    $has_questions = true;
    if ( strpos( $html, 'dwqa-question-title' ) === false ) {
      /* There aren't any questions, don't hide the form */
      $html = '';
      $has_questions = false;
    }
    else {
      $html = '<h' . $atts[ 'header_level' ] . '>' . __( 'Questions', 'dwqa-comments' ) . '</h' . $atts[ 'header_level' ] . '>' . $html;
    }

    $form = do_shortcode('[dwqa-submit-question-form]');

    $html .= '<div class="dwqa-question-form-wrapper" style="display:' . ($has_questions  && strpos($form, 'alert-error') === false ? 'none' : '') . ';">';
    $html .= '<h' . $atts[ 'header_level' ] . '>' . __( 'Ask a Question', 'dwqa-comments' ) . '</h' . $atts[ 'header_level' ] . '>';
    $html .= $form;
    $html .= '</div>';
    /* Add CSS to hide the category, tags and privacy for the question */
    if ( $atts[ 'hide_options' ] ) {
      $html .= '<style type="text/css">input[name="question-tag"],select[name="question-status"],select[name="question-category"]{
display: none !important;}label[for="question-tag"],label[for="question-status"],label[for="question-category"]{display:none !important;}</style>';
    }

    $html .= '<script type="text/javascript">jQuery(".dwqa-ask-question a").click(function() { jQuery(".dwqa-question-form-wrapper").show(); return false;});</script>';

    return $html;
  }

}
