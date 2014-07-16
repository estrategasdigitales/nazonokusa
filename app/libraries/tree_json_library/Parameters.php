<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Colección de nodos hijos, búsqueda en los nodos hijos para devolver cuando se encuentra
 * @author staff@codebit.com.mx
 * @author aldorodriguez@codebit.com.mx
 */
class Parameters{
	/**
	 * Nodos hijos de un elemento partícular
	 * @var array
	 */
	protected $childrens = [];

	/**
	 * Añadimos un hijo al conjunto de elementos hijos del elemento
	 * @param Tree $children Hijo añadido
	 */
	public function addChildren(Tree $children) {
		array_push($this->childrens, $children);
		return true;
	}

	/**
	 * Devuelve hijo en específico
	 * @param  integer $index
	 * @return Tree
	 */
	public function getChildren($index) {
		if (in_array($index, $this->childrens)) {
			return $this->childrens[$index];
		}

		return $this;
	}

	/**
	 * Devuelve todos los nodos hijos de un elemento nodo
	 * @return Tree[]
	 */
	public function getChildrens() {
		return $this->childrens;
	}

	/**
	 * Elimina un hijo específico
	 * @param  integer $index
	 * @return boolean
	 */
	public function removeChildren($index) {
		if(in_array($index, $this->childrens)) {
			unset($this->childrens[$index]);
			return true;
		}

		return $this;
	}

	/**
	 * Elimina todos los elementos en nodos hijos internos
	 * @return
	 */
	public function removeChildrens() {
		$this->childrens = [];
	}
}