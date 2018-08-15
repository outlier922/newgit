<?php
    $shows = $component['_children'];
    foreach($shows as $show){
        echo "<p>".$show['title'].$show['value']."</p>";
    }
?>


