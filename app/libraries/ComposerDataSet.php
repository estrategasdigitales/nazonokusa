<?php 
require( __DIR__.'/composer/IComposer.php');

/**
* Implementa la funcionalidad base para la interacción con cada uno de los nodos de respuesta del
* sistema
*/
class ComposerDataSet implements IComposer
{
	// Nombre del campo
	const NODE_NAME = "name";
	// Tipo de campo, folder|item
	const NODE_TYPE = "type";
	// Padre del nodo
	const NODE_PARENT = "parent";

	/**
	 * Indica si el nodo actual esta dentro de un padre
	 * @var boolean
	 */
	protected $_inParent = false;

	/**
	 * Listado de padres actuales
	 *
	 * Cuando se sale de un nivel se elimina un padre de array
	 * @var array
	 */
	protected $_parents = [];

	/**
	 * Índica la profundidad en la que un nodo se encuentra actualmente con respecto a su padre raíz
	 * @var integer
	 */
	protected $depth = 0;

	/**
	 * Armado de los nodos con sus diferentes niveles de profundidad
	 * @var array
	 */
	protected $nodes = [];

	/**
	 * Origen de datos para comparativa
	 * @var array
	 */
	private $__write;

	/**
	 * Añade un elemento a la colección del arreglo base para la sálida del json
	 * Si el elemento es un stdClass se añade como elemento padre e hijo a la vez.
	 * @param array|stdClass $item [description]
	 */
	public function addItem($item, $index) {
		// Cuando añadimos un nuevo nodo primero deberemos comprobar que en el nivel actual, no se
		// encuentra un nodo con estas características
		if (array_search($index, $this->nodes)) {
			// Encontró otro elemento con el mismo nombre esto significa que este elemento se encuentra
			// a otro nivel
		}

		// Si no encontro otro elemento con el mismo nombre entonces significa que es un elemento nuevo
		// al nivel actual $this->depth. Por lo cual lo añadiremos como clave asignada
		$__key = $this->_generateKey($index);
		// padre del elemento
		$__parent = false;

		if ($this->_inParent) {
			// Copia local de padres
			$__parents = $this->getParents();
			// Eliminamos el último elemento
			array_pop($__parents);

			// Buscamos la clave del padre
			$__parent = sprintf('kgrt%s_%s',  join('__', $__parents), $this->getParent());

			// Entonces significa que la clave no la vamos a encontrar directamente sobre la búsqueda de parent
			if ($this->depth > 1) {
				$t_parents = [];

				$collectionParents = "";
				// Obtenemos el parent, reacomodo
				foreach($this->getParents() as $key => $_iparent) {
					// Añadimos
					array_push($t_parents, $_iparent);
					// Si es mayor que cero entonces tiene padre
					if ($key > 0) {
						$getParent = $this->getParents()[$key - 1];
					}
					// Si esta configurado getParent entonces concatenamos
					if (isset($getParent)) {
						$__parent = sprintf('kgrt_%s', $this->getParents()[$this->depth - 1]);
					} else {
						$__parent = sprintf('kgrt_%s',  join('__', $t_parents));
					}
				}
				// 
				$tmpParents = $this->getParents();
				// Reinvertimos el arreglo
				$tmpParents = array_reverse($tmpParents);
				//
				$itemDelete = array_pop($tmpParents);
				// Eliminamos el primer elemento (ahora el último)
				$tmpParents = array_reverse($tmpParents);
				// Consulta para nodos
				$query = "['kgrt_".$itemDelete . "']['additionalParameters']['children']['kgrt_".join("']['", $tmpParents) . "']";
			}

			if (isset($query)) {
				//eval('$this->nodes' . $query . ';')
				return;
			}

			// Si el padre esta habilitado y el elemento se encuentra entonces añadimos a nodo específico
			// Si no existe additionalParameters entonces añadimos
			if (!array_key_exists('additionalParameters', $this->nodes[$__parent])) {
				$this->nodes[$__parent]['additionalParameters'] = [];
			}

			// Si no esta añadido children entonces añadimos
			if (!array_key_exists('children', $this->nodes[$__parent]['additionalParameters'])) {
				$this->nodes[$__parent]['additionalParameters']['children'] = [];
			}

			// Añadimos los parámetros adicionales
			$this->nodes[$__parent]['additionalParameters']['children'][$__key] = [
				$this::NODE_NAME => $index,
				// Por defecto es un elemento hasta que no se demuestra lo contrario por el número de hijos
				$this::NODE_TYPE => 'item',
				$this::NODE_PARENT => $__parent
			];

			return;
		}

		// clave asignada al arreglo.
		$this->nodes[$__key] = [
			$this::NODE_NAME => $index,
			// Por defecto es un elemento hasta que no se demuestra lo contrario por el número de hijos
			$this::NODE_TYPE => 'item',
		];
	}

