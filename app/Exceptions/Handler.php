<?php

namespace App\Exceptions;

use Powerpanel\CmsPage\Models\CmsPage;
use App\Helpers\MenuBuilder;
use App\Http\Traits\slug;
use Powerpanel\Menu\Models\Menu;
use Config;
use Exception;
use File;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof NotFoundHttpException) {
            if (Request::segment(1) != "powerpanel") {
                $cmsPageId = slug::resolve_alias_for_routes(!empty(Request::segment(1)) ? Request::segment(1) : 'home');
                $pageCms = null;

                if (is_numeric($cmsPageId)) {
                    $pageCms = CmsPage::getPageByPageId($cmsPageId);
                }
                $menu_content = Menu::getFrontList();
                if (isset($menu_content[1]) && $menu_content[1][0]['menuType']['chrPublish'] == 'Y') {
                    MenuBuilder::loadMenu($menu_content[1], 'headerMenu');
                }
                if (isset($menu_content[2]) && $menu_content[2][0]['menuType']['chrPublish'] == 'Y') {
                    MenuBuilder::loadMenu($menu_content[2], 'footerMenu');
                }
                if (isset($menu_content[3]) && $menu_content[3][0]['menuType']['chrPublish'] == 'Y') {
                    MenuBuilder::loadMenu($menu_content[3], 'quickLinks');
                }
                $shareData['META_TITLE'] = 'Oops! 404 The requested page not found';
                $shareData['META_KEYWORD'] = 'Oops! 404 The requested page not found';
                $shareData['META_DESCRIPTION'] = 'Oops! 404 The requested page not found';
                $shareData['menu_content'] = $menu_content;
                $shareData['APP_URL'] = Config::get('Constant.ENV_APP_URL');
                $shareData['SHARE_IMG'] = Config::get('Constant.FRONT_LOGO_ID');
                $shareData['currentPageTitle'] = '404 Page Not Found';

                if (File::exists(app_path() . '/ContactInfo.php') != null) {
                    $contacts = \App\ContactInfo::getContactDetails();
                    $shareData['objContactInfo'] = (!empty($contacts))?$contacts:'';
                }
                
                return response()->view('errors.404', $shareData, 404);
            } else {
                return response()->view('powerpanel.errors.404', [], 404);
            }
        }
        return parent::render($request, $exception);
    }
}
