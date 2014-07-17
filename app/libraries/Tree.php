<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once( __DIR__. '/tree_json_library/Parameters.php' );
define("NODE_TYPE_ITEM", 'item');
define("NODE_TYPE_FOLDER", 'folder');

/**
 * @property String $type Para identificar si es una carpeta o un elemento seleccionable
 * @property String $name Nombre del nodo actual
 * @property [type] [varname] [description]
 */
class Tree {
	/**
	 * Tipo de nodo
	 * Si no tiene nodos hijos por defecto entonces es un item, si tuviera nodos hijos entonces
	 * sería un folder
	 * @var
	 */
	protected $_type = NODE_TYPE_ITEM;
	/**
	 * Nombre del elemento
	 * @var
	 */
	protected $_name;
	/**
	 * Parámetros adicionales
	 * @var [type]
	 */
	protected $_additionalParameters;
	/**
	 * Padre del objeto
	 * @var TreeBase
	 */
	protected $_parent;

	/**
	 * Identificador único
	 * @var string
	 */
	private $_identifier;

	/**
	 * Colección de TreeBase que nos indique cuales son los padres del elemento.
	 * @var string
	 */
	protected $_parents = [];
	/**
	 * Dentro del padre
	 * @var Estamos dentro de un padre(?)
	 */
	protected $in_parent = false;

	/**
	 * Nível de profundidad escalada
	 * @var integer
	 */
	protected static $_depth = 0;

	/**
	 * Todos los elementos no estan seleccionados por defecto
	 * @var boolean
	 */
	public $_selected = false;

	protected static $_nodes = [];

	/**
	 * Árbol base
	 * @param stdClass $node Árbol base
	 */
	public function Tree($node, $root = false) {
		$this->_additionalParameters = new Parameters();
		// Es un elemento raíz (?)
		if ($root === true) {
			// Le damos un nombre al elemento
			$this->setIdentifier($node, md5('root'));
		} else {
			$this->setIdentifier($node, $root);
		}

		$this->setName($root);

		// Añade los hijos a la clase
		if ($node instanceof stdClass) {
			//
			if (!isset($this::$_nodes[$this::$_depth])) {
				$this::$_nodes[$this::$_depth] = array();
			}

			array_push($this::$_nodes[$this::$_depth], $this);

			$this->setChildrens($node);
			// Decrementa el nivel de profundidad
		} else {
			if (!isset($this::$_nodes[$this::$_depth])) {
				$this::$_nodes[$this::$_depth] = array();
			}

			array_push($this::$_nodes[$this::$_depth], $this);
		}
	}

	/**
	 * Recorremos cada uno de los elementos del arreglo para poder obtener la información de cada uno
	 * de sus elementos que contiene
	 * @param strClass $node
	 */
	public function setChildrens(stdClass $node) {
		$this->setType(NODE_TYPE_FOLDER);


		// iteración
		foreach ($node as $key => $value) {
			$this->toIncrement();
			// Recorrido
			// Si es un objeto entonces creamos otro objeto treebase
			if ($value instanceof stdClass) {

				$children = new Tree($value, $key);
				// Añadimos el nombre del nodo
				$children->setParent($this);

				$this->_additionalParameters->addChildren($children);
			} else {
				$children = new Tree($value, $key);
				$children->setParent($this);
				$this->_additionalParameters->addChildren($children);
			}
			$this->toDecrement();
		}
	}

	/**
	 * Asignamos el tipo
	 * @param string $type
	 */
	public function setType($type) {
		$this->_type = $type;
	}

	/**
	 * Obtenemos el tipo de dato que se configura
	 * @return string
	 */
	public function getType() {
		return $this->_type;
	}

	/**
	 * Añadimos el nombre
	 * @param string $name
	 */
	public function setName($name) {
		$this->_name = $name;
	}

	/**
	 * Obtenemos el nombre
	 * @return string
	 */
	public function getName() {
		return $this->_name;
	}

	/**
	 * Configuramos si tiene un padre
	 * @param string $parent
	 */
	public function setParent($parent) {
		// Eliminamos todos los padres u objetos que tengan relación

		// Excepción
		if (!$parent instanceof Tree) {
			throw new Exception("No es una instancia de TreeBase");
		}

		// Añadimos al padre
		$this->_parent = $parent;

		return;
	}

	/**
	 * Devuelve el padre en el que estamos situado actualmente
	 * @return TreeBase
	 */
	public function getParent() {
		return $this->_parent;
	}

	/**
	 * Devuelve identificador único
	 * @return string
	 */
	public function getIdentifier() {
		return $this->_identifier;
	}

	/**
	 * Configura el identificador único
	 * @param stdClass $identifier
	 */
	public function setIdentifier($identifier, $provider = null) {
		// si se configura un proveedor de nombres entonces lo asignamos
		if ($provider) {
			$this->_identifier = md5($provider . rand(1000000, 10000000));
			return;
		}
		$this->_identifier = md5($identifier . rand(1000000, 10000000));
	}

	/**
	 * Instancia a toString
	 * @return string
	 */
	public function __toString() {
		return $this->_identifier;
	}

	/**
	 * Incrementa el valor del nivel de profundidad
	 * @return
	 */
	public function toIncrement() {
		$this::$_depth++;
	}

	/**
	 * Decrementa el valor en profundidad
	 * @return
	 */
	public function toDecrement() {
		// Generamos excepción si ya se ha llegado al elementop raíz
		if ($this::$_depth === 0) {
			return;
		}

		$this::$_depth--;
	}

	/**
	 * Retorna los parámetros adicionales
	 * @return AdditionalParametersBase
	 */
	public function getParameters() {
		return $this->_additionalParameters;
	}

	/**
	 * Reiniciamos el nivel de profundidad
	 * @return
	 */
	public function resetDepth() {
		$this->_depth = 0;
	}

	/**
	 * Selecciona el elemento
	 */
	public function setSelected() {
		$this->_selected = true;

		foreach ($this->getParameters()->getChildrens() as $key => $value) {
			$value->setSelected(true);
		}
	}

	/**
	 * Retorna si un elemento ha sido seleccionado
	 * @return boolean
	 */
	public function getSelected() {
		return $this->_selected;
	}

	/**
	 * Devuelve true si uno de sus hijos ha sido seleccionado
	 * @return boolean
	 */
	public function getChildrensAsSelected() {
		foreach ($this->getParameters()->getChildrens() as $key => $value) {
			if ($value->getSelected()){
				return true;
			}

			if ($value->getType() === 'folder') {
				return $value->getChildrensAsSelected();
			}
		}

		return false;
	}

	public function getNodes() {
		return $this::$_nodes;
	}

	public function setNodes($_nodes) {
		$this::$_nodes = $_nodes;
	}
}