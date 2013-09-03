<?php

use Rx_Facade as R;

class Rx_FindHelper
{
	protected $type;

	protected $params = array();

	protected $params_plain = array();

	protected $search = array();

	protected $order = array();

	protected $related = array();

	protected $find = '';

	/**
	 * Main Find Helper function that concludes a search, returning results
	 *
	 * @return array|RedBean_OODBBean
	 */
	public function find( $force_make = false, $force_array = false )
	{
		if ( empty( $this->related ) ) {
			$ft = 'find' . ucfirst( $this->find );

			if ( $this->params ) {
				$r = R::$ft( $this->type, $this->makeQuery(), $this->params );
			} else {
				$r = R::$ft( $this->type );
			}
		} else {
			if ( $this->find == 'all' ) $this->find = '';

			$rt = 'related' . ucfirst( $this->find );

			if ( $this->params ) {
				$r = R::$rt( $this->related[0], $this->type, $this->makeQuery(), $this->params );
			} else {
				$r = R::$rt( $this->related[0], $this->type );
			}

			if ( count($r) && ( count( $this->related ) > 1 ) ) {
				foreach ( $r as $k => $b ) {
					if ( $k === 0 ) continue;

					foreach ( $this->related as $bean ) {
						if ( !R::areRelated( $b, $bean ) ) {
							unset( $r[$k] );
						}
					}
				}
			}
		}

		if ( !is_array( $r ) && !empty( $r ) ) $r = array( $r );

		if ( $force_make && empty( $r ) ) {
			$r = array( R::_( $this->type, $this->params_plain, true ) );

			if ( !empty( $this->related ) ) {
				R::associate( $r[0], $this->related );
			}
		}

		$this->free();

		if ( ( count( $r ) > 1 ) || $force_array ) {
			return $r;
		} else {
			return array_pop($r);
		}
	}

	/**
	 * Pretty much the same as find(), just for counting beans
	 *
	 * @return int
	 */
	public function count()
	{
		if ( empty( $this->related ) ) {
			$r = R::count( $this->type, $this->makeQuery(), $this->param );
		} else {
			$r = 0;
			foreach ( $this->related as $bean ) {
				$r += R::relatedCount( $bean, $this->type, $this->makeQuery(), $this->param );
			}
		}

		$this->free();

		return $r;
	}

	public function makeQuery()
	{
		$search = $order = $limit = '';

		if ( !empty( $this->search ) ) {
			$search = implode( ' AND ', $this->search );
		}

		if ( !empty( $this->order ) ) {
			$order = ' ORDER BY ' . $this->order . ' ';
		}

		if ( !empty( $this->limit ) ) {
			$limit = ' LIMIT ' . $this->limit . ' ';
		}

		return ' ' . $search . $order . $limit . ' ';
	}

	/**
	 * Add find parameters based on array or object passed into the function
	 *
	 * @param $item
	 *
	 * @return $this
	 */
	public function like( $item )
	{
		$temp = $this;

		foreach ( $item as $k => $v ) {
			$temp = $temp->$k( $v );
		}

		return $temp;
	}

	/**
	 * Instead of carrying out a search, return an Iterator that
	 * can be used in a foreach loop
	 *
	 * foreach( R::$x->user->age(26) as $user ) {
	 *     // Do something
	 * }
	 */
	public function iterate()
	{
		// TODO!
		//$ps = R::$adapter->$db->query("SELECT * FROM accounts");

		/*return new NoRewindIterator(
			new IteratorIterator( $ps )
		);*/
	}

	public function free()
	{
		foreach ( $this as $k => $v ) {
            $this->$k = is_array( $v ) ? array() : null;
		}
	}

	public function last()
	{
		$this->find = 'last';

		return $this;
	}

	public function all()
	{
		$this->find = 'all';

		return $this;
	}

	public function one()
	{
		$this->find = 'one';

		return $this;
	}

	public function order( $by )
	{
		$this->order = $by;

		return $this;
	}

	public function limit( $limit, $limit2 = null )
	{
		if ( $limit2 ) {
			$this->limit = $limit . ',' . $limit2;
		} else {
			$this->limit = $limit;
		}

		return $this;
	}

	public function related( $bean )
	{
		if ( !is_object( $bean ) ) return $this;

		if ( is_array( $bean ) ) {
			$this->related = array_merge( $this->related, $bean );
		} else {
			$this->related[] = $bean;
		}

		return $this;
	}

	public function __get( $name )
	{
		if ( method_exists( $this, $name ) ) {
			return $this->$name();
		} else {
			return $this->__call( $name, array() );
		}
	}

	/**
	 * Args for constructing a find:
	 *
	 * [0] Data to search for
	 * [1]
	 * [2] Override the comparator, default being '='
	 *
	 * @param $name
	 * @param $args
	 *
	 * @return $this
	 */
	public function __call( $name, $args )
	{
		if ( empty( $args ) ) {
			$this->type = $name;

			return $this;
		}

		if ( is_array( $args[0] ) ) {
			$names = array();
			foreach ( $args[0] as $k => $v ) {
				$n = ':' . $name . $k;

				$this->params[$n] = $v;

				$names[] = $n;
			}

			$this->search[] = $name . ' IN (' . implode( ',', $names ) . ')';

			$this->params_plain[$name] = $args[0];
		} else {
			if ( isset( $args[2] ) ) {
				$c = $args[2];
			} else {
				$c = '=';
			}

			$this->search[] = $name . ' ' . $c . ' :' . $name;

			$this->params[':' . $name] = $args[0];

			$this->params_plain[$name] = $args[0];
		}

		return $this;
	}
}
