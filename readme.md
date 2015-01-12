# Form Date, Date&Time and Time control

[![Build Status](https://img.shields.io/travis/iPublikuj/form-datetime.svg?style=flat-square)](https://travis-ci.org/iPublikuj/form-datetime)
[![Latest Stable Version](https://img.shields.io/packagist/v/ipub/form-datetime.svg?style=flat-square)](https://packagist.org/packages/ipub/form-datetime)
[![Composer Downloads](https://img.shields.io/packagist/dt/ipub/form-datetime.svg?style=flat-square)](https://packagist.org/packages/ipub/form-datetime)

Add form elements for creating date, date&time and time fields under [Nette Framework](http://nette.org/) forms

This extension is based on two datetime libraries. For [Bootstrap](http://getbootstrap.com) template is used [js library](http://www.malot.fr/bootstrap-datetimepicker/) from [Sebastien Malot](https://github.com/smalot) and for [UIkit](http://getuikit.com/) template is used their [datepicker](http://getuikit.com/docs/addons_datepicker.html) and [timepicker](http://getuikit.com/docs/addons_timepicker.html) extensions.   

## Instalation

The best way to install ipub/form-datetime is using  [Composer](http://getcomposer.org/):

```json
{
	"require": {
		"ipub/form-datetime": "dev-master"
	}
}
```

or

```sh
$ composer require ipub/form-datetime:@dev
```

After that you have to register extension in config.neon.

```neon
extensions:
	formDateTime: IPub\FormDateTime\DI\FormDateTimeExtension
```

> In Nette 2.0, registration is done in `app/bootstrap.php`:
```php
$configurator->onCompile[] = function ($configurator, $compiler) {
	$compiler->addExtension('formDateTime', new IPub\FormDateTime\DI\FormDateTimeExtension);
};
```

And you also need to include static files into your page:

```html
	<script src="{$basePath}/libs/ipub.formDateTime.js"></script>
</body>
```

note: You have to upload static files from **client-site** folder to your project.

## Usage

```php
$form->addDatePicker('date', 'Date picker:');
$form->addDateTimePicker('datetime', 'Date & time picker:');
$form->addTimePicker('time', 'Time picker:');
```

You can pass configuration for each type of form field:

### Settings for all types

You can enable additional buttons which can control showed form field:

```php
$form
	->addDateTimePicker('datetime', 'Date & time picker:')
	// Enable trigger button to open date/time picker
	->enableTriggerButton()
	// Enable cancel button to clear field
	->enableCancelButton();
```

### Settings for Date picker

Just setup how date picker window should display:

```php
$form
	->addDatePicker('date', 'Date picker:')
	// Set week start day
	->setWeekStart(1) // 0 for sunday, 6 for saturday
	// Disable some days from week, this days can not be selected via picker
	->setDaysOfWeekDisabled([0, 6]);
```

### Settings for Time picker

Just setup how time picker window should display:

```php
$form
	->addTimePicker('time', 'Time picker:')
	// Set week start day
	->setShowMeridian(TRUE) // Show time in meridian format (e.g. 04:15 PM)
```

### Settings for Date & Time picker

This type of picker combines all settings from previous two types.

### Defining date and time formats

You can set for each field its format how value should be displayed in form.

```php
$form
	->addDateTimePicker('datetime', 'Date & time picker:')
	// Set date format
	->setDateFormat('yyyy/dd/mm')
	// Set time format
	->setTimeFormat('hh:ii:ss');
```

This two methods require JS format, so for two digits day/mont and four digits year write dd/mm/yyyy etc.

### Validation

Validation can be done as on usual text input and also this fields support special ranges values.

If you need to define some range for selecting value, you can use filed range rule:

```php
$form
	->addDateTimePicker('datetime', 'Date & time picker:')
	// Set date format
	->addRule(\IPub\FormDateTime\Controls\DateTime::DATE_TIME_RANGE, 'Date must be between defined values', [(new Utils\DateTime(2015-01-01)), (new Utils\DateTime(2015-09-01))]);
```

This rule will also create min and max for picker window, so user will be not able to pick date out of defined range.

There are also nex two rules for minimal and maximal value:

```php
$form
	->addDateTimePicker('datetime', 'Date & time picker:')
	// Set date format
	->addRule(\IPub\FormDateTime\Controls\DateTime::DATE_TIME_MIN, 'Date must be higher than selected', (new Utils\DateTime(2015-01-01)));
	->addRule(\IPub\FormDateTime\Controls\DateTime::DATE_TIME_MAX, 'Date must be lower than selected', (new Utils\DateTime(2015-09-01)));
```

### Custom templates and rendering

This control come with templating feature, that mean form element of this control is rendered in latte template. There are 3 predefined templates:

* bootstrap.latte if you are using [Twitter Bootstrap](http://getbootstrap.com/)
* uikit.latte if you are using [YooTheme UIKit](http://getuikit.com/)
* default.latte for custom styling (this template is loaded as default)

> But be aware, if you choose different template than UIkit or Bootstrap, JS will be deactivated!

You can also define you own template if you need to fit this control into your layout.

Template can be set in form definition:

```php
$form
	->addDateTimePicker('datetime', 'Date & time picker:')
	->setTemplate('path/to/your/template.latte');
```

or in manual renderer of the form:

```php
{?$form['datetime']->setTemplate('path/to/your/template.latte')}
{input datetime class => "some-custom-class"}
```

and if you want to switch default template to **bootstrap** or **uikit** just it write into template setter:

```php
$form
	->addDateTimePicker('datetime', 'Date & time picker:')
	->setTemplate('bootstrap.latte');
```

or

```php
{?$form['datetime']->setTemplate('bootstrap.latte')}
{input datetime class => "some-custom-class"}
```
