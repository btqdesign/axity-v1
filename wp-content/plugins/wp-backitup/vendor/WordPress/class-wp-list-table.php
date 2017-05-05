<?php
/**
 * Administration API: WPBackitup_WP_List_Table class
 *
 * @package WordPress
 * @subpackage List_Table
 * @since 3.1.0
 */

/**
 * Base class for displaying a list of items in an ajaxified HTML table.
 *
 * @since 3.1.0
 * @access private
 */
class WPBackitup_WP_List_Table {

	/**
	 * The current list of items.
	 *
	 * @since 3.1.0
	 * @access public
	 * @var array
	 */
	public $items;

	/**
	 * Various information about the current table.
	 *
	 * @since 3.1.0
	 * @access protected
	 * @var array
	 */
	protected $_args;

	/**
	 * Various information needed for displaying the pagination.
	 *
	 * @since 3.1.0
	 * @access protected
	 * @var array
	 */
	protected $_pagination_args = array();

	/**
	 * The current screen.
	 *
	 * @since 3.1.0
	 * @access protected
	 * @var object
	 */
	protected $screen;

	/**
	 * Cached bulk actions.
	 *
	 * @since 3.1.0
	 * @access private
	 * @var array
	 */
	private $_actions;

	/**
	 * Cached pagination output.
	 *
	 * @since 3.1.0
	 * @access private
	 * @var string
	 */
	private $_pagination;

	/**
	 * The view switcher modes.
	 *
	 * @since 4.1.0
	 * @access protected
	 * @var array
	 */
	protected $modes = array();

	/**
	 * Stores the value returned by ->get_column_info().
	 *
	 * @since 4.1.0
	 * @access protected
	 * @var array
	 */
	protected $_column_headers;

	/**
	 * {@internal Missing Summary}
	 *
	 * @access protected
	 * @var array
	 */
	protected $compat_fields = array( '_args', '_pagination_args', 'screen', '_actions', '_pagination' );

	/**
	 * {@internal Missing Summary}
	 *
	 * @access protected
	 * @var array
	 */
	protected $compat_methods = array( 'set_pagination_args', 'get_views', 'get_bulk_actions', 'bulk_actions',
		'row_actions', 'months_dropdown', 'view_switcher', 'comments_bubble', 'get_items_per_page', 'pagination',
		'get_sortable_columns', 'get_column_info', 'get_table_classes', 'display_tablenav', 'extra_tablenav',
		'single_row_columns' );

	/**
	 * Constructor.
	 *
	 * The child class should call this constructor from its own constructor to override
	 * the default $args.
	 *
	 * @since 3.1.0
	 * @access public
	 *
	 * @param array|string $args {
	 *     Array or string of arguments.
	 *
	 *     @type string $plural   Plural value used for labels and the objects being listed.
	 *                            This affects things such as CSS class-names and nonces used
	 *                            in the list table, e.g. 'posts'. Default empty.
	 *     @type string $singular Singular label for an object being listed, e.g. 'post'.
	 *                            Default empty
	 *     @type bool   $ajax     Whether the list table supports Ajax. This includes loading
	 *                            and sorting data, for example. If true, the class will call
	 *                            the _js_vars() method in the footer to provide variables
	 *                            to any scripts handling Ajax events. Default false.
	 *     @type string $screen   String containing the hook name used to determine the current
	 *                            screen. If left null, the current screen will be automatically set.
	 *                            Default null.
	 * }
	 */
	public function __construct( $args = array() ) {
		$args = wp_parse_args( $args, array(
			'plural' => '',
			'singular' => '',
			'ajax' => false,
			'screen' => null,
		) );

		$this->screen = $this->convert_to_screen( $args['screen'] );

		add_filter( "manage_{$this->screen->id}_columns", array( $this, 'get_columns' ), 0 );

		if ( !$args['plural'] )
			$args['plural'] = $this->screen->base;

		$args['plural'] = sanitize_key( $args['plural'] );
		$args['singular'] = sanitize_key( $args['singular'] );

		$this->_args = $args;

		if ( $args['ajax'] ) {
			// wp_enqueue_script( 'list-table' );
			add_action( 'admin_footer', array( $this, '_js_vars' ) );
		}

		if ( empty( $this->modes ) ) {
			$this->modes = array(
				'list'    => __( 'List View' ),
				'excerpt' => __( 'Excerpt View' )
			);
		}
	}

	/**
	 * Make private properties readable for backward compatibility.
	 *
	 * @since 4.0.0
	 * @access public
	 *
	 * @param string $name Property to get.
	 * @return mixed Property.
	 */
	public function __get( $name ) {
		if ( in_array( $name, $this->compat_fields ) ) {
			return $this->$name;
		}
	}

	/**
	 * Make private properties settable for backward compatibility.
	 *
	 * @since 4.0.0
	 * @access public
	 *
	 * @param string $name  Property to check if set.
	 * @param mixed  $value Property value.
	 * @return mixed Newly-set property.
	 */
	public function __set( $name, $value ) {
		if ( in_array( $name, $this->compat_fields ) ) {
			return $this->$name = $value;
		}
	}

	/**
	 * Make private properties checkable for backward compatibility.
	 *
	 * @since 4.0.0
	 * @access public
	 *
	 * @param string $name Property to check if set.
	 * @return bool Whether the property is set.
	 */
	public function __isset( $name ) {
		if ( in_array( $name, $this->compat_fields ) ) {
			return isset( $this->$name );
		}
	}

	/**
	 * Make private properties un-settable for backward compatibility.
	 *
	 * @since 4.0.0
	 * @access public
	 *
	 * @param string $name Property to unset.
	 */
	public function __unset( $name ) {
		if ( in_array( $name, $this->compat_fields ) ) {
			unset( $this->$name );
		}
	}

	/**
	 * Make private/protected methods readable for backward compatibility.
	 *
	 * @since 4.0.0
	 * @access public
	 *
	 * @param callable $name      Method to call.
	 * @param array    $arguments Arguments to pass when calling.
	 * @return mixed|bool Return value of the callback, false otherwise.
	 */
	public function __call( $name, $arguments ) {
		if ( in_array( $name, $this->compat_methods ) ) {
			return call_user_func_array( array( $this, $name ), $arguments );
		}
		return false;
	}

	/**
	 * Checks the current user's permissions
	 *
	 * @since 3.1.0
	 * @access public
	 * @abstract
	 */
	public function ajax_user_can() {
		die( 'function WPBackitup_WP_List_Table::ajax_user_can() must be over-ridden in a sub-class.' );
	}

	/**
	 * Prepares the list of items for displaying.
	 * @uses WP_List_Table::set_pagination_args()
	 *
	 * @since 3.1.0
	 * @access public
	 * @abstract
	 */
	public function prepare_items() {
		die( 'function WPBackitup_WP_List_Table::prepare_items() must be over-ridden in a sub-class.' );
	}

	/**
	 * An internal method that sets all the necessary pagination arguments
	 *
	 * @since 3.1.0
	 * @access protected
	 *
	 * @param array|string $args Array or string of arguments with information about the pagination.
	 */
	protected function set_pagination_args( $args ) {
		$args = wp_parse_args( $args, array(
			'total_items' => 0,
			'total_pages' => 0,
			'per_page' => 0,
		) );

		if ( !$args['total_pages'] && $args['per_page'] > 0 )
			$args['total_pages'] = ceil( $args['total_items'] / $args['per_page'] );

		// Redirect if page number is invalid and headers are not already sent.
		if ( ! headers_sent() && ! wp_doing_ajax() && $args['total_pages'] > 0 && $this->get_pagenum() > $args['total_pages'] ) {
			wp_redirect( add_query_arg( 'paged', $args['total_pages'] ) );
			exit;
		}

		$this->_pagination_args = $args;
	}

	/**
	 * Access the pagination args.
	 *
	 * @since 3.1.0
	 * @access public
	 *
	 * @param string $key Pagination argument to retrieve. Common values include 'total_items',
	 *                    'total_pages', 'per_page', or 'infinite_scroll'.
	 * @return int Number of items that correspond to the given pagination argument.
	 */
	public function get_pagination_arg( $key ) {
		if ( 'page' === $key ) {
			return $this->get_pagenum();
		}

		if ( isset( $this->_pagination_args[$key] ) ) {
			return $this->_pagination_args[$key];
		}
	}

	/**
	 * Whether the table has items to display or not
	 *
	 * @since 3.1.0
	 * @access public
	 *
	 * @return bool
	 */
	public function has_items() {
		return !empty( $this->items );
	}

	/**
	 * Message to be displayed when there are no items
	 *
	 * @since 3.1.0
	 * @access public
	 */
	public function no_items() {
		_e( 'No items found.' );
	}

	/**
	 * Displays the search box.
	 *
	 * @since 3.1.0
	 * @access public
	 *
	 * @param string $text     The 'submit' button label.
	 * @param string $input_id ID attribute value for the search input field.
	 */
	public function search_box( $text, $input_id ) {
		if ( empty( $_REQUEST['s'] ) && !$this->has_items() )
			return;

		$input_id = $input_id . '-search-input';

		if ( ! empty( $_REQUEST['orderby'] ) )
			echo '<input type="hidden" name="orderby" value="' . esc_attr( $_REQUEST['orderby'] ) . '" />';
		if ( ! empty( $_REQUEST['order'] ) )
			echo '<input type="hidden" name="order" value="' . esc_attr( $_REQUEST['order'] ) . '" />';
		if ( ! empty( $_REQUEST['post_mime_type'] ) )
			echo '<input type="hidden" name="post_mime_type" value="' . esc_attr( $_REQUEST['post_mime_type'] ) . '" />';
		if ( ! empty( $_REQUEST['detached'] ) )
			echo '<input type="hidden" name="detached" value="' . esc_attr( $_REQUEST['detached'] ) . '" />';
?>
<p class="search-box">
	<label class="screen-reader-text" for="<?php echo esc_attr( $input_id ); ?>"><?php echo $text; ?>:</label>
	<input type="search" id="<?php echo esc_attr( $input_id ); ?>" name="s" value="<?php _admin_search_query(); ?>" />
	<?php submit_button( $text, '', '', false, array( 'id' => 'search-submit' ) ); ?>
</p>
<?php
	}

	/**
	 * Get an associative array ( id => link ) with the list
	 * of views available on this table.
	 *
	 * @since 3.1.0
	 * @access protected
	 *
	 * @return array
	 */
	protected function get_views() {
		return array();
	}

	/**
	 * Display the list of views available on this table.
	 *
	 * @since 3.1.0
	 * @access public
	 */
	public function views() {
		$views = $this->get_views();
		/**
		 * Filters the list of available list table views.
		 *
		 * The dynamic portion of the hook name, `$this->screen->id`, refers
		 * to the ID of the current screen, usually a string.
		 *
		 * @since 3.5.0
		 *
		 * @param array $views An array of available list table views.
		 */
		$views = apply_filters( "views_{$this->screen->id}", $views );

		if ( empty( $views ) )
			return;

		$this->screen->render_screen_reader_content( 'heading_views' );

		echo "<ul class='subsubsub'>\n";
		foreach ( $views as $class => $view ) {
			$views[ $class ] = "\t<li class='$class'>$view";
		}
		echo implode( " |</li>\n", $views ) . "</li>\n";
		echo "</ul>";
	}

	/**
	 * Get an associative array ( option_name => option_title ) with the list
	 * of bulk actions available on this table.
	 *
	 * @since 3.1.0
	 * @access protected
	 *
	 * @return array
	 */
	protected function get_bulk_actions() {
		return array();
	}

	/**
	 * Display the bulk actions dropdown.
	 *
	 * @since 3.1.0
	 * @access protected
	 *
	 * @param string $which The location of the bulk actions: 'top' or 'bottom'.
	 *                      This is designated as optional for backward compatibility.
	 */
	protected function bulk_actions( $which = '' ) {
		if ( is_null( $this->_actions ) ) {
			$this->_actions = $this->get_bulk_actions();
			/**
			 * Filters the list table Bulk Actions drop-down.
			 *
			 * The dynamic portion of the hook name, `$this->screen->id`, refers
			 * to the ID of the current screen, usually a string.
			 *
			 * This filter can currently only be used to remove bulk actions.
			 *
			 * @since 3.5.0
			 *
			 * @param array $actions An array of the available bulk actions.
			 */
			$this->_actions = apply_filters( "bulk_actions-{$this->screen->id}", $this->_actions );
			$two = '';
		} else {
			$two = '2';
		}

		if ( empty( $this->_actions ) )
			return;

		echo '<label for="bulk-action-selector-' . esc_attr( $which ) . '" class="screen-reader-text">' . __( 'Select bulk action' ) . '</label>';
		echo '<select name="action' . $two . '" id="bulk-action-selector-' . esc_attr( $which ) . "\">\n";
		echo '<option value="-1">' . __( 'Bulk Actions' ) . "</option>\n";

		foreach ( $this->_actions as $name => $title ) {
			$class = 'edit' === $name ? ' class="hide-if-no-js"' : '';

			echo "\t" . '<option value="' . $name . '"' . $class . '>' . $title . "</option>\n";
		}

		echo "</select>\n";

		submit_button( __( 'Apply' ), 'action', '', false, array( 'id' => "doaction$two" ) );
		echo "\n";
	}

	/**
	 * Get the current action selected from the bulk actions dropdown.
	 *
	 * @since 3.1.0
	 * @access public
	 *
	 * @return string|false The action name or False if no action was selected
	 */
	public function current_action() {
		if ( isset( $_REQUEST['filter_action'] ) && ! empty( $_REQUEST['filter_action'] ) )
			return false;

		if ( isset( $_REQUEST['action'] ) && -1 != $_REQUEST['action'] )
			return $_REQUEST['action'];

		if ( isset( $_REQUEST['action2'] ) && -1 != $_REQUEST['action2'] )
			return $_REQUEST['action2'];

		return false;
	}

	/**
	 * Generate row actions div
	 *
	 * @since 3.1.0
	 * @access protected
	 *
	 * @param array $actions The list of actions
	 * @param bool $always_visible Whether the actions should be always visible
	 * @return string
	 */
	protected function row_actions( $actions, $always_visible = false ) {
		$action_count = count( $actions );
		$i = 0;

		if ( !$action_count )
			return '';

		$out = '<div class="' . ( $always_visible ? 'row-actions visible' : 'row-actions' ) . '">';
		foreach ( $actions as $action => $link ) {
			++$i;
			( $i == $action_count ) ? $sep = '' : $sep = ' | ';
			$out .= "<span class='$action'>$link$sep</span>";
		}
		$out .= '</div>';

		$out .= '<button type="button" class="toggle-row"><span class="screen-reader-text">' . __( 'Show more details' ) . '</span></button>';

		return $out;
	}

