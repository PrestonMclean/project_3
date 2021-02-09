<?php
include('inc/header.php');

if(isset($_POST['delete'])) {
  if (delete_entry($_POST['delete'])) {
    header("Location: index.php?msg=Entry+Deleted");
    exit;
  } else {
    $error_message = 'could not delete that entry';
  }
}

?>
                <div class="entry-list">
                  <?php
                    if (!empty($_GET['tag'])) {
                      $entries = get_entries(get_tags_ids($_GET['tag']));
                      //var_dump(get_entries(2));
                    } else {
                      $entries = get_entries();
                    }
                    foreach ($entries as $entry) {
                      echo "<article>\n";
                          echo "<h2>";
                          echo "<a href='detail.php?id=" . $entry['id'] . "'>" . $entry['title'] . "</a>";
                          echo "</h2>\n";
                          echo "<ul id='tag_list'>";
                          foreach (get_tags($entry['id']) as $tag) {
                            echo "<li><a href='index.php?tag=". $tag ."'>" . ($tag) . "</a></li>";
                          }
                          echo "</ul>";
                          echo "<time datetime='". date( 'Y-m-d' , strtotime($entry['date'])) ."'>";
                             echo date( 'F j, Y' , strtotime($entry['date']));
                          echo "</time>\n";
                          echo "<form method=post action='index.php' onsubmit=\"return confirm('Are you sure you want tot delete this project?');\">\n";
                          echo "<input type='hidden' value='" . $entry['id'] . "' name='delete' />\n";
                          echo "<input type='submit' class='button button-delete' value='Delete' />\n";
                          echo "</form>\n";
                      echo "</article>\n";
                    }
                  ?>
                  <article>
                    <h2>
                      <a href="new.php">+ add new entry</a>
                    </h2>
                  </article>
                </div>
<?php
  include('inc/footer.php');
?>
