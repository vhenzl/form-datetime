<?php
/**
 * Test: IPub\Forms\TimeInput
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

namespace IPubTests\Forms\DateTime;

use Nette;
use Nette\Forms;
use Nette\Utils;

use Tester;
use Tester\Assert;

use IPub;
use IPub\FormDateTime;

require __DIR__ . '/../../bootstrap.php';

class TimeInputTest extends Tester\TestCase
{
	/**
	 * @return array[]|array
	 */
	public function dataValidInputValues()
	{
		return [
			[NULL, NULL],
			[new Utils\DateTime('2015-01-10 10:50:00'), new Utils\DateTime('2015-01-10 10:50:00')],
			[new \DateTime('2015-01-10 10:50:00'), new Utils\DateTime('1970-01-01 10:50:00')],
			[1421017200, new Utils\DateTime('2015-01-12 00:00:00')],
			['10:50', new Utils\DateTime('1970-01-01 10:50')],
		];
	}

	/**
	 * @return array[]|array
	 */
	public function dataInvalidInputValues()
	{
		return [
			[39814358000.75489],
			['some not-date string'],
			['2015-01-01'],
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
			['12:00', new Utils\DateTime('1970-01-01 12:00:00')],
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
		$control = new FormDateTime\Controls\Time;
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
		$control = new FormDateTime\Controls\Time;
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
			'time' => [
				FormDateTime\Controls\Time::FIELD_NAME_TIME => $input,
			]
		]);

		Assert::equal($expected, $control->getValue());
	}

	public function testHtml()
	{
		// Create form control
		$control = $this->createControl();
		// Set form control value
		$control->setValue(new Utils\DateTime('2015-01-10 10:50:00'));
		// Set one of default templates
		$control->setTemplateFile('bootstrap.latte');

		$dq = Tester\DomQuery::fromHtml((string) $control->getControl());

		Assert::true($dq->has("input[value='10:50']"));
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
		$control->setValue(new Utils\DateTime('2015-01-10 10:50:00'));
		// Set one of default templates
		$control->setTemplateFile($template);

		$dq = Tester\DomQuery::fromHtml((string) $control->getControl());

		Assert::true($dq->has($expected));
	}

	/**
	 * @throws Nette\InvalidStateException
	 */
	public function testMultipleRegistration()
	{
		FormDateTime\Controls\Time::register();
		FormDateTime\Controls\Time::register();
	}

	public function testRegistration()
	{
		FormDateTime\Controls\Time::register();

		// Create form
		$form = new Forms\Form;
		// Create form control
		$control = $form->addTimePicker('time', 'Time picker');

		Assert::type('IPub\FormDateTime\Controls\Time', $control);
		Assert::equal('time', $control->getName());
		Assert::equal('Time picker', $control->caption);
		Assert::same($form, $control->getForm());
	}

	/**
	 * @param array $data
	 *
	 * @return FormDateTime\Controls\Time
	 */
	private function createControl($data = [])
	{
		$_SERVER['REQUEST_METHOD'] = 'POST';
		$_FILES = [];
		$_POST = $data;

		// Create form
		$form = new Forms\Form;
		// Create form control
		$control = new FormDateTime\Controls\Time();
		// Add form control to form
		$form->addComponent($control, 'time');

		return $control;
	}
}

\run(new TimeInputTest());