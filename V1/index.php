<?php

/**
 * THIS IS A MOVIES API BUILD FOR DUMBASS!!
 *
 * THIS WILL EXPLAIN HOW THIS API WORKS AS HELL
 * @author LUCIFER
 * @version 1.0
 * @package API MOVIIIEEZZZ
 *
 * IF U WANT MORE DETAILS , MOVIES\V1\MOVIES\V1\output\index.html
 */


require('/token.php');
require("vendor/autoload.php");


// DATABASE
$servername = 'localhost';
$username = 'root';
$password = "";
$database = "movies_imie";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    echo ('connection establish');
}




/**
 * @param $router is the new router created and i think this annotation wont work!
 *
 *
 */

// ROUTER
$router = new AltoRouter();
$router->setBasePath('/MOVIES/V1/');


// FUNCTIONS

  
/**
 *
 * This is a function to get actors's movie
 *
 * @param    integer  $id the id u must supply
 * @param    object $conn  the conn object u must supply
 * @return   array $actors the array of actors associated to the film supplied
 *
 */

function getMovieActors($id, $conn)
{
    $actors = [];
    $sql = "SELECT name FROM movie as m INNER JOIN movie_actor as ma ON m.id = ma.movieId
    INNER JOIN people as p ON p.id = ma.peopleId WHERE m.id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $results = $stmt->get_result();
    foreach ($results as $result) {
        array_push($actors, $result);
    }
    return $actors;
}

/**
 *
 * This is a function to get directors's movie
 *
 * @param    integer  $id the id u must supply
 * @param    object $conn  the conn object u must supply
 * @return   array $directors the array of directors associated to the film supplied
 *
 */

function getMovieDirectors($id, $conn)
{
    $directors = [];
    $sql = "SELECT name FROM movie as m INNER JOIN movie_director as md ON m.id = md.movieId
    INNER JOIN people as p ON p.id = md.peopleId WHERE m.id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $results = $stmt->get_result();
    foreach ($results as $result) {
        array_push($directors, $result);
    }
    return $directors;
}

/**
 *
 * This is a function to get writers's movie
 *
 * @param    integer  $id the id u must supply
 * @param    object $conn  the conn object u must supply
 * @return   array $writers the array of writers associated to the film supplied
 *
 */

function getMovieWriters($id, $conn)
{
    $writers = [];
    $sql = "SELECT name FROM movie as m INNER JOIN movie_writer as mw ON m.id = mw.movieId
    INNER JOIN people as p ON p.id = mw.peopleId WHERE m.id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $results = $stmt->get_result();
    foreach ($results as $result) {
        array_push($writers, $result);
    }
    return $writers;
}

/**
 *
 * This is a function to get genres's movie
 *
 * @param    integer  $id the id u must supply
 * @param    object $conn  the conn object u must supply
 * @return   array $genres the array of genres associated to the film supplied
 *
 */

function getMovieGenres($id, $conn)
{
    $genres = [];
    $sql = "SELECT g.name FROM movie as m 
    INNER JOIN movie_genre as mg ON m.id = mg.movieId
    INNER JOIN genre as g ON g.id = mg.genreId WHERE m.id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $results = $stmt->get_result();
    foreach ($results as $result) {
        array_push($genres, $result);
    }
            return $genres;
}

/**
 *
 * This is a function to get reviews's movie
 *
 * @param    integer  $id the id u must supply
 * @param    object $conn  the conn object u must supply
 * @return   array $reviews the array of reviews associated to the film supplied
 *
 */

function getMovieReviews($id, $conn)
{
    $reviews = [];
    $sql = "SELECT r.content, r.title, r.username FROM movie as m 
    INNER JOIN review as r ON m.id = r.movieId
    WHERE m.id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $results = $stmt->get_result();
    foreach ($results as $result) {
        array_push($reviews, $result);
    }
    return $reviews;
}


// ROUTES


// HOME
$router->map('GET', '', function () {
    echo "home";
});

// MOVIES LIST
$router->map('GET', 'movies', function () use ($conn) {
    $sql = "SELECT * FROM movie";
    if ($results = $conn->query($sql)) {
        foreach ($results as $result) {
            $moviesList = json_encode($result);
            return $moviesList;
            //var_dump($movieList);
        }
    }
});

// MOVIE details
$router->map('GET', 'movies/[i:id]', function ($id) use ($conn) {
    $movieDetails =[];
    $actors = getMovieActors($id, $conn);
    $directors = getMovieDirectors($id, $conn);
    $writers = getMovieWriters($id, $conn);
    $genres = getMovieGenres($id, $conn);
    $reviews = getMovieReviews($id, $conn);
    array_push($movieDetails, $actors, $directors, $writers, $genres, $reviews);
    $movieDetails = json_encode($movieDetails);
    //var_dump($movieDetails);
    return $movieDetails;
});

