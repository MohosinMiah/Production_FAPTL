<?php

use App\Http\Controllers\Api\AccountClassController;
use App\Http\Controllers\Api\AccountController;
use App\Http\Controllers\Api\AccountTypeController;
use App\Http\Controllers\Api\AgentController;
use App\Http\Controllers\Api\Aggregate\LeaseAggregateController;
use App\Http\Controllers\Api\Aggregate\PropertyAggregateController;
use App\Http\Controllers\Api\AmenityController;
use App\Http\Controllers\Api\CommunicationSettingController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\DashboardLandLordController;
use App\Http\Controllers\Api\DashboardTenantController;
use App\Http\Controllers\Api\EmailConfigSettingController;
use App\Http\Controllers\Api\EmailTemplateController;
use App\Http\Controllers\Api\ExtraChargeController;
use App\Http\Controllers\Api\FeeController;
use App\Http\Controllers\Api\GeneralSettingController;
use App\Http\Controllers\Api\InvoiceItemController;
use App\Http\Controllers\Api\InvoicePaymentsController;
use App\Http\Controllers\Api\JournalController;
use App\Http\Controllers\Api\LandlordController;
use App\Http\Controllers\Api\LandlordInvoicesController;
use App\Http\Controllers\Api\LandlordLeasesController;
use App\Http\Controllers\Api\LandlordNoticesController;
use App\Http\Controllers\Api\LandlordPaymentsController;
use App\Http\Controllers\Api\LandlordProfileController;
use App\Http\Controllers\Api\LandlordPropertiesController;
use App\Http\Controllers\Api\LateFeeController;
use App\Http\Controllers\Api\LeaseController;
use App\Http\Controllers\Api\LeaseInvoicesController;
use App\Http\Controllers\Api\LeaseModeController;
use App\Http\Controllers\Api\LeaseSettingController;
use App\Http\Controllers\Api\LeaseTypeController;
use App\Http\Controllers\Api\Oauth\ForgotPasswordController;
use App\Http\Controllers\Api\Oauth\LoginController;
use App\Http\Controllers\Api\PaymentFrequencyController;
use App\Http\Controllers\Api\PaymentMethodController;
use App\Http\Controllers\Api\PeriodController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\PropertyController;
use App\Http\Controllers\Api\PropertyInvoicesController;
use App\Http\Controllers\Api\PropertyLeasesController;
use App\Http\Controllers\Api\PropertyNoticesController;
use App\Http\Controllers\Api\PropertySettingController;
use App\Http\Controllers\Api\PropertyTenantsController;
use App\Http\Controllers\Api\PropertyTypeController;
use App\Http\Controllers\Api\PropertyUnitsController;
use App\Http\Controllers\Api\ReadingController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\SmsConfigSettingController;
use App\Http\Controllers\Api\SmsTemplateController;
use App\Http\Controllers\Api\Summary\AdminSummaryController;
use App\Http\Controllers\Api\Summary\LandlordSummaryController;
use App\Http\Controllers\Api\Summary\PeriodBillingController;
use App\Http\Controllers\Api\Summary\PropertyReportController;
use App\Http\Controllers\Api\Summary\TenantSummaryController;
use App\Http\Controllers\Api\Summary\VacantUnitsController;
use App\Http\Controllers\Api\SystemNotificationController;
use App\Http\Controllers\Api\TaskCategoryController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\TenantController;
use App\Http\Controllers\Api\TenantInvoicesController;
use App\Http\Controllers\Api\TenantLeasesController;
use App\Http\Controllers\Api\TenantNoticesController;
use App\Http\Controllers\Api\TenantPaymentsController;
use App\Http\Controllers\Api\TenantProfileController;
use App\Http\Controllers\Api\TenantSettingController;
use App\Http\Controllers\Api\TenantTypeController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\UnitController;
use App\Http\Controllers\Api\UnitTypeController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\CurrencyController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\UserProfileController;
use App\Http\Controllers\Api\UtilityBillController;
use App\Http\Controllers\Api\UtilityController;
use App\Http\Controllers\Api\VacationNoticeController;
use App\Http\Controllers\Api\WaiverController;
use App\Http\Controllers\Api\PropertyImageController;
use App\Http\Controllers\Api\PropertyUnitController;
use App\Http\Controllers\Api\PropertyUnitImageController;
use App\Http\Controllers\Api\FTenantController;
use App\Http\Controllers\Api\FLeaseController;
use App\Http\Controllers\Api\FPaymentController;
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

