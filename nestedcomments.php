<?php

get_header();



 ?>

<script type="text/javascript">
	SideComments.pathData = <?php echo sidecomments_get_path_json($top_comment[0]); ?>;

 jQuery(document).ready(function() {
	SideComments.init();
 });
</script>
<div id="ui-dialog" class="ui-dialog">


</div>
		<div id="container">
			<div id="content" role="main">

					<div class="entry-content">
<div id="sidecomments-slider">
<ul id="sidecomments-path">

</ul>

</div>
						<?php wp_list_comments( array( 'callback' => 'sidecomments_comment' ), $top_comment );  ?>
						<?php if ( get_the_author_meta( 'description', $top_comment[0]->user_id ) ) : // If a user has filled out their description, show a bio on their entries  ?>
											<div id="entry-author-info">
												<div id="author-avatar">
													<?php echo get_avatar( get_the_author_meta( 'user_email', $top_comment[0]->user_id ), apply_filters( 'twentyten_author_bio_avatar_size', 60 ) ); ?>
												</div><!-- #author-avatar -->
												<div id="author-description">
													<h2><?php printf( esc_attr__( 'About %s', 'twentyten' ) , get_the_author_meta('display_name', $top_comment[0]->user_id ) ); ?></h2>
													<?php the_author_meta( 'description', $top_comment[0]->user_id ); ?>
													<div id="author-link">

													</div><!-- #author-link	-->
												</div><!-- #author-description -->
											</div><!-- #entry-author-info -->
						<?php endif; ?>
					</div>

					<hr />

			</div><!-- #content -->
		</div><!-- #container -->
<div id="comments">

<?php wp_list_comments( array( 'callback' => 'sidecomments_comment' ), $child_comments );  ?>

</div>
<div style="clear:both;"> </div>
<div style="display: none">
<?php sidecomments_comment_form(); ?>
</div>

<?php get_footer(); ?>