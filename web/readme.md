

## About Laravel extending

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. 
Laravel extending attempts to make the teamwork more simple and fast.

How to write a database model based Restful APIs
1. First, create a table in database
```
php artisan table:create

By now, it's not support adding fields in command line.
So, we ONLY see primary key, status, create_time, update_time. 
We can add other fields manually
```

2. Generate model
```
php artisan model:create
```

- Model generated two layer structure
- IDE Friendly


3. Generate Restful APIs for CURD

```
php artisan restful:add

GET  http://127.0.0.1:9090/api/kxusers
POST http://127.0.0.1:9090/api/kxuser
POST http://127.0.0.1:9090/api/kxuser/{user_id}
GET  http://127.0.0.1:9090/api/kxuser/{user_id}
```
- Pager is supported by default


4. Try add a converter function in a model
```php
public function getCreateTimeAttribute($value)
{
    return date('Y-m-d H:i:s', $value);
}
```

5. Result is simple and rude
- In a service
```php
return Result::error(Errors::NotFound, []);
// ------ or ------
return Result::ok($object);
```
- In a controller
```php
if ($result->hasError()) {
    return $this->jsonFromError($result);
}
$data = $result->data();
// Handle $data from service
return $this->json(Errors::Ok, $data);
```

6. php artisan doc:generate %
```php
/**
 * @cat [Category name will be the document file's name]
 * @title 
 * @comment
 * @url-param
 * @form-param
 * @ret-val
 * @case
 * @pager TODO
 */
```
- And the docs would be auto-published to WIKI pages

7. Auto generated UT cases
```
phpunit tests/AutoSmoking/AutoSmokingTest.php
```

## Admin builder

1. Add a blank page
```
php artisan admin:add:blank some-page5
```

2. Create CURD admin pages from a Model
```
php artisan admin:add:curd
```

3. Create a List view from a HTTP API in json
- 30% Completed
- Create or Edit a List view config (Need post the config to beckend)
- Show the List without pager, filter.
- Create a Committer for a HTTP POST API! (TODO)


