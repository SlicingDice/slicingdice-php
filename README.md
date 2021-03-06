# SlicingDice Official PHP Client (v2.1.0)

Official PHP client for [SlicingDice - Data Warehouse and Analytics Database as a Service](https://www.slicingdice.com/).

[SlicingDice](http://www.slicingdice.com/) is a serverless, SQL & API-based, easy-to-use and really cost-effective alternative to Amazon Redshift and Google BigQuery.

### Build Status: [![CircleCI](https://circleci.com/gh/SlicingDice/slicingdice-php/tree/master.svg?style=svg)](https://circleci.com/gh/SlicingDice/slicingdice-php/tree/master)

### Code Quality: [![Codacy Badge](https://api.codacy.com/project/badge/Grade/0d58ca92a6c54a41ad30e643bb846484)](https://www.codacy.com/app/SimbioseVentures/slicingdice-php?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=SlicingDice/slicingdice-php&amp;utm_campaign=Badge_Grade)

## Documentation

If you are new to SlicingDice, check our [quickstart guide](https://docs.slicingdice.com/docs/quickstart-guide) and learn to use it in 15 minutes.

Please refer to the [SlicingDice official documentation](https://docs.slicingdice.com/) for more information on [how to create a database](https://docs.slicingdice.com/docs/how-to-create-a-database), [how to insert data](https://docs.slicingdice.com/docs/how-to-insert-data), [how to make queries](https://docs.slicingdice.com/docs/how-to-make-queries), [how to create columns](https://docs.slicingdice.com/docs/how-to-create-columns), [SlicingDice restrictions](https://docs.slicingdice.com/docs/current-restrictions) and [API details](https://docs.slicingdice.com/docs/api-details).

## Tests and Examples

Whether you want to test the client installation or simply check more examples on how the client works, take a look at [tests and examples directory](tests_and_examples/).

## Installing

In order to install the PHP client, add the following excerpt to your [`composer.json`](https://getcomposer.org/) file.

```json
{
  "require": {
    "slicingdice/slicingdice": "*"
  }
}
```

Install the dependencies by executing the command below:

```bash
composer install
```

### Troubleshooting
If you have problem to install on Linux, try to install these system dependencies:

```bash
# PHP 7
sudo apt-get install php-curl php7.0-dom php7.0-mbstring php7.0-xml 
# PHP >= 5.2.8
sudo apt-get install php-curl php5.6-dom php-mbstring php5.6-xml
```

## Usage

The following code snippet is an example of how to add and query data
using the SlicingDice PHP client. We entry data informing
`user1@slicingdice.com` has age 22 and then query the database for
the number of users with age between 20 and 40 years old.
If this is the first register ever entered into the system,
 the answer should be 1.

```php
<?php
use Slicer\SlicingDice;

// Configure the client

$client = new SlicingDice(array("masterKey" => "MASTER_API_KEY"));

// Inserting data
$insertData = array(
    "user1@slicingdice.com" => array(
        "age" => 22
    ),
    "auto-create" => array("dimension", "column")
);
$client->insert($insertData);

// Querying data
$queryData = array(
    "query-name" => "users-between-20-and-40",
    "query" => array(
        array(
            "age" => array(
                "range" => array(20, 40)
            )
        )
    )
);
print_r($client->countEntity($queryData));
?>
```

## Reference

`SlicingDice` encapsulates logic for sending requests to the API. Its methods are thin layers around the [API endpoints](https://docs.slicingdice.com/docs/api-details), so their parameters and return values are JSON-like `Object` objects with the same syntax as the [API endpoints](https://docs.slicingdice.com/docs/api-details)

### Attributes

* `$key (array)` - [API key](https://docs.slicingdice.com/docs/api-keys) to authenticate requests with the SlicingDice API.
* `$timeout (int)` - Amount of time, in seconds, to wait for results for each request.

### Constructor

`_construct($key, $timeout=60)`
* `$key (array)` - [API key](https://docs.slicingdice.com/docs/api-keys) to authenticate requests with the SlicingDice API.
* `$timeout (int)` - Amount of time, in seconds, to wait for results for each request.

### `getDatabase()`
Get information about current database. This method corresponds to a `GET` request at `/database`.

#### Request example

```php
<?php
use Slicer\SlicingDice;

$client = new SlicingDice(array("masterKey" => "MASTER_API_KEY"));
print_r($client->getDatabase());
?>
```

#### Output example

```json
{
    "name": "Database 1",
    "description": "My first database",
    "tables": [
    	"default",
        "users"
    ],
    "updated-at": "2017-05-19T14:27:47.417415",
    "created-at": "2017-05-12T02:23:34.231418"
}
```

### `getColumns()`
Get all created columns, both active and inactive ones. This method corresponds to a [GET request at /column](https://docs.slicingdice.com/docs/how-to-list-edit-or-delete-columns).

#### Request example

```php
<?php
use Slicer\SlicingDice;

$client = new SlicingDice(array("masterKey" => "MASTER_API_KEY"));
print_r($client->getColumns());
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

### `createColumn($jsonData)`
Create a new column. This method corresponds to a [POST request at /column](https://docs.slicingdice.com/docs/how-to-create-columns#section-creating-columns-using-column-endpoint).

#### Request example

```php
<?php
use Slicer\SlicingDice;

$client = new SlicingDice(array("masterKey" => "MASTER_API_KEY"));
$column = array(
    "name" => "Year",
    "api-name" => "year",
    "type" => "integer",
    "description" => "Year of manufacturing",
    "storage" => "latest-value"
);
print_r($client->createColumn($column));
?>
```

#### Output example

```json
{
    "status": "success",
    "api-name": "year"
}
```

### `insert($jsonData)`
Insert data to existing entities or create new entities, if necessary. This method corresponds to a [POST request at /insert](https://docs.slicingdice.com/docs/how-to-insert-data).

#### Request example

```php
<?php
use Slicer\SlicingDice;

$client = new SlicingDice(array("masterKey" => "MASTER_API_KEY"));
$insertData = array(
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
    ),
    "auto-create" => array("dimension", "column")
);
print_r($client->insert($insertData));
?>
```

#### Output example

```json
{
    "status": "success",
    "inserted-entities": 4,
    "inserted-columns": 10,
    "took": 0.023
}
```

### `existsEntity($ids, $dimension)`
Verify which entities exist in a dimension (uses `default` dimension if not provided) given a list of entity IDs. This method corresponds to a [POST request at /query/exists/entity](https://docs.slicingdice.com/docs/exists).

#### Request example

```php
<?php
use Slicer\SlicingDice;

$client = new SlicingDice(array("masterKey" => "MASTER_API_KEY"));
$ids = array(
        "user1@slicingdice.com",
        "user2@slicingdice.com",
        "user3@slicingdice.com"
);
print_r($client->existsEntity($ids));
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
Count the number of inserted entities in the whole database. This method corresponds to a [POST request at /query/count/entity/total](https://docs.slicingdice.com/docs/total).

#### Request example

```php
<?php
use Slicer\SlicingDice;

$client = new SlicingDice(array("masterKey" => "MASTER_API_KEY"));

print_r($client->countEntityTotal());
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

### `countEntityTotal($dimensions)`
Count the total number of inserted entities in the given dimensions. This method corresponds to a [POST request at /query/count/entity/total](https://docs.slicingdice.com/docs/total#section-counting-specific-tables).

#### Request example

```php
<?php
use Slicer\SlicingDice;

$client = new SlicingDice(array("masterKey" => "MASTER_API_KEY"));

$dimensions = array("default");

print_r($client->countEntityTotal($dimensions));
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
Count the number of entities matching the given query. This method corresponds to a [POST request at /query/count/entity](https://docs.slicingdice.com/docs/count-entities).

#### Request example

```php
<?php
use Slicer\SlicingDice;

$client = new SlicingDice(array("masterKey" => "MASTER_API_KEY"));
$query = array(
    array(
        "query-name" => "corolla-or-fit",
        "query" => array(
            array(
                "car-model" => array(
                    "equals" => "toyota corolla"
                )
            ),
            "or",
            array(
                "car-model" => array(
                    "equals" => "honda fit"
                )
            )
        ),
        "bypass-cache" => false
    ),
    array(
        "query-name" => "ford-ka",
        "query" => array(
            array(
                "car-model" => array(
                    "equals" => "ford ka"
                )
            )
        ),
        "bypass-cache" => false
    )
);
print_r($client->countEntity($query));
?>
```

#### Output example

```json
{
   "result":{
      "ford-ka":2,
      "corolla-or-fit":2
   },
   "took":0.083,
   "status":"success"
}
```

### `countEvent($jsonData)`
Count the number of occurrences for time-series events matching the given query. This method corresponds to a [POST request at /query/count/event](https://docs.slicingdice.com/docs/count-events).

#### Request example

```php
<?php
use Slicer\SlicingDice;

$client = new SlicingDice(array("masterKey" => "MASTER_API_KEY"));
$query = array(
    array(
        "query-name" => "test-drives-in-ny",
        "query" => array(
            array(
            "test-drives" => array(
                    "equals" => "NY",
                    "between" => array(
                        "2016-08-16T00:00:00Z",
                        "2016-08-18T00:00:00Z"
                    )
                )
            )
        ),
        "bypass-cache" => true
    ),
    array(
        "query-name" => "test-drives-in-ca",
        "query" => array(
            array(
                "test-drives" => array(
                    "equals" => "CA",
                    "between" => array(
                        "2016-04-04T00:00:00Z",
                        "2016-04-06T00:00:00Z"
                    )
                )
            )
        ),
        "bypass-cache" => true
    )
);
print_r($client->countEvent($query));
?>
```

#### Output example

```json
{
   "result":{
      "test-drives-in-ny":3,
      "test-drives-in-ca":0
   },
   "took":0.063,
   "status":"success"
}
```

### `topValues($jsonData)`
Return the top values for entities matching the given query. This method corresponds to a [POST request at /query/top_values](https://docs.slicingdice.com/docs/top-values).

#### Request example

```php
<?php
use Slicer\SlicingDice;

$client = new SlicingDice(array("masterKey" => "MASTER_API_KEY"));
$query = array(
    "car-year" => array(
        "year" => 2
    ),
    "car models" => array(
        "car-model" => 3
    )
);
print_r($client->topValues($query));
?>
```

#### Output example

```json
{
   "result":{
      "car models":{
         "car-model":[
            {
               "quantity":2,
               "value":"ford ka"
            },
            {
               "quantity":1,
               "value":"honda fit"
            },
            {
               "quantity":1,
               "value":"toyota corolla"
            }
         ]
      },
      "car-year":{
         "year":[
            {
               "quantity":2,
               "value":"2016"
            },
            {
               "quantity":1,
               "value":"2010"
            }
         ]
      }
   },
   "took":0.034,
   "status":"success"
}
```

### `aggregation($jsonData)`
Return the aggregation of all columns in the given query. This method corresponds to a [POST request at /query/aggregation](https://docs.slicingdice.com/docs/aggregations).

#### Request example

```php
<?php
use Slicer\SlicingDice;

$client = new SlicingDice(array("masterKey" => "MASTER_API_KEY"));
$query = array(
    "query" => array(
        array(
            "year" => 2
        ),
        array(
            "car-model" => 2,
            "equals" => array(
                "honda fit",
                "toyota corolla"
            )
        )
    )
);
print_r($client->aggregation($query));
?>
```

#### Output example

```json
{
   "result":{
      "year":[
         {
            "quantity":2,
            "value":"2016",
            "car-model":[
               {
                  "quantity":1,
                  "value":"honda fit"
               }
            ]
         },
         {
            "quantity":1,
            "value":"2005"
         }
      ]
   },
   "took":0.079,
   "status":"success"
}
```

### `getSavedQueries()`
Get all saved queries. This method corresponds to a [GET request at /query/saved](https://docs.slicingdice.com/docs/saved-queries).

#### Request example

```php
<?php
use Slicer\SlicingDice;

$client = new SlicingDice(array("masterKey" => "MASTER_API_KEY"));
print_r($client->getSavedQueries());
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
Create a saved query at SlicingDice. This method corresponds to a [POST request at /query/saved](https://docs.slicingdice.com/docs/saved-queries).

#### Request example

```php
<?php
use Slicer\SlicingDice;

$client = new SlicingDice(array("masterKey" => "MASTER_API_KEY"));
$query = array(
    "name" => "my-saved-query",
    "type" => "count/entity",
    "query" => array(
        array(
            "car-model" => array(
                "equals" => "honda fit"
            )
        ),
        "or",
        array(
            "car-model" => array(
                "equals" => "toyota corolla"
            )
        )
    ),
    "cache-period" => 100
);
print_r($client->createSavedQuery($query));
?>
```

#### Output example

```json
{
   "took":0.053,
   "query":[
      {
         "car-model":{
            "equals":"honda fit"
         }
      },
      "or",
      {
         "car-model":{
            "equals":"toyota corolla"
         }
      }
   ],
   "name":"my-saved-query",
   "type":"count/entity",
   "cache-period":100,
   "status":"success"
}
```

### `updateSavedQuery($queryName, $jsonData)`
Update an existing saved query at SlicingDice. This method corresponds to a [PUT request at /query/saved/QUERY_NAME](https://docs.slicingdice.com/docs/saved-queries).

#### Request example

```php
<?php
use Slicer\SlicingDice;

$client = new SlicingDice(array("masterKey" => "MASTER_API_KEY"));
$newQuery = array(
    "type" => "count/entity",
    "query" => array(
        array(
            "car-model" => array(
                "equals" => "ford ka"
            )
        ),
        "or",
        array(
            "car-model" => array(
                "equals" => "toyota corolla"
            )
        )
    ),
    "cache-period" => 100
);
print_r($client->updateSavedQuery("my-saved-query", $newQuery));
?>
```

#### Output example

```json
{
   "took":0.037,
   "query":[
      {
         "car-model":{
            "equals":"ford ka"
         }
      },
      "or",
      {
         "car-model":{
            "equals":"toyota corolla"
         }
      }
   ],
   "type":"count/entity",
   "cache-period":100,
   "status":"success"
}
```

### `getSavedQuery($queryName)`
Executed a saved query at SlicingDice. This method corresponds to a [GET request at /query/saved/QUERY_NAME](https://docs.slicingdice.com/docs/saved-queries).

#### Request example

```php
<?php
use Slicer\SlicingDice;

$client = new SlicingDice(array("masterKey" => "MASTER_API_KEY"));
print_r($client->getSavedQuery("my-saved-query"));
?>
```

#### Output example

```json
{
   "result":{
      "query":2
   },
   "took":0.035,
   "query":[
      {
         "car-model":{
            "equals":"honda fit"
         }
      },
      "or",
      {
         "car-model":{
            "equals":"toyota corolla"
         }
      }
   ],
   "type":"count/entity",
   "status":"success"
}
```

### `deleteSavedQuery($queryName)`
Delete a saved query at SlicingDice. This method corresponds to a [DELETE request at /query/saved/QUERY_NAME](https://docs.slicingdice.com/docs/saved-queries).

#### Request example

```php
<?php
use Slicer\SlicingDice;

$client = new SlicingDice(array("masterKey" => "MASTER_API_KEY"));
print_r($client->deleteSavedQuery("my-saved-query"));
?>
```

#### Output example

```json
{
   "took":0.029,
   "query":[
      {
         "car-model":{
            "equals":"honda fit"
         }
      },
      "or",
      {
         "car-model":{
            "equals":"toyota corolla"
         }
      }
   ],
   "type":"count/entity",
   "cache-period":100,
   "status":"success",
   "deleted-query":"my-saved-query"
}
```

### `result($jsonData)`
Retrieve inserted values for entities matching the given query. This method corresponds to a [POST request at /data_extraction/result](https://docs.slicingdice.com/docs/result-extraction).

#### Request example

```php
<?php
use Slicer\SlicingDice;

$client = new SlicingDice(array("masterKey" => "MASTER_API_KEY"));
$query = array(
    "query" => array(
        array(
            "car-model" => array(
                "equals" => "ford ka"
            )
        ),
        "or",
        array(
            "car-model" => array(
                "equals" => "toyota corolla"
            )
        )
    ),
    "columns" => array("car-model", "year"),
    "limit" => 2
);
print_r($client->result($query));
?>
```

#### Output example

```json
{
   "took":0.113,
   "next-page":null,
   "data":{
      "customer5@mycustomer.com":{
         "year":"2005",
         "car-model":"ford ka"
      },
      "user1@slicingdice.com":{
         "year":"2016",
         "car-model":"ford ka"
      }
   },
   "page":1,
   "status":"success"
}
```

### `score($jsonData)`
Retrieve inserted values as well as their relevance for entities matching the given query. This method corresponds to a [POST request at /data_extraction/score](https://docs.slicingdice.com/docs/score-extraction).

#### Request example

```php
<?php
use Slicer\SlicingDice;

$client = new SlicingDice(array("masterKey" => "MASTER_API_KEY"));
$query = array(
    "query" => array(
        array(
            "car-model" => array(
                "equals" => "ford ka"
            )
        ),
        "or",
        array(
            "car-model" => array(
                "equals" => "toyota corolla"
            )
        )
    ),
    "columns" => array("car-model", "year"),
    "limit" => 2
);
print_r($client->score($query));
?>
```

#### Output example

```json
{
   "took":0.063,
   "next-page":null,
   "data":{
      "user3@slicingdice.com":{
         "score":1,
         "year":"2010",
         "car-model":"toyota corolla"
      },
      "user2@slicingdice.com":{
         "score":1,
         "year":"2016",
         "car-model":"honda fit"
      }
   },
   "page":1,
   "status":"success"
}
```

### `sql($query)`
Retrieve inserted values using a SQL syntax. This method corresponds to a POST request at /query/sql.

#### Query statement

```php
<?php
use Slicer\SlicingDice;

$client = new SlicingDice(array("masterKey" => "MASTER_API_KEY"));
$query = "SELECT COUNT(*) FROM default WHERE age BETWEEN 0 AND 49";
print_r($client->sql($query));
?>
```

#### Insert statement
```php
<?php
use Slicer\SlicingDice;

$client = new SlicingDice(array("masterKey" => "MASTER_API_KEY"));
$query = "INSERT INTO default([entity-id], name, age) VALUES(1, 'john', 10)";
print_r($client->sql($query));
?>
```

#### Output example

```json
{
   "took":0.063,
   "result":[
       {"COUNT": 3}
   ],
   "count":1,
   "status":"success"
}
```

## License

[MIT](https://opensource.org/licenses/MIT)
