<?php
/**
 * Created by PhpStorm.
 * User: artur
 * Date: 9/3/15
 * Time: 5:02 PM
 */
    foreach($resources as $xmlObject){
        echo "<ul>";

        foreach($xmlObject->channel->item as $entry) {
            echo "<li><a href='$entry->link' target='_blank' title='$entry->title'>" . $entry->title . "</a></li>";
        }
        echo "</ul>";
    }
