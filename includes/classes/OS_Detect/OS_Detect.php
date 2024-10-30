<?php

  /**
  * OS Detect Library
  *
  * @version   1.0.0
  */
  class OS_Detect {

     /**
  	 * @since    1.0.0
  	 * @access   protected
  	 * @var      string
  	 */
     protected $user_agent;

     /**
     * @since    1.0.0
     * @access   protected
     * @var      array
     */
     protected $os_array = array(
           '/windows/i'            =>  'Windows',
           '/win98/i'              =>  'Windows',
           '/win95/i'              =>  'Windows',
           '/win16/i'              =>  'Windows',
           '/macintosh|mac os x/i' =>  'macOS',
           '/mac_powerpc/i'        =>  'macOS',
           '/linux/i'              =>  'Linux',
           '/ubuntu/i'             =>  'Linux',
           '/iphone/i'             =>  'iOS',
           '/ipod/i'               =>  'iOS',
           '/ipad/i'               =>  'iOS',
           '/android/i'            =>  'Android',
           '/blackberry/i'         =>  'BlackBerry'
       );

     /**
     * @since    1.0.0
     * @param string  $userAgent Inject the User-Agent header. If null, will use HTTP_USER_AGENT
     */
     public function __construct ( $userAgent = null ) {

       $this->user_agent = ( $userAgent ) ? $userAgent : $_SERVER['HTTP_USER_AGENT'];
     }

     /**
     * @since    1.0.0
     * @return string $os
     */
     public function getOS () {

       $os = 'Unknown';

       foreach ( $this->os_array as $regex => $value ) {
         if ( preg_match( $regex, $this->user_agent ) ) {
           return $value;
         }
       }

       return $os;
     }
}
