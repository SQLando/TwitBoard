<?php namespace Twit;
 
/**
 * Silex App 
 * 
 * @copyright 2013 Lukasz Grzegorz Maciak
 * @author Lukasz Grzegorz Maciak (maciak.net) 
 */
class App 
{
    /**
     * Bootstrap the Silex application by registering all providers and
     * services and returning a pre-configured Silex Application object.
     * 
     * @static
     * @access public
     * @return \Silex\Application A Silxex app object
     */
    public static function bootstrap()
    {
 
        // front controller
        $app = new \Silex\Application();
		//$app['debug'] = true;
		
		//declare app globals
		$app['userName'] = null;
		$app['posterID'] = null;
		$app['sqlInsertMessage'] = "INSERT INTO messageboard (posterID, content) VALUES (?, ?)";
		$app['sqlInsertUser'] =  "insert into users (userName) values(?)";
		$app['sqlListMessageBoard'] = "Select mbID, userName, Content, postTS from messageboard mb
										join users u on mb.posterID = u.UserID order by mbID desc";
		
		$app['sqlGetMaxID'] = "Select Max(userID) as uid from users where userName = ?";
		$app['sqlCreateDB'] = "CREATE DATABASE IF NOT EXISTS sqlando;";
		
		$app['sqlCreateTableMessageBoard'] = "use sqlando;
			CREATE TABLE IF NOT EXISTS `messageboard` (
			`mbID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			`posterID` int(11) NOT NULL,
			`Content` varchar(140) NOT NULL,
			`postTS` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY (`mbID`),
			UNIQUE KEY `mbID` (`mbID`)
				) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
		
		$app['sqlCreateTableUsers'] = "use sqlando;
			CREATE TABLE IF NOT EXISTS `users` (
			`userID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			`userName` varchar(250) NOT NULL,
			`loginTS` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY (`userID`),
			UNIQUE KEY `userID` (`userID`)
			) ENGINE=InnoDB DEFAULT CHARSET=latin1;";



		
		// set up the db provider
		$app->register(new \Silex\Provider\DoctrineServiceProvider(), array(
			'dbs.options' => array(
			'mysql_dts' =>array(
			'dbhost' => 'localhost',
			'dbname' => 'sqlando',
			'user' => 'localUser'),
			'mysql_createDB' =>array('dbhost' => 'localhost',
			//'dbname' => 'sqlando',
			'user' => 'localUser'),)
    )
);
 
        // ## Register Official Silex Providers ##
 
        // Twig provider for templating
        // twig.path is /views and autoescape ought to be on
        $app->register(new \Silex\Provider\TwigServiceProvider(), array(
            'twig.path' => 'views',
            'twig.autoescape' => true
        ));
 
        // session provider to handle user sessions
        $app->register(new \Silex\Provider\SessionServiceProvider());
 
       
 
        
        return $app;
 
    }
}

?>