<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @property String $type Para identificar si es una carpeta o un elemento seleccionable
 * @property String $name Nombre del nodo actual
 * @property [type] [varname] [description]
 */
class TreeFeed {
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
	protected $_selected = false;

	protected static $_nodes = [];

	protected $_jsonValidate;

	public $_dead = false;

	/**
	 * Árbol base
	 * @param stdClass $node Árbol base
	 */
	public function TreeFeed(&$node, $root = false, $jsonValidate, $alias = false) {
		// Instancia
		$this->_jsonValidate = $jsonValidate;
		$inSearch  = call_user_func(function ($items, $index, $root) {
			foreach ($items as $key => $item) {
				if (($item->getName() == $root) && ($item->getSelected()) || $item->getChildrensAsSelected()) {
					unset($items[$index]->$key);
					return TRUE;
				}
			}

			return FALSE;
		}, $jsonValidate[$this::$_depth], $this::$_depth, $root);

		if (!$inSearch) {
			if ($alias) {
				unset($node->$alias);
			} else {
				unset($node);
			}
		}

		if (!isset($node)) {
			$this->_dead = true;
			return;
		}

		// Añade los hijos a la clase
		if ($node instanceof stdClass) {
			$this->setChildrens($node);
			// Decrementa el nivel de profundidad
		} elseif (is_array($node)) {
			new TreeFeed($node[0], $root, $this->_jsonValidate);
		}
	}

	/**
	 * Recorremos cada uno de los elementos del arreglo para poder obtener la información de cada uno
	 * de sus elementos que contiene
	 * @param strClass $node
	 */
	public function setChildrens(stdClass &$node) {
		// iteración
		foreach ($node as $key => $value) {
			if (is_array($value)) {
				$this->setChildrens($value[0]);
				continue;
			}

			$this->toIncrement();

			// Recorrido
			// Si es un objeto entonces creamos otro objeto treebase
			if ($value instanceof stdClass) {
				$children = new TreeFeed($value, $key, $this->_jsonValidate);
			} else {
				$children = new TreeFeed($value, $key, $this->_jsonValidate);
			}

			if ($children->_dead) {
				unset($node->$key);
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
	 * Reiniciamos el nivel de profundidad
	 * @return
	 */
	public function resetDepth() {
		$this->_depth = 0;
	}
}