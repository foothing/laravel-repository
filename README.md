# Laravel repository
This package includes a set of common PHP and Laravel tools:
- Repository pattern with Laravel 5 implementation
- Resources handler
- A standardized http input reader

## Composer installation
```
"require": [
	"foothing/laravel-repository": "0.5.*"
]
```

## Basic usage
This package ships with a repository interface definition and
an `Eloquent` implementation.
The `EloquentRepository` is ready to use.

```php
// Make a repository instance manually
$repository = new EloquentRepository(new Post());
```

You are now ready to go:
```php
// Find first occurrence with matching field 'email'
$repository->findOneBy('email', 'test@example.com');

// Find first occurrence where id > 1
$repository->findOneBy('id', '1', '>');

// Find where title starts with 'foo'
$repository->findAllBy('title', 'foo%', 'like');

// Find all occurrences where id > 1
$repository->findAllBy('id', '1', '>');

// Returns all posts
$repository->all();

// Returns all post with Laravel pagination format
$repository->paginate();

// Create, update and delete instances.
$model = new Post(Input::all());
$freshMoel = $repository->create($model);
$updatedModel = $repository->update($model);
$repository->delete($model);
```

If you want to extend the base implementation with
your custom methods all you need to do is extend the
base class and define a constructor with the `Eloquent`
model as the first argument.
```php
class PostsRepository extends EloquentRepository {
	function __construct(Post $post) {
		// Call superclass constructor.
		parent::construct($post);
	}

	function customFancyMethod() {

	}
}
```

Don't forget to call the superclass constructor in order to
enable all the features that are delivered out of the box.
You can also add additional dependencies while overriding the base constructor.

```php
class PostsRepository extends AbstractEloquentRepository {
	function __construct(Post $post, AnotherDependency $dependency) {
		parent::construct($post);
		// Do whatever you like with your $dependency
	}
}
```

and you are all set. Examples:



```php
// Use with a dependency injector

class PostsController {
	protected $posts;

	function __construct(PostsRepository $posts) {
		$this->posts = $posts;
	}

	function getIndex($id = null) {
		// Return one post by primary key
		return $this->posts->find($id);
	}
}
```

### Eager load relations
You can eager load relations using the `with` method:
```php
$posts = $repository->with(['author', 'comments'])->all();

Output:
[
	{
		"id": 1,
		"title": "Post title",
		"author": {
			"id": 1,
			"email": "test@example.com"
		},
		"comments": [
			{"id": 1, "text": "a comment"},
			{"id": 2, "text": "another comment"},
		],
	}
]
```
You can chain the `with` method to all available methods:
```php
$posts = $repository->with(['author', 'comments'])->findOneBy('name', 'foo');
$posts = $repository->with(['author', 'comments'])->all();
// And so on.
```

You'll often want to eager load
some relations by default. Let's assume you have an User model with
a many-to-many relation with its Role models, and you want to fetch both
the user info and the roles.

The `Foothing\Resources\Resource` interface has three methods
which define the default Model behaviour:
- which relations to eager load by default for a *findOne* operation
- which relations to eager load by default for a *findMany* operation
- which fields to skip upon save

```php
class User extends Model implements Foothing\Resources\Resource {
	/**
	 * Array of relations we want to eager-load when
	 * a single entity is being fetched.
	 *
	 * @return array
	 */
	function unitRelations() {
		return ['roles', 'posts'];
	}

	/**
	 * Array of relations we want to eager-load when
	 * a list of entities is being fetched.
	 *
	 * @return array
	 */
	function listRelations() {
		return ['roles'];
	}

	/**
	 * When the resource is sent in a json-encoded
	 * format it may happen to have relations fields
	 * populated. Since they would be set as stdClass
	 * objects we need to unset them before save.
	 *
	 * This method should return an array with all relations
	 * we want to be unset when processing the updates.
	 *
	 * @return array
	 */
	function skipOnSave() {
		return ['roles', 'posts'];
	}
}
```

In this way, each time you will use your UserRepository the following
would be the default behaviour:

```php
$users->find($id); 		// will eager load *roles* and *posts*
$users->findOneBy($id); // will eager load *roles* and *posts*
$users->create();		// will eager load *roles* and *posts*
$users->update();		// will eager load *roles* and *posts*
$users->all(); 			// will eager load *roles*
$users->paginate(); 	// will eager load *roles*
```

This default behaviour will be ignored if you use the `with` method explicitly.

The `skipOnSave` method comes handy when dealing with JSON object, since
when you read a resource, then process it on the client side, then
send it back to the server, it will contain the relation properties.
This properties will be converted to stdClass PHP object breaking the
Eloquent save method.

## Criteria
You can apply filters and order constraints using the `CriteriaInterface`,
which also ships with an `Eloquent` implementation.

Usage:
```php
$criteria = new \Foothing\Repository\Eloquent\EloquentCriteria();
$criteria->filter('name', 'Homer');
$criteria->filter('lastName', 'Simpson');

// Chain methods
$criteria->order('name')->sort('asc');
$repository->criteria($criteria)->paginate();
```

The repository provides shortcut methods you can use in your chains.
The following example produces the same effect as the previous one.
```php
$repository
	->filter('name', 'Homer')
	->filter('lastName', 'Simpson')
	->order('name')
	->sort('asc')
	->paginate();
```

Available filters:
```php
// Defaults to '='
$criteria->filter('name', 'Homer');

$criteria->filter('name', 'Home%', 'like');
$criteria->filter('id', 1, '>');
$criteria->filter('id', 1', '>=');
$criteria->filter('id', 1, '<');
$criteria->filter('id', 1, '<=');
$criteria->filter('id', 1, '!=');
$criteria->filter('lastName', 'Simpson,Nahasapeemapetilon', 'in');
```

You can use the **in** operator with a comma separated list of values
or with an array of values.

```php
$criteria->filter('lastName', 'Simpson,Nahasapeemapetilon', 'in');
$criteria->filter('lastName', ['Simpson', 'Nahasapeemapetilon'], 'in');
```

### Nested filters
It's also possible to query a Model relation. Assume your
Eloquent Model defines a `children` relation like so:

```php
class Foo extends Model {
	protected $table = 'foo';

	public function children() {
		return $this->hasMany('Bar');
	}
}
```

In this case you'll be allowed to query `children` values:
```php
$criteria->filter('children.name', 'Bart');
```

> Keep in mind that the methods subject to criteria restrictions are all those fetching more than one
> record.

## Repository API

```php
//
//
//	Crud.
//
//

public function find($id);
public function findOneBy($field, $arg1, $arg2 = null);
public function findAllBy($field, $arg1, $arg2 = null);
public function all();
public function paginate($limit = null, $offset = null);

public function create($entity);
public function update($entity);
public function delete($entity);

//
//
//	Eager loading.
//
//

public function with(array $relations);

//
//
//	Criteria shortcuts.
//
//

public function criteria(CriteriaInterface $criteria);
public function filter($field, $value, $operator = '=');
public function order($field, $sort = null);
public function sort($direction);

//
//
//	Helpers.
//
//

/**
 * Forces the next read query to skip cached values.
 * @return self
 */
public function refresh();

/**
 * Reset the refresh flag.
 * @return self
 */
public function reset();

public function validationRules();
public function validationRulesPartial($partial);
```

## RemoteQuery
More info coming soon.

## License
[MIT](https://opensource.org/licenses/MIT)