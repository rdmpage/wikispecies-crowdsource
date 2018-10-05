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
  
	<style type="text/css">	
	body { 
	  padding-top:50px; 
	}
	</style>
	
	<script>
	function do_text_search(text) {
	
	$('#content').html('');
	
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
								style += 'border-left:2px solid orange;background-color:yellow;';
							}
							
							for (var j in clusters[i]) {
								html += '<div style="padding:10px;' + style + '">';
								html += '<span style="font-size:1.5em;line-height:1em;">' 
									//+ '[' + clusters[i][j].length + '] '
									+ clusters[i][j].name 
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
      <div class="col-md-8">
        <div style="padding:10px;" class="row" id="content">
        </div>
      </div>
      <div class="col-md-4">
        <div class="row" id="facet">           
          
          <div>
            <h4>
              Sidebar
            </h4>
            <div id="facet"></div>
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

