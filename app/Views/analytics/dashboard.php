<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Dashboard de Analíticas') ?> - Brixo</title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    
    <style>
        :root {
            --primary-color: #485166;
            --secondary-color: #6c757d;
            --success-color: #28a745;
            --info-color: #17a2b8;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
        }
        
        body {
            background-color: #f8f9fa;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }
        
        .navbar {
            background: linear-gradient(135deg, var(--primary-color) 0%, #3a4255 100%);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .stat-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            transition: transform 0.2s, box-shadow 0.2s;
            height: 100%;
        }
        
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 4px 16px rgba(0,0,0,0.12);
        }
        
        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }
        
        .chart-container {
            position: relative;
            height: 350px;
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        
        .table-container {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        
        .period-selector {
            background: white;
            border-radius: 12px;
            padding: 15px 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            margin-bottom: 20px;
        }
        
        .btn-period {
            margin: 0 5px;
            border-radius: 8px;
            padding: 8px 20px;
        }
        
        .btn-period.active {
            background-color: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }
        
        .page-header {
            background: white;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        
        .badge-device {
            font-size: 0.85rem;
            padding: 6px 12px;
            border-radius: 6px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="/admin">
                <i class="fas fa-chart-line me-2"></i>
                Dashboard de Analíticas
            </a>
            <div>
                <a href="/admin" class="btn btn-outline-light btn-sm">
                    <i class="fas fa-shield-alt me-1"></i> Panel Admin
                </a>
                <a href="/" class="btn btn-outline-light btn-sm ms-2">
                    <i class="fas fa-home me-1"></i> Inicio
                </a>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-4 mb-5">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h2 class="mb-0">
                        <i class="fas fa-chart-bar text-primary me-2"></i>
                        Analítica First-Party
                    </h2>
                    <p class="text-muted mb-0 mt-2">
                        <i class="fas fa-shield-alt me-1"></i>
                        100% propio • Sin terceros • IPs anonimizadas
                    </p>
                </div>
                <div class="col-md-6 text-end">
                    <span class="badge bg-success" style="font-size: 1rem; padding: 10px 15px;">
                        <i class="fas fa-calendar-alt me-1"></i>
                        Últimos <?= $days ?> días
                    </span>
                </div>
            </div>
        </div>

        <!-- Period Selector -->
        <div class="period-selector">
            <div class="d-flex align-items-center justify-content-between flex-wrap">
                <div>
                    <strong><i class="fas fa-filter me-2"></i>Período:</strong>
                </div>
                <div>
                    <a href="?days=7" class="btn btn-outline-secondary btn-period <?= $days === 7 ? 'active' : '' ?>">
                        Últimos 7 días
                    </a>
                    <a href="?days=30" class="btn btn-outline-secondary btn-period <?= $days === 30 ? 'active' : '' ?>">
                        Últimos 30 días
                    </a>
                    <a href="?days=90" class="btn btn-outline-secondary btn-period <?= $days === 90 ? 'active' : '' ?>">
                        Últimos 90 días
                    </a>
                    <a href="?days=365" class="btn btn-outline-secondary btn-period <?= $days === 365 ? 'active' : '' ?>">
                        Último año
                    </a>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row g-4 mb-4">
            <!-- Visitantes Únicos -->
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted mb-1">Visitantes Únicos</p>
                                <h3 class="mb-0 fw-bold"><?= number_format($stats['unique_visitors']) ?></h3>
                            </div>
                            <div class="stat-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sesiones -->
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted mb-1">Sesiones</p>
                                <h3 class="mb-0 fw-bold"><?= number_format($stats['unique_sessions']) ?></h3>
                            </div>
                            <div class="stat-icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
                                <i class="fas fa-clock"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Páginas Vistas -->
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted mb-1">Páginas Vistas</p>
                                <h3 class="mb-0 fw-bold"><?= number_format($stats['pageviews']) ?></h3>
                            </div>
                            <div class="stat-icon" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white;">
                                <i class="fas fa-eye"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tiempo Promedio -->
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted mb-1">Tiempo Promedio</p>
                                <h3 class="mb-0 fw-bold"><?= number_format($stats['avg_duration'], 1) ?>s</h3>
                            </div>
                            <div class="stat-icon" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); color: white;">
                                <i class="fas fa-stopwatch"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Gráfico de Páginas Vistas por Día -->
            <div class="col-12 col-lg-8">
                <div class="chart-container">
                    <h5 class="mb-3">
                        <i class="fas fa-chart-line me-2 text-primary"></i>
                        Páginas Vistas por Día
                    </h5>
                    <canvas id="pageviewsChart"></canvas>
                </div>
            </div>

            <!-- Distribución por Dispositivo -->
            <div class="col-12 col-lg-4">
                <div class="chart-container">
                    <h5 class="mb-3">
                        <i class="fas fa-mobile-alt me-2 text-primary"></i>
                        Dispositivos
                    </h5>
                    <canvas id="deviceChart"></canvas>
                </div>
            </div>
        </div>

        <div class="row g-4 mt-1">
            <!-- Páginas Más Populares -->
            <div class="col-12 col-lg-6">
                <div class="table-container">
                    <h5 class="mb-3">
                        <i class="fas fa-fire me-2 text-danger"></i>
                        Páginas Más Populares
                    </h5>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Página</th>
                                    <th class="text-center">Visitas</th>
                                    <th class="text-center">Únicos</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($popularPages)): ?>
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">
                                            <i class="fas fa-info-circle me-1"></i>
                                            No hay datos disponibles
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($popularPages as $page): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <strong><?= esc($page['path']) ?></strong>
                                                    <?php if ($page['title']): ?>
                                                        <small class="text-muted"><?= esc($page['title']) ?></small>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-primary"><?= number_format($page['views']) ?></span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-secondary"><?= number_format($page['unique_visitors']) ?></span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Eventos Personalizados -->
            <div class="col-12 col-lg-6">
                <div class="table-container">
                    <h5 class="mb-3">
                        <i class="fas fa-bolt me-2 text-warning"></i>
                        Eventos Personalizados
                    </h5>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Evento</th>
                                    <th class="text-center">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($events)): ?>
                                    <tr>
                                        <td colspan="2" class="text-center text-muted">
                                            <i class="fas fa-info-circle me-1"></i>
                                            No hay eventos personalizados registrados
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($events as $event): ?>
                                        <tr>
                                            <td>
                                                <i class="fas fa-circle me-2" style="color: <?= getEventColor($event['event_type']) ?>; font-size: 8px;"></i>
                                                <strong><?= esc($event['event_type']) ?></strong>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-info"><?= number_format($event['count']) ?></span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navegadores -->
        <div class="row g-4 mt-1">
            <div class="col-12">
                <div class="table-container">
                    <h5 class="mb-3">
                        <i class="fas fa-window-restore me-2 text-info"></i>
                        Navegadores Más Usados
                    </h5>
                    <div class="row">
                        <?php if (empty($browsers)): ?>
                            <div class="col-12 text-center text-muted py-4">
                                <i class="fas fa-info-circle me-1"></i>
                                No hay datos de navegadores disponibles
                            </div>
                        <?php else: ?>
                            <?php foreach ($browsers as $browser): ?>
                                <div class="col-12 col-sm-6 col-lg-4 mb-3">
                                    <div class="d-flex justify-content-between align-items-center p-3 border rounded">
                                        <div>
                                            <strong><?= esc($browser['browser']) ?></strong>
                                            <div class="text-muted small"><?= number_format($browser['count']) ?> visitas</div>
                                        </div>
                                        <div>
                                            <span class="badge bg-secondary" style="font-size: 1rem;">
                                                <?= number_format($browser['percentage'], 1) ?>%
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Helper function para colores de eventos
        function getEventColorJS(eventType) {
            const colors = {
                'click_cta': '#28a745',
                'signup_click': '#17a2b8',
                'cotizador_start': '#ffc107',
                'cotizador_complete': '#28a745',
                'solicitud_created': '#17a2b8',
                'search': '#6610f2',
                'error': '#dc3545'
            };
            return colors[eventType] || '#6c757d';
        }

        // Gráfico de Páginas Vistas por Día
        <?php if (!empty($pageviews)): ?>
        const pageviewsData = {
            labels: <?= json_encode(array_column($pageviews, 'date')) ?>,
            datasets: [{
                label: 'Páginas Vistas',
                data: <?= json_encode(array_column($pageviews, 'views')) ?>,
                borderColor: '#485166',
                backgroundColor: 'rgba(72, 81, 102, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        };

        const pageviewsChart = new Chart(document.getElementById('pageviewsChart'), {
            type: 'line',
            data: pageviewsData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
        <?php endif; ?>

        // Gráfico de Dispositivos (Doughnut)
        <?php if (!empty($deviceBreakdown)): ?>
        const deviceData = {
            labels: <?= json_encode(array_map(function($d) { 
                return ucfirst($d['device_type']); 
            }, $deviceBreakdown)) ?>,
            datasets: [{
                data: <?= json_encode(array_column($deviceBreakdown, 'count')) ?>,
                backgroundColor: [
                    '#667eea',
                    '#f093fb',
                    '#4facfe',
                    '#fa709a',
                    '#fee140'
                ],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        };

        const deviceChart = new Chart(document.getElementById('deviceChart'), {
            type: 'doughnut',
            data: deviceData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
        <?php endif; ?>
    </script>
</body>
</html>

<?php
// Helper function para asignar colores a eventos
function getEventColor($eventType) {
    $colors = [
        'click_cta' => '#28a745',
        'signup_click' => '#17a2b8',
        'cotizador_start' => '#ffc107',
        'cotizador_complete' => '#28a745',
        'solicitud_created' => '#17a2b8',
        'search' => '#6610f2',
        'error' => '#dc3545'
    ];
    return $colors[$eventType] ?? '#6c757d';
}
?>
