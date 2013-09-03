<?php

class Rx_Facade extends RedBean_Facade
{
	/**
	 * @var Rx_FindHelper
	 */
	public static $x;

	public static function setup($dsn = null, $username = null, $password = null, $frozen = false)
	{
		parent::setup( $dsn, $username, $password, $frozen );

		self::$x = new Rx_FindHelper();
	}

	/**
	 * Multi-Purpose Shortcut for handling Beans
	 *
	 * @param mixed $left
	 * @param mixed $right
	 *
	 * @return array|int|\RedBean_OODBBean
	 */
	public static function _( $one, $two=null, $three=null )
	{
		if ( is_object($one) ) return self::store($one);

		if ( empty($two) ) return self::dispense($one);

		if ( is_numeric($two) ) return self::load($one, $two);

		$bean = self::dispense($one);

		foreach ( $two as $k => $v ) {
			$bean->$k = $v;
		}

		if ( $three === true ) $bean->id = self::store($bean);

		$bean->setMeta('fresh', true);

		return $bean;
	}

	/**
	 * Handle multiple databases
	 *
	 * @param $cfg
	 */
	public static function db( $cfg )
	{
		if ( empty(self::$toolboxes) ) {
			self::setup(
				$cfg->type.':host='.$cfg->host.';'
				.'dbname='.$cfg->name,
				$cfg->user,
				$cfg->password
			);
		}

		if ( !isset(self::$toolboxes[$cfg->name]) ) {
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
