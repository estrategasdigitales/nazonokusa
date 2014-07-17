<?php

class HomeController extends BaseController {
	protected static $tree;
	protected static $increase;
	/*
	|--------------------------------------------------------------------------
	| Default Home Controller
	|--------------------------------------------------------------------------
	|
	| You may wish to use controllers instead of, or in addition to, Closure
	| based routes. That's great! Here is an example controller method to
	| get you started. To route to this controller, just add the route:
	|
	|	Route::get('/', 'HomeController@showWelcome');
	|
	*/

	public function getIndex()
	{
		// Dirección url a descomprimir
		$url = "http://middleware.estrategasdigitales.net/nucleo/feed_service?url=aHR0cDovL2ZlZWRzLmVzbWFzLmNvbS9kYXRhLWZlZWRzLWVzbWFzL2lwYWQvZGVwb3J0ZXMuanM%3D";
		// Objeto base descomprimido
		$json = json_decode(file_get_contents($url))[0];

		// Árbol
		$tree = new Tree($json, true);

		$jsonStr = "[";
		$jsonStr .= $this->treeBuild($tree);
		$jsonStr = substr($jsonStr, 0, -5) . "]";
		$jsonStr =  preg_replace("/,\]\}/", "]}", $jsonStr);

		Session::put('tree', serialize($tree));

		Session::put('nodes', serialize($tree->getNodes()));


		return Response::make($jsonStr, 200)->header('Content-Type', 'application/json');
	}

