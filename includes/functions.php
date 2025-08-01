<?php

namespace OES\Linked_Lists;

/**
 * Enqueue styles and scripts conditionally when the shortcode is used.
 */
function enqueue_scripts(): void
{
    global $post;
    if ($post instanceof \WP_Post && has_shortcode($post->post_content, 'oes_linked_lists')) {

        $path = plugin_dir_url(__DIR__) . 'assets/';

        wp_enqueue_style(
            'oes-linked-lists',
            $path . 'css/linked-lists.css',
            [],
            '1.0.0'
        );

        wp_enqueue_script(
            'oes-linked-lists',
            $path . 'js/linked-lists.min.js'
        );
    }
}

/**
 * Display linked lists.
 *
 * @return string The lists.
 */
function html($args): string
{
    global $oes_linked_list_args;
    $oes_linked_list_args = $args;
    $class = oes_get_project_class_name('\OES\Linked_Lists\Renderer');
    $renderer = new $class($args);
    return $renderer->render();
}

/**
 * Render a single archive item.
 *
 * @param array  $row           The item data.
 * @param string $title         Title HTML.
 * @param string $previewTable  Preview HTML.
 * @param string $readMore      Read more HTML.
 * @return string Rendered HTML block.
 */
function archive_list_html($row, $title, $previewTable, $readMore): string
{
    global $oes_linked_lists_page;

    if (empty($oes_linked_lists_page)) {
        return '';
    }

    global $oes_linked_list_args;

    $excerpt = '';
    $excerptField = $oes_linked_list_args['list_excerpt'] ?? false;

    if ($excerptField) {
        $excerptValue = $row['data'][$excerptField]['value-display'] ?? oes_get_field($excerptField, $row['id']);
        $excerpt = '<div class="oes-linked-list-excerpt">' . wp_kses_post($excerptValue) . '</div>';
    }

    $postID = esc_attr($row['id']);
    $langClass = esc_attr(oes_get_post_language($postID) ?: 'all');
    $content = wp_kses_post($title);
    $content .= is_string($row['additional']) ? wp_kses_post($row['additional']) : '';
    $content .= isset($row['content']) ? wp_kses_post($row['content']) : '';

    return sprintf(
        '<div class="wp-block-group oes-post-filter-wrapper oes-linked-list-wrapper oes-post-%s oes-post-filter-%s" data-post="%s">
            <div class="wp-block-group">
                <div>%s</div>
                <div class="oes-archive-table-wrapper wp-block-group" id="row%s">
                    %s
                </div>
            </div>
        </div>',
        $langClass,
        $postID,
        $postID,
        $content,
        $postID,
        $excerpt
    );
}