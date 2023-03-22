<div class="wrap">
    <div class="heatmap-wrapper">
        <div class="wrapper-header">
            <div id="logo-link" class="flex items-center gap-3">
                <img src="<?= $variables['plugin_url'] . $variables['plugin_name'] ?>/templates/assets/Vector.svg" alt="">
                <p class="text-secondary"><?= $variables['site_name'] ?></p>
            </div>
            <div><?= $variables['description']; ?></div>
        </div>
        <div class="wrapper-body">
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