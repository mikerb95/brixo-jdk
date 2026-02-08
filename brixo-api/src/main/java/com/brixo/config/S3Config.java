package com.brixo.config;

import org.springframework.beans.factory.annotation.Value;
import org.springframework.context.annotation.Bean;
import org.springframework.context.annotation.Configuration;
import software.amazon.awssdk.auth.credentials.AwsBasicCredentials;
import software.amazon.awssdk.auth.credentials.StaticCredentialsProvider;
import software.amazon.awssdk.regions.Region;
import software.amazon.awssdk.services.s3.S3Client;

/**
 * Configuración del cliente AWS S3 para subida de fotos de perfil.
 * Se activa sólo si las credenciales están configuradas en application.yml / variables de entorno.
 */
@Configuration
public class S3Config {

    @Value("${aws.s3.access-key:}")
    private String accessKey;

    @Value("${aws.s3.secret-key:}")
    private String secretKey;

    @Value("${aws.s3.region:us-east-1}")
    private String region;

    @Bean
    public S3Client s3Client() {
        if (accessKey.isBlank() || secretKey.isBlank()) {
            // Sin credenciales → retorna un cliente que fallará si se usa.
            // El servicio de storage debe usar fallback local.
            return S3Client.builder()
                    .region(Region.of(region))
                    .build();
        }

        return S3Client.builder()
                .region(Region.of(region))
                .credentialsProvider(
                        StaticCredentialsProvider.create(
                                AwsBasicCredentials.create(accessKey, secretKey)
                        )
                )
                .build();
    }
}
