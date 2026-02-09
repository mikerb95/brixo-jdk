package com.brixo.controller;

import com.brixo.service.ReporteService;
import org.springframework.http.HttpHeaders;
import org.springframework.http.MediaType;
import org.springframework.http.ResponseEntity;
import org.springframework.stereotype.Controller;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.RequestMapping;

import java.io.IOException;
import java.time.LocalDate;
import java.time.format.DateTimeFormatter;

/**
 * Controlador de reportes Excel.
 *
 * Rutas:
 *   GET /reportes/contratistas     — Descarga XLSX de contratistas
 *   GET /reportes/solicitudes-xlsx — Descarga XLSX de solicitudes
 */
@Controller
@RequestMapping("/reportes")
public class ReportesController {

    private static final String XLSX_CONTENT_TYPE =
            "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";
    private static final DateTimeFormatter FILE_DATE = DateTimeFormatter.ofPattern("yyyy-MM-dd");

    private final ReporteService reporteService;

    public ReportesController(ReporteService reporteService) {
        this.reporteService = reporteService;
    }

    @GetMapping("/contratistas")
    public ResponseEntity<byte[]> contratistas() throws IOException {
        byte[] xlsx = reporteService.reporteContratistas();
        String filename = "reporte_contratistas_" + LocalDate.now().format(FILE_DATE) + ".xlsx";

        return ResponseEntity.ok()
                .header(HttpHeaders.CONTENT_DISPOSITION, "attachment; filename=\"" + filename + "\"")
                .contentType(MediaType.parseMediaType(XLSX_CONTENT_TYPE))
                .body(xlsx);
    }

    @GetMapping("/solicitudes-xlsx")
    public ResponseEntity<byte[]> solicitudes() throws IOException {
        byte[] xlsx = reporteService.reporteSolicitudes();
        String filename = "reporte_solicitudes_" + LocalDate.now().format(FILE_DATE) + ".xlsx";

        return ResponseEntity.ok()
                .header(HttpHeaders.CONTENT_DISPOSITION, "attachment; filename=\"" + filename + "\"")
                .contentType(MediaType.parseMediaType(XLSX_CONTENT_TYPE))
                .body(xlsx);
    }
}
