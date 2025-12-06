<?php
/**
 * Modern Theme Plugin for osTicket
 * A refined, modern Bootstrap 5 theme with warm aesthetics
 */

class ModernThemePluginConfig extends PluginConfig {

    function getOptions() {
        return array(
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
                'label'   => 'Primary Color',
                'hint'    => 'Main brand color in hex format (e.g., #c2410c)',
                'default' => '#c2410c',
                'configuration' => array('size' => 20, 'length' => 7),
            )),

            'accent_color' => new TextboxField(array(
                'label'   => 'Accent Color',
                'hint'    => 'Secondary accent color in hex format (e.g., #ea580c)',
                'default' => '#ea580c',
                'configuration' => array('size' => 20, 'length' => 7),
            )),

            'border_radius' => new ChoiceField(array(
                'label'   => 'Border Radius Style',
                'default' => 'rounded',
                'choices' => array(
                    'sharp'   => 'Sharp (No rounding)',
                    'subtle'  => 'Subtle',
                    'rounded' => 'Rounded (Modern)',
                    'pill'    => 'Pill (Very rounded)',
                ),
            )),

            'enable_animations' => new BooleanField(array(
                'label'   => 'Enable Animations',
                'default' => true,
            )),

            'enable_glassmorphism' => new BooleanField(array(
                'label'   => 'Enable Glassmorphism',
                'default' => true,
            )),
        );
    }
}

class ModernThemePlugin extends Plugin {

    var $config_class = 'ModernThemePluginConfig';

    function bootstrap() {
        global $ost;

        if (!$ost) {
            return;
        }

        // Get plugin asset URL
        $plugin_path = $this->getInstallPath();
        $asset_url = ROOT_PATH . 'include/' . $plugin_path . 'assets/';

        // Debug comment
        $ost->addExtraHeader('<!-- Modern Theme Plugin v1.0 ACTIVE -->');

        // Get configuration values (with defaults)
        $config = $this->getConfig();
        $theme_mode = $config ? ($config->get('theme_mode') ?: 'auto') : 'auto';
        $primary_color = $config ? ($config->get('primary_color') ?: '#c2410c') : '#c2410c';
        $accent_color = $config ? ($config->get('accent_color') ?: '#ea580c') : '#ea580c';
        $enable_animations = $config ? ($config->get('enable_animations') !== false) : true;
        $enable_glassmorphism = $config ? ($config->get('enable_glassmorphism') !== false) : true;
        $border_radius = $config ? ($config->get('border_radius') ?: 'rounded') : 'rounded';

        // Inject Google Fonts
        $ost->addExtraHeader(
            '<link rel="preconnect" href="https://fonts.googleapis.com">' .
            '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' .
            '<link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@300..900&family=Plus+Jakarta+Sans:wght@200..800&display=swap" rel="stylesheet">'
        );

        // Inject Bootstrap 5
        $ost->addExtraHeader(
            '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">'
        );

        // Inject Bootstrap Icons
        $ost->addExtraHeader(
            '<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css" rel="stylesheet">'
        );

        // Inject custom theme CSS
        $ost->addExtraHeader(
            '<link rel="stylesheet" href="' . $asset_url . 'css/modern-theme.css">'
        );

        // Inject dynamic CSS variables
        $radius_values = array('sharp' => '0', 'subtle' => '0.375rem', 'rounded' => '0.75rem', 'pill' => '2rem');
        $radius = isset($radius_values[$border_radius]) ? $radius_values[$border_radius] : '0.75rem';

        $ost->addExtraHeader('<style id="modern-theme-vars">
            :root {
                --mt-primary: ' . $this->sanitizeColor($primary_color) . ';
                --mt-accent: ' . $this->sanitizeColor($accent_color) . ';
                --mt-border-radius: ' . $radius . ';
            }
        </style>');

        // Inject Bootstrap JS
        $ost->addExtraHeader(
            '<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>',
            true
        );

        // Inject theme JS
        $ost->addExtraHeader(
            '<script src="' . $asset_url . 'js/modern-theme.js"></script>',
            true
        );

        // Initialize theme
        $ost->addExtraHeader(
            '<script>document.addEventListener("DOMContentLoaded", function() { if(typeof ModernTheme !== "undefined") ModernTheme.init({themeMode:"' . $theme_mode . '"}); });</script>',
            true
        );
    }

    private function sanitizeColor($color) {
        $color = trim($color);
        if (preg_match('/^#[a-fA-F0-9]{3,6}$/', $color)) {
            return $color;
        }
        return '#c2410c';
    }
}