	/**
	 * Display a monthly dropdown for filtering items
	 *
	 * @since 3.1.0
	 * @access protected
	 *
	 * @global wpdb      $wpdb
	 * @global WP_Locale $wp_locale
	 *
	 * @param string $post_type
	 */
	protected function months_dropdown( $post_type ) {
		global $wpdb, $wp_locale;

		/**
		 * Filters whether to remove the 'Months' drop-down from the post list table.
		 *
		 * @since 4.2.0
		 *
		 * @param bool   $disable   Whether to disable the drop-down. Default false.
		 * @param string $post_type The post type.
		 */
		if ( apply_filters( 'disable_months_dropdown', false, $post_type ) ) {
			return;
		}

		$extra_checks = "AND post_status != 'auto-draft'";
		if ( ! isset( $_GET['post_status'] ) || 'trash' !== $_GET['post_status'] ) {
			$extra_checks .= " AND post_status != 'trash'";
		} elseif ( isset( $_GET['post_status'] ) ) {
			$extra_checks = $wpdb->prepare( ' AND post_status = %s', $_GET['post_status'] );
		}

		$months = $wpdb->get_results( $wpdb->prepare( "
			SELECT DISTINCT YEAR( post_date ) AS year, MONTH( post_date ) AS month
			FROM $wpdb->posts
			WHERE post_type = %s
			$extra_checks
			ORDER BY post_date DESC
		", $post_type ) );

		/**
		 * Filters the 'Months' drop-down results.
		 *
		 * @since 3.7.0
		 *
		 * @param object $months    The months drop-down query results.
		 * @param string $post_type The post type.
		 */
		$months = apply_filters( 'months_dropdown_results', $months, $post_type );

		$month_count = count( $months );

		if ( !$month_count || ( 1 == $month_count && 0 == $months[0]->month ) )
			return;

		$m = isset( $_GET['m'] ) ? (int) $_GET['m'] : 0;
?>
		<label for="filter-by-date" class="screen-reader-text"><?php _e( 'Filter by date' ); ?></label>
		<select name="m" id="filter-by-date">
			<option<?php selected( $m, 0 ); ?> value="0"><?php _e( 'All dates' ); ?></option>
<?php
		foreach ( $months as $arc_row ) {
			if ( 0 == $arc_row->year )
				continue;

			$month = zeroise( $arc_row->month, 2 );
			$year = $arc_row->year;

			printf( "<option %s value='%s'>%s</option>\n",
				selected( $m, $year . $month, false ),
				esc_attr( $arc_row->year . $month ),
				/* translators: 1: month name, 2: 4-digit year */
				sprintf( __( '%1$s %2$d' ), $wp_locale->get_month( $month ), $year )
			);
		}
?>
		</select>
<?php
	}

	/**
	 * Display a view switcher
	 *
	 * @since 3.1.0
	 * @access protected
	 *
	 * @param string $current_mode
	 */
	protected function view_switcher( $current_mode ) {
?>
		<input type="hidden" name="mode" value="<?php echo esc_attr( $current_mode ); ?>" />
		<div class="view-switch">
<?php
			foreach ( $this->modes as $mode => $title ) {
				$classes = array( 'view-' . $mode );
				if ( $current_mode === $mode )
					$classes[] = 'current';
				printf(
					"<a href='%s' class='%s' id='view-switch-$mode'><span class='screen-reader-text'>%s</span></a>\n",
					esc_url( add_query_arg( 'mode', $mode ) ),
					implode( ' ', $classes ),
					$title
				);
			}
		?>
		</div>
<?php
	}

	/**
	 * Display a comment count bubble
	 *
	 * @since 3.1.0
	 * @access protected
	 *
	 * @param int $post_id          The post ID.
	 * @param int $pending_comments Number of pending comments.
	 */
	protected function comments_bubble( $post_id, $pending_comments ) {
		$approved_comments = get_comments_number();

		$approved_comments_number = number_format_i18n( $approved_comments );
		$pending_comments_number = number_format_i18n( $pending_comments );

		$approved_only_phrase = sprintf( _n( '%s comment', '%s comments', $approved_comments ), $approved_comments_number );
		$approved_phrase = sprintf( _n( '%s approved comment', '%s approved comments', $approved_comments ), $approved_comments_number );
		$pending_phrase = sprintf( _n( '%s pending comment', '%s pending comments', $pending_comments ), $pending_comments_number );

		// No comments at all.
		if ( ! $approved_comments && ! $pending_comments ) {
			printf( '<span aria-hidden="true">—</span><span class="screen-reader-text">%s</span>',
				__( 'No comments' )
			);
		// Approved comments have different display depending on some conditions.
		} elseif ( $approved_comments ) {
			printf( '<a href="%s" class="post-com-count post-com-count-approved"><span class="comment-count-approved" aria-hidden="true">%s</span><span class="screen-reader-text">%s</span></a>',
				esc_url( add_query_arg( array( 'p' => $post_id, 'comment_status' => 'approved' ), admin_url( 'edit-comments.php' ) ) ),
				$approved_comments_number,
				$pending_comments ? $approved_phrase : $approved_only_phrase
			);
		} else {
			printf( '<span class="post-com-count post-com-count-no-comments"><span class="comment-count comment-count-no-comments" aria-hidden="true">%s</span><span class="screen-reader-text">%s</span></span>',
				$approved_comments_number,
				$pending_comments ? __( 'No approved comments' ) : __( 'No comments' )
			);
		}

		if ( $pending_comments ) {
			printf( '<a href="%s" class="post-com-count post-com-count-pending"><span class="comment-count-pending" aria-hidden="true">%s</span><span class="screen-reader-text">%s</span></a>',
				esc_url( add_query_arg( array( 'p' => $post_id, 'comment_status' => 'moderated' ), admin_url( 'edit-comments.php' ) ) ),
				$pending_comments_number,
				$pending_phrase
			);
		} else {
			printf( '<span class="post-com-count post-com-count-pending post-com-count-no-pending"><span class="comment-count comment-count-no-pending" aria-hidden="true">%s</span><span class="screen-reader-text">%s</span></span>',
				$pending_comments_number,
				$approved_comments ? __( 'No pending comments' ) : __( 'No comments' )
			);
		}
	}

	/**
	 * Get the current page number
	 *
	 * @since 3.1.0
	 * @access public
	 *
	 * @return int
	 */
	public function get_pagenum() {
		$pagenum = isset( $_REQUEST['paged'] ) ? absint( $_REQUEST['paged'] ) : 0;

		if ( isset( $this->_pagination_args['total_pages'] ) && $pagenum > $this->_pagination_args['total_pages'] )
			$pagenum = $this->_pagination_args['total_pages'];

		return max( 1, $pagenum );
	}

	/**
	 * Get number of items to display on a single page
	 *
	 * @since 3.1.0
	 * @access protected
	 *
	 * @param string $option
	 * @param int    $default
	 * @return int
	 */
	protected function get_items_per_page( $option, $default = 20 ) {
		$per_page = (int) get_user_option( $option );
		if ( empty( $per_page ) || $per_page < 1 )
			$per_page = $default;

		/**
		 * Filters the number of items to be displayed on each page of the list table.
		 *
		 * The dynamic hook name, $option, refers to the `per_page` option depending
		 * on the type of list table in use. Possible values include: 'edit_comments_per_page',
		 * 'sites_network_per_page', 'site_themes_network_per_page', 'themes_network_per_page',
		 * 'users_network_per_page', 'edit_post_per_page', 'edit_page_per_page',
		 * 'edit_{$post_type}_per_page', etc.
		 *
		 * @since 2.9.0
		 *
		 * @param int $per_page Number of items to be displayed. Default 20.
		 */
		return (int) apply_filters( $option, $per_page );
	}

	/**
	 * Display the pagination.
	 *
	 * @since 3.1.0
	 * @access protected
	 *
	 * @param string $which
	 */
	protected function pagination( $which ) {
		if ( empty( $this->_pagination_args ) ) {
			return;
		}

		$total_items = $this->_pagination_args['total_items'];
		$total_pages = $this->_pagination_args['total_pages'];
		$infinite_scroll = false;
		if ( isset( $this->_pagination_args['infinite_scroll'] ) ) {
			$infinite_scroll = $this->_pagination_args['infinite_scroll'];
		}

		if ( 'top' === $which && $total_pages > 1 ) {
			$this->screen->render_screen_reader_content( 'heading_pagination' );
		}

		$output = '<span class="displaying-num">' . sprintf( _n( '%s item', '%s items', $total_items ), number_format_i18n( $total_items ) ) . '</span>';

		$current = $this->get_pagenum();
		$removable_query_args = wp_removable_query_args();

		$current_url = set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );

		$current_url = remove_query_arg( $removable_query_args, $current_url );

		$page_links = array();

		$total_pages_before = '<span class="paging-input">';
		$total_pages_after  = '</span></span>';

		$disable_first = $disable_last = $disable_prev = $disable_next = false;

 		if ( $current == 1 ) {
			$disable_first = true;
			$disable_prev = true;
 		}
		if ( $current == 2 ) {
			$disable_first = true;
		}
 		if ( $current == $total_pages ) {
			$disable_last = true;
			$disable_next = true;
 		}
		if ( $current == $total_pages - 1 ) {
			$disable_last = true;
		}

		if ( $disable_first ) {
			$page_links[] = '<span class="tablenav-pages-navspan" aria-hidden="true">&laquo;</span>';
		} else {
			$page_links[] = sprintf( "<a class='first-page' href='%s'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></a>",
				esc_url( remove_query_arg( 'paged', $current_url ) ),
				__( 'First page' ),
				'&laquo;'
			);
		}

		if ( $disable_prev ) {
			$page_links[] = '<span class="tablenav-pages-navspan" aria-hidden="true">&lsaquo;</span>';
		} else {
			$page_links[] = sprintf( "<a class='prev-page' href='%s'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></a>",
				esc_url( add_query_arg( 'paged', max( 1, $current-1 ), $current_url ) ),
				__( 'Previous page' ),
				'&lsaquo;'
			);
		}

		if ( 'bottom' === $which ) {
			$html_current_page  = $current;
			$total_pages_before = '<span class="screen-reader-text">' . __( 'Current Page' ) . '</span><span id="table-paging" class="paging-input"><span class="tablenav-paging-text">';
		} else {
			$html_current_page = sprintf( "%s<input class='current-page' id='current-page-selector' type='text' name='paged' value='%s' size='%d' aria-describedby='table-paging' /><span class='tablenav-paging-text'>",
				'<label for="current-page-selector" class="screen-reader-text">' . __( 'Current Page' ) . '</label>',
				$current,
				strlen( $total_pages )
			);
		}
		$html_total_pages = sprintf( "<span class='total-pages'>%s</span>", number_format_i18n( $total_pages ) );
		$page_links[] = $total_pages_before . sprintf( _x( '%1$s of %2$s', 'paging' ), $html_current_page, $html_total_pages ) . $total_pages_after;

		if ( $disable_next ) {
			$page_links[] = '<span class="tablenav-pages-navspan" aria-hidden="true">&rsaquo;</span>';
		} else {
			$page_links[] = sprintf( "<a class='next-page' href='%s'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></a>",
				esc_url( add_query_arg( 'paged', min( $total_pages, $current+1 ), $current_url ) ),
				__( 'Next page' ),
				'&rsaquo;'
			);
		}

		if ( $disable_last ) {
			$page_links[] = '<span class="tablenav-pages-navspan" aria-hidden="true">&raquo;</span>';
		} else {
			$page_links[] = sprintf( "<a class='last-page' href='%s'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></a>",
				esc_url( add_query_arg( 'paged', $total_pages, $current_url ) ),
				__( 'Last page' ),
				'&raquo;'
			);
		}

		$pagination_links_class = 'pagination-links';
		if ( ! empty( $infinite_scroll ) ) {
			$pagination_links_class = ' hide-if-js';
		}
		$output .= "\n<span class='$pagination_links_class'>" . join( "\n", $page_links ) . '</span>';

		if ( $total_pages ) {
			$page_class = $total_pages < 2 ? ' one-page' : '';
		} else {
			$page_class = ' no-pages';
		}
		$this->_pagination = "<div class='tablenav-pages{$page_class}'>$output</div>";

		echo $this->_pagination;
	}

	/**
	 * Get a list of columns. The format is:
	 * 'internal-name' => 'Title'
	 *
	 * @since 3.1.0
	 * @access public
	 * @abstract
	 *
	 * @return array
	 */
	public function get_columns() {
		die( 'function WPBackitup_WP_List_Table::get_columns() must be over-ridden in a sub-class.' );
	}

	/**
	 * Get a list of sortable columns. The format is:
	 * 'internal-name' => 'orderby'
	 * or
	 * 'internal-name' => array( 'orderby', true )
	 *
	 * The second format will make the initial sorting order be descending
	 *
	 * @since 3.1.0
	 * @access protected
	 *
	 * @return array
	 */
	protected function get_sortable_columns() {
		return array();
	}

	/**
	 * Gets the name of the default primary column.
	 *
	 * @since 4.3.0
	 * @access protected
	 *
	 * @return string Name of the default primary column, in this case, an empty string.
	 */
	protected function get_default_primary_column_name() {
		$columns = $this->get_columns();
		$column = '';

		if ( empty( $columns ) ) {
			return $column;
		}

		// We need a primary defined so responsive views show something,
		// so let's fall back to the first non-checkbox column.
		foreach ( $columns as $col => $column_name ) {
			if ( 'cb' === $col ) {
				continue;
			}

			$column = $col;
			break;
		}

		return $column;
	}

	/**
	 * Public wrapper for WPBackitup_WP_List_Table::get_default_primary_column_name().
	 *
	 * @since 4.4.0
	 * @access public
	 *
	 * @return string Name of the default primary column.
	 */
	public function get_primary_column() {
		return $this->get_primary_column_name();
	}

	/**
	 * Gets the name of the primary column.
	 *
	 * @since 4.3.0
	 * @access protected
	 *
	 * @return string The name of the primary column.
	 */
	protected function get_primary_column_name() {
		$columns = get_column_headers( $this->screen );
		$default = $this->get_default_primary_column_name();

		// If the primary column doesn't exist fall back to the
		// first non-checkbox column.
		if ( ! isset( $columns[ $default ] ) ) {
			$default = WPBackitup_WP_List_Table::get_default_primary_column_name();
		}

		/**
		 * Filters the name of the primary column for the current list table.
		 *
		 * @since 4.3.0
		 *
		 * @param string $default Column name default for the specific list table, e.g. 'name'.
		 * @param string $context Screen ID for specific list table, e.g. 'plugins'.
		 */
		$column  = apply_filters( 'list_table_primary_column', $default, $this->screen->id );

		if ( empty( $column ) || ! isset( $columns[ $column ] ) ) {
			$column = $default;
		}

		return $column;
	}

	/**
	 * Get a list of all, hidden and sortable columns, with filter applied
	 *
	 * @since 3.1.0
	 * @access protected
	 *
	 * @return array
	 */
	protected function get_column_info() {
		// $_column_headers is already set / cached
		if ( isset( $this->_column_headers ) && is_array( $this->_column_headers ) ) {
			// Back-compat for list tables that have been manually setting $_column_headers for horse reasons.
			// In 4.3, we added a fourth argument for primary column.
			$column_headers = array( array(), array(), array(), $this->get_primary_column_name() );
			foreach ( $this->_column_headers as $key => $value ) {
				$column_headers[ $key ] = $value;
			}

			return $column_headers;
		}

		$columns = get_column_headers( $this->screen );
		$hidden = get_hidden_columns( $this->screen );

		$sortable_columns = $this->get_sortable_columns();
		/**
		 * Filters the list table sortable columns for a specific screen.
		 *
		 * The dynamic portion of the hook name, `$this->screen->id`, refers
		 * to the ID of the current screen, usually a string.
		 *
		 * @since 3.5.0
		 *
		 * @param array $sortable_columns An array of sortable columns.
		 */
		$_sortable = apply_filters( "manage_{$this->screen->id}_sortable_columns", $sortable_columns );

		$sortable = array();
		foreach ( $_sortable as $id => $data ) {
			if ( empty( $data ) )
				continue;

			$data = (array) $data;
			if ( !isset( $data[1] ) )
				$data[1] = false;

			$sortable[$id] = $data;
		}

		$primary = $this->get_primary_column_name();
		$this->_column_headers = array( $columns, $hidden, $sortable, $primary );

		return $this->_column_headers;
	}

	/**
	 * Return number of visible columns
	 *
	 * @since 3.1.0
	 * @access public
	 *
	 * @return int
	 */
	public function get_column_count() {
		list ( $columns, $hidden ) = $this->get_column_info();
		$hidden = array_intersect( array_keys( $columns ), array_filter( $hidden ) );
		return count( $columns ) - count( $hidden );
	}

	/**
	 * Print column headers, accounting for hidden and sortable columns.
	 *
	 * @since 3.1.0
	 * @access public
	 *
	 * @staticvar int $cb_counter
	 *
	 * @param bool $with_id Whether to set the id attribute or not
	 */
	public function print_column_headers( $with_id = true ) {
		list( $columns, $hidden, $sortable, $primary ) = $this->get_column_info();

		$current_url = set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
		$current_url = remove_query_arg( 'paged', $current_url );

		if ( isset( $_GET['orderby'] ) ) {
			$current_orderby = $_GET['orderby'];
		} else {
			$current_orderby = '';
		}

		if ( isset( $_GET['order'] ) && 'desc' === $_GET['order'] ) {
			$current_order = 'desc';
		} else {
			$current_order = 'asc';
		}

		if ( ! empty( $columns['cb'] ) ) {
			static $cb_counter = 1;
			$columns['cb'] = '<label class="screen-reader-text" for="cb-select-all-' . $cb_counter . '">' . __( 'Select All' ) . '</label>'
				. '<input id="cb-select-all-' . $cb_counter . '" type="checkbox" />';
			$cb_counter++;
		}

		foreach ( $columns as $column_key => $column_display_name ) {
			$class = array( 'manage-column', "column-$column_key" );

			if ( in_array( $column_key, $hidden ) ) {
				$class[] = 'hidden';
			}

			if ( 'cb' === $column_key )
				$class[] = 'check-column';
			elseif ( in_array( $column_key, array( 'posts', 'comments', 'links' ) ) )
				$class[] = 'num';

			if ( $column_key === $primary ) {
				$class[] = 'column-primary';
			}

			if ( isset( $sortable[$column_key] ) ) {
				list( $orderby, $desc_first ) = $sortable[$column_key];

				if ( $current_orderby === $orderby ) {
					$order = 'asc' === $current_order ? 'desc' : 'asc';
					$class[] = 'sorted';
					$class[] = $current_order;
				} else {
					$order = $desc_first ? 'desc' : 'asc';
					$class[] = 'sortable';
					$class[] = $desc_first ? 'asc' : 'desc';
				}

				$column_display_name = '<a href="' . esc_url( add_query_arg( compact( 'orderby', 'order' ), $current_url ) ) . '"><span>' . $column_display_name . '</span><span class="sorting-indicator"></span></a>';
			}

			$tag = ( 'cb' === $column_key ) ? 'td' : 'th';
			$scope = ( 'th' === $tag ) ? 'scope="col"' : '';
			$id = $with_id ? "id='$column_key'" : '';

			if ( !empty( $class ) )
				$class = "class='" . join( ' ', $class ) . "'";

			echo "<$tag $scope $id $class>$column_display_name</$tag>";
		}
	}

	/**
	 * Display the table
	 *
	 * @since 3.1.0
	 * @access public
	 */
	public function display() {
		$singular = $this->_args['singular'];

		$this->display_tablenav( 'top' );

		$this->screen->render_screen_reader_content( 'heading_list' );
?>
<table class="wp-list-table <?php echo implode( ' ', $this->get_table_classes() ); ?>">
	<thead>
	<tr>
		<?php $this->print_column_headers(); ?>
	</tr>
	</thead>

	<tbody id="the-list"<?php
		if ( $singular ) {
			echo " data-wp-lists='list:$singular'";
		} ?>>
		<?php $this->display_rows_or_placeholder(); ?>
	</tbody>

	<tfoot>
	<tr>
		<?php $this->print_column_headers( false ); ?>
	</tr>
	</tfoot>

</table>
<?php
		$this->display_tablenav( 'bottom' );
	}

	/**
	 * Get a list of CSS classes for the WPBackitup_WP_List_Table table tag.
	 *
	 * @since 3.1.0
	 * @access protected
	 *
	 * @return array List of CSS classes for the table tag.
	 */
	protected function get_table_classes() {
		return array( 'widefat', 'fixed', 'striped', $this->_args['plural'] );
	}

	/**
	 * Generate the table navigation above or below the table
	 *
	 * @since 3.1.0
	 * @access protected
	 * @param string $which
	 */
	protected function display_tablenav( $which ) {
		if ( 'top' === $which ) {
			wp_nonce_field( 'bulk-' . $this->_args['plural'] );
		}
		?>
	<div class="tablenav <?php echo esc_attr( $which ); ?>">

		<?php if ( $this->has_items() ): ?>
		<div class="alignleft actions bulkactions">
			<?php $this->bulk_actions( $which ); ?>
		</div>
		<?php endif;
		$this->extra_tablenav( $which );
		$this->pagination( $which );
?>

		<br class="clear" />
	</div>
<?php
	}

	/**
	 * Extra controls to be displayed between bulk actions and pagination
	 *
	 * @since 3.1.0
	 * @access protected
	 *
	 * @param string $which
	 */
	protected function extra_tablenav( $which ) {}

	/**
	 * Generate the tbody element for the list table.
	 *
	 * @since 3.1.0
	 * @access public
	 */
	public function display_rows_or_placeholder() {
		if ( $this->has_items() ) {
			$this->display_rows();
		} else {
			echo '<tr class="no-items"><td class="colspanchange" colspan="' . $this->get_column_count() . '">';
			$this->no_items();
			echo '</td></tr>';
		}
	}

	/**
	 * Generate the table rows
	 *
	 * @since 3.1.0
	 * @access public
	 */
	public function display_rows() {
		foreach ( $this->items as $item )
			$this->single_row( $item );
	}

	/**
	 * Generates content for a single row of the table
	 *
	 * @since 3.1.0
	 * @access public
	 *
	 * @param object $item The current item
	 */
	public function single_row( $item ) {
		echo '<tr>';
		$this->single_row_columns( $item );
		echo '</tr>';
	}

	/**
	 *
	 * @param object $item
	 * @param string $column_name
	 */
	protected function column_default( $item, $column_name ) {}

	/**
	 *
	 * @param object $item
	 */
	protected function column_cb( $item ) {}

	/**
	 * Generates the columns for a single row of the table
	 *
	 * @since 3.1.0
	 * @access protected
	 *
	 * @param object $item The current item
	 */
	protected function single_row_columns( $item ) {
		list( $columns, $hidden, $sortable, $primary ) = $this->get_column_info();

		foreach ( $columns as $column_name => $column_display_name ) {
			$classes = "$column_name column-$column_name";
			if ( $primary === $column_name ) {
				$classes .= ' has-row-actions column-primary';
			}

			if ( in_array( $column_name, $hidden ) ) {
				$classes .= ' hidden';
			}

			// Comments column uses HTML in the display name with screen reader text.
			// Instead of using esc_attr(), we strip tags to get closer to a user-friendly string.
			$data = 'data-colname="' . wp_strip_all_tags( $column_display_name ) . '"';

			$attributes = "class='$classes' $data";

			if ( 'cb' === $column_name ) {
				echo '<th scope="row" class="check-column">';
				echo $this->column_cb( $item );
				echo '</th>';
			} elseif ( method_exists( $this, '_column_' . $column_name ) ) {
				echo call_user_func(
					array( $this, '_column_' . $column_name ),
					$item,
					$classes,
					$data,
					$primary
				);
			} elseif ( method_exists( $this, 'column_' . $column_name ) ) {
				echo "<td $attributes>";
				echo call_user_func( array( $this, 'column_' . $column_name ), $item );
				echo $this->handle_row_actions( $item, $column_name, $primary );
				echo "</td>";
			} else {
				echo "<td $attributes>";
				echo $this->column_default( $item, $column_name );
				echo $this->handle_row_actions( $item, $column_name, $primary );
				echo "</td>";
			}
		}
	}

	/**
	 * Generates and display row actions links for the list table.
	 *
	 * @since 4.3.0
	 * @access protected
	 *
	 * @param object $item        The item being acted upon.
	 * @param string $column_name Current column name.
	 * @param string $primary     Primary column name.
	 * @return string The row actions HTML, or an empty string if the current column is the primary column.
	 */
	protected function handle_row_actions( $item, $column_name, $primary ) {
		return $column_name === $primary ? '<button type="button" class="toggle-row"><span class="screen-reader-text">' . __( 'Show more details' ) . '</span></button>' : '';
 	}

	/**
	 * Handle an incoming ajax request (called from admin-ajax.php)
	 *
	 * @since 3.1.0
	 * @access public
	 */
	public function ajax_response() {
		$this->prepare_items();

		ob_start();
		if ( ! empty( $_REQUEST['no_placeholder'] ) ) {
			$this->display_rows();
		} else {
			$this->display_rows_or_placeholder();
		}

		$rows = ob_get_clean();

		$response = array( 'rows' => $rows );

		if ( isset( $this->_pagination_args['total_items'] ) ) {
			$response['total_items_i18n'] = sprintf(
				_n( '%s item', '%s items', $this->_pagination_args['total_items'] ),
				number_format_i18n( $this->_pagination_args['total_items'] )
			);
		}
		if ( isset( $this->_pagination_args['total_pages'] ) ) {
			$response['total_pages'] = $this->_pagination_args['total_pages'];
			$response['total_pages_i18n'] = number_format_i18n( $this->_pagination_args['total_pages'] );
		}

		die( wp_json_encode( $response ) );
	}

	/**
	 * Send required variables to JavaScript land
	 *
	 * @access public
	 */
	public function _js_vars() {
		$args = array(
			'class'  => get_class( $this ),
			'screen' => array(
				'id'   => $this->screen->id,
				'base' => $this->screen->base,
			)
		);

		printf( "<script type='text/javascript'>list_args = %s;</script>\n", wp_json_encode( $args ) );
	}

	/**
	 * Output an unordered list of checkbox input elements labeled with category names.
	 *
	 * @since 2.5.1
	 *
	 * @see wp_terms_checklist()
	 *
	 * @param int    $post_id              Optional. Post to generate a categories checklist for. Default 0.
	 *                                     $selected_cats must not be an array. Default 0.
	 * @param int    $descendants_and_self Optional. ID of the category to output along with its descendants.
	 *                                     Default 0.
	 * @param array  $selected_cats        Optional. List of categories to mark as checked. Default false.
	 * @param array  $popular_cats         Optional. List of categories to receive the "popular-category" class.
	 *                                     Default false.
	 * @param object $walker               Optional. Walker object to use to build the output.
	 *                                     Default is a Walker_Category_Checklist instance.
	 * @param bool   $checked_ontop        Optional. Whether to move checked items out of the hierarchy and to
	 *                                     the top of the list. Default true.
	 */
	function wp_category_checklist( $post_id = 0, $descendants_and_self = 0, $selected_cats = false, $popular_cats = false, $walker = null, $checked_ontop = true ) {
		wp_terms_checklist( $post_id, array(
			'taxonomy' => 'category',
			'descendants_and_self' => $descendants_and_self,
			'selected_cats' => $selected_cats,
			'popular_cats' => $popular_cats,
			'walker' => $walker,
			'checked_ontop' => $checked_ontop
		) );
	}

	/**
	 * Output an unordered list of checkbox input elements labelled with term names.
	 *
	 * Taxonomy-independent version of wp_category_checklist().
	 *
	 * @since 3.0.0
	 * @since 4.4.0 Introduced the `$echo` argument.
	 *
	 * @param int          $post_id Optional. Post ID. Default 0.
	 * @param array|string $args {
	 *     Optional. Array or string of arguments for generating a terms checklist. Default empty array.
	 *
	 *     @type int    $descendants_and_self ID of the category to output along with its descendants.
	 *                                        Default 0.
	 *     @type array  $selected_cats        List of categories to mark as checked. Default false.
	 *     @type array  $popular_cats         List of categories to receive the "popular-category" class.
	 *                                        Default false.
	 *     @type object $walker               Walker object to use to build the output.
	 *                                        Default is a Walker_Category_Checklist instance.
	 *     @type string $taxonomy             Taxonomy to generate the checklist for. Default 'category'.
	 *     @type bool   $checked_ontop        Whether to move checked items out of the hierarchy and to
	 *                                        the top of the list. Default true.
	 *     @type bool   $echo                 Whether to echo the generated markup. False to return the markup instead
	 *                                        of echoing it. Default true.
	 * }
	 */
	function wp_terms_checklist( $post_id = 0, $args = array() ) {
	 	$defaults = array(
			'descendants_and_self' => 0,
			'selected_cats' => false,
			'popular_cats' => false,
			'walker' => null,
			'taxonomy' => 'category',
			'checked_ontop' => true,
			'echo' => true,
		);

		/**
		 * Filters the taxonomy terms checklist arguments.
		 *
		 * @since 3.4.0
		 *
		 * @see wp_terms_checklist()
		 *
		 * @param array $args    An array of arguments.
		 * @param int   $post_id The post ID.
		 */
		$params = apply_filters( 'wp_terms_checklist_args', $args, $post_id );

		$r = wp_parse_args( $params, $defaults );

		if ( empty( $r['walker'] ) || ! ( $r['walker'] instanceof Walker ) ) {
			$walker = new Walker_Category_Checklist;
		} else {
			$walker = $r['walker'];
		}

		$taxonomy = $r['taxonomy'];
		$descendants_and_self = (int) $r['descendants_and_self'];

		$args = array( 'taxonomy' => $taxonomy );

		$tax = get_taxonomy( $taxonomy );
		$args['disabled'] = ! current_user_can( $tax->cap->assign_terms );

		$args['list_only'] = ! empty( $r['list_only'] );

		if ( is_array( $r['selected_cats'] ) ) {
			$args['selected_cats'] = $r['selected_cats'];
		} elseif ( $post_id ) {
			$args['selected_cats'] = wp_get_object_terms( $post_id, $taxonomy, array_merge( $args, array( 'fields' => 'ids' ) ) );
		} else {
			$args['selected_cats'] = array();
		}
		if ( is_array( $r['popular_cats'] ) ) {
			$args['popular_cats'] = $r['popular_cats'];
		} else {
			$args['popular_cats'] = get_terms( $taxonomy, array(
				'fields' => 'ids',
				'orderby' => 'count',
				'order' => 'DESC',
				'number' => 10,
				'hierarchical' => false
			) );
		}
		if ( $descendants_and_self ) {
			$categories = (array) get_terms( $taxonomy, array(
				'child_of' => $descendants_and_self,
				'hierarchical' => 0,
				'hide_empty' => 0
			) );
			$self = get_term( $descendants_and_self, $taxonomy );
			array_unshift( $categories, $self );
		} else {
			$categories = (array) get_terms( $taxonomy, array( 'get' => 'all' ) );
		}

		$output = '';

		if ( $r['checked_ontop'] ) {
			// Post process $categories rather than adding an exclude to the get_terms() query to keep the query the same across all posts (for any query cache)
			$checked_categories = array();
			$keys = array_keys( $categories );

			foreach ( $keys as $k ) {
				if ( in_array( $categories[$k]->term_id, $args['selected_cats'] ) ) {
					$checked_categories[] = $categories[$k];
					unset( $categories[$k] );
				}
			}

			// Put checked cats on top
			$output .= call_user_func_array( array( $walker, 'walk' ), array( $checked_categories, 0, $args ) );
		}
		// Then the rest of them
		$output .= call_user_func_array( array( $walker, 'walk' ), array( $categories, 0, $args ) );

		if ( $r['echo'] ) {
			echo $output;
		}

		return $output;
	}

	/**
	 * Retrieve a list of the most popular terms from the specified taxonomy.
	 *
	 * If the $echo argument is true then the elements for a list of checkbox
	 * `<input>` elements labelled with the names of the selected terms is output.
	 * If the $post_ID global isn't empty then the terms associated with that
	 * post will be marked as checked.
	 *
	 * @since 2.5.0
	 *
	 * @param string $taxonomy Taxonomy to retrieve terms from.
	 * @param int $default Not used.
	 * @param int $number Number of terms to retrieve. Defaults to 10.
	 * @param bool $echo Optionally output the list as well. Defaults to true.
	 * @return array List of popular term IDs.
	 */
	function wp_popular_terms_checklist( $taxonomy, $default = 0, $number = 10, $echo = true ) {
		$post = get_post();

		if ( $post && $post->ID )
			$checked_terms = wp_get_object_terms($post->ID, $taxonomy, array('fields'=>'ids'));
		else
			$checked_terms = array();

		$terms = get_terms( $taxonomy, array( 'orderby' => 'count', 'order' => 'DESC', 'number' => $number, 'hierarchical' => false ) );

		$tax = get_taxonomy($taxonomy);

		$popular_ids = array();
		foreach ( (array) $terms as $term ) {
			$popular_ids[] = $term->term_id;
			if ( !$echo ) // Hack for Ajax use.
				continue;
			$id = "popular-$taxonomy-$term->term_id";
			$checked = in_array( $term->term_id, $checked_terms ) ? 'checked="checked"' : '';
			?>

			<li id="<?php echo $id; ?>" class="popular-category">
				<label class="selectit">
					<input id="in-<?php echo $id; ?>" type="checkbox" <?php echo $checked; ?> value="<?php echo (int) $term->term_id; ?>" <?php disabled( ! current_user_can( $tax->cap->assign_terms ) ); ?> />
					<?php
					/** This filter is documented in wp-includes/category-template.php */
					echo esc_html( apply_filters( 'the_category', $term->name ) );
					?>
				</label>
			</li>

			<?php
		}
		return $popular_ids;
	}

	/**
	 * Outputs a link category checklist element.
	 *
	 * @since 2.5.1
	 *
	 * @param int $link_id
	 */
	function wp_link_category_checklist( $link_id = 0 ) {
		$default = 1;

		$checked_categories = array();

		if ( $link_id ) {
			$checked_categories = wp_get_link_cats( $link_id );
			// No selected categories, strange
			if ( ! count( $checked_categories ) ) {
				$checked_categories[] = $default;
			}
		} else {
			$checked_categories[] = $default;
		}

		$categories = get_terms( 'link_category', array( 'orderby' => 'name', 'hide_empty' => 0 ) );

		if ( empty( $categories ) )
			return;

		foreach ( $categories as $category ) {
			$cat_id = $category->term_id;

			/** This filter is documented in wp-includes/category-template.php */
			$name = esc_html( apply_filters( 'the_category', $category->name ) );
			$checked = in_array( $cat_id, $checked_categories ) ? ' checked="checked"' : '';
			echo '<li id="link-category-', $cat_id, '"><label for="in-link-category-', $cat_id, '" class="selectit"><input value="', $cat_id, '" type="checkbox" name="link_category[]" id="in-link-category-', $cat_id, '"', $checked, '/> ', $name, "</label></li>";
		}
	}

	/**
	 * Adds hidden fields with the data for use in the inline editor for posts and pages.
	 *
	 * @since 2.7.0
	 *
	 * @param WP_Post $post Post object.
	 */
	function get_inline_data($post) {
		$post_type_object = get_post_type_object($post->post_type);
		if ( ! current_user_can( 'edit_post', $post->ID ) )
			return;

		$title = esc_textarea( trim( $post->post_title ) );

		/** This filter is documented in wp-admin/edit-tag-form.php */
		echo '
	<div class="hidden" id="inline_' . $post->ID . '">
		<div class="post_title">' . $title . '</div>' .
		/** This filter is documented in wp-admin/edit-tag-form.php */
		'<div class="post_name">' . apply_filters( 'editable_slug', $post->post_name, $post ) . '</div>
		<div class="post_author">' . $post->post_author . '</div>
		<div class="comment_status">' . esc_html( $post->comment_status ) . '</div>
		<div class="ping_status">' . esc_html( $post->ping_status ) . '</div>
		<div class="_status">' . esc_html( $post->post_status ) . '</div>
		<div class="jj">' . mysql2date( 'd', $post->post_date, false ) . '</div>
		<div class="mm">' . mysql2date( 'm', $post->post_date, false ) . '</div>
		<div class="aa">' . mysql2date( 'Y', $post->post_date, false ) . '</div>
		<div class="hh">' . mysql2date( 'H', $post->post_date, false ) . '</div>
		<div class="mn">' . mysql2date( 'i', $post->post_date, false ) . '</div>
		<div class="ss">' . mysql2date( 's', $post->post_date, false ) . '</div>
		<div class="post_password">' . esc_html( $post->post_password ) . '</div>';

		if ( $post_type_object->hierarchical ) {
			echo '<div class="post_parent">' . $post->post_parent . '</div>';
		}

		echo '<div class="page_template">' . ( $post->page_template ? esc_html( $post->page_template ) : 'default' ) . '</div>';

		if ( post_type_supports( $post->post_type, 'page-attributes' ) ) {
			echo '<div class="menu_order">' . $post->menu_order . '</div>';
		}

		$taxonomy_names = get_object_taxonomies( $post->post_type );
		foreach ( $taxonomy_names as $taxonomy_name) {
			$taxonomy = get_taxonomy( $taxonomy_name );

			if ( $taxonomy->hierarchical && $taxonomy->show_ui ) {

				$terms = get_object_term_cache( $post->ID, $taxonomy_name );
				if ( false === $terms ) {
					$terms = wp_get_object_terms( $post->ID, $taxonomy_name );
					wp_cache_add( $post->ID, wp_list_pluck( $terms, 'term_id' ), $taxonomy_name . '_relationships' );
				}
				$term_ids = empty( $terms ) ? array() : wp_list_pluck( $terms, 'term_id' );

				echo '<div class="post_category" id="' . $taxonomy_name . '_' . $post->ID . '">' . implode( ',', $term_ids ) . '</div>';

			} elseif ( $taxonomy->show_ui ) {

				$terms_to_edit = get_terms_to_edit( $post->ID, $taxonomy_name );
				if ( ! is_string( $terms_to_edit ) ) {
					$terms_to_edit = '';
				}

				echo '<div class="tags_input" id="'.$taxonomy_name.'_'.$post->ID.'">'
					. esc_html( str_replace( ',', ', ', $terms_to_edit ) ) . '</div>';

			}
		}

		if ( !$post_type_object->hierarchical )
			echo '<div class="sticky">' . (is_sticky($post->ID) ? 'sticky' : '') . '</div>';

		if ( post_type_supports( $post->post_type, 'post-formats' ) )
			echo '<div class="post_format">' . esc_html( get_post_format( $post->ID ) ) . '</div>';

		echo '</div>';
	}

	/**
	 * Outputs the in-line comment reply-to form in the Comments list table.
	 *
	 * @since 2.7.0
	 *
	 * @global WP_List_Table $wp_list_table
	 *
	 * @param int    $position
	 * @param bool   $checkbox
	 * @param string $mode
	 * @param bool   $table_row
	 */
	function wp_comment_reply( $position = 1, $checkbox = false, $mode = 'single', $table_row = true ) {
		global $wp_list_table;
		/**
		 * Filters the in-line comment reply-to form output in the Comments
		 * list table.
		 *
		 * Returning a non-empty value here will short-circuit display
		 * of the in-line comment-reply form in the Comments list table,
		 * echoing the returned value instead.
		 *
		 * @since 2.7.0
		 *
		 * @see wp_comment_reply()
		 *
		 * @param string $content The reply-to form content.
		 * @param array  $args    An array of default args.
		 */
		$content = apply_filters( 'wp_comment_reply', '', array( 'position' => $position, 'checkbox' => $checkbox, 'mode' => $mode ) );

		if ( ! empty($content) ) {
			echo $content;
			return;
		}

		if ( ! $wp_list_table ) {
			if ( $mode == 'single' ) {
				$wp_list_table = _get_list_table('WP_Post_Comments_List_Table');
			} else {
				$wp_list_table = _get_list_table('WP_Comments_List_Table');
			}
		}

	?>
	<form method="get">
	<?php if ( $table_row ) : ?>
	<table style="display:none;"><tbody id="com-reply"><tr id="replyrow" class="inline-edit-row" style="display:none;"><td colspan="<?php echo $wp_list_table->get_column_count(); ?>" class="colspanchange">
	<?php else : ?>
	<div id="com-reply" style="display:none;"><div id="replyrow" style="display:none;">
	<?php endif; ?>
		<fieldset class="comment-reply">
		<legend>
			<span class="hidden" id="editlegend"><?php _e( 'Edit Comment' ); ?></span>
			<span class="hidden" id="replyhead"><?php _e( 'Reply to Comment' ); ?></span>
			<span class="hidden" id="addhead"><?php _e( 'Add new Comment' ); ?></span>
		</legend>

		<div id="replycontainer">
		<label for="replycontent" class="screen-reader-text"><?php _e( 'Comment' ); ?></label>
		<?php
		$quicktags_settings = array( 'buttons' => 'strong,em,link,block,del,ins,img,ul,ol,li,code,close' );
		wp_editor( '', 'replycontent', array( 'media_buttons' => false, 'tinymce' => false, 'quicktags' => $quicktags_settings ) );
		?>
		</div>

		<div id="edithead" style="display:none;">
			<div class="inside">
			<label for="author-name"><?php _e( 'Name' ) ?></label>
			<input type="text" name="newcomment_author" size="50" value="" id="author-name" />
			</div>

			<div class="inside">
			<label for="author-email"><?php _e('Email') ?></label>
			<input type="text" name="newcomment_author_email" size="50" value="" id="author-email" />
			</div>

			<div class="inside">
			<label for="author-url"><?php _e('URL') ?></label>
			<input type="text" id="author-url" name="newcomment_author_url" class="code" size="103" value="" />
			</div>
		</div>

		<p id="replysubmit" class="submit">
		<a href="#comments-form" class="save button button-primary alignright">
		<span id="addbtn" style="display:none;"><?php _e('Add Comment'); ?></span>
		<span id="savebtn" style="display:none;"><?php _e('Update Comment'); ?></span>
		<span id="replybtn" style="display:none;"><?php _e('Submit Reply'); ?></span></a>
		<a href="#comments-form" class="cancel button alignleft"><?php _e('Cancel'); ?></a>
		<span class="waiting spinner"></span>
		<span class="error" style="display:none;"></span>
		</p>

		<input type="hidden" name="action" id="action" value="" />
		<input type="hidden" name="comment_ID" id="comment_ID" value="" />
		<input type="hidden" name="comment_post_ID" id="comment_post_ID" value="" />
		<input type="hidden" name="status" id="status" value="" />
		<input type="hidden" name="position" id="position" value="<?php echo $position; ?>" />
		<input type="hidden" name="checkbox" id="checkbox" value="<?php echo $checkbox ? 1 : 0; ?>" />
		<input type="hidden" name="mode" id="mode" value="<?php echo esc_attr($mode); ?>" />
		<?php
			wp_nonce_field( 'replyto-comment', '_ajax_nonce-replyto-comment', false );
			if ( current_user_can( 'unfiltered_html' ) )
				wp_nonce_field( 'unfiltered-html-comment', '_wp_unfiltered_html_comment', false );
		?>
		</fieldset>
	<?php if ( $table_row ) : ?>
	</td></tr></tbody></table>
	<?php else : ?>
	</div></div>
	<?php endif; ?>
	</form>
	<?php
	}

	/**
	 * Output 'undo move to trash' text for comments
	 *
	 * @since 2.9.0
	 */
	function wp_comment_trashnotice() {
	?>
	<div class="hidden" id="trash-undo-holder">
		<div class="trash-undo-inside"><?php printf(__('Comment by %s moved to the trash.'), '<strong></strong>'); ?> <span class="undo untrash"><a href="#"><?php _e('Undo'); ?></a></span></div>
	</div>
	<div class="hidden" id="spam-undo-holder">
		<div class="spam-undo-inside"><?php printf(__('Comment by %s marked as spam.'), '<strong></strong>'); ?> <span class="undo unspam"><a href="#"><?php _e('Undo'); ?></a></span></div>
	</div>
	<?php
	}

	/**
	 * Outputs a post's public meta data in the Custom Fields meta box.
	 *
	 * @since 1.2.0
	 *
	 * @param array $meta
	 */
	function list_meta( $meta ) {
		// Exit if no meta
		if ( ! $meta ) {
			echo '
	<table id="list-table" style="display: none;">
		<thead>
		<tr>
			<th class="left">' . _x( 'Name', 'meta name' ) . '</th>
			<th>' . __( 'Value' ) . '</th>
		</tr>
		</thead>
		<tbody id="the-list" data-wp-lists="list:meta">
		<tr><td></td></tr>
		</tbody>
	</table>'; //TBODY needed for list-manipulation JS
			return;
		}
		$count = 0;
	?>
	<table id="list-table">
		<thead>
		<tr>
			<th class="left"><?php _ex( 'Name', 'meta name' ) ?></th>
			<th><?php _e( 'Value' ) ?></th>
		</tr>
		</thead>
		<tbody id='the-list' data-wp-lists='list:meta'>
	<?php
		foreach ( $meta as $entry )
			echo _list_meta_row( $entry, $count );
	?>
		</tbody>
	</table>
	<?php
	}

	/**
	 * Outputs a single row of public meta data in the Custom Fields meta box.
	 *
	 * @since 2.5.0
	 *
	 * @staticvar string $update_nonce
	 *
	 * @param array $entry
	 * @param int   $count
	 * @return string
	 */
	function _list_meta_row( $entry, &$count ) {
		static $update_nonce = '';

		if ( is_protected_meta( $entry['meta_key'], 'post' ) )
			return '';

		if ( ! $update_nonce )
			$update_nonce = wp_create_nonce( 'add-meta' );

		$r = '';
		++ $count;

		if ( is_serialized( $entry['meta_value'] ) ) {
			if ( is_serialized_string( $entry['meta_value'] ) ) {
				// This is a serialized string, so we should display it.
				$entry['meta_value'] = maybe_unserialize( $entry['meta_value'] );
			} else {
				// This is a serialized array/object so we should NOT display it.
				--$count;
				return '';
			}
		}

		$entry['meta_key'] = esc_attr($entry['meta_key']);
		$entry['meta_value'] = esc_textarea( $entry['meta_value'] ); // using a <textarea />
		$entry['meta_id'] = (int) $entry['meta_id'];

		$delete_nonce = wp_create_nonce( 'delete-meta_' . $entry['meta_id'] );

		$r .= "\n\t<tr id='meta-{$entry['meta_id']}'>";
		$r .= "\n\t\t<td class='left'><label class='screen-reader-text' for='meta-{$entry['meta_id']}-key'>" . __( 'Key' ) . "</label><input name='meta[{$entry['meta_id']}][key]' id='meta-{$entry['meta_id']}-key' type='text' size='20' value='{$entry['meta_key']}' />";

		$r .= "\n\t\t<div class='submit'>";
		$r .= get_submit_button( __( 'Delete' ), 'deletemeta small', "deletemeta[{$entry['meta_id']}]", false, array( 'data-wp-lists' => "delete:the-list:meta-{$entry['meta_id']}::_ajax_nonce=$delete_nonce" ) );
		$r .= "\n\t\t";
		$r .= get_submit_button( __( 'Update' ), 'updatemeta small', "meta-{$entry['meta_id']}-submit", false, array( 'data-wp-lists' => "add:the-list:meta-{$entry['meta_id']}::_ajax_nonce-add-meta=$update_nonce" ) );
		$r .= "</div>";
		$r .= wp_nonce_field( 'change-meta', '_ajax_nonce', false, false );
		$r .= "</td>";

		$r .= "\n\t\t<td><label class='screen-reader-text' for='meta-{$entry['meta_id']}-value'>" . __( 'Value' ) . "</label><textarea name='meta[{$entry['meta_id']}][value]' id='meta-{$entry['meta_id']}-value' rows='2' cols='30'>{$entry['meta_value']}</textarea></td>\n\t</tr>";
		return $r;
	}

	/**
	 * Prints the form in the Custom Fields meta box.
	 *
	 * @since 1.2.0
	 *
	 * @global wpdb $wpdb WordPress database abstraction object.
	 *
	 * @param WP_Post $post Optional. The post being edited.
	 */
	function meta_form( $post = null ) {
		global $wpdb;
		$post = get_post( $post );

		/**
		 * Filters values for the meta key dropdown in the Custom Fields meta box.
		 *
		 * Returning a non-null value will effectively short-circuit and avoid a
		 * potentially expensive query against postmeta.
		 *
		 * @since 4.4.0
		 *
		 * @param array|null $keys Pre-defined meta keys to be used in place of a postmeta query. Default null.
		 * @param WP_Post    $post The current post object.
		 */
		$keys = apply_filters( 'postmeta_form_keys', null, $post );

		if ( null === $keys ) {
			/**
			 * Filters the number of custom fields to retrieve for the drop-down
			 * in the Custom Fields meta box.
			 *
			 * @since 2.1.0
			 *
			 * @param int $limit Number of custom fields to retrieve. Default 30.
			 */
			$limit = apply_filters( 'postmeta_form_limit', 30 );
			$sql = "SELECT DISTINCT meta_key
				FROM $wpdb->postmeta
				WHERE meta_key NOT BETWEEN '_' AND '_z'
				HAVING meta_key NOT LIKE %s
				ORDER BY meta_key
				LIMIT %d";
			$keys = $wpdb->get_col( $wpdb->prepare( $sql, $wpdb->esc_like( '_' ) . '%', $limit ) );
		}

		if ( $keys ) {
			natcasesort( $keys );
			$meta_key_input_id = 'metakeyselect';
		} else {
			$meta_key_input_id = 'metakeyinput';
		}
	?>
	<p><strong><?php _e( 'Add New Custom Field:' ) ?></strong></p>
	<table id="newmeta">
	<thead>
	<tr>
	<th class="left"><label for="<?php echo $meta_key_input_id; ?>"><?php _ex( 'Name', 'meta name' ) ?></label></th>
	<th><label for="metavalue"><?php _e( 'Value' ) ?></label></th>
	</tr>
	</thead>

	<tbody>
	<tr>
	<td id="newmetaleft" class="left">
	<?php if ( $keys ) { ?>
	<select id="metakeyselect" name="metakeyselect">
	<option value="#NONE#"><?php _e( '&mdash; Select &mdash;' ); ?></option>
	<?php

		foreach ( $keys as $key ) {
			if ( is_protected_meta( $key, 'post' ) || ! current_user_can( 'add_post_meta', $post->ID, $key ) )
				continue;
			echo "\n<option value='" . esc_attr($key) . "'>" . esc_html($key) . "</option>";
		}
	?>
	</select>
	<input class="hide-if-js" type="text" id="metakeyinput" name="metakeyinput" value="" />
	<a href="#postcustomstuff" class="hide-if-no-js" onclick="jQuery('#metakeyinput, #metakeyselect, #enternew, #cancelnew').toggle();return false;">
	<span id="enternew"><?php _e('Enter new'); ?></span>
	<span id="cancelnew" class="hidden"><?php _e('Cancel'); ?></span></a>
	<?php } else { ?>
	<input type="text" id="metakeyinput" name="metakeyinput" value="" />
	<?php } ?>
	</td>
	<td><textarea id="metavalue" name="metavalue" rows="2" cols="25"></textarea></td>
	</tr>

	<tr><td colspan="2">
	<div class="submit">
	<?php submit_button( __( 'Add Custom Field' ), '', 'addmeta', false, array( 'id' => 'newmeta-submit', 'data-wp-lists' => 'add:the-list:newmeta' ) ); ?>
	</div>
	<?php wp_nonce_field( 'add-meta', '_ajax_nonce-add-meta', false ); ?>
	</td></tr>
	</tbody>
	</table>
	<?php

	}

	/**
	 * Print out HTML form date elements for editing post or comment publish date.
	 *
	 * @since 0.71
	 * @since 4.4.0 Converted to use get_comment() instead of the global `$comment`.
	 *
	 * @global WP_Locale  $wp_locale
	 *
	 * @param int|bool $edit      Accepts 1|true for editing the date, 0|false for adding the date.
	 * @param int|bool $for_post  Accepts 1|true for applying the date to a post, 0|false for a comment.
	 * @param int      $tab_index The tabindex attribute to add. Default 0.
	 * @param int|bool $multi     Optional. Whether the additional fields and buttons should be added.
	 *                            Default 0|false.
	 */
	function touch_time( $edit = 1, $for_post = 1, $tab_index = 0, $multi = 0 ) {
		global $wp_locale;
		$post = get_post();

		if ( $for_post )
			$edit = ! ( in_array($post->post_status, array('draft', 'pending') ) && (!$post->post_date_gmt || '0000-00-00 00:00:00' == $post->post_date_gmt ) );

		$tab_index_attribute = '';
		if ( (int) $tab_index > 0 )
			$tab_index_attribute = " tabindex=\"$tab_index\"";

		// todo: Remove this?
		// echo '<label for="timestamp" style="display: block;"><input type="checkbox" class="checkbox" name="edit_date" value="1" id="timestamp"'.$tab_index_attribute.' /> '.__( 'Edit timestamp' ).'</label><br />';

		$time_adj = current_time('timestamp');
		$post_date = ($for_post) ? $post->post_date : get_comment()->comment_date;
		$jj = ($edit) ? mysql2date( 'd', $post_date, false ) : gmdate( 'd', $time_adj );
		$mm = ($edit) ? mysql2date( 'm', $post_date, false ) : gmdate( 'm', $time_adj );
		$aa = ($edit) ? mysql2date( 'Y', $post_date, false ) : gmdate( 'Y', $time_adj );
		$hh = ($edit) ? mysql2date( 'H', $post_date, false ) : gmdate( 'H', $time_adj );
		$mn = ($edit) ? mysql2date( 'i', $post_date, false ) : gmdate( 'i', $time_adj );
		$ss = ($edit) ? mysql2date( 's', $post_date, false ) : gmdate( 's', $time_adj );

		$cur_jj = gmdate( 'd', $time_adj );
		$cur_mm = gmdate( 'm', $time_adj );
		$cur_aa = gmdate( 'Y', $time_adj );
		$cur_hh = gmdate( 'H', $time_adj );
		$cur_mn = gmdate( 'i', $time_adj );

		$month = '<label><span class="screen-reader-text">' . __( 'Month' ) . '</span><select ' . ( $multi ? '' : 'id="mm" ' ) . 'name="mm"' . $tab_index_attribute . ">\n";
		for ( $i = 1; $i < 13; $i = $i +1 ) {
			$monthnum = zeroise($i, 2);
			$monthtext = $wp_locale->get_month_abbrev( $wp_locale->get_month( $i ) );
			$month .= "\t\t\t" . '<option value="' . $monthnum . '" data-text="' . $monthtext . '" ' . selected( $monthnum, $mm, false ) . '>';
			/* translators: 1: month number (01, 02, etc.), 2: month abbreviation */
			$month .= sprintf( __( '%1$s-%2$s' ), $monthnum, $monthtext ) . "</option>\n";
		}
		$month .= '</select></label>';

		$day = '<label><span class="screen-reader-text">' . __( 'Day' ) . '</span><input type="text" ' . ( $multi ? '' : 'id="jj" ' ) . 'name="jj" value="' . $jj . '" size="2" maxlength="2"' . $tab_index_attribute . ' autocomplete="off" /></label>';
		$year = '<label><span class="screen-reader-text">' . __( 'Year' ) . '</span><input type="text" ' . ( $multi ? '' : 'id="aa" ' ) . 'name="aa" value="' . $aa . '" size="4" maxlength="4"' . $tab_index_attribute . ' autocomplete="off" /></label>';
		$hour = '<label><span class="screen-reader-text">' . __( 'Hour' ) . '</span><input type="text" ' . ( $multi ? '' : 'id="hh" ' ) . 'name="hh" value="' . $hh . '" size="2" maxlength="2"' . $tab_index_attribute . ' autocomplete="off" /></label>';
		$minute = '<label><span class="screen-reader-text">' . __( 'Minute' ) . '</span><input type="text" ' . ( $multi ? '' : 'id="mn" ' ) . 'name="mn" value="' . $mn . '" size="2" maxlength="2"' . $tab_index_attribute . ' autocomplete="off" /></label>';

		echo '<div class="timestamp-wrap">';
		/* translators: 1: month, 2: day, 3: year, 4: hour, 5: minute */
		printf( __( '%1$s %2$s, %3$s @ %4$s:%5$s' ), $month, $day, $year, $hour, $minute );

		echo '</div><input type="hidden" id="ss" name="ss" value="' . $ss . '" />';

		if ( $multi ) return;

		echo "\n\n";
		$map = array(
			'mm' => array( $mm, $cur_mm ),
			'jj' => array( $jj, $cur_jj ),
			'aa' => array( $aa, $cur_aa ),
			'hh' => array( $hh, $cur_hh ),
			'mn' => array( $mn, $cur_mn ),
		);
		foreach ( $map as $timeunit => $value ) {
			list( $unit, $curr ) = $value;

			echo '<input type="hidden" id="hidden_' . $timeunit . '" name="hidden_' . $timeunit . '" value="' . $unit . '" />' . "\n";
			$cur_timeunit = 'cur_' . $timeunit;
			echo '<input type="hidden" id="' . $cur_timeunit . '" name="' . $cur_timeunit . '" value="' . $curr . '" />' . "\n";
		}
	?>

	<p>
	<a href="#edit_timestamp" class="save-timestamp hide-if-no-js button"><?php _e('OK'); ?></a>
	<a href="#edit_timestamp" class="cancel-timestamp hide-if-no-js button-cancel"><?php _e('Cancel'); ?></a>
	</p>
	<?php
	}

	/**
	 * Print out option HTML elements for the page templates drop-down.
	 *
	 * @since 1.5.0
	 * @since 4.7.0 Added the `$post_type` parameter.
	 *
	 * @param string $default   Optional. The template file name. Default empty.
	 * @param string $post_type Optional. Post type to get templates for. Default 'post'.
	 */
	function page_template_dropdown( $default = '', $post_type = 'page' ) {
		$templates = get_page_templates( null, $post_type );
		ksort( $templates );
		foreach ( array_keys( $templates ) as $template ) {
			$selected = selected( $default, $templates[ $template ], false );
			echo "\n\t<option value='" . $templates[ $template ] . "' $selected>$template</option>";
		}
	}

	/**
	 * Print out option HTML elements for the page parents drop-down.
	 *
	 * @since 1.5.0
	 * @since 4.4.0 `$post` argument was added.
	 *
	 * @global wpdb $wpdb WordPress database abstraction object.
	 *
	 * @param int         $default Optional. The default page ID to be pre-selected. Default 0.
	 * @param int         $parent  Optional. The parent page ID. Default 0.
	 * @param int         $level   Optional. Page depth level. Default 0.
	 * @param int|WP_Post $post    Post ID or WP_Post object.
	 *
	 * @return null|false Boolean False if page has no children, otherwise print out html elements
	 */
	function parent_dropdown( $default = 0, $parent = 0, $level = 0, $post = null ) {
		global $wpdb;
		$post = get_post( $post );
		$items = $wpdb->get_results( $wpdb->prepare("SELECT ID, post_parent, post_title FROM $wpdb->posts WHERE post_parent = %d AND post_type = 'page' ORDER BY menu_order", $parent) );

		if ( $items ) {
			foreach ( $items as $item ) {
				// A page cannot be its own parent.
				if ( $post && $post->ID && $item->ID == $post->ID )
					continue;

				$pad = str_repeat( '&nbsp;', $level * 3 );
				$selected = selected( $default, $item->ID, false );

				echo "\n\t<option class='level-$level' value='$item->ID' $selected>$pad " . esc_html($item->post_title) . "</option>";
				parent_dropdown( $default, $item->ID, $level +1 );
			}
		} else {
			return false;
		}
	}

	/**
	 * Print out option html elements for role selectors.
	 *
	 * @since 2.1.0
	 *
	 * @param string $selected Slug for the role that should be already selected.
	 */
	function wp_dropdown_roles( $selected = '' ) {
		$p = '';
		$r = '';

		$editable_roles = array_reverse( get_editable_roles() );

		foreach ( $editable_roles as $role => $details ) {
			$name = translate_user_role($details['name'] );
			if ( $selected == $role ) // preselect specified role
				$p = "\n\t<option selected='selected' value='" . esc_attr($role) . "'>$name</option>";
			else
				$r .= "\n\t<option value='" . esc_attr($role) . "'>$name</option>";
		}
		echo $p . $r;
	}

	/**
	 * Outputs the form used by the importers to accept the data to be imported
	 *
	 * @since 2.0.0
	 *
	 * @param string $action The action attribute for the form.
	 */
	function wp_import_upload_form( $action ) {

		/**
		 * Filters the maximum allowed upload size for import files.
		 *
		 * @since 2.3.0
		 *
		 * @see wp_max_upload_size()
		 *
		 * @param int $max_upload_size Allowed upload size. Default 1 MB.
		 */
		$bytes = apply_filters( 'import_upload_size_limit', wp_max_upload_size() );
		$size = size_format( $bytes );
		$upload_dir = wp_upload_dir();
		if ( ! empty( $upload_dir['error'] ) ) :
			?><div class="error"><p><?php _e('Before you can upload your import file, you will need to fix the following error:'); ?></p>
			<p><strong><?php echo $upload_dir['error']; ?></strong></p></div><?php
		else :
	?>
	<form enctype="multipart/form-data" id="import-upload-form" method="post" class="wp-upload-form" action="<?php echo esc_url( wp_nonce_url( $action, 'import-upload' ) ); ?>">
	<p>
	<label for="upload"><?php _e( 'Choose a file from your computer:' ); ?></label> (<?php printf( __('Maximum size: %s' ), $size ); ?>)
	<input type="file" id="upload" name="import" size="25" />
	<input type="hidden" name="action" value="save" />
	<input type="hidden" name="max_file_size" value="<?php echo $bytes; ?>" />
	</p>
	<?php submit_button( __('Upload file and import'), 'primary' ); ?>
	</form>
	<?php
		endif;
	}

	/**
	 * Adds a meta box to one or more screens.
	 *
	 * @since 2.5.0
	 * @since 4.4.0 The `$screen` parameter now accepts an array of screen IDs.
	 *
	 * @global array $wp_meta_boxes
	 *
	 * @param string                 $id            Meta box ID (used in the 'id' attribute for the meta box).
	 * @param string                 $title         Title of the meta box.
	 * @param callable               $callback      Function that fills the box with the desired content.
	 *                                              The function should echo its output.
	 * @param string|array|WP_Screen $screen        Optional. The screen or screens on which to show the box
	 *                                              (such as a post type, 'link', or 'comment'). Accepts a single
	 *                                              screen ID, WP_Screen object, or array of screen IDs. Default
	 *                                              is the current screen.
	 * @param string                 $context       Optional. The context within the screen where the boxes
	 *                                              should display. Available contexts vary from screen to
	 *                                              screen. Post edit screen contexts include 'normal', 'side',
	 *                                              and 'advanced'. Comments screen contexts include 'normal'
	 *                                              and 'side'. Menus meta boxes (accordion sections) all use
	 *                                              the 'side' context. Global default is 'advanced'.
	 * @param string                 $priority      Optional. The priority within the context where the boxes
	 *                                              should show ('high', 'low'). Default 'default'.
	 * @param array                  $callback_args Optional. Data that should be set as the $args property
	 *                                              of the box array (which is the second parameter passed
	 *                                              to your callback). Default null.
	 */
	function add_meta_box( $id, $title, $callback, $screen = null, $context = 'advanced', $priority = 'default', $callback_args = null ) {
		global $wp_meta_boxes;

		if ( empty( $screen ) ) {
			$screen = get_current_screen();
		} elseif ( is_string( $screen ) ) {
			$screen = convert_to_screen( $screen );
		} elseif ( is_array( $screen ) ) {
			foreach ( $screen as $single_screen ) {
				add_meta_box( $id, $title, $callback, $single_screen, $context, $priority, $callback_args );
			}
		}

		if ( ! isset( $screen->id ) ) {
			return;
		}

		$page = $screen->id;

		if ( !isset($wp_meta_boxes) )
			$wp_meta_boxes = array();
		if ( !isset($wp_meta_boxes[$page]) )
			$wp_meta_boxes[$page] = array();
		if ( !isset($wp_meta_boxes[$page][$context]) )
			$wp_meta_boxes[$page][$context] = array();

		foreach ( array_keys($wp_meta_boxes[$page]) as $a_context ) {
			foreach ( array('high', 'core', 'default', 'low') as $a_priority ) {
				if ( !isset($wp_meta_boxes[$page][$a_context][$a_priority][$id]) )
					continue;

				// If a core box was previously added or removed by a plugin, don't add.
				if ( 'core' == $priority ) {
					// If core box previously deleted, don't add
					if ( false === $wp_meta_boxes[$page][$a_context][$a_priority][$id] )
						return;

					/*
					 * If box was added with default priority, give it core priority to
					 * maintain sort order.
					 */
					if ( 'default' == $a_priority ) {
						$wp_meta_boxes[$page][$a_context]['core'][$id] = $wp_meta_boxes[$page][$a_context]['default'][$id];
						unset($wp_meta_boxes[$page][$a_context]['default'][$id]);
					}
					return;
				}
				// If no priority given and id already present, use existing priority.
				if ( empty($priority) ) {
					$priority = $a_priority;
				/*
				 * Else, if we're adding to the sorted priority, we don't know the title
				 * or callback. Grab them from the previously added context/priority.
				 */
				} elseif ( 'sorted' == $priority ) {
					$title = $wp_meta_boxes[$page][$a_context][$a_priority][$id]['title'];
					$callback = $wp_meta_boxes[$page][$a_context][$a_priority][$id]['callback'];
					$callback_args = $wp_meta_boxes[$page][$a_context][$a_priority][$id]['args'];
				}
				// An id can be in only one priority and one context.
				if ( $priority != $a_priority || $context != $a_context )
					unset($wp_meta_boxes[$page][$a_context][$a_priority][$id]);
			}
		}

		if ( empty($priority) )
			$priority = 'low';

		if ( !isset($wp_meta_boxes[$page][$context][$priority]) )
			$wp_meta_boxes[$page][$context][$priority] = array();

		$wp_meta_boxes[$page][$context][$priority][$id] = array('id' => $id, 'title' => $title, 'callback' => $callback, 'args' => $callback_args);
	}

	/**
	 * Meta-Box template function
	 *
	 * @since 2.5.0
	 *
	 * @global array $wp_meta_boxes
	 *
	 * @staticvar bool $already_sorted
	 * @param string|WP_Screen $screen  Screen identifier
	 * @param string           $context box context
	 * @param mixed            $object  gets passed to the box callback function as first parameter
	 * @return int number of meta_boxes
	 */
	function do_meta_boxes( $screen, $context, $object ) {
		global $wp_meta_boxes;
		static $already_sorted = false;

		if ( empty( $screen ) )
			$screen = get_current_screen();
		elseif ( is_string( $screen ) )
			$screen = convert_to_screen( $screen );

		$page = $screen->id;

		$hidden = get_hidden_meta_boxes( $screen );

		printf('<div id="%s-sortables" class="meta-box-sortables">', htmlspecialchars($context));

		// Grab the ones the user has manually sorted. Pull them out of their previous context/priority and into the one the user chose
		if ( ! $already_sorted && $sorted = get_user_option( "meta-box-order_$page" ) ) {
			foreach ( $sorted as $box_context => $ids ) {
				foreach ( explode( ',', $ids ) as $id ) {
					if ( $id && 'dashboard_browser_nag' !== $id ) {
						add_meta_box( $id, null, null, $screen, $box_context, 'sorted' );
					}
				}
			}
		}

		$already_sorted = true;

		$i = 0;

		if ( isset( $wp_meta_boxes[ $page ][ $context ] ) ) {
			foreach ( array( 'high', 'sorted', 'core', 'default', 'low' ) as $priority ) {
				if ( isset( $wp_meta_boxes[ $page ][ $context ][ $priority ]) ) {
					foreach ( (array) $wp_meta_boxes[ $page ][ $context ][ $priority ] as $box ) {
						if ( false == $box || ! $box['title'] )
							continue;
						$i++;
						$hidden_class = in_array($box['id'], $hidden) ? ' hide-if-js' : '';
						echo '<div id="' . $box['id'] . '" class="postbox ' . postbox_classes($box['id'], $page) . $hidden_class . '" ' . '>' . "\n";
						if ( 'dashboard_browser_nag' != $box['id'] ) {
							$widget_title = $box[ 'title' ];

							if ( is_array( $box[ 'args' ] ) && isset( $box[ 'args' ][ '__widget_basename' ] ) ) {
								$widget_title = $box[ 'args' ][ '__widget_basename' ];
								// Do not pass this parameter to the user callback function.
								unset( $box[ 'args' ][ '__widget_basename' ] );
							}

							echo '<button type="button" class="handlediv button-link" aria-expanded="true">';
							echo '<span class="screen-reader-text">' . sprintf( __( 'Toggle panel: %s' ), $widget_title ) . '</span>';
							echo '<span class="toggle-indicator" aria-hidden="true"></span>';
							echo '</button>';
						}
						echo "<h2 class='hndle'><span>{$box['title']}</span></h2>\n";
						echo '<div class="inside">' . "\n";
						call_user_func($box['callback'], $object, $box);
						echo "</div>\n";
						echo "</div>\n";
					}
				}
			}
		}

		echo "</div>";

		return $i;

	}

	/**
	 * Removes a meta box from one or more screens.
	 *
	 * @since 2.6.0
	 * @since 4.4.0 The `$screen` parameter now accepts an array of screen IDs.
	 *
	 * @global array $wp_meta_boxes
	 *
	 * @param string                 $id      Meta box ID (used in the 'id' attribute for the meta box).
	 * @param string|array|WP_Screen $screen  The screen or screens on which the meta box is shown (such as a
	 *                                        post type, 'link', or 'comment'). Accepts a single screen ID,
	 *                                        WP_Screen object, or array of screen IDs.
	 * @param string                 $context The context within the screen where the box is set to display.
	 *                                        Contexts vary from screen to screen. Post edit screen contexts
	 *                                        include 'normal', 'side', and 'advanced'. Comments screen contexts
	 *                                        include 'normal' and 'side'. Menus meta boxes (accordion sections)
	 *                                        all use the 'side' context.
	 */
	function remove_meta_box( $id, $screen, $context ) {
		global $wp_meta_boxes;

		if ( empty( $screen ) ) {
			$screen = get_current_screen();
		} elseif ( is_string( $screen ) ) {
			$screen = convert_to_screen( $screen );
		} elseif ( is_array( $screen ) ) {
			foreach ( $screen as $single_screen ) {
				remove_meta_box( $id, $single_screen, $context );
			}
		}

		if ( ! isset( $screen->id ) ) {
			return;
		}

		$page = $screen->id;

		if ( !isset($wp_meta_boxes) )
			$wp_meta_boxes = array();
		if ( !isset($wp_meta_boxes[$page]) )
			$wp_meta_boxes[$page] = array();
		if ( !isset($wp_meta_boxes[$page][$context]) )
			$wp_meta_boxes[$page][$context] = array();

		foreach ( array('high', 'core', 'default', 'low') as $priority )
			$wp_meta_boxes[$page][$context][$priority][$id] = false;
	}

	/**
	 * Meta Box Accordion Template Function
	 *
	 * Largely made up of abstracted code from do_meta_boxes(), this
	 * function serves to build meta boxes as list items for display as
	 * a collapsible accordion.
	 *
	 * @since 3.6.0
	 *
	 * @uses global $wp_meta_boxes Used to retrieve registered meta boxes.
	 *
	 * @param string|object $screen  The screen identifier.
	 * @param string        $context The meta box context.
	 * @param mixed         $object  gets passed to the section callback function as first parameter.
	 * @return int number of meta boxes as accordion sections.
	 */
	function do_accordion_sections( $screen, $context, $object ) {
		global $wp_meta_boxes;

		wp_enqueue_script( 'accordion' );

		if ( empty( $screen ) )
			$screen = get_current_screen();
		elseif ( is_string( $screen ) )
			$screen = convert_to_screen( $screen );

		$page = $screen->id;

		$hidden = get_hidden_meta_boxes( $screen );
		?>
		<div id="side-sortables" class="accordion-container">
			<ul class="outer-border">
		<?php
		$i = 0;
		$first_open = false;

		if ( isset( $wp_meta_boxes[ $page ][ $context ] ) ) {
			foreach ( array( 'high', 'core', 'default', 'low' ) as $priority ) {
				if ( isset( $wp_meta_boxes[ $page ][ $context ][ $priority ] ) ) {
					foreach ( $wp_meta_boxes[ $page ][ $context ][ $priority ] as $box ) {
						if ( false == $box || ! $box['title'] )
							continue;
						$i++;
						$hidden_class = in_array( $box['id'], $hidden ) ? 'hide-if-js' : '';

						$open_class = '';
						if ( ! $first_open && empty( $hidden_class ) ) {
							$first_open = true;
							$open_class = 'open';
						}
						?>
						<li class="control-section accordion-section <?php echo $hidden_class; ?> <?php echo $open_class; ?> <?php echo esc_attr( $box['id'] ); ?>" id="<?php echo esc_attr( $box['id'] ); ?>">
							<h3 class="accordion-section-title hndle" tabindex="0">
								<?php echo esc_html( $box['title'] ); ?>
								<span class="screen-reader-text"><?php _e( 'Press return or enter to open this section' ); ?></span>
							</h3>
							<div class="accordion-section-content <?php postbox_classes( $box['id'], $page ); ?>">
								<div class="inside">
									<?php call_user_func( $box['callback'], $object, $box ); ?>
								</div><!-- .inside -->
							</div><!-- .accordion-section-content -->
						</li><!-- .accordion-section -->
						<?php
					}
				}
			}
		}
		?>
			</ul><!-- .outer-border -->
		</div><!-- .accordion-container -->
		<?php
		return $i;
	}

	/**
	 * Add a new section to a settings page.
	 *
	 * Part of the Settings API. Use this to define new settings sections for an admin page.
	 * Show settings sections in your admin page callback function with do_settings_sections().
	 * Add settings fields to your section with add_settings_field()
	 *
	 * The $callback argument should be the name of a function that echoes out any
	 * content you want to show at the top of the settings section before the actual
	 * fields. It can output nothing if you want.
	 *
	 * @since 2.7.0
	 *
	 * @global $wp_settings_sections Storage array of all settings sections added to admin pages
	 *
	 * @param string   $id       Slug-name to identify the section. Used in the 'id' attribute of tags.
	 * @param string   $title    Formatted title of the section. Shown as the heading for the section.
	 * @param callable $callback Function that echos out any content at the top of the section (between heading and fields).
	 * @param string   $page     The slug-name of the settings page on which to show the section. Built-in pages include
	 *                           'general', 'reading', 'writing', 'discussion', 'media', etc. Create your own using
	 *                           add_options_page();
	 */
	function add_settings_section($id, $title, $callback, $page) {
		global $wp_settings_sections;

		if ( 'misc' == $page ) {
			_deprecated_argument( __FUNCTION__, '3.0.0', sprintf( __( 'The "%s" options group has been removed. Use another settings group.' ), 'misc' ) );
			$page = 'general';
		}

		if ( 'privacy' == $page ) {
			_deprecated_argument( __FUNCTION__, '3.5.0', sprintf( __( 'The "%s" options group has been removed. Use another settings group.' ), 'privacy' ) );
			$page = 'reading';
		}

		$wp_settings_sections[$page][$id] = array('id' => $id, 'title' => $title, 'callback' => $callback);
	}

	/**
	 * Add a new field to a section of a settings page
	 *
	 * Part of the Settings API. Use this to define a settings field that will show
	 * as part of a settings section inside a settings page. The fields are shown using
	 * do_settings_fields() in do_settings-sections()
	 *
	 * The $callback argument should be the name of a function that echoes out the
	 * html input tags for this setting field. Use get_option() to retrieve existing
	 * values to show.
	 *
	 * @since 2.7.0
	 * @since 4.2.0 The `$class` argument was added.
	 *
	 * @global $wp_settings_fields Storage array of settings fields and info about their pages/sections
	 *
	 * @param string   $id       Slug-name to identify the field. Used in the 'id' attribute of tags.
	 * @param string   $title    Formatted title of the field. Shown as the label for the field
	 *                           during output.
	 * @param callable $callback Function that fills the field with the desired form inputs. The
	 *                           function should echo its output.
	 * @param string   $page     The slug-name of the settings page on which to show the section
	 *                           (general, reading, writing, ...).
	 * @param string   $section  Optional. The slug-name of the section of the settings page
	 *                           in which to show the box. Default 'default'.
	 * @param array    $args {
	 *     Optional. Extra arguments used when outputting the field.
	 *
	 *     @type string $label_for When supplied, the setting title will be wrapped
	 *                             in a `<label>` element, its `for` attribute populated
	 *                             with this value.
	 *     @type string $class     CSS Class to be added to the `<tr>` element when the
	 *                             field is output.
	 * }
	 */
	function add_settings_field($id, $title, $callback, $page, $section = 'default', $args = array()) {
		global $wp_settings_fields;

		if ( 'misc' == $page ) {
			_deprecated_argument( __FUNCTION__, '3.0.0', __( 'The miscellaneous options group has been removed. Use another settings group.' ) );
			$page = 'general';
		}

		if ( 'privacy' == $page ) {
			_deprecated_argument( __FUNCTION__, '3.5.0', __( 'The privacy options group has been removed. Use another settings group.' ) );
			$page = 'reading';
		}

		$wp_settings_fields[$page][$section][$id] = array('id' => $id, 'title' => $title, 'callback' => $callback, 'args' => $args);
	}

	/**
	 * Prints out all settings sections added to a particular settings page
	 *
	 * Part of the Settings API. Use this in a settings page callback function
	 * to output all the sections and fields that were added to that $page with
	 * add_settings_section() and add_settings_field()
	 *
	 * @global $wp_settings_sections Storage array of all settings sections added to admin pages
	 * @global $wp_settings_fields Storage array of settings fields and info about their pages/sections
	 * @since 2.7.0
	 *
	 * @param string $page The slug name of the page whose settings sections you want to output
	 */
	function do_settings_sections( $page ) {
		global $wp_settings_sections, $wp_settings_fields;

		if ( ! isset( $wp_settings_sections[$page] ) )
			return;

		foreach ( (array) $wp_settings_sections[$page] as $section ) {
			if ( $section['title'] )
				echo "<h2>{$section['title']}</h2>\n";

			if ( $section['callback'] )
				call_user_func( $section['callback'], $section );

			if ( ! isset( $wp_settings_fields ) || !isset( $wp_settings_fields[$page] ) || !isset( $wp_settings_fields[$page][$section['id']] ) )
				continue;
			echo '<table class="form-table">';
			do_settings_fields( $page, $section['id'] );
			echo '</table>';
		}
	}

	/**
	 * Print out the settings fields for a particular settings section
	 *
	 * Part of the Settings API. Use this in a settings page to output
	 * a specific section. Should normally be called by do_settings_sections()
	 * rather than directly.
	 *
	 * @global $wp_settings_fields Storage array of settings fields and their pages/sections
	 *
	 * @since 2.7.0
	 *
	 * @param string $page Slug title of the admin page who's settings fields you want to show.
	 * @param string $section Slug title of the settings section who's fields you want to show.
	 */
	function do_settings_fields($page, $section) {
		global $wp_settings_fields;

		if ( ! isset( $wp_settings_fields[$page][$section] ) )
			return;

		foreach ( (array) $wp_settings_fields[$page][$section] as $field ) {
			$class = '';

			if ( ! empty( $field['args']['class'] ) ) {
				$class = ' class="' . esc_attr( $field['args']['class'] ) . '"';
			}

			echo "<tr{$class}>";

			if ( ! empty( $field['args']['label_for'] ) ) {
				echo '<th scope="row"><label for="' . esc_attr( $field['args']['label_for'] ) . '">' . $field['title'] . '</label></th>';
			} else {
				echo '<th scope="row">' . $field['title'] . '</th>';
			}

			echo '<td>';
			call_user_func($field['callback'], $field['args']);
			echo '</td>';
			echo '</tr>';
		}
	}

	/**
	 * Register a settings error to be displayed to the user
	 *
	 * Part of the Settings API. Use this to show messages to users about settings validation
	 * problems, missing settings or anything else.
	 *
	 * Settings errors should be added inside the $sanitize_callback function defined in
	 * register_setting() for a given setting to give feedback about the submission.
	 *
	 * By default messages will show immediately after the submission that generated the error.
	 * Additional calls to settings_errors() can be used to show errors even when the settings
	 * page is first accessed.
	 *
	 * @since 3.0.0
	 *
	 * @global array $wp_settings_errors Storage array of errors registered during this pageload
	 *
	 * @param string $setting Slug title of the setting to which this error applies
	 * @param string $code    Slug-name to identify the error. Used as part of 'id' attribute in HTML output.
	 * @param string $message The formatted message text to display to the user (will be shown inside styled
	 *                        `<div>` and `<p>` tags).
	 * @param string $type    Optional. Message type, controls HTML class. Accepts 'error' or 'updated'.
	 *                        Default 'error'.
	 */
	function add_settings_error( $setting, $code, $message, $type = 'error' ) {
		global $wp_settings_errors;

		$wp_settings_errors[] = array(
			'setting' => $setting,
			'code'    => $code,
			'message' => $message,
			'type'    => $type
		);
	}

	/**
	 * Fetch settings errors registered by add_settings_error()
	 *
	 * Checks the $wp_settings_errors array for any errors declared during the current
	 * pageload and returns them.
	 *
	 * If changes were just submitted ($_GET['settings-updated']) and settings errors were saved
	 * to the 'settings_errors' transient then those errors will be returned instead. This
	 * is used to pass errors back across pageloads.
	 *
	 * Use the $sanitize argument to manually re-sanitize the option before returning errors.
	 * This is useful if you have errors or notices you want to show even when the user
	 * hasn't submitted data (i.e. when they first load an options page, or in the {@see 'admin_notices'}
	 * action hook).
	 *
	 * @since 3.0.0
	 *
	 * @global array $wp_settings_errors Storage array of errors registered during this pageload
	 *
	 * @param string $setting Optional slug title of a specific setting who's errors you want.
	 * @param boolean $sanitize Whether to re-sanitize the setting value before returning errors.
	 * @return array Array of settings errors
	 */
	function get_settings_errors( $setting = '', $sanitize = false ) {
		global $wp_settings_errors;

		/*
		 * If $sanitize is true, manually re-run the sanitization for this option
		 * This allows the $sanitize_callback from register_setting() to run, adding
		 * any settings errors you want to show by default.
		 */
		if ( $sanitize )
			sanitize_option( $setting, get_option( $setting ) );

		// If settings were passed back from options.php then use them.
		if ( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] && get_transient( 'settings_errors' ) ) {
			$wp_settings_errors = array_merge( (array) $wp_settings_errors, get_transient( 'settings_errors' ) );
			delete_transient( 'settings_errors' );
		}

		// Check global in case errors have been added on this pageload.
		if ( ! count( $wp_settings_errors ) )
			return array();

		// Filter the results to those of a specific setting if one was set.
		if ( $setting ) {
			$setting_errors = array();
			foreach ( (array) $wp_settings_errors as $key => $details ) {
				if ( $setting == $details['setting'] )
					$setting_errors[] = $wp_settings_errors[$key];
			}
			return $setting_errors;
		}

		return $wp_settings_errors;
	}

