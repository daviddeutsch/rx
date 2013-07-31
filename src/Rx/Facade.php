<?php

class Rx_Facade extends RedBean_Facade
{
	/**
	 * @var Rx_FindHelper
	 */
	public static $x;

	public static function setup($dsn = null, $username = null, $password = null, $frozen = false)
	{
		parent::setup($dsn, $username, $password, $frozen);

		self::$x = new Rx_FindHelper();
	}

	/**
	 * Multi-Purpose Shortcut for handling Beans
	 *
	 * Store a bean:
	 *
	 * self::_( $bean );
	 *
	 * Dispensing a bean:
	 *
	 * $type = self::_( 'type' );
	 *
	 * Dispense a bean and inject data:
	 *
	 * $object = new \stdClass();
	 * $object->name = 'name';
	 * $object->data = 'data';
	 *
	 * $type = self::_( 'type', $object );
	 *
	 * Load a bean:
	 *
	 * $type = self::_( 'type', $id );
	 *
	 * @param      $left
	 * @param null $right
	 * @return array|int|\RedBean_OODBBean
	 */
	public static function _( $left, $right=null )
	{
		if ( is_object( $left ) ) {
			return self::store( $left );
		} else {
			if ( empty($right) ) {
				return self::dispense( $left );
			}

			if ( is_int( $right ) ) {
				return self::load( $left, $right );
			} else {
				$bean = self::dispense( $left );

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
		if ( empty( self::$toolboxes ) ) {
			self::setup(
				$cfg->type.':host='.$cfg->host.';'
				.'dbname='.$cfg->name,
				$cfg->user,
				$cfg->password
			);
		}

		if ( !isset( self::$toolboxes[$cfg->name] ) ) {
			self::addDatabase(
				$cfg->name,
				$cfg->type.':host='.$cfg->host.';'
				.'dbname='.$cfg->name,
				$cfg->user,
				$cfg->password
			);
		}

		self::selectDatabase( $cfg->name );
	}
}
