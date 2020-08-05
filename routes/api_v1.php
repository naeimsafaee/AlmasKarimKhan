<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('add-admin', 'PassportController@add_admin');
Route::post('admin-login', 'PassportController@admin_login');

Route::post('unit-login', 'PassportController@unit_login');

Route::post('user_register', 'PassportController@user_register');
Route::post('user_login', 'PassportController@user_login');

Route::middleware(['auth:unit'])->prefix('units_section')->group(function () {

    Route::apiResource('category', 'api\units\CategoryController');
    Route::apiResource('group_category', 'api\units\GroupCategoryController');
    Route::apiResource('product', 'api\units\ProductController');
    Route::get('comments', 'api\units\UnitController@get_comment');

    Route::post('add_attribute_to_product2', 'api\units\ProductController@add_attribute');
    Route::post('remove_attribute_from_product2', 'api\units\ProductController@delete_attribute');

    Route::get('attribute_with_product_id', 'api\units\ProductController@attribute_by_product_id');

    Route::apiResource('article', 'api\units\UnitArticleController');
    Route::apiResource('unit', 'api\units\UnitController');
    Route::apiResource('order', 'api\units\OrderController');
    Route::apiResource('gallery', 'api\units\GalleryController');
    Route::get('comment','api\CommentController@index3');
    Route::post('reply','api\CommentController@reply');

    Route::apiResource('attribute', 'api\AttributeController');
    Route::get('attribute_with_category', 'api\AttributeController@get_attr_with_category_id');
    Route::apiResource('attribute_option', 'api\AttributeOptionController');
    Route::apiResource('attribute_price', 'api\AttributePriceController');
    Route::apiResource('group_attribute', 'api\GroupAttributeController');

    Route::post('set_discount_to_unit', 'api\units\ProductController@set_discount_to_unit');

    Route::get('order_status', 'api\OrderController@order_status');

    Route::get('category_by_group/{id}', 'api\CategoryController@category_by_group');
    Route::post('add_image_to_product/{id}', 'api\units\ProductController@add_image_to_product');

    Route::delete('delete_image_of_product/{id}', 'api\units\ProductController@delete_image_of_product');

    Route::get('get_product_statuses', 'api\units\ProductController@get_product_statuses');
    Route::post('add_attribute_to_product', 'api\units\ProductController@add_attribute_to_product');
    Route::get('get_attribute_of_product/{id}', 'api\units\ProductController@get_attribute_of_product');
    Route::delete('delete_attribute_from_product/{id}', 'api\units\ProductController@delete_attribute_from_product');

});


Route::middleware(['auth:admin'])->group(function () {

    Route::get('/', function () {
        return 'aa';
    });

    Route::get('attribute_with_product_id2', 'api\units\ProductController@attribute_by_product_id');


    Route::get('attribute_with_group', 'api\AttributeController@get_attr_with_group_attr_id');

    Route::apiResource('category', 'api\CategoryController');
    Route::get('category_by_group/{id}', 'api\CategoryController@category_by_group');

    Route::apiResource('group_category', 'api\GroupCategoryController');

    Route::apiResource('unit_category', 'api\UnitCategoryController');
    Route::apiResource('unit_status', 'api\UnitStatusController');

    Route::apiResource('article', 'api\ArticleController');

    Route::apiResource('attribute', 'api\AttributeController');
    Route::apiResource('attribute_option', 'api\AttributeOptionController');
    Route::apiResource('attribute_price', 'api\AttributePriceController');
    Route::apiResource('group_attribute', 'api\GroupAttributeController');
    Route::get('comment', 'api\CommentController@index');
    Route::get('active_comment', 'api\CommentController@index2');
    Route::put('comment/{id}', 'api\CommentController@update');
    Route::delete('comment/{id}', 'api\CommentController@destroy');

    Route::get('attribute_option_with_attribute_id/{id}', 'api\AttributeOptionController@show_with_attribute_id');

    Route::get('attribute_price_with_attribute_id/{id}', 'api\AttributePriceController@show_with_attribute_id');

    Route::post('set_home_unit', 'api\HomeController@set_home_unit');
    Route::post('delete_home_unit', 'api\HomeController@delete_home_unit');
    Route::get('get_most_sell_product', 'api\HomeController@get_most_sell_product');

    Route::apiResource('pluck', 'api\PluckController');
    Route::apiResource('unit', 'api\UnitController');
    Route::apiResource('unit_gallery', 'api\UnitGalleryController');
    Route::apiResource('unit_article', 'api\UnitArticleController');
    Route::apiResource('user', 'api\UserController');;
    Route::apiResource('address', 'api\AddressController');
    Route::apiResource('product', 'api\ProductController');
    Route::get('get_product_with_unit_id/{id}', 'api\ProductController@get_product_with_unit_id');

    Route::delete('delete_image_of_product/{id}', 'api\ProductController@delete_image_of_product');


    Route::apiResource('order1', 'api\OrderController');


    Route::get('visits_track', 'api\HomeController@visits_track');


});

Route::middleware(['track'])->group(function () {

    Route::get('home', 'api\HomeController@index');
    Route::get('get_units', 'api\UnitController@get_units');
    Route::get('get_unit/{id}', 'api\UnitController@get_unit');
    Route::get('get_product/{id}', 'api\ProductController@show');
    Route::get('show_article/{id}', 'api\ArticleController@show');
    Route::get('show_with_unit_id/{id}', 'api\ProductController@show_with_unit_id');
    Route::get('get_article', 'api\ArticleController@get_article');
    Route::get('get_articles', 'api\ArticleController@index1');
    Route::get('get_single_article/{id}', 'api\ArticleController@show');
    Route::post('search_product', 'api\ProductController@search_product');
    Route::post('search_unit', 'api\ProductController@search_unit');

    Route::apiResource('city', 'api\CityController');
    Route::apiResource('province', 'api\ProvinceController');

//alisalmabadi filter
    Route::get('products/get/filter', 'api\ProductController@getProductFilters');
    Route::post('products/post/global/filter', 'api\ProductController@productGlobalFilterResult');
    Route::post('products/post/unit/filter', 'api\ProductController@productUnitFilterResult');


    Route::get('vitrin', 'api\HomeController@vitrin');

});

Route::middleware(['auth:user'])->group(function () {

    Route::post('order/{id}', 'api\HomeController@order');
    Route::get('order', 'api\HomeController@order_of_user');
    Route::delete('order/{id}', 'api\OrderController@destroy');
    Route::get('show_user_info', 'api\UserController@show1');
    Route::get('add_to_favorite/{id}', 'api\UserController@add_to_favorite');
    Route::get('index_favorites', 'api\UserController@index_favorites');
    Route::get('delete_from_favorite/{id}', 'api\UserController@delete_from_favorite');

    Route::post('comment', 'api\CommentController@store');
    Route::get('get_comment', 'api\CommentController@index1');
    Route::post('complete_info', 'api\HomeController@complete_info');

});