	/**
	 * Display settings errors registered by add_settings_error().
	 *
	 * Part of the Settings API. Outputs a div for each error retrieved by
	 * get_settings_errors().
	 *
	 * This is called automatically after a settings page based on the
	 * Settings API is submitted. Errors should be added during the validation
	 * callback function for a setting defined in register_setting().
	 *
	 * The $sanitize option is passed into get_settings_errors() and will
	 * re-run the setting sanitization
	 * on its current value.
	 *
	 * The $hide_on_update option will cause errors to only show when the settings
	 * page is first loaded. if the user has already saved new values it will be
	 * hidden to avoid repeating messages already shown in the default error
	 * reporting after submission. This is useful to show general errors like
	 * missing settings when the user arrives at the settings page.
	 *
	 * @since 3.0.0
	 *
	 * @param string $setting        Optional slug title of a specific setting who's errors you want.
	 * @param bool   $sanitize       Whether to re-sanitize the setting value before returning errors.
	 * @param bool   $hide_on_update If set to true errors will not be shown if the settings page has
	 *                               already been submitted.
	 */
	function settings_errors( $setting = '', $sanitize = false, $hide_on_update = false ) {

		if ( $hide_on_update && ! empty( $_GET['settings-updated'] ) )
			return;

		$settings_errors = get_settings_errors( $setting, $sanitize );

		if ( empty( $settings_errors ) )
			return;

		$output = '';
		foreach ( $settings_errors as $key => $details ) {
			$css_id = 'setting-error-' . $details['code'];
			$css_class = $details['type'] . ' settings-error notice is-dismissible';
			$output .= "<div id='$css_id' class='$css_class'> \n";
			$output .= "<p><strong>{$details['message']}</strong></p>";
			$output .= "</div> \n";
		}
		echo $output;
	}

