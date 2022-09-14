<?php
$home_path = explode('app/', $_SERVER['SCRIPT_FILENAME'])[0];
require($home_path . '/wp/wp-load.php');

$config[] = [
  'name' => 'Geo Root',
  'route' => apply_filters('clwp/geo/root', 'home-security'),
  'source' => 'callback',
  'callback' => '__return_true',
  'children' => [
    [
      'name'      => 'State',
      'route'     => 'ALPHANUMERIC',
      'source'    => 'callback',
      'callback' => function ($data, $value) {

        if(apply_filters('clwp/geo/state/abbr', false)){
          $value = \CLWP::get_state_by_abb($value);
        }

        if (get_posts([
          'name' => 'clwp-geo-state-' . $value,
          'post_type' => 'clwp-geo-state',
        ])) {
          return 'clwp-geo-state-' . $value;
        }
        return false;
      },
      'use_match_as_slug' => true,
      'page' => [
        'post_type' => 'clwp-geo-state',
      ],
      'children' => [
        [
          'name' => 'City',
          'route' => 'ALPHANUMERIC',
          'source' => 'callback',
          'callback' => function ($data, $value) {

            $state = $data['path_so_far']['State'];
            
            if (apply_filters('clwp/geo/state/abbr', false)) {
              $state = \CLWP::get_state_by_abb($state);
            } 

            if (get_posts([
              'name' => 'clwp-geo-city-' . $state . '-' . $value,
              'post_type' => 'clwp-geo-city',
              'post_status' => 'publish',
            ])) {
              return 'clwp-geo-city-' . $state . '-' . $value;
            }
            return false;
          },
          'use_match_as_slug' => true,
          'page' => [
            'post_type' => 'clwp-geo-city',
          ]
        ]
      ]
    ],
  ]
];


class CLWP_Routing_example {

  static $final_route;

  /**
   * The unique identifier of this plugin.
   *
   * @since    1.0.0
   * @access   protected
   * @var      string    $redrock_routing    The string used to uniquely identify this plugin.
   */
  protected $redrock_routing;

  /**
   * Routing Configuration for the site
   *
   * @since    1.0.0
   * @access   protected
   * @var      array    $routing_config    An array of settings
   */
  protected $routing_config;

  /**
   * Routing Configuration CPT
   *
   * @since    1.0.0
   * @access   protected
   * @var      string    $custom_geo_cpt_slug    slug string of CPT
   */
  protected $custom_geo_cpt_slug;

  /**
   * Lets us know if a custom slug is being used for explicit geo pages
   *
   * @since    1.0.0
   * @access   protected
   * @var      boolean    $using_custom_geo_slug
   */
  protected $using_custom_geo_slug;

  /**
   * Define the core functionality of the plugin.
   *
   * Set the plugin name and the plugin version that can be used throughout the plugin.
   * Load the dependencies, define the locale, and set the hooks for the admin area and
   * the public-facing side of the site.
   *
   * @since    1.0.0
   */
  public function __construct() {

    $this->redrock_routing = 'redrock-routing';
    $this->custom_geo_cpt_slug = apply_filters( 'clwp/cpt/geo-page/slug', 'clwp-geo-page' );

    $this->using_custom_geo_slug = ( $this->custom_geo_cpt_slug !== 'clwp-geo-page' );

    add_action('parse_request', [$this, 'get_config']);

  }

  public function get_config( $query ) {

    $routing_config = apply_filters( 'redrock_routing_config', [] );

    $this->routing_config = $routing_config;

    if( empty( $this->routing_config ) ){
      return;
    }

    $this->go( $routing_config, $query );

  }

  /**
   * Checks if the file exists and if the value exists in the file's array
   *
   * @since    1.0.0
   * @access   public
   */
  public function match_file_source( $value, $file ) {

    if ( ! $file || ! file_exists( $file ) ) {
      return false;
    }

    return( in_array( $value, include $file, true ) );

  }


