<?php

namespace App\Http\Controllers;

use App\Models\Pages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PagesController extends Controller
{

    function privacyPolicy()
    {
        $data = Pages::first();
        return $data->privacy;
    }

    function termsOfUse()
    {
        $data = Pages::first();
        return $data->termsofuse;
    }

    function viewTerms()
    {
        $data = Pages::first();
        return view('pages.viewTerms', ['data' => $data->termsofuse]);
    }

    function updatePrivacy(Request $request)
    {
        $data = Pages::first();
        $data->privacy = $request->content;
        $data->save();

        return  json_encode(['status' => true, 'message' => "update successful"]);
    }

    function updateTerms(Request $request)
    {
        $data = Pages::first();
        $data->termsofuse = $request->content;
        $data->save();

        return  json_encode(['status' => true, 'message' => "update successful"]);
    }

    function viewPrivacy()
    {
        
        $data = Pages::first();
        return view('pages.viewPrivacy', ['data' => $data->privacy]);
    }

    public function addContentForm(Request $request)
    {
        $privacy = Pages::first();
        if ($privacy) {
            if ($request->has('privacy')) {
                $privacy->privacy = $request->privacy;
            }
        }
        $privacy->save();

        return response()->json([
            'status' => true,
            'message' => 'privacy Policy Added',
            'data' => $privacy,
        ]); 
    }

    public function addTermsForm(Request $request)
    {
        $terms = Pages::first();
        if ($terms) {
            if ($request->has('termsofuse')) {
                $terms->termsofuse = $request->termsofuse;
            }
        }
        $terms->save();

        return response()->json([
            'status' => true,
            'message' => 'Terms Policy Added',
            'data' => $terms,
        ]); 
    }

    private const DOCUMENTS = [
        'community-guidelines' => 'Community Guidelines',
        'copyright-ip-policy' => 'Copyright / IP Policy',
        'payment-terms' => 'Payment Terms',
        'user-guide' => 'User Guide',
    ];

    public function editDocument(string $slug)
    {
        abort_unless(isset(self::DOCUMENTS[$slug]), 404);
        $document = DB::table('legal_documents')->where('slug', $slug)->first();
        return view('pages.editDocument', ['slug' => $slug, 'title' => self::DOCUMENTS[$slug], 'document' => $document]);
    }

    public function saveDocument(Request $request, string $slug)
    {
        abort_unless(isset(self::DOCUMENTS[$slug]), 404);
        $data = $request->validate(['content' => 'nullable|string', 'published' => 'nullable|boolean']);
        DB::table('legal_documents')->updateOrInsert(['slug' => $slug], [
            'content' => $data['content'] ?? '',
            'published' => $request->boolean('published'),
            'updated_at' => now(),
        ]);
        return redirect()->route('documents.edit', $slug)->with('document_saved', true);
    }

    public function showDocument(string $slug)
    {
        abort_unless(isset(self::DOCUMENTS[$slug]), 404);
        $document = DB::table('legal_documents')->where('slug', $slug)->where('published', true)->first();
        abort_unless($document && trim($document->content) !== '', 404);
        return response('<!doctype html><html><head><meta name="viewport" content="width=device-width, initial-scale=1"><meta charset="utf-8"><title>'.e(self::DOCUMENTS[$slug]).'</title></head><body><h1>'.e(self::DOCUMENTS[$slug]).'</h1><div>'.$document->content.'</div></body></html>')->header('Content-Security-Policy', "default-src 'none'; style-src 'unsafe-inline'; img-src https: data:; base-uri 'none'; form-action 'none'");
    }

}
