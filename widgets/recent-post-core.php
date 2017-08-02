<?php 

class Recent_Post_Widget_Thumbnail extends WP_Widget {
 
	/**
	 * Sets up a new Recent Posts widget instance.
	 *
	 * @since 2.8.0
	 * @access public
	 */
	function __construct() {
		$widget_rpwt = array('classname' => 'rpw_thumbnail', 'description' => __( "Latest Post with Thumbnails") );
		parent::__construct('rpwt', __('Recent Posts with Thumbnail'), $widget_rpwt);
		$this->alt_option_name = 'rpw_thumbnail';
	}
 
	/**
	 * Outputs the content for the current Recent Posts widget instance.
	 *
	 * @since 2.8.0
	 * @access public
	 *
	 * @param array $args     Display arguments including 'before_title', 'after_title',
	 *                        'before_widget', and 'after_widget'.
	 * @param array $instance Settings for the current Recent Posts widget instance.
	 */
	function widget( $args, $instance ) {
		if ( ! isset( $args['widget_id'] ) ) {
			$args['widget_id'] = $this->id;
		}
 
		$title = ( ! empty( $instance['title'] ) ) ? $instance['title'] : __( 'Recent Posts' );
 
		/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );
 
		$number = ( ! empty( $instance['number'] ) ) ? absint( $instance['number'] ) : 5;
		if ( ! $number )
			$number = 5;
		$show_date = isset( $instance['show_date'] ) ? $instance['show_date'] : false;
		$show_excerpt = isset( $instance['show_excerpt'] ) ? $instance['show_excerpt'] : false;
		$show_thumbnail = isset( $instance['show_thumbnail'] ) ? $instance['show_thumbnail'] : false;
		$excerpt_length = ( ! empty( $instance['excerpt_length'] ) ) ? absint( $instance['excerpt_length'] ) : 7;
		if ( ! $excerpt_length )
			$excerpt_length = 7;
 
		/**
		 * Filter the arguments for the Recent Posts widget.
		 *
		 * @since 3.4.0
		 *
		 * @see WP_Query::get_posts()
		 *
		 * @param array $args An array of arguments used to retrieve the recent posts.
		 */
		$r = new WP_Query( apply_filters( 'widget_posts_args', array(
			'posts_per_page'      => $number,
			'no_found_rows'       => true,
			'post_status'         => 'publish',
			'ignore_sticky_posts' => true
		) ) );
 
