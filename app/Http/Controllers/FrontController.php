<?php
/**
 * Created by PhpStorm.
 * User: kacpe
 * Date: 22.12.2016
 * Time: 00:11
 */

namespace App\Http\Controllers;

use App\Models\Page;

abstract class FrontController extends Controller
{
    public function __construct()
    {
        parent::__construct();

        if (!request()->ajax()) {
            $main_menu = Page::generateMenu();
            view()->share(compact('main_menu'));
        }

        $this->addCSS('https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;700;800&display=swap');
        $this->addCSS('css/app.css');
        $this->addJS('js/vendor/jquery-3.2.1.min.js');
        $this->addJS('js/vendor/popper.min.js');
        $this->addJS('js/vendor/bootstrap.min.js');
        $this->addJS('js/vendor/parsley.min.js');
        $this->addJS('js/vendor/aos.min.js');
        $this->addJS('js/vendor/jquery.matchHeight.min.js');
        $this->addJS('js/vendor/jquery.magnific-popup.min.js');
        $this->addJS('js/vendor/jquery.mask.min.js');
        $this->addJS('js/vendor/js.cookie.min.js');
        $this->addJS('js/vendor/jquery.nice-select.min.js');
        $this->addJS('js/vendor/swiper.min.js');
        $this->addJS('js/vendor/jquery.countTo.js');
        $this->addJS('js/vendor/in-view.min.js');
        $this->addJS('js/app.js');

        $this->addJS('https://maps.googleapis.com/maps/api/js?key=AIzaSyCmbFXBJOQKldPt3ZesQEJ_G5Do3274deQ');
        $this->addJS('js/map.js');
    }
}
