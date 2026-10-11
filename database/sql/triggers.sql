-- =============================================================================
-- TRIGGERS Y FUNCIONES EN POSTGRESQL PARA AUDITORÍA Y OLAP
-- Base de Datos: cashea_borrow_db
-- =============================================================================

-- 1. Tabla de Auditoría de Cambios de Estado en Préstamos
CREATE TABLE IF NOT EXISTS auditoria_prestamos (
    id SERIAL PRIMARY KEY,
    id_prestamo INT NOT NULL,
    estado_anterior VARCHAR(50),
    estado_nuevo VARCHAR(50),
    fecha_cambio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    usuario_accion VARCHAR(100) DEFAULT CURRENT_USER
);

-- 2. Función Trigger para registrar auditoría
CREATE OR REPLACE FUNCTION fn_auditar_cambio_estado_prestamo()
RETURNS TRIGGER AS $$
BEGIN
    IF (OLD.estado_prestamo IS DISTINCT FROM NEW.estado_prestamo) THEN
        INSERT INTO auditoria_prestamos (id_prestamo, estado_anterior, estado_nuevo)
        VALUES (NEW.id_prestamo, OLD.estado_prestamo, NEW.estado_prestamo);
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- 3. Trigger sobre la tabla 'prestamos_financieros'
DROP TRIGGER IF EXISTS trg_auditoria_prestamos ON prestamos_financieros;
CREATE TRIGGER trg_auditoria_prestamos
AFTER UPDATE ON prestamos_financieros
FOR EACH ROW
EXECUTE FUNCTION fn_auditar_cambio_estado_prestamo();

-- 4. Vista Analítica / OLAP para Inteligencia de Negocios
CREATE OR REPLACE VIEW vista_olap_resumen_cartera AS
SELECT 
    p.id_comercio,
    COUNT(p.id_prestamo) AS total_prestamos_otorgados,
    SUM(p.monto_total) AS cartera_total_otorgada,
    SUM(p.monto_financiado) AS capital_en_riesgo,
    COUNT(CASE WHEN p.estado_prestamo = 'completado' THEN 1 END) AS prestamos_liquidados,
    COUNT(CASE WHEN p.estado_prestamo = 'pendiente' THEN 1 END) AS prestamos_activos
FROM prestamos_financieros p
GROUP BY p.id_comercio;