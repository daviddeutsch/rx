rx
==

A shorthand library for RedBeanPHP

Rx_Facade extends the RedBean_Facade, so you can load it like so:

use Rx_Facade as R;

Currently, the two main concepts I'm adding is a R::_() function that serves as a shorthand for the most common functions (loading, dispensing etc.) and the R::$x FindHelper which extends on R::$f to cut down creation of finder queries.

A couple of examples for the _() function:

Storing a bean:

R::_( $bean );

Dispensing a bean:

$type = R::_( 'type' );

Dispense a bean and inject data:

$object = new \stdClass();
$object->name = 'name';
$object->data = 'data';

$type = R::_( 'type', $object );

(can be used to easily convert existing data into a bean)

Load a bean:
$type = R::_( 'type', $id );

---

Example for the $x() FindHelper:

R::findLast(
     'package',
     ' name = :name AND major = :major AND minor = :minor ',
     array(
        ':name' => $name,
        ':major' => $version[0],
        ':minor' => $version[1]
    )
  );

Would become:

R::$x->last->package
    ->name($name)
    ->major($version[0])
    ->minor($version[1])
    ->find();

If you already know pretty well what you're searching for, there's the like() function where instead of this:

R::$x->thing->test("data")->test2("data2")->find();

You can do this:

$array = [ "test" => "data", "test2" => "data2" ];

R::$x->thing->like($array)->find();

(granted, this only cuts things short if you already have the data in an array)
