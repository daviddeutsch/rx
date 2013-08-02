```php
R::findLast(
     'package',
     ' name = :name AND major = :major AND minor = :minor ',
     array(
        ':name' => $name,
        ':major' => $version[0],
        ':minor' => $version[1]
    )
  );
```

Would become:

```php
R::$x->last->package
    ->name($name)
    ->major($version[0])
    ->minor($version[1])
    ->find();
```

If you already know pretty well what you're searching for, there's the like() function where instead of this:

```php
R::$x->thing->test("data")->test2("data2")->find();
```

You can do this:

```php
$array = [ "test" => "data", "test2" => "data2" ];

R::$x->thing->like($array)->find();
```

(granted, this only cuts things short if you already have the data in an array)

You can also pass true to the find() function to create exactly the thing you were looking for if it doesn't exist yet:

```php
$project = R::$x->project->name($name)->find(true);
```

What if you want to find something related?

```php
$branch = R::$x->one->branch->name($name)->related($project)->find();
```

You know what? If you don't find it, create it. *And automatically associate it, too*.

```php
$branch = R::$x->one->branch->name($name)->related($project)->find(true);
```
