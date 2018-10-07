<?php

// test existence of link

//----------------------------------------------------------------------------------------
function get_http_code($url)
{
	$http_code = 0;
	
	$opts = array(
	  CURLOPT_URL =>$url,
	  //CURLOPT_FOLLOWLOCATION => TRUE,
	  CURLOPT_RETURNTRANSFER => TRUE
	);
	
	$ch = curl_init();
	curl_setopt_array($ch, $opts);
	$data = curl_exec($ch);
	$info = curl_getinfo($ch); 
	
	$http_code = $info['http_code'];
	
	curl_close($ch);
	
	return $http_code;
}

//----------------------------------------------------------------------------------------
$filename = dirname(__FILE__) . '/pdfs.txt';

$file_handle = fopen($filename, "r");

while (!feof($file_handle)) 
{
	$pdf = trim(fgets($file_handle));
	
	if (preg_match('/^#/', $pdf))
	{
		// skip
	}
	else
	{
		//echo $pdf . "\t" . get_http_code($pdf) . "\n";
		echo  get_http_code($pdf) . "\t" .  $pdf . "\n";
	}	
}



?>