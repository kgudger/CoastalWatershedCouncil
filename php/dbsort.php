<?php
/**
* @file dbsort.php
* Purpose: sort data base
*
* @author Keith Gudger
* @version 1.0 10/28/15
*
* @note CWC Data base sort
*/

/**
 * dbstart.php opens the database and gets the user variables
 */
require_once("includes/dbstart.php");

include_once("includes/dbsortpage.php");

/**
 * The checkArray defines what checkForm does so you don't
 * have to overwrite it in the derived class. */

$checkArray = array();
/// a new instance of the derived class (from MainPage)
$dbsort = new dbSortPage($db,$sessvar,$checkArray) ;
/// and ... start it up!  
$dbsort->main("Welcome", $uid, "", "dfile.php");
/**
 * There are 2 choices for redirection dependent on the sessvar
 * above which one gets taken.
 * For this page, altredirect to download. */

?>
