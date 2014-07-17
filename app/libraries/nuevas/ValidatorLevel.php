<?php  

/**
* 
*/
class ValidatorLevel extends BaseMatch
{	
	/**
	 * Objeto de validación
	 * @var stdClass
	 */
	protected $_jsonValidate;
	/**
	 * Nivel en el que nos encontramos actualmente
	 * @var integer
	 */
	protected static $_level = 0;

	/**
	 * Nivel requerido
	 * @var integer
	 */
	protected $_levelRequired = 0;

	/**
	 * Se obtienen dos parametros, 
	 * @param String $json         Objeto a analizar
	 * @param integer $levelRequired Nivel requerido de profundidad
	 * @param String $jsonValidate Objeto de validación
	 */
	public function ValidatorLevel($itemNow, $jsonValidate, $levelRequired, $increment = true) {
		if ($increment) {
			// Incrementamos valor por ingreso a clase
			$this->next();
			echo $this->getLevel();exit;
		}

		// Instanciamos objeto validador
		$this->_jsonValidate = $jsonValidate;
		// Determinamos el tipo del parametro actual
		$this->workByType($this->_jsonValidate);

		// Nivel requerido
		$this->_levelRequired = $levelRequired;
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
				// Si es una cadena entonces es un alias, si no entonces es el objeto validador
				if (is_string($_keys[0])) {
					// Recorremos inmediatamente
					$this->workByType($item[$_keys[0]]);
				} else {
					if ($this->getLevel() === $this->_levelRequired) {
						// Si es igual ya no iteramos, se acaba la ejecución y seteamos el nodo donde se
						// presenta el validador.
						// 
						$this->setLevelItem($item);

						return;
					}
				}
			}
		} else {
			if ($this->getLevel() === $this->_levelRequired) {
				// Si es igual ya no iteramos, se acaba la ejecución y seteamos el nodo donde se
				// presenta el validador.
				// 
				$this->setLevelItem($item);

				return;
			} 
			// Iteramos
			$validatorLevel = new ValidatorLevel($item);
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
		if (count($item) === 1) {
			return true;
		}
		return false; 
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
	 * Devuelve el nivel en el que nos encontramos actualmente
	 * @return integer
	 */
	public function getLevel() {
		return $this::$_level;
	}

	/**
	 * Configura el nodo del elemento requerido
	 * @param stdClass $item Elemento encontrado
	 */
	public function setLevelItem($item) {
		$this->_itemRequired = $item;
	}

	/**
	 * Devuelve el elemento encontrado requerido
	 * @return stdClass
	 */
	public function getLevelItem() {
		return $this->_itemRequired;
	}

	/**
	 * Configuramos el nivel actual
	 */
	public function setLevel($level) {
		$this::$_level = $level;
	}
}