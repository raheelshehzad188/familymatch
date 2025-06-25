<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/userguide3/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'welcome';
$route['signup'] = 'user/signup';
$route['admin'] = 'admin/dashboard';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
//APIs routes
$route['api/signup']['POST'] = 'api/signup/register';
$route['api/login']['POST'] = 'api/login/login';
$route['api/email']['POST'] = 'api/api/email';
$route['api/upload']['POST'] = 'api/mediaController/upload';
$route['api/gallary']['GET'] = 'api/mediaController/gallary';
$route['api/delete-img/(:any)']['GET'] = 'api/mediaController/del_img/$1';
$route['api/submit_survey']['POST'] = 'api/Profile/submit_survey';
$route['api/update-profile']['POST'] = 'api/Profile/update_profile';
$route['api/ignore-profile']['POST'] = 'api/Profile/ignore_profile';
$route['api/like-profile']['POST'] = 'api/Profile/like_profile';
$route['api/wink-profile']['POST'] = 'api/Profile/wink_profile';
$route['api/childern/add']['POST'] = 'api/Childern/add';
$route['api/delete-childern/(:any)']['GET'] = 'api/Childern/del_child/$1'; 
$route['api/user-profile/(:any)'] = 'api/Profile/user_profile/$1';
$route['api/user-about/(:any)'] = 'api/Profile/user_about/$1';

$route['api/likes']['GET'] = 'api/Profile/likes';
$route['api/winks']['GET'] = 'api/Profile/winks';
$route['api/profile']['GET'] = 'api/Profile';
$route['api/matches']['GET'] = 'api/Profile/matches';
$route['api/results']['GET'] = 'api/api/results';
$route['api/user-matches/(:any)']['GET'] = 'api/Profile/user_matches/$1';
$route['api/countries']['GET'] = 'api/api/countries';
$route['api/states']['GET'] = 'api/api/states'; 
$route['api/cities']['GET'] = 'api/api/cities';
$route['api/questions']['GET'] = 'api/api/questions';
$route['api/refferals']['GET'] = 'api/api/refferals';
$route['api/body_types']['GET'] = 'api/api/body_types';
$route['api/genders']['GET'] = 'api/api/genders';
$route['api/profile_options']['GET'] = 'api/api/profile_options';
$route['api/options']['GET'] = 'api/api/options';
$route['api/search']['GET'] = 'api/api/search';
$route['image/(:any)/(.+)'] = 'image/resize/$1/$2';



