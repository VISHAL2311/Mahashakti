<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;
use Carbon\Carbon;

class ModuleTableSeeder extends Seeder
{
		public function run()
		{

				DB::table('module')->insert([
					'varTitle' => 'Front Home',
					'varModuleName' =>  'home',
					'varTableName' => '',
					'varModelName' => '',
					'varModuleClass' => 'HomeController',
                                        'varModuleNameSpace'=>'',
					'intDisplayOrder' => 1,
					'chrIsFront' => 'N',
					'chrIsPowerpanel' => 'N',
					'chrIsGenerated' => 'N',
 					'decVersion' => 1.0,
					'chrPublish' => 'Y',
					'chrDelete' => 'N',
					'varPermissions'=>'',
					'created_at'=> Carbon::now(),
					'updated_at'=> Carbon::now()
				]);
				
									
                                                                                    DB::table('module')->insert([
                                            'intFkGroupCode' => '2',
						'varTitle' => 'Banners',
						'varModuleName' =>  'banners',
						'varTableName' => 'banner',
						'varModelName' => 'Banner',
						'varModuleClass' => 'BannerController',
						'varModuleNameSpace' => 'Powerpanel\Banner\\',
						'intDisplayOrder' => 0,
						'chrIsFront' => 'N',
						'chrIsPowerpanel' => 'Y',
						'chrIsGenerated' => 'N',
						'decVersion' => 1.0,
						'chrPublish' => 'Y',
						'chrDelete' => 'N',
						'varPermissions'=> 'list, create, edit, delete, publish,reviewchanges',
						'created_at'=> Carbon::now(),
						'updated_at'=> Carbon::now()
					]);
                                        					
                                        
					
						
                                                                                    DB::table('module')->insert([
                                            'intFkGroupCode' => '2',
						'varTitle' => 'Menu',
						'varModuleName' =>  'menu',
						'varTableName' => 'menu',
						'varModelName' => 'Menu',
						'varModuleClass' => 'MenuController',
						'varModuleNameSpace' => 'Powerpanel\Menu\\',
						'intDisplayOrder' => 0,
						'chrIsFront' => 'N',
						'chrIsPowerpanel' => 'Y',
						'chrIsGenerated' => 'N',
						'decVersion' => 3.0,
						'chrPublish' => 'Y',
						'chrDelete' => 'N',
						'varPermissions'=> 'list, create, edit, delete',
						'created_at'=> Carbon::now(),
						'updated_at'=> Carbon::now()
					]);
                                        					
                                        
					
						
                                                                                    DB::table('module')->insert([
                                            'intFkGroupCode' => '2',
						'varTitle' => 'Pages',
						'varModuleName' =>  'pages',
						'varTableName' => 'cms_page',
						'varModelName' => 'CmsPage',
						'varModuleClass' => 'CmsPagesController',
						'varModuleNameSpace' => 'Powerpanel\CmsPage\\',
						'intDisplayOrder' => 0,
						'chrIsFront' => 'Y',
						'chrIsPowerpanel' => 'Y',
						'chrIsGenerated' => 'N',
						'decVersion' => 1.0,
						'chrPublish' => 'Y',
						'chrDelete' => 'N',
						'varPermissions'=> 'list, create, edit, delete, publish,reviewchanges',
						'created_at'=> Carbon::now(),
						'updated_at'=> Carbon::now()
					]);
                                        					
                                        
					
						
                                                                                    DB::table('module')->insert([
                                            'intFkGroupCode' => '2',
						'varTitle' => 'Staticblocks',
						'varModuleName' =>  'static-block',
						'varTableName' => 'static_block',
						'varModelName' => 'StaticBlocks',
						'varModuleClass' => 'StaticBlocksController',
						'varModuleNameSpace' => 'Powerpanel\StaticBlocks\\',
						'intDisplayOrder' => 0,
						'chrIsFront' => 'N',
						'chrIsPowerpanel' => 'Y',
						'chrIsGenerated' => 'N',
						'decVersion' => 1.0,
						'chrPublish' => 'Y',
						'chrDelete' => 'N',
						'varPermissions'=> 'list, create, edit, delete, publish',
						'created_at'=> Carbon::now(),
						'updated_at'=> Carbon::now()
					]);
                                        					
                                        
					
						
                                                                                DB::table('module')->insert([
                                        'intFkGroupCode' => '0',
						'varTitle' => 'Sitemap',
						'varModuleName' =>  'sitemap',
						'varTableName' => '',
						'varModelName' => '',
						'varModuleClass' => '',
						'varModuleNameSpace' => '',
						'intDisplayOrder' => 0,
						'chrIsFront' => 'N',
						'chrIsPowerpanel' => 'Y',
						'chrIsGenerated' => 'N',
						'decVersion' => 1.0,
						'chrPublish' => 'Y',
						'chrDelete' => 'N',
						'varPermissions'=> 'list',
						'created_at'=> Carbon::now(),
						'updated_at'=> Carbon::now()
					]);
                                        					
                                        
					
						
                                                                                DB::table('module')->insert([
                                        'intFkGroupCode' => '0',
						'varTitle' => 'menu-type',
						'varModuleName' =>  'menu-type',
						'varTableName' => 'menu_type',
						'varModelName' => 'MenuType',
						'varModuleClass' => '',
						'varModuleNameSpace' => '',
						'intDisplayOrder' => 0,
						'chrIsFront' => 'N',
						'chrIsPowerpanel' => 'Y',
						'chrIsGenerated' => 'N',
						'decVersion' => 1.0,
						'chrPublish' => 'Y',
						'chrDelete' => 'N',
						'varPermissions'=> 'list, create, edit, delete',
						'created_at'=> Carbon::now(),
						'updated_at'=> Carbon::now()
					]);
                                        					
                                        
					
						
                                                                                DB::table('module')->insert([
                                        'intFkGroupCode' => '0',
						'varTitle' => 'Email Log',
						'varModuleName' =>  'email-log',
						'varTableName' => 'email_log',
						'varModelName' => 'EmailLog',
						'varModuleClass' => 'EmailLogController',
						'varModuleNameSpace' => '',
						'intDisplayOrder' => 0,
						'chrIsFront' => 'N',
						'chrIsPowerpanel' => 'Y',
						'chrIsGenerated' => 'N',
						'decVersion' => 1.0,
						'chrPublish' => 'Y',
						'chrDelete' => 'N',
						'varPermissions'=> 'list,delete',
						'created_at'=> Carbon::now(),
						'updated_at'=> Carbon::now()
					]);
                                        					
                                        
					
						
                                                                                DB::table('module')->insert([
                                        'intFkGroupCode' => '0',
						'varTitle' => 'log',
						'varModuleName' =>  'log',
						'varTableName' => 'log',
						'varModelName' => 'Log',
						'varModuleClass' => 'LogController',
						'varModuleNameSpace' => '',
						'intDisplayOrder' => 0,
						'chrIsFront' => 'N',
						'chrIsPowerpanel' => 'Y',
						'chrIsGenerated' => 'N',
						'decVersion' => 1.0,
						'chrPublish' => 'Y',
						'chrDelete' => 'N',
						'varPermissions'=> 'list, delete, advanced',
						'created_at'=> Carbon::now(),
						'updated_at'=> Carbon::now()
					]);
                                        					
                                        
					
						
                                                                                DB::table('module')->insert([
                                        'intFkGroupCode' => '0',
						'varTitle' => 'Privacy Removal Leads',
						'varModuleName' =>  'privacy-removal-leads',
						'varTableName' => 'privacy_removal_leads',
						'varModelName' => 'PrivacyRemovalLead',
						'varModuleClass' => 'PrivacyRemovalLeadsController',
						'varModuleNameSpace' => '',
						'intDisplayOrder' => 0,
						'chrIsFront' => 'N',
						'chrIsPowerpanel' => 'Y',
						'chrIsGenerated' => 'N',
						'decVersion' => 1.0,
						'chrPublish' => 'Y',
						'chrDelete' => 'N',
						'varPermissions'=> 'list, delete',
						'created_at'=> Carbon::now(),
						'updated_at'=> Carbon::now()
					]);
                                        					
                                        
					
						
                                                                                    DB::table('module')->insert([
                                            'intFkGroupCode' => '0',
						'varTitle' => 'Role',
						'varModuleName' =>  'roles',
						'varTableName' => 'roles',
						'varModelName' => 'RoleManager',
						'varModuleClass' => 'RoleController',
						'varModuleNameSpace' => 'Powerpanel\RoleManager\\',
						'intDisplayOrder' => 0,
						'chrIsFront' => 'N',
						'chrIsPowerpanel' => 'Y',
						'chrIsGenerated' => 'N',
						'decVersion' => 1.0,
						'chrPublish' => 'Y',
						'chrDelete' => 'N',
						'varPermissions'=> 'list, create, edit, delete, publish',
						'created_at'=> Carbon::now(),
						'updated_at'=> Carbon::now()
					]);
                                        					
                                        
					
						
                                                                                    DB::table('module')->insert([
                                            'intFkGroupCode' => '0',
						'varTitle' => 'Blocked IPs',
						'varModuleName' =>  'blocked_ips',
						'varTableName' => 'blocked_ips',
						'varModelName' => 'BlockedIP',
						'varModuleClass' => 'BlockedIpsController',
						'varModuleNameSpace' => 'Powerpanel\BlockedIP\\',
						'intDisplayOrder' => 0,
						'chrIsFront' => 'N',
						'chrIsPowerpanel' => 'Y',
						'chrIsGenerated' => 'N',
						'decVersion' => 1.0,
						'chrPublish' => 'Y',
						'chrDelete' => 'N',
						'varPermissions'=> 'list, create, delete',
						'created_at'=> Carbon::now(),
						'updated_at'=> Carbon::now()
					]);
                                        					
                                        
					
						
                                                                                    DB::table('module')->insert([
                                            'intFkGroupCode' => '2',
						'varTitle' => 'Messaging System',
						'varModuleName' =>  'messagingsystem',
						'varTableName' => 'messagingsystem',
						'varModelName' => 'MessagingSystem',
						'varModuleClass' => 'MessagingSystemController',
						'varModuleNameSpace' => 'Powerpanel\MessagingSystem\\',
						'intDisplayOrder' => 0,
						'chrIsFront' => 'N',
						'chrIsPowerpanel' => 'Y',
						'chrIsGenerated' => 'N',
						'decVersion' => 1.0,
						'chrPublish' => 'Y',
						'chrDelete' => 'N',
						'varPermissions'=> 'list, create, edit, delete, publish',
						'created_at'=> Carbon::now(),
						'updated_at'=> Carbon::now()
					]);
                                        					
                                        
					
						
                                                                                    DB::table('module')->insert([
                                            'intFkGroupCode' => '2',
						'varTitle' => 'Page Template',
						'varModuleName' =>  'page_template',
						'varTableName' => 'visultemplate',
						'varModelName' => 'PageTemplates',
						'varModuleClass' => 'PageTemplateController',
						'varModuleNameSpace' => 'Powerpanel\PageTemplates\\',
						'intDisplayOrder' => 0,
						'chrIsFront' => 'N',
						'chrIsPowerpanel' => 'Y',
						'chrIsGenerated' => 'N',
						'decVersion' => 1.0,
						'chrPublish' => 'Y',
						'chrDelete' => 'N',
						'varPermissions'=> 'list, create, edit, delete, publish, reviewchanges',
						'created_at'=> Carbon::now(),
						'updated_at'=> Carbon::now()
					]);
                                        					
                                        
					
						
                                                                                    DB::table('module')->insert([
                                            'intFkGroupCode' => '4',
						'varTitle' => 'Submit Tickets',
						'varModuleName' =>  'submit-tickets',
						'varTableName' => 'ticket_master',
						'varModelName' => 'TicketList',
						'varModuleClass' => 'SubmitTicketsController',
						'varModuleNameSpace' => 'Powerpanel\TicketList\\',
						'intDisplayOrder' => 0,
						'chrIsFront' => 'N',
						'chrIsPowerpanel' => 'Y',
						'chrIsGenerated' => 'N',
						'decVersion' => 1.0,
						'chrPublish' => 'Y',
						'chrDelete' => 'N',
						'varPermissions'=> 'list, delete',
						'created_at'=> Carbon::now(),
						'updated_at'=> Carbon::now()
					]);
                                        					
                                        
					
						
                                                                                    DB::table('module')->insert([
                                            'intFkGroupCode' => '4',
						'varTitle' => 'FeedBack Leads',
						'varModuleName' =>  'feedback-leads',
						'varTableName' => 'feedback_leads',
						'varModelName' => 'FeedbackLead',
						'varModuleClass' => 'FeedbackleadController',
						'varModuleNameSpace' => 'Powerpanel\FeedbackLead\\',
						'intDisplayOrder' => 0,
						'chrIsFront' => 'N',
						'chrIsPowerpanel' => 'Y',
						'chrIsGenerated' => 'N',
						'decVersion' => 1.0,
						'chrPublish' => 'Y',
						'chrDelete' => 'N',
						'varPermissions'=> 'list, delete',
						'created_at'=> Carbon::now(),
						'updated_at'=> Carbon::now()
					]);
                                        					
                                        
					
						
                                                                                    DB::table('module')->insert([
                                            'intFkGroupCode' => '4',
						'varTitle' => 'Search Statictics',
						'varModuleName' =>  'search-statictics',
						'varTableName' => 'Search_Statictics',
						'varModelName' => 'SearchStaticticsReport',
						'varModuleClass' => 'SearchStaticticsController',
						'varModuleNameSpace' => 'Powerpanel\SearchStaticticsReport\\',
						'intDisplayOrder' => 0,
						'chrIsFront' => 'N',
						'chrIsPowerpanel' => 'Y',
						'chrIsGenerated' => 'N',
						'decVersion' => 1.0,
						'chrPublish' => 'Y',
						'chrDelete' => 'N',
						'varPermissions'=> 'list, delete',
						'created_at'=> Carbon::now(),
						'updated_at'=> Carbon::now()
					]);
                                        					
                                        
					
						
                                                                                    DB::table('module')->insert([
                                            'intFkGroupCode' => '4',
						'varTitle' => 'Hits Report',
						'varModuleName' =>  'hits-report',
						'varTableName' => 'Hits_Report',
						'varModelName' => 'HitsReport',
						'varModuleClass' => 'HitsReportController',
						'varModuleNameSpace' => 'Powerpanel\HitsReport\\',
						'intDisplayOrder' => 0,
						'chrIsFront' => 'N',
						'chrIsPowerpanel' => 'Y',
						'chrIsGenerated' => 'N',
						'decVersion' => 1.0,
						'chrPublish' => 'Y',
						'chrDelete' => 'N',
						'varPermissions'=> 'list, delete',
						'created_at'=> Carbon::now(),
						'updated_at'=> Carbon::now()
					]);
                                        					
                                        
					
						
                                                                                    DB::table('module')->insert([
                                            'intFkGroupCode' => '4',
						'varTitle' => 'Document Report',
						'varModuleName' =>  'document-report',
						'varTableName' => 'Document_Report',
						'varModelName' => 'DocumentReport',
						'varModuleClass' => 'DocumentReportController',
						'varModuleNameSpace' => 'Powerpanel\DocumentReport\\',
						'intDisplayOrder' => 0,
						'chrIsFront' => 'N',
						'chrIsPowerpanel' => 'Y',
						'chrIsGenerated' => 'N',
						'decVersion' => 1.0,
						'chrPublish' => 'Y',
						'chrDelete' => 'N',
						'varPermissions'=> 'list, delete',
						'created_at'=> Carbon::now(),
						'updated_at'=> Carbon::now()
					]);
                                        					
                                        
					
						
                                                                                    DB::table('module')->insert([
                                            'intFkGroupCode' => '4',
						'varTitle' => 'Notification',
						'varModuleName' =>  'notificationlist',
						'varTableName' => 'notificationlist',
						'varModelName' => 'NotificationList',
						'varModuleClass' => 'NotificationListController',
						'varModuleNameSpace' => 'Powerpanel\NotificationList\\',
						'intDisplayOrder' => 0,
						'chrIsFront' => 'N',
						'chrIsPowerpanel' => 'Y',
						'chrIsGenerated' => 'N',
						'decVersion' => 1.0,
						'chrPublish' => 'Y',
						'chrDelete' => 'N',
						'varPermissions'=> 'list, delete',
						'created_at'=> Carbon::now(),
						'updated_at'=> Carbon::now()
					]);
                                        					
                                        
					
						
                                                                                    DB::table('module')->insert([
                                            'intFkGroupCode' => '2',
						'varTitle' => 'FormBuilder',
						'varModuleName' =>  'formbuilder',
						'varTableName' => 'form_builder',
						'varModelName' => 'FormBuilder',
						'varModuleClass' => 'FormBuilderController',
						'varModuleNameSpace' => 'Powerpanel\FormBuilder\\',
						'intDisplayOrder' => 0,
						'chrIsFront' => 'N',
						'chrIsPowerpanel' => 'Y',
						'chrIsGenerated' => 'N',
						'decVersion' => 1.0,
						'chrPublish' => 'Y',
						'chrDelete' => 'N',
						'varPermissions'=> 'list, create, edit, delete, publish',
						'created_at'=> Carbon::now(),
						'updated_at'=> Carbon::now()
					]);
                                        					
                                        
					
						
                                                                                    DB::table('module')->insert([
                                            'intFkGroupCode' => '4',
						'varTitle' => 'Form Builder Lead',
						'varModuleName' =>  'formbuilder-lead',
						'varTableName' => 'formbuilder_lead',
						'varModelName' => 'FormBuilderLead',
						'varModuleClass' => 'FormBuilderLeadController',
						'varModuleNameSpace' => 'Powerpanel\FormBuilderLead\\',
						'intDisplayOrder' => 0,
						'chrIsFront' => 'N',
						'chrIsPowerpanel' => 'Y',
						'chrIsGenerated' => 'N',
						'decVersion' => 1.0,
						'chrPublish' => 'Y',
						'chrDelete' => 'N',
						'varPermissions'=> 'list, delete',
						'created_at'=> Carbon::now(),
						'updated_at'=> Carbon::now()
					]);
                                        					
                                        
					
						
                                                                                    DB::table('module')->insert([
                                            'intFkGroupCode' => '4',
						'varTitle' => 'Live User',
						'varModuleName' =>  'liveuser',
						'varTableName' => 'live_user',
						'varModelName' => 'LiveUser',
						'varModuleClass' => 'LiveUsersController',
						'varModuleNameSpace' => 'Powerpanel\LiveUser\\',
						'intDisplayOrder' => 0,
						'chrIsFront' => 'N',
						'chrIsPowerpanel' => 'Y',
						'chrIsGenerated' => 'N',
						'decVersion' => 1.0,
						'chrPublish' => 'Y',
						'chrDelete' => 'N',
						'varPermissions'=> 'list, delete',
						'created_at'=> Carbon::now(),
						'updated_at'=> Carbon::now()
					]);
                                        					
                                        
					
						
                                                                                    DB::table('module')->insert([
                                            'intFkGroupCode' => '2',
						'varTitle' => 'Workflow',
						'varModuleName' =>  'workflow',
						'varTableName' => 'workflow',
						'varModelName' => 'Workflow',
						'varModuleClass' => 'WorkflowController',
						'varModuleNameSpace' => 'Powerpanel\Workflow\\',
						'intDisplayOrder' => 0,
						'chrIsFront' => 'N',
						'chrIsPowerpanel' => 'Y',
						'chrIsGenerated' => 'N',
						'decVersion' => 1.0,
						'chrPublish' => 'Y',
						'chrDelete' => 'N',
						'varPermissions'=> 'list, create, edit, delete, publish, reviewchanges',
						'created_at'=> Carbon::now(),
						'updated_at'=> Carbon::now()
					]);
                                        					
                                        
					
						
                                                                                    DB::table('module')->insert([
                                            'intFkGroupCode' => '2',
						'varTitle' => 'Blogs',
						'varModuleName' =>  'blogs',
						'varTableName' => 'blogs',
						'varModelName' => 'Blogs',
						'varModuleClass' => 'BlogsController',
						'varModuleNameSpace' => 'Powerpanel\Blogs\\',
						'intDisplayOrder' => 0,
						'chrIsFront' => 'Y',
						'chrIsPowerpanel' => 'Y',
						'chrIsGenerated' => 'N',
						'decVersion' => 1.0,
						'chrPublish' => 'Y',
						'chrDelete' => 'N',
						'varPermissions'=> 'list, create, edit, delete, publish,reviewchanges',
						'created_at'=> Carbon::now(),
						'updated_at'=> Carbon::now()
					]);
                                        					
                                        
					
						
                                                                                    DB::table('module')->insert([
                                            'intFkGroupCode' => '2',
						'varTitle' => 'Blog Category',
						'varModuleName' =>  'blog-category',
						'varTableName' => 'blog_category',
						'varModelName' => 'BlogCategory',
						'varModuleClass' => 'BlogCategoryController',
						'varModuleNameSpace' => 'Powerpanel\BlogCategory\\',
						'intDisplayOrder' => 0,
						'chrIsFront' => 'Y',
						'chrIsPowerpanel' => 'Y',
						'chrIsGenerated' => 'N',
						'decVersion' => 1.0,
						'chrPublish' => 'Y',
						'chrDelete' => 'N',
						'varPermissions'=> 'list, create, edit, delete, publish,reviewchanges',
						'created_at'=> Carbon::now(),
						'updated_at'=> Carbon::now()
					]);
                                        					
                                        
					
						
                                                                                    DB::table('module')->insert([
                                            'intFkGroupCode' => '2',
						'varTitle' => 'Photo Gallery',
						'varModuleName' =>  'photo-gallery',
						'varTableName' => 'photo_gallery',
						'varModelName' => 'PhotoGallery',
						'varModuleClass' => 'PhotoGalleryController',
						'varModuleNameSpace' => 'Powerpanel\PhotoGallery\\',
						'intDisplayOrder' => 0,
						'chrIsFront' => 'N',
						'chrIsPowerpanel' => 'Y',
						'chrIsGenerated' => 'N',
						'decVersion' => 1.0,
						'chrPublish' => 'Y',
						'chrDelete' => 'N',
						'varPermissions'=> 'list, create, edit, delete, publish,reviewchanges',
						'created_at'=> Carbon::now(),
						'updated_at'=> Carbon::now()
					]);
                                        					
                                        
					
						
                                                                                    DB::table('module')->insert([
                                            'intFkGroupCode' => '2',
						'varTitle' => 'Contact Us',
						'varModuleName' =>  'contact-us',
						'varTableName' => 'contact_lead',
						'varModelName' => 'ContactUsLead',
						'varModuleClass' => 'ContactleadController',
						'varModuleNameSpace' => 'Powerpanel\ContactUsLead\\',
						'intDisplayOrder' => 0,
						'chrIsFront' => 'Y',
						'chrIsPowerpanel' => 'Y',
						'chrIsGenerated' => 'N',
						'decVersion' => 2.0,
						'chrPublish' => 'Y',
						'chrDelete' => 'N',
						'varPermissions'=> 'list, delete',
						'created_at'=> Carbon::now(),
						'updated_at'=> Carbon::now()
					]);
                                        					
                                        
					
						
                                                                                    DB::table('module')->insert([
                                            'intFkGroupCode' => '2',
						'varTitle' => 'Services',
						'varModuleName' =>  'services',
						'varTableName' => 'services',
						'varModelName' => 'Services',
						'varModuleClass' => 'ServicesController',
						'varModuleNameSpace' => 'Powerpanel\Services\\',
						'intDisplayOrder' => 0,
						'chrIsFront' => 'Y',
						'chrIsPowerpanel' => 'Y',
						'chrIsGenerated' => 'N',
						'decVersion' => 1.0,
						'chrPublish' => 'Y',
						'chrDelete' => 'N',
						'varPermissions'=> 'list, create, edit, delete, publish',
						'created_at'=> Carbon::now(),
						'updated_at'=> Carbon::now()
					]);
                                        					
                                        
					
						
                                                                                    DB::table('module')->insert([
                                            'intFkGroupCode' => '2',
						'varTitle' => 'Testimonials',
						'varModuleName' =>  'testimonial',
						'varTableName' => 'testimonials',
						'varModelName' => 'Testimonial',
						'varModuleClass' => 'TestimonialController',
						'varModuleNameSpace' => 'Powerpanel\Testimonial\\',
						'intDisplayOrder' => 0,
						'chrIsFront' => 'Y',
						'chrIsPowerpanel' => 'Y',
						'chrIsGenerated' => 'N',
						'decVersion' => 1.0,
						'chrPublish' => 'Y',
						'chrDelete' => 'N',
						'varPermissions'=> 'list, create, edit, delete, publish',
						'created_at'=> Carbon::now(),
						'updated_at'=> Carbon::now()
					]);
                                        					
                                        
					
						
                                                                                    DB::table('module')->insert([
                                            'intFkGroupCode' => '2',
						'varTitle' => 'Team',
						'varModuleName' =>  'team',
						'varTableName' => 'team',
						'varModelName' => 'Team',
						'varModuleClass' => 'TeamController',
						'varModuleNameSpace' => 'Powerpanel\Team\\',
						'intDisplayOrder' => 0,
						'chrIsFront' => 'Y',
						'chrIsPowerpanel' => 'Y',
						'chrIsGenerated' => 'N',
						'decVersion' => 2.0,
						'chrPublish' => 'Y',
						'chrDelete' => 'N',
						'varPermissions'=> 'list, create, edit, delete, publish',
						'created_at'=> Carbon::now(),
						'updated_at'=> Carbon::now()
					]);
                                        					
                                        
					
						
                                                                                    DB::table('module')->insert([
                                            'intFkGroupCode' => '4',
						'varTitle' => 'News Letter',
						'varModuleName' =>  'newsletter-lead',
						'varTableName' => 'newsletter_lead',
						'varModelName' => 'NewsletterLead',
						'varModuleClass' => 'NewsletterController',
						'varModuleNameSpace' => 'Powerpanel\NewsletterLead\\',
						'intDisplayOrder' => 0,
						'chrIsFront' => 'N',
						'chrIsPowerpanel' => 'Y',
						'chrIsGenerated' => 'N',
						'decVersion' => 1.0,
						'chrPublish' => 'Y',
						'chrDelete' => 'N',
						'varPermissions'=> 'list, delete',
						'created_at'=> Carbon::now(),
						'updated_at'=> Carbon::now()
					]);
                                        					
                                        
					
						
                                                                                    DB::table('module')->insert([
                                            'intFkGroupCode' => '2',
						'varTitle' => 'Contact Info',
						'varModuleName' =>  'contact-info',
						'varTableName' => 'contact_info',
						'varModelName' => 'ContactInfo',
						'varModuleClass' => 'ContactInfoController',
						'varModuleNameSpace' => 'Powerpanel\ContactInfo\\',
						'intDisplayOrder' => 0,
						'chrIsFront' => 'N',
						'chrIsPowerpanel' => 'Y',
						'chrIsGenerated' => 'N',
						'decVersion' => 1.0,
						'chrPublish' => 'Y',
						'chrDelete' => 'N',
						'varPermissions'=> 'list, create, edit, delete, publish',
						'created_at'=> Carbon::now(),
						'updated_at'=> Carbon::now()
					]);
                                        					
                                        
					
						
                                                                                    DB::table('module')->insert([
                                            'intFkGroupCode' => '2',
						'varTitle' => 'Services Category',
						'varModuleName' =>  'service-category',
						'varTableName' => 'service_category',
						'varModelName' => 'ServicesCategory',
						'varModuleClass' => 'ServiceCategoryController',
						'varModuleNameSpace' => 'Powerpanel\ServicesCategory\\',
						'intDisplayOrder' => 0,
						'chrIsFront' => 'Y',
						'chrIsPowerpanel' => 'Y',
						'chrIsGenerated' => 'N',
						'decVersion' => 1.0,
						'chrPublish' => 'Y',
						'chrDelete' => 'N',
						'varPermissions'=> 'list, create, edit, delete, publish',
						'created_at'=> Carbon::now(),
						'updated_at'=> Carbon::now()
					]);
                                        					
                                        
					
						
                                                                                    DB::table('module')->insert([
                                            'intFkGroupCode' => '2',
						'varTitle' => 'FAQ Category',
						'varModuleName' =>  'faq-category',
						'varTableName' => 'faq_category',
						'varModelName' => 'FaqCategory',
						'varModuleClass' => 'FaqCategoryController',
						'varModuleNameSpace' => 'Powerpanel\FaqCategory\\',
						'intDisplayOrder' => 0,
						'chrIsFront' => 'Y',
						'chrIsPowerpanel' => 'Y',
						'chrIsGenerated' => 'N',
						'decVersion' => 1.0,
						'chrPublish' => 'Y',
						'chrDelete' => 'N',
						'varPermissions'=> 'list, create, edit, delete, publish,reviewchanges',
						'created_at'=> Carbon::now(),
						'updated_at'=> Carbon::now()
					]);
                                        					
                                        
					
						
                                                                                    DB::table('module')->insert([
                                            'intFkGroupCode' => '2',
						'varTitle' => 'FAQ',
						'varModuleName' =>  'faq',
						'varTableName' => 'faq',
						'varModelName' => 'Faq',
						'varModuleClass' => 'FaqController',
						'varModuleNameSpace' => 'Powerpanel\Faq\\',
						'intDisplayOrder' => 0,
						'chrIsFront' => 'Y',
						'chrIsPowerpanel' => 'Y',
						'chrIsGenerated' => 'N',
						'decVersion' => 1.0,
						'chrPublish' => 'Y',
						'chrDelete' => 'N',
						'varPermissions'=> 'list, create, edit, delete, publish,reviewchanges',
						'created_at'=> Carbon::now(),
						'updated_at'=> Carbon::now()
					]);
                                        					
                                        
					
						
                                                                                    DB::table('module')->insert([
                                            'intFkGroupCode' => '2',
						'varTitle' => 'Video Gallery',
						'varModuleName' =>  'video-gallery',
						'varTableName' => 'video_gallery',
						'varModelName' => 'VideoGallery',
						'varModuleClass' => 'VideoGalleryController',
						'varModuleNameSpace' => 'Powerpanel\VideoGallery\\',
						'intDisplayOrder' => 0,
						'chrIsFront' => 'Y',
						'chrIsPowerpanel' => 'Y',
						'chrIsGenerated' => 'N',
						'decVersion' => 1.0,
						'chrPublish' => 'Y',
						'chrDelete' => 'N',
						'varPermissions'=> 'list, create, edit, delete, publish,reviewchanges',
						'created_at'=> Carbon::now(),
						'updated_at'=> Carbon::now()
					]);
                                        					
                                        
					
								
					DB::table('module')->insert([
						'varTitle' => 'Users',
						'varModuleName' =>  'users',
						'varTableName' => 'users',
						'varModelName' => 'User',
						'varModuleClass' => 'UserController',
                                                'varModuleNameSpace'=>'',
						'intDisplayOrder' => 0,
						'chrIsFront' => 'N',
						'chrIsPowerpanel' => 'Y',
						'chrIsGenerated' => 'N',
						'decVersion' => 1.0,
						'chrPublish' => 'Y',
						'chrDelete' => 'N',
						'varPermissions'=>'list, create, edit, delete, publish',
						'created_at'=> Carbon::now(),
						'updated_at'=> Carbon::now()
					]);

					DB::table('module')->insert([
						'varTitle' => 'Profile',
						'varModuleName' =>  'changeprofile',
						'varTableName' => 'users',
						'varModelName' => 'User',
						'varModuleClass' => 'ProfileController',
                                                'varModuleNameSpace'=>'',
						'intDisplayOrder' => 0,
						'chrIsFront' => 'N',
						'chrIsPowerpanel' => 'Y',
						'chrIsGenerated' => 'N',
						'decVersion' => 1.0,
						'chrPublish' => 'Y',
						'chrDelete' => 'N',
						'varPermissions'=>'edit, change-password',
						'created_at'=> Carbon::now(),
						'updated_at'=> Carbon::now()
					]);

					DB::table('module')->insert([
						'varTitle' => 'General Setting',
						'varModuleName' =>  'settings',
						'varTableName' => 'general_setting',
						'varModelName' => 'GeneralSettings',
						'varModuleClass' => 'SettingsController',
                                                'varModuleNameSpace'=>'',
						'intDisplayOrder' => 0,
						'chrIsFront' => 'N',
						'chrIsPowerpanel' => 'Y',
						'chrIsGenerated' => 'N',
						'decVersion' => 1.0,
						'chrPublish' => 'Y',
						'chrDelete' => 'N',
						'varPermissions'=>'general-setting-management, smtp-mail-setting, seo-setting, social-setting, social-media-share-setting, other-setting, maintenance-setting, recent-activities, module-setting,security-setting,cron-setting,features-setting,magic-setting,maintenancenew-setting',
						'created_at'=> Carbon::now(),
						'updated_at'=> Carbon::now()
					]);

					DB::table('module')->insert([
						'varTitle' => 'Login history',
						'varModuleName' =>  'login-history',
						'varTableName' => 'login_history',
						'varModelName' => 'Login History',
						'varModuleClass' => 'LoginHistoryController',
                                                'varModuleNameSpace'=>'',
						'intDisplayOrder' => 61,
						'chrIsFront' => 'N',
						'chrIsPowerpanel' => 'Y',
						'chrIsGenerated' => 'N',
						'decVersion' => 1.0,
						'chrPublish' => 'Y',
						'chrDelete' => 'N',
						'varPermissions'=>'list, delete',
						'created_at'=> Carbon::now(),
						'updated_at'=> Carbon::now()
					]);
				
				
		}
}
