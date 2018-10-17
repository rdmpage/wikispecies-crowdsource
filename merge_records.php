<?php

// Support for clustering references
// Use https://en.wikipedia.org/wiki/Disjoint-set_data_structure to find components of
// a graph, these are the clusters.

error_reporting(E_ALL);

require_once (dirname(__FILE__) . '/fingerprint.php');
require_once (dirname(__FILE__) . '/lcs.php');


//----------------------------------------------------------------------------------------
// Disjoint-set data structure

$parents = array();

function makeset($x) {
	global $parents;
	
	$parents[$x] = $x;
}

function find($x) {
	global $parents;
	
	if ($x == $parents[$x]) {
		return $x;
	} else {
		return find($parents[$x]);
	}
}

function union($x, $y) {
	global $parents;
	
	$x_root = find($x);
	$y_root = find($y);
	$parents[$x_root] = $y_root;
	
}

//----------------------------------------------------------------------------------------
// Merge records
// Returns array of clusters that can then be merged
function merge_records ($records, $check = false)
{
	global $parents;
	
	$clusters = array();
	
	// If we have more than one reference with the same hash, compare and cluster
	$n = count($records);

	if ($n > 1)
	{
	
		for ($i = 0; $i < $n; $i++)
		{
			makeset($i);
		}
		
		$pairwise = ($n < 10);

		if (!$pairwise)
		{
			// compare just with first one, to avoid explosion
			$i = 0;
			
			for ($j = 1; $j < $n; $j++)
			{
				// use string matching to check match
				if ($check)
				{					
					// Wikispecies-specific
					$v1 = $records[$i]->value[1];
					$v2 = $records[$j]->value[1];
					
					// trim so we don't get an out of memory error on long strings
					$v1 = substr($v1, 0, 100);
					$v2 = substr($v2, 0, 100);
		
					$v1 = finger_print($v1);
					$v2 = finger_print($v2);
				
					if (0)
					{
						echo $v1 . "\n";
						echo $v2 . "\n";
					}
		
					$lcs = new LongestCommonSequence($v1, $v2);
					$d = $lcs->score();
		
					$score = min($d / strlen($v1), $d / strlen($v2));
		
					if ($score > 0.80)
					{
						union($j, $i);
					}					
				}
				else
				{
					// Just merge (e.g., if set of records is based on sharing an identifier)
					union($j, $i);
				}
			}		
		}
		else
		{
			// Pairwise comparison, explodes if n is too large
			for ($i = 1; $i < $n; $i++)
			{
				for ($j = 0; $j < $i; $j++)
				{
					// use string matching to check match
					if ($check)
					{					
						// Wikispecies-specific
						$v1 = $records[$i]->value[1];
						$v2 = $records[$j]->value[1];
			
						$v1 = finger_print($v1);
						$v2 = finger_print($v2);
					
						if (0)
						{
							echo $v1 . "\n";
							echo $v2 . "\n";
						}
			
						$lcs = new LongestCommonSequence($v1, $v2);
						$d = $lcs->score();
			
						$score = min($d / strlen($v1), $d / strlen($v2));
			
						if ($score > 0.80)
						{
							union($i, $j);
						}					
					}
					else
					{
						// Just merge (e.g., if set of records is based on sharing an identifier)
						union($i, $j);
					}
				}
			}
		}
		
		if (0)
		{
			for ($i = 0; $i < $n; $i++)
			{
				echo $i . '->' . $parents[$i] . "\n";
			}		
		}		
			
		// Get list of components of graph, which are the sets rooted on each parent node
		$blocks = array();
	
		for ($i = 0; $i < $n; $i++)
		{
			$p = $parents[$i];
		
			if (!isset($blocks[$p]))
			{
				$blocks[$p] = array();
			}
			$blocks[$p][] = $i;
		}
		
		if (0)
		{
			echo "Blocks\n";
			print_r($blocks);
		}
	
		// merge things 
		foreach ($blocks as $k => $block)
		{
			if (count($block) > 1)
			{
				$clusters[$k] = array();
				
				foreach ($block as $i)
				{
					// wikispecies
					$member = new stdclass;
					$member->id = $records[$block[$i]]->id;
					$member->index = $records[$block[$i]]->value[0];
					$clusters[$k][] = $member;
				}
			}
		}
	
	}
	
	return $clusters;
	
}


?>
