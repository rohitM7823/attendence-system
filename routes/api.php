<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\FaceController;

Route::get('/device/register', [DeviceController::class, 'register']);
Route::get('/face/embeddings', [FaceController::class, 'getEmbeddings']);
