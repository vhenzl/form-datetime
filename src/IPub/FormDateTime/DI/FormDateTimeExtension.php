<?php
/**
 * FormDateTimeExtension.php
 *
 * @copyright	More in license.md
 * @license		http://www.ipublikuj.eu
 * @author		Adam Kadlec http://www.ipublikuj.eu
 * @package		iPublikuj:FormDateTime!
 * @subpackage	DI
 * @since		5.0
 *
 * @date		04.12.13
 */

namespace IPub\FormDateTime\DI;

use Nette;
use Nette\DI;
use Nette\PhpGenerator as Code;

class FormDateTimeExtension extends DI\CompilerExtension
{
	/**
	 * @param Code\ClassType $class
	 */
	public function afterCompile(Code\ClassType $class)
	{
		parent::afterCompile($class);

		$initialize = $class->methods['initialize'];
		$initialize->addBody('IPub\FormDateTime\Controls\Date::register();');
		$initialize->addBody('IPub\FormDateTime\Controls\DateTime::register();');
		$initialize->addBody('IPub\FormDateTime\Controls\Time::register();');
	}
}