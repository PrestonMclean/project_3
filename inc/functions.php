<?php

function get_tags($id)
{

  include('connection.php');

  try {
    // select all the tags that are connected whith $id of entry
    $results = $db->prepare('
      SELECT tags.tag FROM entries
      JOIN tags_entry ON entries.id = tags_entry.entry_id
      JOIN tags ON tags.id = tags_entry.tag_id WHERE entries.id = ?
      ');
    $results->bindValue(1, $id);
    $results->execute();
  } catch (Eception $e) {
    echo 'Error: ' . $e->getMessage();
    return(array());
  }
  $tags;
  //fetch the results and loop thogh all of them
  foreach (($results->fetchAll(PDO::FETCH_ASSOC)) as $item) {
      // put all the tags into the array of $tags
      $tags[] = $item['tag'];
  }
  return $tags;
}

function get_all_tags() {
  include('connection.php');

  try {
    // select all the posible tags
    $results = $db->prepare('SELECT * FROM tags');
    $results->execute();
  } catch (Eception $e) {
    echo 'Error: ' . $e->getMessage();
    return(array());
  }
  // return the array of all the tags
  return ($results->fetchAll(PDO::FETCH_ASSOC));
}

function get_entries($filter = null)
{
  include('connection.php');

  $sql = 'SELECT * FROM entries';

  $join = ' JOIN tags_entry ON entries.id = tags_entry.entry_id';

  $where = ' WHERE tags_entry.entry_id = entries.id AND tags_entry.tag_id = ?';

  $order = ' ORDER BY date DESC';

  try {
    // if there is a filter
    if (!empty($filter)) {
      // select all entries that have the tag in filter and order by date decending
      $results = $db->prepare($sql . $join . $where . $order);
      $results->bindValue(1, $filter);
    } else {
      // select all the entry and order by date decending
      $results = $db->prepare($sql . $order);
    }
    $results->execute();
  } catch (Eception $e) {
    echo 'Error: ' . $e->getMessage();
    return(array());
  }
  return ($results->fetchAll(PDO::FETCH_ASSOC));
}

function get_entry($id)
{
  include('connection.php');

  $entry;

  try {
    // selet the entry whith the id = to $id
    $results = $db->prepare('SELECT * FROM entries WHERE id = ?');
    $results->bindValue(1, $id);
    $results->execute();
  } catch (Eception $e) {
    echo 'Error: ' . $e->getMessage();
    return false;
  }
  $entry = $results->fetch();
  return ($entry);

}

function add_entry($title, $date, $time, $learned, $resources, $tags, $id = null)
{
  include('inc/connection.php');

  if ($id) {
    // if editing a entry use update
    $sql = 'UPDATE entries SET title = ?, date = ?, time_spent = ?, learned = ?, resources = ? WHERE id = ?';
    $entry_id = $id;
  } else {
    // if new use insert
    $sql = 'INSERT INTO entries(title, date, time_spent, learned, resources) VALUES(?, ?, ?, ?, ?)';
  }

  try {
    $results = $db->prepare($sql);
    $results->bindValue(1, $title);
    $results->bindValue(2, $date);
    $results->bindValue(3, $time);
    $results->bindValue(4, $learned);
    $results->bindValue(5, $resources);
    if ($id) {
      $results->bindValue(6, $id);
    }
    $results->execute();
    if (!($id)) {
      // if it is new set $entry_id to the last $id
      $entry_id = get_last_entry_id();
    }
    // get the ids in an array of all the $tags
    // add all the tags ids with the $entry_id
    if (!add_tag(get_tags_ids($tags), $entry_id)) {
      // if you can not add the tags
      return false;
    }
  } catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
    return false;
  }
  return true;
}

function add_tag($tag_ids, $id) {

  include('inc/connection.php');

    try {
      // select all from tag_entry for the entry whith the id == $id
      $results = $db->prepare('SELECT * FROM tags_entry WHERE entry_id = ?');
      $results->bindValue(1, $id);
      $results->execute();
      $tags = $results->fetchAll(PDO::FETCH_ASSOC);
      if (!empty($tags)) {
        // if editing the tags delete all the tags for $id
        delete_tags($id);
      }
      // loop thogh all the $tag_ids
      foreach ($tag_ids as $tag_id) {
        // insert each tag_id whith $id into tags_entry
        $results = $db->prepare('INSERT INTO tags_entry(tag_id, entry_id) VALUES(?, ?)');
        $results->bindValue(1, $tag_id);
        $results->bindValue(2, $id);
        $results->execute();
        $results = $db->prepare('SELECT * FROM tags_entry');
        $results->execute();
      }
    } catch (Exception $e) {
      echo 'Error: ' . $e->getMessage();
      return false;
    }
    return ($results->fetchAll(PDO::FETCH_ASSOC));
  }

function get_tags_ids($tags) {
  include('connection.php');

  // select all ids of the tags
  $sql = 'SELECT id FROM tags WHERE tag = ?';

  try {
    // if there is just one tag
    if (is_string($tags)) {
      $results = $db->prepare($sql);
      $results->bindValue(1, $tags);
      $results->execute();
      // fetch the id of the row
      $tag_ids = $results->fetch(PDO::FETCH_ASSOC)['id'];
    } else {
    // if there ore mutipule tags
    foreach ($tags as $tag) {
      $results = $db->prepare($sql);
      $results->bindValue(1, $tag);
      $results->execute();
      // make an arry of all the tag ids
      $tag_ids[] = $results->fetch(PDO::FETCH_ASSOC)['id'];
    }
    }
  } catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
    return false;
  }
  // return the array of ids
  return $tag_ids;
}

function get_last_entry_id() {

  include('connection.php');

  // select the id of the last entry
  $sql = 'SELECT id FROM entries ORDER BY id DESC limit 1';

  try {
      $results = $db->prepare($sql);
      $results->execute();
      // put the id into $entry_id
      $entry_id = $results->fetch(PDO::FETCH_ASSOC)['id'];
  } catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
    return false;
  }
  return $entry_id;
}

function delete_tags($id) {
  include('connection.php');

  try {
    // delete all the tags for the entry with id == $id
    $results = $db->prepare('DELETE FROM tags_entry WHERE entry_id = ?');
    $results->bindValue(1, $id);
    $results->execute();
  } catch (Eception $e) {
    echo 'Error: ' . $e->getMessage();
    return false;
  }
  return true;
}

function delete_entry($id)
{
  include('connection.php');

  try {
    // delete the entry with id == $id
    $results = $db->prepare('DELETE FROM entries WHERE id = ?');
    $results->bindValue(1, $id);
    $results->execute();
    if (!delete_tags($id)) {
      return false;
    }
  } catch (Eception $e) {
    echo 'Error: ' . $e->getMessage();
    return false;
  }
  return true;
}