	/**
	 * Outputs the modal window used for attaching media to posts or pages in the media-listing screen.
	 *
	 * @since 2.7.0
	 *
	 * @param string $found_action
	 */
	function find_posts_div($found_action = '') {
	?>
		<div id="find-posts" class="find-box" style="display: none;">
			<div id="find-posts-head" class="find-box-head">
				<?php _e( 'Attach to existing content' ); ?>
				<button type="button" id="find-posts-close"><span class="screen-reader-text"><?php _e( 'Close media attachment panel' ); ?></button>
			</div>
			<div class="find-box-inside">
				<div class="find-box-search">
					<?php if ( $found_action ) { ?>
						<input type="hidden" name="found_action" value="<?php echo esc_attr($found_action); ?>" />
					<?php } ?>
					<input type="hidden" name="affected" id="affected" value="" />
					<?php wp_nonce_field( 'find-posts', '_ajax_nonce', false ); ?>
					<label class="screen-reader-text" for="find-posts-input"><?php _e( 'Search' ); ?></label>
					<input type="text" id="find-posts-input" name="ps" value="" />
					<span class="spinner"></span>
					<input type="button" id="find-posts-search" value="<?php esc_attr_e( 'Search' ); ?>" class="button" />
					<div class="clear"></div>
				</div>
				<div id="find-posts-response"></div>
			</div>
			<div class="find-box-buttons">
				<?php submit_button( __( 'Select' ), 'primary alignright', 'find-posts-submit', false ); ?>
				<div class="clear"></div>
			</div>
		</div>
	<?php
	}

