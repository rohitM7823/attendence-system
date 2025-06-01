<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\FaceController;
use App\Http\Controllers\EmployeesController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\AdminsController;
use App\Http\Controllers\DepartmentController;



Route::get('/face/embeddings', [FaceController::class, 'getEmbeddings']);

Route::post('/device/register', [DeviceController::class, 'register']);
Route::get('/device/status', [DeviceController::class, 'deviceStatus']);
Route::get('/device/all', [DeviceController::class, 'index']);
Route::post('/device/status', [DeviceController::class, 'updateStatus']);
Route::delete('/device/deleteAll', [DeviceController::class, 'deleteAllDevices']);
Route::get('/devices/approved', [DeviceController::class, 'approvedDevices']);


Route::post('/employee', [EmployeesController::class, 'add']);
Route::post('/employee/{id}/update', [EmployeesController::class, 'update']);
Route::delete('/employee/{id}/delete', [EmployeesController::class, 'delete']);
Route::get('/employee/all', [EmployeesController::class, 'index']);
Route::get('/employee/face/metadata/all', [EmployeesController::class, 'getFaceEmbeddings']);
Route::get('/employee/{id}', [EmployeesController::class, 'show']);
Route::get('/employee/{id}/site-radius', [EmployeesController::class, 'getEmployeeSiteRadius']);
Route::post('/employee/{id}/attendance-take', [EmployeesController::class, 'updateAttendanceTime']);
Route::post('/employee/attendance-report-pdf', [EmployeesController::class, 'downloadAttendanceReportPdf']);


Route::post('/site/add', [SiteController::class, 'add']);
Route::get('/sites', [SiteController::class, 'index']);
Route::delete('/site/{id}', [SiteController::class, 'delete']);
Route::put('/site/{id}', [SiteController::class, 'update']);


Route::get('/shifts', [ShiftController::class, 'index']);
Route::get('/shifts/{id}', [ShiftController::class, 'show']);
Route::post('/shifts', [ShiftController::class, 'storeOrUpdate']);


Route::post('/admin/login', [AdminsController::class, 'login']);
Route::post('/admin/forgot-password', [AdminsController::class, 'forgotPassword']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/admin/logout', [AdminsController::class, 'logout']);
});


Route::get('/departments', [DepartmentController::class, 'index']);
Route::post('/departments/add', [DepartmentController::class, 'store']);
Route::put('/departments/{id}', [DepartmentController::class, 'update']);
Route::delete('/departments/{id}', [DepartmentController::class, 'destroy']);