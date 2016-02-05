<!
Index.php for TwitBoard. This is the app controller/router for the
message posting app.

Author: Landon Owens

>
<head>
<link rel="stylesheet" href="css/twit.css">
<link rel="stylesheet" href="bootstrap/css/bootstrap.css">
<link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" href="bootstrap/css/bootstrap-responsive.css">
<link rel="stylesheet" href="bootstrap/css/bootstrap-responsive.min.css">
<link rel="javascript" href="bootstrap/js/bootstrap.js">
<link rel="javascript" href="bootstrap/js/bootstrap.min.js">
</head>
<header>
<Title>
Twit Board, where nobody will listen.
</Title>
Twit Board
</header>
<?PHP
//phpinfo();

require "vendor/autoload.php";
 
// front controller
$app = Twit\App::bootstrap();


//get login page
$app->get('/', function () use ($app) {
	
 return $app['twig']->render('login.html',array('time' => time()));	

 });

/*post login to the database for the user. 
There are no security checks, the user just enters the name to post with
*/
$app->post('/Login', function () use ($app) {
	
	//grab the user name, change to anonymous if left blank
   $app['userName'] = $app['request']->get('LoginName');
   
	if( $app['userName'] ==''  or $app['userName']==null){
		$app['userName'] ='anonymous';}
		
		
	//Insert the user with an ID and timestamp to the databae	
	$app['db']->executeUpdate($app['sqlInsertUser'], array( $app['userName']));
	
	//Grab the last userID that is generated for that UserName on the server
	$posterID = $app['db']->fetchAssoc($app['sqlGetMaxID'], array( $app['userName']));
	$app['posterID'] = $posterID['uid'];	
		
	
	$posts = $app['db']->fetchAll($app['sqlListMessageBoard']);
	
	return $app['twig']->render('list.html',array('posts' => $posts,'userID'=>$app['posterID'], 'username'=> $app['userName']));	}
	
);

//Post users message to the database
$app->post('/', function () use($app) {
	
	//grab message content, username, and userID from the page
	$content= $app['request']->get('messageContent');
	$app['posterID'] = $app['request']->get('userID');
	$app['userName'] = $app['request']->get('username');
	
	//Make sure you have a userID to enter in as posterID on the databae, in case for some reason its not there
	if($app['posterID']==null){
		
		$posterID = $app['db']->fetchAssoc($app['sqlGetMaxID'], array( $app['userName']));
		$app['posterID'] = $posterID['uid'];
	}
	
	//insert the message to the database then list messages back out to the message board
	$app['db']->executeUpdate($app['sqlInsertMessage'], array($app['posterID'],$content ));
	
	$posts = $app['db']->fetchAll($app['sqlListMessageBoard']);
	
	return $app['twig']->render('list.html',array('posts' => $posts,'userID'=>$app['posterID'], 'username'=> $app['userName']));	}
	

);

//set up database for mySQL. Need to create a user on the server named 'localUser' with no password and create database rights. 
$app->get('/setup', function() use($app){
	
	$app['dbs']['mysql_createDB']->executeUpdate($app['sqlCreateDB']);
	$app['db']->executeUpdate($app['sqlCreateTableMessageBoard']);
	$app['db']->executeUpdate($app['sqlCreateTableUsers']);
	
	return $app->redirect('/TwitBoard');
	
}
);




$app->run();

?>