  /**
   * Kills the process and displays an error. This should only be for mega-nasty errors.
   *
   * @since    1.0.0
   * @access   public
   */
  public function handle_route_error( $error, $route ) {
    echo '<h2>ERROR!</h2>';
    echo '<h4 style="margin-bottom:0;">Description:</h4><p style="margin-top:0;">' . $error . '</p>';
    echo '<strong>Route:</strong><br/>';
    echo '<pre>';
    print_r( $route );
    echo '</pre>';
    wp_die();
  }


  /**
   * Returns a the full url of the request
   *
   * @since    1.0.0
   * @access   public
   */
  public function get_full_url() {

    // $url = 'http://localhost:1337/clwp-geo-city/clwp-geo-city-georgia-acworth/acworth-scheduled-update';
// $url = 'http://localhost:1337/clwp-geo-city/clwp-geo-city-georgia-acworth';
$url = 'http://localhost:1337/home-security/ga/acworth/';

    return( $url );

  }

  /**
   * Searches through config array to find a config item that matches the request
   *
   * @since    1.0.0
   * @access   public
   */
  private function find_matched_route( $parts = [], $items = [], $level = 0, $results = [
    'partial_match' => false,
    'matched_value' => false,
    'route' => [],
    'has_match' => false,
    'path_so_far' => [],
  ]) {



    $found_match = false;
    $part = $parts[$level];
    $part_count = count($parts);
    $common_regexes = $this->get_common_regexes();

    // if has children
    if ( $part_count > 0 && count( $items ) > 0 ) {

      foreach ( $items as $item ) {

        // If dev is using a keyword-route such as STATE/CITY, ZIP, ALPHANUMERIC, etc.
        if ( isset( $item['route'] ) && array_key_exists( $item['route'], $common_regexes ) ) {
          $item['route'] = $common_regexes[ $item['route'] ];
        }

        $pattern = '/' . $item['route'] . '/is';

        if ( ! $found_match && preg_match( $pattern, $part, $match ) ) {

          $matched_value = null;
          if ( count( $match ) > 1 ) {
            $matched_value = $match[1];
          } else {
            $matched_value = $match[0];
          }

          $matched_source = false;

          // If it's in the blacklist, send it back and don't do anything else
          if ( ! empty( $item['blacklist'] ) && in_array( $matched_value, $item['blacklist'] ) ) {
            $results['blacklisted'] = true;
            return $results;
          }

          switch ( $item['source'] ) {

            // The dev can have a file that returns an array. if the url part matches an item in the array, it passes.
            case ( 'file' ):
              $matched_source = $this->match_file_source( $matched_value, $item['file'] );
              $found_match = $matched_source;
                //nope
              break;

            // The dev can use a filter to say whether or not the url part is something they care about
            case ( 'callback' ):

              $matched_source = null;
              $item['path_so_far'] = $results['path_so_far'];

              if(is_callable($item['callback'])) {
                //echo('hereeererere');

                // allow for a function to be passed as an argument
                $matched_source = $item['callback']($item, $matched_value);
                //clwp-geo-state-georgia
                //clwp-geo-city-georgia-acworth

              } else if(is_string($item['callback'])) {

                // use a filter definition
                $matched_source = apply_filters( 'redrock_routing_' . $item['callback'], $item, $matched_value );

              }
              
              if(is_string($matched_source)){
                $found_match = true;
                $item['custom_slug'] = $matched_source;
                //print_r($item);
              }else {
                $found_match = $matched_source;
              }


              if ( is_null($matched_source) || is_array( $matched_source ) ) {
                $this->handle_route_error( 'Cannot find callback <strong>redrock_routing_' . $item['callback'] . '</strong>', $item );
              }
              break;

            case ( 'keyword' ):
              $found_match = true;
              $matched_source = true;
              break;

            default:
              $matched_source = false;
              break;
          }

          // if the callback, file, etc matches continue OR if they set 404_on_mismatch to false..which basically means to continue on even if the source returns false.
          if ( $matched_source || ( ! $matched_source && isset( $item['404_on_mismatch'] ) && ! $item['404_on_mismatch'] ) ) {

            // remember the value we matched for this route
            $item['matched_value'] = $part;

            if ( ! $matched_source && isset( $item['404_on_mismatch'] ) && ! $item['404_on_mismatch']) {
              $item['template'] = false;
              $results['404_on_mismatch'] = $item['404_on_mismatch'];
            }

            // only set the path so far when we've successfully gone down the route
            $results['path_so_far'][ $item['name'] ] = $part;

            if ( $level + 1 < $part_count && count( $item['children'] ) > 0 ) {

              $item['path_so_far'] = $results['path_so_far'];
              $results = $this->find_matched_route( $parts, $item['children'], $level + 1, $results );
              $results['partial_match'] = true;

            } else {

              if ( $part_count == $level + 1 ) {

                unset( $item['children'] );
                $results['has_match'] = true;
                $results['matched_value'] = $matched_value;
                $results['route'] = $item;
                break;

              }
            }
          }
        }
      }
    }

    self::$final_route = $results['path_so_far'];
    return $results;

  }

