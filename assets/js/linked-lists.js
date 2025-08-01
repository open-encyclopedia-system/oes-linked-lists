let oes_filter_copy,
    oes_linked_lists_options,
    oes_linked_lists_filter,
    oes_linked_lists_center_items,
    canvas,
    vw = window.innerWidth / 100,
    vh = window.innerHeight / 100,
    ctx = null,
    canvasRect = null,
    hovered = null;

document.addEventListener('DOMContentLoaded', () => {
    const defaults = {
        left: '',
        top: '',
        hide_filter: true,
        line: 'rgba(100,100,100,0.2)',
        line_hovered: 'rgba(100,100,100,0.7)',
        center_percentage: 0.7
    };

    oes_linked_lists_options = { ...defaults, ...oes_linked_lists_options };

    prepareCanvas();
    markItems();
});

let ticking = false;
window.addEventListener('scroll', () => {
    if (!ticking) {
        window.requestAnimationFrame(() => {
            markItems();
            ticking = false;
        });
        ticking = true;
    }
});

window.addEventListener('resize', () => {
    prepareCanvas();
    markItems();
});

function prepareCanvas() {
    canvas = document.getElementById('oes-linked-lists-canvas');
    if (!canvas || !canvas.getContext) {
        console.error('Canvas not found or unsupported.');
        return;
    }

    ctx = canvas.getContext('2d');
    const dpr = window.devicePixelRatio || 1;
    const width = window.innerWidth;
    const height = window.innerHeight;

    vw = width / 100;
    vh = height / 100;

    canvas.width = width * dpr;
    canvas.height = (height + 200) * dpr;
    canvas.style.width = width + 'px';
    canvas.style.height = (height + 200) + 'px';
    ctx.scale(dpr, dpr);

    oes_linked_lists_center_items = document.querySelectorAll('.oes-post-filter-wrapper');

    oes_linked_lists_center_items.forEach(item => {
        item.addEventListener('mouseenter', () => {
            hovered = item;
            markItems();
        });
    });

    const filterItems = document.querySelectorAll('.oes-linked-lists-filter');
    filterItems.forEach(item => {
        item.addEventListener('mouseenter', () => {
            hovered = item;
            markItems();
        });

        item.addEventListener('click', () => {
            const isSelected = item.classList.contains('oes-linked-lists-filter-selected');
            filterItems.forEach(el => el.classList.remove('oes-linked-lists-filter-selected'));

            if (isSelected) {
                oes_linked_lists_center_items.forEach(el => el.style.display = 'block');
            } else {
                oes_linked_lists_center_items.forEach(el => el.style.display = 'none');

                const type = item.dataset.type;
                const filter = item.dataset.filter;
                const postIDs = oes_filter_copy?.[type]?.[filter] || [];

                postIDs.forEach(postID => {
                    document.querySelectorAll(`.oes-post-filter-${postID}`).forEach(el => {
                        el.style.display = 'block';
                    });
                });

                item.classList.add('oes-linked-lists-filter-selected');
            }

            markItems();
        });
    });
}

function isInViewport(el) {
    const rect = el.getBoundingClientRect();
    const padding = window.innerHeight * (1 - oes_linked_lists_options.center_percentage) * 0.5;
    return rect.top >= padding && rect.bottom <= (window.innerHeight - padding);
}

function markItems() {
    if (!ctx || !canvas) return;

    const w = window.innerWidth;
    const h = window.innerHeight;
    ctx.clearRect(0, 0, w, h + 200);

    canvasRect = canvas.getBoundingClientRect();

    const filters = document.querySelectorAll('.oes-linked-lists-filter-item.oes-linked-lists-active .oes-linked-lists-filter');
    filters.forEach(f => f.parentElement.classList.remove('oes-linked-lists-active'));

    const visibleItems = [];

    oes_linked_lists_center_items.forEach(item => {
        const visible = isInViewport(item) && item.style.display !== 'none';

        if (visible) {
            item.classList.add('oes-linked-lists-active-center');
            visibleItems.push(item);

            const postID = item.dataset.post;
            const filterMap = oes_linked_lists_filter?.[postID];

            if (filterMap) {
                for (const [key, filterIDs] of Object.entries(filterMap)) {
                    filterIDs.forEach(filterID => {
                        const filterEl = document.querySelector(`.oes-linked-lists-filter-${key}-${filterID}`);
                        filterEl?.parentElement.classList.add('oes-linked-lists-active');
                    });
                }
            }
        } else {
            item.classList.remove('oes-linked-lists-active-center');
        }
    });

    visibleItems.forEach(item => {
        const postID = item.dataset.post;
        const filterMap = oes_linked_lists_filter?.[postID];

        if (filterMap) {
            for (const [key, filterIDs] of Object.entries(filterMap)) {
                filterIDs.forEach(filterID => {
                    const filterEl = document.querySelector(`.oes-linked-lists-filter-${key}-${filterID}`);
                    if (!filterEl) return;

                    const color = (hovered === item || hovered === filterEl)
                        ? oes_linked_lists_options.line_hovered
                        : oes_linked_lists_options.line;

                    if (key === oes_linked_lists_options.left) {
                        drawLine(item, filterEl, null, color);
                    } else {
                        drawLine(item, null, filterEl, color);
                    }
                });
            }
        }
    });
}

function drawLine(center, leftFilter, rightFilter, color) {
    const centerRect = center.getBoundingClientRect();
    const dx = -canvasRect.left;
    const dy = -canvasRect.top;
    const h = centerRect.height / 2;

    let start, cp1, cp2, end;

    if (leftFilter) {
        const filterRect = leftFilter.getBoundingClientRect();
        start = {
            x: dx + filterRect.left + filterRect.width + vw / 4,
            y: dy + filterRect.top + filterRect.height * 0.45
        };
        cp1 = {
            x: dx + filterRect.left + filterRect.width + (20 * vw - filterRect.width),
            y: start.y
        };
        cp2 = {
            x: dx + centerRect.left - vw * 4,
            y: dy + centerRect.top + h
        };
        end = {
            x: dx + centerRect.left - vw / 2,
            y: dy + centerRect.top + h
        };
    } else if (rightFilter) {
        const filterRect = rightFilter.getBoundingClientRect();
        start = {
            x: dx + centerRect.left + centerRect.width + vw / 2,
            y: dy + centerRect.top + h
        };
        cp1 = {
            x: start.x + vw * 3.5,
            y: start.y
        };
        cp2 = {
            x: dx + filterRect.left - (20 * vw - filterRect.width),
            y: dy + filterRect.top + filterRect.height * 0.45
        };
        end = {
            x: dx + filterRect.left - vw / 4,
            y: cp2.y
        };
    }

    ctx.lineWidth = 0.075 * vw;
    ctx.strokeStyle = color;
    ctx.beginPath();
    ctx.moveTo(start.x, start.y);
    ctx.bezierCurveTo(cp1.x, cp1.y, cp2.x, cp2.y, end.x, end.y);
    ctx.stroke();
}
