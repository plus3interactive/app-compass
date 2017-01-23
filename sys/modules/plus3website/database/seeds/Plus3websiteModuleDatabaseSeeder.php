<?php

namespace P3in\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use P3in\Builders\FormBuilder;
use P3in\Builders\WebsiteBuilder;
use P3in\Models\Component;
// use P3in\Models\Fieldtype;
use P3in\Models\Plus3Person;
use P3in\Models\User;
use P3in\Models\Website;
use P3in\Renderers\PageRenderer;

class Plus3websiteModuleDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement("TRUNCATE TABLE plus3_people RESTART IDENTITY CASCADE");
        DB::statement("TRUNCATE TABLE users RESTART IDENTITY CASCADE");
        DB::statement("TRUNCATE TABLE components RESTART IDENTITY CASCADE");

        // Imene
        $imene = User::create([
            'first_name' => 'Imene',
            'last_name' => 'Saidi',
            'email' => 'imene.saidi@p3in.com',
            'phone' => '617-470-6003',
            'password' => 'd3velopment',
            'active' => true,
        ]);

        (new Plus3Person([
            'title' => 'Co-Founder and CEO',
            'meta_keywords' => 'words',
            'meta_description' => 'desc',
            'bio' => 'imene\'s Bio',
            'instagram' => 'imenebsaidi',
            'twitter' => 'imenesaidi',
            'facebook' => 'Imene.Saidi',
            'linkedin' => 'imenesaidi',
        ]))
            ->user()
            ->associate($imene)
            ->save();

        // Jubair
        $jubair = User::create([
            'first_name' => 'Jubair',
            'last_name' => 'Saidi',
            'email' => 'jubair.saidi@p3in.com',
            'phone' => '617-755-7012',
            'password' => 'd3velopment',
            'active' => true,
        ]);

        (new Plus3Person([
            'title' => 'Co-Founder and CTO',
            'meta_keywords' => 'words',
            'meta_description' => 'desc',
            'bio' => 'Jubair\'s Bio',
            'instagram' => 'jubairsaidi',
            'twitter' => 'jubairsaidi',
            'facebook' => 'jubairsaidi',
            'linkedin' => 'jsaidi',
        ]))
            ->user()
            ->associate($jubair)
            ->save();

        // Aisha
        $aisha = User::create([
            'first_name' => 'Aisha',
            'last_name' => 'Saidi',
            'email' => 'aisha.saidi@p3in.com',
            'phone' => '',
            'password' => 'd3velopment',
            'active' => true,
        ]);

        (new Plus3Person([
            'title' => 'Information Architect',
            'meta_keywords' => 'words',
            'meta_description' => 'desc',
            'bio' => 'aisha\'s Bio',
            'instagram' => '',
            'twitter' => '',
            'facebook' => '',
            'linkedin' => '',
        ]))
            ->user()
            ->associate($aisha)
            ->save();

        // Federico
        $federico = User::create([
            'first_name' => 'Federico',
            'last_name' => 'Francescato',
            'email' => 'federico@p3in.com',
            'phone' => '',
            'password' => 'd3velopment',
            'active' => true,
        ]);

        (new Plus3Person([
            'title' => 'Application Developer',
            'meta_keywords' => 'words',
            'meta_description' => 'desc',
            'bio' => 'federico\'s Bio',
            'instagram' => '',
            'twitter' => '',
            'facebook' => '',
            'linkedin' => '',
        ]))
            ->user()
            ->associate($federico)
            ->save();

        // Lazarus
        $lazarus = User::create([
            'first_name' => 'Lazarus',
            'last_name' => 'Morrison',
            'email' => 'lazarus@p3in.com',
            'phone' => '',
            'password' => 'd3velopment',
            'active' => true,
        ]);

        (new Plus3Person([
            'title' => 'Web Developer',
            'meta_keywords' => 'words',
            'meta_description' => 'desc',
            'bio' => 'lazarus\'s Bio',
            'instagram' => '',
            'twitter' => '',
            'facebook' => '',
            'linkedin' => '',
        ]))
            ->user()
            ->associate($lazarus)
            ->save();

        // Website Builder API Design

        // field type ->link() allows the admin to select page || select link || create new link.
        // field validation should match exactly: https://laravel.com/docs/5.3/validation#available-validation-rules

        DB::table('websites')->where('host', 'www.plus3interactive.com')->delete();
        DB::table('forms')->whereIn('name', [
            'SliderBanner',
            'SectionHeading',
            'BoxCallouts',
            'OurProcess',
            'MeetOurTeam',
            'SocialStream',
            'CustomerTestimonials',
            'ThickPageBanner',
            'WhiteBreakCalloutSectionLinks',
            'ProvidedSolution',
            'BlueBreakCallout',
            'BreadCrumbRightSideLink',
            'ProcessTimeline',
            'MaintenanceDetails',
            'ProjectList',
            'ContactUs',
            'MapAddress',
            'CustomerLogin',
        ])->delete();

        $website = WebsiteBuilder::new('Plus 3 Interactive, LLC', 'https', 'www.plus3interactive.com', function ($websiteBuilder) {
            $websiteBuilder
                ->setTemplateBasePath(realpath(__DIR__.'/../../Public/js/components/Site'))
                ->setHeader('SiteHeader.vue')
                ->setFooter('SiteFooter.vue')
                ->setMetaData([
                    'title' => '',
                    'description' => '',
                    'keywords' => '',
                    'custom_header_html' => '',
                    'custom_before_body_end_html' => '',
                    'custom_footer_html' => '',
                    'robots_txt' => '',
                ]);
            // Build the components.

            // This one should prob be build with websites module load.
            Component::create([
                'name' => 'Container',
                'template' => 'container.vue', //not sure about this, do containers need templates? I feel there are good arguments for both yes and no.
                'type' => 'container',
            ]);

            $slider_banner = Component::create([
                'name' => 'Slider Banner',
                'template' => 'SliderBanner.vue',
                'type' => 'section'
            ]);

            FormBuilder::new('SliderBanner', function ($fb) {
                // we need to figure out how to handle the 'type' field.
                // the fields internally are created in the order they appear in the builder.
                $fb->string('Title', 'title', ['required']);
                $fb->fieldset('Slides', 'slides', [], function ($slide) {
                    // not field type, sub section builder.
                    $slide->file('Banner Image', 'banner_image', Photo::class, ['required']);
                    $slide->string('Title', 'title', ['required']);
                    $slide->wysiwyg('Description', 'description', ['required']);
                    $slide->string('Link Text', 'link_text', ['required']);
                    $slide->link('Link Destination', 'link_href', ['required']);
                })->repeatable();
            })->setOwner($slider_banner);

            $section_heading = Component::create([
                'name' => 'Section Heading',
                'template' => 'SectionHeading.vue',
                'type' => 'section'
            ]);

            FormBuilder::new('SectionHeading', function ($fb) {
                $fb->string('Title', 'title', ['required']);
                $fb->wysiwyg('Description', 'description', ['required']);
            })->setOwner($section_heading);

            $box_callouts = Component::create([
                'name' => 'Box Callouts',
                'template' => 'BoxCallouts.vue',
                'type' => 'section'
            ]);

            FormBuilder::new('BoxCallouts', function ($fb) {
                $fb->fieldset('Boxes', 'boxes', [], function ($box) {
                    $box->string('Title', 'title', ['required']);
                    $box->string('Points', 'points', [])->repeatable();
                    $box->string('Link Text', 'link_text', ['required']);
                    $box->link('Link Destination', 'link_href', ['required']);
                })->repeatable();
            })->setOwner($box_callouts);

            $our_proccess = Component::create([
                'name' => 'Our Process',
                'template' => 'OurProcess.vue',
                'type' => 'section'
            ]);

            FormBuilder::new('OurProcess', function ($fb) {
                $fb->string('Title', 'title', ['required']);
                $fb->wysiwyg('Description', 'description', ['required']);
                // SVG Animation is static, editable in code only.
            })->setOwner($our_proccess);

            $meet_our_team = Component::create([
                'name' => 'Meet Our Team',
                'template' => 'MeetOurTeam.vue',
                'type' => 'section'
            ]);

            FormBuilder::new('MeetOurTeam', function ($fb) {
                $fb->string('Title', 'title', ['required']);
                $fb->wysiwyg('Description', 'description', ['required']);
            })->setOwner($meet_our_team)
            // ->dynamic(Plus3Person::class) // we need to decide if the section is dynamic, or the field (or both can be)
            ;

            $social_stream = Component::create([
                'name' => 'Social Stream',
                'template' => 'SocialStream.vue',
                'type' => 'section'
            ]);

            FormBuilder::new('SocialStream', function ($fb) {
                $fb->string('Title', 'title', ['required']);
                $fb->wysiwyg('Description', 'description', ['required']);
                // Fields
            })->setOwner($social_stream);

            $customer_testimonials = Component::create([
                'name' => 'Customer Testimonials',
                'template' => 'CustomerTestimonials.vue',
                'type' => 'section'
            ]);

            FormBuilder::new('CustomerTestimonials', function ($fb) {
                $fb->fieldset('Testimonials', 'testimonials', [], function ($testimonial) {
                    $testimonial->string('Author', 'author', ['required'])->required();
                    $testimonial->wysiwyg('Content', 'content', ['required'])->required();

                    //BEGIN DUMMY: these below are a dummy set to test nesting fieldsets.
                    $testimonial->fieldset('Testimonials', 'testimonials', [], function ($lvl3) {
                        $lvl3->string('Author', 'author', ['required']);
                        $lvl3->wysiwyg('Content', 'content', ['required']);
                        $lvl3->fieldset('Testimonials', 'testimonials', [], function ($lvl4) {
                            $lvl4->string('Author', 'author', ['required']);
                            $lvl4->wysiwyg('Content', 'content', ['required']);
                        })->repeatable();
                    })->repeatable();
                    // END DUMMY:
                })->repeatable();
            })->setOwner($customer_testimonials);

            $thick_page_banner = Component::create([
                'name' => 'Thick Page Banner',
                'template' => 'ThickPageBanner.vue',
                'type' => 'section'
            ]);

            FormBuilder::new('ThickPageBanner', function ($fb) {
                $fb->file('Background Image', 'background_image', []);
                $fb->string('Title', 'title', ['required']);
                $fb->wysiwyg('Description', 'description', ['required']);
            })->setOwner($thick_page_banner);

            $white_break_w_section_links = Component::create([
                'name' => 'White Break Callout Section Links',
                'template' => 'WhiteBreakCalloutSectionLinks.vue',
                'type' => 'section'
            ]);

            FormBuilder::new('WhiteBreakCalloutSectionLinks', function ($fb) {
                $fb->string('Title', 'title', ['required']);
                $fb->wysiwyg('Description', 'description', ['required']);
                $fb->fieldset('Page Section Quick Links', 'quick_links', [], function ($quickLinks) {
                    $quickLinks->radio('Link Format', 'link_format', [
                        'ol' => 'Ordered List',
                        'ul' => 'Un Ordered List',
                        'arrow' => 'Link with Arrow',
                    ])->required();
                    $quickLinks->pageSectionSelect('Page Section Quick Links', 'quick_links', [])->repeatable();
                });
            })->setOwner($white_break_w_section_links);

            $provided_solution = Component::create([
                'name' => 'Provided Solution',
                'template' => 'ProvidedSolution.vue',
                'type' => 'section'
            ]);

            FormBuilder::new('ProvidedSolution', function ($fb) {
                $fb->fieldset('Solution', 'solution', [], function ($solution) {
                    $solution->radio('Layout', 'layout', ['left' => 'Left', 'right' => 'Right'])->required();
                    $solution->string('Title', 'title', ['required']);
                    $solution->file('Solution Photo', 'solution_photo', []);
                    $solution->wysiwyg('Description', 'description', ['required'])->required();
                    $solution->pageSectionSelect('Projects Using Solution', 'projects_using_solution', [])->repeatable();
                    $solution->text('Link Description', 'link_description', [])->required();
                    $solution->string('Link Title', 'link_title', [])->required();
                    $solution->link('Link Destination', 'link_href', [])->required();
                })->repeatable();
            })->setOwner($provided_solution);

            $blue_break_callout = Component::create([
                'name' => 'Blue Break Callout',
                'template' => 'BlueBreakCallout.vue',
                'type' => 'section'
            ]);

            FormBuilder::new('BlueBreakCallout', function ($fb) {
                $fb->string('Link Title', 'link_title', [])->required();
                $fb->link('Link Destination', 'link_href', [])->required();
            })->setOwner($blue_break_callout);

            $breadcrumb_with_right_link = Component::create([
                'name' => 'BreadCrumb With Right Side Link',
                'template' => 'BreadCrumbRightSideLink.vue',
                'type' => 'section'
            ]);

            FormBuilder::new('BreadCrumbRightSideLink', function ($fb) {
                $fb->string('Link Title', 'link_title', [])->required();
                $fb->link('Link Destination', 'link_href', [])->required();
            })->setOwner($breadcrumb_with_right_link);

            $process_timeline = Component::create([
                'name' => 'Process Timeline',
                'template' => 'ProcessTimeline.vue',
                'type' => 'section'
            ]);

            FormBuilder::new('ProcessTimeline', function ($fb) {
                $fb->string('Title', 'title', ['required']);
                $fb->wysiwyg('Description', 'description', ['required']);
                $fb->fieldset('Process Steps', 'process_steps', [], function ($process) {
                    $process->file('Image File', 'image', ['type:svg']);
                    $process->string('Image width', 'image_width', []);
                    $process->string('Image Height', 'image_height', []);
                    $process->string('Title', 'title', ['required']);
                    $process->wysiwyg('Description', 'description', ['required']);
                })->repeatable();
            })->setOwner($process_timeline);

            $process_maintenance_details = Component::create([
                'name' => 'Maintenance Details',
                'template' => 'MaintenanceDetails.vue',
                'type' => 'section'
            ]);

            FormBuilder::new('MaintenanceDetails', function ($fb) {
                $fb->string('Title', 'title', ['required']);
                $fb->wysiwyg('Description', 'description', ['required']);
                $fb->file('Image File', 'image', ['type:svg']);
                $fb->string('Image width', 'image_width', []);
                $fb->string('Image Height', 'image_height', []);
                $fb->string('Link Title', 'link_title', [])->required();
                $fb->link('Link Destination', 'link_href', [])->required();
            })->setOwner($process_maintenance_details);

            $project_list = Component::create([
                'name' => 'Project List',
                'template' => 'ProjectList.vue',
                'type' => 'section'
            ]);

            FormBuilder::new('ProjectList', function ($fb) {
                $fb->fieldset('Projects', 'projects', [], function ($project) {
                    $project->file('Background Image', 'background_image', ['required']);
                    $project->file('Logo', 'logo', ['required']);
                    $project->string('Name', 'name', ['required']);
                    $project->string('Business Area', 'business_area', ['required']);
                    $project->wysiwyg('Description', 'description', ['required']);
                    $project->pageSectionSelect('Page Section Quick Links', 'quick_links', [])->repeatable();
                    $project->boolean('Highlighted', 'highlighted', ['required']);
                })->repeatable();
            })->setOwner($project_list);

            $contact_form = Component::create([
                'name' => 'Contact Us',
                'template' => 'ContactUs.vue',
                'type' => 'section'
            ]);

            FormBuilder::new('ContactUs', function ($fb) {
                $fb->formBuilder('Contact Form', 'contact_form', []);
            })->setOwner($contact_form);

            $map_address = Component::create([
                'name' => 'Map Address',
                'template' => 'MapAddress.vue',
                'type' => 'section'
            ]);

            FormBuilder::new('MapAddress', function ($fb) {
                $fb->string('Title', 'title', []);
                $fb->string('Phone Number', 'phone', []);
                $fb->string('Address Line 1', 'address_1', []);
                $fb->string('Address Line 2', 'address_2', []);
                $fb->string('City', 'city', []);
                $fb->string('State', 'state', []);
                $fb->string('Zip', 'zip', []);
                $fb->map('Map', 'map', ['address_1', 'address_2', 'city', 'state', 'zip']);
            })->setOwner($map_address);

            // We might want to consider having this section be dynamic rather than set a field to be the dynamic piece?
            // Fields: Email, Password, Remember Me
            // Links: Forgot Password
            $login_form = Component::create([
                'name' => 'Customer Login',
                'template' => 'CustomerLogin.vue',
                'type' => 'section'
            ]);

            FormBuilder::new('CustomerLogin', function ($fb) {
                $fb->loginForm('Customer Login', 'customer_login', []);
            })->setOwner($login_form);

            // we should always (but only(?)) have one container component.
            // prob something we seed when loading websites module.



            // container->addSection
            // section->addContent

            // Build Pages
            $homepage = $websiteBuilder
                ->addPage('Home Page', '')
                ->setAuthor('Aisha Saidi')
                ->setDescription('This is where we would put the Plus 3 Interactive description')
                ->setPriority('1.0')
                ->setUpdatedFrequency('always');

            $homepage
                ->addContainer(12, 1)
                ->addSection($slider_banner, 12, 1);

            $home_box_callout_container = $homepage
                ->addContainer(12, 2)
                ->addSection($section_heading, 12, 1);

            $home_box_callout_container
                ->addContainer(6, 2)
                ->addSection($box_callouts, 6, 1);

            $home_box_callout_container
                ->addContainer(6, 3)
                ->addSection($box_callouts, 6, 1);

            $homepage
                ->addContainer(12, 3)
                ->addSection($our_proccess, 12, 4)
                ->addSection($meet_our_team, 12, 5)
                ->addSection($social_stream, 12, 6)
                ->addSection($customer_testimonials, 12, 7);

            $solutions = $websiteBuilder
                ->addPage('Solutions', 'solutions')
                ->setAuthor('Aisha Saidi')
                ->setDescription('This is where we would put the Plus 3 Interactive description')
                ->setPriority('0.9')
                ->setUpdatedFrequency('yearly');

            $solutions
                ->addContainer(12, 1)
                ->addSection($thick_page_banner, 12, 1)
                ->addSection($white_break_w_section_links, 12, 2)
                ->addSection($provided_solution, 12, 3)
                ->addSection($blue_break_callout, 12, 4);

            $process = $solutions
                ->addChild('Our Process', 'our-process')
                ->setAuthor('Aisha Saidi')
                ->setDescription('This is where we would put the Plus 3 Interactive description')
                ->setPriority('0.8')
                ->setUpdatedFrequency('yearly');

            $process
                ->addContainer(12, 1)
                ->addSection($thick_page_banner, 12, 1)
                ->addSection($breadcrumb_with_right_link, 12, 2)
                ->addSection($process_timeline, 12, 3)
                ->addSection($process_maintenance_details, 12, 4);

            $projects = $websiteBuilder
                ->addPage('Projects', 'projects')
                ->setAuthor('Aisha Saidi')
                ->setDescription('This is where we would put the Plus 3 Interactive description')
                ->setPriority('0.9')
                ->setUpdatedFrequency('monthly');

            $projects
                ->addContainer(12, 1)
                ->addSection($thick_page_banner, 12, 1)
                ->addSection($project_list, 12, 2)
                ->addSection($blue_break_callout, 12, 3)
                ->addSection($white_break_w_section_links, 12, 4);

            $company = $websiteBuilder
                ->addPage('Company', 'company')
                ->setAuthor('Aisha Saidi')
                ->setDescription('This is where we would put the Plus 3 Interactive description')
                ->setPriority('0.8')
                ->setUpdatedFrequency('monthly');

            $company
                ->addContainer(12, 1)
                ->addSection($thick_page_banner, 12, 1)
                ->addSection($meet_our_team, 12, 2)
                ->addSection($social_stream, 12, 3);

            $contact = $websiteBuilder
                ->addPage('Contact Us', 'contact')
                ->setAuthor('Aisha Saidi')
                ->setDescription('This is where we would put the Plus 3 Interactive description')
                ->setPriority('0.7')
                ->setUpdatedFrequency('yearly');

            $contact
                ->addContainer(12, 1)
                ->addSection($thick_page_banner, 12, 1)
                ->addSection($contact_form, 12, 2)
                ->addSection($map_address, 12, 3);

            $login = $websiteBuilder
                ->addPage('Customer Login', 'customer-login')
                ->setAuthor('Aisha Saidi')
                ->setDescription('This is where we would put the Plus 3 Interactive description')
                ->setPriority('0.6')
                ->setUpdatedFrequency('never');

            $login
                ->addContainer(12, 1)
                ->addSection($thick_page_banner, 12, 1)
                ->addSection($login_form, 12, 2);

            // Create the nav menus and add the items.
            $main_header_menu = $websiteBuilder->addMenu('main_header_menu');

            $solutions_item = $main_header_menu->addItem($solutions, 1);
            $solutions_item->addItem($process, 1);

            $main_header_menu->addItem($projects, 2);
            $main_header_menu->addItem($company, 3);
            $main_header_menu->addItem($contact, 4);
            $main_header_menu->addItem($login, 5);

            $main_footer_menu = $websiteBuilder->addMenu('main_footer_menu');
            $main_footer_menu->addItem($solutions, 1);
            $main_footer_menu->addItem($process, 2);
            $main_footer_menu->addItem($projects, 3);
            $main_footer_menu->addItem($company, 4);
            $main_footer_menu->addItem($contact, 5);
            $main_footer_menu->addItem($login, 6);
        })->getWebsite();

        // // Now lets test the magic!
        // DB::enableQueryLog();
        // $website_for_renderer = Website::find($website->id);
        // $renderer =  new PageRenderer($website_for_renderer);

        // $data = $renderer->setPage('/solutions/our-process')->render(); // edit() for CP, render() for public.

        // dd(DB::getQueryLog(), $data);

/*

 */
    }
}
