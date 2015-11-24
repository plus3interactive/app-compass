<?php

namespace P3in\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use DB;
use Illuminate\Http\Request;
use P3in\Controllers\UiBaseController;
use P3in\Models\Page;
use P3in\Models\Section;
use P3in\Models\Website;
use Response;

class CpWebsitePageSectionsController extends UiBaseController
{
    public $meta_install = [
        'edit' => [
            'data_targets' => [
                [
                    'route' => 'websites.pages.show',
                    'target' => '#main-content-out',
                ],[
                    'route' => 'websites.pages.section.edit',
                    'target' => '#content-edit',
                ],
            ],
        ],
    ];

    public function __construct()
    {

        $this->middleware('auth');

        $this->controller_class = __CLASS__;
        $this->module_name = 'pages';

        $this->setControllerDefaults();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {}

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {}

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $website_id, $page_id)
    {

        $this->validate($request, [
            'layout_part' => 'required',
            'section_id' => 'required:numeric'
        ]);

        $website = Website::findOrFail($website_id);

        $page = $website->pages()->findOrFail($page_id);

        if ($request->has('reorder')) {

            foreach($request->reorder as $order => $item_id) {

                DB::table('page_section')->where('id', $item_id)
                    ->update(['order' => $order]);

            }

        }

        $order = intVal( DB::table('page_section')
            ->where('page_id', '=', $page_id)
            ->max('order') ) + 1;

        $section = Section::findOrFail($request->section_id);

        if ($section->fits !== $request->layout_part) {

            return $this->json([], false, 'Unable to complete the request, section has been dragged in the wrong spot.');

        }

        $page->sections()
            ->attach($section, [
                'order' => $order,
                'section' => $section->fits,
                'type' => null
            ]);

        return redirect()->action('\P3in\Controllers\CpWebsitePagesController@show', [$page->website->id, $page->id]);
        // return redirect()->action('\P3in\Controllers\CpWebsitePagesController@show', [$website->id, $page->id]);
        // return $this->json($this->setBaseUrl(['websites', $website_id, 'pages', $page->id, 'section', $request->section_id, 'edit']));

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {}

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($website_id, $page_id, $section_id)
    {
        $page = Page::findOrFail($page_id)->load('sections.photos');

        $section = $page->sections()
            ->where('page_section.id', $section_id)
            ->firstOrFail();

        $photos = $section->photos;

        $edit_view = 'sections/'.$section->edit_view;

        $this->setBaseUrl(['websites', $website_id, 'pages', $page_id, 'section', $section->pivot->id]);

        $meta = $this->meta;

        $record = json_decode($section->pivot->content);

        // return redirect()->action('\P3in\Controllers\CpWebsitePagesController@show', [$page->website->id, $page->id]);

        return view($edit_view, compact('meta', 'section', 'page', 'photos', 'record'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $website_id, $page_id, $section_id)
    {

        if ($request->file) {

        //     dd($request->file);

        }

        $page = Page::findOrFail($page_id);

        $content = json_encode($request->except(['_token', '_method']));

        $result = DB::table('page_section')->where('id', $section_id)
            ->update(['content' => $content]);

        dd("here");

        return redirect()->action('\P3in\Controllers\CpWebsitePagesController@show', [$page->website->id, $page->id]);
        // return $this->json($this->setBaseUrl(['websites', $website_id, 'pages', $page->id, 'section', $section_id, 'edit']));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $website_id, $page_id, $section_id) {

        $page = Page::findOrFail($page_id);

        DB::table('page_section')
            ->where('id', $section_id)
            ->delete();

        return redirect()->action('\P3in\Controllers\CpWebsitePagesController@show', [$page->website->id, $page->id]);
    }

}
