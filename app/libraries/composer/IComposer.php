<?php defined('BASEPATH') OR exit('No direct script access allowed');

	interface IComposer {
		/**
		 * Añade un elemento a la colección del arreglo base para la sálida del json
		 * Si el elemento es un stdClass se añade como elemento padre e hijo a la vez.
		 * @param array|stdClass $item [description]
		 */
		public function addItem($item, $index);

		/**
		 * Obtiene el padre del elemento actual
		 * @param string $itemCTF Nombre del elemento único que se ha asignado al arreglo.
		 * @return array
		 */
		public function getParent();

		/**
		 * Devuelve los padres del nodo
		 * @param string $itemCTF Nombre del elemento único que se ha asignado al arreglo.
		 * @return array()
		 */
		public function getParents();

		/**
		 * Devuelve el listado de nodos hijos para el elemento actual
		 * @param string $itemCTF Nombre del elemento único que se ha asignado al arreglo.
		 * @return array
		 */
		public function getChilds($itemCTF);

		/**
		 * Devuelve el nombre del elemento del arreglo
		 * @param  array $itemCTF
		 * @return string
		 */
		public function getName($itemCTF);

		/**
		 * Configura la instancia como hijo de otro elemento
		 * @param boolean $inParent En verdadero indica que se encuentra como hijo de otro elemento
		 */
		public function setInParent($inParent);

		/**
		 * Incrementa en uno la suma de nodos de profundidad en los que se encuentra el nodo actual.
		 *
		 * Cuando un nodo es hijo de otro nodo entonces incrementamos en uno siendo 0 -> 1
		 * Si este ingresará en otro nivel de profundidad entonces incrementaremos a 1
		 * Cuando nos encontramos en el nivel 1 y este sale del nivel de profundidad actual entonces res-
		 * taremos.
		 * @return
		 */
		public function incrementDepth();

		/**
		 * Decrementa en uno la suma de nodos de profundidad en las que se encuentra el nodo actual.
		 *
		 * Cuando un nodo sale de la iteración y regresa un nivel atrás de profundidad entonces restamos
		 * uno
		 * @return
		 */
		public function decrementDepth();
}