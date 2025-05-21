<?php
function getAllEvents(PDO $dbh): array
{
    $stmt = $dbh->query('SELECT e.id, ei.image_path, ei.image_type 
                        FROM events e 
                        LEFT JOIN event_images ei ON ei.event_id = e.id
                        WHERE ei.image_type = "バナー"');
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $events = [];

    foreach ($results as $row) {
        $eventId = $row['id'];
        
        if (!isset($events[$eventId])) {
            $events[$eventId] = [
                'id' => $row['id'],
                'images' => []  
            ];
        }
        
        $events[$eventId]['images'][] = [
            'image_path' => $row['image_path'],
            'image_type' => $row['image_type']
        ];
    }
    
    return $events;
}