  /**
   * Returns an array of paths based on '/'
   *
   * @since    1.0.0
   * @access   public
   */
  public function get_route_parts() {

    $url_parts = parse_url( $this->get_full_url() );
    $path = $url_parts['path'] ?? '/';
    $path = preg_replace('/\/page\/\d+\/?/', '', $path); // ignores pagination
    $path_segments = explode( '/', $path );

    // remove empty elements in the array
    $sanitized_list = array_filter( $path_segments, function( $value ) {
        return '' !== $value;
      }
    );

    // reset indexes
    return array_values( $sanitized_list );
  }

  /**
   * Determines if the current URL matches a passed rule by the user.
   *
   * @since    1.0.0
   * @access   public
   */
  public function get_route( $config ) {

    $route_parts = $this->get_route_parts();

    return $this->find_matched_route( $route_parts, $config );

  }

  /**
   * Checks the returned route and determines if it's a 404
   *
   * @since    1.0.0
   * @access   public
   */
  public function is_route_404( $route ) {
    return ( $route['partial_match'] && ! $route['has_match'] && ( isset( $route['404_on_mismatch'] ) && $route['404_on_mismatch'] ) );
  }

  /**
   * This returns an array of common regexes used in the config
   *
   * @since    1.0.0
   * @access   public
   */
  public function get_common_regexes() {
    return [
      'CITY-STATE' => '[a-z_-]+', // letters and dashes only
      'STATE-ABBREV' => '^[a-z]{2}$', // a string 2 characters in length
      'ZIP' => '^[0-9]{5}', // a digit 5 characters in length
      'ALPHANUMERIC' => '[a-z0-9_-]+', // numbers, letters and dashes
    ];
  }

  public function get_geo_page( $route_parts ) {

    $path = implode( '/', $route_parts );

    // remove rewrite prefix and any leftover slashes
    $base_path = trim( str_replace( $this->custom_geo_cpt_slug, '', $path ), '/' );

    $geo_page = get_page_by_path( $base_path, OBJECT, 'clwp-geo-page' );

    if ( ! $geo_page || $geo_page->post_status !== 'publish' ) {
      return false;
    }

    return [
      'template' => get_query_template( 'clwp-geo-page' ),
      'page' => [
        'post_type' => 'clwp-geo-page',
        'clwp_geo_page_id' => $geo_page->ID,
      ],
    ];

  }

