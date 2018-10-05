<?php

// Send data from CouchDB to Elasticsearch

require_once(dirname(__FILE__) . '/elastic_utils.php');


// upload a record

// id is id of each reference, not the id of the document (whihc is Wikispecies page)

$ids = array('Boganiidae#6', 'Cavognathidae#2');
foreach ($ids as $id)
{
	doc_to_elastic($id);
}


?>

