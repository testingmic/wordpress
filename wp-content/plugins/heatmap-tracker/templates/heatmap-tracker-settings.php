<div class="wrap">
    <div class="heatmap-wrapper">
        <div class="wrapper-header">
            <h2>Heatmap Tracker</h2>
            <?= $variables['description']; ?>
        </div>
        <div class="wrapper-body">
            <?php settings_errors(); ?>
            <form method="POST" action="options.php">
                <?php
                settings_fields('plugin_name_general_settings');
                do_settings_sections('plugin_name_general_settings');
                ?>
                <?php submit_button(); ?>
            </form>
        </div>
    </div>
</div>