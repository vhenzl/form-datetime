## Javascript API Documentation

API for Date/Time picker is accessible in global object `window.IPub.Forms.DateTime`.

### Loading

Serverside part of Date/Time picker form element is element with custom data attribute `data-ipub-forms-datepicker`. This element can be initialized with method `initialize()`.

```js
IPub.Forms.DateTime.initialize($('[data-ipub-forms-datepicker]'));
```

But there is shortcut implemented as jQuery plugin:

```js
$('[data-ipub-forms-datepicker]').ipubFormsDateTime();
```

You can chain other jQuery methods after this as usual. If you try to initialize one Date/Time picker twice, it will fail silently (second initialization won't proceed).

Finally you can initialize date/time field on the page by calling:

```js
IPub.Forms.DateTime.load();
```

This will be automatically called when document is ready.

### Change event

You can listen to event, when date is changed:

```js
$('#foo').on('update.date.ipub.forms.datepicker', function (e, date) {
	console.log('new date: ', date);
});
```

or when picker is shown or hidden:

```js
$('#foo')
	.on('show.date.ipub.forms.datepicker', function (e, date) {
		console.log('Picker is displayed');
	})
	.on('hide.date.ipub.forms.datepicker', function (e, date) {
		console.log('Picker is hidden');
	});
```

This events are also available for time picker but only for bootstrap template:

```js
$('#foo')
	.on('update.time.ipub.forms.datepicker', function (e, date) {
		console.log('new date: ', date);
	})
	.on('show.time.ipub.forms.datepicker', function (e, date) {
		console.log('Picker is displayed');
	})
	.on('hide.time.ipub.forms.datepicker', function (e, date) {
		console.log('Picker is hidden');
	});
```
