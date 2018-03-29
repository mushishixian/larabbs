<?php

use Illuminate\Http\Request;

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

$api = app(Dingo\Api\Routing\Router::class);

$api->version('v1', [
    'namespace' => 'App\Http\Controllers\Api',
    'middleware' => ['serializer:array', 'cors', 'bindings'],
], function ($api) {
    $api->group([
        'middleware' => 'api.throttle',
        'limit' => config('api.rate_limits.access.limit'),
        'expires' => config('api.rate_limits.access.expires'),
    ], function ($api) {
        //游客可以访问的接口
        $api->get('categories', 'CategoriesController@index')
            ->name('api.categories.index');

        //文章
        $api->get('topics', 'TopicsController@index')
            ->name('api.topics.index');
        $api->post('topics', 'TopicsController@store')
            ->name('api.topics.store');
        $api->patch('topics/{topic}', 'TopicsController@update')
            ->name('api.topics.update');
        $api->delete('topics/{topic}', 'TopicsController@delete')
            ->name('api.topics.delete');
        $api->get('users/{user}/topics', 'TopicsController@userIndex')
            ->name('api.users.topics.index');
        $api->get('topics/{topic}', 'TopicsController@show')
            ->name('api.topics.show');

        //回复
        $api->post('topics/{topics}/replies', 'RepliesController@store')
            ->name('api.replies.store');
        $api->delete('topics/{topic}/replies/{reply}', 'RepliesController@destroy')
            ->name('api.topics.replies.destroy');
        $api->get('topics/{topic}/replies', 'RepliesController@index')
            ->name('api.topics.replies.index');
        $api->get('users/{user}/replies', 'RepliesController@userIndex')
            ->name('api.users.replies.index');

        // 登录
        $api->post('authorizations', 'AuthorizationsController@store')
            ->name('api.authorizations.store');
        $api->put('authorizations/current', 'AuthorizationsController@update')
            ->name('api.authorizations.update');
        $api->delete('authorizations/current', 'AuthorizationsController@destroy')
            ->name('api.authorizations.destroy');
        // 需要 token 验证的接口
        $api->group(['middleware' => 'api.auth'], function ($api) {
            // 当前登录用户信息
            $api->get('user', 'UsersController@me')
                ->name('api.user.show');
            $api->patch('user', 'UsersController@update')
                ->name('api.user.update');
            $api->post('images', 'ImagesController@store')
                ->name('api.images.store');

        });
    });

    $api->group([
        'middleware' => 'api.throttle',
        'limit' => config('api.rate_limits.sign.limit'),
        'expires' => config('api.rate_limits.sign.expires'),
    ], function ($api) {
        $api->get('version', function () {
            return response('this is version v1');
        });
        $api->post('verificationCodes', 'VerificationCodesController@store')
            ->name('api.verificationCodes.store');
        $api->post('users', 'UsersController@store')
            ->name('api.users.store');
        $api->post('captchas', 'CaptchasController@store')
            ->name('api.captchas.store');


    });
});