	/**
	 * Displays the post password.
	 *
	 * The password is passed through esc_attr() to ensure that it is safe for placing in an html attribute.
	 *
	 * @since 2.7.0
	 */
	function the_post_password() {
		$post = get_post();
		if ( isset( $post->post_password ) )
			echo esc_attr( $post->post_password );
	}

	/**
	 * Get the post title.
	 *
	 * The post title is fetched and if it is blank then a default string is
	 * returned.
	 *
	 * @since 2.7.0
	 *
	 * @param int|WP_Post $post Optional. Post ID or WP_Post object. Default is global $post.
	 * @return string The post title if set.
	 */
	function _draft_or_post_title( $post = 0 ) {
		$title = get_the_title( $post );
		if ( empty( $title ) )
			$title = __( '(no title)' );
		return esc_html( $title );
	}

	/**
	 * Displays the search query.
	 *
	 * A simple wrapper to display the "s" parameter in a `GET` URI. This function
	 * should only be used when the_search_query() cannot.
	 *
	 * @since 2.7.0
	 */
	function _admin_search_query() {
		echo isset($_REQUEST['s']) ? esc_attr( wp_unslash( $_REQUEST['s'] ) ) : '';
	}

	/**
	 * Generic Iframe header for use with Thickbox
	 *
	 * @since 2.7.0
	 *
	 * @global string    $hook_suffix
	 * @global string    $admin_body_class
	 * @global WP_Locale $wp_locale
	 *
	 * @param string $title      Optional. Title of the Iframe page. Default empty.
	 * @param bool   $deprecated Not used.
	 */
	function iframe_header( $title = '', $deprecated = false ) {
		show_admin_bar( false );
		global $hook_suffix, $admin_body_class, $wp_locale;
		$admin_body_class = preg_replace('/[^a-z0-9_-]+/i', '-', $hook_suffix);

		$current_screen = get_current_screen();

		@header( 'Content-Type: ' . get_option( 'html_type' ) . '; charset=' . get_option( 'blog_charset' ) );
		_wp_admin_html_begin();
	?>
	<title><?php bloginfo('name') ?> &rsaquo; <?php echo $title ?> &#8212; <?php _e('WordPress'); ?></title>
	<?php
	wp_enqueue_style( 'colors' );
	?>
	<script type="text/javascript">
	addLoadEvent = function(func){if(typeof jQuery!="undefined")jQuery(document).ready(func);else if(typeof wpOnload!='function'){wpOnload=func;}else{var oldonload=wpOnload;wpOnload=function(){oldonload();func();}}};
	function tb_close(){var win=window.dialogArguments||opener||parent||top;win.tb_remove();}
	var ajaxurl = '<?php echo admin_url( 'admin-ajax.php', 'relative' ); ?>',
		pagenow = '<?php echo $current_screen->id; ?>',
		typenow = '<?php echo $current_screen->post_type; ?>',
		adminpage = '<?php echo $admin_body_class; ?>',
		thousandsSeparator = '<?php echo addslashes( $wp_locale->number_format['thousands_sep'] ); ?>',
		decimalPoint = '<?php echo addslashes( $wp_locale->number_format['decimal_point'] ); ?>',
		isRtl = <?php echo (int) is_rtl(); ?>;
	</script>
	<?php
	/** This action is documented in wp-admin/admin-header.php */
	do_action( 'admin_enqueue_scripts', $hook_suffix );

	/** This action is documented in wp-admin/admin-header.php */
	do_action( "admin_print_styles-$hook_suffix" );

	/** This action is documented in wp-admin/admin-header.php */
	do_action( 'admin_print_styles' );

	/** This action is documented in wp-admin/admin-header.php */
	do_action( "admin_print_scripts-$hook_suffix" );

	/** This action is documented in wp-admin/admin-header.php */
	do_action( 'admin_print_scripts' );

	/** This action is documented in wp-admin/admin-header.php */
	do_action( "admin_head-$hook_suffix" );

	/** This action is documented in wp-admin/admin-header.php */
	do_action( 'admin_head' );

	$admin_body_class .= ' locale-' . sanitize_html_class( strtolower( str_replace( '_', '-', get_user_locale() ) ) );

	if ( is_rtl() )
		$admin_body_class .= ' rtl';

	?>
	</head>
	<?php
	/** This filter is documented in wp-admin/admin-header.php */
	$admin_body_classes = apply_filters( 'admin_body_class', '' );
	?>
	<body<?php
	/**
	 * @global string $body_id
	 */
	if ( isset($GLOBALS['body_id']) ) echo ' id="' . $GLOBALS['body_id'] . '"'; ?> class="wp-admin wp-core-ui no-js iframe <?php echo $admin_body_classes . ' ' . $admin_body_class; ?>">
	<script type="text/javascript">
	(function(){
	var c = document.body.className;
	c = c.replace(/no-js/, 'js');
	document.body.className = c;
	})();
	</script>
	<?php
	}

