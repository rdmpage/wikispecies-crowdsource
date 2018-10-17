# Generate summaries of views

## Hash

```
curl http://127.0.0.1:5984/wikispecies-crowdsource/_design/matching/_view/hash_count?group_level=3 > hash.json
```

## DOI

```
curl http://127.0.0.1:5984/wikispecies-crowdsource/_design/matching/_view/doi_count?group_level=3 > doi.json
```

