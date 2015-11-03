<?php

namespace P3in\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use Auth;
use Event;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use P3in\Controllers\ModularBaseController;
use P3in\Models\Navmenu;

class UiBaseController extends ModularBaseController {

    public $records;
    public $record;

    // public function __construct()
    // {
    // }

    public function build($type)
    {
        $method = 'view'.$type;

        return call_user_func([$this, $method]);
    }

    private function viewIndex()
    {
        return view('ui::index', [
            'meta' => $this->meta,
            'records' => $this->records,
        ]);
    }

    private function viewCreate()
    {
        return view('ui::create', [
            'meta' => $this->meta,
        ]);
    }

    private function viewShow()
    {
        // $subnav = Event::fire('navigation.cms.sub', json_encode(['origin' => get_class($record), 'id' => $record->id] ))[0];

        return view('ui::show', [
            'meta' => $this->meta,
            'record' => $this->record,
            'nav' => $this->getNav()
        ]);

    }

    private function viewEdit()
    {
        return view('ui::edit', [
            'meta' => $this->meta,
            'record' => $this->record,
        ]);
    }


    /**
     *
     */
    public function getNav($id = null)
    {

        if (!is_null($id)) {

            // dd($id);

        }

        $navmenu_name = 'cp-'.$this->module_name.'-subnav';

        $navmenu = Navmenu::byName($navmenu_name);

        return $navmenu;

    }
}