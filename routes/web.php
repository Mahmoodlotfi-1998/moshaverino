<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PhoneOtpController;

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


Route::get('/', function () {
    return view('welcome');
});



Route :: post("/api/test" , [\App\Http\Controllers\TestController::class , 'Test'] );
Route :: post("/phone_otp" , [\App\Http\Controllers\TestController::class , 'PhoneOtp'] );
Route :: post("/narsofal/phone_otp" , [\App\Http\Controllers\TestController::class , 'NarsofalPhoneOtp'] );


Route :: get("/api/photo/{name}" , [\App\Http\Controllers\HomeController::class , 'GetPhoto'] );
Route :: get("/api/photo/{path}/{name}" , [\App\Http\Controllers\HomeController::class , 'GetGeneralPhoto'] );
Route :: get("/pages/about-us" , [\App\Http\Controllers\HomeController::class , 'PageAbout'] );
Route :: get("/api/payment" , [\App\Http\Controllers\PaymnetController::class , 'Payment'] );

Route :: post("/api/phone_otp" , [PhoneOtpController::class , 'Manage_Request'] );

Route :: post("/api/register" , [\App\Http\Controllers\UserController::class , 'Register'] );
Route :: post("/api/norm_info" , [\App\Http\Controllers\UserController::class , 'NormInfo'] );
Route :: post("/api/doctor_info" , [\App\Http\Controllers\UserController::class , 'DoctorInfo'] );
Route :: post("/api/norm_edit" , [\App\Http\Controllers\UserController::class , 'NormEdit'] );
Route :: post("/api/doctor_edit" , [\App\Http\Controllers\UserController::class , 'DoctorEdit'] );

Route :: post("/api/upload_photo" , [\App\Http\Controllers\HomeController::class , 'UploadPhoto'] );
Route :: post("/api/get_register_requirment" , [\App\Http\Controllers\HomeController::class , 'GetRegisterRequirement'] );
Route :: post("/api/get_city" , [\App\Http\Controllers\HomeController::class , 'GetCity'] );

Route :: post("/api/representation" , [\App\Http\Controllers\RepresentationController::class , 'Give'] );

Route :: post("/api/add_ticket" , [\App\Http\Controllers\TicketsController::class , 'AddTicket'] );
Route :: post("/api/give_ticket" , [\App\Http\Controllers\TicketsController::class , 'GiveTicket'] );

Route :: post("/api/give_workshop" , [\App\Http\Controllers\WorkshopController::class , 'GiveWorkshop'] );
Route :: post("/api/get_new_workshop" , [\App\Http\Controllers\WorkshopController::class , 'GetNewWorkshop'] );

Route :: post("/api/give_products" , [\App\Http\Controllers\ProductsController::class , 'GiveProducts'] );
Route :: post("/api/get_new_product" , [\App\Http\Controllers\ProductsController::class , 'GetNewProduct'] );

Route :: post("/api/filter" , [\App\Http\Controllers\HomeController::class , 'Filter'] );
Route :: post("/api/get_doctor_page" , [\App\Http\Controllers\HomeController::class , 'DoctorInfo'] );
Route :: post("/api/doctor_save" , [\App\Http\Controllers\HomeController::class , 'DoctorSave'] );
Route :: post("/api/get_doctor_comment" , [\App\Http\Controllers\HomeController::class , 'GetDoctorComment'] );
Route :: post("/api/insert_doctor_comment" , [\App\Http\Controllers\HomeController::class , 'InsertDoctorComment'] );
Route :: post("/api/get_wallet_page" , [\App\Http\Controllers\HomeController::class , 'GetWalletPage'] );
Route :: post("/api/main_page" , [\App\Http\Controllers\HomeController::class , 'MainPage'] );

Route :: post("/api/get_turns_money" , [\App\Http\Controllers\TurnsController::class , 'GetMoneyTurns'] );
Route :: post("/api/edit_turns_money" , [\App\Http\Controllers\TurnsController::class , 'EditMoneyTurns'] );
Route :: post("/api/get_turns_free" , [\App\Http\Controllers\TurnsController::class , 'GetFreeTurns'] );
Route :: post("/api/edit_turns_free" , [\App\Http\Controllers\TurnsController::class , 'EditFreeTurns'] );
Route :: post("/api/get_open_time_money" , [\App\Http\Controllers\TurnsController::class , 'GetOpenTimeMoney'] );
Route :: post("/api/check_new_turns" , [\App\Http\Controllers\TurnsController::class , 'CheckNewTurn'] );
Route :: post("/api/get_new_turns" , [\App\Http\Controllers\TurnsController::class , 'GetNewTurn'] );
Route :: post("/api/set_referer" , [\App\Http\Controllers\TurnsController::class , 'SetReferer'] );
Route :: post("/api/end_turn" , [\App\Http\Controllers\TurnsController::class , 'EndTrun'] );

Route :: post("/api/get_reports" , [\App\Http\Controllers\ReportsController::class , 'GetReports'] );
Route :: post("/api/get_report_page" , [\App\Http\Controllers\ReportsController::class , 'GetReportPage'] );
Route :: post("/api/get_referer_doctor" , [\App\Http\Controllers\ReportsController::class , 'GetRefererDoctor'] );

