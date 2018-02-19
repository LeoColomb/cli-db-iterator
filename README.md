# CLI Database Iterator

> Interface for iterating through a SQL selection

## Usage

```shell
$ composer require leocolomb/cli-db-iterator
```

## Example

```php
require_once __DIR__ . '/vendor/autoload.php';

use CLIDatabaseIterator\Iterator;

$bridge = new Iterator("SELECT `ID`, `content` FROM `data`;");

while ($row = $bridge->fetch()) {
    $bridge->query("UPDATE `data` SET `content`='new content' WHERE `ID`={$row['ID']};", true);
    $bridge->alert("Updated!", $row['ID']);
}

$bridge->finish();
```

## License

ISC © Léo Colombaro
