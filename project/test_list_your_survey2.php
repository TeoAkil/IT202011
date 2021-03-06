<?php require_once(__DIR__ . "/partials/nav.php"); ?>

<?php
if (!is_logged_in()) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    flash("You must be logged in to access this page");
    die(header("Location: login.php"));
}
$page = 1;
$per_page = 10;
if(isset($_GET["page"])){
    try {
        $page = (int)$_GET["page"];
    }
    catch(Exception $e){

    }
}
$db = getDB();
$stmt = $db->prepare("SELECT count(*) as total from Survey s where s.user_id = :id");
$stmt->execute([":id"=>get_user_id()]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);
$total = 0;
if($result){
    $total = (int)$result["total"];
}
$total_pages = ceil($total / $per_page);
$offset = ($page-1) * $per_page;
$stmt = $db->prepare("SELECT s.* from Survey s where s.user_id = :id ORDER BY id DESC LIMIT :offset, :count");
$stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
$stmt->bindValue(":count", $per_page, PDO::PARAM_INT);
$stmt->bindValue(":id", get_user_id());
$stmt->execute();
$e = $stmt->errorInfo();
if($e[0] != "00000"){
    flash(var_export($e, true), "alert");
}
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
    <div class="container-fluid">
    <h3>Surveys You Made</h3>
    <div class="row">
    <div class="card-group">
<?php if($results && count($results) > 0):?>
    <?php foreach($results as $r):?>
        <div class="col-auto mb-3">
            <div class="card" style="width: 18rem;">
                <div class="card-body">
                    <div class="card-title">
                        <div>Title: <?php safer_echo($r["title"]);?></div>
                    </div>
                    <div class="card-text">
                        <div>Description: <?php safer_echo($r["description"]); ?></div>
                    </div>
                    <div class="card-footer">
                        <div>Times Taken: <?php safer_echo($r["total"]); ?></div>
                    </div>
	      	    <div>
                        <a type="button" href="test_edit_survey.php?id=<?php safer_echo($r['id']); ?>">Edit Survey Info</a>
                        <a type="button" href="test_view_survey.php?id=<?php safer_echo($r['id']); ?>">View Survey Info</a>
			<a type="button" href="test_create_questions.php?id=<?php safer_echo($r['id']); ?>">Add Question</a>
			<a type="button" href="test_list_questions.php?id=<?php safer_echo($r['id']); ?>">View/Edit Questions</a>
			<a type="button" href="test_results.php?id=<?php safer_echo($r['id']); ?>">Results Page </a>
			<a type="button" href="test_view_profile.php?id=<?php safer_echo($r['user_id']); ?>">View Profile </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach;?>

<?php else:?>
<div class="col-auto">
    <div class="card">
       You don't have any surveys.
    </div>
</div>
<?php endif;?>
    </div>
    </div>
        <nav aria-label="Your Surveys">
            <ul class="pagination justify-content-center">
                <?php if ($page != 1): ?>
                <li class="page-item <?php echo ($page-1) < 1?"disabled":"";?>">
                    <a class="page-link" href="?page=<?php echo $page-1;?>" tabindex="-1">Previous</a>
                </li>
                <?php else: ?>
                <?php endif; ?>
                <?php for($i = 0; $i < $total_pages; $i++):?>
                <li class="page-item <?php echo ($page-1) == $i?"active":"";?>"><a class="page-link" href="?page=<?php echo ($i+1);?>"><?php echo ($i+1);?></a></li>
                <?php endfor; ?>
		<?php if ($page != $total_pages): ?>
                <li class="page-item <?php echo ($page) >= $total_pages?"disabled":"";?>">
                    <a class="page-link" href="?page=<?php echo $page+1;?>">Next</a>
                </li>
		<?php else: ?>
		<?php endif; ?>
            </ul>
        </nav>
    </div>
<?php require(__DIR__ . "/partials/flash.php");
