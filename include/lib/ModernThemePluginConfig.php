<?php

class ModernThemePluginConfig extends PluginConfig {

    function getOptions() {
        return array(
            'appearance_section' => new SectionBreakField(array(
                'label' => 'Appearance Settings',
                'hint'  => 'Customize the visual appearance of your osTicket installation.',
            )),

            'theme_mode' => new ChoiceField(array(
                'label'   => 'Theme Mode',
                'hint'    => 'Choose between light, dark, or auto (follows system preference).',
                'default' => 'auto',
                'choices' => array(
                    'auto'  => 'Auto (System Preference)',
                    'light' => 'Light Mode',
                    'dark'  => 'Dark Mode',
                ),
            )),

            'primary_color' => new TextboxField(array(
                'label'       => 'Primary Color',
                'hint'        => 'Main brand color in hex format (e.g., #c2410c). Used for headers, buttons, and key UI elements.',
                'default'     => '#c2410c',
                'configuration' => array(
                    'size'  => 20,
                    'length' => 7,
                ),
            )),

            'accent_color' => new TextboxField(array(
                'label'       => 'Accent Color',
                'hint'        => 'Secondary accent color in hex format (e.g., #ea580c). Used for highlights and hover states.',
                'default'     => '#ea580c',
                'configuration' => array(
                    'size'  => 20,
                    'length' => 7,
                ),
            )),

            'border_radius' => new ChoiceField(array(
                'label'   => 'Border Radius Style',
                'hint'    => 'Controls the roundness of cards, buttons, and other UI elements.',
                'default' => 'rounded',
                'choices' => array(
                    'sharp'   => 'Sharp (No rounding)',
                    'subtle'  => 'Subtle (Slightly rounded)',
                    'rounded' => 'Rounded (Modern look)',
                    'pill'    => 'Pill (Very rounded)',
                ),
            )),

            'effects_section' => new SectionBreakField(array(
                'label' => 'Effects & Animations',
                'hint'  => 'Toggle visual effects and animations.',
            )),

            'enable_animations' => new BooleanField(array(
                'label'   => 'Enable Animations',
                'hint'    => 'Enable smooth transitions and micro-animations for a polished feel.',
                'default' => true,
            )),

            'enable_glassmorphism' => new BooleanField(array(
                'label'   => 'Enable Glassmorphism',
                'hint'    => 'Enable frosted glass effects on cards and modals for depth.',
                'default' => true,
            )),

            'branding_section' => new SectionBreakField(array(
                'label' => 'Branding',
                'hint'  => 'Custom branding options.',
            )),

            'custom_logo' => new TextboxField(array(
                'label'       => 'Custom Logo URL',
                'hint'        => 'Full URL to your custom logo image. Leave empty to use the default osTicket logo.',
                'default'     => '',
                'configuration' => array(
                    'size'  => 60,
                    'length' => 255,
                ),
            )),

            'advanced_section' => new SectionBreakField(array(
                'label' => 'Advanced Options',
                'hint'  => 'Additional customization for power users.',
            )),

            'custom_css' => new TextareaField(array(
                'label'       => 'Custom CSS',
                'hint'        => 'Add your own CSS rules to further customize the theme. These will be loaded after the theme styles.',
                'default'     => '',
                'configuration' => array(
                    'rows' => 8,
                    'cols' => 60,
                ),
            )),
        );
    }

    function pre_save(&$config, &$errors) {
        // Validate primary color
        if (!empty($config['primary_color'])) {
            $config['primary_color'] = trim($config['primary_color']);
            if (!preg_match('/^#[a-fA-F0-9]{3,6}$/', $config['primary_color'])) {
                $errors['err'] = 'Primary color must be a valid hex color (e.g., #c2410c)';
                return false;
            }
        }

        // Validate accent color
        if (!empty($config['accent_color'])) {
            $config['accent_color'] = trim($config['accent_color']);
            if (!preg_match('/^#[a-fA-F0-9]{3,6}$/', $config['accent_color'])) {
                $errors['err'] = 'Accent color must be a valid hex color (e.g., #ea580c)';
                return false;
            }
        }

        // Validate custom logo URL
        if (!empty($config['custom_logo'])) {
            $config['custom_logo'] = trim($config['custom_logo']);
            if (!filter_var($config['custom_logo'], FILTER_VALIDATE_URL)) {
                $errors['err'] = 'Custom logo must be a valid URL';
                return false;
            }
        }

        // Sanitize custom CSS (basic XSS prevention)
        if (!empty($config['custom_css'])) {
            // Remove potential script injections
            $config['custom_css'] = preg_replace('/<script[^>]*>.*?<\/script>/si', '', $config['custom_css']);
            $config['custom_css'] = preg_replace('/javascript:/i', '', $config['custom_css']);
            $config['custom_css'] = preg_replace('/expression\s*\(/i', '', $config['custom_css']);
        }

        return true;
    }
}
?>
