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

        return view('layouts.master.'.str_replace(':', '_', $page->layout), $data);

    }

    public function submitForm(Request $request)
    {
        if ($website->settings('recaptcha_secret_key')) {
            $recaptcha = new \ReCaptcha\ReCaptcha($website->settings('recaptcha_secret_key'));
            $resp = $recaptcha->verify($request->get('g-recaptcha-response'), $request->getClientIp());

            if ($resp->isSuccess()) {
                dd('verified!');
            }else{
                $errors = $resp->getErrorCodes();
                dd($errors);
            }
        }

        $website = Website::current();

        $from = $website->settings('from_email') ?: 'info@bostonpads.com';

        $to = $request->has('form_id') ? base64_decode($request->get('form_id')) : $website->settings('to_email');
        $to = 'jubair.saidi@p3in.com';
        $data = $request->except(['_token', 'form_id', 'meta', 'heading', 'subheading', 'text', 'style', 'form_name', 'file', 'g-recaptcha-response']);
        $meta = unserialize(base64_decode($request->get('meta')));

        $formData = [
            'website' => $website,
            'data' => $data,
            'name' => $request->get('form_name'),
            'messaging' => $meta['email_body_intro'],
        ];

        Mail::send('mail.form-submission', $formData, function($message) use($from, $to, $request, $website) {
            $message->from($from)
                ->to($to)
                ->subject('New '.$request->get('form_name').' from '.$website->site_name);

            foreach($request->file() as $field_name => $file) {

                $message->attach($file->getRealPath(), [
                    'as' => $file->getClientOriginalName(),
                    'mime' => $file->getMimeType()
                ]);

            }

        });

        return redirect($request->get('redirect'))->with('success_message', $meta['success_message']);

    }
}
