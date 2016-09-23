<?php
/**
 * Date.php
 *
 * @copyright	More in license.md
 * @license		http://www.ipublikuj.eu
 * @author		Adam Kadlec http://www.ipublikuj.eu
 * @package		iPublikuj:FormDateTime!
 * @subpackage	Controls
 * @since		5.0
 *
 * @date		30.06.13
 */

namespace IPub\FormDateTime\Controls;

use Nette;
use Nette\Forms;
use Nette\Utils;

use IPub;
use IPub\FormDateTime;
use Tracy\Debugger;

class Date extends BaseControl
{
	/**
	 * Default date format
	 */
	const W3C_DATE_FORMAT = 'yyyy-mm-dd';

	/**
	 * Define fields names
	 */
	const FIELD_NAME_DATE	= 'date';

	/**
	 * Calendar view mode with hour overview
	 */
	const START_VIEW_HOUR = 'hour';

	/**
	 * Calendar view mode with day overview
	 */
	const START_VIEW_DAY = 'day';

	/**
	 * Calendar view mode with month overview
	 */
	const START_VIEW_MONTH = 'month';

	/**
	 * Calendar view mode with year overview
	 */
	const START_VIEW_YEAR = 'year';

	/**
	 * Calendar view mode with dacade overview
	 */
	const START_VIEW_DECADE = 'decade';

	/**
	 * Calendar without today button
	 */
	const TODAY_BUTTON_FALSE = FALSE;

	/**
	 * Calendar with simple today button which scrolls calendar to now
	 */
	const TODAY_BUTTON_TRUE = TRUE;

	/**
	 * Calendar with today button which scrolls calendar to now and set value
	 */
	const TODAY_BUTTON_LINKED = 'linked';

	/**
	 * Picker position left
	 */
	const PICKER_POSITION_LEFT = 'bottom-left';

	/**
	 * Picker position right
	 */
	const PICKER_POSITION_RIGHT = 'bottom-right';

	/**
	 * Date format - d, m, M, y
	 * 
	 * @var string date format
	 */
	protected $dateFormat = self::W3C_DATE_FORMAT;

	/**
	 * Definition of first day of week
	 *	0 for Sunday
	 *	6 for Saturday
	 * 
	 * @var int
	 */
	protected $weekStart = 1;

	/**
	 * Days of the week that should be disabled
	 * 
	 * @var number
	 */
	protected $daysOfWeekDisabled = NULL;

	/**
	 * The view that the date picker should show when it is opened
	 * 
	 * @var string
	 */
	protected $startView = self::START_VIEW_MONTH;

	/**
	 * The lowest view that the date picker should show
	 * 
	 * @var string
	 */
	protected $minView = self::START_VIEW_MONTH;

	/**
	 * The highest view that the date picker should show
	 * 
	 * @var string
	 */
	protected $maxView = self::START_VIEW_DECADE;

	/**
	 * Today button mode
	 * 
	 * @var int
	 */
	protected $todayButton = self::TODAY_BUTTON_FALSE;

	/**
	 * If true today column will be highlighted
	 * 
	 * @var bool
	 */
	protected $todayHighlight = TRUE;

	/**
	 * With it you can place the picker just under the input field
	 * 
	 * @var string
	 */
	protected $pickerPosition = self::PICKER_POSITION_RIGHT;

	/**
	 * @var bool
	 */
	private static $registered = FALSE;

	/**
	 * @param string $dateFormat
	 * @param string $language
	 * @param string $label
	 */
	public function __construct($dateFormat = self::W3C_DATE_FORMAT, $language = self::DEFAULT_LANGUAGE, $label = NULL)
	{
		parent::__construct($label);

		$this->control->type = 'text';

		$this->dateFormat	= $dateFormat;
		$this->language		= $language;
	}

	/**
	 * The date format, combination of d, dd, m, mm, M, MM, yy, yyyy
	 *
	 * @param string $dateFormat
	 *
	 * @return $this
	 */
	public function setDateFormat($dateFormat = self::W3C_DATE_FORMAT)
	{
		$this->dateFormat = $dateFormat;

		return $this;
	}

	/**
	 * @param int $weekStart 0 for sunday, 6 for saturday
	 *
	 * @return $this
	 */
	public function setWeekStart($weekStart = 1)
	{
		$this->weekStart = (int) $weekStart;

		return $this;
	}

	/**
	 * Days of the week that should be disabled
	 * 
	 * @param array $daysOfWeekDisabled
	 * 
	 * @return $this
	 */
	public function setDaysOfWeekDisabled($daysOfWeekDisabled = array())
	{
		$this->daysOfWeekDisabled = $daysOfWeekDisabled;

		return $this;
	}

	/**
	 * The view that the date picker should show when it is opened
	 *
	 * @param string $startView
	 *
	 * @return $this
	 */
	public function setStartView($startView = self::START_VIEW_MONTH)
	{
		$this->startView = (string) $startView;

		return $this;
	}

