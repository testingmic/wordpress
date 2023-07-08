<div class="wrap">
    <div class="heatmap-wrapper">
        <div class="wrapper-header">
            <div id="logo-link" class="flex items-center gap-3">
                <img src="https://app.heatmap.com/assets/logo.svg" alt="">
                <p class="text-secondary"><?= $variables['site_name'] ?></p>
            </div>
            <div><?= $variables['description']; ?></div>
        </div>
        <div class="wrapper-body">
            <div class="content">
                <div>
                    <strong><?= $variables['site_name'] ?></strong> provides unique access to on-site metrics that are not available in other heatmap solutions, with a primary focus on revenue attribution. Our platform allows you to track:
                    <ul>
                        <li>Users interaction with your pages including scrolls, normal and rage clicks on links and other elements.</li>
                        <li>We also collect data on customer orders processed with WooCommerce,</li>
                        <li>Overall time spent on page, and save a minimal amount of information in the user's browser localStorage.</li>
                    </ul>
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
            <div class="content">
                <?php settings_errors(); ?>
                <?php if(empty($variables['plugin_option'])) { ?>
                    <div class="info notice-error settings-error"> 
                        <strong>You have not yet agreed to the terms to track users activities.</strong>
                    </div>
                    <div>
                        <p>
                            <input disabled type="hidden" id="heat_admin_answer" value="1">
                            <button type="submit" data-heatmap_plugin="activate" name="submit" id="submit" class="button button-primary">Enable Tracking</button>
                        </p>
                    </div>
                <?php } else { ?>
                    <div class="info notice-success settings-error"> 
                        <strong>Thanks for agreeing to the terms, tracking snippet has been injected into your website.</strong>
                        <div style="margin-top:10px">
                            <strong>
                                <span class="pad-right">Site URL:</span>
                            </strong> 
                            <?= $variables['plugin_option']['site_url']; ?>
                        </div>
                        <div>
                            <strong>
                                <span class="pad-right">ID Site:</span>
                            </strong> 
                            <?= $variables['plugin_option']['idsite']; ?>
                        </div>
                        <div>
                            <strong>
                                <span class="pad-right">Site Name:</span>
                            </strong> 
                            <?= $variables['plugin_option']['name']; ?>
                        </div>
                        <div>
                            <strong>
                                <span class="pad-right">Enabled Since:</span>
                            </strong> 
                            <?= $variables['plugin_option']['heatLastUpdated']; ?>
                        </div>
                    </div>
                    <div>
                        <p>
                            <input type="hidden" disabled id="heat_admin_answer" value="2">
                            <button type="submit" data-heatmap_plugin="activate" name="submit" id="submit" class="button button-danger">Disable Tracking</button>
                        </p>
                    </div>
                <?php } ?>
                <script>
                    jQuery(document).ready(function($) {
                        $(`button[data-heatmap_plugin="activate"]`).on("click", function() {
                            let status = $(`input[id="heat_admin_answer"]`).val();
                            $(`button[data-heatmap_plugin="activate"]`).prop("disabled", true);
                            $.post(`<?= admin_url('admin-ajax.php'); ?>`, {action: 'HeatmapManageSettings', status}).then((response) => {
                                if(response == 'option_updated') {
                                    window.location.href = "";
                                } else {
                                    $(`div[class~="notice-error"]`).html(`<strong>Sorry! We could not validate your website url. Please try again later.</strong>`);
                                    $(`button[data-heatmap_plugin="activate"]`).prop("disabled", false);
                                }
                            }).fail((err) => {
                                $(`button[data-heatmap_plugin="activate"]`).prop("disabled", false);
                            });
                        });
                    });
                </script>
            </div>
        </div>
    </div>
</div>