	public function getItem() {
		$tree = unserialize(Session::get('tree'));
		$nodesSelected = unserialize(Session::get('nodes'));

		// Buscamos en el arreglo y activamos el indice
		$this->selected($tree, '3faad3268ca7f5e9b88e8f792418a956', $nodesSelected);
		$this->selected($tree, 'b1be5cfbb1cc77a9713244d8caf04ad5', $nodesSelected);
		$this->selected($tree, '316e8967a54344f04ad558b73850bc47', $nodesSelected);
		$this->selected($tree, '63b23e503f5155207d0a0619fcbecfec', $nodesSelected);
		$this->selected($tree, 'a424a36fe27d756343fec1db734179da', $nodesSelected);
		$this->selected($tree, '98429a158f0d45d92bce2ed8b89c33e6', $nodesSelected);
		
		
		/*$this->selected($tree, 'dbfc84e760a779b76076a27f5ddce8ed', $nodesSelected);
		$this->selected($tree, '3adf71b1a74c25dd66adaa28d770a853', $nodesSelected);*/


		//var_dump($nodesSelected[4]);exit;
		/*$this->selected($tree, '74ddfd26dac55e9233408cca6ffabc48');
		$this->selected($tree, '8000c0921d2d51a7fef8af2b62663769');
		$this->selected($tree, 'c853ddc369ef16fe11de16d12ee64439');
		$this->selected($tree, 'f3d2041d653d656dc2c9b75f34a09a8c');
		$this->selected($tree, '69ef9452f5f5283212e0fb3d3164bec7');*/
		//$this->selected($tree, '3af795ad5727e8290d2fd2290c2aadcc');
		//$this->selected($tree, '36956741bcee127d6ad35064127f4d3f');
		//$this->selected($tree, 'db7fdd913b90513e0c4ab6af692eaeae');
		//$this->selected($tree, '8f55195de73a2dc7e1c93f407c741282');
		//
		//$this->selected($tree, 'b37c3b9f9259cdf542bbc4e3b8151e6a');

		/*foreach ($json as $key => $value) {
			var_dump($value);exit;
		}*/
		//var_dump($tree->getNodes()[4]);exit;
		//$tree->setNodes();

		//var_dump($nodesSelected[1][1]->getChildrens());exit;
		//$this->treeBuild($tree, true);
		// Validamos el json
		//var_dump($nodesSelected[2][0]->getSelected());exit;
		$urlFeed = 'http://feeds.esmas.com/data-feeds-esmas/ipad/deportes.js';
		$feed = json_decode(file_get_contents($urlFeed))->category;

		new TreeMatch($feed, $nodesSelected);


		echo json_encode($feed);exit;

		

		//var_dump($tree->getParameters()->getChildrens()[2]->getParameters()->getChildrens()[1]->getParameters()->getChildrens()[1]->getParameters()->getChildrens());exit;
		$jsonStr = "[";
		$jsonStr .= $this->treeBuild($tree, true);

		if (json_decode($jsonStr)) {
			$jsontmp = $jsonStr;
		} else {
			$i = -20;

			$jsontmp = substr($jsonStr, 0, $i) . "]}]";

			//$jsonStr .= "]}]";

			while(!json_decode($jsontmp)) {
				$i++;
				$jsontmp = substr($jsonStr, 0, $i) . "]}]";
			}
		}

		$jsontmp = '{"category": ' . $jsontmp . '}';
		foreach ($nodesSelected[2] as $key => $value) {
			echo $value->getName();
		}
		exit;
		//var_dump(join(',',$nodesSelected[1])); exit;

		$jsonValidate = new TreeFeed($feed, 1, $jsontmp, 'category');
		echo json_encode($feed);exit;

		//$json = (array)json_decode($jsontmp);

		//echo $jsontmp;exit;

		//var_dump($json);exit;



		//$url = "http://middleware.estrategasdigitales.net/nucleo/feed_service?url=aHR0cDovL2ZlZWRzLmVzbWFzLmNvbS9kYXRhLWZlZWRzLWVzbWFzL2lwYWQvZGVwb3J0ZXMuanM%3D";
		//$url = "http://middleware.estrategasdigitales.net/nucleo/feed_service?url=aHR0cDovL3N0YXRpYy10ZWxldmlzYWRlcG9ydGVzLmVzbWFzLmNvbS9zcG9ydHNkYXRhL2Z1dGJvbC9kYXRhLzMA";
		//$url = "http://middleware.estrategasdigitales.net/nucleo/feed_service?url=aHR0cDovL2ZlZWRzLmVzbWFzLmNvbS9kYXRhLWZlZWRzLWVzbWFzL2FwcGpzL3RkaG9tZS5qcw%3D%3D";
		//$content = file_get_contents('http://feeds.esmas.com/data-feeds-esmas/ipad/deportes.js');
		$content = file_get_contents('http://feeds.esmas.com/data-feeds-esmas/ipad/deportes.js');


		$content = (array)json_decode($content);
/*
		// Armamos la clase con los valores de constructor.
		$treeMatch = new TreeMatch($content,  $json);

		// Número de elementos
		$totalItems = count($json['category']);

		echo json_encode($json['category']);exit;

		foreach ($json['category'] as $key => $value) {
			if (condition) {
				# code...
			}
			var_dump($value);exit;
		}*/

		/*for($i = 0; $i<$totalItems; $i++) {
			if (property_exists($json['category'][$i], 'program')) {*/
		//var_dump($content);exit;



		$content = $this->getRss($json['category'], $content['category']);

		$content[0]->program = $this->getRss($json['category'][2]->program, $content[0]->program);



		foreach ($content[0]->program as $key => $value) {
			$content[0]->program[$key]->videos = $this->getRss($json['category'][2]->program[5]->videos, $content[0]->program[$key]->videos);
		}

		echo json_encode($content);exit;

/*		$content['category'][0] = array_map(function($key, $index){
			var_dump($content['category'][0]);exit;
		}, $vars, array_keys($vars));*/

		//var_dump(array_diff($content, $json));exit;

		//var_dump($content);exit;

		//$jsonStr = substr($jsonStr, 0, -1);
		//$jsonStr =  preg_replace("/,\}\}/", "}}", $jsonStr);
		/*$jsonStr =  preg_replace("/,\}/", "}", $jsonStr);
		$jsonStr =  preg_replace("/:\{\{/", ":{", $jsonStr);
		$jsonStr =  preg_replace("/\"\}\}/", "\"}", $jsonStr);*/


		/*$jsonStr = substr($jsonStr, 0, -1) . "}";
		$jsonStr =  preg_replace("/,\}/", "}", $jsonStr);
		$jsonStr =  preg_replace("/:\{\{/", ":{", $jsonStr);
		$jsonStr =  preg_replace("/\"\}\}/", "\"}", $jsonStr);*/
		//$jsonStr .= "]";

		return Response::make($jsontmp, 200)->header('Content-Type', 'application/json');
	}

	/**
	 * Devuelve el RSS
	 * @param  string $content
	 * @return string
	 */
	public function getRss($json, $content, $key = null) {
		//var_dump(getType($content));exit;
		// Si es un arreglo entonces obtenemos las claves
		if (is_array($content)) {
			foreach ($content as $key => $value) {
				$this->getRss($json, $value);
			}
		}
		$keys = [];
		// Si es un objeto entonces lo recorremos
		if (is_object($content)) {
			$keys = get_object_vars($content);
		}

		foreach ($json as $key => $value) {
			$allowed[] = call_user_func(function ($item) {
				$item = get_object_vars($item);

				foreach ($item as $key => $value) {
					return $key;
				}
			}, $value);
		}

		foreach ($keys as $key => $value) {
			if(!in_array($key, $allowed)) {
				unset($content->$key);
			} else {
				if (is_array($value)) {
					$index = array_search($key, $allowed);

					//$this->getRss($json[$index], $value);
				}
			}
		}

		return $content;
	}

