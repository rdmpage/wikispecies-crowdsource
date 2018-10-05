<?php

// Send data from CouchDB to Elasticsearch
require_once(dirname(__FILE__) . '/config.inc.php');
require_once(dirname(__FILE__) . '/couchsimple.php');
require_once(dirname(__FILE__) . '/elastic.php');

//----------------------------------------------------------------------------------------
// Upload one search document (use a search data "schema")
function doc_to_elastic($id)
{
	global $config;
	global $couch;
	global $elastic;

	$url = '_design/export/_view/elastic' . '?key=' . urlencode('"' . $id . '"');
	
	$resp = $couch->send("GET", "/" . $config['couchdb_options']['database'] . "/" . $url);
	
	if ($resp)
	{
		$response_obj = json_decode($resp);
		if (!isset($response_obj->error))
		{
			$doc = $response_obj->rows[0]->value;
			
			$doc->id = $response_obj->rows[0]->id;

			$elastic_doc = new stdclass;
			$elastic_doc->doc = $doc;
			$elastic_doc->doc_as_upsert = true;

			//print_r($elastic_doc);

			$elastic->send('POST', '_doc/' . urlencode($id) . '/_update', json_encode($elastic_doc));					
		
		}
	}
}

// upload a record

// id is id of each reference, not the id of the document (whihc is Wikispecies page)

$ids = array('Boganiidae#6', 'Cavognathidae#2');
foreach ($ids as $id)
{
	doc_to_elastic($id);
}


?>

