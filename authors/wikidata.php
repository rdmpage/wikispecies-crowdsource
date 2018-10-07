<?php

// match Wikispecies authors to Wikidata

//----------------------------------------------------------------------------------------
function get($url)
{
	$data = null;
	
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
	
	return $data;
}

//----------------------------------------------------------------------------------------
// Page might be a direct, if so extract destination
function get_redirect($name)
{
	$redirect = $name;
	
	$url = 'https://species.wikimedia.org/w/index.php?title=' . $name . '&action=edit';
	
	$html = get($url);
	
	//echo $html;
	
	if (preg_match('/#REDIRECT\s+\[\[(?<redirect>.*)\]\]/Uu', $html, $m))
	{
		//print_r($m);
		$redirect = str_replace(' ', '_', $m['redirect']);
	}
	
	return $redirect;

}


//----------------------------------------------------------------------------------------
$filename = dirname(__FILE__) . '/authors.txt';
//$filename = dirname(__FILE__) . '/test.txt';

$file_handle = fopen($filename, "r");

$current_author = '';

while (!feof($file_handle)) 
{
	$name = trim(fgets($file_handle));
	
	if ($name != $current_author)
	{
		echo "\n\n-----------------------\n";
		echo $name . "\n";
		
		$query_name = $name;
		
		// Page may be a redirect...
		
		$redirect_name = get_redirect($query_name);
		
		if ($redirect_name != $name)
		{
			echo "redirect=$redirect_name\n";
			$query_name = $redirect_name;
		}
	
		$sparql = "SELECT *
WHERE
{
    VALUES ?article {<https://species.wikimedia.org/wiki/" . $query_name . ">}
	?article schema:about ?item .
    ?item wdt:P31 wd:Q5 .
  OPTIONAL {
	   ?item wdt:P213 ?isni .
		}
	  OPTIONAL {
	   ?item wdt:P214 ?viaf .
		}    
	  OPTIONAL {
	   ?item wdt:P18 ?image .
		} 
	  OPTIONAL {
	   ?item wdt:P496 ?orcid .
		} 	
	  OPTIONAL {
	   ?item wdt:P2038 ?researchgate .
		} 					
	  OPTIONAL {
	   ?item wdt:P586 ?ipni .
		} 
	  OPTIONAL {
	   ?item wdt:P2006 ?zoobank .
		} 		
}";

//echo $sparql . "\n";
	
		$url = 'https://query.wikidata.org/bigdata/namespace/wdq/sparql?query=' . urlencode($sparql) . '&format=json';
		
		$json = get($url);
		
		//echo $json;
		
		$obj = json_decode($json);
		
		if (isset($obj->results))
		{
			if (isset($obj->results->bindings))
			{
				foreach ($obj->results->bindings as $binding)
				{
			
					foreach ($binding as $k => $v)
					{
						//echo $k;
						//print_r($v);
						switch ($k)
						{
							case 'isni':
								echo "isni=" . $v->value . "\n";
								break;						

							case 'ipni':
								echo "ipni=" . $v->value . "\n";
								break;						
						
							case 'item':
								$wikidata = $v->value;
								$wikidata = preg_replace('/https?:\/\/www.wikidata.org\/entity\//', '', $wikidata);
								echo "wikidata=" . $wikidata . "\n";
								break;
								
							case 'orcid':
								echo "orcid=" . $v->value . "\n";
								break;
								
							case 'researchgate':
								echo "researchgate=" . $v->value . "\n";
								break;														
								
							case 'viaf':
								echo "viaf=" . $v->value . "\n";
								break;
								
							case 'zoobank':
								echo "zoobank=" . $v->value . "\n";
								break;
					
							default:
								break;
						}
					}
				}
			}
		}
	
	
		$current_author = $name;
	
	}

}



?>