# widget-options-extended

> A wordpress plugin extending [widget-options](https://github.com/phpbits/widget-options) with a fewextended options.

_Note that this is built with Timber in mind. The classes aren't actually added to the widget but can be retrieved using:_

```php
WidgetOptionsExtended::get_widget_classes($this->extended_widget_opts);
```

### Features

- Foundation grid classes
- Language filtering (WPML, Polylang)

#### Foundation Grid sytems

```php
// Use Xy-grid rather than the default flex-grid.
add_filter('widget-options-extended/grid', function () {
  return 'xy-grid';
});
```
