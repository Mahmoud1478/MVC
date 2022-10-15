# MVC like laravel
### Not finished yet

## Features
* Routing with prams (only Controller and class name) and naming routes
* Dependency Injection (InterFace Binding - Auto wiring)
* Controllers constructor and methods dependency injection

## Routing
### creating routes with names
```php
Router::group(['prefix'=> 'users' ,'as'=> 'users' ],function (){
    Router::get('/',[UserController::class,'index'])->name('index');
    Router::post('/',[UserController::class,'store'])->name('store');
    Router::get('/create',[UserController::class,'create'])->name('create')->middleware('working');
    Router::get('/{id}',[UserController::class,'show'])->name('show');
    Router::put('/{id}',[UserController::class,'update'])->name('update');
    Router::delete('/{id}',[UserController::class,'destroy'])->name('destroy');
    Router::get('/{id}/edit',[UserController::class,'edit'])->name('edit');
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
    public function show($id,Request $request, Server $server)
    {
        return 'show function with pram: ' . $id ." with uri {$request->uri()} " ;
    }
}
```
