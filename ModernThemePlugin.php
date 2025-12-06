<?php

require_once 'config.php';

class ModernThemePlugin extends Plugin {

    var $config_class = 'ModernThemePluginConfig';

    function bootstrap() {
        global $ost;

        if (!$ost) {
            return;
        }

        $config = $this->getConfig();
        if (!$config) {
            return;
        }

        // Get plugin asset URL
        $plugin_path = $this->getInstallPath();
        $asset_url = ROOT_PATH . 'include/' . $plugin_path . 'assets/';

        // Get configuration values
        $theme_mode = $config->get('theme_mode') ?: 'auto';
        $primary_color = $config->get('primary_color') ?: '#c2410c';
        $accent_color = $config->get('accent_color') ?: '#ea580c';
        $enable_animations = $config->get('enable_animations') !== false;
        $enable_glassmorphism = $config->get('enable_glassmorphism') !== false;
        $custom_logo = $config->get('custom_logo') ?: '';
        $border_radius = $config->get('border_radius') ?: 'rounded';

        // Inject Google Fonts - Elegant serif + modern sans
        $ost->addExtraHeader(
            '<link rel="preconnect" href="https://fonts.googleapis.com">' .
            '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' .
            '<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300..900;1,9..144,300..900&family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" rel="stylesheet">'
        );

        // Inject Bootstrap 5
        $ost->addExtraHeader(
            '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">'
        );

        // Inject Bootstrap Icons
        $ost->addExtraHeader(
            '<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css" rel="stylesheet">'
        );

        // Inject custom theme CSS
        $ost->addExtraHeader(
            '<link rel="stylesheet" href="' . $asset_url . 'css/modern-theme.css?v=' . $this->getVersion() . '">'
        );

        // Inject dynamic CSS variables based on config
        $border_radius_value = $this->getBorderRadiusValue($border_radius);

        $dynamic_css = '<style id="modern-theme-vars">
            :root {
                --mt-primary: ' . $this->sanitizeColor($primary_color) . ';
                --mt-primary-rgb: ' . $this->hexToRgb($primary_color) . ';
                --mt-accent: ' . $this->sanitizeColor($accent_color) . ';
                --mt-accent-rgb: ' . $this->hexToRgb($accent_color) . ';
                --mt-border-radius: ' . $border_radius_value . ';
                --mt-animations: ' . ($enable_animations ? '1' : '0') . ';
                --mt-glassmorphism: ' . ($enable_glassmorphism ? '1' : '0') . ';
            }';

        // Theme mode handling
        if ($theme_mode === 'dark') {
            $dynamic_css .= '[data-mt-theme="dark"] { color-scheme: dark; }';
        } elseif ($theme_mode === 'light') {
            $dynamic_css .= '[data-mt-theme="light"] { color-scheme: light; }';
        }

        // Custom logo override
        if (!empty($custom_logo)) {
            $dynamic_css .= '
            .modern-theme .logo img,
            .modern-theme #logo img,
            .modern-theme .company-logo img {
                content: url("' . htmlspecialchars($custom_logo, ENT_QUOTES) . '") !important;
            }';
        }

        $dynamic_css .= '</style>';
        $ost->addExtraHeader($dynamic_css);

        // Inject Bootstrap 5 JS bundle
        $ost->addExtraHeader(
            '<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>',
            true
        );

        // Inject custom theme JS
        $ost->addExtraHeader(
            '<script src="' . $asset_url . 'js/modern-theme.js?v=' . $this->getVersion() . '"></script>',
            true
        );

        // Initialize theme with config
        $theme_config_json = json_encode(array(
            'themeMode' => $theme_mode,
            'animations' => $enable_animations,
            'glassmorphism' => $enable_glassmorphism,
        ));

        $ost->addExtraHeader(
            '<script>
                document.addEventListener("DOMContentLoaded", function() {
                    if (typeof ModernTheme !== "undefined") {
                        ModernTheme.init(' . $theme_config_json . ');
                    }
                });
            </script>',
            true
        );
    }

    /**
     * Sanitize hex color input
     */
    private function sanitizeColor($color) {
        $color = trim($color);
        if (preg_match('/^#[a-fA-F0-9]{6}$/', $color)) {
            return $color;
        }
        if (preg_match('/^#[a-fA-F0-9]{3}$/', $color)) {
            return $color;
        }
        return '#c2410c'; // Default fallback
    }

    /**
     * Convert hex color to RGB values
     */
    private function hexToRgb($hex) {
        $hex = ltrim($hex, '#');

        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }

        if (strlen($hex) !== 6) {
            return '194, 65, 12'; // Default fallback
        }

        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        return "$r, $g, $b";
    }

    /**
     * Get border radius CSS value from config option
     */
    private function getBorderRadiusValue($option) {
        $values = array(
            'sharp'   => '0',
            'subtle'  => '0.375rem',
            'rounded' => '0.75rem',
            'pill'    => '2rem',
        );

        return isset($values[$option]) ? $values[$option] : $values['rounded'];
    }

}
?>
