<?php
/**
 * The template for displaying Comment form
 */
 
	global $cs_theme_options;
	if ( comments_open() ) {
		if ( post_password_required() ) return;
	}
?>
<?php if ( have_comments() ) : ?>
<div id="cs-comments" class="cs-comments cs-classic-form cs-form-styling">
        
        <div class="cs-section-title"><h2><?php echo comments_number(__('No Comments', 'lassic'), __('1 Comment', 'lassic'), __('% Comments', 'lassic') );?></h2></div>
        <ul>
            <?php wp_list_comments( array( 'callback' => 'cs_comment' ) );	?>
        </ul>
        <?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // Are there comments to navigate through? ?>
            <div class="navigation">
                <div class="nav-previous"><?php previous_comments_link( __( '<span class="meta-nav">&larr;</span> Older Comments', 'lassic') ); ?></div>
                <div class="nav-next"><?php next_comments_link( __( 'Newer Comments <span class="meta-nav">&rarr;</span>', 'lassic') ); ?></div>
            </div> <!-- .navigation -->
        <?php endif; // check for comment navigation ?>
        
        <?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // Are there comments to navigate through? ?>
            <div class="navigation">
                <div class="nav-previous"><?php previous_comments_link( __( '<span class="meta-nav">&larr;</span> Older Comments', 'lassic') ); ?></div>
                <div class="nav-next"><?php next_comments_link( __( 'Newer Comments <span class="meta-nav">&rarr;</span>', 'lassic') ); ?></div>
            </div><!-- .navigation -->
        <?php endif; ?>
    </div>
<?php endif; // end have_comments() ?>
<div id="Form" class="cs-classic-form">
	
		<?php 
        global $post_id;
        $you_may_use = __( 'You may use these <abbr title="HyperText Markup Language">HTML</abbr> tags and attributes: %s', 'lassic');
        $must_login = __( 'You must be <a href="%s">logged in</a> to post a comment.', 'lassic');
        $logged_in_as = __('Logged in as <a href="%1$s">%2$s</a>. <a href="%3$s" title="Log out of this account">Log out?</a>', 'lassic');
        $required_fields_mark = ' ' . __('Required fields are marked %s', 'lassic');
        $required_text = sprintf($required_fields_mark , '<span class="required">*</span>' );

        $defaults = array( 'fields' => apply_filters( 'comment_form_default_fields', 
            array(
                'notes' => '',
                
                'author' => '<p>'.
                '<label class="icon-usr">' .
                ''.( $req ? __( '', 'lassic') : '' ) .'<input placeholder="Name" id="author"  name="author" class="nameinput" type="text" value=""' .
                esc_attr( $commenter['comment_author'] ) . ' tabindex="1"></label>' .
                '</p><!-- #form-section-author .form-section -->',
                
                'email'  => '<p>' .
                '<label class="icon-envlp">' .
                ''.( $req ? __( '', 'lassic') : '' ) .''.
                '<input placeholder="Email" id="email"  name="email" class="emailinput" type="text"  value=""' . 
                esc_attr(  $commenter['comment_author_email'] ) . ' size="30" tabindex="2"></label>' .
                '</p><!-- #form-section-email .form-section -->',
				 
                'url'    => '<p>' .
                '<label class="icon-globe">' .
                '<input  placeholder="Website" id="url" name="url" type="text" class="websiteinput"  value="" size="30" tabindex="3"></label>' .
                '</p><!-- #<span class="hiddenSpellError" pre="">form-section-url</span> .form-section -->' ) ),
				
				 'comment_field' => '<p class="comment-form-comment">'.
                ''.__( '', 'lassic'). ''.( $req ? __( '', 'lassic') : '' ) .'' .
                '<label>' . __( '', 'lassic').'<textarea id="comment_mes" placeholder="Message" name="comment"  class="commenttextarea" rows="4" cols="39"></textarea></label>' .
                '</p><!-- #form-section-comment .form-section -->',

				
                'must_log_in' => '<p class="form-submit-loggedin">' .  sprintf( $must_login,	wp_login_url( apply_filters( 'the_permalink', get_permalink( $post_id ) ) ) ) . '</p>',
                'logged_in_as' => '<p class="form-submit-loggedin">' . sprintf( $logged_in_as, admin_url( 'profile.php' ), $user_identity, wp_logout_url( apply_filters( 'the_permalink', get_permalink( $post_id ) ) ) ).'</p>',
                'comment_notes_before' => '',
                'comment_notes_after' =>  '',
                'class_form' => 'form-style',
                'id_form' => 'contact_formnLb',
				'class_submit' => 'form-style',
                'id_submit' => 'cs-bg-color',
                'title_reply' => __( 'Leave us a Reply', 'lassic' ),
                'title_reply_to' => __( 'Leave a Reply to %s', 'lassic' ),
                'cancel_reply_link' => __( 'Cancel Reply', 'lassic' ),
                'label_submit' => __( 'Submit', 'lassic' ),); 
                comment_form($defaults, $post_id); 
            ?>
    </div>
 
<!-- Col Start -->