<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\ServerInfo;
use Illuminate\View\View;

/** GET /server - everything about the machine this app runs on. */
class ServerController extends Controller
{
    public function index(ServerInfo $server): View
    {
        return view('admin.server.index', ['info' => $server->all()]);
    }
}
