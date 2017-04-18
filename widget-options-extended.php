<?php
/*
Plugin Name:        Widget Options Extended
Plugin URI:         http://genero.fi
Description:        Extends Widget Options with foundation grid classes and language.
Version:            0.0.1
Author:             Genero
Author URI:         http://genero.fi/

License:            MIT License
License URI:        http://opensource.org/licenses/MIT
*/

if (!defined('ABSPATH')) {
  exit;
}

class WidgetOptionsExtended
{
    private static $breakpoints = [
        'small' => ['name' => 'Mobile', 'icon' => 'dashicons-smartphone'],
        'medium' => ['name' => 'Tablet', 'icon' => 'dashicons-tablet'],
        'large' => ['name' => 'Desktop', 'icon' => 'dashicons-desktop'],
    ];

    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function init()
    {
        if ($this->get_current_language()) {
            add_action('extended_widget_opts_tabs', [$this, 'tab_language'], 9);
            add_action('extended_widget_opts_tabcontent', [$this, 'content_language']);
        }

        add_action('extended_widget_opts_tabs', [$this, 'tab_grid'], 9);
        add_action('extended_widget_opts_tabcontent', [$this, 'content_grid']);

        add_action('widget_display_callback', [$this, 'filter_language'], 50, 3);
    }

    public function tab_grid($args)
    {
    ?>
        <li class="extended-widget-opts-tab-grid">
            <a href="#extended-widget-opts-tab-<?php echo $args['id'];?>-grid">
                <span class="dashicons dashicons-grid-view"></span>
                <span class="tabtitle"><?php _e('Grid', 'theme-admin');?></span>
            </a>
        </li>
    <?php
    }

    public function content_grid($args)
    {
        $shrink = false;
        $alignment = false;
        $grid = [];
        if (isset($args['params']) && isset($args['params']['grid'])) {
            if (isset($args['params']['grid']['shrink'])) {
                $shrink = $args['params']['grid']['shrink'];
            }
            if (isset($args['params']['grid']['alignment'])) {
                $alignment = $args['params']['grid']['alignment'];
            }
        }
        foreach (self::$breakpoints as $breakpoint => $info) {
            $grid[$breakpoint] = ['columns' => '', 'expand' => '', 'offset' => '', 'order' => ''];
            if (isset($args['params']['grid']['breakpoints'][$breakpoint])) {
                $grid[$breakpoint] = array_merge($grid[$breakpoint], $args['params']['grid']['breakpoints'][$breakpoint]);
            }
        }
        ?>
        <div id="extended-widget-opts-tab-<?php echo $args['id'];?>-grid" class="extended-widget-opts-tabcontent extended-widget-opts-tabcontent-grid">
            <p>
                <strong><?php _e('Shrink', 'widget-options');?></strong>&nbsp;
                <input type="checkbox" name="extended_widget_opts-<?php echo $args['id'];?>[extended_widget_opts][grid][shrink]" value="1" <?php echo $shrink ? 'checked="checked"' : ''; ?> />
            </p>
            <p>
                <strong><?php _e('Alignment', 'widget-options');?></strong>
                <select name="extended_widget_opts-<?php echo $args['id'];?>[extended_widget_opts][grid][alignment]">
                    <option></option>
                    <?php foreach (['top', 'bottom', 'middle', 'stretch'] as $align) : ?>
                        <option value="align-self-<?php echo $align; ?>" <?php echo $alignment == "align-self-$align" ? 'selected="selected"' : ''; ?>><?php echo ucfirst($align); ?></option>
                    <?php endforeach; ?>
                </select>
            </p>
            <table class="form-table">
                <tbody>
                    <tr valign="top">
                        <td scope="row"><strong><?php _e('Grid', 'widget-options');?></strong></td>
                        <td>Column</td>
                        <td>Offset</td>
                        <td>Order</td>
                        <td>Expand</td>
                    </tr>

                    <?php foreach (self::$breakpoints as $breakpoint => $info) : ?>
                        <tr valign="top">
                            <td scope="row">
                                <label for="opts-grid-<?php echo $breakpoint; ?>-<?php echo $args['id'];?>">
                                    <span class="dashicons <?php echo $info['icon']; ?>"></span> <?php echo $info['name']; ?>
                                </label>
                            </td>
                            <td>
                                <select class="widefat" name="extended_widget_opts-<?php echo $args['id'];?>[extended_widget_opts][grid][breakpoints][<?php echo $breakpoint; ?>][columns]">
                                    <option></option>
                                    <?php foreach (range(1, 12) as $columns) : ?>
                                        <option value="<?php echo $breakpoint; ?>-<?php echo $columns; ?>" <?php echo $grid[$breakpoint]['columns'] == "$breakpoint-$columns" ? 'selected="selected"' : ''; ?>><?php echo $columns; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td>
                                <select class="widefat" name="extended_widget_opts-<?php echo $args['id'];?>[extended_widget_opts][grid][breakpoints][<?php echo $breakpoint; ?>][offset]">
                                    <option></option>
                                    <?php foreach (range(1, 12) as $offset) : ?>
                                        <option value="<?php echo $breakpoint; ?>-offset-<?php echo $offset; ?>" <?php echo $grid[$breakpoint]['offset'] == "$breakpoint-offset-$offset" ? 'selected="selected"' : ''; ?>><?php echo $offset; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td>
                                <select class="widefat" name="extended_widget_opts-<?php echo $args['id'];?>[extended_widget_opts][grid][breakpoints][<?php echo $breakpoint; ?>][order]">
                                    <option></option>
                                    <?php foreach (range(1, 6) as $order) : ?>
                                        <option value="<?php echo $breakpoint; ?>-order-<?php echo $order; ?>" <?php echo $grid[$breakpoint]['order'] == "$breakpoint-order-$order" ? 'selected="selected"' : ''; ?>><?php echo $order; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td>
                                <input type="checkbox" name="extended_widget_opts-<?php echo $args['id'];?>[extended_widget_opts][grid][breakpoints][<?php echo $breakpoint; ?>][expand]" value="1" <?php echo $grid[$breakpoint]['expand'] == '1' ? 'checked="checked"' : ''; ?> />
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php
    }

