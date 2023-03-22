<div class="wrap">
    <div class="heatmap-wrapper">
        <div class="wrapper-header">
            <h2>Heatmap Tracker</h2>
            <?= $variables['description']; ?>
        </div>
        <div class="wrapper-body">
            <div class="content">
                <?php settings_errors(); ?>
                
                <?php if(empty($variables['plugin_option'])) { ?>
                    <div class="info notice-error settings-error"> 
                        <strong>You have not yet agreed to the terms to collect information from your users.</strong>
                    </div>
                <?php } ?>
                
                <form method="POST" action="options.php">
                    <?php
                    settings_fields('plugin_name_general_settings');
                    do_settings_sections('plugin_name_general_settings');
                    ?>
                    <?php submit_button(); ?>
                </form>
            </div>
            <div class="content">
                <div>
                    <strong><?= $variables['site_name'] ?></strong> gives you access to on-site metrics that you can’t see in 
                    any other heatmap solutions, primarily around revenue attribution.
                    <ul>
                        <li>Users interaction with your pages related to scrolls.</li>
                        <li>Normal and rage clicks on links and other elements on the page.</li>
                        <li>All <strong>Customer Orders</strong> processed with <strong>WooCommerce</strong></li>
                        <li>Overall time spent on page</li>
                    </ul>
                    In doing so, a <strong>very tiny bit</strong> of information is saved in the user's browser localStorage.
                    <p class="strong">The data collected enables us to:</p>
                    <ul>
                        <li>Attribute Revenue to all elements on website pages that users interacted with.</li>
                        <li>Efficiently analyze and display datapoints on the heatmap areas of your pages.</li>
                    </ul>
                    <p class="strong">How is the data processed:</p>
                    Upon collection of the sample data, it is sent as a heartbeat beacon to our servers 
                    <strong><em>(https://<?= strtolower($variables['site_name']) ?>)</em></strong>
                    for insertion into the database and further processing to generate reports for you.
                </div>
            </div>
        </div>
    </div>
</div>