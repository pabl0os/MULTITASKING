<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stichoza\GoogleTranslate\GoogleTranslate;

class TranslationController extends Controller
{
    public function index()
    {
        return view('translation.index');
    }

    public function translate(Request $request)
    {
        $request->validate([
            'text' => 'required|string',
            'source_lang' => 'nullable|string',
            'target_lang' => 'required|string',
        ]);

        $text = $request->input('text');
        $sourceLang = $request->input('source_lang', 'auto');
        $targetLang = $request->input('target_lang');

        try {
            $tr = new GoogleTranslate();
            $tr->setOptions(['verify' => false]);
            $tr->setSource($sourceLang);
            $tr->setTarget($targetLang);
            
            $translatedText = $tr->translate($text);
            
            return response()->json([
                'success' => true,
                'translated_text' => $translatedText
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al traducir: ' . $e->getMessage()
            ], 500);
        }
    }
}
