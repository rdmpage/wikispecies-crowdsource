<?php

require_once(dirname(__FILE__) . '/couchsimple.php');
require_once(dirname(__FILE__) . '/merge_records.php');

//----------------------------------------------------------------------------------------
// Get hash for document $id
function doc_to_hash($id)
{
	global $config;
	global $couch;
	
	$hash = array();

	$url = '_design/matching/_view/doc_to_hash' . '?key=' . urlencode('"' . $id . '"');
	
	$resp = $couch->send("GET", "/" . $config['couchdb_options']['database'] . "/" . $url);

	if ($resp)
	{
		$response_obj = json_decode($resp);
		
		if (!isset($response_obj->error))
		{
			if (count($response_obj->rows) == 1) {
				$hash = $response_obj->rows[0]->value;
			}		
		}
	}
	
	return $hash;
}


//----------------------------------------------------------------------------------------
// Get DOI for document $id
function doc_to_doi($id)
{
	global $config;
	global $couch;

	$doi = '';

	$url = '_design/matching/_view/doc_to_doi' . '?key=' . urlencode('"' . $id . '"');
	
	$resp = $couch->send("GET", "/" . $config['couchdb_options']['database'] . "/" . $url);

	if ($resp)
	{
		$response_obj = json_decode($resp);
		
		if (!isset($response_obj->error))
		{
			if (count($response_obj->rows) == 1) {
				$doi = $response_obj->rows[0]->value;
			}		
		}
	}
	
	return $doi;
}

//----------------------------------------------------------------------------------------
// Wikispecies-specific clustering where we may have > 1 reference per document
function cluster_by_doi_wikispecies($doi) 
{
	global $config;
	global $couch;
	
	// list of works that have been clustered
	//$works_clustered = [];
	
	$url = '_design/matching/_view/doi?key=' . urlencode('"' . $doi . '"');
	
	$resp = $couch->send("GET", "/" . $config['couchdb_options']['database'] . "/" . $url);
	
	$response_obj = json_decode($resp);
	
	$records = array();

	foreach ($response_obj->rows as $row)
	{
		// Wikispecies-specific
		$one_record = new stdclass;
		$one_record->id = $row->id;
		$one_record->key = $row->key;
		$one_record->value = $row->value;
		
		$records[] = $one_record;
	}
	
	// Do we have more than one work with this DOI?
	if (count($records) > 1)
	{
		// Records that could be clustered		
		echo "Works with this doi:\n";
		print_r($records);
		
		// Find clusters for these records
		$clusters = merge_records($records, false);
		
		echo "Clusters:\n";
		print_r($clusters);
		
		// merge documents...
		update($clusters);
		
	}	
}

//----------------------------------------------------------------------------------------
// Wikispecies-specific clustering where we may have > 1 reference per document
function cluster_by_hash_wikispecies($hash) 
{
	global $config;
	global $couch;
	
	// list of works that have been clustered
	//$works_clustered = [];
	
	//print_r($hash);
	
	$url = '_design/matching/_view/hash?key=' . urlencode(json_encode($hash));
	
	$resp = $couch->send("GET", "/" . $config['couchdb_options']['database'] . "/" . $url);
	
	$response_obj = json_decode($resp);
	
	$records = array();

	foreach ($response_obj->rows as $row)
	{
		// Wikispecies-specific
		$one_record = new stdclass;
		$one_record->id = $row->id;
		$one_record->key = $row->key;
		$one_record->value = $row->value;
		
		$records[] = $one_record;
	}
	
	// Do we have more than one work with this hash?
	if (count($records) > 1)
	{
		// Records that could be clustered		
		echo "Works with this hash:\n";
		print_r($records);
		
		// Find clusters for these records
		$clusters = merge_records($records, true);
		
		echo "Clusters:\n";
		print_r($clusters);
		
		// merge documents...
		update($clusters);
		
	}	
}

//----------------------------------------------------------------------------------------

function update($clusters)
{
	global $config;
	global $couch;
	
	if (count($clusters) > 0)
	{
		foreach ($clusters as $k => $members)
		{
			$cluster_id = $members[0]->id . '#' . ($members[0]->index + 1);
			
			echo $cluster_id . "\n";
		
			foreach ($members as $member)
			{
				print_r($member);
				
				// "merge" by updating cluster_id					
				$resp = $couch->send("GET", "/" . $config['couchdb_options']['database'] . "/" . urlencode($member->id));
				if ($resp)
				{
					$doc = json_decode($resp);
					if (!isset($doc->error))
					{
						// Set cluster_id to id for this cluster
						$doc->references[$member->index]->csl->cluster_id = $cluster_id;
						
						// print_r($doc->references[$member->index]->csl);
									
						// update
						$couch->add_update_or_delete_document($doc, $doc->_id, 'update');						
					}
				}	
				
				//$works_clustered[] = $member;					
			}
		}
	}
}

//----------------------------------------------------------------------------------------


// list of documents to check or cluster


if (0)
{
	$doi = '10.1080/00222936900770481';

	$dois = array(
	'10.1111/j.1440-6055.2008.00647.x',
	'10.3853/j.0067-1975.62.2010.1556'
	);
	
	$dois = array(
	'10.1080/01647959608684104'
	);
	

	foreach ($dois as $doi)
	{
		cluster_by_doi_wikispecies($doi);
	}
}

if (0)
{
	$hashes = array(
		array(1994, 58, 123),
		array(1966, 12, 307),
		array(1965, 9, 21),
	);

	$hashes = array(
		array(1871, 43, 111) // 550 members
	);
		
	foreach ($hashes as $hash) 
	{
		cluster_by_hash_wikispecies($hash);
	}
}

if (1)
{
	$filename = 'summary/hash.json';
	$json = file_get_contents($filename);
	
	$obj = json_decode($json);
	
	foreach ($obj->rows as $row)
	{
		$hash = $row->key;
		if ($row->value > 1)
		{
			print_r($row->key);
			
			cluster_by_hash_wikispecies($hash);
		}
		
	
	}
	
}





