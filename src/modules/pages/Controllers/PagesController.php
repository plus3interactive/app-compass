<?php

namespace P3in\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\Factory;
use P3in\Models\Page;
use P3in\Models\Section;
use P3in\Models\Website;
use Mail;

class PagesController extends Controller
{

    public function renderPage(Request $request, $url = '')
    {
        $page = Page::byUrl($url)->ofWebsite()->firstOrFail();

        $data = $page->render();

        $data['page'] = $page;
        $data['settings'] = $page->settings->data;
        $data['website'] = Website::current();

        $data['navmenus'] = [];

        $navmenus = Website::current()->navmenus()
            ->whereNull('parent_id')
            ->get();

        foreach ($navmenus as $navmenu) {
            $navmenu->load('items');

            $data['navmenus'][$navmenu->name] = $navmenu->toArray();

            $data['navmenus'][$navmenu->name]['children'] = [];

            foreach($navmenu->children as $child) {

                $data['navmenus'][$navmenu->name]['children'][$child->id] = $child;

            }


        }

        $data['navmenus'] = json_decode(json_encode($data['navmenus']));

        return view('layouts.master.'.str_replace(':', '_', $page->layout), $data);

    }

    public function submitForm(Request $request)
    {

        $website = Website::current();

        $from = $website->config->from_email ?: 'info@bostonpads.com';

        $to = $request->get('to') ?: $website->config->default_recipients;

        $data = $request->except(['_token', 'to', 'heading', 'subheading', 'text', 'style']);

        Mail::send('mail.form-submission', ['website' => $website, 'data' => $data], function($message) use($from, $to, $request) {
            $message->from($from)
                ->to($to)
                ->subject('Form submission');

                foreach($request->file() as $field_name => $file) {

                    $message->attach($file->getRealPath(), [
                        'as' => $file->getClientOriginalExtension(),
                        'mime' => $file->getMimeType()
                    ]);

                }

            });

    }
}
