const getOrCreateLegendList = (chart, id) => {
    const legendContainer = document.getElementById(id);
    let listContainer = legendContainer.querySelector('ul');
    if (!listContainer) {
        listContainer = document.createElement('ul');
        listContainer.className = 'chart-legend dl-horizontal unstyled'
        legendContainer.appendChild(listContainer);
    }
    return listContainer;
};

export const htmlLegendPlugin = {
    id: 'htmlLegend',
    afterUpdate(chart, args, options) {
        const ul = getOrCreateLegendList(chart, options.containerId);

        // Remove old legend items
        while (ul.firstChild) {
            ul.firstChild.remove();
        }

        // Reuse the built-in legendItems generator
        const items = chart.options.plugins.legend.labels.generateLabels(chart);
        items.forEach(item => {
            const li = document.createElement('li');

            const span = document.createElement('span');
            span.className = 'badge';
            span.style.backgroundColor = item.fillStyle;
            span.style.margin = '2px';
            span.textContent = '\u00a0';

            const small = document.createElement('small');
            small.appendChild(span);
            small.append(' ' + item.text);

            li.appendChild(small);
            ul.appendChild(li);
        });
    }
}