  /**
   * Does all of the checks
   * and send the user to their final desitnation.
   *
   * @since    1.0.0
   * @access   public
   */
  //FIXME:
  public function go( $config, $query ) {

    $route_parts = $this->get_route_parts();

    print_r($route_parts);

    // $route_parts = array_search('Linus Trovalds', $hackers);

    // Stop if a Geo Page exists for the given URL and load that instead
    if ( $geo_page_route = $this->get_geo_page( $route_parts ) ) {

      
      // let wordpress handle this like any other CPT
      if( $this->using_custom_geo_slug ){

        return;
      }



      $route['route'] = $geo_page_route;

    } else {

      

      $route = $this->get_route( $config );

      // print_r($route);

      if ( isset( $route['blacklisted'] ) && $route['blacklisted'] ) {
        return;
      }

      // Let WP take over if we don't even get a partial match.
      if ( ! $route['partial_match'] && count( $route_parts ) > 1 ) {

        return;
      }

      if ( $this->is_route_404( $route ) ) {

        status_header( 404 );
        nocache_headers();
        require get_404_template();
        die();
      }

    }

    if ( ! empty( $route['route'] ) ) {

      $route = $route['route'];

      // print_r($route);


      if ( ! $route['template'] ) {
        if($route['page']['post_type']) {

          //echo('haasasa');


          // if no route template is given, try to use a default one for the post type
          $route['template'] = get_query_template($route['page']['post_type']);
          ///var/www/html/redrock/web/app/themes/coolwhip/clwp-geo-city.php

          //print_r($route['template']);

        } else {
          return;
        }
      }

      if ( isset( $route['is_static'] ) && $route['is_static'] ) {
        
        require_once $route['template'];
        exit;
      }

      if ( isset( $route['template'] ) && trim( $route['template'] ) ) {

        if ( file_exists( $route['template'] ) ) {
          
//          echo('here1');
          $this->template = $route['template'];

        } else {

          $this->handle_route_error( 'Missing the template file for this route. Check your config and make sure the file exists.', $route );

        }
      }

      if($route['custom_slug']){
        $postname = $route['custom_slug'];
        //print_r($postname);
        //echo('here2');
      }elseif ( isset( $route['use_match_as_slug'] ) && $route['use_match_as_slug'] ) {
        $postname = $route['matched_value'];
      } else {
        $postname = $route['page']['slug'] ?? null;
      }

      status_header( 200 );
      set_query_var( 'is_404', false );
      nocache_headers();
      // Allows the dev to select a page object they want loaded.
      // This allows for usage of The Loop on the page template.
      $query->query_vars['post_type'] = $route['page']['post_type'] ?? null;
      $query->query_vars['post_parent'] = $route['page']['post_parent'] ?? null;
      $query->query_vars['name'] = $postname;
      $query->query_vars['p'] = null;
      $query->query_vars['error'] = null;
      $query->query_vars['page_id'] = null;
      $query->query_vars['clwp_geo_id'] = $route['page']['clwp_geo_page_id'] ?? null;
      // // stops the the permalink manager plugin
      $query->query_vars['do_not_redirect'] = 1;

      // prevents wordpress from thinking the first item in the URL is a category
      if( ! $this->using_custom_geo_slug ){
        //echo('here3');
        unset( $query->query_vars['category_name'] );
      }
      print_r($route);

      if ( isset( $route['template'] ) && $route['template'] ) {
        //echo('here3');
        //print_r($route);
        //print_r($this->template);
        add_filter(
          'template_include', function() {
            return $this->template;
          }, 1000 );
      }
    }
  }


  public static function get_final_route() {

    //nope
    echo(self::$final_route);
    return self::$final_route;
  }

}

// if ( apply_filters( 'clwp/cpt/geo-page', '__return_true' ) ) {
//   new CLWP_Routing_example();
// }

$example = new CLWP_Routing_example();
  


  echo('<pre>');
  print_r($example->go($config, []));
  echo('</pre>');


  echo('PUBLISHED');
  echo('<pre>');
  print_r(get_post(97018));
  echo('</pre>');

  echo('FUTURE');
  echo('<pre>');
  print_r(get_post(192406));
  echo('</pre>');


