<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\SearchService;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __invoke(Request $r)
    {
        return response()->json(['results' => SearchService::admin((string) $r->query('q', ''))]);
    }
}
