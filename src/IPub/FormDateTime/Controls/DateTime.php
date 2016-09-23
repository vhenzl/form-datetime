<?php
/**
 * DateTime.php
 *
 * @copyright	More in license.md
 * @license		http://www.ipublikuj.eu
 * @author		Adam Kadlec http://www.ipublikuj.eu
 * @package		iPublikuj:FormDateTime!
 * @subpackage	Controls
 * @since		5.0
 *
 * @date		01.07.13
 */

namespace IPub\FormDateTime\Controls;

use Nette;
use Nette\Forms;
use Nette\Utils;

use IPub;
use IPub\FormDateTime;

class DateTime extends Date
{
	/**
	 * Default date format
	 */
	const W3C_TIME_FORMAT = 'hh:ii';

	/**
	 * Merge field format pattern
	 */
	const MERGE_FIELDS_PATTERN = '%s %s';

	/**
	 * Define fields names
	 */
	const FIELD_NAME_TIME	= 'time';

	/**
	 * Date format - d, m, M, y
	 * 
	 * @var string date format
	 */
	protected $timeFormat = self::W3C_TIME_FORMAT;

	/**
	 * This option will enable meridian views.
	 *
	 * @var bool
	 */
	protected $showMeridian = FALSE;

	/**
	 * The lowest view that the datetime picker should show
	 * 
	 * @var number
	 */
	protected $minView = self::START_VIEW_HOUR;

	/**
	 * @var bool
	 */
	private static $registered = FALSE;

	/**
	 * @param string $dateFormat
	 * @param string $timeFormat
	 * @param string $language
	 * @param string $label
	 */
	public function __construct($dateFormat = self::W3C_DATE_FORMAT, $timeFormat = self::W3C_TIME_FORMAT, $language = self::DEFAULT_LANGUAGE, $label = NULL)
	{
		parent::__construct($dateFormat, $language, $label);

		$this->timeFormat = $timeFormat;
	}

	/**
	 * The time format, combination of p, P, h, hh, i, ii, s, ss
	 *
	 * @param string $timeFormat
	 *
	 * @return $this
	 */
	public function setTimeFormat($timeFormat = self::W3C_TIME_FORMAT)
	{
		$this->timeFormat = $timeFormat;

		return $this;
	}

	/**
	 * This option will enable meridian views
	 *
	 * @param bool $showMeridian
	 *
	 * @return $this
	 */
	public function setShowMeridian($showMeridian = FALSE)
	{
		$this->showMeridian = (bool) $showMeridian;

		if ($this->showMeridian && strpos($this->timeFormat, 'p') === FALSE && strpos($this->timeFormat, 'P') === FALSE) {
			$this->timeFormat .= ' P';
		}

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
		$this->template->timeInput = $this->getControlPart(self::FIELD_NAME_TIME);

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

		if ($key === static::FIELD_NAME_TIME) {
			if (method_exists('Nette\Forms\Helpers', 'exportRules')) {
				$exportedRules = Forms\Helpers::exportRules($this->getRules());
			} else {
				$exportedRules = self::exportRules($this->getRules());
			}

			$control = Utils\Html::el('input');
			$control->addAttributes([
				'name'				=> ($name . '[' . static::FIELD_NAME_TIME . ']'),
				'type'				=> 'text',
				'value'				=> $this->value ? $this->value->format($this->toPhpFormat($this->timeFormat)) : NULL,
				'required'			=> $this->isRequired(),
				'disabled'			=> $this->isDisabled(),
				'data-nette-rules'	=> $exportedRules ?: NULL,
			]);

			if ($this->disabled) {
				$control->disabled($this->disabled);
			}

			return $control;

		} else {
			return parent::getControlPart($key);
		}
	}

