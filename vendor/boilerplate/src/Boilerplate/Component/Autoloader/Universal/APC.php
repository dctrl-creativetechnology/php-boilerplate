<?php namespace Boilerplate\Component\Autoloader\Universal;

/**
 * APC class
 *
 * The APC class implements a "universal" autoloader cached in APC for PHP 5.3
 *
 * @see        Boilerplate\Component\Autoloader\Universal
 * @package    Boilerplate
 * @subpackage Autoloader
 */
class APC extends \Boilerplate\Component\Autoloader\Universal
{
	/**
	 * APC cache key prefix
	 *
	 * @var    string
	 */
	private $prefix;

	/**
	 * Constructor
	 *
	 * @param    string           A prefix to create a namespace in APC
	 * @return   void             No value is returned
	 */
	public function __construct($prefix = null)
	{
		if(!\extension_loaded('apc'))
		{
			throw new \RuntimeException('Unable to use Boilerplate\\Component\\Autoloader\\Universal\\APC as APC is not enabled.');
		}

		$this->prefix = $prefix;
	}

	/**
	 * Normalize a class/interface/trait name into a path
	 *
	 * @param    string           The name of a class/interface/trait
	 * @return   string           Returns the matching path
	 */
	public function getNormalizedPath($name)
	{
		if(false === ($file = \apc_fetch($this->prefix.$name)))
		{
			\apc_store($this->prefix.$class, ($file = parent::getNormalizedPath($name)));
		}

		return $file;
	}
}