	/**
	 * Obtiene el padre del elemento actual
	 * @param string $itemCTF Nombre del elemento único que se ha asignado al arreglo.
	 * @return array
	 */
	public function getParent() {
		$depth = 0;
		// Si solo tiene 0 elementos retornamos, ya que no hay padres
		if (count($this->getParents()) === 0) {
			return;
		}
		// Si hay más de un elemento
		if ($this->depth > 0) {
			$depth = $this->depth - 1;
		}
		return $this->getParents()[$depth];
	}

	/**
	 * Devuelve los padres del nodo
	 * @param string $itemCTF Nombre del elemento único que se ha asignado al arreglo.
	 * @return array()
	 */
	public function getParents() {
		return $this->_parents;
	}

	/**
	 * Devuelve el listado de nodos hijos para el elemento actual
	 * @param string $itemCTF Nombre del elemento único que se ha asignado al arreglo.
	 * @return array
	 */
	public function getChilds($itemCTF) {}

	/**
	 * Devuelve el nombre del elemento del arreglo
	 * @param  array $itemCTF
	 * @return string
	 */
	public function getName($itemCTF) {}

	/**
	 * Añadimos la configuración para el lector y comparativo del extracto de datos.
	 * @param array
	 */
	public function setWriter($write) {
		$this->__write = json_decode(json_encode($write), true);
	}

	/**
	 * Configura la instancia como hijo de otro elemento
	 * @param boolean $inParent En verdadero indica que se encuentra como hijo de otro elemento
	 */
	public function setInParent($inParent) {}

	/**
	 * Incrementa en uno la suma de nodos de profundidad en los que se encuentra el nodo actual.
	 *
	 * Cuando un nodo es hijo de otro nodo entonces incrementamos en uno siendo 0 -> 1
	 * Si este ingresará en otro nivel de profundidad entonces incrementaremos a 1
	 * Cuando nos encontramos en el nivel 1 y este sale del nivel de profundidad actual entonces res-
	 * taremos.
	 * @return
	 */
	public function incrementDepth() {
		$this->depth++;
	}

	/**
	 * Decrementa en uno la suma de nodos de profundidad en las que se encuentra el nodo actual.
	 *
	 * Cuando un nodo sale de la iteración y regresa un nivel atrás de profundidad entonces restamos
	 * uno
	 * @return
	 */
	public function decrementDepth() {
		// Si es igual a cero ya no decrementamos por que estamos en raíz
		if ($this->depth == 0) return;

		$this->depth--;
		// eliminamos el padre último
		array_pop($this->_parents);
		// Entonces quitamos estado en padres
		if ($this->depth === 0) {
			$this->_inParent = false;
		}
	}

	/**
	 * Devuelve el listado de nodos armados
	 * @return array
	 */
	public function getNodes() {
		return $this->nodes;
	}

	/**
	 * Añade un nuevo elemento padre a la colección
	 * @param array $item
	 */
	public function addParent($index) {
		$this->_inParent = true;
		array_push($this->_parents, $this->_generateKey($index, true));
	}

	/**
	 * Cuando se genera una clave, se genera a través del índice del nivel actual de profundidad, con-
	 * catenamos los padres del elemento + el
	 * nombre del elemento index
	 *
	 * @example
	 * - Si tenemos el indice de profundidad 0,
	 * - Si tenemos como elementos padres []
	 * - Si tenemos como nombre del elemento id
	 * 
	 * Entonces la salida sería similar a: 'kgrt_0__id' + una sencilla codificación para generar estos
	 * elementos únicos
	 * @return string
	 */
	public function _generateKey($index, $parent = false) {
		$__base = "kgrt_{$this->depth}_";
		// Si es padre entonces omitimos prefix
		if ($parent) {
			$__base = "{$this->depth}_";
		}
		// Concatenamos padres
		$__base .= join('___', $this->_parents);

		// Concatenamos el indice
		$__base .= "_{$index}";


		return $__base;
	}
}