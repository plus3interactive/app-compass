<?php

namespace P3in\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use Auth;
use Gate;
use Event;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Modular;
use P3in\Controllers\ModularBaseController;
use P3in\Models\Navmenu;
use P3in\Models\Website;
use P3in\Module;
use P3in\Traits\HasRouteMetaTrait;

class UiBaseController extends ModularBaseController {
    use HasRouteMetaTrait;

    public $records;
    public $record;
    public $site_url;
    public $meta;
    public $template;

    public function output($data, $success = true, $message = '')
    {
        if (with(new Request)->capture()->wantsJson()) {
            return $this->json($data, $success, $message);
        }else{
            return view($this->template, $data);
        }
    }
    public function json($data, $success = true, $message = '')
    {
        $rtn = [
            'success' => $success,
            'data' => $data,
            'message' => $message,
        ];

        return response()->json($rtn);
    }

    public function build($type, $root = [])
    {

        if (Gate::denies($type, $this->meta->classname)) {

            abort(403);

        }

        $method = 'view'.$type;

        $this->setBaseUrl($root);

        if (!isset($this->meta->data_target)) {

            $this->setDataTarget($root);

        }

        return call_user_func([$this, $method]);
    }

    private function viewIndex()
    {
        $this->template = 'ui::index';

        return $this->output([
            'meta' => $this->meta,
            'records' => $this->records,
        ]);
    }

    private function viewCreate()
    {

        $this->template = 'ui::create';

        return $this->output([
            'meta' => $this->meta,
        ]);
    }

    private function viewShow()
    {
        $this->template = 'ui::show';

        return $this->output([
            'meta' => $this->meta,
            'record' => $this->record,
            'nav' => $this->getCpSubNav(),
            'left_panels' => $this->getLeftPanels(),
        ]);

    }

    private function viewEdit()
    {
        $this->template = 'ui::edit';

        return $this->output([
            'meta' => $this->meta,
            'record' => $this->record,
        ]);
    }

    /**
     *
     */
    public function getCpSubNav($id = null)
    {
        $menu = Cache::tags('cp_ui')->get('nav');

        return isset($menu->{$this->nav_name}) ? $menu->{$this->nav_name} : [];
    }

    /**
     *  SetBaseUrl
     *
     */
    public function setBaseUrl($root)
    {

        $this->meta->base_url = '/'.implode('/', $root);

        return $this->meta->base_url;

    }

    /**
     *
     */
    public function setDataTarget($root)
    {

        if (count($root) == 1 && !isset($this->meta->data_target)) {

            $this->meta->data_target = '#main-content-out';

        } else {

            $this->meta->data_target = '#record-detail';

        }

        return $this->meta->data_target;

    }

    /**
     *
     */
    public function getLeftPanels() {}


    /**
     *
     *
     *
     */
    public function setControllerDefaults()
    {

        if (Auth::check()) {

            $this->user = Auth::user();

        }

        if (isset($this->meta_install)) {
            Module::setClassConfig('ui', $this->controller_class, $this->meta_install);
        }

        $class_meta = Module::getClassConfig('ui', $this->controller_class);

        $this->meta = $class_meta ?: new \stdClass();
    }

    // this needs to be abstracted...
    public function requestMeta($url)
    {
        $rtn = [
            'success' => false,
            'data' => [],
            'message' => 'a url must be passed.',
        ];
        if ($url) {
            // default catch all.
            $parsedUrl = parse_url($url);
            $query = !empty($parsedUrl['query']) ? $parsedUrl['query'] : '';
            $uriAry = explode('/',trim($parsedUrl['path'],'/'));
            $target = $this->setDataTarget($uriAry);
            // now lets split the url up into the resources and it's params
            $resources = [];
            $params = [];
            $both = [&$resources, &$params];
            array_walk($uriAry, function($v, $k) use ($both) { $both[$k % 2][] = $v; });

            // get url's route controller name and method (aka the route action)
            $route = Route::getRoutes()->match(Request::create($parsedUrl['path']));
            $action = $route->getActionName();
            $name = $route->getName();

            list($class, $method) = explode('@', $action);

            $rtn['message'] = 'The url must have a defined route.';
            if ($class && $method) {

                // lets get the meta data for this controller.
                // $metaData = with(new $class)->meta;
                $metaData = \App::make($class)->meta;

                $rtn['message'] = 'The controller for this route needs target meta data.';
                $tree = [];
                if (!empty($metaData->$method) && !empty($metaData->$method->data_targets)) {
                    $rtn['success'] = true;
                    $rtn['message'] = '';
                    $rtn['data'] = $this->buildTree($tree, $metaData->$method->data_targets, $params, $query);
                }elseif($metaData = $this->getMeta($name)) {
                    if (!empty($metaData->data_targets)) {
                        $rtn['success'] = true;
                        $rtn['message'] = '';
                        $rtn['data'] = $this->buildTree($tree, $metaData->data_targets, $params, $query);
                    }
                }
            }

            if (!$rtn['success']) {
                $rtn['data'] = [
                    'url' => $parsedUrl['path'],
                    'target' => $target,
                ];
            }
        }

        return $rtn;
    }

    public function buildTree(&$tree, $data, $params, $query = '')
    {
        foreach ($data as $i => $row) {
            // lets find out how many params we are working with.
            $route = Route::getRoutes()->getByName($row->route);
            $matches = [];
            preg_match_all('/{\$?([_a-z][\w\.]+[\w])}/' , $route->uri(), $matches);

            // now lets get the url using the route and the params that apply to this route.
            $url =  route($row->route, array_slice($params, 0, count($matches[0])), false);
            $target = $row->target;

            unset($data[$i]);
            $queryStr = $query ? '?'.$query : '';
            $tree = [
                'url' => $url.$queryStr,
                'target' => $target,
                'next' => !empty($data) ? $this->buildTree($tree, $data, $params, $query) : [],
            ];
            break;
        }
        return $tree;
    }


}
