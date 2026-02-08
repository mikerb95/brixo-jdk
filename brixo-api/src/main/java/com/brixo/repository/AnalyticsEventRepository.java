package com.brixo.repository;

import com.brixo.entity.AnalyticsEvent;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.Query;
import org.springframework.data.repository.query.Param;
import org.springframework.stereotype.Repository;

import java.time.LocalDateTime;
import java.util.List;

@Repository
public interface AnalyticsEventRepository extends JpaRepository<AnalyticsEvent, Long> {

    long countByCreatedAtAfter(LocalDateTime since);

    long countByEventType(String eventType);

    @Query("SELECT DISTINCT a.visitorId FROM AnalyticsEvent a WHERE a.createdAt > :since")
    List<String> findDistinctVisitorsSince(@Param("since") LocalDateTime since);

    @Query("""
            SELECT a.deviceType, COUNT(a) FROM AnalyticsEvent a
            WHERE a.createdAt > :since AND a.deviceType IS NOT NULL
            GROUP BY a.deviceType
            ORDER BY COUNT(a) DESC
            """)
    List<Object[]> countByDeviceTypeSince(@Param("since") LocalDateTime since);

    @Query("""
            SELECT a.browser, COUNT(a) FROM AnalyticsEvent a
            WHERE a.createdAt > :since AND a.browser IS NOT NULL
            GROUP BY a.browser
            ORDER BY COUNT(a) DESC
            """)
    List<Object[]> countByBrowserSince(@Param("since") LocalDateTime since);

    @Query("""
            SELECT a.path, COUNT(a) FROM AnalyticsEvent a
            WHERE a.eventType = 'pageview' AND a.createdAt > :since
            GROUP BY a.path
            ORDER BY COUNT(a) DESC
            """)
    List<Object[]> topPagesSince(@Param("since") LocalDateTime since);
}
