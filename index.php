<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />

	<script src="//code.jquery.com/jquery-1.12.4.js"></script>	

	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

	<!-- Optional theme -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

	<!-- Latest compiled and minified JavaScript -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>  
	
	<link rel="stylesheet" href="academicons-1.8.6/css/academicons.min.css"/>
  
	<style type="text/css">	
	body { 
	  padding-top:50px; 
	}
	</style>
	
	<script>
	
	// https://osric.com/chris/accidental-developer/2012/11/balancing-tags-in-html-and-xhtml-excerpts/
	
// balance:
// - takes an excerpted or truncated XHTML string
// - returns a well-balanced XHTML string
function balance(string) {
  // Check for broken tags, e.g. <stro
  // Check for a < after the last >, indicating a broken tag
  if (string.lastIndexOf("<") > string.lastIndexOf(">")) {
    // Truncate broken tag
    string = string.substring(0,string.lastIndexOf("<"));
  }

  // Check for broken elements, e.g. &lt;strong&gt;Hello, w
  // Get an array of all tags (start, end, and self-closing)
  var tags = string.match(/<[^>]+>/g);
  var stack = new Array();
  for (tag in tags) {
    if (tags[tag].search("/") <= 0) {
      // start tag -- push onto the stack
      stack.push(tags[tag]);
    } else if (tags[tag].search("/") == 1) {
      // end tag -- pop off of the stack
      stack.pop();
    } else {
      // self-closing tag -- do nothing
    }
  }

  // stack should now contain only the start tags of the broken elements,
  // the most deeply-nested start tag at the top
  while (stack.length > 0) {
    // pop the unmatched tag off the stack
    var endTag = stack.pop();
    // get just the tag name
    endTag = endTag.substring(1,endTag.search(/[ >]/));
    // append the end tag
    string += "</" + endTag + ">";
  }

  // Return the well-balanced XHTML string
  return(string);
}	
	</script>
	
	<script>
	
       //--------------------------------------------------------------------------------
		function doi_in_wikidata(doi, element_id) {
			var sparql = `SELECT *
WHERE
{
  ?work wdt:P356 "DOI" .
}`;

			sparql = sparql.replace(/DOI/, doi.toUpperCase());
	
			$.getJSON('https://query.wikidata.org/bigdata/namespace/wdq/sparql?query=' + encodeURIComponent(sparql),
				function(data){
				  if (data.results.bindings.length == 1) {
            		html = 'DOI in Wikidata <a class="external" href="' + data.results.bindings[0].work.value + '" target="_new">' + data.results.bindings[0].work.value.replace("http://www.wikidata.org/entity/","") + '</a>';
				  } else {
				     html = 'DOI not in Wikidata';         
				  }
				  document.getElementById(element_id).innerHTML = html;
			});			

		}	
		
		
		
		// ISSN in Wikidata?
		
		
		// People in Wikidata?
       //--------------------------------------------------------------------------------
		function author_in_wikidata(wikispecies, element_id) {

			document.getElementById(element_id).innerHTML = '';
			
			$.getJSON('wikidata-author.php?name=' + encodeURIComponent(wikispecies) + '&callback=?',
				function(data){
				  if (data.hits.length == 1) {
				    var html = '';
				    html += decodeURIComponent(data.query) + '<br />';
				    html += '<ul style="list-style-type:none">';
				    for (var j in data.hits[0]) {
				    	switch (j) {
	
				    		case 'google':
				    			html += '<li>' + '<i class="ai ai-google-scholar"></i>' + ' ' + '<a href="https://scholar.google.com/citations?user=' + data.hits[0][j] + '" target="_new">' + data.hits[0][j] + '</a>' + '</li>';
				    			break;				    					    	
				    	
				    		case 'orcid':
				    			html += '<li>' + '<i class="ai ai-orcid"></i>'  + ' ' + '<a href="https://orcid.org/' + data.hits[0][j] + '" target="_new">' + data.hits[0][j] + '</a>' + '</li>';
				    			break;				    					    	
				    	
				    		case 'researchgate':
				    			html += '<li>' + '<i class="ai ai-researchgate"></i>'  + ' ' + '<a href="https://www.researchgate.net/profile/' + data.hits[0][j] + '" target="_new">' + data.hits[0][j] + '</a>' + '</li>';
				    			break;				    	
				    	
				    		case 'wikidata':
				    			html += '<li>' + '<img src="images/wikidata.png" height="16" />' + ' ' + '<a href="https://www.wikidata.org/wiki/' + data.hits[0][j] + '" target="_new">' + data.hits[0][j] + '</a>' + '</li>';
				    			break;				    	
				    	
				    		case 'zoobank':
				    			html += '<li>' + 'ZB' + ' ' + '<a href="http://zoobank.org/' + data.hits[0][j] + '" target="_new">' + data.hits[0][j] + '</a>' + '</li>';
				    			break;
				    			
				    		default:
				    			break;
				    	
				    	}
				    }
				    html += '</ul>';
					document.getElementById(element_id).innerHTML = document.getElementById(element_id).innerHTML + html;
				  }				  
			});			
		}			
		
		
		
		// Other identifier in Wikidata e.g. ISBN?
		
		
		// OpenURL-style query in Wikidata?
		
		
		
			
       //--------------------------------------------------------------------------------
	
	function show_record(id) {
		$('#record').html(id);
		$('#wikidata-doi').html('');
		$('#wikidata-author').html('');
		
		if (id.match(/%23/)) {
		} else {
			id = encodeURI(id);
		}
		
		$.getJSON('proxy.php?url=' 
				+ encodeURI('http://user:7WbQZedlAvzQ@35.204.73.93/elasticsearch/wikispecies/_doc/' + id)
				+ '&callback=?',
			function(data){ 
				if (data._source) {
					var html = '';
					html += '<span style="font-size:1.5em;line-height:1em;">' + data._source.search_result_data.name + '</span>' + '<br />';
					html += data._source.search_result_data.description + '<br />';
					
					html += data._source.search_result_data.creator.join(' ') + '<br />';

					/*
					if (data._source.search_result_data.WIKISPECIES) {
						html += data._source.search_result_data.WIKISPECIES.join(' | ') + '<br />';
					}
					*/
					
					if (data._source.search_result_data.doi) {
						html += '<i class="ai ai-doi"></i>' + data._source.search_result_data.doi + '<br />';
					}

					if (data._source.search_result_data.pdf) {
						html += '<a href="' + data._source.search_result_data.pdf + '" target="_new">PDF</a><br />';
					}
					
					// Wikidata searches
					if (data._source.search_result_data.doi) {
						doi_in_wikidata(data._source.search_result_data.doi, 'wikidata-doi');
					}
					
					if (data._source.search_result_data.WIKISPECIES) {
						for (var j in data._source.search_result_data.WIKISPECIES) {
							author_in_wikidata(data._source.search_result_data.WIKISPECIES[j], 'wikidata-author');
						}
						
					}

					
					
					$('#record').html(html);
				
				}
			
			});       
		
		
		
	}
	
	
	
	function do_text_search(text) {
	
	$('#content').html('');
	$('#record').html('');
	$('#wikidata-doi').html('');
	$('#wikidata-author').html('');
	
	var q = {
			"size":50,
				"query": {
					"bool" : {
						"must" : [ {
				   "multi_match" : {
				  "query": "",
				  "fields":["search_data.fulltext", "search_data.fulltext_boosted^4"] 
				}
				}],
			"filter": []
				}
			},
			

"highlight": {
      "pre_tags": [
         "<span style=\"background-color:yellow;\">" 
      ],
      "post_tags": [
         "<\/span>"
      ],
      "fields": {
         "search_data.fulltext": {},
         "search_data.fulltext_boosted": {}
      }
   },			
			
			"aggs": {
			
	
	"by_cluster_id": {
		"terms": {
			"field": "search_data.cluster_id.keyword",
			"size": 50,
			"order": {
				"max_score.value": "desc"
			}
		},
	
	
		"aggs": {
			"max_score": {
				"max": {
					"script": {
						"lang": "painless",
						"inline": "_score"
					}
				}
			}
		}
	},
			
			
			
			"type" :{
				"terms": { "field" : "search_data.type.keyword" }
			  },
			  "year" :{
				"terms": { "field" : "search_data.year" }
			  },
			  "container" :{
				"terms": { "field" : "search_data.container.keyword" }
			  },
			  "author" :{
				"terms": { "field" : "search_data.author.keyword" }
			  },

			}

	
			};
			
		q.query.bool.must[0].multi_match.query = text;
		
		console.log(JSON.stringify(q, null, 2));
		
		
		$.getJSON('proxy.php?url=' 
				+ encodeURI('http://user:7WbQZedlAvzQ@35.204.73.93/elasticsearch/wikispecies/_search?pretty')
				+ '&postdata=' + JSON.stringify(q)
				+ '&callback=?',
			function(data){        
			
			
				// do stuff here...
				if (data.hits) {
					if (data.hits.total > 0) {
					
						// Cluster by cluster_id
						/*
						var c = {};
						for (var i in data.hits.hits) {
							var cluster_id = data.hits.hits[i]._source.search_data.cluster_id;
							if (!c[cluster_id]) {
								c[cluster_id] = [];
							}
							c[cluster_id].push
						
						}
						*/
						
						var clusters = {};
						for (var i in data.aggregations.by_cluster_id.buckets) {
							clusters[data.aggregations.by_cluster_id.buckets[i].key] = [];
						}

						for (var i in data.hits.hits) {
							var cluster_id = data.hits.hits[i]._source.search_data.cluster_id;
							console.log(cluster_id);
							clusters[cluster_id].push(data.hits.hits[i]._source.search_result_data);						
						}
						
						var html = '';
						for (var i in clusters) {
							//html += clusters[i][0].name + '<br />';
							
							var style = '';
							if (clusters[i].length > 1) {
								style += 'border-left:2px solid orange;background-color:#FFFFCC;';
							}
							
							for (var j in clusters[i]) {
								html += '<div style="padding:10px;' + style + '">';
								html += '<span style="font-size:1.5em;line-height:1em;"' 
								
									// this really should be record id not cluster id, but we don't have a way to
									// access this the way I've set up search_data
								
									+ ' onclick="show_record(\'' + i.replace('#', '%23') + '\')"'									
									+ '>'
									
									//+ '[' + clusters[i][j].length + '] '
									+ balance(clusters[i][j].name)
									+ '</span><br />';
								html += '<span style="color:green;">' + clusters[i][j].description + '</span><br />';
						
								/*
								if (data.hits.hits[i].highlight["search_data.fulltext"]) {
									for (var j in data.hits.hits[i].highlight["search_data.fulltext"]) {
										html += '<div>' + data.hits.hits[i].highlight["search_data.fulltext"][j] + '</div>';
									}
								}
								*/
						
						
								html += '<a href="' + clusters[i][j].url + '" target="_new">' + clusters[i][j].url + '</a>' + '<br />';
						
								if (clusters[i][j].doi) {
									html += '<a href="https://doi.org/' + clusters[i][j].doi + '" target="_new">' + 'DOI:' + clusters[i][j].doi + '</a>' + '<br />';							
								}

								if (clusters[i][j].pdf) {
									html += '<a style="color:white;background-color:red;" href="' + clusters[i][j].pdf + '" target="_new">' + clusters[i][j].pdf + '</a>' + '<br />';							
								}
						
								html += '</div>';
							}
							
						
						}
						$('#content').html(html);
						
					
						// List raw hits
						if (0)
						{
							var html = '';
							for (var i in data.hits.hits) {
								html += '<div style="padding:10px;">';
								html += '<span style="font-size:1.5em;line-height:1em;">' + data.hits.hits[i]._source.search_result_data.name + '</span><br />';
								html += '<span style="color:green;">' + data.hits.hits[i]._source.search_result_data.description + '</span><br />';
							
								if (data.hits.hits[i].highlight["search_data.fulltext"]) {
									for (var j in data.hits.hits[i].highlight["search_data.fulltext"]) {
										html += '<div>' + data.hits.hits[i].highlight["search_data.fulltext"][j] + '</div>';
									}
								}
							
							
								html += '<a href="' + data.hits.hits[i]._source.search_result_data.url + '" target="_new">' + data.hits.hits[i]._source.search_result_data.url + '</a>' + '<br />';
							
								if (data.hits.hits[i]._source.search_result_data.doi) {
									html += '<a href="https://doi.org/' + data.hits.hits[i]._source.search_result_data.doi + '" target="_new">' + 'DOI:' + data.hits.hits[i]._source.search_result_data.doi + '</a>' + '<br />';							
								}
							
								html += '</div>';
							}
				
							$('#content').html(html);
						}
					}
				}
        
    });

	
	
	}
	
	</script>
	

</head>
<body class="container">

 <header>
 <nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container-fluid">

     <form class="navbar-form navbar-left" role="search" > 
       <div class="form-group">
         <input id="search" type="text" class="form-control" placeholder="Search" name="q" value="">
       </div>
      </form> 

      
        <ul class="nav navbar-nav">
            <li><a href="/">Home</a></li>
            <!-- <li><a href="/titles">Titles</a></li> -->
            <li><a href="/about">About</a></li> 
        </ul>

    </div>
</nav>
 </header>
 
   <div class="container">

	  <div class="row">
      <div class="col-md-8" style="border-right:1px solid rgb(224,224,224);">
        <div style="padding:10px;" class="row" id="content">
        </div>
      </div>
      <div class="col-md-3">
        <div class="row affix" id="facet">         
          <div style="padding:10px;">
            <div id="record"></div>
            <div id="wikidata-doi"></div>
             <div id="wikidata-author"></div>
          </div>
          
         

          
        </div>
      </div>
    </div>
  </div>

<script>
	document.getElementById('search').addEventListener('keypress', function(event) {
	        if (event.keyCode == 13) {
	            do_text_search(document.getElementById('search').value);
	            event.preventDefault();
	        }
	    });
</script>

</body>
</html>

