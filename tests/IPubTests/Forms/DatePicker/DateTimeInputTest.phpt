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

namespace IPubTests\Forms\DateTime;

use Nette;
use Nette\Forms;
use Nette\Utils;

use Tester;
use Tester\Assert;

use IPub;
use IPub\FormDateTime;

require __DIR__ . '/../../bootstrap.php';

class DateTimeInputTest extends Tester\TestCase
{
	/**
	 * @return array[]|array
	 */
	public function dataValidInputValues()
	{
		return [
			[NULL, NULL],
			[new Utils\DateTime('2015-01-10 10:50:00'), new Utils\DateTime('2015-01-10 10:50:00')],
			[new \DateTime('2015-01-10 10:50:00'), new Utils\DateTime('2015-01-10 10:50:00')],
			[1421017200, new Utils\DateTime('2015-01-12 00:00:00')],
			['2015-01-12 10:50', new Utils\DateTime('2015-01-12 10:50:00')],
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
			[['timestamp' => 1421020800]],
		];
	}

	/**
	 * @return array[]|array
	 */
	public function dataValidPostValues()
	{
		return [
			[NULL, NULL, NULL],
			[NULL, '', NULL],
			['', NULL, NULL],
			['', '', NULL],
			['2015-01-10', '12:00', new Utils\DateTime('2015-01-10 12:00:00')],
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
		$control = new FormDateTime\Controls\DateTime;
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
		$control = new FormDateTime\Controls\DateTime;
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
	 * @param string $date
	 * @param string $time
	 * @param \DateTime|NULL $expected
	 */
	public function testLoadHttpDataValid($date, $time, $expected)
	{
		$control = $this->createControl([
			'datetime' => [
				FormDateTime\Controls\DateTime::FIELD_NAME_DATE => $date,
				FormDateTime\Controls\DateTime::FIELD_NAME_TIME => $time,
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

		Assert::true($dq->has("input[value='2015-01-10']"));
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
		FormDateTime\Controls\DateTime::register();
		FormDateTime\Controls\DateTime::register();
	}

	public function testRegistration()
	{
		FormDateTime\Controls\DateTime::register();

		// Create form
		$form = new Forms\Form;
		// Create form control
		$control = $form->addDateTimePicker('datetime', 'Date & time picker');

		Assert::type('IPub\FormDateTime\Controls\DateTime', $control);
		Assert::equal('datetime', $control->getName());
		Assert::equal('Date & time picker', $control->caption);
		Assert::same($form, $control->getForm());
	}

	/**
	 * @param array $data
	 *
	 * @return FormDateTime\Controls\DateTime
	 */
	private function createControl($data = [])
	{
		$_SERVER['REQUEST_METHOD'] = 'POST';
		$_FILES = [];
		$_POST = $data;

		// Create form
		$form = new Forms\Form;
		// Create form control
		$control = new FormDateTime\Controls\DateTime();
		// Add form control to form
		$form->addComponent($control, 'datetime');

		return $control;
	}
}

\run(new DateTimeInputTest());