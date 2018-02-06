<?php
/**
 * Created by PhpStorm.
 * User: healer
 * Date: 2017/11/24
 * Time: 21:54
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\MenuService;
use Illuminate\View\View;
use Mockery\CountValidator\Exception;

class AdminBaseController extends Controller
{
    private $pageTitle = '';

    private $breadcrumb = [];

    private $skin = 'skin-blur-greenish';   // set default skin
    /**
     * @param $pageTitle
     * @param $breadcrumb
     */
    protected function setPageTitleAndBreadcrumb($pageTitle, array $breadcrumb)
    {
        $this->pageTitle = $pageTitle;
        $this->breadcrumb = $breadcrumb;
    }

    /**
     * @param $view
     * @param $vars
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    protected function render($view, $vars)
    {
        $vars['title'] = env('ADMIN_TITLE', 'Admin');
        $vars['currentUserName'] = 'Healer';
        $vars['currentUserDomain'] = 'healer.kx.yu@gmail.com';
        $vars['menus'] = MenuService::getMenu();
        $vars['pageTitle'] = $this->pageTitle;
        $vars['breadcrumb'] = $this->breadcrumb;
        $vars['skin'] = $this->skin;
        
        try {
            $jsView = $view . '-js';
            view($jsView, []);  // Try-catch to detect the template exists.
            $vars['pageJs'] = $view . '-js';
        } catch (\InvalidArgumentException $e) {
            $vars['pageJs'] = null;
        }

        return view($view, $vars);
    }
}