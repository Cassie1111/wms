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

// 首页
Route::group(['namespace' => 'Home', 'middleware' => ['auth', 'check']], function () {
    Route::get('/home/getBgManageCount',
        'HomeController@getBgManageCount')->name('home.getBgManageCount');
    Route::get('/home/getMainAccountInfo', 'HomeController@getMainAccountInfo');
    Route::resource('home', 'HomeController');
});

// 登陆相关
Route::group(['namespace' => 'Auth', 'prefix' => 'auth'], function() {
    Route::get('/checkLogin', 'AuthController@checkLogin');
    Route::post('/doLogin', 'AuthController@doLogin');
    Route::post('/logout', 'AuthController@logout');
    Route::get('/getRegisterSign', 'AuthController@getRegisterSignature');
});

Route::group(['prefix' => 'distribution', 'namespace' => 'Distribution', 'middleware' => ['auth', 'check']], function () {
    Route::post('distributionLaunch', 'DistributionController@launch')->name('distribution.launch');
    Route::post('distributionComment', 'DistributionController@editComment')->name('distribution.comment');
    Route::resource('distributionDetail', 'DistributionController');
});

Route::group(['prefix' => 'order', 'namespace' => 'Order', 'middleware' => ['auth', 'check']], function () {
    Route::post('orderDetail/edit', 'OrderDetailController@edit')->name('orderDetail.edit');
    Route::resource('orderDetail', 'OrderDetailController');
    Route::get('exportExcel', 'RefundController@exportExcel')->name('refund.exportExcel');
    Route::post('refundOrder', 'RefundController@refundOrder');
    Route::resource('refund', 'RefundController');
    Route::get('orderList/exportExcel', 'OrderListController@exportExcel');
    Route::resource('orderList', 'OrderListController');
    Route::get('settingOrderList/exportExcel', 'SettingOrderListController@exportExcel');
    Route::post('settingOrderList/changePrice', 'SettingOrderListController@changePrice');
    Route::resource('settingOrderList', 'SettingOrderListController');
});

Route::group(['prefix' => 'funds', 'namespace' => 'Funds', 'middleware' => ['auth', 'check']], function () {
    Route::post('fundsList/setOrderStatus', 'FundListController@setOrderStatus');
    Route::get('fundsList/exportExcel', 'FundListController@exportExcel');
    Route::resource('fundsList', 'FundListController');
});

Route::group(['prefix' => 'marketing', 'namespace' => 'Marketing', 'middleware' => ['auth', 'check']], function () {
    Route::get('getActivityConfigList', 'TemplateController@getActivityConfigList')->name('activityConfig.list');
    Route::get('getActivityConfigDetail', 'TemplateController@getActivityConfigDetail')->name('activityConfig.detail');
    Route::post('editActivityConfigDetail', 'TemplateController@editActivityConfigDetail')->name('activityConfig.edit');
    Route::resource('banner', 'TemplateController');
    Route::resource('activity', 'ActivityController');
    Route::resource('resource', 'ResourceController');
    Route::get('getGoodsList', 'CommonController@getGoodsList')->name('marketing.getGoodsList');
    Route::get('getSupplierList', 'CommonController@getSupplierList')->name('marketing.getSupplierList');
    Route::get('getBrandList', 'CommonController@getBrandList')->name('marketing.getBrandList');

    // 推送
    Route::resource('pusher', 'PusherController');
});

Route::group(['namespace' => 'RetailerManage', 'middleware' => ['auth', 'check']], function () {
    Route::resource('retailer', 'RetailerManageController');
});

Route::group(['namespace' => 'SupplierManage', 'middleware' => ['auth', 'check']], function () {
    Route::get('/supplier/getCountryCode', 'SupplierManageController@getCountryCode')->name('supplier.getCountryCode');
    Route::resource('supplier', 'SupplierManageController');
});


