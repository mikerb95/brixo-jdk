package com.brixo.config;

import org.springframework.context.annotation.Configuration;
import org.springframework.web.servlet.config.annotation.CorsRegistry;
import org.springframework.web.servlet.config.annotation.ResourceHandlerRegistry;
import org.springframework.web.servlet.config.annotation.ViewControllerRegistry;
import org.springframework.web.servlet.config.annotation.WebMvcConfigurer;

/**
 * Configuración MVC: recursos estáticos, CORS, view controllers.
 */
@Configuration
public class WebConfig implements WebMvcConfigurer {

    @Override
    public void addResourceHandlers(ResourceHandlerRegistry registry) {
        // Servir imágenes subidas localmente (fallback cuando no hay S3)
        registry.addResourceHandler("/uploads/**")
                .addResourceLocations("file:uploads/");
    }

    @Override
    public void addCorsMappings(CorsRegistry registry) {
        registry.addMapping("/api/**")
                .allowedOrigins("*")
                .allowedMethods("GET", "POST")
                .maxAge(3600);
    }

    @Override
    public void addViewControllers(ViewControllerRegistry registry) {
        // Páginas estáticas de info — mapean directamente a templates
        registry.addViewController("/sobre-nosotros").setViewName("info/sobre_nosotros");
        registry.addViewController("/como-funciona").setViewName("info/como_funciona");
        registry.addViewController("/seguridad").setViewName("info/seguridad");
        registry.addViewController("/ayuda").setViewName("info/ayuda");
        registry.addViewController("/unete-pro").setViewName("info/unete_pro");
        registry.addViewController("/historias-exito").setViewName("info/historias_exito");
        registry.addViewController("/recursos").setViewName("info/recursos");
        registry.addViewController("/carreras").setViewName("info/carreras");
        registry.addViewController("/prensa").setViewName("info/prensa");
        registry.addViewController("/blog").setViewName("info/blog");
        registry.addViewController("/politica-cookies").setViewName("info/politica_cookies");
    }
}
