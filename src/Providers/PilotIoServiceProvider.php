<?php

namespace P3in\Providers;

use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use Intervention\Image\Facades\Image;
use Intervention\Image\ImageServiceProvider;
use P3in\Middleware\ValidateWebsite;
use P3in\Models\Field;
use P3in\Models\Gallery;
use P3in\Models\GalleryItem;
use P3in\Models\Group;
use P3in\Models\Menu;
use P3in\Models\Page;
use P3in\Models\PageSectionContent;
use P3in\Models\Permission;
use P3in\Models\Photo;
use P3in\Models\Redirect;
use P3in\Models\User;
use P3in\Models\Video;
use P3in\Models\Website;
use P3in\Observers\FieldObserver;
use P3in\Observers\GalleryItemObserver;
use P3in\Observers\PageObserver;
use Roumen\Sitemap\SitemapServiceProvider;
// use Roumen\Feed\FeedServiceProvider;
use Tymon\JWTAuth\Providers\LaravelServiceProvider;

class PilotIoServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->bindToRoute();
    }

    public function register()
    {
        \Log::info('Running CMS provider');

        $this->registerDependentPackages();

        $this->registerObservers();

        $this->bindInterfacesToRepos();

        $this->app['router']->middleware('validateWebsite', ValidateWebsite::class);

        // @TODO: currently a mix of views and stubs. should be better organized/split.
        $this->app['view']->addNamespace('cms', realpath(__DIR__.'/../Templates'));


    }

    /**
     * Load Intervention for images handling
     */
    private function registerDependentPackages()
    {
        $this->app->register(SitemapServiceProvider::class);
        // $this->app->register(FeedServiceProvider::class);
        $this->app->register(ImageServiceProvider::class);
        $this->app->register(LaravelServiceProvider::class);

        $loader = AliasLoader::getInstance();

        $loader->alias('Image', Image::class);

        //@TODO: we require the use of imagick, not sure we should force this though.
        Config::set(['image' => ['driver' => 'imagick']]);
    }

    private function registerObservers()
    {

        foreach ([
            Field::class => FieldObserver::class,
            // GalleryItem::class => GalleryItemObserver::class, // @TODO: "Cannot instantiate abstract class P3in\Models\GalleryItem" but we can register this on boot of that class.
            Photo::class => GalleryItemObserver::class,
            Video::class => GalleryItemObserver::class,
            // Photo::class => PhotoObserver::class, //@TODO: old but possibly needed for Alerts? look into it when we get to Alerts.
            Page::class => PageObserver::class,
        ] as $model => $obersver) {
            $model::observe($obersver);
        }

    }

    private function bindInterfacesToRepos()
    {
        $models = [
            'Users',
            'UserPermissions',
            'Permissions',
            'Groups',
            'UserGroups',
            'Galleries',
            'GalleryPhotos',
            'GalleryVideos',
            'Menus',
            'Websites',
            'WebsiteRedirects',
            'Pages',
            'WebsitePages',
            'PageContent',
            'WebsiteMenus'
        ];

        foreach ($models as $model) {
            $this->app->bind(
                '\\P3in\\Interfaces\\' . $model . 'RepositoryInterface', '\\P3in\\Repositories\\' . $model . 'Repository'
            );
        }

    }

    private function bindToRoute()
    {
        // @TODO: sort out Route::bind vs. Route::model.
        foreach ([
            'user' => User::class,
            'permission' => Permission::class,
            'group' => Group::class,
            'gallery' => Gallery::class,
            'photo' => Photo::class,
            'video' => Video::class,
            'website' => Website::class,
            'redirect' => Redirect::class,
            'page' => Page::class,
            'content' => PageSectionContent::class,
            'section' => Section::class,
            'menu' => Menu::class,
        ] as $key => $model) {
            Route::bind($key, function ($value) use ($model) {
                return $model::findOrFail($value);
            });

            Route::model($key, $model);
        }
    }
}