    /**
     * Retrieve all widget classes. You need to attach them to the widget
     * template yourself.
     */
    public static function get_widget_classes($options)
    {
        $extra_classes = [];
        if (!empty($options['class']['classes'])) {
            $extra_classes = array_merge($extra_classes, explode(' ', $options['class']['classes']));
        }
        if (!empty($options['class']['predefined'])) {
            $extra_classes = array_merge($extra_classes, $options['class']['predefined']);
        }
        if (!empty($options['class']['id'])) {
            $this->id = $options['class']['id'];
        }
        // Hide the title.
        if (!empty($options['class']['title'])) {
            $this->title = '';
        }
        if (!empty($options['devices'])) {
            $visibility = $options['devices']['options'];
            $desktop = !empty($options['devices']['desktop']);
            $tablet = !empty($options['devices']['tablet']);
            $mobile = !empty($options['devices']['mobile']);

            $small_up       = ( $desktop &&  $tablet &&  $mobile);
            $medium_up      = ( $desktop &&  $tablet && !$mobile);
            $large_up       = ( $desktop && !$tablet && !$mobile);

            $small_only     = (!$desktop && !$tablet &&  $mobile);
            $medium_only    = (!$desktop &&  $tablet && !$mobile);
            $large_only     = ( $desktop && !$tablet && !$mobile);

            $small_desktop  = ( $desktop && !$tablet &&  $mobile);

            if ($small_up && $visibility == 'hide') {
                $extra_classes [] = 'hide';
            } elseif ($small_desktop && $visibility == 'show') {
                $extra_classes[] = 'hide-for-medium-only';
            } elseif ($small_desktop && $visibility == 'hide') {
                $extra_classes[] = 'show-for-medium-only';
            } elseif ($medium_up) {
                $extra_classes[] = "$visibility-for-medium";
            } elseif ($large_up) {
                $extra_classes[] = "$visibility-for-large";
            } elseif ($small_only) {
                $extra_classes[] = "$visibility-for-small-only";
            } elseif ($medium_only) {
                $extra_classes[] = "$visibility-for-medium-only";
            } elseif ($large_only) {
                $extra_classes[] = "$visibility-for-large-only";
            }
        }
        // @todo
        if (!empty($options['alignment']['desktop'])) {
            $extra_classes[] = 'text-' . $options['alignment']['desktop'];
        }

        if (!empty($options['grid']['shrink'])) {
            $extra_classes[] = 'shrink';
        }
        if (!empty($options['grid']['alignment'])) {
            $extra_classes[] = $options['grid']['alignment'];
        }
        if (!empty($options['grid']['breakpoints'])) {
            foreach ($options['grid']['breakpoints'] as $breakpoint => $data) {
                if (!empty($data['columns'])) {
                    $extra_classes[] = 'column';
                    $extra_classes[] = $data['columns'];
                }
                if (!empty($data['offset'])) {
                    $extra_classes[] = $data['offset'];
                }
                if (!empty($data['order'])) {
                    $extra_classes[] = $data['order'];
                }
                if (!empty($data['expand'])) {
                    $extra_classes[] = "$breakpoint-expand";
                }
            }
        }

        return $extra_classes;
    }

