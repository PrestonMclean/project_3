<?php

include 'inc/header.php';

?>
                <div class="entry-list single">
                    <article>
                      <?php
                        // get the entry with id == $id
                        $entry = get_entry($_GET['id']);
                        // show all the columns except id
                        echo "<h1>" . $entry['title'] . "</h1>\n";
                        echo "<time datetime='" . date( 'Y-m-d' , strtotime($entry['date'])) . "'>";
                           echo date( 'F j, Y' , strtotime($entry['date']));
                        echo "</time>\n";
                        echo "<div class='entry'>\n";
                          echo "<h3>Time Spent: </h3>\n";
                          echo "<p>" . $entry['time_spent'] . "</p>\n";
                        echo "</div>\n";
                        echo "<div class='entry'>\n";
                          echo "<h3>What I Learned:</h3>\n";
                          echo "<p>" . $entry['learned'] . "</p>\n";
                        echo "</div>\n";
                        echo "<div class='entry'>\n";
                          echo "<h3>Resources to Remember:</h3>\n";
                          echo "<p>" . $entry['resources'] . "</p>\n";
                        echo "</div>\n";
                        echo "<div class='entry'>\n";
                          echo "<h3>Tags</h3>";
                          echo "<ul>";
                          // loop thogh all tags on entry
                          foreach (get_tags($entry['id']) as $tag) {
                            echo "<li><a href='index.php?tag='". $tag ."'>" . $tag . "</a></li>";
                          }
                          echo "</ul>";
                        echo "</div>";

                      ?>
                    </article>
                </div>
            </div>
            <div class="edit">
                <p><a href="<?php echo 'new.php?id=' . $entry['id'];?>">Edit Entry</a></p>
<?php
  include('inc/footer.php');
?>
