let wmaChartInstance = null;

$(document).ready(function () {
    loadForecastData();

    $('#ingredient-select').change(function () {
        loadForecastData($(this).val());
    });

    $('#form-run-forecast').submit(function (e) {
        e.preventDefault();
        runForecast();
    });
});

function loadForecastData(ingredientId = null) {
    let url = API_URL + '/forecast';
    if (ingredientId) {
        url += '?ingredient_id=' + ingredientId;
    }

    $.ajax({
        url: url,
        type: 'GET',
        success: function (res) {
            let data = res.data;

            // Populate select if empty
            let select = $('#ingredient-select');
            if (select.find('option').length <= 1) {
                select.empty();
                data.ingredients.forEach(ing => {
                    let selected = data.selected_ingredient_id == ing.id ? 'selected' : '';
                    select.append(`<option value="${ing.id}" ${selected}>${ing.name} (${ing.unit})</option>`);
                });
            }

            // Render Chart and Result
            if (data.chart_data) {
                renderChart(data.chart_data);
                renderResult(data.chart_data);
                $('#chart-empty-state').addClass('d-none');
                $('#chart-container').removeClass('d-none');
                $('#forecast-result-container').removeClass('d-none');
            } else {
                $('#chart-empty-state').removeClass('d-none');
                $('#chart-container').addClass('d-none');
                $('#forecast-result-container').addClass('d-none');
            }
        },
        error: function () {
            showAlert('error', 'Gagal memuat data peramalan.');
        }
    });
}

function renderResult(chartData) {
    $('#next-forecast-val').text(parseFloat(chartData.next_forecast).toFixed(1));
    $('#used-weights').text('[' + chartData.weights + ']');
    $('#mae-val').text(parseFloat(chartData.mae).toFixed(2));
}

function renderChart(chartData) {
    const ctx = document.getElementById('wmaChart').getContext('2d');
    
    if (wmaChartInstance) {
        wmaChartInstance.destroy();
    }

    wmaChartInstance = new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartData.labels,
            datasets: [
                {
                    label: 'Penggunaan Aktual',
                    data: chartData.actuals,
                    borderColor: '#4361ee',
                    backgroundColor: '#4361ee',
                    borderWidth: 2,
                    tension: 0.1,
                    pointRadius: 4,
                    pointHoverRadius: 6
                },
                {
                    label: 'Hasil Peramalan (WMA)',
                    data: chartData.forecasts,
                    borderColor: '#c97d20',
                    backgroundColor: '#c97d20',
                    borderWidth: 3,
                    borderDash: [5, 5], // Garis putus-putus
                    tension: 0.1,
                    pointStyle: 'rectRot',
                    pointRadius: 6,
                    pointHoverRadius: 8
                }
            ]
        },
        options: {
            responsive: true,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) label += ': ';
                            if (context.parsed.y !== null) {
                                label += parseFloat(context.parsed.y).toFixed(2);
                            }
                            return label;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Jumlah Penggunaan'
                    }
                }
            }
        }
    });
}

function runForecast() {
    let btn = $('#btn-run');
    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Memproses...');

    $.ajax({
        url: API_URL + '/forecast/run',
        type: 'POST',
        data: {
            weights: $('#wma-weights').val()
        },
        success: function (res) {
            showAlert('success', res.message);
            loadForecastData($('#ingredient-select').val()); // reload current
        },
        error: function (xhr) {
            handleValidationErrors(xhr);
        },
        complete: function () {
            btn.prop('disabled', false).html('<i class="bi bi-play-fill me-1"></i> Jalankan Prediksi (Semua Bahan)');
        }
    });
}
