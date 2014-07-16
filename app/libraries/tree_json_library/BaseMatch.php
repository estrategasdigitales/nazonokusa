<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
* 
*/
class BaseMatch{
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
				// Recorremos inmediatamente
				$this->workByType($item[0]);
			}
		} else {
			// Eliminamos los elementos que no son necesarios
			//$this->remove($item);
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
}