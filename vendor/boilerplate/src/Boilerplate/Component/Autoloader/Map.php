<?php namespace Boilerplate\Component\Autoloader;

/**
 * Map class
 *
 * The Map class implements autoloading based on mapping class names to files
 *
 * @package    Boilerplate
 * @subpackage Autoloader
 */
class Map
{
	/**
	 * Class map, where keys are classes and values are absolute file paths
	 *
	 * @var    array
	 */
	private $map = array();

	/**
	 * Constructor
	 *
	 * @param    array            A map where keys are classes and values are absolute file paths
	 * @return   void             No value is returned
	 */
	public function __construct(array $map = array())
	{
		$this->map = $map;
	}

	/**
	 * Gets the current map
	 *
	 * @return   array            The configured map
	 */
	public function getMap()
	{
		return $this->map;
	}

	/**
	 * Appends the class map with additional mappings
	 *
	 * @param    array            An array of classes where keys are class names and values are absolute file paths
	 * @return   void             No value is returned
	 */
	public function registerMap(array $map)
	{
		foreach($map as $class => $location)
		{
			$this->map[$class] = $location;
		}
	}

	/**
	 * Registers this instance to the SPL autoload stack
	 *
	 * @param    boolean          Whether to prepend the autoloader or not
	 * @return   void             No value is returned
	 */
	public function register($prepend = false)
	{
		\spl_autoload_register(array($this, 'load'), true, $prepend);
	}

	/**
	 * Autoload a given class/interface/trait
	 *
	 * @param    string           The name of a class/interface/trait
	 * @return   void             No value is returned
	 */
	public function load($name)
	{
		if($file = $this->getNormalizedPath($name))
		{
			require $file;
		}
	}

	/**
	 * Normalize a class/interface/trait name into a path
	 *
	 * @param    string           The name of a class/interface/trait
	 * @return   string           Returns the matching path
	 */
	public function getNormalizedPath($name)
	{
		if('\\' === $class[0])
		{
			$class = \substr($class, 1);
		}

		if(isset($this->map[$class]))
		{
			return $this->map[$class];
		}
	}
}