	/**
	 * The lowest view that the date picker should show
	 *
	 * @param string $minView
	 *
	 * @return $this
	 */
	public function setMinView($minView = self::START_VIEW_MONTH)
	{
		$this->minView = (string) $minView;

		return $this;
	}

	/**
	 * The highest view that the date picker should show
	 *
	 * @param string $maxView
	 *
	 * @return $this
	 */
	public function setMaxView($maxView = self::START_VIEW_DECADE)
	{
		$this->maxView = (string) $maxView;

		return $this;
	}

	/**
	 * Sets today button mode
	 *
	 * @param string $todayButton
	 *
	 * @return $this
	 */
	public function setTodayButton($todayButton = self::TODAY_BUTTON_LINKED)
	{
		$this->todayButton = $todayButton;

		return $this;
	}

	/**
	 * Turns on today column highlighting
	 *
	 * @param bool $highlight
	 *
	 * @return $this
	 */
	public function setTodayHighlight($highlight = TRUE)
	{
		$this->todayHighlight = (bool) $highlight;

		return $this;
	}

	/**
	 * With it you can place the picker just under the input field
	 *
	 * @param string $pickerPosition
	 *
	 * @return $this
	 */
	public function setPickerPosition($pickerPosition = self::PICKER_POSITION_RIGHT)
	{
		if (!in_array((string) $pickerPosition, [self::PICKER_POSITION_LEFT, self::PICKER_POSITION_RIGHT])) {
			throw new Nette\InvalidArgumentException('Invalid buttons position given');
		}

		$this->pickerPosition = (string) $pickerPosition;

		return $this;
	}

	/**
	 * Generates control's HTML element
	 *
	 * @return Utils\Html
	 */
	public function getControl()
	{
		// Assign vars to template
		$this->template->dateInput	= $this->getControlPart(self::FIELD_NAME_DATE);

		return parent::getControl();
	}

	/**
	 * @param string|NULL $key
	 *
	 * @return Utils\Html
	 *
	 * @throws Nette\InvalidArgumentException
	 */
	public function getControlPart($key = NULL)
	{
		$name = $this->getHtmlName();

		if ($key === static::FIELD_NAME_DATE) {
			if (method_exists('Nette\Forms\Helpers', 'exportRules')) {
				$exportedRules = Forms\Helpers::exportRules($this->getRules());
			} else {
				$exportedRules = self::exportRules($this->getRules());
			}

			$control = Utils\Html::el('input');
			$control->addAttributes([
				'name'				=> ($name . '[' . static::FIELD_NAME_DATE . ']'),
				'type'				=> 'text',
				'value'				=> $this->value ? $this->value->format($this->toPhpFormat($this->dateFormat)) : NULL,
				'required'			=> $this->isRequired(),
				'disabled'			=> $this->isDisabled(),
				'data-nette-rules'	=> $exportedRules ?: NULL,
			]);

			if ($this->disabled) {
				$control->disabled($this->disabled);
			}

			return $control;
		}

		throw new Nette\InvalidArgumentException('Part ' . $key . ' does not exist');
	}

	/**
	 * @return null
	 */
	public function getLabelPart()
	{
		return NULL;
	}

	/**
	 * @return array
	 */
	protected function getControlSettings()
	{
		// Set data-attribute options
		$settings = [
			'date' => [
				// The date format, combination of p, P, h, hh, i, ii, s, ss, d, dd, m, mm, M, MM, yy, yyyy
				'format'				=> $this->dateFormat,
				// Day of the week start. 0 (Sunday) to 6 (Saturday)
				'weekStart'				=> $this->weekStart,
				// Whether or not to close the date picker immediately when a date is selected
				'autoclose'				=> $this->autoclose ? 'true' : 'false',
				// The view that the date picker should show when it is opened
				'startView'				=> $this->startView,
				// The lowest view that the date picker should show
				'minView'				=> $this->minView,
				// The highest view that the date picker should show
				'maxView'				=> $this->maxView,
				// If true or "linked", displays a "Today" button at the bottom of the date picker to select the current date
				'todayBtn'				=> $this->todayButton,
				// If TRUE, highlights the current date.
				'todayHighlight'		=> $this->todayHighlight,
				// Enable keyboard for navigation
				'keyboardNavigation'	=> $this->keyboardNavigation,
				// The two-letter code of the language to use for month and day names
				'language'				=> $this->language,
				// Whether or not to force parsing of the input value when the picker is closed
				'forceParse'			=> $this->forceParse,
				// This option is currently only available in the component implementation
				'pickerPosition'		=> $this->pickerPosition,
			],
		];

		// The earliest date that may be selected
		if ($this->startDateTime !== NULL) {
			$settings['date']['minDate']	= $this->startDateTime->format($this->getDateFormat(TRUE));
			$settings['date']['startDate']	= $this->startDateTime->format($this->getDateFormat(TRUE));
		}

		// The latest date that may be selected
		if ($this->endDateTime !== NULL) {
			$settings['date']['endDate']	= $this->endDateTime->format($this->getDateFormat(TRUE));
			$settings['date']['maxDate']	= $this->endDateTime->format($this->getDateFormat(TRUE));
		}

		// Days of the week that should be disabled. Values are 0 (Sunday) to 6 (Saturday)
		if ($this->daysOfWeekDisabled !== NULL) {
			$settings['date']['daysOfWeekDisabled'] = implode(',', $this->daysOfWeekDisabled);
		}

		// Default date
		if ($this->value) {
			$settings['date']['initialDate'] = $this->value->format($this->getDateFormat(TRUE));
		}

		return $settings;
	}

