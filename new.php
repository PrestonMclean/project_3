<?php
include('inc/header.php');

$id = $title = $date = $time_spent = $learned = $resources = $tags = '';

if (isset($_GET['id'])) {
  // get all columns of entry and set each to thier own variable
  list($id, $title, $date, $time_spent, $learned, $resources) = get_entry(filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT));
  // set $tags to an array of all the tags of the entry
  $tags = get_tags($id);
}

// if form has been submited
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // set the submited values to variables
  $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
  $title = trim(filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING));
  $date = trim(filter_input(INPUT_POST, 'date', FILTER_SANITIZE_STRING));
  $time_spent = filter_input(INPUT_POST, 'timeSpent', FILTER_SANITIZE_STRING);
  $learned = filter_input(INPUT_POST, 'whatILearned', FILTER_SANITIZE_STRING);
  $resources = filter_input(INPUT_POST, 'ResourcesToRemember', FILTER_SANITIZE_STRING);
  $tags = null;
  foreach (get_all_tags() as $tag) {
    if (!empty($_POST[$tag['id']])) {
      $tags[] = $_POST[$tag['id']];
    }
  }
  if (empty($title) || empty($date) || empty($time_spent) || empty($learned) || empty($resources) || empty($tags)) {
    $error_message = 'Please make sure to fill in the required fields: Title, Date, Time spent, What I Learned, Resources to Remember, and Tags';
  } else {
    if (add_entry($title, $date, $time_spent, $learned, $resources, $tags, $id)) {
      header('Location: index.php');
      exit;
    } else {
      $error_message = 'Could not add project';
    }
  }
}

?>
                <div class="new-entry">
                  <?php if(!empty($error_message)) { echo "<h2>". $error_message ."</h2>";}?>
                    <h2><?php
                      if($id) {
                        echo "Edit ";
                      } else {
                        echo "New ";
                      }
                    ?>Entry</h2>
                    <form method="post" action="<?php echo 'new.php?id=' . $id;?>">
                        <label for="title"> Title</label>
                        <input id="title" type="text" name="title" value="<?php echo $title;?>"><br>
                        <label for="date">Date</label>
                        <input id="date" type="date" name="date" value="<?php echo $date;?>"><br>
                        <label for="time-spent"> Time Spent</label>
                        <input id="time-spent" type="text" name="timeSpent" value="<?php echo $time_spent;?>"><br>
                        <label for="what-i-learned">What I Learned</label>
                        <textarea id="what-i-learned" rows="5" name="whatILearned"><?php echo $learned;?></textarea>
                        <label for="resources-to-remember">Resources to Remember</label>
                        <textarea id="resources-to-remember" rows="5" name="ResourcesToRemember"><?php echo $resources;?></textarea>
                        <?php
                          foreach (get_all_tags() as $tag) {
                            if (!empty($tags) && in_array($tag['tag'], $tags)) {
                              foreach ($tags as $item) {
                                if ($tag['tag'] == $item) {
                                  echo "<input id='".$tag['id']."' type='checkbox' name='".$tag['id']."' value='".$tag['tag']."' checked> ";
                                  echo "<label for='".$tag['tag']."' class='tag'>  ". $tag['tag'] ."</label></br>";
                                  $item = null;
                                }
                              }
                            } else {
                              echo "<input id='".$tag['id']."' type='checkbox' name='".$tag['id']."' value='".$tag['tag']."'> ";
                              echo "<label for='".$tag['tag']."' class='tag'> ". $tag['tag'] ."</label></br>";
                            }
                          }
                        ?>
                        <input type="submit" value="Publish Entry" class="button">
                        <a href="index.php" class="button button-secondary">Cancel</a>
                    </form>
                </div>
<?php
  include('inc/footer.php');
?>
