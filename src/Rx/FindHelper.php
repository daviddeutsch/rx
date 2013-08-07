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

	protected $find = 'find';

	/**
	 * Main Find Helper function that concludes a search, returning results
	 */
	public function find( $force=false )
	{
		$ft = $this->find;

		if ( $this->params ) {
			$r = R::$ft( $this->type, $this->makeQuery(), $this->params );
		} else {
			$r = R::$ft( $this->type );
		}

		if ( !is_array($r) ) {
			$r = array($r);
		}

		// If we are looking for related beans, filter out unrelated ones
		if ( !empty( $this->related ) ) {
			foreach ( $r as $k => $b ) {
				foreach ( $this->related as $bean ) {
					if ( !R::areRelated( $b, $bean ) ) {
						unset( $r[$k] );
					}
				}
			}
		}

		if ( $force && empty($r) ) {
			$r = array( R::_($this->type, $this->params_plain, true) );

			if ( !empty( $this->related ) ) {
				foreach ( $this->related as $bean ) {
					R::associate( $r[0], $bean );
				}
			}
		}

		$this->free();

		if ( count($r) > 1 ) {
			return $r;
		} else {
			return $r[0];
		}
	}

	/**
	 * Pretty much the same as find(), just for counting beans
	 *
	 * @return int
	 */
	public function count()
	{
		$r = R::count( $this->type, $this->makeQuery(), $this->param );

		$this->free();

		return $r;
	}

	public function makeQuery()
	{
		$search = $order = $limit = '';

		if ( !empty( $this->search ) ) {
			$search = implode( ', ', $this->search );
		}

		if ( !empty( $this->order ) ) {
			$order = ' ORDER BY '.$this->order.' ';
		}

		if ( !empty( $this->limit ) ) {
			$limit = ' LIMIT '.$this->limit.' ';
		}

		return ' '.$search.$order.$limit.' ';
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
			$temp = $temp->$k($v);
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
			if ( is_array( $v ) ) {
				$this->$k = array();
			} else {
				$this->$k = null;
			}
		}
	}

	public function last()
	{
		$this->find = 'findLast';

		return $this;
	}

	public function all()
	{
		$this->find = 'findAll';

		return $this;
	}

	public function one()
	{
		$this->find = 'findOne';

		return $this;
	}

	public function order( $by )
	{
		$this->order = $by;

		return $this;
	}

	public function limit( $limit, $limit2=null )
	{
		if ( $limit2 ) {
			$this->limit = $limit.','.$limit2;
		} else {
			$this->limit = $limit;
		}

		return $this;
	}

	public function related( $bean )
	{
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
	 * @param $name
	 * @param $args
	 * @return $this
	 */
	public function __call( $name, $args )
	{
		if ( empty( $args ) ) {
			$this->type = $name;
		} else {
			if ( is_array( $args[0] ) ) {
				$this->search[] = $name.' IN (:'.$name.')';

				$this->params[':'.$name] = implode( $args[0] );

				$this->params_plain[$name] = implode( $args[0] );
			} else {
				if ( isset( $args[2] ) ) {
					$c = $args[2];
				} else {
					$c = '=';
				}

				$this->search[] = $name.' '.$c.' :'.$name;

				$this->params[':'.$name] = $args[0];

				$this->params_plain[$name] = $args[0];
			}
		}

		return $this;
	}
}
