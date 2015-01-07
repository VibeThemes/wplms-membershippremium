<?php

/**
 * Rule class responsible for Event pages protection.
 *
 * @category Membership
 * @package Model
 * @subpackage Rule
 * @subpackage WPLMS
 */
class Membership_Model_Rule_WPLMS_Events extends Membership_Model_Rule {

	var $name = 'wplms_events';
	var $label = 'Event';
	var $description = 'Allows specific WPLMS Events to be protected.';

	var $rulearea = 'public';

	function get_pages() {
		global $bp;

		$args = array(
			'post_type' => 'wplms-event',
			'post_status'=>'publish',
			'posts_per_page'=> 999
			);

		$courses = new WP_Query($args);
		$course_pages=array();
		if($courses->have_posts()){
			while($courses->have_posts()){
				$courses->the_post();
				$course_pages[get_the_ID()]=get_the_title();
			}
		}
		wp_reset_postdata();
		return apply_filters( 'wplms_membership_events', $course_pages );
	}

	function admin_main($data) {

		global $bp;

		if(!$data) $data = array();

		$directory_pages = $this->get_pages();

		?>
		<div class='level-operation' id='main-wplms_events'>
			<h2 class='sidebar-name'><?php _e('Events', 'wplms-membership');?><span><a href='#remove' id='remove-wplms_events' class='removelink' title='<?php _e("Remove Event Pages from this rules area.",'wplms-membership'); ?>'><?php _e('Remove','wplms-membership'); ?></a></span></h2>
			<div class='inner-operation'>
				<p><?php _e('Select the WPLMS Pages to be covered by this rule by checking the box next to the relevant pages title.','wplms-membership'); ?></p>
				<?php

					if($directory_pages) {
						?>
						<table cellspacing="0" class="widefat fixed">
							<thead>
							<tr>
								<th style="" class="manage-column column-cb check-column" id="cb" scope="col"><input type="checkbox"></th>
								<th style="" class="manage-column column-name" id="name" scope="col"><?php _e('Events title', 'wplms-membership'); ?></th>
								</tr>
							</thead>

							<tfoot>
							<tr>
								<th style="" class="manage-column column-cb check-column" id="cb" scope="col"><input type="checkbox"></th>
								<th style="" class="manage-column column-name" id="name" scope="col"><?php _e('Events title', 'wplms-membership'); ?></th>
								</tr>
							</tfoot>

							<tbody>
						<?php


						foreach($directory_pages as $key => $page) { ?>

							<tr valign="middle" class="alternate" id="post-<?php echo $post->ID; ?>">
								<th class="check-column" scope="row">
									<input type="checkbox" value="<?php echo $key; ?>" name="wplms_events[]" <?php if(in_array($key, $data)) echo 'checked="checked"'; ?>>
								</th>
								<td class="column-name">
									<strong><?php echo esc_html($page); ?></strong>
								</td>
						    </tr>
							<?php
						}
						?>
							</tbody>
						</table>
						<?php
					}

				?>
			</div>
		</div>
		<?php
	}

	function on_positive( $data ) {
		$this->data = array_filter( array_map( 'intval', (array)$data ) );
		add_action( 'pre_get_posts', array( $this, 'add_viewable_posts' ), 99 );
	}

	function on_negative( $data ) {
		$this->data = array_filter( array_map( 'intval', (array)$data ) );
		add_action( 'pre_get_posts', array( $this, 'add_unviewable_posts' ), 99 );
	}

	function add_viewable_posts( $wp_query ) {
		if ( !$wp_query->is_singular && empty( $wp_query->query_vars['pagename'] ) && ( !isset( $wp_query->query_vars['post_type'] ) || in_array( $wp_query->query_vars['post_type'], array( 'wplms-event', '' ) )) ) {

			// We are in a list rather than on a single post
			foreach ( (array) $this->data as $key => $value ) {
				$wp_query->query_vars['post__in'][] = $value;
			}

			$wp_query->query_vars['post__in'] = array_unique( $wp_query->query_vars['post__in'] );
		}
	}

	function add_unviewable_posts( $wp_query ) {
		if ( !$wp_query->is_singular && empty( $wp_query->query_vars['pagename'] ) && ( !isset( $wp_query->query_vars['post_type'] ) || in_array( $wp_query->query_vars['post_type'], array( 'wplms-event', '' ) ) ) ) {

			// We are on a list rather than on a single post
			foreach ( (array) $this->data as $key => $value ) {
				$wp_query->query_vars['post__not_in'][] = $value;
			}

			$wp_query->query_vars['post__not_in'] = array_unique( $wp_query->query_vars['post__not_in'] );
		}
	}

	function validate_negative() {
		$page = get_queried_object();
		return is_a( $page, 'WP_Post' ) && $page->post_type == 'wplms-event'
			? !in_array( $page->ID, $this->data )
			: parent::validate_positive();
	}

	function validate_positive() {
		$page = get_queried_object();
		return is_a( $page, 'WP_Post' ) && $page->post_type == 'wplms-event'
			? in_array( $page->ID, $this->data )
			: parent::validate_positive();
	}

}
