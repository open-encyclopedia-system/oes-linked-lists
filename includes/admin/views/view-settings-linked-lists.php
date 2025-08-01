<div class="oes-page-header-wrapper">
    <div class="oes-page-header">
        <h1><?php esc_html_e('OES Linked Lists', 'oes'); ?></h1>
    </div>
</div>

<div class="oes-page-body">
    <div>
        <h2><?php esc_html_e('Credits', 'oes'); ?></h2>
        <p>
            <?php
            echo wp_kses_post(sprintf(
                __('The design of OES Linked Lists is inspired by the personal website of <a href="%s" target="_blank" rel="noopener noreferrer">Marian Dörk</a>. For more information and access to the open source code, visit his website.', 'oes'),
                esc_url('https://mariandoerk.de/')
            ));
            ?>
        </p>
    </div>

    <div>
        <h2><?php esc_html_e('Shortcode', 'oes'); ?></h2>
        <p><?php esc_html_e('You can display data as linked lists by adding a shortcode to a page. The shortcode supports various parameters that define how the data is selected and displayed.', 'oes'); ?></p>
        <p><?php esc_html_e('Example:', 'oes'); ?></p>
        <p><code>
[oes_linked_lists list="{post type}" sort="{option}" left="{object}" sort_left="{option}" right="{object}" sort_right="{option}" top="{object}" sort_top="{option}" line="{RGBA color}" line_hovered="{RGBA color}" exclude_preview="{true|false}" hide_filter="{true|false}" hide_count="{true|false}" center_percentage="{percentage}"]
        </code></p>
        <p>
            <?php esc_html_e('The shortcode must be enclosed in square brackets [] and start with', 'oes'); ?>
            <code>oes_linked_lists</code>,
            <?php esc_html_e('followed by optional key-value pairs in the format', 'oes'); ?>
            <code>key="value"</code>.
        </p>
    </div>

    <div>
        <p><strong><?php esc_html_e('List', 'oes'); ?></strong></p>
        <p>
            <?php esc_html_e('Defines the central list of OES posts to be displayed. The value should be a post type key.', 'oes'); ?>
            <?php esc_html_e('Sorting is alphabetical by default, but can be customized with', 'oes'); ?> <code>sort</code>.
            <?php esc_html_e('Valid options are:','oes'); ?><code>frequency</code>
            <?php esc_html_e('and', 'oes');?><code>sorting_title</code>.
            <?php esc_html_e('Use', 'oes'); ?> <code>list_excerpt</code> <?php esc_html_e('to show a field excerpt below each title.', 'oes'); ?>
        </p>
    </div>

    <div>
        <p><strong><?php esc_html_e('Left / Right / Top', 'oes'); ?></strong></p>
        <p>
            <?php esc_html_e('These parameters define which objects appear around the central list.', 'oes'); ?>
            <?php esc_html_e('Valid values include custom fields, taxonomies, and parent relationships configured in OES archive settings.', 'oes'); ?>
            <?php esc_html_e('You can customize sorting with parameters like', 'oes'); ?>
            <code>sort_left</code>, <code>sort_right</code>, <?php esc_html_e('or', 'oes'); ?> <code>sort_top</code>.
            <?php esc_html_e('Valid options are:','oes'); ?><code>frequency</code>
            <?php esc_html_e('and', 'oes');?><code>sorting_title</code>.
        </p>
    </div>

    <div>
        <p><strong><?php esc_html_e('Further Options', 'oes'); ?></strong></p>

        <p><?php esc_html_e('Use the', 'oes'); ?> <code>line</code> <?php esc_html_e('parameter to set the default line color (RGBA or hex).', 'oes'); ?></p>
        <p><?php esc_html_e('Use', 'oes'); ?> <code>line_hovered</code> <?php esc_html_e('to define the color when a line is hovered.', 'oes'); ?></p>

        <p><?php esc_html_e('To hide the preview content defined in the OES "archive data" settings, set', 'oes'); ?> <code>exclude_preview="true"</code>.</p>
        <p><?php esc_html_e('To show filters even when they have no connection to the central list, use', 'oes'); ?> <code>hide_filter="false"</code>.</p>
        <p><?php esc_html_e('To hide the count of connections, use', 'oes'); ?> <code>hide_count="true"</code>.</p>
        <p>
            <?php esc_html_e('The', 'oes'); ?> <code>center_percentage</code>
            <?php esc_html_e('parameter defines the active vertical area of the screen for the central list. Default is', 'oes'); ?>
            <code>0.7</code> <?php esc_html_e('(70%), meaning the top and bottom 15% are inactive.', 'oes'); ?>
        </p>
    </div>
</div>
