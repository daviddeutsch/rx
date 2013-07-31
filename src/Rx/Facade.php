<?php

class Rx_Facade extends RedBean_Facade
{
	/**
	 * @var Rx_FindHelper
	 */
	public static $x;

	/**
	 * Multi-Purpose Shortcut for handling Beans
	 *
	 * Store a bean:
	 *
	 * R::_( $bean );
	 *
	 * Dispense a bean:
	 *
	 * $type = R::_( 'type' );
	 *
	 * Dispense a bean and inject data:
	 *
	 * $object = new \stdClass();
	 * $object->name = 'name';
	 * $object->data = 'data';
	 *
	 * $type = R::_( 'type', $object );
	 *
	 * Load a bean:
	 *
	 * $type = R::_( 'type', $id );
	 *
	 * @param      $left
	 * @param null $right
	 * @return array|int|\RedBean_OODBBean
	 */
	public static function _( $left, $right=null )
	{
		if ( is_object( $left ) ) {
			return R::store( $left );
		} else {
			if ( empty($right) ) {
				return R::dispense( $left );
			}

			if ( is_int( $right ) ) {
				return R::load( $left, $right );
			} else {
				$bean = R::dispense( $left );

				foreach ( $right as $k => $v ) {
					$bean->$k = $v;
				}

				return $bean;
			}
		}
	}

	/**
	 * Handle multiple databases
	 *
	 * @param $cfg
	 */
	public static function db( $cfg )
	{
		if ( empty( R::$toolboxes ) ) {
			R::setup(
				$cfg->type.':host='.$cfg->host.';'
				.'dbname='.$cfg->name,
				$cfg->user,
				$cfg->password
			);
		}

		if ( !isset( R::$toolboxes[$cfg->name] ) ) {
			R::addDatabase(
				$cfg->name,
				$cfg->type.':host='.$cfg->host.';'
				.'dbname='.$cfg->name,
				$cfg->user,
				$cfg->password
			);
		}

		R::selectDatabase( $cfg->name );
	}
}
