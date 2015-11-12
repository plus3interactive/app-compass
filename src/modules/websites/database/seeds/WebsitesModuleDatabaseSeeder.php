<?php

namespace P3in\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use P3in\Models\Navmenu;
use P3in\Models\Page;
use P3in\Models\Website;
use Modular;

class WebsitesModuleDatabaseSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

            $website = Website::firstOrNew([
                'site_name' => env('ADMIN_WEBSITE_NAME', 'CMS Admin CP'),
                'site_url' => env('ADMIN_WEBSITE_URL', 'cp.p3in.com'),
            ]);

            $website->config = [];

            $website->save();

        if (Modular::isLoaded('navigation') && Modular::isLoaded('pages')) {
            $website_subnav = Navmenu::byName('cp_websites_subnav');

            //
            //  SETTINGS
            //
            $page = Page::firstOrNew([
                'name' => 'cp_website_settings',
                'title' => 'Settings',
                'description' => 'Website Settings',
                'slug' => 'settings',
                'order' => 1,
                'active' => true,
                'parent' => null,
                'req_permission' => null,
                'website_id' => Website::admin()->id
            ]);

            $page->published_at = Carbon::now();

            $page->save();

            $website_subnav->addItem($page, 1, ['props' => ['link' => ['data-target' => '#record-detail']]]);

            //
            //  INFO
            //
            $page = Page::firstOrNew([
                'name' => 'cp_website_info',
                'title' => 'Info',
                'description' => 'Website Informations',
                'slug' => 'edit',
                'order' => 2,
                'active' => true,
                'parent' => null,
                'req_permission' => null,
                'website_id' => Website::admin()->id
            ]);

            $page->published_at = Carbon::now();

            $page->save();

            $website_subnav->addItem($page, 2, ['props' => ['link' => ['data-target' => '#record-detail']]]);

            //
            //  PAGES
            //
            $page = Page::firstOrNew([
                'name' => 'cp_website_pages',
                'title' => 'Pages',
                'description' => 'Page Info',
                'slug' => 'pages',
                'order' => 3,
                'active' => true,
                'parent' => null,
                'req_permission' => null,
                'website_id' => Website::admin()->id
            ]);

            $page->published_at = Carbon::now();

            $page->save();

            $website_subnav->addItem($page, 3);

        }

        Model::reguard();
    }
}
