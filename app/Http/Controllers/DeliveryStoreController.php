<?php

namespace App\Http\Controllers;

use App\Models\DeliveryStore;
use Illuminate\Http\Request;

class DeliveryStoreController extends Controller
{
    public function index()
    {
        return DeliveryStore::with(['areas'])->all();
    }
}
