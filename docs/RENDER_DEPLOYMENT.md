# Guía de Despliegue en Render

## Configuración del Servicio

### 1. Crear Web Service en Render
- **Build Command**: (automático con Dockerfile)
- **Start Command**: (automático con Dockerfile ENTRYPOINT)
- **Port**: 8080

### 2. Variables de Entorno Requeridas

#### Base de Datos MySQL
```bash
DB_HOST=your-mysql-host.render.com
DB_PORT=3306
DB_NAME=brixo
DB_USERNAME=brixo_user
DB_PASSWORD=your-secure-password
```

#### Spring Profile
```bash
SPRING_PROFILES_ACTIVE=prod
```

#### OpenAI API (para servicio LLM)
```bash
OPENAI_API_KEY=sk-...
```

#### Configuración de Sesión (opcional)
```bash
SESSION_TIMEOUT=1800
```

### 3. Base de Datos MySQL en Render

1. Crear MySQL instance en Render
2. Copiar las credenciales al servicio web (variables de entorno)
3. Las migraciones Flyway se ejecutan automáticamente al iniciar

### 4. Health Check

Render detectará automáticamente el health check configurado:
- **Endpoint**: `/actuator/health`
- **Interval**: 30s
- **Timeout**: 5s

### 5. Recursos Recomendados

- **Instance Type**: Standard o superior
- **RAM**: 1GB mínimo (2GB recomendado)
- **Autoscaling**: Habilitar si esperas tráfico variable

### 6. Verificación Post-Deploy

```bash
# Health check
curl https://your-app.onrender.com/actuator/health

# Endpoints públicos
curl https://your-app.onrender.com/
curl https://your-app.onrender.com/login
```

## Troubleshooting

### Error: Connection timeout
- Verificar que DB_HOST apunte a la instancia MySQL interna de Render
- Asegurar que el MySQL service esté en la misma región

### Error: 502 Bad Gateway
- Revisar logs: Application no inició correctamente
- Verificar variables de entorno requeridas
- Confirmar que el puerto 8080 esté expuesto

### Error: Cannot connect to database
- Verificar credenciales en variables de entorno
- Confirmar que la base de datos esté activa
- Revisar reglas de firewall/networking en Render

## Notas Importantes

1. **Primera Ejecución**: Flyway creará las tablas automáticamente
2. **Logs**: Accesibles desde el dashboard de Render
3. **Rollback**: Render mantiene historial de deployments
4. **SSL**: Render proporciona SSL/HTTPS automáticamente
5. **CDN**: Activar CDN en settings para mejor performance de assets
