<?php 

// INTILIZE THE VALIABLES //
$_POST    			= array_map('trim', $_POST);	
$total_post			= !empty($_POST['number_of_post']) ? $_POST['number_of_post'] : 10;

// GET RESULT FROM 'BEYOND.COM' API //
$beyond_url     	= "http://www.beyond.com/common/services/job/search/default.asp?aff=A1C610EB-3496-4C5F-831E-0CEED7168DEA&k=".urlencode($_POST['job_keyword'])."&l=".urlencode($_POST['job_location'])."";
$beyond_url 		= file_get_contents($beyond_url);
$jobSearchResults 	= simplexml_load_string($beyond_url);
$json 				= json_encode($jobSearchResults);
$beyond_array 		= json_decode($json, true);

// STORE SEARCH PARAMETERS IN 'COOKIES' FOR THE FUTURE USE //
setcookie('job_keyword'		, $_POST['job_keyword']		, time() + (60*60*24)	, '/');
setcookie('job_location'	, $_POST['job_location']	, time() + (60*60*24)	, '/');
setcookie('number_of_post'	, $total_post				, time() + (60*60*24)	, '/');

// LISTING OF SEARCH RESULT //
if(!empty($beyond_array) && isset($beyond_array)) {
	if(isset($beyond_array['Item']) && !empty($beyond_array['Item'])) { ?>
		<h3>Your Job search results below:</h3><?php
			$search_array 					= $beyond_array['Item'];
			if(isset($search_array[0])) {
				$search_array 				= array_slice($search_array, 0, $total_post);
				foreach($search_array as $key=>$value){ 
					$applyurl 				= isset($value['ApplyURL'])			? $value['ApplyURL']		: '';
					$title					= isset($value['Title']) 			? $value['Title']			: '';
					$companyname			= isset($value['CompanyName'])		? $value['CompanyName']		: '';
					$location				= isset($value['Location'])			? $value['Location']		: '';
					$shortdescription		= isset($value['ShortDescription'])	? $value['ShortDescription']: '';?>
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
				}
			} else {
			
					$applyurl 				= isset($search_array['ApplyURL'])			? $search_array['ApplyURL']		: '';
					$title					= isset($search_array['Title']) 			? $search_array['Title']			: '';
					$companyname			= isset($search_array['CompanyName'])		? $search_array['CompanyName']		: '';
					$location				= isset($search_array['Location'])			? $search_array['Location']		: '';
					$shortdescription		= isset($search_array['ShortDescription'])	? $search_array['ShortDescription']: '';?>
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
					</div>
				
		<?php	  
			} ?>
		
		<div class="more_jobs">
			<a href="http://www.beyond.com/jobs/job-search.asp?aff=A1C610EB-3496-4C5F-831E-0CEED7168DEA&a=0&k=<?php echo urlencode($_POST['job_keyword']); ?>&l=<?php echo urlencode($_POST['job_location']); ?>" target="_blank">See more Jobs Like these, Click Here</a>
			<IMG SRC="http://ad.doubleclick.net/ad/N7384.6650.BEYOND.COM1/B7788500.2;sz=1x1;ord=<?php echo time() ;?>?" BORDER=0 WIDTH=1 HEIGHT=1 ALT="Advertisement">
		</div>
		<div class="footertxt"><b>Powered by </b> &nbsp; 
			<a href="http://www.beyond.com" target="_blank">
				<IMG SRC="http://ad.doubleclick.net/ad/N7384.6650.BEYOND.COM1/B7788500;sz=149x40;ord=<?php echo time() ;?>?" BORDER=0 WIDTH=149 HEIGHT=40 ALT="Advertisement">
			</a>
		</div>
		<?php
	} else {
		echo "<div class='record_error'>Record not found</div>";
	}
}  else {
	echo "<div class='record_error'>Record not found</div>";
}?>
