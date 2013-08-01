The Rx_Facade extends the RedBean_Facade, so you can load it like so:

```php
use Rx_Facade as R;
```

Currently, the two main concepts I'm adding is a R::_() function that serves as a shorthand for the most common functions (loading, dispensing etc.) and the R::$x FindHelper which extends on R::$f to cut down creation of finder queries.

A couple of examples for the _() function:

Storing a bean:

```php
R::_( $bean );
```

Dispensing a bean:

```php
$type = R::_( 'type' );
```

Dispense a bean and inject data:

```php
$object = new \stdClass();
$object->name = 'name';
$object->data = 'data';

$type = R::_( 'type', $object );
```

(can be used to easily convert existing data into a bean)

Load a bean:

```php
$type = R::_( 'type', $id );
```
