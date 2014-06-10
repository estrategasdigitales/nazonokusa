<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
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
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

$route['default_controller'] = "cms";
$route['404_override'] = '';

$route['login']						= 'cms/login';
$route['inicio']					= 'cms/index';
$route['salir']						= 'cms/logout';
$route['usuarios']					= 'cms/admin_usuarios';
$route['trabajos']					= 'cms/admin_trabajos';
$route['categorias']				= 'cms/admin_categorias';
$route['verticales']				= 'cms/admin_verticales';
$route['nuevo_usuario']				= 'cms/nuevo_usuario';
$route['nueva_categoria']			= 'cms/nueva_categoria';
$route['nueva_vertical']			= 'cms/nueva_vertical';
$route['nuevo_trabajo']				= 'cms/nuevo_trabajo';
$route['reportes']					= 'cms/reportes';
$route['eliminar/(:any)']			= 'cms/eliminar_usuario/$1';
$route['eliminar_categoria/(:any)']	= 'cms/eliminar_categoria/$1';
$route['eliminar_vertical/(:any)']	= 'cms/eliminar_vertical/$1';
$route['editar/(:any)']				= 'cms/editar_usuario/$1';
$route['editar_trabajo/(:any)']     = 'nucleo/editar_trabajo/$1';
$route['ejecutar_trabajo/(:any)']   = 'nucleo/ejecutar_trabajo/$1';  
$route['eliminar_trabajo']   		= 'nucleo/eliminar_trabajo';  
$route['nucleo'] 					= 'nucleo/';

/* End of file routes.php */
/* Location: ./application/config/routes.php */