// ACTORS LIST
$router->map('GET', 'actors', function () use ($conn) {
    $actorsList = [];
    $sql = "SELECT name FROM people as p
    INNER JOIN movie_actor as ma ON p.id = ma.peopleId
    GROUP BY p.name";
    if ($results = $conn->query($sql)) {
        foreach ($results as $result) {
            array_push($actorsList, $result);
        }
        $actorsList = json_encode($actorsList);
        //var_dump($actorsList);
        return $actorsList;
    }
});

// WRITERS LIST
$router->map('GET', 'writers', function () use ($conn) {
    $writersList = [];
    $sql = "SELECT name FROM people as p
    INNER JOIN movie_writer as mw ON p.id = mw.peopleId
    GROUP BY p.name";
    if ($results = $conn->query($sql)) {
        foreach ($results as $result) {
            array_push($writersList, $result);
        }
        $writersList = json_encode($writersList);
        //var_dump($writersList);
        return $writersList;
    }
});

// DIRECTORS LIST
$router->map('GET', 'directors', function () use ($conn) {
    $directorsList = [];
    $sql = "SELECT name FROM people as p
    INNER JOIN movie_director as md ON p.id = md.peopleId
    GROUP BY p.name";
    if ($results = $conn->query($sql)) {
        foreach ($results as $result) {
            array_push($directorsList, $result);
        }
        $directorsList = json_encode($directorsList);
        //var_dump($directorsList);
        return $directorsList;
    }
});

// GENRE LIST
$router->map('GET', 'genres', function () use ($conn) {
    $genresList = [];
    $sql = "SELECT name FROM genre";
    if ($results = $conn->query($sql)) {
        foreach ($results as $result) {
            array_push($genresList, $result);
        }
        $genresList = json_encode($genresList);
        //var_dump($genreList);
        return $genresList;
    }
});

/**
 * GET route to receive a list of reviews
 * return a formatted json array of reviews list
 */
// REVIEWS LIST
$router->map('GET', 'reviews', function () use ($conn) {
    $reviewsList = [];
    $sql = "SELECT * FROM review";
    if ($results = $conn->query($sql)) {
        foreach ($results as $result) {
            array_push($reviewsList, $result);
        }
        $reviewsList = json_encode($reviewsList);
        //var_dump($reviewsList);
        return $reviewsList;
    }
});

/**
 * POST route to add a new review
 * All params should be send received in form-data
 */
// POST REVIEW
$router->map('POST', 'reviews', function () use ($conn) {
    $content = $_POST['content'];
    $date = date("Y-m-d H:i:s");
    $movieId = $_POST['movieId'];
    $title = $_POST['title'];
    $username = $_POST['username'];
    $sql = "INSERT INTO review (content, dateCreated, movieId, title, username)
    VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssiss", $content, $date, $movieId, $title, $username);
    $stmt->execute();
    $results = $stmt->get_result();
    $result = json_encode($results);
    //var_dump($result);
    return ('THE ROW HAVE BEEN ADDED' . $result);
});


/**
 * PUT route to update a review
 * All params should be send in jSon
 */
// UPDATE REVIEW
$router->map('PUT', 'reviews/[i:id]', function ($id) use ($conn) {
    $handle = fopen("php://input", "rb");
    $data = stream_get_contents($handle);
    $datas = json_decode($data, true);
    $content = $datas['content'];
    $movieId = (int) $datas['movieId'];
    $title = $datas['title'];
    $username = $datas['username'];
    $newdate = new DateTime('Y-m-d H:i:s');
    $date = (string) $newdate;
    $sql = "UPDATE review
    SET content = ?, dateCreated = ?, movieId = ?, title = ?, username = ?
    WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssissi", $content, $date, $movieId, $title, $username, $id);
    $stmt->execute();
    $results = $stmt->get_result();
    $result = json_encode($results);
    return ('THE ROW' . $result . 'HAVE BEEN UPDATED');
});


// DELETE REVIEW
$router->map('DELETE', 'reviews/[i:id]', function ($id) use ($conn) {
    $sql = "DELETE FROM review
    WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $results = $stmt->get_result();
    $result = json_encode($results);
    return ('THE ROW' . $result . 'HAVE BEEN DELETED');
});






$match = $router->match();
if ($match && is_callable( $match['target'] )) {
    call_user_func_array( $match['target'], $match['params'] );
} else {
    // no route was matched
    header( $_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
    echo "aie 404. route not found.";
}
