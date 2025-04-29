<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\FaceController;
use App\Http\Controllers\EmployeesController;


Route::post('/device/register', [DeviceController::class, 'register']);
Route::get('/face/embeddings', [FaceController::class, 'getEmbeddings']);
Route::get('/device/status', [DeviceController::class, 'deviceStatus']);
Route::get('/device/all', [DeviceController::class, 'index']);
Route::post('/device/status', [DeviceController::class, 'updateStatus']);
Route::post('/employee', [EmployeesController::class, 'add']);
Route::post('/employee/{id}', [EmployeesController::class, 'update']);
Route::delete('/employee/{id}', [EmployeesController::class, 'delete']);
Route::get('/employee/all', [EmployeesController::class, 'index']);
Route::delete('/device/deleteAll', [DeviceController::class, 'deleteAllDevices']);
Route::get('/devices/approved', [DeviceController::class, 'approvedDevices']);
Route::post('/employee/face/metadata/all', [EmployeesController::class, 'getFaceEmbeddings']);
