package com.brixo.repository;

import com.brixo.entity.Admin;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;

import java.util.Optional;

@Repository
public interface AdminRepository extends JpaRepository<Admin, Long> {

    Optional<Admin> findByCorreo(String correo);

    boolean existsByCorreo(String correo);
}