	/**
	 * @return array
	 */
	protected function getControlSettings()
	{
		$settings = parent::getControlSettings();

		$settings['time'] = [
			// The time format, combination of p, P, h, hh, i, ii, s, ss, d, dd, m, mm, M, MM, yy, yyyy
			'format'				=> $this->timeFormat,
			// Enable or disable meridian views
			'showMeridian'			=> $this->showMeridian ? 'true' : 'false',
			// Whether or not to close the date picker immediately when a date is selected
			'autoclose'				=> $this->autoclose ? 'true' : 'false',
			// The lowest view that the date picker should show
			'startView'				=> self::START_VIEW_DAY,
			// The lowest view that the date picker should show
			'minView'				=> self::START_VIEW_HOUR,
			// The highest view that the date picker should show
			'maxView'				=> self::START_VIEW_DAY,
			//
			'viewSelect'			=> self::START_VIEW_HOUR,
			// Enable keyboard for navigation
			'keyboardNavigation'	=> $this->keyboardNavigation,
			// The two-letter code of the language to use for month and day names
			'language'				=> $this->language,
			// Whether or not to force parsing of the input value when the picker is closed
			'forceParse'			=> $this->forceParse,
		];

		// The earliest date that may be selected
		if ($this->startDateTime !== NULL) {
			$settings['time']['startDate'] = $this->startDateTime->format($this->getDateTimeFormat(TRUE));
		}

		// The latest date that may be selected
		if ($this->endDateTime !== NULL) {
			$settings['time']['endDate'] = $this->endDateTime->format($this->getDateTimeFormat(TRUE));
		}

		// Default date
		if ($this->value) {
			$settings['date']['initialDate'] = $this->value->format($this->getDateTimeFormat(TRUE));
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
			$value = Utils\DateTime::createFromFormat($this->getDateTimeFormat(TRUE), $value->format($this->getDateTimeFormat(TRUE)));

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

			$value = Utils\DateTime::createFromFormat($this->getDateTimeFormat(TRUE), $value);

			// Check if value is valid string
			if ($value === FALSE) {
				throw new Nette\InvalidArgumentException;
			}

		} else {
			throw new Nette\InvalidArgumentException;
		}

		if (!isset($rawValue) && isset($value)) {
			$rawValue = $value->format($this->getDateTimeFormat(TRUE));
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
			$value = Utils\DateTime::createFromFormat($this->getDateTimeFormat(TRUE), $value);

			if ($value === FALSE) {
				return NULL;
			}
		}

		return $value;
	}

	public function loadHttpData()
	{
		try {
			// Get date value
			$date = $this->getHttpData(Forms\Form::DATA_LINE, '[' . static::FIELD_NAME_DATE . ']');
			// Get time value
			$time = $this->getHttpData(Forms\Form::DATA_LINE, '[' . static::FIELD_NAME_TIME . ']');

			$this->setValue(sprintf(static::MERGE_FIELDS_PATTERN, $date, $time));

		} catch (Nette\InvalidArgumentException $ex) {
			$this->value = NULL;
		}
	}

	/**
	 * @param bool $php
	 *
	 * @return string
	 */
	protected function getDateTimeFormat($php = FALSE)
	{
		$format = sprintf(static::MERGE_FIELDS_PATTERN, $this->dateFormat, $this->timeFormat);

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
			array('p',	'P',	'hh',	'ii',	'ss',	'dd',	'd',	'mm',	'm',	'MM',	'M',	'yyyy',	'yyy',	'yy'),
			array('a',	'A',	'H',	'i',	's',	'd',	'j',	'm',	'n',	'F',	'M',	'Y',	'y',	'y'),
			$str
		);

		return $f;
	}

	/**
	 * @param string $timeFormat
	 * @param string $dateFormat
	 * @param string $language
	 * @param string $method
	 */
	public static function register($timeFormat = self::W3C_TIME_FORMAT, $dateFormat = self::W3C_DATE_FORMAT, $language = self::DEFAULT_LANGUAGE, $method = 'addDateTimePicker')
	{
		// Check for multiple registration
		if (static::$registered) {
			throw new Nette\InvalidStateException('Date & time picker control already registered.');
		}

		static::$registered = TRUE;

		$class = function_exists('get_called_class')?get_called_class():__CLASS__;
		Forms\Container::extensionMethod(
			$method, function (Forms\Container $form, $name, $label = NULL) use ($class, $dateFormat, $timeFormat, $language) {
				$component = new $class($dateFormat, $timeFormat, $language,  $label);
				$form->addComponent($component, $name);
				return $component;
			}
		);
	}
}
