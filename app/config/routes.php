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
$route['usuarios/(:any)']			= 'cms/admin_usuarios/$1';
$route['trabajos']					= 'cms/admin_trabajos';
$route['trabajos/(:any)']			= 'cms/admin_trabajos/$1';
$route['categorias']				= 'cms/admin_categorias';
$route['categorias/(:any)']			= 'cms/admin_categorias/$1';
$route['verticales']				= 'cms/admin_verticales';
$route['verticales/(:any)']			= 'cms/admin_verticales/$1';
$route['estructuras']				= 'cms/admin_estructuras';
$route['estructuras/(:any)']		= 'cms/admin_estructuras/$1';
$route['nuevo_usuario']				= 'cms/nuevo_usuario';
$route['nueva_categoria']			= 'cms/nueva_categoria';
$route['nueva_vertical']			= 'cms/nueva_vertical';
$route['nuevo_trabajo']				= 'cms/nuevo_trabajo';
$route['editar_trabajo/(:any)']		= 'cms/editar_trabajo/$1';
$route['nueva_estructura']			= 'cms/nueva_estructura';
$route['nuevo_reporte']				= 'cms/nuevo_reporte';
$route['reportes']					= 'cms/reportes';
$route['reportes/(:any)']			= 'cms/reportes/$1';
$route['eliminar_usuario']			= 'cms/modal_eliminar_usuario';
$route['eliminar_categoria']		= 'cms/modal_eliminar_categoria';
$route['eliminar_vertical']			= 'cms/modal_eliminar_vertical';
$route['eliminar_trabajo']   		= 'cms/modal_eliminar_trabajo';
$route['eliminar_estructura']   	= 'cms/modal_eliminar_estructura';
$route['editar/(:any)']				= 'cms/editar_usuario/$1';
// $route['editar_trabajo/(:any)']     = 'nucleo/editar_trabajo/$1';
$route['ejecutar_trabajo/(:any)']   = 'nucleo/ejecutar_trabajo/$1';
$route['job_execute']     			= 'nucleo/job_execute';
$route['generar_reporte_pdf']     	= 'cms/generar_reporte_pdf';
$route['generar_reporte_csv']     	= 'cms/generar_reporte_csv';
$route['generar_reporte_xls']     	= 'cms/generar_reporte_excel';
//$route['eliminar_trabajo']   		= 'nucleo/eliminar_trabajo';
$route['nucleo'] 					= 'nucleo/index';
$route['actualizar_perfil']			= 'cms/actualizar_perfil';
$route['forgot']					= 'cms/recuperar_contrasena';
$route['forgot_validate']			= 'cms/recuperar_contrasena_validar';
$route['categorias_asignadas']		= 'cms/modal_listar_categorias_asignadas';
$route['verticales_asignadas']		= 'cms/modal_listar_verticales_asignadas';

/* End of file routes.php */
/* Location: ./application/config/routes.php */