<?php

// match Wikispecies authors to Wikidata as web service


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

	if (preg_match('/#REDIRECT\s+\[\[(?<redirect>[^"].*)\]\]/Uu', $html, $m))
	{
		//print_r($m);
		$redirect = str_replace(' ', '_', $m['redirect']);
	}
	
	return $redirect;

}

//----------------------------------------------------------------------------------------


$name = 'Shu-Xia_Wang';
$name = 'A._Slipinski';

if (isset($_GET['name']))
{
	$name = $_GET['name'];
}

$callback = '';
if (isset($_GET['callback']))
{
	$callback = $_GET['callback'];
}
	
$query_name = $name;

// Page may be a redirect...

$redirect_name = get_redirect($query_name);

if ($redirect_name != $name)
{
	$query_name = $redirect_name;
}

$query_name = urlencode($query_name);

// <https://species.wikimedia.org/wiki/Stanislaw_Adam_%C5%9Alipi%C5%84ski>


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
   ?item wdt:P1960 ?google .
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

//echo "<pre>" . htmlentities($sparql) . "</pre>\n";
	
$url = 'https://query.wikidata.org/bigdata/namespace/wdq/sparql?query=' . urlencode($sparql) . '&format=json';

$json = get($url);

//echo $json;

$obj = json_decode($json);

$result = new stdclass;
$result->query = $query_name;
$result->hits = array();

if (isset($obj->results))
{
	if (isset($obj->results->bindings))
	{
		foreach ($obj->results->bindings as $binding)
		{
			$hit = new stdclass;
			
			foreach ($binding as $k => $v)
			{
				switch ($k)
				{
					case 'google':
					case 'isni':
					case 'ipni':
					case 'orcid':
					case 'researchgate':
					case 'viaf':
					case 'zoobank':					
						$hit->{$k} = $v->value;
						break;						
				
					case 'item':
						$wikidata = $v->value;
						$wikidata = preg_replace('/https?:\/\/www.wikidata.org\/entity\//', '', $wikidata);
						$hit->wikidata = $wikidata;
						break;
				
					default:
						break;
				}
			}
			
			$result->hits[] = $hit;
		}
	}
}

if ($callback != '')
{
	echo $callback . '(';
}

echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

if ($callback != '')
{
	echo ')';
}


?>