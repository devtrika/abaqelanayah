<?php

namespace App\Traits;

use Illuminate\Support\Facades\Route;
use App\Models\Permission;

trait  SideBar
{

    // display routes
    static function sidebar()
    {
        $routes         = Route::getRoutes();
        $routes_data    = [];
        $html = '' ;
        $my_routes      = Permission::where('role_id', auth()->guard('admin')->user()->role_id)->pluck('permission')->toArray();
        foreach ($routes as $route) {
            if ($route->getName() && in_array($route->getName(), $my_routes))
                $routes_data['"'.$route->getName().'"'] = [
                    'title'     => isset($route->getAction()['title']) ? $route->getAction()['title'] : null,
                    'icon'      => isset($route->getAction()['icon']) ? $route->getAction()['icon'] : null,
                    'name'      => $route->getName()  ,
                ];
        }

        foreach ($routes as $value) {
            if ($value->getName() !== null) {

                //display only parent routes
                if (isset($value->getAction()['title']) && isset($value->getAction()['icon']) && isset($value->getAction()['type']) && $value->getAction()['type'] == 'parent') {


                    //display route with sub directory
                    if (isset($value->getAction()['sub_route']) && $value->getAction()['sub_route'] == true && isset($value->getAction()['child']) && count($value->getAction()['child'])) {

                        // check user auth to access this route
                        if (in_array($value->getName(), $my_routes)) {


                            // All parent items are open, and the active one is highlighted
                            $parentLiClasses = 'has-sub open';
                            $isParentActive = false;
                            $currentRouteName = Route::currentRouteName();

                            if (isset($value->getAction()['child']) && is_array($value->getAction()['child'])) {
                                foreach ($value->getAction()['child'] as $child) {
                                    $resource = explode('.', $child)[0];
                                    if (str_starts_with($currentRouteName, 'admin.' . $resource)) {
                                        $isParentActive = true;
                                        break;
                                    }
                                }
                            }

                            if ($isParentActive) {
                                $parentLiClasses .= ' sidebar-group-active';
                            }

                            $html .= '<li class="nav-item ' . $parentLiClasses . '"><a href="javascript:void(0);">' . $value->getAction()['icon'] . '<span class="menu-title" data-i18n="Dashboard">' . __('admin.'.$value->getAction()['title']) . '</span></a>
                                <ul class="menu-content">';

                            // display child sub directories
                            foreach ($value->getAction()['child'] as $child){
                                $resource = explode('.', $child)[0];
                                $active = str_starts_with(Route::currentRouteName(), 'admin.' . $resource) ? 'active' : '';

                                if (isset($routes_data['"admin.' . $child . '"']) && $routes_data['"admin.' . $child . '"']['title'] && $routes_data['"admin.' . $child . '"']['icon']){
                                    $html .=  '<li class="'. $active.'"><a href="' . route('admin.'.$child) . '"><i class="feather icon-circle"></i>'. __('admin.'.$routes_data['"admin.' . $child . '"']['title']) . ' </a></li>';
                                }
                            }

                            $html .= '</ul></li>';
                        }
                    } else {

                    if (in_array($value->getName(), $my_routes)) {
                        $active = $value->getName() == Route::currentRouteName() ? 'active' : '';
                        $activeLi ="";
                        $html .= '<li class="nav-item '.$active.'"><a href="' . route($value->getName()) . '"> ' . $value->getAction()['icon'] . '<span class="menu-title" data-i18n="Dashboard">' . __('admin.'.$value->getAction()['title']) . '</span> <span class="link-text d-flex align-items-center"></a></li>';
                    }
                }
            }
        }
    }
    return $html ;
}

    // display routes
    static function sidebar2()
    {
        $routes         = Route::getRoutes();
        $my_routes      = Permission::where('role_id', auth()->guard('admin')->user()->role_id)->pluck('permission')->toArray();

        $parents        = [];
        $childs         = [];




        foreach ($routes as $route) {
            if ($route->getName() && in_array($route->getName(), $my_routes) && isset($route->getAction()['title']) && isset($route->getAction()['icon']) && ! isset($route->getAction()['type'])) {

                $childs['"'.$route->getName().'"'] = [
                    'title'     => isset($route->getAction()['title']) ? $route->getAction()['title'] : null,
                    'icon'      => isset($route->getAction()['icon']) ? $route->getAction()['icon'] : null,
                    'name'      => $route->getName()  ,
                ];
            }

        }

        foreach ($routes as $route) {
            if ($route->getName() && in_array($route->getName(), $my_routes) && isset($route->getAction()['title']) && isset($route->getAction()['icon']) && isset($route->getAction()['type']) && $route->getAction()['type'] == 'parent' ){

                $parents['"'.$route->getName().'"'] = [
                    'title'         => isset($route->getAction()['title']) ? $route->getAction()['title'] : null,
                    'icon'          => isset($route->getAction()['icon']) ? $route->getAction()['icon'] : null,
                    'name'          => $route->getName()  ,
                    'childsArray'   => isset($route->getAction()['child']) ? $route->getAction()['child'] : [] ,
                    'childs'        =>  null ,
                ];

                if (isset($route->getAction()['sub_route']) && $route->getAction()['sub_route'] == true ){
                    foreach ($route->getAction()['child'] as $child){
                        if (isset($childs['"admin.' . $child . '"']) && $childs['"admin.' . $child . '"']['title'] && $childs['"admin.' . $child . '"']['icon']){
                            $parents['"'.$route->getName().'"']['childs'][] = [
                                'title'     => $childs['"admin.' . $child . '"']['title'] ? $childs['"admin.' . $child . '"']['title'] : null,
                                'icon'      => $childs['"admin.' . $child . '"']['icon'] ? $childs['"admin.' . $child . '"']['icon'] : null,
                                'name'      => $child  ,
                            ] ;
                        }
                    }
                }

            }

        }

        return $parents;
    }

}