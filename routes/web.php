<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/migrate', function () {

    Artisan::call('migrate');
//    Artisan::call('migrate', ['--path' => 'vendor/laravel/passport/database/migrations']);

    dd('complete');

});

Route::get('/passportinstall', function () {

    Artisan::call('passport:install');

    dd('complete');

});


Route::get('test',function(){
    $attributes['value'] = ['طلا','نقره'];
    
    $product = App\Product::find(65);
    
    dd($product->real_options,$attributes['value']);

    foreach($attributes['value'] as $attr){
                    //echo $attr;
               
                    foreach($product->real_options as $option){
                        if($option === $attr){
                            return true;
                        }{
                            return false;
                        }
    }
    
    }
                 
   /*$products = \App\Product::query()->get()->filter(function($item) {
       if($item->real_options){
      
        return in_array(['طلا'],$item->real_options);
             
       }else{
           return false;
       }
    });*/
   
   
   /*foreach($products as $product){
       $arr[] = $product->real_options;
   }
   dd($products);
   */
});


Route::get('/testtt',function(){
phpinfo();
});