Route::group(['namespace' => 'Message', 'middleware' => ['auth', 'check']], function() {
    Route::get('/getMessageList', 'MessageController@getMessageList');
    Route::get('/getNoticeList', 'MessageController@getNoticeList');
    Route::get('/updateMessage', 'MessageController@updateMessage');
    Route::get('/getNoticeDetail', 'MessageController@getNoticeDetail');

});

Route::get('/getUploadSign', 'UploadController@getUploadSign');

// 系统设置
Route::group(['namespace' => 'Settings', 'middleware' => ['auth', 'check']], function () {
    Route::get('/account/getCountryCode',
        'AccountManageController@getCountryCode')->name('account.getCountryCode');
    Route::get('/account/getDepartmentList',
        'AccountManageController@getDepartmentList')->name('account.getDepartmentList');
    Route::get('/account/getDutyList',
        'AccountManageController@getDutyList')->name('account.getDutyList');
    Route::resource('account', 'AccountManageController');
});

// 客服中心 , 'middleware' => ['auth', 'check']
Route::group(['prefix' => 'im', 'namespace' => 'Service', 'middleware' => ['auth', 'check']], function () {
    Route::get('/getUnReadConversationList', 'ServiceController@getUnReadConversationList');

    // 获取未读、已读用户列表
    Route::get('/unread', 'ServiceController@getUnreadUserList')->name('service.unread');
    Route::get('/read', 'ServiceController@getReadUserList')->name('service.read');

    // 拉取、发送消息
    Route::get('/pull', 'ServiceController@pull')->name('service.pull');
    Route::post('/push', 'ServiceController@push')->name('service.push');

    // 释放会话
    Route::get('/release', 'ServiceController@release')->name('service.release');
    // 搜索聊天记录
    Route::get('/search', 'ServiceController@search')->name('service.search');
    // 获取环信客服配置信息
    Route::get('/getConfig', 'ServiceController@getConfig')->name('service.getConfig');
});

// 产品管理
Route::group(['prefix' => 'goods', 'namespace' => 'Goods', 'middleware' => ['auth', 'check']], function () {
    Route::get('/goodsList','GoodsListController@getGoodsList')->name('goods.getGoodsList');
    Route::get('/getSupplierList','GoodsListController@getSupplierList')->name('goods.getSupplierList');
    Route::post('/updatePrice', 'GoodsListController@updatePrice')->name('goods.updatePrice');
    Route::post('/unshelve', 'GoodsListController@unshelve')->name('goods.unshelve');
    Route::get('/exportProductList', 'GoodsListController@exportProductList')->name('goods.exportProductList');
    Route::get('/exportAllProductList', 'GoodsListController@exportAllProductList')->name('goods.exportAllProductList');
    Route::post('importGoods','GoodsListController@importGoods')->name('goods.importGoods');
    Route::post('/syncOfGoodsInfo','GoodsListController@syncOfGoodsInfo')->name('warehouse.syncOfGoodsInfo');

    Route::get('/getCates', 'CreateGoodsController@getCates')->name('createGoods.getCates');
    Route::get('/searchProductQuoteType', 'CreateGoodsController@searchProductQuoteType')->name('createGoods.searchProductQuoteType');
    Route::post('readRemindInfo', 'CreateGoodsController@readRemindInfo')->name('createGoods.readRemindInfo');
    Route::get('/getLastestAnnouncement','CreateGoodsController@getLastestAnnouncement')->name('createGoods.getLastestAnnouncement');

    Route::get('/getGoodsDetail', 'GoodsDetailController@getGoodsDetail')->name('goods.getGoodsDetail');
    Route::get('/getQuoteInfoByCate', 'GoodsDetailController@getQuoteInfoByCate')->name('goods.getQuoteInfoByCate');
    Route::get('/getBrands', 'GoodsDetailController@getBrands')->name('goods.getBrands');
    Route::get('/getGoodsPropertyList', 'GoodsDetailController@getGoodsPropertyList')->name('goods.getGoodsPropertyList');
    Route::post('/editProductDetail', 'GoodsDetailController@editProductDetail')->name('goods.editProductDetail');
});
