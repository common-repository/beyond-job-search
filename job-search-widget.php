<?php
ob_start();
/*
 *	Plugin Name:  Beyond Job Search 
 *	Plugin URI:   http://www.beyond.com/
 *	Description:  Find Jobs with on your search.
 *	Version:      1.0
 *	Author:       Beyond.com
 *	Author URI:   http://www.beyond.com/	
*/

/*  Copyright YEAR  PLUGIN_AUTHOR_NAME  (email : PLUGIN AUTHOR EMAIL)
 *	
 *	This program is free software; you can redistribute it and/or modify
 *	it under the terms of the GNU General Public License, version 2, as 
 *	published by the Free Software Foundation.
 *	
 *	This program is distributed in the hope that it will be useful,
 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *	GNU General Public License for more details.
 *	
 *	You should have received a copy of the GNU General Public License
 *	along with this program; if not, write to the Free Software
 *	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

function beyond_assets() {

	// CALL BEYOND VALIDATION //
	wp_register_script('job_search_validation', plugins_url( '/assets/job_search_validation.js', __FILE__ ) );
	wp_enqueue_script('job_search_validation');
	
	// CALL BEYOND STYLESHEET //
	wp_enqueue_style( 'job_search_style', plugins_url( '/assets/job_search_style.css', __FILE__ ) );
}
// load js in non-admin pages
add_action('wp_enqueue_scripts', 'beyond_assets');

function jad_beyond_search_widget($args, $widget_args = 1) {
	extract( $args, EXTR_SKIP );
	if ( is_numeric($widget_args) ) {
		$widget_args	= array( 'number' => $widget_args );
	}
	$widget_args 		= wp_parse_args( $widget_args, array( 'number' => -1 ) );
	extract( $widget_args, EXTR_SKIP );

	$options 			= get_option('beyond_search_widget');
	if ( !isset($options[$number]) ) 
		return;
	$number_of_post 	= $options[$number]['number_of_post'];

	echo $before_widget; // start widget display code ?>
	
	<div class="form_table">
		<h2 class="Title">Job Search</h2>
		<form action="#" method="post" id="job_search_frm">
			<label><span>Search new jobs for: </span><?php echo date("F j, Y") ; ?></label>
			<label><span>Keyword: </span><input type="text" name="job_keyword" id="job_keyword" value="<?php echo $_COOKIE['job_keyword'];?>" /></label>
			<label><span>Location: </span><input type="text" name="job_location" id="job_location" value="<?php echo $_COOKIE['job_location'];?>" /></label>
			<label><input type="submit" name="submit" value="Search Jobs"  onclick="return validation('<?php echo plugin_dir_url(__FILE__); ?>ajaxpage.php');"/></label>
			<input type="hidden" name="number_of_post" id="number_of_post" value="<?php echo $number_of_post;?>" />
			<div style="clear:both;"></div>
		</form>
		<div class="center">
			<a href="http://www.beyond.com" target="_blank">
				<IMG SRC="http://ad.doubleclick.net/ad/N7384.6650.BEYOND.COM1/B7788500;sz=149x40;ord=<?php echo time() ;?>?" BORDER=0 WIDTH=149 HEIGHT=40 ALT="Advertisement">
			</a>
		</div>
		<div id="imagereload">
			<img src="<?php echo plugins_url( 'assets/loading.gif' , __FILE__ );?>"/>
		</div>

		<div id="reponse"><?php				
		if (isset($_COOKIE["job_keyword"])) {
			$_COOKIE			= array_map('trim', $_COOKIE);
			$total_post			= !empty($_COOKIE['number_of_post']) ? $_COOKIE['number_of_post'] : 10;
			$beyond_url 		= "http://www.beyond.com/common/services/job/search/default.asp?aff=A1C610EB-3496-4C5F-831E-0CEED7168DEA&k=".urlencode($_COOKIE['job_keyword'])."&l=".urlencode($_COOKIE['job_location'])."";
			$beyond_url 		= file_get_contents($beyond_url);
			$jobSearchResults 	= simplexml_load_string($beyond_url);
			$json 				= json_encode($jobSearchResults);
			$beyond_array 		= json_decode($json, true);
			if(!empty($beyond_array) && isset($beyond_array)) {
				if(isset($beyond_array['Item']) && !empty($beyond_array['Item'])) { ?>
					<h3>Your Job search results below:</h3><?php
					$search_array 			= $beyond_array['Item'];
					if(isset($search_array[0])) {
						$search_array 			= array_slice($search_array, 0, $total_post);
						foreach($search_array as $key=>$value){ 
							$title				= isset($value['Title']) 			? $value['Title']			: '';
							$location			= isset($value['Location'])			? $value['Location']		: '';
							$applyurl 			= isset($value['ApplyURL'])			? $value['ApplyURL']		: '';
							$companyname		= isset($value['CompanyName'])		? $value['CompanyName']		: '';
							$shortdescription	= isset($value['ShortDescription'])	? $value['ShortDescription']: '';?>
							<div class="job_detail">
								<div class='job_title'>
									<a href="<?php echo $applyurl; ?>" target="_blank"><?php echo $title; ?></a>
								</div>
								<div class="heading">
									<span><?php echo $companyname; ?></span> | 
									<span><?php echo $location; ?></span> | 
									<span><?php 
									if(isset($value['Modified'])) { 
										echo date("F j, Y",strtotime($value['Modified'])); 
									}?></span>
								</div>
								<p><?php echo $shortdescription; ?></p>
							</div><?php 
						} 
					 } else {
						$title					= isset($search_array['Title']) 			? $search_array['Title']			: '';
						$location				= isset($search_array['Location'])			? $search_array['Location']			: '';
						$applyurl 				= isset($search_array['ApplyURL'])			? $search_array['ApplyURL']			: '';
						$companyname			= isset($search_array['CompanyName'])		? $search_array['CompanyName']		: '';
						$shortdescription		= isset($search_array['ShortDescription'])	? $search_array['ShortDescription']	: '';?>
						<div class="job_detail">
							<div class='job_title'>
								<a href="<?php echo $applyurl; ?>" target="_blank"><?php echo $title; ?></a>
							</div>
							<div class="heading">
								<span><?php echo $companyname; ?></span> | 
								<span><?php echo $location; ?></span> | 
								<span><?php 
								if(isset($value['Modified'])) { 
									echo date("F j, Y",strtotime($value['Modified'])); 
								}?></span>
							</div>
							<p><?php echo $shortdescription ; ?> </p>
						</div><?php 
					}?>
					<div class="more_jobs">
						<a href="http://www.beyond.com/jobs/job-search.asp?aff=A1C610EB-3496-4C5F-831E-0CEED7168DEA&a=0&k=<?php echo urlencode($_COOKIE['job_keyword']); ?>&l=<?php echo urlencode($_COOKIE['job_location']); ?>" target="_blank">See more Jobs Like these, Click Here</a>
						<IMG SRC="http://ad.doubleclick.net/ad/N7384.6650.BEYOND.COM1/B7788500.2;sz=1x1;ord=<?php echo time() ;?>?" BORDER=0 WIDTH=1 HEIGHT=1 ALT="Advertisement">
					</div>
					<div class="footertxt"><b>Powered by </b> &nbsp; 
						<a href="http://www.beyond.com" target="_blank">
							<IMG SRC="http://ad.doubleclick.net/ad/N7384.6650.BEYOND.COM1/B7788500;sz=149x40;ord=<?php echo time() ;?>?" BORDER=0 WIDTH=149 HEIGHT=40 ALT="Advertisement">
						</a>
					</div><?php
				} else {
					echo "<div class ='record_error'>Record not Found</div>";
				}
			}  else {
				echo "<div class ='record_error'>Record not found </div>";
			}
		}?>
		</div>
	</div>
	<?php echo $after_widget; // end widget display code
}

function job_search_widget_control($widget_args) {	
	global $wp_registered_widgets;
	static $updated 	= false;
	if ( is_numeric($widget_args) ) {
		$widget_args 	= array( 'number' => $widget_args );
	}
	$widget_args 		= wp_parse_args( $widget_args, array( 'number' => -1 ) );
	extract( $widget_args, EXTR_SKIP );
	$options 			= get_option('beyond_search_widget');
	if ( !is_array($options) )	
		$options 		= array();
	if ( !$updated && !empty($_POST['sidebar']) ) {
		$sidebar 			= (string) $_POST['sidebar'];	
		$sidebars_widgets	= wp_get_sidebars_widgets();
		
		if ( isset($sidebars_widgets[$sidebar]) )
			$this_sidebar 	= & $sidebars_widgets[$sidebar];
		else
			$this_sidebar 	= array();

		foreach ( (array) $this_sidebar as $_widget_id ) {
			if ( 'jad_beyond_search_widget' == $wp_registered_widgets[$_widget_id]['callback'] && isset($wp_registered_widgets[$_widget_id]['params'][0]['number']) ) {
				$widget_number = $wp_registered_widgets[$_widget_id]['params'][0]['number'];
				if ( !in_array( "beyond-search-widget-$widget_number", $_POST['widget-id'] ) ) // the widget has been removed.
					unset($options[$widget_number]);
			}
		}

		foreach ( (array) $_POST['beyond-search-widget'] as $widget_number => $beyond_search_widget ) {
			if ( !isset($beyond_search_widget['number_of_post']) && isset($options[$widget_number]) ) // user clicked cancel
				continue;
			
			$number_of_post 			= strip_tags(stripslashes($beyond_search_widget['number_of_post']));
			// Pact the values into an array
			$options[$widget_number] 	= compact( 'number_of_post' );
		}

		update_option('beyond_search_widget', $options);
		$updated = true;
	}
	if ( -1 == $number ) { // if it's the first time and there are no existing values
		$number_of_post = '';
		$number 		= '%i%';	
	} else { // otherwise get the existing values
		$number_of_post = attribute_escape($options[$number]['number_of_post']);
	} ?>
	<p>
		<label>Number of Posts to show </label>
		<input id="title_value_<?php echo $number; ?>" name="beyond-search-widget[<?php echo $number; ?>][number_of_post]" type="text" size="10" value="<?=$number_of_post?>" />
	</p>
	<input type="hidden" name="beyond-search-widget[<?php echo $number; ?>][submit]" value="1" />
	<?php
}

function job_search_widget_register() {
	if ( !$options 	= get_option('beyond_search_widget') ) {
		$options 	= array();
	}
	$widget_ops 	= array('classname' => 'beyond_search_widget', 'description' => __('Search Beyond Job'));
	$control_ops 	= array('width' => 250, 'height' => 450, 'id_base' => 'beyond-search-widget');
	$name 			= __('Job Search Widget');
	$id 			= false;
	foreach ( (array) array_keys($options) as $o ) {
		if ( !isset( $options[$o]['number_of_post'] ) )
			continue;

		$id 		= "beyond-search-widget-$o";
		wp_register_sidebar_widget($id, $name, 'jad_beyond_search_widget', $widget_ops, array( 'number' => $o ));
		wp_register_widget_control($id, $name, 'job_search_widget_control', $control_ops, array( 'number' => $o ));
	}
	
	if ( !$id ) {
		wp_register_sidebar_widget( 'beyond-search-widget-1', $name, 'jad_beyond_search_widget', $widget_ops, array( 'number' => -1 ) );
		wp_register_widget_control( 'beyond-search-widget-1', $name, 'job_search_widget_control', $control_ops, array( 'number' => -1 ) );
	}
}
add_action('init', job_search_widget_register, 1);
?>