    public function tab_language($args)
    {
    ?>
        <li class="extended-widget-opts-tab-grid">
            <a href="#extended-widget-opts-tab-<?php echo $args['id'];?>-language">
                <span class="dashicons dashicons-translation"></span>
                <span class="tabtitle"><?php _e('Languages', 'theme-admin');?></span>
            </a>
        </li>
    <?php
    }

    public function content_language($args)
    {
        $options_role = false;
        $language = [];
        if (isset($args['params']) && isset($args['params']['language'])) {
            if (isset($args['params']['language']['options'])) {
                $options_role = $args['params']['language']['options'];
            }
            if (isset($args['params']['language']['language'])) {
                $language = $args['params']['language']['language'];
            }
        }
        $languages = apply_filters('wpml_active_languages', null);
        ?>
        <div id="extended-widget-opts-tab-<?php echo $args['id'];?>-language" class="extended-widget-opts-tabcontent extended-widget-opts-tabcontent-language">
            <p>
                <strong><?php _e('Hide/Show', 'widget-options');?></strong>
                <select class="widefat" name="extended_widget_opts-<?php echo $args['id'];?>[extended_widget_opts][language][options]">
                    <option value="hide" <?php echo $options_role == 'hide' ? 'selected="selected"' : ''; ?>><?php _e('Hide on selected languages', 'widget-options');?></option>
                    <option value="show" <?php echo $options_role == 'show' ? 'selected="selected"' : ''; ?>><?php _e('Show on selected languages', 'widget-options');?></option>
                </select>
            </p>
            <?php foreach ($languages as $langcode => $lang) : ?>
                <p>
                    <input type="checkbox" name="extended_widget_opts-<?php echo $args['id'];?>[extended_widget_opts][language][language][<?php echo $langcode;?>]" id="<?php echo $args['id'];?>-opts-language-<?php echo $langcode;?>" value="1" <?php echo !empty($language[$langcode]) ? 'checked="checked"' : ''; ?> />
                    <label for="<?php echo $args['id'];?>-opts-language-<?php echo $langcode;?>"><?php echo $lang['translated_name'];?></label>
                </p>
            <?php endforeach; ?>
        </div>
        <?php
    }

    public function filter_language($instance, $widget, $args)
    {
        $current_language = $this->get_current_language();
        if (!$current_language) {
            return $instance;
        }
        $options = (isset($instance['extended_widget_opts-'. $widget->id])) ? $instance['extended_widget_opts-'. $widget->id] : array();
        $options_role = isset($options['language']['options']) ? $options['language']['options'] : 'hide';
        $language = isset($options['language']['language']) ? $options['language']['language'] : [];

        switch ($options_role) {
            case 'show':
                if (!isset($language[$current_language])) {
                    return false;
                }
                break;
            case 'hide':
                if (isset($language[$current_language])) {
                    return false;
                }
                break;
        }

        return $instance;
    }

    protected function get_current_language()
    {
        $language = null;
        if (defined('ICL_LANGUAGE_CODE')) {
            $language = ICL_LANGUAGE_CODE;
        }
        return $language;
    }
}

add_action('plugins_loaded', array(WidgetOptionsExtended::get_instance(), 'init'), 11);
