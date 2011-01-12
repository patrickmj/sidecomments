<?php

if ( ! function_exists( 'sidecomments_comment' ) ) {

	function sidecomments_comment( $comment, $args, $depth ) {
		$GLOBALS['comment'] = $comment;
		global $sidecomments_is_post;
		global $top_comment;
		switch ( $comment->comment_type ) :
			case '' :
		?>
		<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
			<div id="comment-<?php comment_ID(); ?>">
			<div class="comment-author">
				<?php echo get_avatar( $comment, 40 ); ?>

					<a href="<?php echo esc_url( sidecomments_get_focus_link( $comment ) ); ?>"><?php echo implode(" " ,array_slice(preg_split("/\s+/", $comment->comment_content), 0, 2) ) . "..."; ?></a>


			</div><!-- .comment-author .vcard -->
			<?php if ( $comment->comment_approved == '0' ) : ?>
				<em><?php _e( 'Your comment is awaiting moderation.', 'twentyten' ); ?></em>
				<br />
			<?php endif; ?>

			<div class="comment-meta commentmetadata">

				<?php if  ($comment->comment_ID == $top_comment[0]->comment_ID)  :?>
					<a href="<?php echo esc_url( sidecomments_get_up_link( $comment ) ); ?>">UP</a>
				<?php endif; ?>
				<span class="sidecomments_child_count"><?php echo sidecomments_get_child_count($comment->comment_ID); ?> replies</span>
				<?php edit_comment_link( __( '(Edit)', 'twentyten' ), ' ' ); ?>
			</div><!-- .comment-meta .commentmetadata -->

			<div class="comment-body"><?php comment_text(); ?></div>
			<?php if (true) :  //if ($comment->comment_ID == $top_comment[0]->comment_ID) : ?>
			<div class="reply">
				<?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
			</div><!-- .reply -->
			<?php  endif; ?>
		</div><!-- #comment-##  -->

		<?php
				break;
			case 'pingback'  :
			case 'trackback' :
		?>
		<li class="post pingback">
			<p><?php _e( 'Pingback:', 'twentyten' ); ?> <?php comment_author_link(); ?><?php edit_comment_link( __('(Edit)', 'twentyten'), ' ' ); ?></p>
		<?php
				break;
		endswitch;
	}
}


function sidecomments_get_up_link( $comment = null, $args = array() ) {

	$post_link = get_permalink( $comment->comment_post_ID );

	if($comment->comment_parent != 0) {
		return apply_filters( 'get_comment_link', $post_link . '?comment=' . $comment->comment_parent, $comment, $args );

	}
	return apply_filters( 'get_comment_link', $post_link );

}
function sidecomments_get_focus_link( $comment = null, $args = array() ) {

	$post_link = get_permalink( $comment->comment_post_ID );

	return apply_filters( 'get_comment_link', $post_link . '?comment=' . $comment->comment_ID, $comment, $args );

}

function sidecomments_get_child_count($comment_id) {
	global $wpdb;
	$result = $wpdb->get_results($wpdb->prepare("SELECT COUNT(*) FROM $wpdb->comments WHERE comment_parent = %d", $comment_id));
	$COUNT = 'COUNT(*)';
	return $result[0]->$COUNT;
}

function sidecomments_get_path_json($comment) {
	global $wpdb;
	$depth = 0;

	$result = $wpdb->get_results($wpdb->prepare("SELECT post_title, post_content, guid FROM $wpdb->posts WHERE ID = %d", $comment->comment_post_ID));

	$newObj = new StdClass();
	$newObj->id = $comment->comment_post_ID;
	$newObj->title =  $result[0]->post_title ;
	$newObj->link =  "<a href='" . $result[0]->guid . "'>" . $result[0]->post_title . "</a>";
	$newObj->content = $result[0]->post_content;
	$path[] = $newObj;



	$parent = $comment;

	while ($parent->comment_parent != 0) {
		$parent = get_comment($parent->comment_parent);
		$newObj = new StdClass();
		$newObj->id = $parent->comment_ID;
		$newObj->title =  implode(" ", array_slice(preg_split("/\s+/", $parent->comment_content), 0, 2)) . "...";
		$newObj->link = "<a href='?comment=$newObj->id'>" . $newObj->title  . "</a>";
		$newObj->content = $parent->comment_content;
		$path[] = $newObj;
	}

	$newObj = new StdClass();
	$newObj->id = $comment->comment_ID;
	$newObj->title = implode(" ", array_slice(preg_split("/\s+/", $comment->comment_content), 0, 2)) . "...";
	$newObj->link = "<a href='?comment=$newObj->id'>" . $newObj->title  . "</a>";
	$newObj->content = 	$comment->comment_content;

	$path[] = $newObj;
	return json_encode($path);

}


