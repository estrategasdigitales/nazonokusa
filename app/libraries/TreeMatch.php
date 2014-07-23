<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once(__DIR__ . '/tree_json_library/BaseMatch.php');
/*require_once(__DIR__ . '/tree_json_library/ValidatorLevel.php');

/**
* 
*/
class TreeMatch extends BaseMatch{	
	/**
	 * Objeto a analizar
	 * @var stdClass
	 */
	protected $_json;
	/**
	 * Objeto de validación
	 * @var stdClass
	 */
	protected $_jsonValidate;
	/**
	 * Nivel en el que nos encontramos actualmente
	 * @var integer
	 */
	protected static $_level = -1;

	/**
	 * Se obtienen dos parametros, 
	 * @param String $json         Objeto a analizar
	 * @param String $jsonValidate Objeto de validación
	 */
	public function TreeMatch( $json, $jsonValidate ){
		// Incrementamos valor por ingreso a clase
		$this->next();

		// Instanciamos el objeto a analizar
		$this->_json = $json;
		// Instanciamos objeto validador
		$this->_jsonValidate = $jsonValidate;
		// Determinamos el tipo del parametro actual
		$this->workByType($this->_json);
	}
	/**
	 * Verifica si una clase es un array o un objeto
	 * Si fuera una clase manda obtener sus propiedades las cuales servirán para
	 * match contra las propiedades de nuestro objeto. Incrementa el nivel de 
	 * profundidad de nuestro objeto actual
	 * - Si fuera un arreglo preguntamos si se trata de un unico elemento, si así fuera
	 * accedemos inmediatamente a este para RECURSIVIDAD. Si trajera más de un elemento
	 * entonces iterariamos el contenido para RECURSIVIDAD
	 * @param  stdClass|array $item Elemento a analizar		
	 * @return
	 */
	public function workByType($item) {
		// Elementos en arreglo
		if (is_array($item)) {
			// Obtenemos el número de elementos que contiene el item
			$n = $this->howManyItems($item);

			if ($this->uniqueItem($n)) {
				// Claves de acceso
				$_keys = array_keys($item);
				// Recorremos inmediatamente
				$this->workByType($item[$_keys[0]]);
			} else {
				foreach ($item as $key => $value) {
					$this->workByType($value);
				}
			}
		} else {
			// Eliminamos los elementos que no son necesarios
			$this->remove($item);
		}		
	}

	/**
	 * Devuelve el numero de elementos que contiene el arreglo
	 * @param  array $item Elemento a analizar	
	 * @return integer Número de elementos que contiene
	 */
	public function howManyItems(array $item) {
		return count($item);
	}

	/**
	 * Devuelve true si es un solo elemento y false en caso contrario
	 * @param  array $item Elemento a analizar
	 * @return boolean
	 */
	public function uniqueItem($item) {
		// Si es un arreglo se analiza el número de elementos y en caso de ser 1 
		// devuelve true
		if (is_array($item) && count($item) === 1) {
			return true;
		}
		return false; 
	}

	/**
	 * Devuelve las propiedades que contiene una clase
	 * @param  array|stdClass $item Elemento a analizar
	 * @return array
	 */
	public function getProperties($item, $nowItem) { 
		if (is_array($item)) {
			// Tags permitidos
			$allowed = [];

			foreach ($item as $key => $value) {
				//
				if ($value->getSelected()) {
					array_push($allowed, $value->getName());
				}
			}

			return $allowed;
		}

		return get_object_vars($item);
	}

	/**
	 * Incremento el nivel de profundidad
	 * @return TreeMatch Instancia a la clase actual
	 */
	public function next() {
		$this::$_level++;
	}

	/**
	 * Decrementa el nivel de profundidad
	 * Si el nivel de profundidad es cero no decrementa nada
	 * @return TreeMatch Instancia a la clase actual
	 */
	public function prev() {
		if ($this::$_level === 0) {
			return;
		}
		$this::$_level--;
	}

	/**
	 * Devuelve los elementos que coincidan con la instancia actual
	 * @return array Coincidencias
	 */
	public function match($search, $item) {
		// Obtiene el validador de clase
		$_item = $this->getValidatorByLevel($item);

		// Obtenemos las propiedades que estan permitidas
		$properties = $this->getProperties($_item, $item);

		if (in_array($search, $properties)) {
			return true;
		}
		return false;
	}

	/**
	 * Eliminara todos los elementos que no esten en el listado de getProperties
	 * @return [type] [description]
	 */
	public function remove($item) {		
		// Recorremos item y validamos las propiedas que existen y hacemos un unset sobre
		// las que no existen
		foreach ($item as $key => $value) {
			if (!$this->match($key, $item)) {
				// Eliminamos el elemento
				unset($item->$key);
			}
			// Si es un arreglo o una clase mandamos a llamar a workbytype
			if (is_array($value)) {
				$this->next();
				$this->workByType($value);
				$this->prev();
			} elseif(is_object($value)) {
				$this->next();
				$this->workByType($value);
				$this->prev();
			}
		}
	}

	/**
	 * Devuelve el nivel en el que nos encontramos actualmente
	 * @return integer
	 */
	public function getLevel() {
		return $this::$_level;
	}

	/**
	 * Devuelve un validador en base al nivel actual
	 * @return stdClass
	 */
	public function getValidatorByLevel( $item ) {
		// El número de nivel
		$level  = $this->getLevel();
		return $this->_jsonValidate[ $level + 1 ];
	}
}