<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Préstamos - Tipo Cashea (UPTOSCR)</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome para iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f7f6;
        }
        .navbar-brand {
            font-weight: 700;
            letter-spacing: -0.5px;
        }
        .hero-section {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: white;
            border-radius: 16px;
            padding: 3rem 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }
        .card-custom {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transition: transform 0.2s ease;
        }
        .card-custom:hover {
            transform: translateY(-3px);
        }
        .stat-icon {
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            font-size: 1.5rem;
        }
        .table-custom th {
            background-color: #f8fafc;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            color: #64748b;
        }
        .badge-status {
            padding: 0.5em 0.8em;
            font-weight: 600;
            font-size: 0.75rem;
            border-radius: 50rem;
        }
    </style>
</head>
<body>

    <!-- Barra de Navegación -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container-fluid px-4">
            <a class="navbar-brand" href="#">
                <i class="fa-solid fa-bolt text-warning me-2"></i> Cashea<span class="text-info">Express</span> <small class="text-muted fs-6">| UPTOSCR</small>
            </a>
            <div class="d-flex align-items-center">
                <span class="badge bg-success bg-opacity-10 text-success border border-success px-3 py-2 rounded-pill">
                    <i class="fa-solid fa-circle text-success me-1" style="font-size: 8px;"></i> OLTP / OLAP / Auditoría Activos
                </span>
            </div>
        </div>
    </nav>

    <!-- Contenido Principal -->
    <div class="container py-4">
        
        <!-- FACHADA / BIENVENIDA (Hero Section) -->
        <div class="hero-section position-relative overflow-hidden">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <span class="badge bg-warning text-dark mb-2 fw-semibold px-3 py-1 rounded-pill">Entregable IV - PNF en Informática</span>
                    <h1 class="fw-bold display-6 mb-3">Sistema de Gestión de Préstamos <span class="text-warning">Tipo Cashea</span></h1>
                    <p class="text-light opacity-75 lead mb-4">
                        Plataforma híbrida orientada a la trazabilidad transaccional (OLTP), auditoría automatizada en PostgreSQL y análisis gerencial multidimensional (OLAP).
                    </p>
                    <div class="d-flex gap-3">
                        <!-- Botón para abrir el Modal de Agregar Datos -->
                        <button class="btn btn-warning fw-semibold px-4 py-2" data-bs-toggle="modal" data-bs-target="#modalNuevoPrestamo">
                            <i class="fa-solid fa-plus-circle me-2"></i> Registrar Nuevo Préstamo
                        </button>
                        <a href="#seccion-cartera" class="btn btn-outline-light px-4 py-2">
                            <i class="fa-solid fa-chart-line me-2"></i> Ver Cartera Analítica
                        </a>
                    </div>
                </div>
                <div class="col-lg-4 text-center d-none d-lg-block">
                    <i class="fa-solid fa-shield-halved text-warning opacity-50" style="font-size: 9rem;"></i>
                </div>
            </div>
        </div>

        <!-- Tarjetas de Estadísticas (KPIs Rápidos) -->
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card card-custom p-4 bg-white">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-primary bg-opacity-10 text-primary me-3">
                            <i class="fa-solid fa-wallet"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block">Monto Total Cartera</span>
                            <h3 class="fw-bold text-dark mb-0">$1,450.00</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-custom p-4 bg-white">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-success bg-opacity-10 text-success me-3">
                            <i class="fa-solid fa-circle-check"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block">Préstamos Activos</span>
                            <h3 class="fw-bold text-dark mb-0">12</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-custom p-4 bg-white">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-danger bg-opacity-10 text-danger me-3">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block">Préstamos en Mora</span>
                            <h3 class="fw-bold text-dark mb-0">3</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección de Tabla Interactiva (Vista OLAP / Cartera) -->
        <div class="row" id="seccion-cartera">
            <div class="col-12">
                <div class="card card-custom bg-white mb-4">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-0">
                        <h5 class="fw-bold m-0 text-dark"><i class="fa-solid fa-table-cells me-2 text-primary"></i> Resumen Analítico de Cartera (Vista OLAP)</h5>
                        <button class="btn btn-outline-dark btn-sm" onclick="location.reload()">
                            <i class="fa-solid fa-rotate-right me-1"></i> Actualizar Datos
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle table-custom mb-0">
                                <thead>
                                    <tr>
                                        <th class="ps-4">ID Hecho</th>
                                        <th>Cliente</th>
                                        <th>Monto Financiado</th>
                                        <th>Estado Actual</th>
                                        <th>Cuotas Pagadas</th>
                                        <th class="text-end pe-4">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="ps-4 fw-semibold">#H-001</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-size: 14px;">BM</div>
                                                <div>
                                                    <span class="fw-bold d-block text-dark">Belkis Moreno</span>
                                                    <small class="text-muted">V-20.123.456</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="fw-bold text-success">$150.00</td>
                                        <td><span class="badge bg-success bg-opacity-15 text-success badge-status">ACTIVO</span></td>
                                        <td>3 / 3</td>
                                        <td class="text-end pe-4">
                                            <button class="btn btn-sm btn-light text-primary border" title="Ver detalles"><i class="fa-solid fa-eye"></i></button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="ps-4 fw-semibold">#H-002</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-size: 14px;">AG</div>
                                                <div>
                                                    <span class="fw-bold d-block text-dark">Asdrúbal Gómez</span>
                                                    <small class="text-muted">V-18.987.654</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="fw-bold text-warning">$240.00</td>
                                        <td><span class="badge bg-warning bg-opacity-15 text-warning badge-status">EN_MORA</span></td>
                                        <td>1 / 3</td>
                                        <td class="text-end pe-4">
                                            <button class="btn btn-sm btn-light text-primary border" title="Ver detalles"><i class="fa-solid fa-eye"></i></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- MODAL PARA AGREGAR NUEVO PRÉSTAMO (Formulario de Ingreso) -->
    <div class="modal fade" id="modalNuevoPrestamo" tabindex="-1" aria-labelledby="modalNuevoPrestamoLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                <div class="modal-header bg-dark text-white" style="border-top-left-radius: 16px; border-top-right-radius: 16px;">
                    <h5 class="modal-title fw-bold" id="modalNuevoPrestamoLabel"><i class="fa-solid fa-file-invoice-dollar text-warning me-2"></i> Registrar Préstamo (Cashea)</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-muted">Cédula o Nombre del Cliente</label>
                            <input type="text" class="form-control" placeholder="Ej. V-20.123.456 - Belkis Moreno">
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-muted">Monto Total ($)</label>
                                <input type="number" class="form-control" placeholder="150.00">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-muted">Inicial a Pagar (30% / 40%)</label>
                                <input type="number" class="form-control" placeholder="45.00">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-muted">Plazo / Cuotas</label>
                            <select class="form-select">
                                <option>3 Cuotas (Modo Cashea)</option>
                                <option>6 Cuotas Especiales</option>
                            </select>
                        </div>
                        <div class="alert alert-info py-2 small mb-3">
                            <i class="fa-solid fa-circle-info me-1"></i> Al guardar, se disparará el trigger de auditoría en PostgreSQL automáticamente.
                        </div>
                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-light border px-3" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-warning fw-semibold px-4" onclick="alert('Préstamo registrado con éxito y auditado por la base de datos.'); return false;">Guardar Registro</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>