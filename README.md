# SlicingDice Official PHP Client (v1.0)
![](https://circleci.com/gh/SlicingDice/slicingdice-php/tree/master.svg?style=shield)

Official PHP client for [SlicingDice](http://www.slicingdice.com/), Data Warehouse and Analytics Database as a Service.  

[SlicingDice](http://www.slicingdice.com/) is a serverless, API-based, easy-to-use and really cost-effective alternative to Amazon Redshift and Google BigQuery.

## Documentation

If you are new to SlicingDice, check our [quickstart guide](http://panel.slicingdice.com/docs/#quickstart-guide) and learn to use it in 15 minutes.

Please refer to the [SlicingDice official documentation](http://panel.slicingdice.com/docs/) for more information on [analytics databases](http://panel.slicingdice.com/docs/#analytics-concepts), [data modeling](http://panel.slicingdice.com/docs/#data-modeling), [indexing](http://panel.slicingdice.com/docs/#data-indexing), [querying](http://panel.slicingdice.com/docs/#data-querying), [limitations](http://panel.slicingdice.com/docs/#current-slicingdice-limitations) and [API details](http://panel.slicingdice.com/docs/#api-details).

## Tests and Examples

Whether you want to test the client installation or simply check more examples on how the client works, take a look at [tests and examples directory](tests_and_examples/).

## Installing

In order to install the PHP client, add the following excerpt to your [`composer.json`](https://getcomposer.org/) file.

```json
{
  "require": {
    "simbiose/slicingdice": "*"
  }
}
```

Install the dependencies by executing the command below:

```bash
composer install
```

## Usage

```php
<?php
user Slicer\SlicingDice;

// Configure the client
$client = new SlicingDice("API_KEY");

// Indexing data
$indexData = array(
    "user1@slicingdice.com" => array(
        "age" => 22
    ),
    "auto-create-fields" => true
);
$client.index($indexData);

// Querying data
$queryData = array(
    "users-between-20-and-40" => array(
        array(
            "age" => array(
                "range" => array(20, 40)
            )
        )
    )
);
print_r($client.countEntity($queryData));
?>
```

## Reference

`SlicingDice` encapsulates logic for sending requests to the API. Its methods are thin layers around the [API endpoints](http://panel.slicingdice.com/docs/#api-details-api-endpoints), so their parameters and return values are JSON-like `Object` objects with the same syntax as the [API endpoints](http://panel.slicingdice.com/docs/#api-details-api-endpoints)

### Attributes

* `$key (string)` - [API key](http://panel.slicingdice.com/docs/#api-details-api-connection-api-keys) to authenticate requests with the SlicingDice API.
* `$timeout (int)` - Amount of time, in seconds, to wait for results for each request.

### Constructor

`_construct($key, $timeout=60)`
* `$key (string)` - [API key](http://panel.slicingdice.com/docs/#api-details-api-connection-api-keys) to authenticate requests with the SlicingDice API.
* `$timeout (int)` - Amount of time, in seconds, to wait for results for each request.

### `getProjects()`
Get all created projects, both active and inactive ones. This method corresponds to a [GET request at /project](http://panel.slicingdice.com/docs/#api-details-api-endpoints-get-project).

#### Request example

```php
<?php
use Slicer\SlicingDice;
$slicingDice = new SlicingDice("MASTER_API_KEY");
print_r($slicingDice->getProjects());
?>
```

#### Output example

```json
{
    "active": [
        {
            "name": "Project 1",
            "description": "My first project",
            "data-expiration": 30,
            "created-at": "2016-04-05T10:20:30Z"
        }
    ],
    "inactive": [
        {
            "name": "Project 2",
            "description": "My second project",
            "data-expiration": 90,
            "created-at": "2016-04-05T10:20:30Z"
        }
    ]
}
```

### `getFields()`
Get all created fields, both active and inactive ones. This method corresponds to a [GET request at /field](http://panel.slicingdice.com/docs/#api-details-api-endpoints-get-field).

#### Request example

```php
<?php
use Slicer\SlicingDice;
$slicingDice = new SlicingDice("MASTER_API_KEY");
print_r($slicingDice->getFields());
?>
```

#### Output example

```json
{
    "active": [
        {
          "name": "Model",
          "api-name": "car-model",
          "description": "Car models from dealerships",
          "type": "string",
          "category": "general",
          "cardinality": "high",
          "storage": "latest-value"
        }
    ],
    "inactive": [
        {
          "name": "Year",
          "api-name": "car-year",
          "description": "Year of manufacture",
          "type": "integer",
          "category": "general",
          "storage": "latest-value"
        }
    ]
}
```

### `createField($jsonData)`
Create a new field. This method corresponds to a [POST request at /field](http://panel.slicingdice.com/docs/#api-details-api-endpoints-post-field).

#### Request example

```php
<?php
use Slicer\SlicingDice;
$slicingDice = new SlicingDice("MASTER_API_KEY");
$field = array(
    "name" => "Year",
    "api-name" => "year",
    "type" => "integer",
    "description" => "Year of manufacturing",
    "storage" => "latest-value"
);
print_r($slicingDice->createField($field));
?>
```

#### Output example

```json
{
    "status": "success",
    "api-name": "year"
}
```

### `index($jsonData)`
Index data to existing entities or create new entities, if necessary. This method corresponds to a [POST request at /index](http://panel.slicingdice.com/docs/#api-details-api-endpoints-post-index).

#### Request example

```php
<?php
use Slicer\SlicingDice;
$slicingDice = new SlicingDice("MASTER_OR_WRITE_API_KEY");
$indexData = array(
    "user1@slicingdice.com" => array(
        "car-model" => "Ford Ka",
        "year" => 2016
    ),
    "user2@slicingdice.com" => array(
        "car-model" => "Honda Fit",
        "year" => 2016
    ),
    "user3@slicingdice.com" => array(
        "car-model" => "Toyota Corolla",
        "year" => 2010,
        "test-drives" => array(
            array(
                "value" => "NY",
                "date" => "2016-08-17T13:23:47+00:00"
            ), array(
                "value" => "NY",
                "date" => "2016-08-17T13:23:47+00:00"
            ), array(
                "value" => "CA",
                "date" => "2016-04-05T10:20:30Z"
            )
        )
    ),
    "user4@slicingdice.com" => array(
        "car-model" => "Ford Ka",
        "year" => 2005,
        "test-drives" => array(
            "value" => "NY",
            "date" => "2016-08-17T13:23:47+00:00"
        )
    )
);
print_r($slicingDice->index($indexData));
?>
```

#### Output example

```json
{
    "status": "success",
    "indexed-entities": 4,
    "indexed-fields": 10,
    "took": 0.023
}
```

### `existsEntity($ids)`
Verify which entities exist in a project given a list of entity IDs. This method corresponds to a [POST request at /query/exists/entity](http://panel.slicingdice.com/docs/#api-details-api-endpoints-post-query-exists-entity).

#### Request example

```php
<?php
use Slicer\SlicingDice;
$slicingDice = new SlicingDice("MASTER_OR_READ_API_KEY");
ids = array(
        "user1@slicingdice.com",
        "user2@slicingdice.com",
        "user3@slicingdice.com"
);
print_r($slicingDice->existsEntity($ids));
?>
```

#### Output example

```json
{
    "status": "success",
    "exists": [
        "user1@slicingdice.com",
        "user2@slicingdice.com"
    ],
    "not-exists": [
        "user3@slicingdice.com"
    ],
    "took": 0.103
}
```

### `countEntityTotal()`
Count the number of indexed entities. This method corresponds to a [GET request at /query/count/entity/total](http://panel.slicingdice.com/docs/#api-details-api-endpoints-get-query-count-entity-total).

#### Request example

```php
<?php
use Slicer\SlicingDice;
$slicingDice = new SlicingDice("MASTER_OR_READ_API_KEY");
print_r($slicingDice->countEntityTotal());
?>
```

#### Output example

```json
{
    "status": "success",
    "result": {
        "total": 42
    },
    "took": 0.103
}
```

### `countEntity($jsonData)`
Count the number of entities attending the given query. This method corresponds to a [POST request at /query/count/entity](http://panel.slicingdice.com/docs/#api-details-api-endpoints-post-query-count-entity).

#### Request example

```php
<?php
use Slicer\SlicingDice;
$slicingDice = new SlicingDice("MASTER_OR_READ_API_KEY");
$query = array(
    "users-from-ny-or-ca" => array(
        array(
            "state" => array(
                "equals" => "NY"
            )
        ),
        "or",
        array(
            "state-origin" => array(
                "equals" => "CA"
            )
        ),
    ),
    "users-from-ny" => array(
        array(
            "state" => array(
                "equals" => "NY"
            )
        )
    ),
    "bypass-cache" => false
);
print_r($slicingDice->countEntity($query));
?>
```

#### Output example

```json
{
    "status": "success",
    "result": {
        "users-from-ny-or-ca": 175,
        "users-from-ny": 296
    },
    "took": 0.103
}
```

### `countEvent($jsonData)`
Count the number of occurrences for time-series events attending the given query. This method corresponds to a [POST request at /query/count/event](http://panel.slicingdice.com/docs/#api-details-api-endpoints-post-query-count-event).

#### Request example

```php
<?php
use Slicer\SlicingDice;
$slicingDice = new SlicingDice("MASTER_OR_READ_API_KEY");
$query = array(
    "users-from-ny-in-jan" => array(
        array(
        "test-field" => array(
                "equals" => "NY",
                "between" => array(
                    "2016-01-01T00:00:00Z",
                    "2016-01-31T00:00:00Z"
                ),
                "minfreq" => 2
            )
        )
    ),
    "users-from-ny-in-feb" => array(
        array(
            "test-field" => array(
                "equals" => "NY",
                "between" => array(
                    "2016-02-01T00:00:00Z",
                    "2016-02-28T00:00:00Z"
                ),
                "minfreq" => 2
            )
        )
    ),
    "bypass-cache" => true
);
print_r($slicingDice->countEvent($query));
?>
```

#### Output example

```json
{
    "status": "success",
    "result": {
        "users-from-ny-in-jan": 175,
        "users-from-ny-in-feb": 296
    },
    "took": 0.103
}
```

### `topValues($jsonData)`
Return the top values for entities attending the given query. This method corresponds to a [POST request at /query/top_values](http://panel.slicingdice.com/docs/#api-details-api-endpoints-post-query-top-values).

#### Request example

```php
<?php
use Slicer\SlicingDice;
$slicingDice = new SlicingDice("MASTER_OR_READ_API_KEY");
$query = array(
    "user-gender" => array(
        "gender" => 2
    ),
    "operating-systems" => array(
        "os" => 3
    ),
    "linux-operating-systems" => array(
        "os" => 3,
        "contains" => array(
            "linux",
            "unix"
        )
    )
);
print_r($slicingDice->topValues($query));
?>
```

#### Output example

```json
{
    "status": "success",
    "result": {
        "user-gender": {
            "gender": [
                {
                    "quantity": 6.0,
                    "value": "male"
                }, {
                    "quantity": 4.0,
                    "value": "female"
                }
            ]
        },
        "operating-systems": {
            "os": [
                {
                    "quantity": 55.0,
                    "value": "windows"
                }, {
                    "quantity": 25.0,
                    "value": "macos"
                }, {
                    "quantity": 12.0,
                    "value": "linux"
                }
            ]
        },
        "linux-operating-systems": {
            "os": [
                {
                    "quantity": 12.0,
                    "value": "linux"
                }, {
                    "quantity": 3.0,
                    "value": "debian-linux"
                }, {
                    "quantity": 2.0,
                    "value": "unix"
                }
            ]
        }
    },
    "took": 0.103
}
```

### `aggregation($jsonData)`
Return the aggregation of all fields in the given query. This method corresponds to a [POST request at /query/aggregation](http://panel.slicingdice.com/docs/#api-details-api-endpoints-post-query-aggregation).

#### Request example

```php
<?php
use Slicer\SlicingDice;
$slicingDice = new SlicingDice("MASTER_OR_READ_API_KEY");
$query = array(
    "query" => array(
        array(
            "gender" => 2
        ),
        array(
            "os" => 2,
            "equals" => array(
                "linux",
                "macos",
                "windows"
            )
        ),
        array(
            "browser" => 2
        )
    )
);
print_r($slicingDice->aggregation($query));
?>
```

#### Output example

```json
{
    "status": "success",
    "result": {
        "gender": [
            {
                "quantity": 6,
                "value": "male",
                "os": [
                    {
                        "quantity": 5,
                        "value": "windows",
                        "browser": [
                            {
                                "quantity": 3,
                                "value": "safari"
                            }, {
                                "quantity": 2,
                                "value": "internet explorer"
                            }
                        ]
                    }, {
                        "quantity": 1,
                        "value": "linux",
                        "browser": [
                            {
                                "quantity": 1,
                                "value": "chrome"
                            }
                        ]
                    }
                ]
            }, {
                "quantity": 4,
                "value": "female",
                "os": [
                    {
                        "quantity": 3,
                        "value": "macos",
                        "browser": [
                            {
                                "quantity": 3,
                                "value": "chrome"
                            }
                        ]
                    }, {
                        "quantity": 1,
                        "value": "linux",
                        "browser": [
                            {
                                "quantity": 1,
                                "value": "chrome"
                            }
                        ]
                    }
                ]
            }
        ]
    },
    "took": 0.103
}
```

### `getSavedQueries()`
Get all saved queries. This method corresponds to a [GET request at /query/saved](http://panel.slicingdice.com/docs/#api-details-api-endpoints-get-query-saved).

#### Request example

```php
<?php
use Slicer\SlicingDice;
$slicingDice = new SlicingDice("MASTER_API_KEY");
print_r($slicingDice->getSavedQueries());
?>
```

#### Output example

```json
{
    "status": "success",
    "saved-queries": [
        {
            "name": "users-in-ny-or-from-ca",
            "type": "count/entity",
            "query": [
                {
                    "state": {
                        "equals": "NY"
                    }
                },
                "or",
                {
                    "state-origin": {
                        "equals": "CA"
                    }
                }
            ],
            "cache-period": 100
        }, {
            "name": "users-from-ca",
            "type": "count/entity",
            "query": [
                {
                    "state": {
                        "equals": "NY"
                    }
                }
            ],
            "cache-period": 60
        }
    ],
    "took": 0.103
}
```

### `createSavedQuery($jsonData)`
Create a saved query at SlicingDice. This method corresponds to a [POST request at /query/saved](http://panel.slicingdice.com/docs/#api-details-api-endpoints-post-query-saved).

#### Request example

```php
<?php
use Slicer\SlicingDice;
$slicingDice = new SlicingDice("MASTER_API_KEY");
$query = array(
    "name" => "my-saved-query",
    "type" => "count/entity",
    "query" => array(
        array(
            "state" => array(
                "equals" => "NY"
            )
        ),
        "or",
        array(
            "state-origin" => array(
                "equals" => "CA"
            )
        )
    ),
    "cache-period" => 100
);
print_r($slicingDice->createSavedQuery($query));
?>
```

#### Output example

```json
{
    "status": "success",
    "name": "my-saved-query",
    "type": "count/entity",
    "query": [
        {
            "state": {
                "equals": "NY"
            }
        },
        "or",
        {
            "state-origin": {
                "equals": "CA"
            }
        }
    ],
    "cache-period": 100,
    "took": 0.103
}
```

### `updateSavedQuery($queryName, $jsonData)`
Update an existing saved query at SlicingDice. This method corresponds to a [PUT request at /query/saved/QUERY_NAME](http://panel.slicingdice.com/docs/#api-details-api-endpoints-put-query-saved-query-name).

#### Request example

```php
<?php
use Slicer\SlicingDice;
$slicingDice = new SlicingDice("MASTER_API_KEY");
new$query = array(
    "type" => "count/entity",
    "query" => array(
        array(
            "state" => array(
                "equals" => "NY"
            )
        ),
        "or",
        array(
            "state-origin" => array(
                "equals" => "CA"
            )
        )
    ),
    "cache-period" => 100
);
print_r($slicingDice->updateSavedQuery("my-saved-query", $newQuery));
?>
```

#### Output example

```json
{
    "status": "success",
    "name": "my-saved-query",
    "type": "count/entity",
    "query": [
        {
            "state": {
                "equals": "NY"
            }
        },
        "or",
        {
            "state-origin": {
                "equals": "CA"
            }
        }
    ],
    "cache-period": 100,
    "took": 0.103
}
```

### `getSavedQuery($queryName)`
Executed a saved query at SlicingDice. This method corresponds to a [GET request at /query/saved/QUERY_NAME](http://panel.slicingdice.com/docs/#api-details-api-endpoints-get-query-saved-query-name).

#### Request example

```php
<?php
use Slicer\SlicingDice;
$slicingDice = new SlicingDice("MASTER_API_KEY");
print_r($slicingDice->getSavedQuery("my-saved-query"));
?>
```

#### Output example

```json
{
    "status": "success",
    "type": "count/entity",
    "query": [
        {
            "state": {
                "equals": "NY"
            }
        },
        "or",
        {
            "state-origin": {
                "equals": "CA"
            }
        }
    ],
    "result": {
        "my-saved-query": 175
    },
    "took": 0.103
}
```

### `deleteSavedQuery($queryName)`
Delete a saved query at SlicingDice. This method corresponds to a [DELETE request at /query/saved/QUERY_NAME](http://panel.slicingdice.com/docs/#api-details-api-endpoints-delete-query-saved-query-name).

#### Request example

```php
<?php
use Slicer\SlicingDice;
$slicingDice = new SlicingDice("MASTER_API_KEY");
print_r($slicingDice->deleteSavedQuery("my-saved-query"));
?>
```

#### Output example

```json
{
    "status": "success",
    "deleted-query": "my-saved-query",
    "type": "count/entity",
    "query": [
        {
            "state": {
                "equals": "NY"
            }
        },
        "or",
        {
            "state-origin": {
                "equals": "CA"
            }
        }
    ],
    "took": 0.103
}
```

### `result($jsonData)`
Retrieve indexed values for entities attending the given query. This method corresponds to a [POST request at /data_extraction/result](http://panel.slicingdice.com/docs/#api-details-api-endpoints-post-data-extraction-result).

#### Request example

```php
<?php
use Slicer\SlicingDice;
$slicingDice = new SlicingDice("MASTER_OR_READ_API_KEY");
$query = array(
    "query" => array(
        array(
            "users-from-ny" => array(
                "equals" => "NY"
            )
        ),
        "or",
        array(
            "users-from-ca" => array(
                "equals" => "CA"
            )
        )
    ),
    "fields" => array("name", "year"),
    "limit" => 2
);
print_r($slicingDice->result($query));
?>
```

#### Output example

```json
{
    "status": "success",
    "data": {
        "user1@slicingdice.com": {
            "name": "John",
            "year": 2016
        },
        "user2@slicingdice.com": {
            "name": "Mary",
            "year": 2005
        }
    },
    "took": 0.103
}
```

### `score($jsonData)`
Retrieve indexed values as well as their relevance for entities attending the given query. This method corresponds to a [POST request at /data_extraction/score](http://panel.slicingdice.com/docs/#api-details-api-endpoints-post-data-extraction-score).

#### Request example

```php
<?php
use Slicer\SlicingDice;
$slicingDice = new SlicingDice("MASTER_OR_READ_API_KEY");
$query = array(
    "query" => array(
        array(
            "users-from-ny" => array(
                "equals" => "NY"
            )
        ),
        "or",
        array(
            "users-from-ca" => array(
                "equals" => "CA"
            )
        )
    ),
    "fields" => array("name", "year"),
    "limit" => 2
);
print_r($slicingDice->score($query));
?>
```

#### Output example

```json
{
    "status": "success",
    "data": {
        "user1@slicingdice.com": {
            "name": "John",
            "year": 2016,
            "score": 2
        },
        "user2@slicingdice.com": {
            "name": "Mary",
            "year": 2005,
            "score": 1
        }
    },
    "took": 0.103
}
```

## License

[MIT](https://opensource.org/licenses/MIT)