function sidecomments_comment_form( $args = array(), $post_id = null ) {
	global $user_identity, $id;

	if ( null === $post_id )
		$post_id = $id;
	else
		$id = $post_id;

	$commenter = wp_get_current_commenter();

	$req = get_option( 'require_name_email' );
	$aria_req = ( $req ? " aria-required='true'" : '' );
	$fields =  array(
		'author' => '<p class="comment-form-author">' . '<label for="author">' . __( 'Name' ) . '</label> ' . ( $req ? '<span class="required">*</span>' : '' ) .
		            '<input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30"' . $aria_req . ' /></p>',
		'email'  => '<p class="comment-form-email"><label for="email">' . __( 'Email' ) . '</label> ' . ( $req ? '<span class="required">*</span>' : '' ) .
		            '<input id="email" name="email" type="text" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" size="30"' . $aria_req . ' /></p>',
		'url'    => '<p class="comment-form-url"><label for="url">' . __( 'Website' ) . '</label>' .
		            '<input id="url" name="url" type="text" value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="30" /></p>',
	);

	$required_text = sprintf( ' ' . __('Required fields are marked %s'), '<span class="required">*</span>' );
	$defaults = array(
		'fields'               => apply_filters( 'comment_form_default_fields', $fields ),
		'comment_field'        => '<p class="comment-form-comment"><label for="comment">' . _x( 'Comment', 'noun' ) . '</label><textarea id="comment" name="comment" cols="45" rows="8" aria-required="true"></textarea></p>',
		'must_log_in'          => '<p class="must-log-in">' .  sprintf( __( 'You must be <a href="%s">logged in</a> to post a comment.' ), wp_login_url( apply_filters( 'the_permalink', get_permalink( $post_id ) ) ) ) . '</p>',
		'logged_in_as'         => '<p class="logged-in-as">' . sprintf( __( 'Logged in as <a href="%1$s">%2$s</a>. <a href="%3$s" title="Log out of this account">Log out?</a>' ), admin_url( 'profile.php' ), $user_identity, wp_logout_url( apply_filters( 'the_permalink', get_permalink( $post_id ) ) ) ) . '</p>',
		'comment_notes_before' => '<p class="comment-notes">' . __( 'Your email address will not be published.' ) . ( $req ? $required_text : '' ) . '</p>',
		'comment_notes_after'  => '<p class="form-allowed-tags">' . sprintf( __( 'You may use these <abbr title="HyperText Markup Language">HTML</abbr> tags and attributes: %s' ), ' <code>' . allowed_tags() . '</code>' ) . '</p>',
		'id_form'              => 'commentform',
		'id_submit'            => 'submit',
		'title_reply'          => __( 'Leave a Reply' ),
		'title_reply_to'       => __( 'Leave a Reply to %s' ),
		'cancel_reply_link'    => __( 'Cancel reply' ),
		'label_submit'         => __( 'Post Comment' ),
	);

	$args = wp_parse_args( $args, apply_filters( 'comment_form_defaults', $defaults ) );

	?>
		<?php if ( comments_open() ) : ?>
			<?php do_action( 'comment_form_before' ); ?>
			<div id="respond">
				<h3 id="reply-title"><?php comment_form_title( $args['title_reply'], $args['title_reply_to'] ); ?> <small><?php cancel_comment_reply_link( $args['cancel_reply_link'] ); ?></small></h3>
				<?php if ( get_option( 'comment_registration' ) && !is_user_logged_in() ) : ?>
					<?php echo $args['must_log_in']; ?>
					<?php do_action( 'comment_form_must_log_in_after' ); ?>
				<?php else : ?>
					<form action="<?php echo site_url( '/wp-comments-post.php' ); ?>" method="post" id="<?php echo esc_attr( $args['id_form'] ); ?>">
					<?php
						$redirect = $_SERVER['HTTP_HOST']  . $_SERVER['REQUEST_URI'];
					?>

						<input type="hidden" name="redirect_to" value="<?php echo 'http://' . $redirect ?>"  />
						<?php do_action( 'comment_form_top' ); ?>
						<?php if ( is_user_logged_in() ) : ?>
							<?php echo apply_filters( 'comment_form_logged_in', $args['logged_in_as'], $commenter, $user_identity ); ?>
							<?php do_action( 'comment_form_logged_in_after', $commenter, $user_identity ); ?>
						<?php else : ?>
							<?php echo $args['comment_notes_before']; ?>
							<?php
							do_action( 'comment_form_before_fields' );
							foreach ( (array) $args['fields'] as $name => $field ) {
								echo apply_filters( "comment_form_field_{$name}", $field ) . "\n";
							}
							do_action( 'comment_form_after_fields' );
							?>
						<?php endif; ?>
						<?php echo apply_filters( 'comment_form_field_comment', $args['comment_field'] ); ?>
						<?php echo $args['comment_notes_after']; ?>
						<p class="form-submit">
							<input name="submit" type="submit" id="<?php echo esc_attr( $args['id_submit'] ); ?>" value="<?php echo esc_attr( $args['label_submit'] ); ?>" />
							<?php comment_id_fields(); ?>
						</p>
						<?php do_action( 'comment_form', $post_id ); ?>
					</form>
				<?php endif; ?>
			</div><!-- #respond -->
			<?php do_action( 'comment_form_after' ); ?>
		<?php else : ?>
			<?php do_action( 'comment_form_comments_closed' ); ?>
		<?php endif; ?>
	<?php
}

function sidecomments_add_js() {
	$url = get_bloginfo('stylesheet_directory');
	echo "<script type='text/javascript' src='" . $url . DIRECTORY_SEPARATOR . "js" . DIRECTORY_SEPARATOR . "sidecomments.js' > </script>";
}

add_action('wp_head', 'sidecomments_add_js');


?>
