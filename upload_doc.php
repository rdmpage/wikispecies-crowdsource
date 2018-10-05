<?php

// Upload all references on one Wikispecies page to Elastic

require_once (dirname(__FILE__) . '/config.inc.php');
require_once (dirname(__FILE__) . '/couchsimple.php');
require_once (dirname(__FILE__) . '/elastic_utils.php');

// id is Wikispecies page name

$ids = array('Cangoderces');
$ids = array('Template:Wang,_Li_%26_Haddad,_2018');

foreach ($ids as $id)
{
	$json = $couch->send("GET", "/" . $config['couchdb_options']['database'] . "/" . urlencode($id));
	
	$doc = json_decode($json);
	
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
	
?>