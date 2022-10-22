# MVC like laravel
### Not finished yet

## Features
* Routing with prams and naming 
* Dependency Injection (InterFace Binding - Auto wiring)
* Controllers constructor and methods dependency injection

## Routing
### creating routes with names
```php
$router->get('/' ,function (\Src\Http\Request $request){
    return $request->uri() .  ' home page';
});

// name space works when callback is string ;
$router->group(['prefix'=> 'users' ,'as'=> 'users' ,'namespace'=> 'App\Controllers'],function (Router $router){
    $router->get('/',[UserController::class,'index'])->name('index');
    $router->post('/',[UserController::class,'store'])->name('store');
    $router->get('/create',[UserController::class,'create'])->name('create')
        ->middleware('working');
    // custom pram name is only string in slug format 
    $router->get('/{name:[A-Za-z_-]}','UserController@show')->name('show');
    $router->put('/{id}',[UserController::class,'update'])->name('update');
    $router->delete('/{id}',[UserController::class,'destroy'])->name('destroy');
    $router->get('/{id}/edit',[UserController::class,'edit'])->name('edit');
});
```
### get route by name 
```php
class UserController
{
    public function create()
    {
        return Router::getByNameWithBinding('users.index');
    }
}
```
### get route by name with prams
```php
class UserController
{
    public function create()
    {
        return Router::getByNameWithBinding('users.show',['id'=>1]);
    }
}

```
## Di
### 1- Constructor Di
#### UserController
```php
class UserController
{
    public function __construct(private readonly UserService $service){}
    
    
     public function index()
    {
        return $this->service->index();
    }
}
```
#### UserService
```php
class UserService
{
    public function __construct(private readonly Request $request)
    {}
    public function index(): string
    {
        return $this->request::method();
    }
}
```
### 2- Controllers Methods Di
```php
class UserController
{
    public function show($name,Request $request)
    {
        return 'show function with pram: ' . $name ." with uri {$request->uri()} " ;
    }
}
```
