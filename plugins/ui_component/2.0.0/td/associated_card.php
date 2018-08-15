<?php
    $children = $component['_children'];
    $associated_field = $component['associated'];

    $shows = $children[$associated_field];
    foreach($shows as $show){
        echo "<p>".$show['title'].$show['value']."</p>";
    }
?>


