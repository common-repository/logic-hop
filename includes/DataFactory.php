<?php

/**
 * @since      3.5.6
 * @package    LogicHop
 * @subpackage LogicHop/includes
 */
namespace LogicHop;

class DataFactory {

	/**
	 * @since    3.5.6
	 * @access   protected
	 * @var      object    $logic    Logic Hop core
	 */
	protected $logic = null;

	/**
	 * @since    3.5.6
	 * @access   protected
	 * @var      object    $data    Data storage
	 */
	protected $data = null;

	/**
	 * @since    3.5.6
	 * @access   private
	 * @var      object    $prefix    Transient name prefix
	 */
	private $prefix = 'logichop_';

	/**
	 * Initialize
	 *
	 * @since    3.5.6
	 */
	public function __construct ( $logic ) {
		$this->logic = $logic;
	}

	/**
	 * Has the data object been created
	 *
	 * @return	boolean 	Data object created
	 * @since    3.5.6
	 */
	public function has_data () {
		if ( is_null( $this->data ) ) {
			return false;
		}
		return true;
	}

	/**
	 * Get data object
	 *
	 * @return	object 	Data object
	 * @since    3.5.6
	 */
	public function get_data () {
		return $this->data;
	}

	/**
	 * Get data value
	 *
	 * @param		string    $key		Data object key
	 * @return	integer|string|array|object 	Data value
	 * @since    3.5.6
	 */
	public function get_value ( $key ) {
		if ( $this->has_data() ) {
			if ( isset( $this->data->{$key} ) ) {
				return $this->data->{$key};
			}
		}
		return null;
	}

