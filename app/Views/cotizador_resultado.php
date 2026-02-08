<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="card-header bg-primary text-white py-3 px-4 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold"><i class="fas fa-file-invoice me-2"></i>Cotización Estimada</h5>
        <?php
            $nivel = $cotizacion['complejidad'] ?? 'medio';
            $cls   = ['bajo' => 'badge-complejidad-bajo', 'medio' => 'badge-complejidad-medio', 'alto' => 'badge-complejidad-alto'];
            $icons = ['bajo' => 'check-circle', 'medio' => 'exclamation-circle', 'alto' => 'exclamation-triangle'];
        ?>
        <span class="badge <?= $cls[$nivel] ?? 'bg-secondary' ?> rounded-pill px-3 py-2">
            <i class="fas fa-<?= $icons[$nivel] ?? 'info-circle' ?> me-1"></i><?= ucfirst(esc($nivel)) ?>
        </span>
    </div>
    <div class="card-body p-4">
        <!-- Servicio principal -->
        <div class="mb-4 p-3 bg-light rounded-3">
            <small class="text-muted text-uppercase fw-bold">Servicio Principal</small>
            <h4 class="fw-bold mb-0 mt-1"><?= esc($cotizacion['servicio_principal']) ?></h4>
        </div>

        <!-- Materiales -->
        <h6 class="fw-bold text-uppercase text-muted mb-3"><i class="fas fa-tools me-2"></i>Materiales</h6>
        <div class="table-responsive mb-4">
            <table class="table table-borderless mb-0">
                <thead><tr><th>Material</th><th class="text-end">Cantidad</th></tr></thead>
                <tbody>
                    <?php foreach ($cotizacion['materiales'] as $mat): ?>
                        <tr>
                            <td><i class="fas fa-box text-muted me-2"></i><?= esc($mat['nombre']) ?></td>
                            <td class="text-end fw-semibold"><?= esc($mat['cantidad_estimada']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Personal -->
        <h6 class="fw-bold text-uppercase text-muted mb-3"><i class="fas fa-users me-2"></i>Personal</h6>
        <div class="table-responsive">
            <table class="table table-borderless mb-0">
                <thead><tr><th>Rol</th><th class="text-end">Horas Est.</th></tr></thead>
                <tbody>
                    <?php foreach ($cotizacion['personal'] as $per): ?>
                        <tr>
                            <td><i class="fas fa-hard-hat text-muted me-2"></i><?= esc($per['rol']) ?></td>
                            <td class="text-end fw-semibold"><?= esc($per['horas_estimadas']) ?> hrs</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white border-top py-3">
        <small class="text-muted d-block text-center mb-3"><i class="fas fa-info-circle me-1"></i>Esta es una estimación generada por IA. Los valores reales pueden variar.</small>
        <?php if (!empty(session()->get('user'))): ?>
            <form action="/cotizador/confirmar" method="post">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-success btn-lg rounded-pill w-100 fw-bold">
                    <i class="fas fa-clipboard-list me-2"></i>Crear Solicitud de Servicio
                </button>
            </form>
        <?php else: ?>
            <a href="#" class="btn btn-outline-primary btn-lg rounded-pill w-100 fw-bold" data-bs-toggle="modal" data-bs-target="#loginModal">
                <i class="fas fa-sign-in-alt me-2"></i>Inicia sesión para continuar
            </a>
        <?php endif; ?>
    </div>
</div>
