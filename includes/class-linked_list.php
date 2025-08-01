<?php

namespace OES\Linked_Lists;

if (!defined('ABSPATH')) exit;

/**
 * Class Renderer
 *
 * Handles the rendering of OES linked lists, filters, and scripts for the frontend.
 *
 * @package OES\Linked_Lists
 */
class Renderer
{
    /**
     * Whether the list object is a taxonomy.
     *
     * @var bool
     */
    protected bool $is_taxonomy = false;

    /**
     * The post type or taxonomy key for the linked list.
     *
     * @var string
     */
    protected string $list_object = '';

    /**
     * Rendering and filtering options.
     *
     * @var array
     */
    protected array $options = [];

    /**
     * Constructor.
     *
     * @param array $args Arguments to initialize the renderer.
     */
    public function __construct(array $args)
    {
        $this->set_global($args['global'] ?? true);
        $this->set_list_object($args['list'] ?? '');
        $this->set_options($args);
        $this->set_data();
    }

    /**
     * Renders the full linked list HTML.
     *
     * @return string Rendered HTML string.
     */
    public function render(): string
    {
        if (empty($this->list_object)) {
            return $this->empty_list();
        }

        return $this->prepare_html();
    }

    /**
     * Sets the global page context flag.
     *
     * @param bool $setGlobal Whether to set the global page flag.
     */
    protected function set_global(bool $setGlobal = true): void
    {
        global $oes_linked_lists_page;
        $oes_linked_lists_page = $setGlobal;
    }

    /**
     * Returns an empty list placeholder.
     *
     * @return string Empty list string.
     */
    protected function empty_list(): string
    {
        return '';
    }

    /**
     * Sets the list object and determines if it's a taxonomy.
     *
     * @param string $object Post type or taxonomy name.
     */
    protected function set_list_object(string $object): void
    {
        $this->list_object = $object;

        $oes = OES();
        $this->is_taxonomy = isset($oes->taxonomies[$object]);
    }

    /**
     * Extracts and sanitizes display options.
     *
     * @param array $args Input arguments.
     */
    protected function set_options(array $args): void
    {
        $defaults = [
            'top' => null,
            'sort_top' => null,
            'left' => null,
            'sort_left' => null,
            'right' => null,
            'sort_right' => null,
            'hide_filter' => null,
            'line' => null,
            'line_hovered' => null,
            'center_percentage' => null,
            'hide_count' => null,
            'exclude_preview' => false
        ];

        foreach ($defaults as $key => $default) {
            $this->options[$key] = $args[$key] ?? $default;
        }
    }

    /**
     * Sets archive data context based on post type or taxonomy.
     */
    protected function set_data(): void
    {
        $archiveArgs = $this->is_taxonomy
            ? ['taxonomy' => $this->list_object]
            : ['post-type' => $this->list_object];

        oes_set_archive_data('', $archiveArgs);
    }

    /**
     * Prepares and returns the full HTML layout.
     *
     * @return string HTML output.
     */
    protected function prepare_html(): string
    {
        return '
            <canvas id="oes-linked-lists-canvas"></canvas>
            <div class="' . $this->get_class() . '">
                <div class="wp-block-columns is-not-stacked-on-mobile">
                    <div class="wp-block-column" id="oes-linked-lists-left">
                        <div class="wp-block-group oes-linked-lists-sticky-group">
                            ' . $this->get_left_side() . '
                        </div>
                    </div>
                    <div class="wp-block-column" id="oes-linked-lists-middle">
                        <div class="wp-block-group">
                            ' . $this->get_top() . $this->get_list() . '
                        </div>
                    </div>
                    <div class="wp-block-column" id="oes-linked-lists-right">
                        <div class="wp-block-group oes-linked-lists-align-right oes-linked-lists-sticky-group">
                            ' . $this->get_right_side() . '
                        </div>
                    </div>
                </div>
            </div>' . $this->render_script();
    }

    /**
     * Returns the main wrapper CSS class based on options.
     *
     * @return string CSS class string.
     */
    protected function get_class(): string
    {
        $class = 'wp-block-group oes-linked-lists oes-single-content';

        if (!isset($this->options['hide_count']) || ($this->options['hide_count'] && $this->options['hide_count'] !== 'false')) {
            $class .= ' oes-linked-lists-hide-count';
        }

        return $class;
    }

    /**
     * Returns the left-side filter HTML.
     *
     * @return string Filter HTML.
     */
    protected function get_left_side(): string
    {
        return $this->render_filter_html([
            'key' => $this->options['left'] ?? '',
            'sort' => $this->options['sort_left'] ?? 'default',
        ]);
    }