	/**
	 * Generic Iframe footer for use with Thickbox
	 *
	 * @since 2.7.0
	 */
	function iframe_footer() {
		/*
		 * We're going to hide any footer output on iFrame pages,
		 * but run the hooks anyway since they output JavaScript
		 * or other needed content.
		 */

		/**
		 * @global string $hook_suffix
		 */
		global $hook_suffix;
		?>
		<div class="hidden">
	<?php
		/** This action is documented in wp-admin/admin-footer.php */
		do_action( 'admin_footer', $hook_suffix );

		/** This action is documented in wp-admin/admin-footer.php */
		do_action( "admin_print_footer_scripts-$hook_suffix" );

		/** This action is documented in wp-admin/admin-footer.php */
		do_action( 'admin_print_footer_scripts' );
	?>
		</div>
	<script type="text/javascript">if(typeof wpOnload=="function")wpOnload();</script>
	</body>
	</html>
	<?php
	}

	/**
	 *
	 * @param WP_Post $post
	 */
	function _post_states($post) {
		$post_states = array();
		if ( isset( $_REQUEST['post_status'] ) )
			$post_status = $_REQUEST['post_status'];
		else
			$post_status = '';

		if ( !empty($post->post_password) )
			$post_states['protected'] = __('Password protected');
		if ( 'private' == $post->post_status && 'private' != $post_status )
			$post_states['private'] = __('Private');
		if ( 'draft' == $post->post_status && 'draft' != $post_status )
			$post_states['draft'] = __('Draft');
		if ( 'pending' == $post->post_status && 'pending' != $post_status )
			$post_states['pending'] = _x('Pending', 'post status');
		if ( is_sticky($post->ID) )
			$post_states['sticky'] = __('Sticky');

		if ( 'future' === $post->post_status ) {
			$post_states['scheduled'] = __( 'Scheduled' );
		}

		if ( 'page' === get_option( 'show_on_front' ) ) {
			if ( intval( get_option( 'page_on_front' ) ) === $post->ID ) {
				$post_states['page_on_front'] = __( 'Front Page' );
			}

			if ( intval( get_option( 'page_for_posts' ) ) === $post->ID ) {
				$post_states['page_for_posts'] = __( 'Posts Page' );
			}
		}

		/**
		 * Filters the default post display states used in the posts list table.
		 *
		 * @since 2.8.0
		 *
		 * @param array   $post_states An array of post display states.
		 * @param WP_Post $post        The current post object.
		 */
		$post_states = apply_filters( 'display_post_states', $post_states, $post );

		if ( ! empty($post_states) ) {
			$state_count = count($post_states);
			$i = 0;
			echo ' &mdash; ';
			foreach ( $post_states as $state ) {
				++$i;
				( $i == $state_count ) ? $sep = '' : $sep = ', ';
				echo "<span class='post-state'>$state$sep</span>";
			}
		}

	}