	/**
	 * Sets DateTime value
	 *
	 * @param mixed $value
	 *
	 * @return $this
	 *
	 * @throws Nette\InvalidArgumentException
	 */
	public function setValue($value)
	{
		// Nette\Utils\DateTime object
		if ($value instanceof Utils\DateTime) {
			$rawValue = $value;

		// \DateTime object
		} else if($value instanceof \DateTime) {
			$rawValue = $value;

			// Convert \DateTime to \Nette\Utils\DateTime
			$value = Utils\DateTime::createFromFormat($this->getDateFormat(TRUE), $value->format($this->toPhpFormat($this->dateFormat)));

		// Timestamp
		} else if (is_int($value)) {
			$rawValue = $value;
			$value = (new Utils\DateTime())->setTimestamp($value);

			// Check if value is valid timestamp
			if ($value === FALSE) {
				throw new Nette\InvalidArgumentException;
			}

		// Empty value
		} else if (empty($value)) {
			$rawValue = $value;
			$value = NULL;

		// String representation
		} else if (is_string($value)) {
			$rawValue = $value;
			$value = Utils\DateTime::createFromFormat($this->getDateFormat(TRUE), $value);

			// Check if value is valid string
			if ($value === FALSE) {
				throw new Nette\InvalidArgumentException;
			}

		} else {
			throw new Nette\InvalidArgumentException;
		}

		if (!isset($rawValue) && isset($value)) {
			$rawValue = $value->format($this->getDateFormat(TRUE));
		}

		$this->value	= $value;
		$this->rawValue	= $rawValue;

		return $this;
	}

	/**
	 * @return Utils\DateTime|null
	 */
	public function getValue()
	{
		$value = parent::getValue();

		if ($value instanceof Utils\DateTime) {
			return $value;

		} else if ($value === FALSE || $value === NULL) {
			return NULL;

		} else {
			$value = Utils\DateTime::createFromFormat($this->getDateFormat(TRUE), $value);

			if ($value === FALSE) {
				return NULL;
			}
		}

		return $value;
	}

	public function loadHttpData()
	{
		try {
			$this->setValue($this->getHttpData(Forms\Form::DATA_LINE, '[' . static::FIELD_NAME_DATE . ']'));

		} catch (Nette\InvalidArgumentException $ex) {
			$this->value = NULL;
		}
	}

	/**
	 * @param bool $php
	 *
	 * @return string
	 */
	protected function getDateFormat($php = FALSE)
	{
		$format = (strpos($this->dateFormat, '!') === FALSE ? '!' : '') . $this->dateFormat;

		if ($php) {
			$format = $this->toPhpFormat($format);
		}

		return $format;
	}

	/**
	 * Converts js date format string to php date() format string
	 *
	 * @param string $str
	 *
	 * @return string
	 */
	protected function toPhpFormat($str)
	{
		$f = $this->strReplace(
			array('dd',	'd',	'mm',	'm',	'MM',	'M',	'yyyy',	'yyy',	'yy'),
			array('d',	'j',	'm',	'n',	'F',	'M',	'Y',	'y',	'y'),
			$str
		);

		return $f;
	}

	/**
	 * @param string $dateFormat
	 * @param string $language
	 * @param string $method
	 */
	public static function register($dateFormat = self::W3C_DATE_FORMAT, $language = self::DEFAULT_LANGUAGE, $method = 'addDatePicker')
	{
		// Check for multiple registration
		if (static::$registered) {
			throw new Nette\InvalidStateException('Date picker control already registered.');
		}

		static::$registered = TRUE;

		$class = function_exists('get_called_class')?get_called_class():__CLASS__;
		Forms\Container::extensionMethod(
			$method, function (Forms\Container $form, $name, $label = NULL) use ($class, $dateFormat, $language) {
				$component = new $class($dateFormat, $language,  $label);
				$form->addComponent($component, $name);
				return $component;
			}
		);
	}
}