    /**
     * Returns the top filter bar HTML.
     *
     * @return string Filter HTML.
     */
    protected function get_top(): string
    {
        return $this->render_filter_html([
            'key' => $this->options['top'] ?? '',
            'sort' => $this->options['sort_top'] ?? 'default',
            'filter_only' => true,
            'horizontal' => true,
        ]);
    }

    /**
     * Returns the main post list HTML.
     *
     * @return string List HTML.
     */
    protected function get_list(): string
    {
        return oes_get_archive_loop_html([
            'exclude-preview' => $this->options['exclude_preview'] ?? false,
            'reverse-order' => true,
            'alphabet' => false,
            'render_function' => '\OES\Linked_Lists\archive_list_html'
        ]);
    }

    /**
     * Returns the right-side filter HTML.
     *
     * @return string Filter HTML.
     */
    protected function get_right_side(): string
    {
        return $this->render_filter_html([
            'key' => $this->options['right'] ?? '',
            'sort' => $this->options['sort_right'] ?? 'default',
        ]);
    }

    /**
     * Renders the filter list HTML based on a key and sort method.
     *
     * @param array $args Filter rendering options.
     * @return string HTML list of filter items.
     */
    protected function render_filter_html(array $args): string
    {
        global $oes_filter, $oes_archive_count;

        $key = $args['key'] ?? '';
        if (empty($key)) {
            return '';
        }

        $items = $oes_filter['list'][$key]['items'] ?? [];
        if (empty($items)) {
            return '';
        }

        $filterList = [];

        foreach ($items as $itemKey => $itemLabel) {
            switch ($args['sort']) {
                case 'frequency':
                    $score = isset($oes_filter['json'][$key][$itemKey])
                        ? (10000 + $oes_archive_count) - count($oes_filter['json'][$key][$itemKey])
                        : 0;
                    $sortKey = $score . oes_replace_umlaute($itemLabel) . $itemKey;
                    break;

                case 'sorting_title':
                    $sortKey = oes_replace_umlaute(oes_get_display_title($itemKey, ['option' => 'title_sorting_display'])) . $itemKey;
                    break;

                default:
                    $sortKey = oes_replace_umlaute($itemLabel) . $itemKey;
                    break;
            }

            $cssClass = $args['filter_only'] ?? false
                ? 'oes-linked-lists-filter-item-filter-only'
                : 'oes-linked-lists-filter-item';

            $count = $oes_filter['json'][$key][$itemKey] ?? [];

            $filterList[$sortKey] = sprintf(
                '<li class="%1$s"><a href="javascript:void(0)" class="oes-linked-lists-filter-%2$s-%3$s oes-linked-lists-filter" data-type="%2$s" data-filter="%3$s"><span>%4$s</span>%5$s</a></li>',
                $cssClass,
                esc_attr($key),
                esc_attr($itemKey),
                esc_html($itemLabel),
                ($args['filter_only'] ?? false) ? '' : '<span class="oes-filter-item-count">(' . count($count) . ')</span>'
            );
        }

        ksort($filterList);

        $ulClass = ($args['horizontal'] ?? false) ? 'oes-horizontal-list' : 'oes-vertical-list';

        return '<ul class="' . esc_attr($ulClass) . '">' . implode('', $filterList) . '</ul>';
    }

    /**
     * Renders JavaScript variables needed for client-side filtering.
     *
     * @return string Script tag.
     */
    protected function render_script(): string
    {
        global $oes_filter;

        $listFilter = $this->build_filter_mapping();

        return '<script type="text/javascript">
            oes_filter_copy = ' . json_encode($oes_filter['json'] ?? []) . ';
            oes_linked_lists_options = ' . json_encode($this->options) . ';
            ' . (empty($listFilter) ? '' : 'oes_linked_lists_filter = ' . json_encode($listFilter) . ';') . '
        </script>';
    }

    /**
     * Builds a mapping of post IDs to their associated filter IDs.
     *
     * @return array Post ID to filter ID map.
     */
    protected function build_filter_mapping(): array
    {
        global $oes_filter;

        $mapping = [];

        foreach ($oes_filter['json'] ?? [] as $type => $items) {
            if ($type !== ($this->options['left'] ?? null) && $type !== ($this->options['right'] ?? null)) {
                continue;
            }

            foreach ($items as $filterID => $postIDs) {
                foreach ($postIDs as $postID) {
                    if (!isset($mapping[$postID][$type])) {
                        $mapping[$postID][$type] = [];
                    }

                    if (!in_array($filterID, $mapping[$postID][$type], true)) {
                        $mapping[$postID][$type][] = $filterID;
                    }
                }
            }
        }

        return $mapping;
    }
}
