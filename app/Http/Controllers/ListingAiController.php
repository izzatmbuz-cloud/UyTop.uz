<?php

namespace App\Http\Controllers;

use App\Http\Requests\ParseListingTextRequest;
use App\Services\ListingTextParser;
use Illuminate\Http\JsonResponse;

class ListingAiController extends Controller
{
    public function __invoke(ParseListingTextRequest $request, ListingTextParser $parser): JsonResponse
    {
        return response()->json($parser->parse($request->validated('text')));
    }
}
