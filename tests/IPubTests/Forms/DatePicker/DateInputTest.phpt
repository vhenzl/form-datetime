<?php
/**
* Test: IPub\Forms\DateInput
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
		return [
			[NULL, NULL],
			[new Utils\DateTime('2015-01-10 00:00:00'), new Utils\DateTime('2015-01-10 00:00:00')],
			[new \DateTime('2015-01-10 00:00:00'), new Utils\DateTime('2015-01-10 00:00:00')],
			[1421017200, new Utils\DateTime('2015-01-12 00:00:00')],
			['2015-01-12', new Utils\DateTime('2015-01-12 00:00:00')],
		];
	}

	/**
	 * @return array[]|array
	 */
	public function dataInvalidInputValues()
	{
		return [
			[39814358000],
			['some not-date string'],
			[['timestamp' => 1421020800]],
		];
	}

	/**
	 * @return array[]|array
	 */
	public function dataValidPostValues()
	{
		return [
			[NULL, NULL],
			['', NULL],
			['2015-01-10', new Utils\DateTime('2015-01-10 00:00:00')],
		];
	}

	public function dataTemplates()
	{
		return [
			['bootstrap.latte', "div[data-ipub-forms-datepicker-type='bootstrap']"],
			['uikit.latte', "div[data-ipub-forms-datepicker-type='uikit']"],
			['default.latte', "div[data-ipub-forms-datepicker-type='default']"],
		];
	}

	/**
	 * @dataProvider dataValidInputValues
	 *
	 * @param \DateTime|NULL $input
	 * @param \DateTime|NULL $expected
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
	 * @param string $input
	 *
	 * @throws \Nette\InvalidArgumentException
	 */
	public function testInvalidInputs($input)
	{
		$control = new FormDateTime\Controls\Date;
		$control->setValue($input);
	}

	public function testLoadHttpDataEmpty()
	{
		$control = $this->createControl();

		Assert::false($control->isFilled());
		Assert::null($control->getValue());
	}

	/**
	 * @dataProvider dataValidPostValues
	 *
	 * @param string $input
	 * @param \DateTime|NULL $expected
	 */
	public function testLoadHttpDataValid($input, $expected)
	{
		$control = $this->createControl([
			'date' => [
				FormDateTime\Controls\Date::FIELD_NAME_DATE => $input,
			]
		]);

		Assert::equal($expected, $control->getValue());
	}

	public function testHtml()
	{
		// Create form control
		$control = $this->createControl();
		// Set form control value
		$control->setValue(new Utils\DateTime('2015-01-10 00:00:00'));
		// Set one of default templates
		$control->setTemplate('bootstrap.latte');

		$dq = Tester\DomQuery::fromHtml((string) $control->getControl());

		Assert::true($dq->has("input[value='2015-01-10']"));
	}

	/**
	 * @dataProvider dataTemplates
	 *
	 * @param string $template
	 * @param string $expected
	 */
	public function testHtmlTemplates($template, $expected)
	{
		// Create form control
		$control = $this->createControl();
		// Set form control value
		$control->setValue(new Utils\DateTime('2015-01-10 00:00:00'));
		// Set one of default templates
		$control->setTemplate($template);

		$dq = Tester\DomQuery::fromHtml((string) $control->getControl());

		Assert::true($dq->has($expected));
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