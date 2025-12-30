<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\EmotionService;

class EmotionController extends Controller
{
    public function predict(Request $request, EmotionService $emotion)
    {
        $request->validate(['image' => 'required|image|max:5210']);

        try {
            $file = $request->file('image');
            $result = $emotion->predictFromFile($file);
            
            return response()->json([
                'success' => true,
                'message' => 'Emotion predicted successfully',
                'data' => $result
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}