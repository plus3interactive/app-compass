<?php

namespace P3in\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use P3in\Builders\FormBuilder;
use P3in\Builders\WebsiteBuilder;
use P3in\Models\FieldSource;
use P3in\Models\Section;

class WebsitesSeeder extends Seeder
{
    public function run()
    {
        // DB::statement('TRUNCATE websites RESTART IDENTITY CASCADE');
        // DB::statement('TRUNCATE pages RESTART IDENTITY CASCADE');

        $cp = WebsiteBuilder::new(env('ADMIN_WEBSITE_NAME'), env('ADMIN_WEBSITE_SCHEME'), env('ADMIN_WEBSITE_HOST'), function ($websiteBuilder) {

            $websiteBuilder->setStorage('cp_root');

            $users = $websiteBuilder->addPage('Users', 'users');
            $user_profile = $users->addChild('Profile', 'edit');
            $user_roles = $users->addChild('Roles', 'roles');

            $roles = $websiteBuilder->addPage('Roles', 'roles');
            $role_info = $roles->addChild('Info', 'edit');
            $role_permissions = $roles->addChild('Permissions', 'permissions');

            $permissions = $websiteBuilder->addPage('Permissions', 'permissions');
            $permission_info = $permissions->addChild('Info', 'edit');

            $resources = $websiteBuilder->addPage('Resources', 'resources');
            $resource_info = $resources->addChild('Info', 'edit');

            $websites = $websiteBuilder->addPage('Websites', 'websites');
            $website_info = $websites->addChild('Info', 'edit');
            $navigation = $websites->addChild('Navigation', 'menus');
            $pages = $websites->addChild('Pages', 'pages');
            $page_info = $pages->addChild('Info', 'edit');
            $page_layouts = $pages->addChild('Layout', 'layout');
            $page_contents = $pages->addChild('Content', 'contents');
            $blogEntries = $websites->addChild('Blog Entries', 'blog-entries');
            $blogCategories = $websites->addChild('Blog Categories', 'blog-categories');
            $blogTags = $websites->addChild('Blog Tags', 'blog-tags');

            $galleries = $websiteBuilder->addPage('Galleries', 'galleries');
            $gallery_info = $galleries->addChild('Info', 'edit');
            $gallery_photos = $galleries->addChild('Photos', 'photos');
            $gallery_videos = $galleries->addChild('Videos', 'videos');

            // @TODO: storage workflow needs to be looked at a bit.
            $storage = $websiteBuilder->addPage('Storage', 'storage');
            $storage_info = $storage->addChild('Info', 'edit');
            $storage_types = $storage->addChild('Types', 'storage-types');

            $forms = $websiteBuilder->addPage('Forms', 'forms');
            $form_info = $forms->addChild('Info', 'edit');
            // @TODO: form submissions?

            $websiteBuilder->addMenu('main_nav')
                ->add(['title' => 'Dashboard', 'url' => '/', 'alt' => 'dashboard'], 0)
                ->add(['title' => 'Users Management', 'alt' => 'Users Management'], 1)->sub()
                    ->add($users, 1)->icon('user')->sub()
                        ->add($user_profile, 1)->icon('user')
                        ->add($user_roles, 2)->icon('group')
                        ->parent()
                    ->add($roles, 2)->icon('group')->sub()
                        ->add($role_info, 1)->icon('group')
                        ->add($role_permissions, 2)->icon('permission')
                        ->parent()
                    ->add($permissions, 3)->icon('permission')->sub()
                        ->add($permission_info, 1)->icon('permission')
                        ->parent()
                    ->add($resources, 4)->icon('diamond')->sub()
                        ->add($resource_info, 1)->icon('diamond')
                        ->parent()
                    ->parent()
                ->add(['title' => 'Web Properties', 'alt' => 'Web Properties'], 2)->sub()
                    ->add($websites, 1)->icon('globe')->sub()
                        ->add($website_info, 1)->icon('edit')
                        ->add($pages, 2)->icon('pages')->sub()
                            ->add($page_info, 1)
                            ->add($page_layouts, 2)
                            ->add($page_contents, 3)
                            ->parent()
                        // @TODO: blog end point flow needs to be worked out
                        ->add(['url' => '/blog', 'title' => 'Blog', 'alt' => 'Blog'], 3)->icon('page') ->sub()
                            ->add($blogEntries, 1)
                            ->add($blogCategories, 2)
                            ->add($blogTags, 3)
                            ->parent()
                        ->add($navigation, 4)->icon('navigation')
                        ->parent()
                    ->parent()
                ->add(['title' => 'Media Management', 'alt' => 'Media Management'], 3)->sub()
                    ->add($galleries, 1)->icon('camera')->sub()
                            ->add($gallery_info, 1)->icon('gallery')
                            ->add($gallery_photos, 2)->icon('image')
                            ->add($gallery_videos, 3)->icon('video')
                        ->parent()
                    ->parent()
                ->add(['title' => 'Settings', 'alt' => 'Settings'], 4)->sub()
                    ->add($storage, 1)->icon('gear')->sub()
                        ->add($storage_info, 1)->icon('gear')
                        ->add($storage_types, 2)->icon('gear')
                        ->parent()
                    ->add($forms, 2)->icon('file-text-o')->sub()
                        ->add($form_info, 1)->icon('file-text-o')
                        ;
        })->getWebsite();

        // DB::statement("DELETE FROM forms WHERE name = 'websites'");

        $form = FormBuilder::new('websites', function (FormBuilder $builder) {
            // $builder->setViewTypes(['list','grid']);
            $builder->string('Website Name', 'name')
                ->list()
                ->required()
                ->sortable()
                ->searchable()
                ->help('The Human Readable website name');
            $builder->select('Scheme', 'scheme')
                ->list()
                ->required()
                ->sortable()
                ->searchable()
                ->dynamic([
                    ['index' => 'http', 'label' => 'Plain (HTTP)'],
                    ['index' => 'https', 'label' => 'Secure (HTTPS)']
                ])
                ->help('Website Schema. We recommend website to be served using HTTPS');
            $builder->string('Host', 'host')
                ->list()
                ->required()
                ->sortable()
                ->searchable()
                ->help('Just the fully qualified hostname (FQDN)');

            $builder->fieldset('Configuration', 'config', function(FormBuilder $builder) {
                $builder->select('Header', 'header')
                    ->dynamic(Section::class, function(FieldSource $source) {
                        $source->where('type', 'header');
                        $source->select(['id AS index', 'name AS label']);
                    })
                    ->required()
                    ->help('Please select a Header');
                $builder->select('Footer', 'footer')
                    ->dynamic(Section::class, function(FieldSource $source) {
                        $source->where('type', 'footer');
                        $source->select(['id AS index', 'name AS label']);
                    })
                    ->required()
                    ->help('Please select a Footer');
                $builder->code('Layouts', 'layouts')
                    ->dynamic(['public', 'errors']);
                $builder->fieldset('Deployment', 'deployment', function (FormBuilder $depBuilder) {
                    $depBuilder->string('Publish From Path', 'publish_from')
                        ->required();
                });
            });

            // @NOTE another valid approach is
            // $builder->string('Title', 'config.meta.title')

            $builder->fieldset('Meta Data', 'config.meta', function(FormBuilder $builder) {
                $builder->string('Title', 'title')
                    ->required()
                    ->help('The title of the website as it apears in header');
                $builder->text('Description', 'description')
                    ->required()
                    ->help('The website desscription');
                $builder->text('Keywords', 'keywords')
                    ->help('The website keywords, though this is no longer used by many Search Engines');
                $builder->code('Custom Header HTML', 'custom_header_html')
                    ->help('Custom header HTML, CSS, JS');
                $builder->code('Custom Before Body End HTML', 'custom_before_body_end_html')
                    ->help('HTML, CSS, JS you may need to inject before the closing </body> tag on all pages.');
                $builder->code('Custom Footer HTML', 'custom_footer_html')
                    ->help('Custom footer HTML, CSS, JS');
                $builder->text('Robots.txt Contents', 'robots_txt')
                    ->help('The Contents of the robots.txt file for search engines.');
                $builder->string('Facebook Url', 'facebook_url')
                    ->required()
                    ->help('The title of the website as it apears in header');
                $builder->string('Instagram Url', 'instagram_url')
                    ->required()
                    ->help('The title of the website as it apears in header');
                $builder->string('Twitter Url', 'twitter_url')
                    ->required()
                    ->help('The title of the website as it apears in header');
                $builder->string('Google Plus Url', 'google_plus_url')
                    ->required()
                    ->help('The title of the website as it apears in header');
                $builder->string('LinkedIn Url', 'linkedin_url')
                    ->required()
                    ->help('The title of the website as it apears in header');
                $builder->config('Addtional Header Tags', 'custom')
                    // ->dynamic(['title', 'description', 'keywords'])
                    ->help('Additional meta tags to be added.');
            });
        })->linkToResources(['websites.index', 'websites.show', 'websites.create', 'websites.store', 'websites.update'])
        ->getForm();

        WebsiteBuilder::edit($cp->id)->linkForm($form);

        // DB::statement("DELETE FROM forms WHERE name = 'pages'");

        $form = FormBuilder::new('pages', function (FormBuilder $builder) {
            $builder->editor('Page');
            // $builder->setViewTypes(['list','grid']);
            $builder->string('Page Title', 'title')
                ->list()
                ->required()
                ->sortable()
                ->searchable();
            $builder->string('Slug', 'slug')
                ->list(false)
                ->required();
            $builder->select('Parent', 'parent_id')->list(false)
                ->dynamic(\P3in\Models\Page::class, function(FieldSource $source) {
                    $source->limit(4);
                    $source->where('website_id', \P3in\Models\Website::whereHost(env('ADMIN_WEBSITE_HOST'))->first()->id);
                    $source->select(['id AS index', 'title AS label']);
                });
        })->linkToResources(['pages.show', 'websites.pages.index', 'websites.pages.create', 'websites.pages.show'])
            ->getForm();

        WebsiteBuilder::edit($cp->id)->linkForm($form);

        // DB::statement("DELETE FROM forms WHERE name = 'menus'");

        FormBuilder::new('menus', function (FormBuilder $builder) {
            $builder->string('Name', 'name')->list()->required()->sortable()->searchable();
        })->linkToResources(['websites.menus.index', 'websites.menus.create']);

        // DB::statement("DELETE FROM forms WHERE name = 'menus-editor'");

        FormBuilder::new('menus-editor', function (FormBuilder $builder) {
            $builder->editor('Menu');
            // $builder->menuEditor('Menu', 'menu')->list(false);
        })->linkToResources(['websites.menus.show']);

        // DB::statement("DELETE FROM forms WHERE name = 'create-link'");

        FormBuilder::new('create-link', function (FormBuilder $builder) {
            $builder->string('Label', 'title');
            $builder->string('Url', 'url');
            $builder->string('Alt', 'alt');
            $builder->string('Icon', 'icon');
            $builder->boolean('New Tab', 'new_tab');
            $builder->boolean('Clickable', 'clickable');
            $builder->wysiwyg('Content', 'content');
        });

        // DB::statement("DELETE FROM forms WHERE name = 'edit-menu-item'");

        $form = FormBuilder::new('edit-menu-item', function (FormBuilder $builder) {
            $builder->string('Label', 'title');
            $builder->string('Alt', 'alt');
            $builder->string('Icon', 'icon');
            $builder->select('Permission Required', 'req_perm')->dynamic(\P3in\Models\Permission::class, function(FieldSource $source) {
                $source->select(['id AS index', 'label']);
            });
            $builder->boolean('New Tab', 'new_tab');
            $builder->boolean('Clickable', 'clickable');
        })->getForm();

        WebsiteBuilder::edit($cp->id)->linkForm($form);

        // DB::statement("DELETE FROM forms WHERE name = 'edit-link'");

        $form = FormBuilder::new('edit-link', function (FormBuilder $builder) {
            $builder->string('Label', 'title');
            $builder->string('Url', 'url');
            $builder->string('Alt', 'alt');
            $builder->string('Icon', 'icon');
            $builder->select('Permission Required', 'req_perm')->dynamic(\P3in\Models\Permission::class, function(FieldSource $source) {
                $source->select(['id AS index', 'label']);
            });
            $builder->boolean('New Tab', 'new_tab');
            $builder->boolean('Clickable', 'clickable');
            $builder->wysiwyg('Content', 'content');
        })->getForm();

        WebsiteBuilder::edit($cp->id)->linkForm($form);

        // DB::statement("DELETE FROM forms WHERE name = 'storage'");

        Formbuilder::new('storage', function(FormBuilder $builder) {
            $builder->string('Name', 'name')
                ->list()
                ->sortable()
                ->searchable()
                ->required();
            $builder->string('Type', 'type.name')
                ->list()
                ->edit(false)->sortable()
                ->searchable()
                ->required();
            $builder->select('Disk Instance', 'type_id')
                ->list(false)
                ->dynamic(\P3in\Models\StorageType::class, function(FieldSource $source) {
                    $source->select(['id AS index', 'name AS label']);
                })->required();
            // @TODO this is one way, but validation has issues (too long to explain here)
            // $builder->string('Root', 'config.root')->list()->sortable()->searchable()->required();
            $builder->fieldset('Configuration', 'config', function(FormBuilder $builder) {
                $builder->string('Root', 'root')
                    ->list()
                    ->sortable()
                    ->searchable()
                    ->required();
            })->list(false)->required();

        })->linkToResources(['storage.index', 'storage.show', 'storage.create', 'storage.store', 'storage.update']);

        FormBuilder::new('resources', function(FormBuilder $builder) {
            $builder->string('Resource', 'resource')->list()->sortable()->searchable()->required();
            $builder->string('Created', 'created_at')->list()->edit(false);
            $builder->select('Role required', 'req_role')->dynamic(\P3in\Models\Role::class, function(FieldSource $source) {
                $source->select(['id As index', 'label']);
            })->nullable();
        })->linkToResources(['resources.index', 'resources.show', 'resources.create']);

        FormBuilder::new('forms', function(FormBuilder $builder) {
            $builder->string('Name', 'name')->list(true)->sortable()->searchable();
            $builder->string('Editor', 'editor');
            $builder->string('Fields', 'fieldsCount')->edit(false)->list();
            $builder->string('Created', 'created_at')->edit(false);
            $builder->string('Ureated', 'updated_at')->edit(false);
        })->linkToResources(['forms.index', 'forms.show']);
    }
}