	/**
	 *
	 * @param WP_Post $post
	 */
	function _media_states( $post ) {
		$media_states = array();
		$stylesheet = get_option('stylesheet');

		if ( current_theme_supports( 'custom-header') ) {
			$meta_header = get_post_meta($post->ID, '_wp_attachment_is_custom_header', true );

			if ( is_random_header_image() ) {
				$header_images = wp_list_pluck( get_uploaded_header_images(), 'attachment_id' );

				if ( $meta_header == $stylesheet && in_array( $post->ID, $header_images ) ) {
					$media_states[] = __( 'Header Image' );
				}
			} else {
				$header_image = get_header_image();

				// Display "Header Image" if the image was ever used as a header image
				if ( ! empty( $meta_header ) && $meta_header == $stylesheet && $header_image !== wp_get_attachment_url( $post->ID ) ) {
					$media_states[] = __( 'Header Image' );
				}

				// Display "Current Header Image" if the image is currently the header image
				if ( $header_image && $header_image == wp_get_attachment_url( $post->ID ) ) {
					$media_states[] = __( 'Current Header Image' );
				}
			}
		}

		if ( current_theme_supports( 'custom-background') ) {
			$meta_background = get_post_meta($post->ID, '_wp_attachment_is_custom_background', true );

			if ( ! empty( $meta_background ) && $meta_background == $stylesheet ) {
				$media_states[] = __( 'Background Image' );

				$background_image = get_background_image();
				if ( $background_image && $background_image == wp_get_attachment_url( $post->ID ) ) {
					$media_states[] = __( 'Current Background Image' );
				}
			}
		}

		if ( $post->ID == get_option( 'site_icon' ) ) {
			$media_states[] = __( 'Site Icon' );
		}

		if ( $post->ID == get_theme_mod( 'site_logo' ) ) {
			$media_states[] = __( 'Logo' );
		}

		/**
		 * Filters the default media display states for items in the Media list table.
		 *
		 * @since 3.2.0
		 *
		 * @param array $media_states An array of media states. Default 'Header Image',
		 *                            'Background Image', 'Site Icon', 'Logo'.
		 */
		$media_states = apply_filters( 'display_media_states', $media_states );

		if ( ! empty( $media_states ) ) {
			$state_count = count( $media_states );
			$i = 0;
			echo ' &mdash; ';
			foreach ( $media_states as $state ) {
				++$i;
				( $i == $state_count ) ? $sep = '' : $sep = ', ';
				echo "<span class='post-state'>$state$sep</span>";
			}
		}
	}