	/**
	 * Set data value
	 *
	 * @param		string		$key		Data object key
	 * @param		integer|string|array|object 	$value		Data value
	 * @param		boolean 	$save		Save as transient after setting
	 * @return	boolean 	Status
	 * @since    3.5.6
	 */
	public function set_value ( $key, $value, $save = true ) {
		if ( $this->has_data() ) {
			if ( preg_match( '/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $key ) ) {
				try {
					$this->data->{$key} = $value;

					if ( $save ) {
						$this->transient_save();
					}
					return true;
				} catch ( exception $e ) {
					return false;
				}
			}
		}
		return false;
	}

	/**
	 * Set data value
	 *
	 * @param		string		$key		Data object key
	 * @param		integer|string		$index		Array index
	 * @param		integer|string|array|object 	$value		Data value
	 * @param		boolean 	$save		Save as transient after setting
	 * @return	boolean 	Status
	 * @since    3.5.6
	 */
	public function set_array_value ( $key, $index, $value, $save = true ) {
		if ( $this->has_data() ) {
			if ( preg_match( '/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $key ) && $index ) {
				try {
					$this->data->{$key}[$index] = $value;

					if ( $save ) {
						$this->transient_save();
					}
					return true;
				} catch ( exception $e ) {
					return false;
				}
			}
		}
		return false;
	}

	/**
	 * Set data value
	 *
	 * @param		string		$key		Data object key
	 * @param		integer|string		$index		Array index
	 * @param		integer|string|array|object 	$value		Data value
	 * @param		boolean 	$save		Save as transient after setting
	 * @return	boolean 	Status
	 * @since    3.5.6
	 */
	public function set_object_value ( $key, $object, $value, $save = true ) {
		if ( $this->has_data() ) {
			if ( preg_match( '/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $key ) && preg_match( '/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $object ) ) {
				try {

					if ( ! isset( $this->data->{$key} ) ) {
						$this->data->{$key} = new \stdclass;
					}
					if ( ! isset( $this->data->{$key}->{$object} ) ) {
						$this->data->{$key}->{$object} = new \stdclass;
					}

					$this->data->{$key}->{$object} = $value;

					if ( $save ) {
						$this->transient_save();
					}
					return true;
				} catch ( exception $e ) {
					return false;
				}
			}
		}
		return false;
	}

	/**
	 * Set custom data value
	 *
	 * @param		string		$key		Data object key
	 * @param		integer|string|array|object 	$value		Data value
	 * @param		boolean 	$save		Save as transient after setting
	 * @return	boolean 	Status
	 * @since    3.5.6
	 */
	public function set_custom_value ( $key, $value, $save = true ) {
		if ( $this->has_data() ) {
			if ( preg_match( '/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $key ) ) {
				try {
					$this->data->Custom->{$key} = $value;
					if ( $save ) {
						$this->transient_save();
					}
					return true;
				} catch ( exception $e ) {
					return false;
				}
			}
		}
		return false;
	}

	/**
	 * Retrieve transient data
	 *
	 * @return		boolean    Status
	 * @since    3.5.6
	 */
	public function transient_retrieve ( $uid = false ) {

		if ( $uid ) {
			$id = $this->prefix . $uid;
		} else {
			$id = $this->prefix . $this->data->UID;
		}

		$this->data = get_transient( $id );

		return $this->data;
	}

	/**
	 * Save data as transient
	 *
	 * @return		boolean    Status
	 * @since    3.5.6
	 */
	public function transient_save () {

		$saved = false;
		if ( $this->has_data() ) {
			$id = $this->prefix . $this->data->UID;
			$saved = set_transient( $id, $this->data, HOUR_IN_SECONDS );
		}

		return $saved;
	}

	/**
	 * Store retrieved data
	 * Typically updated when data is retrieved from API/Transients
	 *
	 * @param		string    $data		Data object key
	 * @since    3.5.6
	 */
	public function store_retrieved_data ( $data ) {

		$this->data->FirstVisit = false;

		if ( isset( $data['TotalVisits'] ) ) {
			$_total_visits = ( int ) $data['TotalVisits'];
			$this->data->TotalVisits = $_total_visits + 1;
			$this->data->UpdateVisits = true;
		}

		if ( isset( $data['Source'] ) ) {
			$this->data->Source	= $data['Source'];
		}

		if ( isset( $data['LandingPage'] ) ) {
			$this->data->LandingPage = $data['LandingPage'];
		}

		if ( isset( $data['LeadScore'] ) ) {
			$this->data->LeadScore = ( int ) $data['LeadScore'];
		}

		if ( isset( $data['Pages'] ) ) {
			$this->data->Pages = ( array ) $data['Pages'];
		}

		if ( isset( $data['Goals'] ) ) {
			$goals = array();
			$goals_tmp = ( array ) $data['Goals'];

			foreach ( $goals_tmp as $k => $v ) {
				if ( $v > 0 ) {
						$goals[$k] = $v;
				}
			}

			$this->data->Goals = $goals;
		}

		if ( isset( $data['Location'] ) ) {
			$location = ( array ) $data['Location'];
			$this->data->Location = ( object ) $location;
		}

		if ( isset( $data['FirstDate'] ) ) {
			$this->data->Timestamp->FirstVisit = $data['FirstDate'];
		}

		if ( isset( $data['LastDate'] ) ) {
			$this->data->Timestamp->LastVisit	= $data['LastDate'];
		}

		if ( isset( $data['category'] ) ) {
			foreach ( $data['category'] as $key => $value ) {
				$this->data->Categories[$key] = $value;
			}
		}

		if ( isset( $data['tag'] ) ) {
			foreach ( $data['tag'] as $key => $value ) {
				$this->data->Tags[$key] = $value;
			}
		}

		$this->transient_save();
	}

	/**
	 * Update user data on page load.
	 * Does not update for admin section.
	 *
	 * @since    1.0.0
	 * @param      	string    	$pid		Page ID
	 * @param      	string    	$referrer	Referring URL
	 */
	public function update_data ( $pid, $referrer ) {
		$data_package = array();

		$this->data->Condition = false;

		$post_id = ( $pid ) ? $pid : $this->logic->wordpress_post_get();
		$post_type = get_post_type( $post_id );

		$this->data->Page = $post_id;
		$this->data->URL = ( isset($_SERVER['REQUEST_URI'] ) ) ? strtok( $_SERVER['REQUEST_URI'],'?' ) : '';

		$this->data->Date = $this->date_object();
		if ( $this->data->Date->Timestamp ) {
			$this->data->Timestamp->LastPage = $this->data->Date->Timestamp;
		}

		if ( $this->data->Timestamp->ThisVisit == '' ) {
			$this->data->Timestamp->ThisVisit = $this->data->Date->Timestamp;
		}
		if ( $this->data->Timestamp->LastPage == '' ) {
			$this->data->Timestamp->LastPage = $this->data->Date->Timestamp;
		}

		if ( ! isset( $_REQUEST['logichop-preview'] ) ) {
			$this->data->Query = $_GET;	// QUERY STRING
			if ( is_array ( $_GET ) && is_array( $this->data->QueryStore ) ) {
				if ( $_GET ) {
					$this->logic->process_querystring_goals( $_GET );
				}
				$this->data->QueryStore	= array_merge( $this->data->QueryStore, $_GET );	// STORE QUERY STRING VARS -- DUPLICATES ARE OVERWRITTEN
			}
			$this->logic->get_referrer_query_string();
		}

		if ( isset( $this->data->Path ) ) {
			array_unshift( $this->data->Path, $post_id ); // TRACK VIEW PATH
		}
		if ( count( $this->data->Path) > $this->logic->path_max ) {
			$this->data->Path = array_slice( $this->data->Path, 0, $this->logic->path_max ); // LIMITS VIEW PATH
		}

		if ($referrer !== false) {
			$this->data->Referrer = $referrer;
		} else {
			if ( isset( $_SERVER['HTTP_REFERER'] ) ) { // CHECK REFERRER
				$this->data->Referrer = $_SERVER['HTTP_REFERER'];
			}
		}

		if ( ! $this->data->Language ) {
			if ( isset( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ) {
				$this->data->Language = substr( $_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2 );
			} else {
				$this->data->Language = 'en';
			}
		}

		if ( ! $this->data->Location ) {
			$geo = new \LogicHop_Geo_IP( $this->logic );
			if ( ! $this->logic->geolocation_disable ) {
				$this->data->Location = $geo->geolocate( $this->data->IP );
			} else {
				// Geolocation disabled
				$this->data->Location = $geo->geo_object();
			}
		}

		if ( isset( $this->data->Location->TimeZone ) ) { // ADD DATE/TIME FOR USER'S LOCATION
			$this->data->UserDate = $this->date_object( null, $this->data->Location->TimeZone );
		} else {
			$this->data->UserDate = $this->data->Date;
		}

		if ( $this->logic->js_tracking() ) {
			$landing_page = ( isset( $_SERVER['HTTP_REFERER'] ) ) ? parse_url( $_SERVER['HTTP_REFERER'], PHP_URL_PATH ) : ''; // AJAX FROM REFERRER --> REMOVE QUERY STRING
			if ( ! isset( $landing_page ) ) {
				$landing_page = '/';
			}
		} else {
			$landing_page = strtok( $_SERVER['REQUEST_URI'], '?' ); // REMOVE QUERY STRING DATA
		}

		if ( ! $this->data->LandingPageSession ) {
			$this->data->LandingPageSession = $landing_page;
		}

		if ( isset( $this->data->FirstVisit) && $this->data->FirstVisit ) {
			if ( $this->data->Source == '' ) {
				$this->data->Timestamp->FirstVisit 	= $this->data->Date->Timestamp;
				$this->data->Timestamp->LastVisit	= $this->data->Date->Timestamp;

				$this->data->LandingPage = $this->data->LandingPageSession; // STORE LANDING PAGE
				$data_package['landing_page'] = $this->data->LandingPage;

				$this->data->Source = ( $this->data->Referrer != '') ? $this->data->Referrer : 'direct'; // STORE FIRST VISIT
				$data_package['source'] = $this->data->Source;

				$data_package['user_agent'] = $this->data->UserAgent; // STORE HTTP USER AGENT
				$data_package['lead_score'] = $this->data->LeadScore; // STORE LEAD SCORE
			}
		}

		$value = ( isset( $this->data->Pages[$post_id] ) ) ? $this->data->Pages[$post_id] : 0;
		$this->data->Pages[$post_id] = $value + 1;	// TRACK PAGE VIEW

		$value = ( isset( $this->data->PagesSession[$post_id] ) ) ? $this->data->PagesSession[$post_id] : 0;
		$this->data->PagesSession[$post_id] = $value + 1;	// TRACK PAGE VIEW --> CURRENT SESSION ONLY

		$this->data->Views = $this->data->Pages[$post_id];	// UPDATE PAGE VIEWS
		$this->data->ViewsSession = $this->data->PagesSession[$post_id];	// UPDATE PAGE VIEWS --> CURRENT SESSION ONLY

		$post_types_enabled = array ( 'page', 'post' );
		$post_types_enabled = apply_filters( 'logichop_update_data_post_types', $post_types_enabled );

		if ( $this->data->UpdateVisits ) {
			$data_package['visits'] = 'total';
			$this->data->UpdateVisits = false;
		}

		if ( in_array( $post_type, $post_types_enabled ) ) {
			$data_package['page'] = $post_id; // TRACK PAGE --> STORE PAGE VIEW FOR post OR page POST TYPES
		}

		$tags_enabled = array ( 'post_tag' );
		$tags_enabled = apply_filters( 'logichop_update_data_tags', $tags_enabled );

		$this->data->Tag = array ();
		$tags = wp_get_post_terms( $post_id, $tags_enabled );
		if ( $tags ) {
			$_tags = array();
			foreach ( $tags as $tag ) {
				$this->data->Tag[$tag->term_id] = 1;

				$value = ( isset( $this->data->Tags[$tag->term_id] ) ) ? $this->data->Tags[$tag->term_id] : 0;
				$this->data->Tags[$tag->term_id] = $value + 1;	// TRACK TAG VIEW

				$value = ( isset( $this->data->TagsSession[$tag->term_id] ) ) ? $this->data->TagsSession[$tag->term_id] : 0;
				$this->data->TagsSession[$tag->term_id] = $value + 1;	// TRACK TAG VIEW --> CURRENT SESSION ONLY

				$_tags[] = $tag->term_id; // TRACK TAG --> STORE TAG VIEW
			}
			$data_package['tag'] = $_tags;
		}

		$categories_enabled = array ( 'category' );
		$categories_enabled = apply_filters( 'logichop_update_data_categories', $categories_enabled );

		$this->data->Category = array ();
		$categories = wp_get_post_terms( $post_id, $categories_enabled );
		if ( $categories ) {
			$_categories = array();
			foreach ( $categories as $category ) {
				$this->data->Category[$category->term_id] = 1;

				$value = ( isset( $this->data->Categories[$category->term_id] ) ) ? $this->data->Categories[$category->term_id] : 0;
				$this->data->Categories[$category->term_id] = $value + 1;	// TRACK CATEGORY VIEW

				$value = ( isset( $this->data->CategoriesSession[$category->term_id] ) ) ? $this->data->CategoriesSession[$category->term_id] : 0;
				$this->data->CategoriesSession[$category->term_id] = $value + 1;	// TRACK CATEGORY VIEW --> CURRENT SESSION ONLY

				$_categories[] = $category->term_id;
			}
			$data_package['category'] = $_categories;
		}

		if ( ! isset( $_COOKIE[$this->logic->cookie_name] ) ) { // NO COOKIE -> CREATE COOKIE AFTER GEOLOCATION
			$this->logic->cookie_create(); // CREATE THE COOKIE :: STORE HASH
		}

		$data_package = apply_filters( 'logichop_update_data_package', $data_package, $post_id, $post_type) ;
		$this->logic->data_remote_put( 'data-package', $data_package, 'events-update', true );

		do_action( 'logichop_update_data', $post_id, $post_type );

		if ( $this->logic->get_option( 'debug_mode', false ) && isset( $_GET['debug'] ) ) {
			if ( $_GET['debug'] == 'display' ) {
				$this->logic->d( $this->data );
			} else {
				$this->logic->d( $_GET['debug'] . ': ' . $this->get_value( $_GET['debug'] ) );
			}
		}

		if ( $this->logic->get_option('debug_mode', false ) && isset( $_REQUEST['session-var'] ) && isset( $_REQUEST['debug-val'] ) ) {
			$session_var = filter_var( $_REQUEST['debug-var'], FILTER_SANITIZE_STRING );
			$session_val = filter_var( $_REQUEST['debug-val'], FILTER_SANITIZE_STRING );

			$this->data_session_set( $session_var, $session_val );

			if ( $session_var == 'IP' ) {
				if ( ! isset( $geo ) ) $geo = new \LogicHop_Geo_IP( $this );
				$this->data->Location = $geo->geolocate( $session_val );
			}
		}

		if ( $this->logic->get_option( 'debug_mode', false ) && isset( $_REQUEST['logichop-preview'] ) ) {
			$this->generate_preview_data( $_REQUEST );
		}

		$this->transient_save();
	}

	/**
	 * Update Goal
	 *
	 * @since    	1.0.0
	 * @param      	object    $goal		WordPress post object
	 * @param      	boolean    $delete_goal 	Delete goal
	 * @return      boolean    Goal stored state.
	 */
	public function update_goal ( $goal, $delete_goal ) {

		if ( ! $delete_goal ) {

			$value = ( isset( $this->data->Goals[$goal->ID] ) ) ? $this->data->Goals[$goal->ID] : 0;
			$this->data->Goals[$goal->ID] = $value + 1;	// TRACK GOALS

			$value = ( isset( $this->data->GoalsSession[$goal->ID] ) ) ? $this->data->GoalsSession[$goal->ID] : 0;
			$this->data->GoalsSession[$goal->ID] = $value + 1;	// TRACK GOALS --> CURRENT SESSION ONLY

			$values	= get_post_custom( $goal->ID );

			if ( isset( $values['logichop_goal_lead_score_adjust'][0] ) && isset( $values['logichop_goal_lead_freq'][0] ) ) {
				$lead_adjust = ( int ) $values['logichop_goal_lead_score_adjust'][0];
				$lead_freq = $values['logichop_goal_lead_freq'][0];

				if ( $lead_freq == 'every' ) {
					$this->update_lead_score( $lead_adjust );
				} else if ( $lead_freq == 'first' ) {
					if ( isset( $this->data->Goals[$goal->ID] ) ) {
						if ( $this->data->Goals[$goal->ID] == 1 ) {
							$this->update_lead_score( $lead_adjust );
						}
					}
				} else if ( $lead_freq == 'session' ) {
					if ( isset( $this->data->GoalsSession[$goal->ID] ) ) {
						if ( $this->data->GoalsSession[$goal->ID] == 1 ) {
							$this->update_lead_score( $lead_adjust );
						}
					}
				} else if ( $lead_freq == 'set' ) {
					return $this->set_lead_score( $lead_adjust );
				}
			}

			$this->logic->check_track_event( $goal->ID, $values );

			$this->update_goal_groups( $goal->ID );

			$this->transient_save();
			return $this->logic->data_remote_put( 'goal', $goal->ID );
		} else {
			if ( isset ( $this->data->Goals[$goal->ID] ) ) unset( $this->data->Goals[$goal->ID] );
			if ( isset ( $this->data->GoalsSession[$goal->ID] ) ) unset( $this->data->GoalsSession[$goal->ID] );

			$this->transient_save();
			return $this->logic->data_remote_put( 'goal', $goal->ID, 'event-update' );
		}

		return false;
	}

	/**
	 * Update goal groups
	 *
	 * @since    	3.2.2
	 * @param     interger	$goal_id
	 */
	public function update_goal_groups ( $goal_id ) {

		$groups = get_the_terms( $goal_id, 'logichop_goal_group' );

		if ( $groups ) {

			$group_ids = array();

			foreach ( $groups as $group ) {
				$group_ids[] = $group->term_id;
			}

			$args = array(
				'posts_per_page' => -1,
				'post_type' => 'logichop-goals',
				'tax_query' => array(
					array(
						'taxonomy' => 'logichop_goal_group',
						'field'    => 'term_id',
						'terms'    => $group_ids,
					),
				),
			);

			$query = new \WP_Query( $args );

			if ( $query->have_posts() ) {

				$delete_goals = array();

				while ( $query->have_posts() ) {
        	$query->the_post();
					$_id = get_the_ID();
					if ( $_id != $goal_id ) {
						$delete_goals[] = $_id;

						if ( isset ( $this->data->Goals[$_id] ) ) unset( $this->data->Goals[$_id] );
						if ( isset ( $this->data->GoalsSession[$_id] ) ) unset( $this->data->GoalsSession[$_id] );
					}
      	}

				if ( count( $delete_goals ) > 0 ) {
					$this->transient_save();
					return $this->logic->data_remote_put( 'goals', implode( ',', $delete_goals ), 'event-update' );
				}
      }
			wp_reset_postdata();
		}
	}

	/**
	 * Update geolocation
	 *
	 * @since    	3.6.0
	 * @param     string	$ip_address
	 */
	public function update_geolocation ( $ip_address ) {
		$geo = new \LogicHop_Geo_IP( $this->logic );
		if ( ! $this->logic->geolocation_disable ) {
			$this->set_value( 'Location', $geo->geolocate( $ip_address ) );
		}
	}

	/**
	 * Lead Score Validator
	 *
	 * @since    	2.0.7
	 * @param      	integer    $post_id			Post ID
	 * @param      	integer    $lead_adjust		Lead Score increment
	 * @param      	string     $lead_freq		Lead Score frequency
	 * @return      boolean    Lead Score stored state.
	 */
	public function validate_lead_score ( $post_id, $lead_adjust, $lead_freq ) {
		if ( $lead_freq == 'every' ) {
			return $this->update_lead_score( $lead_adjust );
		} else if ( $lead_freq == 'first' ) {
			if ( isset( $this->data->Pages[$post_id] ) ) {
				if ( $this->data->Pages[$post_id] == 1 ) {
					return $this->update_lead_score($lead_adjust);
				}
			}
		} else if ( $lead_freq == 'session' ) {
			if ( isset( $this->data->PagesSession[$post_id] ) ) {
				if ( $this->data->PagesSession[$post_id] == 1 ) {
					return $this->update_lead_score($lead_adjust);
				}
			}
		} else if ( $lead_freq == 'set' ) {
			return $this->set_lead_score( $lead_adjust );
		}
		return false;
	}

	/**
	 * Update Lead Score.
	 *
	 * @since    	2.0.7
	 * @param      	integer    $lead_adjust		Lead Score increment
	 * @return      boolean    Lead Score stored state.
	 */
	public function update_lead_score ( $lead_adjust ) {
		if ( $lead_adjust != 0 && is_numeric( $lead_adjust ) ) {
			$lead_score = ( isset( $this->data->LeadScore ) ) ? ( int ) $this->data->LeadScore : 0;
			$new_lead_score = $lead_adjust + $lead_score;
			$this->data->LeadScore = $new_lead_score;

			$this->transient_save();
			return $this->logic->data_remote_put( 'lead_score', $new_lead_score, 'event-update' );
		}
		return false;
	}

	/**
	 * Set Lead Score.
	 *
	 * @since    	3.0.1
	 * @param      	integer    $new_score		Lead Score value to set
	 * @return      boolean    Lead Score stored state.
	 */
	public function set_lead_score ( $new_score ) {
		if ( is_numeric( $new_score ) ) {
			$this->data->LeadScore = $new_score;

			$this->transient_save();
			return $this->logic->data_remote_put( 'lead_score', $new_score, 'event-update' );
		}
		return false;
	}

	/**
	 * Create data model
	 *
	 * @param      	string    $hash				UID hash
	 * @param      	string    $client_ip	IP Address // $this->get_client_IP();
	 * @return      string    $token			Token hash // $this->generate_hash( 'token' );
	 *
	 * @since    3.5.6
	 */
	public function data_object_create () {

		$data = new \stdclass();

		$data->UID 					= $this->logic->hash;
		$data->FirstVisit		= true;
		$data->TotalVisits 	= 1;
		$data->UpdateVisits = true;

		$timestamp = new \stdclass();
		$timestamp->FirstVisit 	= ''; // TIMESTAMP FIRST VISIT
		$timestamp->LastVisit 	= ''; // TIMESTAMP LAST SESSION
		$timestamp->ThisVisit 	= ''; // TIMESTAMP LAST SESSION
		$timestamp->LastPage 		= ''; // TIMESTAMP LAST PAGE
		$data->Timestamp				= $timestamp;

		$mobile_detect 	= new \Mobile_Detect();
		$data->Mobile		= $mobile_detect->isMobile();
		$data->Tablet		= $mobile_detect->isTablet();
		$data->MobileOS = ( $mobile_detect->isiOS() ) ? 'iOS' : '';
		if ( $mobile_detect->isAndroidOS() ) {
			$data->MobileOS = 'Android';
		}

		$os_detect 	= new \OS_Detect();
		$data->OS		= $os_detect->getOS();

		$CrawlerDetect 	= new \Jaybizzle\CrawlerDetect\CrawlerDetect();
		$data->IsBot		= $CrawlerDetect->isCrawler();

		$data->IP 								= $this->logic->get_client_IP();
		$data->URL 								= '';
		$data->Language						= null;
		$data->Location 					= null;
		$data->LandingPage 				= '';
		$data->LandingPageSession = '';
		$data->LeadScore 					= 0;
		$data->Source 						= '';
		$data->UserAgent					= ( isset( $_SERVER['HTTP_USER_AGENT'] ) ) ? $_SERVER['HTTP_USER_AGENT'] : '';
		$data->LoggedIn						= false;
		$data->UserData						= new \stdClass();
		$data->Page 							= 0;
		$data->Views 							= 0;
		$data->Pages 							= null;
		$data->Goals 							= array();
		$data->PagesSession				= null;
		$data->GoalsSession				= array();
		$data->ViewsSession 			= 0;

		$data->Category 					= array ();
		$data->Categories 				= array ();
		$data->CategoriesSession 	= array ();
		$data->Tag 								= array ();
		$data->Tags 							= array ();
		$data->TagsSession 				= array ();

		$data->Path 			= array();
		$data->QueryStore = array();
		$data->Query 			= array();
		$data->Referrer		= '';
		$data->Date				= null;
		$data->UserDate		= null;
		$data->Condition	= false;
		$data->Custom			= new \stdClass();
		$data->Token 			= $this->logic->generate_hash( 'token' );

		$data = apply_filters( 'logichop_data_object_create', $data );

		do_action( 'logichop_session_create' );

		$this->data = $data;

		$this->set_user_data( false, false, false );
	}

	/**
	 * Set User Data
	 *
	 * @since    	2.1.5
	 * @param     object		$user    	User object.
	 * @param     boolean		$logout    	Logout user
	 * @param     boolean		$save    	Save data
	 */
	public function set_user_data ( $user = false, $logout = false, $save = true  ) {
		if ( $logout ) {
			$this->data->LoggedIn = false;
		} else {
			if ( ! $user ) {
				$user = wp_get_current_user();
				if ( isset( $user ) && $user->exists() ) {
					$this->data->LoggedIn = true;
				}
			}
		}

		$user_data = new \stdClass();
		$user_data->ID 							= isset( $user->ID ) ? $user->ID : 0;
		$user_data->user_email 			= isset( $user->user_email ) ? $user->user_email : '';
		$user_data->user_firstname 	= isset( $user->user_firstname ) ? $user->user_firstname : '';
		$user_data->user_lastname 	= isset( $user->user_lastname ) ? $user->user_lastname : '';
		$user_data->display_name 		= isset( $user->display_name ) ? $user->display_name : '';
		$user_data->user_nicename 	= isset( $user->user_nicename ) ? $user->user_nicename : '';
		$user_data->role 						= isset( $user->roles[0] ) ? $user->roles[0] : '';

		$user_data = apply_filters( 'logichop_set_user_data', $user_data );

		$this->data->UserData = $user_data;

		do_action( 'logichop_user_data_set', $user );

		if ( $save ) {
			$this->transient_save();
		}
	}

	/**
	 * Create date object
	 *
	 * @since    	3.5.6
	 * @return    Object    Custom date object.
	 */
	public function date_object ( $timestamp = null, $timezone = null ) {
		$d = new \DateTime();
		$date = new \stdclass();

		if ( $timestamp ) { // SET TIME FROM SPECIFIC TIMESTAMP
			$d->setTimestamp( $timestamp );
		} else { // SET TIME FROM WP TIMESTAMP
			$d->setTimestamp( strtotime( current_time( 'mysql' ) ) );
		}

		if ( $timezone ) { // SET TIME FROM SPECIFIC TIMEZONE --> USER LOCATION TIME
			$d = new \DateTime();
			$d->setTimeZone( new \DateTimeZone( $timezone ) );
		}

		$date->Timestamp	= $d->getTimestamp();
		$date->DateTime		= $d->format( 'Y-m-d H:i:s' );
		$date->Date				= $d->format( 'm-d-Y' );
		$date->Year 			= $d->format( 'Y' );
		$date->LeapYear 	= ( $d->format( 'L' ) ) ? true : false;
		$date->Month 			= $d->format( 'n' );
		$date->MonthName 	= $d->format( 'F' );
		$date->Day				= $d->format( 'j' );
		$date->DayName		= $d->format( 'l' );
		$date->DayNumber	= $d->format( 'N' );
		$date->DayYear		= $d->format( 'z' );
		$date->Week				= $d->format( 'W' );
		$date->Hour 			= $d->format( 'g' );
		$date->Hour24 		= $d->format( 'H' );
		$date->Minutes 		= $d->format( 'i' );
		$date->Seconds 		= $d->format( 's' );
		$date->AM					= ( $d->format( 'a' ) == 'am' ) ? true : false;
		$date->PM 				= ( $d->format( 'a' ) == 'pm' ) ? true : false;
		return $date;
	}

	/**
	 * Logic Hop set session data
	 *
	 * Used for testing & debugging
	 *
	 * Parameter 'vars' accepts '.' delimited object elements and ':' delimited array elements
	 * Example: Date.DateTime OR QueryStore:ref
	 *
	 * @since    	1.5.3
	 * @param  		string	$var	Data path
	 * @param  		string	$val	Data value
	 */
	public function data_session_set ( $var, $val = false ) {

		$vars = explode( '.', $var );
		$count = count( $vars ) -1;

		if ( $count >= 0 ) {
			for ( $i = $count; $i >= 0; $i-- ) {
				$tmp_object = new \stdclass;
				if ( $i == 0 ) {
					if ( $count == 0 ) {

						$array_check = explode( ':', $vars[$i] );
						if ( ! isset( $array_check[1] ) ) {
							$this->data->{$vars[0]} = $val;
						} else {
							$this->data->{$array_check[0]}[$array_check[1]] = $val;
						}
					} else {
						$this->data->{$vars[0]} = $output;
					}
				} else {
					if ($i == $count) {
						$object = new \stdclass;
						$array_check = explode( ':', $vars[$i] );
						if ( ! isset( $array_check[1] ) ) {
							$object->{$vars[$i]} = $val;
						} else {
							$object->{$array_check[0]}[$array_check[1]] = $val;
						}
						$tmp_object = $object;
					} else {
						$tmp_object->{$vars[$i]} = $output;
					}

					$output = new \stdclass;
					$output = $tmp_object;
				}
			}
		}
	}

	/**
	 * Utility to generate preview data
	 *
	 * @since		3.2.5
	 * @param      	array    $data       Array of key => value pairs of data
	 */
	public function generate_preview_data ( $data ) {
		$exclude = array(
			'data',
			'action',
			'uncache',
			'logichop_cookie',
			'logichop-preview',
			'logichop-preview-vars'
		);

		if ( isset( $data['logichop-preview-vars'] ) ) {
			$vars = explode( ',', $data['logichop-preview-vars'] );
			if ( $vars ) {
				foreach ( $vars as $v ) {
					$tmp = explode( '=', $v );
					if ( isset( $tmp[0] ) && isset( $tmp[1] ) ) {
						$data[ $tmp[0] ] = $tmp[1];
					}
				}
			}
		}

		foreach ( $data as $k => $v ) {
			if ( $v == 'false' ) {
				$v = false;
			}
			if ( ! in_array( $k, $exclude ) ) {
				$obj = explode( '-', $k );
				$arr = explode( ':', $k );

				if ( isset( $obj[2] ) ) {
	        if ( $arr ) {
	            $obj[2] = strtok( $obj[2], ':' );
	        }
	        if ( ! isset( $this->data->{$obj[0]} ) ) {
	          $this->data->{$obj[0]} = new \stdclass;
	        }
	        if ( ! isset( $this->data->{$obj[0]}->{$obj[1]} ) ) {
	          $this->data->{$obj[0]}->{$obj[1]} = new \stdclass;
	        }

	        if ( ! isset( $arr[1] ) ) {
	          $this->data->{$obj[0]}->{$obj[1]}->{$obj[2]} = $v;
	        } else {
	          $this->data->{$obj[0]}->{$obj[1]}->{$obj[2]}[$arr[1]] = $v;
	        }
	      } else if ( isset( $obj[1] ) ) {
	        if ( $arr ) {
	            $obj[1] = strtok( $obj[1], ':' );
	        }

	        if ( ! isset( $this->data->{$obj[0]} ) ) {
	          $this->data->{$obj[0]} = new \stdclass;
	        }

	        if ( ! isset( $arr[1] ) ) {
	          $this->data->{$obj[0]}->{$obj[1]} = $v;
	        } else {
	          $this->data->{$obj[0]}->{$obj[1]}[$arr[1]] = $v;
	        }
	      } else if ( isset( $arr[1] ) ) {

	        $this->data->{$arr[0]}[$arr[1]] = $v;
	      } else {

	        $this->data->{$k} = $v;
	      }
			}
		}
	}

	/**
	 * Build Gravatar Object
	 *
	 * @since    	1.1.0
	 * @param      	string    $name		Name of object to add Gravatar data to
	 * @param      	string    $email	Email Address
	 */
	public function gravatar_object ( $object_name, $email ) {
		$this->data->{$object_name}->gravatar = new \stdclass();
		$this->data->{$object_name}->gravatar->url = new \stdclass();
		$this->data->{$object_name}->gravatar->img = new \stdclass();
		$this->data->{$object_name}->gravatar->url->fullsize 	= $this->gravatar_get_url($email, 2048);
		$this->data->{$object_name}->gravatar->url->large 		= $this->gravatar_get_url($email, 1024);
		$this->data->{$object_name}->gravatar->url->medium 		= $this->gravatar_get_url($email, 512);
		$this->data->{$object_name}->gravatar->url->small 		= $this->gravatar_get_url($email, 256);
		$this->data->{$object_name}->gravatar->url->thumb 		= $this->gravatar_get_url($email, 100);
		$this->data->{$object_name}->gravatar->img->fullsize 	= $this->gravatar_get_url($email, 2048, true);
		$this->data->{$object_name}->gravatar->img->large 		= $this->gravatar_get_url($email, 1024, true);
		$this->data->{$object_name}->gravatar->img->medium 		= $this->gravatar_get_url($email, 512, true);
		$this->data->{$object_name}->gravatar->img->small 		= $this->gravatar_get_url($email, 256, true);
		$this->data->{$object_name}->gravatar->img->thumb 		= $this->gravatar_get_url($email, 100, true);
	}

	/**
	 * Get Gravatar URL
	 *
	 * @since    	1.1.0
	 * @param      	string    $email	Email address
	 * @param      	integer   $s		Image size
	 * @param      	string    $d		Default imageset [ 404 | mm | identicon | monsterid | wavatar ]
	 * @param      	string    $r		Maximum rating (inclusive) [ g | pg | r | x ]
	 * @param      	boolean   $img		True to return a complete IMG tag False for just the URL
	 * @param      	array     $atts		Optional, additional key/value attributes to include in the IMG tag
	 * @return      string    Gravatar URL or a complete image tag
	 */
	public function gravatar_get_url ( $email, $s = 80, $img = false, $d = 'mm', $r = 'pg', $atts = array() ) {
		$url = 'https://www.gravatar.com/avatar/';
    $url .= md5( strtolower( trim( $email ) ) );
   	$url .= "?s=$s&d=$d&r=$r";
   	$gravatar = sprintf(' gravatar gravatar-%d', $s);
   	$atts['class'] = isset( $atts['class'] ) ? $atts['class'] . $gravatar : $gravatar;
    if ( $img ) {
    	$url = '<img src="' . $url . '"';
			foreach ( $atts as $k => $v ) $url .= ' ' . $k . '="' . $v . '"';
    	$url .= ' />';
    }
    return $url;
	}

	/**
	* Delete expired transients with Logic Hop prefix
	*
	* @since    3.2.3
	*/
  public function delete_expired_transients () {
    global $wpdb;

    $sql = "
		  DELETE
				a, b
			FROM
				{$wpdb->options} a, {$wpdb->options} b
			WHERE
				a.option_name LIKE '%_transient_{$this->prefix}%' AND
				a.option_name NOT LIKE '%_transient_timeout_{$this->prefix}%' AND
				b.option_name = CONCAT(
					'_transient_timeout_{$this->prefix}',
					SUBSTRING(
						a.option_name,
						CHAR_LENGTH('_transient_{$this->prefix}') + 1
					)
				)
			AND b.option_value < UNIX_TIMESTAMP()
		";

		$wpdb->query( $sql );

		if ( is_multisite() && is_main_network() ) {
			$sql = "
				DELETE
					a, b
				FROM
					{$wpdb->sitemeta} a, {$wpdb->sitemeta} b
				WHERE
					a.meta_key LIKE '_site_transient_{$this->prefix}%' AND
					a.meta_key NOT LIKE '_site_transient_timeout_{$this->prefix}%' AND
					b.meta_key = CONCAT(
						'_site_transient_timeout_{$this->prefix}',
						SUBSTRING(
							a.meta_key,
							CHAR_LENGTH('_site_transient_{$this->prefix}') + 1
						)
					)
				AND b.meta_value < UNIX_TIMESTAMP()
			";

			$wpdb->query( $sql );
		}
  }
}
