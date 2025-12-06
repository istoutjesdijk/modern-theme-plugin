<?php
/**
 * Modern Theme Plugin for osTicket
 * A refined, modern Bootstrap 5 theme with warm aesthetics
 */

require_once(INCLUDE_DIR . 'class.plugin.php');

class ModernThemePluginConfig extends PluginConfig {

    function getOptions() {
        return array(
            'theme_mode' => new ChoiceField(array(
                'label'   => 'Theme Mode',
                'default' => 'auto',
                'choices' => array(
                    'auto'  => 'Auto (System Preference)',
                    'light' => 'Light Mode',
                    'dark'  => 'Dark Mode',
                ),
            )),
            'primary_color' => new TextboxField(array(
                'label'   => 'Primary Color',
                'hint'    => 'Hex format (e.g., #c2410c)',
                'default' => '#c2410c',
                'configuration' => array('size' => 20, 'length' => 7),
            )),
            'accent_color' => new TextboxField(array(
                'label'   => 'Accent Color',
                'default' => '#ea580c',
                'configuration' => array('size' => 20, 'length' => 7),
            )),
        );
    }
}

class ModernThemePlugin extends Plugin {

    var $config_class = 'ModernThemePluginConfig';
    private static $assets_loaded = false;

    function bootstrap() {
        global $ost;

        // Debug: log that bootstrap was called
        error_log('ModernThemePlugin::bootstrap() called');

        // Method 1: Use addExtraHeader (works in <head>)
        if ($ost) {
            $base = ROOT_PATH . 'include/plugins/modern-theme-plugin/assets/';

            $ost->addExtraHeader('<!-- MODERN THEME ACTIVE -->');
            $ost->addExtraHeader('<link rel="preconnect" href="https://fonts.googleapis.com">');
            $ost->addExtraHeader('<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">');
            $ost->addExtraHeader('<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">');
            $ost->addExtraHeader('<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css" rel="stylesheet">');
            $ost->addExtraHeader('<link rel="stylesheet" href="' . $base . 'css/modern-theme.css">');
            $ost->addExtraHeader('<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>', true);
            $ost->addExtraHeader('<script src="' . $base . 'js/modern-theme.js"></script>', true);
        }

        // Method 2: Also connect to signal for ticket pages
        Signal::connect('object.view', array($this, 'injectAssets'));
    }

    /**
     * Signal handler: Inject CSS/JS on page view
     */
    function injectAssets($object, &$data) {
        // Prevent duplicate loading
        if (self::$assets_loaded) {
            return;
        }
        self::$assets_loaded = true;

        // Get asset URL
        $base = ROOT_PATH . 'include/plugins/modern-theme-plugin/assets/';

        // Get config
        $config = $this->getConfig();
        $theme_mode = $config ? ($config->get('theme_mode') ?: 'auto') : 'auto';
        $primary = $config ? ($config->get('primary_color') ?: '#c2410c') : '#c2410c';
        $accent = $config ? ($config->get('accent_color') ?: '#ea580c') : '#ea580c';

        // Output assets directly
        ?>
        <!-- Modern Theme Plugin -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css" rel="stylesheet">
        <link rel="stylesheet" href="<?php echo $base; ?>css/modern-theme.css">
        <style>
            :root {
                --mt-primary: <?php echo htmlspecialchars($primary); ?>;
                --mt-accent: <?php echo htmlspecialchars($accent); ?>;
            }
        </style>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
        <script src="<?php echo $base; ?>js/modern-theme.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                if (typeof ModernTheme !== 'undefined') {
                    ModernTheme.init({themeMode: '<?php echo $theme_mode; ?>'});
                }
            });
        </script>
        <?php
    }
}