		if ($r->have_posts()) :
			$thumbnail_colors = array("blue", "gold", "green", "red");
		?>
		<?php echo $args['before_widget']; ?>
		<?php if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		} ?>
		<ul class="rpwt-wrapper">
		<?php while ( $r->have_posts() ) : $r->the_post(); ?>
			<li>
				<div class="rptw-item">
					<?php if ( $show_thumbnail ) : ?>
						<div class="rpwt-thumbnail">
						<?php if (has_post_thumbnail()): ?>
							<?php the_post_thumbnail('thumbnail'); ?>
						<?php else: ?>
							
							<img src="<?php echo rpwt_ASSETS ?>images/thumbnail-default-<?php echo $thumbnail_colors[rand(0, 3)]; ?>.png" alt="" />
						<?php endif; ?>
						</div><!-- .rpwt-thumbnail -->
					<?php endif; ?>

					<div class="rpwt-content">
						<div class="rpwt-widget-title">
						   <a href="<?php the_permalink(); ?>"><?php get_the_title() ? the_title() : the_ID(); ?></a> 
						</div><!-- .rpwt-widget-title -->
						<?php if ( $show_date ) : ?>
							<div class="rpwt-widget-date">
								<?php echo get_the_date(); ?>
							</div><!-- .rpwt-widget-date -->
						<?php endif; ?>

						<?php if ( $show_excerpt ) : ?>
							<div class="rpwt-excerpt">
								<?php echo wp_trim_words( get_the_content(), $excerpt_length, '' ); ?>
							</div><!-- .rpwt-excerpt -->
						<?php endif; ?>
					</div><!-- .rpwt-content -->
				</div><!-- .rptw-item -->
			</li>
		<?php endwhile; ?>
		</ul>
		<?php echo $args['after_widget']; ?>
		<?php
		// Reset the global $the_post as this query will have stomped on it
		wp_reset_postdata();
 
		endif;
	}
 
	/**
	 * Handles updating the settings for the current Recent Posts widget instance.
	 *
	 * @since 2.8.0
	 * @access public
	 *
	 * @param array $new_instance New settings for this instance as input by the user via
	 *                            WP_Widget::form().
	 * @param array $old_instance Old settings for this instance.
	 * @return array Updated settings to save.
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = sanitize_text_field( $new_instance['title'] );
		$instance['number'] = (int) $new_instance['number'];
		$instance['show_date'] = isset( $new_instance['show_date'] ) ? (bool) $new_instance['show_date'] : false;
		$instance['show_thumbnail'] = isset( $new_instance['show_thumbnail'] ) ? (bool) $new_instance['show_thumbnail'] : false;
		$instance['show_excerpt'] = isset( $new_instance['show_excerpt'] ) ? (bool) $new_instance['show_excerpt'] : false;
		$instance['excerpt_length'] = (int) $new_instance['excerpt_length'];

		return $instance;
	}
 
	/**
	 * Outputs the settings form for the Recent Posts with Thumbnail.
	 *
	 * @since 2.8.0
	 * @access public
	 *
	 * @param array $instance Current settings.
	 */
	function form( $instance ) {
		$title     = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
		$number    = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
		$show_date = isset( $instance['show_date'] ) ? (bool) $instance['show_date'] : false;
		$show_excerpt = isset( $instance['show_excerpt'] ) ? (bool) $instance['show_excerpt'] : false;
		$excerpt_length    = isset( $instance['excerpt_length'] ) ? absint( $instance['excerpt_length'] ) : 7;
		$show_thumbnail = isset( $instance['show_thumbnail'] ) ? (bool) $instance['show_thumbnail'] : true;
?>
		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" /></p>
 
		<p><label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of posts to show:' ); ?></label>
		<input class="tiny-text" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="number" step="1" min="1" value="<?php echo $number; ?>" size="3" /></p>

		<p><input class="checkbox" type="checkbox"<?php checked( $show_excerpt ); ?> id="<?php echo $this->get_field_id( 'show_excerpt' ); ?>" name="<?php echo $this->get_field_name( 'show_excerpt' ); ?>" />
		<label for="<?php echo $this->get_field_id( 'show_excerpt' ); ?>"><?php _e( 'Show Excerpt?' ); ?></label></p>

		<p class="rpwt-toggle-excerpt-length" style="display:<?php if ( $show_excerpt ) { echo 'block';} else { echo 'none'; }?>;"><label for="<?php echo $this->get_field_id( 'excerpt_length' ); ?>"><?php _e( 'Specify the length of the Excerpt:' ); ?></label>
		<input class="tiny-text" id="<?php echo $this->get_field_id( 'excerpt_length' ); ?>" name="<?php echo $this->get_field_name( 'excerpt_length' ); ?>" type="number" step="1" min="1" value="<?php echo $excerpt_length; ?>" size="3" /></p>

		<p><input class="checkbox" type="checkbox"<?php checked( $show_thumbnail ); ?> id="<?php echo $this->get_field_id( 'show_thumbnail' ); ?>" name="<?php echo $this->get_field_name( 'show_thumbnail' ); ?>" />
		<label for="<?php echo $this->get_field_id( 'show_thumbnail' ); ?>"><?php _e( 'Show Thumbnail?' ); ?></label></p>
 
		<p><input class="checkbox" type="checkbox"<?php checked( $show_date ); ?> id="<?php echo $this->get_field_id( 'show_date' ); ?>" name="<?php echo $this->get_field_name( 'show_date' ); ?>" />
		<label for="<?php echo $this->get_field_id( 'show_date' ); ?>"><?php _e( 'Display post date?' ); ?></label></p>
<?php
	}
}

 ?>