//utils/chartConfig.js
export const createWaterChartConfig = (monthlyConsumption) => ({
    type: "line",
    data: {
        labels: [
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
        datasets: [
            {
                label: "Water Consumption (m³)",
                data: monthlyConsumption,
                borderColor: "rgb(79, 70, 229)",
                backgroundColor: "rgba(79, 70, 229, 0.05)",
                tension: 0.4,
                fill: true,
                borderWidth: 3,
                pointBackgroundColor: "white",
                pointBorderColor: "rgb(79, 70, 229)",
                pointBorderWidth: 3,
                pointRadius: 5,
                pointHoverRadius: 7,
            },
        ],
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: "white",
                titleColor: "#1f2937",
                bodyColor: "#4b5563",
                borderColor: "#e5e7eb",
                borderWidth: 1,
                padding: 12,
                usePointStyle: true,
                boxShadow: "0 4px 12px rgba(0, 0, 0, 0.08)",
                callbacks: {
                    label: (context) => `${context.parsed.y} m³`,
                },
            },
        },
        scales: {
            y: {
                beginAtZero: false,
                grid: { drawBorder: false, color: "#f3f4f6" },
                ticks: {
                    color: "#9ca3af",
                    callback: (value) => `${value} m³`,
                },
            },
            x: {
                grid: { display: false, drawBorder: false },
                ticks: { color: "#9ca3af" },
            },
        },
        interaction: { intersect: false, mode: "index" },
    },
});

export const createDoughnutChartConfig = (
    labels = [],
    data = [],
    colors = []
) => ({
    type: "doughnut",
    data: {
        labels,
        datasets: [
            {
                data,
                backgroundColor: colors,
                borderWidth: 0,
            },
        ],
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: "bottom",
                labels: {
                    usePointStyle: true,
                    padding: 15,
                    font: { size: 11 },
                },
            },
        },
        cutout: "60%",
    },
});