Route::get('/grettings', function () {
    var_dump("Hello Good Evening Masum Bhai");
    die;
});

/*
|--------------------------------------------------------------------------
| Authentication routes
|--------------------------------------------------------------------------
|
|  Routes to obtain access_token and manage token refresh
|
*/
Route::group(array('prefix' => '/v1'), function () {
    Route::post('/login', [LoginController::class, 'login']);
    Route::post('/login/refresh', [LoginController::class, 'refresh']);
    Route::post('/logout', [LoginController::class, 'logout']);

    Route::post('/forgot_password', [ForgotPasswordController::class, 'forgotPassword']);
    Route::post('/reset_password', [ForgotPasswordController::class, 'resetPassword']);
});

/*
|--------------------------------------------------------------------------
| Protected system routes
|--------------------------------------------------------------------------
|
| For both admin users and customers
|
*/
Route::prefix('v1')->middleware(['auth:api,landlords,tenants', 'throttle:60,1'])->group(function () {

    Route::apiResource('/users', UserController::class)->middleware(['scope:manage-setting']);
    Route::apiResource('/currencies', CurrencyController::class)->middleware(['scope:manage-setting']);
    Route::apiResource('roles', RoleController::class)->middleware(['scope:manage-setting']);
    Route::apiResource('permissions', PermissionController::class)->middleware(['scope:manage-setting']);

    // Route::apiResource('agents', AgentController::class);
    Route::apiResource('accounts', AccountController::class)->middleware(['scope:view-report']);
    Route::post('accounts/lease', [AccountController::class, 'leaseAccountStatement'])
        ->middleware(['scope:view-lease,am-tenant,am-landlord']);
    Route::post('accounts/general', [AccountController::class, 'generalAccountStatement'])
        ->middleware(['scope:view-lease']);

    Route::apiResource('account_types', AccountTypeController::class)->middleware(['scope:manage-setting']);
    Route::apiResource('account_classes', AccountClassController::class)->middleware(['scope:manage-setting']);

    Route::apiResource('amenities', AmenityController::class)->middleware(['scope:view-property']);
    Route::apiResource('extra_charges', ExtraChargeController::class)->middleware(['scope:view-property']);

    Route::apiResource('fees', FeeController::class)->middleware(['scope:create-property']);

    Route::apiResource('journals', JournalController::class)->middleware(['scope:view-lease']);
    Route::apiResource('landlords', LandlordController::class)
        ->middleware(['scope:view-landlord,create-landlord,edit-landlord,delete-landlord']);
    Route::post('landlords/search', [LandlordController::class, 'search'])
        ->middleware(['scope:view-landlord,create-landlord,edit-landlord,delete-landlord']);

    Route::apiResource('lease_modes', LeaseModeController::class)->middleware(['scope:manage-setting']);
    Route::apiResource('lease_types', LeaseTypeController::class)->middleware(['scope:manage-setting']);
    Route::apiResource('payment_frequencies', PaymentFrequencyController::class)->middleware(['scope:manage-setting']);
    Route::apiResource('payment_methods', PaymentMethodController::class)->middleware(['scope:manage-setting']);

    Route::apiResource('property_types', PropertyTypeController::class)->middleware(['scope:manage-setting']);

    Route::post('properties/search', [PropertyController::class, 'search'])
        ->middleware(['scope:view-property,create-property,edit-property,delete-property']);
    Route::post('properties/report', [PropertyReportController::class, 'report']);
    Route::post('properties/periods', [PropertyController::class, 'periods']);
    Route::post('properties/upload_photo', [PropertyController::class, 'uploadPhoto']);
    Route::post('properties/profile_pic', [PropertyController::class, 'profilePic']);

    Route::apiResource('landlords.properties', LandlordPropertiesController::class)->only(['index', 'show']);
    Route::apiResource('landlords.leases', LandlordLeasesController::class)->only(['index', 'show']);
    Route::apiResource('landlords.invoices', LandlordInvoicesController::class)->only(['index', 'show']);
    Route::apiResource('landlords.payments', LandlordPaymentsController::class)->only(['index', 'show']);
    Route::apiResource('landlords.notices', LandlordNoticesController::class)->only(['index', 'show']);

    Route::get('units/vacants', [UnitController::class, 'vacantUnits']);


    Route::apiResource('properties.leases', PropertyLeasesController::class)->shallow();
    Route::apiResource('properties.tenants', PropertyTenantsController::class)->shallow();
    Route::apiResource('properties.invoices', PropertyInvoicesController::class)->shallow();
    Route::apiResource('properties.notices', PropertyNoticesController::class)->shallow();
    Route::apiResource('properties.units', PropertyUnitsController::class)->shallow();

    Route::apiResource('tenants.leases', TenantLeasesController::class)->only(['index', 'show']);
    Route::apiResource('tenants.payments', TenantPaymentsController::class)->only(['index', 'show']);
    Route::apiResource('tenants.notices', TenantNoticesController::class)->only(['index', 'show']);
    Route::apiResource('tenants.invoices', TenantInvoicesController::class)->only(['index', 'show']);

    Route::apiResource('leases.invoices', LeaseInvoicesController::class)->shallow();

    // Must be below properties.leases

    Route::post('leases/search', [LeaseController::class, 'search']);
    Route::post('leases/terminate', [LeaseController::class, 'terminate']);

    Route::apiResource('tenant_types', TenantTypeController::class);
    // Route::apiResource('tenants', TenantController::class)
    //     ->middleware(['scope:view-tenant']);;
	// Route::post('tenants/search', [TenantController::class, 'search']);

    Route::apiResource('invoices', InvoiceController::class);
    Route::post('invoices/search', [InvoiceController::class, 'search']);
    Route::post('invoices/general', [InvoiceController::class, 'downloadInvoice']);
    Route::post('invoices/download', [InvoiceController::class, 'downloadInvoice']);

    Route::apiResource('invoices.payments', InvoicePaymentsController::class)->shallow();

    Route::apiResource('invoices_items', InvoiceItemController::class);
    Route::post('invoices_items/search', [InvoiceItemController::class, 'search']);

    Route::apiResource('units', UnitController::class)->except(['show', 'delete']);
    Route::apiResource('unit_types', UnitTypeController::class);
    Route::apiResource('utilities', UtilityController::class);
    Route::apiResource('utility_bills', UtilityBillController::class);

    // Route::apiResource('/payments', PaymentController::class)->except(['update', 'delete']);
    // Route::post('payments/receipt', [PaymentController::class, 'downloadReceipt']);

    // Route::post('payments/approve', [PaymentController::class, 'approve']);
    // Route::post('payments/cancel',  [PaymentController::class, 'cancel']);

    Route::apiResource('/tasks', TaskController::class);
    Route::apiResource('/task_categories', TaskCategoryController::class);
    Route::apiResource('/vacation_notices', VacationNoticeController::class)->middleware(['scope:view-notice']);

    Route::apiResource('/lease_settings', LeaseSettingController::class)->middleware(['scope:manage-setting']);
    Route::apiResource('/tenant_settings', TenantSettingController::class)->middleware(['scope:manage-setting']);
    Route::apiResource('/property_settings', PropertySettingController::class)->middleware(['scope:manage-setting']);

    Route::apiResource('/communication_settings', CommunicationSettingController::class)
        ->middleware(['scope:manage-setting']);
    Route::apiResource('/sms_configuration_settings', SmsConfigSettingController::class)
        ->middleware(['scope:manage-setting']);
    Route::apiResource('/email_configuration_settings', EmailConfigSettingController::class)
        ->middleware(['scope:manage-setting']);
    Route::apiResource('/email_templates', EmailTemplateController::class)
        ->middleware(['scope:manage-setting']);
    Route::apiResource('/sms_templates', SmsTemplateController::class)
        ->middleware(['scope:manage-setting']);

    Route::apiResource('/readings', ReadingController::class);
    Route::post('readings/previous', [ReadingController::class, 'previousReading']);
	Route::post('readings/csv_template', [ReadingController::class, 'csvTemplate']);
	Route::post('readings/excel_template', [ReadingController::class, 'excelTemplate']);
	Route::post('readings/upload_readings', [ReadingController::class, 'uploadReadings']);

    Route::apiResource('/general_settings', GeneralSettingController::class)
        ->middleware(['scope:manage-setting']);
    Route::post('general_settings/upload_logo', [GeneralSettingController::class, 'uploadLogo'])
        ->middleware(['scope:manage-setting']);
    Route::post('general_settings/fetch_logo', [GeneralSettingController::class, 'fetchLogo'])->where(['file_name' => '.*'])
        ->middleware(['scope:manage-setting']);

    Route::apiResource('user_profile', UserProfileController::class)->only(['index', 'update'])
    ->middleware(['scope:edit-profile']);
    Route::post('user_profile/forgot_password', [UserProfileController::class, 'forgotPassword']);
       // ->middleware(['scope:profile-me']);
    Route::post('user_profile/upload_photo', [UserProfileController::class, 'uploadPhoto'])
        ->middleware(['scope:edit-profile']);
    Route::post('user_profile/fetch_photo', [UserProfileController::class, 'fetchPhoto'])->where(['file_name' => '.*'])
        ->middleware(['scope:edit-profile']);

    Route::apiResource('landlord_profile', LandlordProfileController::class)->only(['index', 'update'])
        ->middleware(['scope:am-landlord']);
    Route::apiResource('tenant_profile', TenantProfileController::class)->only(['index', 'update'])
        ->middleware(['scope:am-tenant']);

    Route::apiResource('transactions', TransactionController::class);
    Route::post('transactions/search', [TransactionController::class, 'search']);

    Route::apiResource('system_notifications', SystemNotificationController::class);
    Route::apiResource('periods', PeriodController::class);
    Route::apiResource('late_fees', LateFeeController::class);
    Route::apiResource('waivers', WaiverController::class)->only(['index', 'store'])
        ->middleware(['scope:waive-invoice']);

    // summary
    Route::get('vacant_units', [VacantUnitsController::class, 'index']);
 //   Route::get('billing_summaries', [PeriodBillingController::class, 'index']);
    Route::post('billing_summaries/property', [PeriodBillingController::class, 'propertyBilling']);

    Route::get('tenant_summaries', [TenantSummaryController::class, 'index'])->middleware(['scope:am-tenant']);
    Route::get('landlord_summaries', [LandlordSummaryController::class, 'index'])->middleware(['scope:am-landlord']);
    Route::get('admin_summaries', [AdminSummaryController::class, 'index']);

    // Aggregate
    Route::get('lease_support_data', [LeaseAggregateController::class, 'leaseData']);
    Route::get('property_support_data', [PropertyAggregateController::class, 'propertyData']);




 // *******************************  Start  : MOHOSIN : PROPERTIES ************************************************* START
 Route::apiResource('properties', PropertyController::class)->middleware(['scope:view-property,create-property,edit-property,delete-property']);
 Route::POST('property/update/{id}', [PropertyController::class,'updateProperty'])->middleware(['scope:view-property,create-property,edit-property,delete-property']);
 Route::GET('property/units/{id}', [ PropertyController::class,'propertyDetails' ])->middleware(['scope:view-property,create-property,edit-property,delete-property']);
 
// *******************************   END  : MOHOSIN : PROPERTIES ********************************************************** END




 // *******************************  Start  : MOHOSIN : PROPERTY_IMAGE ************************************************* START
 Route::apiResource('propertyimages', PropertyImageController::class)->middleware(['scope:view-property,create-property,edit-property,delete-property']);
 Route::POST('propertyimage/update/{id}', [PropertyImageController::class,'updatePropertyImage'])->middleware(['scope:view-property,create-property,edit-property,delete-property']);

// *******************************   END  : MOHOSIN : PROPERTY_IMAGE ********************************************************** END



 // *******************************  Start  : MOHOSIN : PROPERTY_UNIT  ************************************************* START
 Route::apiResource('propertyunits', PropertyUnitController::class)->middleware(['scope:view-property,create-property,edit-property,delete-property']);
 Route::POST('propertyunit/update/{id}', [PropertyUnitController::class,'updatePropertyUnit'])->middleware(['scope:view-property,create-property,edit-property,delete-property']);

// *******************************   END  : MOHOSIN : PROPERTY_IMAGE ********************************************************** END



 // *******************************  Start  : MOHOSIN : PROPERTY_UNIT IMAGE ************************************************* START
 Route::apiResource('propertyunitimages', PropertyUnitImageController::class)->middleware(['scope:view-property,create-property,edit-property,delete-property']);
 Route::POST('propertyunitimage/update/{id}', [PropertyUnitImageController::class,'updatePropertyUnit'])->middleware(['scope:view-property,create-property,edit-property,delete-property']);

// *******************************   END  : MOHOSIN : PROPERTY_UNIT IMAGE ********************************************************** END



 // *******************************  Start  : MOHOSIN : FTenant  ************************************************* START
 Route::apiResource('tenants', FTenantController::class)->middleware(['scope:view-property,create-property,edit-property,delete-property']);
 Route::POST('tenant/update/{id}', [FTenantController::class,'updateTenant'])->middleware(['scope:view-property,create-property,edit-property,delete-property']);

// *******************************   END  : MOHOSIN :  FTenant  ********************************************************** END


 // *******************************  Start  : MOHOSIN : FLease  ************************************************* START
 Route::apiResource('leases', FLeaseController::class )->middleware(['scope:view-property,create-property,edit-property,delete-property']);
 Route::POST( 'lease/update/{id}', [ FLeaseController::class,'updateFLease' ] )->middleware(['scope:view-property,create-property,edit-property,delete-property']);
 
//  Get Property unit list based on property ID 
 Route::GET( 'lease/unit_list/property/{id}', [ FLeaseController::class,'getPropertyUnitsByPropertyID' ] )->middleware(['scope:view-property,create-property,edit-property,delete-property']);
 
// *******************************   END  : MOHOSIN :  FLease  ********************************************************** END


 // *******************************  Start  : MOHOSIN : FLease  ************************************************* START
 Route::apiResource( 'payments', FPaymentController::class )->middleware(['scope:view-property,create-property,edit-property,delete-property']);
 Route::POST( 'payment/update/{id}', [ FPaymentController::class,'updatePayment' ] )->middleware(['scope:view-property,create-property,edit-property,delete-property']);
 Route::POST( 'payments/search', [ FPaymentController::class,'filterPaymentList' ] )->middleware(['scope:view-property,create-property,edit-property,delete-property']);

 Route::GET( 'payments/total_paidamount/all_get', [ FPaymentController::class,'filterTotalPaymentAmountGET' ] )->middleware(['scope:view-property,create-property,edit-property,delete-property']);
 Route::POST( 'payments/total_paidamount/all_post', [ FPaymentController::class,'filterTotalPaymentAmountPOST' ] )->middleware(['scope:view-property,create-property,edit-property,delete-property']);

 Route::GET( 'payments/list/pending', [ FPaymentController::class,'pendingPaymentList' ] )->middleware(['scope:view-property,create-property,edit-property,delete-property']);
 Route::GET( 'payments/list/recorded', [ FPaymentController::class,'recordedPaymentList' ] )->middleware(['scope:view-property,create-property,edit-property,delete-property']);

 Route::GET( 'payment/change/recorded/{id}', [ FPaymentController::class,'statusRecorded' ] )->middleware(['scope:view-property,create-property,edit-property,delete-property']);
 Route::GET( 'payment/change/deposited/{id}', [ FPaymentController::class,'statusDeposited' ] )->middleware(['scope:view-property,create-property,edit-property,delete-property']);
 Route::GET( 'payments/mark/all/deposited', [ FPaymentController::class,'mark_all_deposited' ] )->middleware(['scope:view-property,create-property,edit-property,delete-property']);
 

//  Get Property unit list based on property ID 
 Route::GET( 'payment/unit_list/property/{id}', [ FPaymentController::class,'getPropertyUnitsByPropertyID' ] )->middleware(['scope:view-property,create-property,edit-property,delete-property']);
 
// *******************************   END  : MOHOSIN :  FLease  ********************************************************** END





});



// Unprotected routes
// Dev only
//TODO  - remove this section on production launch
Route::group(array('prefix' => '/v0'), function () {
  //  Route::get('accounts/test', [AccountController::class, 'generalAccountStatement']);
  //  Route::get('invoices/test', [InvoiceController::class, 'downloadInvoice']);

});