	/**
	 * Test support for compressing JavaScript from PHP
	 *
	 * Outputs JavaScript that tests if compression from PHP works as expected
	 * and sets an option with the result. Has no effect when the current user
	 * is not an administrator. To run the test again the option 'can_compress_scripts'
	 * has to be deleted.
	 *
	 * @since 2.8.0
	 */
	function compression_test() {
	?>
		<script type="text/javascript">
		var compressionNonce = <?php echo wp_json_encode( wp_create_nonce( 'update_can_compress_scripts' ) ); ?>;
		var testCompression = {
			get : function(test) {
				var x;
				if ( window.XMLHttpRequest ) {
					x = new XMLHttpRequest();
				} else {
					try{x=new ActiveXObject('Msxml2.XMLHTTP');}catch(e){try{x=new ActiveXObject('Microsoft.XMLHTTP');}catch(e){};}
				}

				if (x) {
					x.onreadystatechange = function() {
						var r, h;
						if ( x.readyState == 4 ) {
							r = x.responseText.substr(0, 18);
							h = x.getResponseHeader('Content-Encoding');
							testCompression.check(r, h, test);
						}
					};

					x.open('GET', ajaxurl + '?action=wp-compression-test&test='+test+'&_ajax_nonce='+compressionNonce+'&'+(new Date()).getTime(), true);
					x.send('');
				}
			},

			check : function(r, h, test) {
				if ( ! r && ! test )
					this.get(1);

				if ( 1 == test ) {
					if ( h && ( h.match(/deflate/i) || h.match(/gzip/i) ) )
						this.get('no');
					else
						this.get(2);

					return;
				}

				if ( 2 == test ) {
					if ( '"wpCompressionTest' == r )
						this.get('yes');
					else
						this.get('no');
				}
			}
		};
		testCompression.check();
		</script>
	<?php
	}

	/**
	 * Echoes a submit button, with provided text and appropriate class(es).
	 *
	 * @since 3.1.0
	 *
	 * @see get_submit_button()
	 *
	 * @param string       $text             The text of the button (defaults to 'Save Changes')
	 * @param string       $type             Optional. The type and CSS class(es) of the button. Core values
	 *                                       include 'primary', 'secondary', 'delete'. Default 'primary'
	 * @param string       $name             The HTML name of the submit button. Defaults to "submit". If no
	 *                                       id attribute is given in $other_attributes below, $name will be
	 *                                       used as the button's id.
	 * @param bool         $wrap             True if the output button should be wrapped in a paragraph tag,
	 *                                       false otherwise. Defaults to true
	 * @param array|string $other_attributes Other attributes that should be output with the button, mapping
	 *                                       attributes to their values, such as setting tabindex to 1, etc.
	 *                                       These key/value attribute pairs will be output as attribute="value",
	 *                                       where attribute is the key. Other attributes can also be provided
	 *                                       as a string such as 'tabindex="1"', though the array format is
	 *                                       preferred. Default null.
	 */
	function submit_button( $text = null, $type = 'primary', $name = 'submit', $wrap = true, $other_attributes = null ) {
		echo get_submit_button( $text, $type, $name, $wrap, $other_attributes );
	}

	/**
	 * Returns a submit button, with provided text and appropriate class
	 *
	 * @since 3.1.0
	 *
	 * @param string       $text             Optional. The text of the button. Default 'Save Changes'.
	 * @param string       $type             Optional. The type of button. Accepts 'primary', 'secondary',
	 *                                       or 'delete'. Default 'primary large'.
	 * @param string       $name             Optional. The HTML name of the submit button. Defaults to "submit".
	 *                                       If no id attribute is given in $other_attributes below, `$name` will
	 *                                       be used as the button's id. Default 'submit'.
	 * @param bool         $wrap             Optional. True if the output button should be wrapped in a paragraph
	 *                                       tag, false otherwise. Default true.
	 * @param array|string $other_attributes Optional. Other attributes that should be output with the button,
	 *                                       mapping attributes to their values, such as `array( 'tabindex' => '1' )`.
	 *                                       These attributes will be output as `attribute="value"`, such as
	 *                                       `tabindex="1"`. Other attributes can also be provided as a string such
	 *                                       as `tabindex="1"`, though the array format is typically cleaner.
	 *                                       Default empty.
	 * @return string Submit button HTML.
	 */
	function get_submit_button( $text = '', $type = 'primary large', $name = 'submit', $wrap = true, $other_attributes = '' ) {
		if ( ! is_array( $type ) )
			$type = explode( ' ', $type );

		$button_shorthand = array( 'primary', 'small', 'large' );
		$classes = array( 'button' );
		foreach ( $type as $t ) {
			if ( 'secondary' === $t || 'button-secondary' === $t )
				continue;
			$classes[] = in_array( $t, $button_shorthand ) ? 'button-' . $t : $t;
		}
		// Remove empty items, remove duplicate items, and finally build a string.
		$class = implode( ' ', array_unique( array_filter( $classes ) ) );

		$text = $text ? $text : __( 'Save Changes' );

		// Default the id attribute to $name unless an id was specifically provided in $other_attributes
		$id = $name;
		if ( is_array( $other_attributes ) && isset( $other_attributes['id'] ) ) {
			$id = $other_attributes['id'];
			unset( $other_attributes['id'] );
		}

		$attributes = '';
		if ( is_array( $other_attributes ) ) {
			foreach ( $other_attributes as $attribute => $value ) {
				$attributes .= $attribute . '="' . esc_attr( $value ) . '" '; // Trailing space is important
			}
		} elseif ( ! empty( $other_attributes ) ) { // Attributes provided as a string
			$attributes = $other_attributes;
		}

		// Don't output empty name and id attributes.
		$name_attr = $name ? ' name="' . esc_attr( $name ) . '"' : '';
		$id_attr = $id ? ' id="' . esc_attr( $id ) . '"' : '';

		$button = '<input type="submit"' . $name_attr . $id_attr . ' class="' . esc_attr( $class );
		$button	.= '" value="' . esc_attr( $text ) . '" ' . $attributes . ' />';

		if ( $wrap ) {
			$button = '<p class="submit">' . $button . '</p>';
		}

		return $button;
	}

	/**
	 *
	 * @global bool $is_IE
	 */
	function _wp_admin_html_begin() {
		global $is_IE;

		$admin_html_class = ( is_admin_bar_showing() ) ? 'wp-toolbar' : '';

		if ( $is_IE )
			@header('X-UA-Compatible: IE=edge');

	?>
	<!DOCTYPE html>
	<!--[if IE 8]>
	<html xmlns="http://www.w3.org/1999/xhtml" class="ie8 <?php echo $admin_html_class; ?>" <?php
		/**
		 * Fires inside the HTML tag in the admin header.
		 *
		 * @since 2.2.0
		 */
		do_action( 'admin_xml_ns' );
	?> <?php language_attributes(); ?>>
	<![endif]-->
	<!--[if !(IE 8) ]><!-->
	<html xmlns="http://www.w3.org/1999/xhtml" class="<?php echo $admin_html_class; ?>" <?php
		/** This action is documented in wp-admin/includes/template.php */
		do_action( 'admin_xml_ns' );
	?> <?php language_attributes(); ?>>
	<!--<![endif]-->
	<head>
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_option('blog_charset'); ?>" />
	<?php
	}

	/**
	 * Convert a screen string to a screen object
	 *
	 * @since 3.0.0
	 *
	 * @param string $hook_name The hook name (also known as the hook suffix) used to determine the screen.
	 * @return WP_Screen Screen object.
	 */
	function convert_to_screen( $hook_name ) {
		if ( ! class_exists( 'WP_Screen' ) ) {
			_doing_it_wrong( 'convert_to_screen(), add_meta_box()', __( "Likely direct inclusion of wp-admin/includes/template.php in order to use add_meta_box(). This is very wrong. Hook the add_meta_box() call into the add_meta_boxes action instead." ), '3.3.0' );
			return (object) array( 'id' => '_invalid', 'base' => '_are_belong_to_us' );
		}

		return WP_Screen::get( $hook_name );
	}

	/**
	 * Output the HTML for restoring the post data from DOM storage
	 *
	 * @since 3.6.0
	 * @access private
	 */
	function _local_storage_notice() {
		?>
		<div id="local-storage-notice" class="hidden notice is-dismissible">
		<p class="local-restore">
			<?php _e( 'The backup of this post in your browser is different from the version below.' ); ?>
			<button type="button" class="button restore-backup"><?php _e('Restore the backup'); ?></button>
		</p>
		<p class="help">
			<?php _e( 'This will replace the current editor content with the last backup version. You can use undo and redo in the editor to get the old content back or to return to the restored version.' ); ?>
		</p>
		</div>
		<?php
	}

	/**
	 * Output a HTML element with a star rating for a given rating.
	 *
	 * Outputs a HTML element with the star rating exposed on a 0..5 scale in
	 * half star increments (ie. 1, 1.5, 2 stars). Optionally, if specified, the
	 * number of ratings may also be displayed by passing the $number parameter.
	 *
	 * @since 3.8.0
	 * @since 4.4.0 Introduced the `echo` parameter.
	 *
	 * @param array $args {
	 *     Optional. Array of star ratings arguments.
	 *
	 *     @type int    $rating The rating to display, expressed in either a 0.5 rating increment,
	 *                          or percentage. Default 0.
	 *     @type string $type   Format that the $rating is in. Valid values are 'rating' (default),
	 *                          or, 'percent'. Default 'rating'.
	 *     @type int    $number The number of ratings that makes up this rating. Default 0.
	 *     @type bool   $echo   Whether to echo the generated markup. False to return the markup instead
	 *                          of echoing it. Default true.
	 * }
	 */
	function wp_star_rating( $args = array() ) {
		$defaults = array(
			'rating' => 0,
			'type'   => 'rating',
			'number' => 0,
			'echo'   => true,
		);
		$r = wp_parse_args( $args, $defaults );

		// Non-english decimal places when the $rating is coming from a string
		$rating = str_replace( ',', '.', $r['rating'] );

		// Convert Percentage to star rating, 0..5 in .5 increments
		if ( 'percent' == $r['type'] ) {
			$rating = round( $rating / 10, 0 ) / 2;
		}

		// Calculate the number of each type of star needed
		$full_stars = floor( $rating );
		$half_stars = ceil( $rating - $full_stars );
		$empty_stars = 5 - $full_stars - $half_stars;

		if ( $r['number'] ) {
			/* translators: 1: The rating, 2: The number of ratings */
			$format = _n( '%1$s rating based on %2$s rating', '%1$s rating based on %2$s ratings', $r['number'] );
			$title = sprintf( $format, number_format_i18n( $rating, 1 ), number_format_i18n( $r['number'] ) );
		} else {
			/* translators: 1: The rating */
			$title = sprintf( __( '%s rating' ), number_format_i18n( $rating, 1 ) );
		}

		$output = '<div class="star-rating">';
		$output .= '<span class="screen-reader-text">' . $title . '</span>';
		$output .= str_repeat( '<div class="star star-full" aria-hidden="true"></div>', $full_stars );
		$output .= str_repeat( '<div class="star star-half" aria-hidden="true"></div>', $half_stars );
		$output .= str_repeat( '<div class="star star-empty" aria-hidden="true"></div>', $empty_stars );
		$output .= '</div>';

		if ( $r['echo'] ) {
			echo $output;
		}

		return $output;
	}

	/**
	 * Output a notice when editing the page for posts (internal use only).
	 *
	 * @ignore
	 * @since 4.2.0
	 */
	function _wp_posts_page_notice() {
		echo '<div class="notice notice-warning inline"><p>' . __( 'You are currently editing the page that shows your latest posts.' ) . '</p></div>';
	}
}