	/**
	 * Selecciona un elemento con determinado indice
	 */
	function selected ($tree, $index, &$nodesSelected, $depth = false) {

		if ($depth === false) {
			$depth = 0;
		}

		foreach ($tree->getParameters()->getChildrens() as $key => $value) {
			if ((string)$value === $index) {
				//echo $depth;
				//echo $value->getName();

				$item = array_search((string)$value, $nodesSelected[$depth+1]);
				/*foreach ( as $key => $value) {
					if (array_search($value)
				}*/
				//var_dump($nodesSelected[$depth+1][0]->getName());exit;
				$value->setSelected();

				// actualizamos
				if ($item !== false) {
					if ($value->getParent()) {
						$value->getParent()->setSelected();

						$itemArray = array_search((string)$value->getParent(), $nodesSelected[$depth]);

						if ($itemArray !== false) {
							$nodesSelected[$depth][$itemArray] = $value->getParent();
						}
					}

					$nodesSelected[$depth+1][$item] = $value;
				}

				return $value;
			}

			if ($value->getType() === 'folder') {
				$depth++;
				$this->selected($value, $index, $nodesSelected, $depth);
				$depth--;
			}
		}
	}

	/**
	 * Construcción de cuerpo
	 * @param  Tree $tree
	 * @param boolean $selected Solo elementos seleccionados
	 * @return string
	 */
	function treeBuild ($tree, $selected = false) {
		if ($selected === false) {
			return $this->wSelected($tree);
		} else {
			return $this->yselected($tree);
		}
	}

	function wSelected($tree) {
		foreach ($tree->getParameters()->getChildrens() as $key => $value) {
			if (!isset($str)) {
				$str = "";
			}

			$str .=  '{"identifier": "' . $value . '",  "name": "' . $value->getName() . '", "type":';

			if ($value->getType() === 'folder') {
				$str .=  '"' . $value->getType() . '",' . '"additionalParameters": { "children":[';
				$str .= $this->treeBuild($value);
			} else {
				$str .=  '"item"';
				$str .=  '},';
			}
		}
		$str .=  ']}},';
		return $str;
	}

	/*function yselected($tree) {
		foreach ($tree->getParameters()->getChildrens() as $key => $value) {
			if (!isset($str)) {
				$str = "";
			}

			if ($value->getType() === 'item' && $value->getSelected() === true) {
				$str .=  '{"'. $value->getName() .'": "' . $value->getName() .'"';
			} elseif ($value->getType() === 'folder' && ($value->getChildrensAsSelected() || $value->getSelected())) {
				$str .=  '{"' . $value->getName() .'"';
				$str .= ':{';
				$str .= $this->treeBuild($value, true);
			}


			if ($value->getType() === 'item' && $value->getSelected()) {
				//$str .=  '"item"';
				$str .=  '},';
			}
		}
		$str .=  '}},';
		return $str;
	}*/

	function yselected($tree) {
		foreach ($tree->getParameters()->getChildrens() as $key => $value) {
			// Si no existe lo creamos
			if (!isset($str)) $str = "[!content!]";

			// Si no esta seleccionado entonces vámos por otro elemento.
			if ($value->getSelected() !== true && $value->getChildrensAsSelected() !== true) {
				continue;
			}

			// si no tiene hijos pintamos directamente
			if (!$value->getChildrensAsSelected() && !$value->getParent()) {
				$replace =  '{"' . $value->getName() . '": "' . $value->getName() . '"},[!content!]';
				$str = preg_replace('/\[\!content\!\]/i', $replace, $str);
			}
			// El elemento raíz con hijo esta siendo seleccionado
			elseif ($value->getSelected() && !$value->getParameters()->getChildrens()) {
				$replace =  '{"' . $value->getName() . '": "' . $value->getName() . '"},[!content!]';
				$str = preg_replace('/\[\!content\!\]/i', $replace, $str);
			}
			// Si tiene hijos entonces mostramos con un diferente formato.
			elseif ($value->getChildrensAsSelected()) {
				$replace =  '{"' . $value->getName() . '": [[!childsContent!]},[!content!]}';
				$str = preg_replace('/\[\!content\!\]/i', $replace, $str);

				$childsStr = $this->treeBuild($value, true);

				$str = preg_replace('/\[\!childsContent\!\]/i', $childsStr, $str);
			}
		}
		/*foreach ($tree->getParameters()->getChildrens() as $key => $value) {
			if (!isset($str)) {
				$str = "";
			}

			if ($value->getType() === 'item' && $value->getSelected() === true) {
				$str .=  '{"' . $value->getName() . '": "' . $value->getName() . '",';
			} elseif ($value->getType() === 'folder' && ($value->getChildrensAsSelected() || $value->getSelected())) {
				$str .=  '{"' . $value->getName() . '"';
				$str .=  ':{';
				$str .= $this->treeBuild($value, true);
			}


			if ($value->getType() === 'item' && $value->getSelected()) {
				//$str .=  '"item"';
				$str .=  '},';
			}
		}
		$str .=  '}},';*/
		$str = preg_replace('/,\[\!content\!\]/i', ']', $str);

		return $str;
	}

	/**
	 * Seleccionamos un elemento del arreglo
	 * @return string
	 */
	public function getSetSelectedItem()
	{
	}
}
