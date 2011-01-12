<?php
/**
 * The Template for displaying all single posts.
 *
 * @package WordPress
 * @subpackage Twenty_Ten
 * @since Twenty Ten 1.0
 */


//print_r($wp_query);
//$count = sidecomments_get_child_count(1736);
//print_r( $count );
//die();

if(isset($_GET['comment'])) {




//$comments = get_comments( array('post_id' => $wp_query->post->ID, 'status' => 'approve', 'order' => 'ASC') );
//$top_comment = get_comments( array('ID' => '1733' , 'status' => 'approve', 'order' => 'ASC') );
$top_comment = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->comments WHERE comment_ID = %d", $_GET['comment']));
$child_comments = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->comments WHERE comment_parent = %d ORDER BY comment_date DESC" .
		"" , $_GET['comment']));


require(STYLESHEETPATH . '/' . 'nestedcomments.php');


die();

}

$comments = get_comments(array('post_id'=> $wp_query->post->ID) );
foreach($comments as $i=>$comment) {
	if ($comment->comment_parent != 0 ) {
		unset($comments[$i]);
	}
}

$sidecomments_is_post = true;
get_header(); ?>

<script type="text/javascript">
	SideComments.pathData = <?php echo sidecomments_get_path_json($top_comment[0]); ?>;
</script>


		<div id="container">

			<div id="content" role="main">

<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

				<div id="nav-above" class="navigation">
					<div class="nav-previous"><?php previous_post_link( '%link', '<span class="meta-nav">' . _x( '&larr;', 'Previous post link', 'twentyten' ) . '</span> %title' ); ?></div>
					<div class="nav-next"><?php next_post_link( '%link', '%title <span class="meta-nav">' . _x( '&rarr;', 'Next post link', 'twentyten' ) . '</span>' ); ?></div>
				</div><!-- #nav-above -->

				<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<h1 class="entry-title"><?php the_title(); ?></h1>

					<div class="entry-meta">
						<?php twentyten_posted_on(); ?>
					</div><!-- .entry-meta -->

					<div class="entry-content">



						<?php the_content(); ?>
						<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'twentyten' ), 'after' => '</div>' ) ); ?>
					</div><!-- .entry-content -->

<?php if ( get_the_author_meta( 'description' ) ) : // If a user has filled out their description, show a bio on their entries  ?>
					<div id="entry-author-info">
						<div id="author-avatar">
							<?php echo get_avatar( get_the_author_meta( 'user_email' ), apply_filters( 'twentyten_author_bio_avatar_size', 60 ) ); ?>
						</div><!-- #author-avatar -->
						<div id="author-description">
							<h2><?php printf( esc_attr__( 'About %s', 'twentyten' ), get_the_author() ); ?></h2>
							<?php the_author_meta( 'description' ); ?>
							<div id="author-link">

							</div><!-- #author-link	-->
						</div><!-- #author-description -->
					</div><!-- #entry-author-info -->
<?php endif; ?>

					<div class="entry-utility">
						<?php twentyten_posted_in(); ?>
						<?php edit_post_link( __( 'Edit', 'twentyten' ), '<span class="edit-link">', '</span>' ); ?>
					</div><!-- .entry-utility -->
				</div><!-- #post-## -->

				<div id="nav-below" class="navigation">
					<div class="nav-previous"><?php previous_post_link( '%link', '<span class="meta-nav">' . _x( '&larr;', 'Previous post link', 'twentyten' ) . '</span> %title' ); ?></div>
					<div class="nav-next"><?php next_post_link( '%link', '%title <span class="meta-nav">' . _x( '&rarr;', 'Next post link', 'twentyten' ) . '</span>' ); ?></div>
				</div><!-- #nav-below -->



<?php endwhile; // end of the loop. ?>

			</div><!-- #content -->
		</div><!-- #container -->
<div id="comments">

<?php wp_list_comments( array( 'callback' => 'sidecomments_comment' ), $comments );  ?>

</div>
<div style="clear:both;"> </div>
<?php comment_form(); ?>
<?php get_footer(); ?>
