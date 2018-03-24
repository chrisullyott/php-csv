# php-csv

CSV-to-array conversion in PHP.

### Installation

With [Composer](https://getcomposer.org/):

```
$ composer require chrisullyott/php-csv
```

### Usage

| ID | First Name | Last Name |
| --- | ---------- | --------- |
| 15 | Ethan | Hunt |
| 16 | Jim | Phelps |
| 17 | Luther | Stickell |


```
$parser = new CsvParser('/path/to/data.csv');

$items = $parser->getItems();

print_r($items);
```

```
Array
(
    [0] => Array
        (
            [id] => 15
            [first_name] => Ethan
            [last_name] => Hunt
        )

    [1] => Array
        (
            [id] => 16
            [first_name] => Jim
            [last_name] => Phelps
        )

    [2] => Array
        (
            [id] => 17
            [first_name] => Luther
            [last_name] => Stickell
        )

)
```
