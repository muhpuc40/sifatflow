<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseCategory;
use Illuminate\Http\JsonResponse;

/** GET /api/v1/categories : only categories that have at least one published course. */
class CategoryController extends Controller
{
    public function index(): JsonResponse
    {
        $categories = CourseCategory::query()
            ->whereHas('courses', fn ($q) => $q->published())
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'icon']);

        return response()->json(['data' => $categories]);
    }
}
