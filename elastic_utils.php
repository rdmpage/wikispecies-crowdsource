<?php


require_once(dirname(__FILE__) . '/config.inc.php');
require_once(dirname(__FILE__) . '/couchsimple.php');
require_once(dirname(__FILE__) . '/elastic.php');

// Shared Elastic code

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
			//print_r($response_obj);
			
			if (count($response_obj->rows) > 0)
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
}


?>