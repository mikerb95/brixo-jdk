package com.brixo.service;

import com.brixo.entity.Contratista;
import com.brixo.entity.Solicitud;
import com.brixo.repository.ContratistaRepository;
import com.brixo.repository.SolicitudRepository;
import org.apache.poi.ss.usermodel.*;
import org.apache.poi.xssf.usermodel.XSSFWorkbook;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.stereotype.Service;

import java.io.ByteArrayOutputStream;
import java.io.IOException;
import java.time.format.DateTimeFormatter;
import java.util.List;

/**
 * Servicio de generación de reportes Excel (XLSX).
 *
 * Reemplaza SimpleXLSXGen del proyecto PHP usando Apache POI.
 * Genera reportes de contratistas y solicitudes para el panel admin.
 */
@Service
public class ReporteService {

    private static final Logger log = LoggerFactory.getLogger(ReporteService.class);
    private static final DateTimeFormatter DATE_FMT = DateTimeFormatter.ofPattern("yyyy-MM-dd HH:mm");

    private final ContratistaRepository contratistaRepo;
    private final SolicitudRepository solicitudRepo;

    public ReporteService(ContratistaRepository contratistaRepo,
                          SolicitudRepository solicitudRepo) {
        this.contratistaRepo = contratistaRepo;
        this.solicitudRepo = solicitudRepo;
    }

    /**
     * Genera reporte Excel de todos los contratistas.
     *
     * @return bytes del archivo XLSX
     */
    public byte[] reporteContratistas() throws IOException {
        List<Contratista> contratistas = contratistaRepo.findAll();

        try (Workbook workbook = new XSSFWorkbook()) {
            Sheet sheet = workbook.createSheet("Contratistas");

            // Estilo cabecera
            CellStyle headerStyle = createHeaderStyle(workbook);

            // Cabeceras
            String[] headers = {
                    "ID", "Nombre", "Correo", "Teléfono",
                    "Ciudad", "Verificado", "Fecha Registro"
            };
            Row headerRow = sheet.createRow(0);
            for (int i = 0; i < headers.length; i++) {
                Cell cell = headerRow.createCell(i);
                cell.setCellValue(headers[i]);
                cell.setCellStyle(headerStyle);
            }

            // Datos
            int rowIdx = 1;
            for (Contratista c : contratistas) {
                Row row = sheet.createRow(rowIdx++);
                row.createCell(0).setCellValue(c.getId());
                row.createCell(1).setCellValue(c.getNombre());
                row.createCell(2).setCellValue(c.getCorreo());
                row.createCell(3).setCellValue(c.getTelefono() != null ? c.getTelefono() : "");
                row.createCell(4).setCellValue(c.getCiudad() != null ? c.getCiudad() : "");
                row.createCell(5).setCellValue(Boolean.TRUE.equals(c.getVerificado()) ? "Sí" : "No");
                row.createCell(6).setCellValue(
                        c.getCreadoEn() != null ? c.getCreadoEn().format(DATE_FMT) : ""
                );
            }

            // Auto-size columnas
            for (int i = 0; i < headers.length; i++) {
                sheet.autoSizeColumn(i);
            }

            return toBytes(workbook);
        }
    }

    /**
     * Genera reporte Excel de solicitudes.
     *
     * @return bytes del archivo XLSX
     */
    public byte[] reporteSolicitudes() throws IOException {
        List<Solicitud> solicitudes = solicitudRepo.findAll();

        try (Workbook workbook = new XSSFWorkbook()) {
            Sheet sheet = workbook.createSheet("Solicitudes");

            CellStyle headerStyle = createHeaderStyle(workbook);

            String[] headers = {
                    "ID", "Título", "Descripción", "Estado",
                    "Cliente ID", "Contratista ID", "Fecha Creación"
            };
            Row headerRow = sheet.createRow(0);
            for (int i = 0; i < headers.length; i++) {
                Cell cell = headerRow.createCell(i);
                cell.setCellValue(headers[i]);
                cell.setCellStyle(headerStyle);
            }

            int rowIdx = 1;
            for (Solicitud s : solicitudes) {
                Row row = sheet.createRow(rowIdx++);
                row.createCell(0).setCellValue(s.getId());
                row.createCell(1).setCellValue(s.getTitulo() != null ? s.getTitulo() : "");
                row.createCell(2).setCellValue(truncate(s.getDescripcion(), 100));
                row.createCell(3).setCellValue(s.getEstado().name());
                row.createCell(4).setCellValue(
                        s.getCliente() != null ? s.getCliente().getId() : 0
                );
                row.createCell(5).setCellValue(
                        s.getContratista() != null ? s.getContratista().getId() : 0
                );
                row.createCell(6).setCellValue(
                        s.getCreadoEn() != null ? s.getCreadoEn().format(DATE_FMT) : ""
                );
            }

            for (int i = 0; i < headers.length; i++) {
                sheet.autoSizeColumn(i);
            }

            return toBytes(workbook);
        }
    }

    // ═══════════════════════════════════════════
    // Helpers
    // ═══════════════════════════════════════════

    private CellStyle createHeaderStyle(Workbook workbook) {
        CellStyle style = workbook.createCellStyle();
        Font font = workbook.createFont();
        font.setBold(true);
        font.setFontHeightInPoints((short) 11);
        style.setFont(font);
        style.setFillForegroundColor(IndexedColors.LIGHT_CORNFLOWER_BLUE.getIndex());
        style.setFillPattern(FillPatternType.SOLID_FOREGROUND);
        style.setBorderBottom(BorderStyle.THIN);
        return style;
    }

    private byte[] toBytes(Workbook workbook) throws IOException {
        try (ByteArrayOutputStream out = new ByteArrayOutputStream()) {
            workbook.write(out);
            return out.toByteArray();
        }
    }

    private String truncate(String text, int maxLen) {
        if (text == null) return "";
        return text.length() > maxLen ? text.substring(0, maxLen) + "..." : text;
    }
}
