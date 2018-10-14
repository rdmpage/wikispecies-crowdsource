#!/bin/sh

echo 'elastic-20000.json'
curl http://user:7WbQZedlAvzQ@35.204.73.93/elasticsearch/wikispecies/_bulk -H 'Content-Type: application/x-ndjson' -XPOST --data-binary '@elastic-20000.json'  --progress-bar | tee /dev/null
echo ''
echo 'elastic-40000.json'
curl http://user:7WbQZedlAvzQ@35.204.73.93/elasticsearch/wikispecies/_bulk -H 'Content-Type: application/x-ndjson' -XPOST --data-binary '@elastic-40000.json'  --progress-bar | tee /dev/null
echo ''
echo 'elastic-60000.json'
curl http://user:7WbQZedlAvzQ@35.204.73.93/elasticsearch/wikispecies/_bulk -H 'Content-Type: application/x-ndjson' -XPOST --data-binary '@elastic-60000.json'  --progress-bar | tee /dev/null
echo ''
echo 'elastic-80000.json'
curl http://user:7WbQZedlAvzQ@35.204.73.93/elasticsearch/wikispecies/_bulk -H 'Content-Type: application/x-ndjson' -XPOST --data-binary '@elastic-80000.json'  --progress-bar | tee /dev/null
echo ''
echo 'elastic-100000.json'
curl http://user:7WbQZedlAvzQ@35.204.73.93/elasticsearch/wikispecies/_bulk -H 'Content-Type: application/x-ndjson' -XPOST --data-binary '@elastic-100000.json'  --progress-bar | tee /dev/null
echo ''
echo 'elastic-120000.json'
curl http://user:7WbQZedlAvzQ@35.204.73.93/elasticsearch/wikispecies/_bulk -H 'Content-Type: application/x-ndjson' -XPOST --data-binary '@elastic-120000.json'  --progress-bar | tee /dev/null
echo ''
echo 'elastic-140000.json'
curl http://user:7WbQZedlAvzQ@35.204.73.93/elasticsearch/wikispecies/_bulk -H 'Content-Type: application/x-ndjson' -XPOST --data-binary '@elastic-140000.json'  --progress-bar | tee /dev/null
echo ''
echo 'elastic-160000.json'
curl http://user:7WbQZedlAvzQ@35.204.73.93/elasticsearch/wikispecies/_bulk -H 'Content-Type: application/x-ndjson' -XPOST --data-binary '@elastic-160000.json'  --progress-bar | tee /dev/null
echo ''
echo 'elastic-179374.json'
curl http://user:7WbQZedlAvzQ@35.204.73.93/elasticsearch/wikispecies/_bulk -H 'Content-Type: application/x-ndjson' -XPOST --data-binary '@elastic-179374.json'  --progress-bar | tee /dev/null
echo ''
