#!/bin/sh

echo 'elastic-6026.json'
curl http://user:7WbQZedlAvzQ@35.204.73.93/elasticsearch/wikispecies/_bulk -H 'Content-Type: application/x-ndjson' -XPOST --data-binary '@elastic-6026.json'  --progress-bar | tee /dev/null
echo ''
