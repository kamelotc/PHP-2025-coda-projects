<?php

require_once 'inc/page.inc.php';
require_once 'inc/database.inc.php';



try {

    $Artist = $db->executeQuery("SELECT id, name, cover FROM artist");
} catch (PDOException $ex) {
    echo "Erreur requête base de données : " . $ex->getMessage();
    exit;
}


$ArtistDetail = $db->executeQuery("
        SELECT *
          FROM artist
         WHERE id = :artistId");


$ChansonsTop5 = $db->executeQuery("
    SELECT
        s.id as song_id,
        s.name as song_name,
        s.duration as song_duration,
        s.note as song_note,
        a.cover as album_cover,
        a.name as album_name
    FROM song s
    INNER JOIN album  a ON s.album_id = a.id
    WHERE s.artist_id = :artist_id
    ORDER BY s.note DESC
    LIMIT 5");

$AlbumsDetail = $db->executeQuery("
    SELECT *
    FROM album
    WHERE artist_id = :artistId
    ORDER BY release_date DESC");