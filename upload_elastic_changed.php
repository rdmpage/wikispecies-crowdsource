<?php

// Upload Elastic documents from CouchDB to Elastic

require_once (dirname(__FILE__) . '/config.inc.php');
require_once (dirname(__FILE__) . '/couchsimple.php');
require_once (dirname(__FILE__) . '/elastic_utils.php');

$limit = 10;

$url = '_changes?limit=' . $limit . '&descending=true';

//echo $url . "\n";

$resp = $couch->send("GET", "/" . $config['couchdb_options']['database'] . "/" . $url);

$obj = json_decode($resp);

print_r($obj);

foreach ($obj->results as $result)
{
	$id = $result->id;
	
	if (!preg_match('/^_design/', $id))
	{
		//echo $id . "\n";
		
		// fetch changed document
		$json = $couch->send("GET", "/" . $config['couchdb_options']['database'] . "/" . urlencode($id));
		
		$doc = json_decode($json);
		
		if (isset($doc->references))
		{
			// If Template then id is page id, otherwise it's index in array of references
			if (isset($doc->references))
			{
				if (preg_match('/^Template:/', $doc->_id))
				{
					doc_to_elastic($doc->_id);
				}
				else
				{
					// upload references
					foreach ($doc->references as $k => $work)
					{
						doc_to_elastic($doc->_id . '#' . ($k + 1));
					}
				}
			}
		}
	}
	
}


	
?>