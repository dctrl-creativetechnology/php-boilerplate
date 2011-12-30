<?php namespace Boilerplate\Component\Autoloader;

/**
 * Universal class
 *
 * The Universal class implements a "universal" autoloader for PHP 5.3. It is
 * able to load classes that use either:
 *
 *  * The technical interoperability standards for PHP 5.3 namespaces and class
 *    names (https://gist.github.com/1234504).
 *
 *  * The PEAR naming convention for classes (http://pear.php.net/).
 *
 * Classes from a sub-namespace or sub-hierarchy of PEAR classes can be searched
 * for in a list of locations to ease the vendoring of a sub-set of classes for
 * large projects.
 *
 * @package    Boilerplate
 * @subpackage Autoloader
 */
class Universal
{
	/**
	 * Configured PSR-0 namespaces
	 *
	 * @var    array
	 */
	private $namespaces = array();

	/**
	 * Configured PEAR prefixes
	 *
	 * @var    array
	 */
	private $prefixes = array();

	/**
	 * Fallback namespace paths, when a configured namespace does not exist
	 *
	 * @var    array
	 */
	private $namespaceFallbacks = array();

	/**
	 * Fallback PEAR paths, when a configured prefix does not exist
	 *
	 * @var    array
	 */
	private $prefixFallbacks = array();

	/**
	 * Whether to utilize the default include path
	 *
	 * @var    boolean
	 */
	private $useIncludePath = false;

	/**
	 * Turns on searching the include for class files. Allows easy loading of
	 * installed PEAR packages
	 *
	 * @param    boolean          Whether to use the include path
	 * @return   void             No value is returned
	 */
	public function useIncludePath($useIncludePath)
	{
		$this->useIncludePath = (bool) $useIncludePath;
	}

	/**
	 * Determine whether the autoloader uses the include path to check for
	 * classes
	 *
	 * @return   boolean          Returns true if the include path is being used, otherwise false
	 */
	public function getUseIncludePath()
	{
		return $this->useIncludePath;
	}

	/**
	 * Gets the configured namespaces
	 *
	 * @return   array            A hash with namespaces as keys and directories as values
	 */
	public function getNamespaces()
	{
		return $this->namespaces;
	}

	/**
	 * Gets the configured class prefixes
	 *
	 * @return   array            A hash with class prefixes as keys and directories as values
	 */
	public function getPrefixes()
	{
		return $this->prefixes;
	}

	/**
	 * Gets the directory(ies) to use as a fallback for namespaces.
	 *
	 * @return   array            An array of directories
	 */
	public function getNamespaceFallbacks()
	{
		return $this->namespaceFallbacks;
	}

	/**
	 * Gets the directory(ies) to use as a fallback for class prefixes
	 *
	 * @return   array            An array of directories
	 */
	public function getPrefixFallbacks()
	{
		return $this->prefixFallbacks;
	}

	/**
	 * Registers the directory(ies) to use as a fallback for namespaces
	 *
	 * @param    array            An array of directories
	 * @return   void             No value is returned
	 */
	public function registerNamespaceFallbacks(array $directories)
	{
		$this->namespaceFallbacks = $directories;
	}

	/**
	 * Registers the directory(ies) to use as a fallback for class prefixes
	 *
	 * @param    array            An array of directories
	 * @return   void             No value is returned
	 */
	public function registerPrefixFallbacks(array $directories)
	{
		$this->prefixFallbacks = $directories;
	}

	/**
	 * Registers an array of namespaces
	 *
	 * @param    array            An array of namespaces (namespaces as keys and locations as values)
	 * @return   void             No value is returned
	 */
	public function registerNamespaces(array $namespaces)
	{
		foreach($namespaces as $namespace => $locations)
		{
			$this->namespaces[$namespace] = (array) $locations;
		}
	}

	/**
	 * Registers a namespace
	 *
	 * @param    string           The namespace
	 * @param    string|array     The location(s) of the namespace
	 * @return   void             No value is returned
	 */
	public function registerNamespace($namespace, $locations)
	{
		$this->namespaces[$namespace] = (array) $locations;
	}

	/**
	 * Registers an array of classes using the PEAR naming convention.
	 *
	 * @param    array            An array of classes (prefixes as keys and locations as values)
	 * @return   void             No value is returned
	 */
	public function registerPrefixes(array $classes)
	{
		foreach($classes as $prefix => $locations)
		{
			$this->prefixes[$prefix] = (array) $locations;
		}
	}

	/**
	 * Registers a set of classes using the PEAR naming convention
	 *
	 * @param    string           The classes prefix
	 * @param    string|array     The location(s) of the classes
	 * @return   void             No value is returned
	 */
	public function registerPrefix($prefix, $locations)
	{
		$this->prefixes[$prefix] = (array) $paths;
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
		if('\\' == $name[0])
		{
			$name = \substr($name, 1);
		}

		// Namespaced class name
		if(false !== ($pos = \strrpos($name, '\\')))
		{
			$namespace = \substr($name, 0, $pos);
			$className = \substr($name, ($pos + 1));

			$normalizedClass = \str_replace('\\', \DIRECTORY_SEPARATOR, $namespace).\DIRECTORY_SEPARATOR.\str_replace('_', \DIRECTORY_SEPARATOR, $className).'.php';

			foreach($this->namespaces as $ns => $dirs)
			{
				if(0 !== \strpos($namespace, $ns))
				{
					continue;
				}

				foreach($dirs as $dir)
				{
					if(\is_file($file = $dir.\DIRECTORY_SEPARATOR.$normalizedClass))
					{
						return $file;
					}
				}
			}

			foreach($this->namespaceFallbacks as $dir)
			{
				if(\is_file($file = $dir.\DIRECTORY_SEPARATOR.$normalizedClass))
				{
					return $file;
				}
			}
		}
		// PEAR-like class names
		else
		{
			$normalizedClass = \str_replace('_', \DIRECTORY_SEPARATOR, $name).'.php';

			foreach($this->prefixes as $prefix => $dirs)
			{
				if(0 !== \strpos($name, $prefix))
				{
					continue;
				}

				foreach($dirs as $dir)
				{
					if(\is_file($file = $dir.\DIRECTORY_SEPARATOR.$normalizedClass))
					{
						return $file;
					}
				}
			}

			foreach($this->prefixFallbacks as $dir)
			{
				if(\is_file($file = $dir.\DIRECTORY_SEPARATOR.$normalizedClass))
				{
					return $file;
				}
			}
		}

		if($this->useIncludePath === true and $file = \stream_resolve_include_path($normalizedClass))
		{
			return $file;
		}
	}
}