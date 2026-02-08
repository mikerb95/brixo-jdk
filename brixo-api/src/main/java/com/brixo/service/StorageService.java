package com.brixo.service;

import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.beans.factory.annotation.Value;
import org.springframework.stereotype.Service;
import org.springframework.web.multipart.MultipartFile;
import software.amazon.awssdk.core.sync.RequestBody;
import software.amazon.awssdk.services.s3.S3Client;
import software.amazon.awssdk.services.s3.model.PutObjectRequest;

import java.io.IOException;
import java.nio.file.Files;
import java.nio.file.Path;
import java.nio.file.StandardCopyOption;
import java.util.UUID;

/**
 * Servicio de almacenamiento de archivos.
 * Sube a AWS S3 cuando las credenciales están configuradas;
 * utiliza almacenamiento local como fallback.
 *
 * Equivale a la lógica de upload en Panel::actualizarPerfil y Register::register del PHP legacy.
 */
@Service
public class StorageService {

    private static final Logger log = LoggerFactory.getLogger(StorageService.class);

    private final S3Client s3Client;

    @Value("${aws.s3.bucket:brixo-images}")
    private String bucket;

    @Value("${aws.s3.region:us-east-1}")
    private String region;

    @Value("${aws.s3.access-key:}")
    private String accessKey;

    /** Directorio local de uploads (fallback). */
    private static final String LOCAL_UPLOAD_DIR = "uploads/profiles";

    public StorageService(S3Client s3Client) {
        this.s3Client = s3Client;
    }

    /**
     * Sube una foto de perfil y retorna la URL pública.
     *
     * @param file     archivo subido
     * @param folder   subcarpeta (e.g. "profiles")
     * @return URL pública de la imagen
     */
    public String uploadProfilePhoto(MultipartFile file, String folder) {
        if (file == null || file.isEmpty()) {
            return null;
        }

        String extension = getExtension(file.getOriginalFilename());
        String filename = UUID.randomUUID() + extension;

        if (isS3Configured()) {
            return uploadToS3(file, folder + "/" + filename);
        } else {
            return uploadToLocal(file, filename);
        }
    }

    /**
     * Sube a AWS S3 y retorna la URL pública.
     */
    private String uploadToS3(MultipartFile file, String key) {
        try {
            PutObjectRequest putRequest = PutObjectRequest.builder()
                    .bucket(bucket)
                    .key(key)
                    .contentType(file.getContentType())
                    .build();

            s3Client.putObject(putRequest,
                    RequestBody.fromInputStream(file.getInputStream(), file.getSize()));

            String url = String.format("https://%s.s3.%s.amazonaws.com/%s", bucket, region, key);
            log.info("Archivo subido a S3: {}", url);
            return url;

        } catch (Exception e) {
            log.error("Error al subir a S3, usando fallback local: {}", e.getMessage());
            return uploadToLocal(file, key.substring(key.lastIndexOf('/') + 1));
        }
    }

    /**
     * Almacenamiento local como fallback.
     */
    private String uploadToLocal(MultipartFile file, String filename) {
        try {
            Path uploadPath = Path.of(LOCAL_UPLOAD_DIR);
            Files.createDirectories(uploadPath);

            Path filePath = uploadPath.resolve(filename);
            Files.copy(file.getInputStream(), filePath, StandardCopyOption.REPLACE_EXISTING);

            log.info("Archivo guardado localmente: {}", filePath);
            return "/uploads/profiles/" + filename;

        } catch (IOException e) {
            log.error("Error al guardar archivo localmente: {}", e.getMessage());
            throw new RuntimeException("No se pudo guardar el archivo", e);
        }
    }

    private boolean isS3Configured() {
        return accessKey != null && !accessKey.isBlank();
    }

    private String getExtension(String filename) {
        if (filename == null) return ".jpg";
        int dot = filename.lastIndexOf('.');
        return dot >= 0 ? filename.substring(dot) : ".jpg";
    }
}
