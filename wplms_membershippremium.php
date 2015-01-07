<?php
/*
Plugin Name: WPLMS Membership Premium AddOn
Version: 1.0
Plugin URI: http://github.com/wplms-membershippremium
Parent Plugin URI: http://premium.wpmudev.org/project/membership
Description: AddOn plugin for WPLMS and WPMU DEV Membership plugin
Author: VibeThemes
License: GPLv2
Text Domain: wplms-membership
 */

include_once 'class.wplms.courses.php';
include_once 'class.wplms.quizzes.php';
include_once 'class.wplms.assignments.php';
include_once 'class.wplms.events.php';

class wplms_membership_addon{

	var $version = 1.0;

	function __construct(){
		if(!$this->check())
			return;

		add_filter('membership_level_sections',array($this,'wplms_membership_level_sections'));
		add_action('membership_register_rules',array($this,'wplms_membership_register_rules'));
	}

	function check(){
		if ( in_array( 'membership/membershippremium.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) )) {	
				return true;
		}
		return false;
	}
	function wplms_membership_level_sections($sections){
		$sections['wplms'] = array("title" => __('WPLMS', 'wplms-membership'));
		return $sections;
	}
	function wplms_membership_register_rules(){
		if ( defined( 'BP_COURSE_SLUG' )) {
			M_register_rule( 'wplms_courses','Membership_Model_Rule_WPLMS_Courses','wplms' );
			M_register_rule( 'wplms_quizzes', 'Membership_Model_Rule_WPLMS_Quizzes','wplms' );
			M_register_rule( 'wplms_assignments','Membership_Model_Rule_WPLMS_Assignments','wplms' );
			M_register_rule( 'wplms_events','Membership_Model_Rule_WPLMS_Events','wplms' );
		}
	}
}

new wplms_membership_addon();
