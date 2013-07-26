<?php

class Rx_FindHelper
{
	protected $type;

	protected $params = array();

	protected $search = array();

	protected $order = array();

	protected $find = 'find';

	/**
	 * Example:
	 *
	 * R::find( 'branch_commit', ' branch_id = ? ', array($branch->id) );
	 *
	 * Would become:
	 *
	 * Rx::$x->branch_commit
	 *      ->branch_id($branch->id)
	 *      ->find();
	 *
	 * Or:
	 *
	 * R::findLast(
	 *          'package',
	 *          ' name = :name AND major = :major AND minor = :minor ',
	 *          array(
	 *             ':name' => $name,
	 *             ':major' => $version[0],
	 *             ':minor' => $version[1]
	 *          )
	 *   );
	 *
	 * Would become:
	 *
	 * Rx::$x->last->package
	 *      ->name($name)
	 *      ->version_major($version[0])
	 *      ->version_minor($version[1])
	 *      ->find();
	 */
	public function find()
	{
		$params = array();

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

		$f = $this->find;

		if ( $params ) {
			$sql = ' '.$search.$order.$limit.' ';

			$r = R::$f( $this->type, $sql, $params );
		} else {
			$r = R::$f( $this->type );
		}

		$this->free();

		return $r;
	}

	/**
	 * Add find parameters based on array or object passed into the function
	 *
	 * Say you have an array like so:
	 *
	 * $array = [ "test" => "data", "test2" => "data2" ];
	 *
	 * And you want to figure out whether there is a bean of the type 'thing',
	 * instead of doing a
	 *
	 * R::$x->thing->test("data")->test2("data2")->find();
	 *
	 * You can just do
	 *
	 * R::$x->like('thing', $array)->find();
	 *
	 * (obviously a lot more useful with larger objects or arrays)

	 * @param $type
	 * @param $item
	 * @return $this
	 */
	public function like( $type, $item )
	{
		$temp = $this->$type;

		foreach ( $item as $k => $v ) {
			$temp = $temp->$k($v);
		}

		return $temp;
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
		$this->order = ':order_by_value';

		$this->params[':order_by_value'] = $by;

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

	public function __get( $name )
	{
		if ( method_exists( $this, $name ) ) {
			return $this->$name();
		} else {
			return $this->__call( $name, array() );
		}
	}

	public function __call( $name, $args )
	{
		if ( empty( $args ) ) {
			$this->type = $name;
		} else {
			if ( is_array( $args[0] ) ) {
				$this->search[] = $name.' IN (:'.$name.')';

				$this->params[':'.$name] = implode( $args[0] );
			} else {
				$this->search[] = $name.' = :'.$name;

				$this->params[':'.$name] = $args[0];
			}
		}

		return $this;
	}
}
