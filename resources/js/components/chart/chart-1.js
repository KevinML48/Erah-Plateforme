

export const initChartOne = () => {
    const chartElement = document.querySelector('#chartOne');
    if (!chartElement) return;

    const labelsData = chartElement.getAttribute('data-chart-labels');
    const gainsData = chartElement.getAttribute('data-chart-gains');
    const lossesData = chartElement.getAttribute('data-chart-losses');

    const dynamicLabels = labelsData ? JSON.parse(labelsData) : null;
    const dynamicGains = gainsData ? JSON.parse(gainsData) : null;
    const dynamicLosses = lossesData ? JSON.parse(lossesData) : null;

    const hasDynamicPointsData =
        Array.isArray(dynamicLabels) &&
        Array.isArray(dynamicGains) &&
        Array.isArray(dynamicLosses);

    const chartOneOptions = {
        series: hasDynamicPointsData
            ? [
                {
                    name: "Points gagnes",
                    data: dynamicGains,
                },
                {
                    name: "Points perdus",
                    data: dynamicLosses,
                },
            ]
            : [{
                name: "Sales",
                data: [168, 385, 201, 298, 187, 195, 291, 110, 215, 390, 280, 112],
            }],
        colors: hasDynamicPointsData ? ["#12B76A", "#F04438"] : ["#465fff"],
        chart: {
            fontFamily: "Outfit, sans-serif",
            type: "bar",
            height: 180,
            toolbar: {
                show: false,
            },
        },
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: "39%",
                borderRadius: 5,
                borderRadiusApplication: "end",
            },
        },
        dataLabels: {
            enabled: false,
        },
        stroke: {
            show: true,
            width: 4,
            colors: ["transparent"],
        },
        xaxis: {
            categories: hasDynamicPointsData ? dynamicLabels : [
                "Jan",
                "Feb",
                "Mar",
                "Apr",
                "May",
                "Jun",
                "Jul",
                "Aug",
                "Sep",
                "Oct",
                "Nov",
                "Dec",
            ],
            axisBorder: {
                show: false,
            },
            axisTicks: {
                show: false,
            },
        },
        legend: {
            show: true,
            position: "top",
            horizontalAlign: "left",
            fontFamily: "Outfit",
            markers: {
                radius: 99,
            },
        },
        yaxis: {
            title: false,
        },
        grid: {
            yaxis: {
                lines: {
                    show: true,
                },
            },
        },
        fill: {
            opacity: 1,
        },

        tooltip: {
            x: {
                show: false,
            },
            y: {
                formatter: function (val) {
                    return hasDynamicPointsData ? `${val} pts` : val;
                },
            },
        },
    };

    const chart = new ApexCharts(chartElement, chartOneOptions);
    chart.render();

    return chart;
};

export default initChartOne;
