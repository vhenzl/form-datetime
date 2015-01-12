<?php
/**
* Test: IPub\Forms\DateTimeInput
* @testCase
*
 * @copyright	More in license.md
 * @license		http://www.ipublikuj.eu
 * @author		Adam Kadlec http://www.ipublikuj.eu
 * @package		iPublikuj:FormDateTime!
 * @subpackage	Tests
 * @since		5.0
 *
 * @date		12.01.15
 */

namespace IPub\Forms\DateTime;

use Nette;
use Nette\Forms;
use Nette\Utils;

use Tester;
use Tester\Assert;

use IPub;
use IPub\FormDateTime;

require __DIR__ . '/../../bootstrap.php';

class DateInputTest extends Tester\TestCase
{
	/**
	 * @return array[]|array
	 */
	public function dataValidInputValues()
	{
		return array(
			array(NULL, NULL),
			array(new Utils\DateTime('2015-01-10 00:00:00'), new Utils\DateTime('2015-01-10 00:00:00')),
			array(new \DateTime('2015-01-10 00:00:00'), new \DateTime('2015-01-10 00:00:00')),
		);
	}

	/**
	 * @return array[]|array
	 */
	public function dataInvalidInputValues()
	{
		return array(
			array(FALSE),
			array('1978/01/23'),
			array(254358000),
		);
	}

	/**
	 * @dataProvider dataValidInputValues
	 *
	 * @param \DateTime|NULL
	 * @param \DateTime|NULL
	 */
	public function testValidInputs($input, $expected)
	{
		$control = new FormDateTime\Controls\Date;
		$control->setValue($input);

		Assert::equal($expected, $control->getValue());
	}

	/**
	 * @dataProvider dataInvalidInputValues
	 *
	 * @param string
	 *
	 * @throws \Nette\InvalidArgumentException
	 */
	public function testInvalidInputs($input)
	{
		$control = new FormDateTime\Controls\Date;
		$control->setValue($input);
	}

	public function testHtml()
	{
		// Create form control
		$control = $this->createControl();
		// Set form control value
		$control->setValue(new Utils\DateTime('2015-01-10 00:00:00'));

		$dq = Tester\DomQuery::fromHtml((string) $control->getControl());

		Assert::true($dq->has("input[value='2015-01-10']"));
	}

	/**
	 * @throws Nette\InvalidStateException
	 */
	public function testMultipleRegistration()
	{
		FormDateTime\Controls\Date::register();
		FormDateTime\Controls\Date::register();
	}

	public function testRegistration()
	{
		FormDateTime\Controls\Date::register();

		// Create form
		$form = new Forms\Form;
		// Create form control
		$control = $form->addDatePicker('date', 'Date picker');

		Assert::type('IPub\FormDateTime\Controls\Date', $control);
		Assert::equal('date', $control->getName());
		Assert::equal('Date picker', $control->caption);
		Assert::same($form, $control->getForm());
	}

	/**
	 * @param array $data
	 *
	 * @return FormDateTime\Controls\Date
	 */
	private function createControl($data = [])
	{
		$_SERVER['REQUEST_METHOD'] = 'POST';
		$_FILES = [];
		$_POST = $data;

		// Create form
		$form = new Forms\Form;
		// Create form control
		$control = new FormDateTime\Controls\Date();
		// Add form control to form
		$form->addComponent($control, 'date');

		return $control;
	}
}

\run(new DateInputTest());