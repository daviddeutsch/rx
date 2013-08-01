rx
==

A shorthand library for RedBeanPHP


#### CAUTION: Large parts of this are untested and I'm making stuff up as I go along

### An Extended Facade

```php
$type = R::_( 'type' ); // Dispense beans

$type = R::_( 'type', $id ); // Load beans

R::_( $bean ); // Store beans
```

[Learn more about the Facade](https://github.com/daviddeutsch/rx/blob/master/docs/00_Facade.md)

### A short, fluid helper for finding beans

```php
/**
 * Search for a project
 */
$project = R::$x->project->name($name)->find();

/**
 * Actually, if you can't find one, make one with that data
 */
$project = R::$x->project->name($name)->find(true);
```

[Learn more about the Find Helper](https://github.com/daviddeutsch/rx/blob/master/docs/01_Find_Helper.md)
