<?php

// users JSON
//$userID;
session_start();

$usersJSON = '../data/users.json';
// posts JSON
$postsJSON = '../data/posts.json';
// comments JSON
$commentsJSON = '../data/comments.json';

$name;
$email;

// function get users from json
function getUsersData() {
    global $usersJSON;
    if (!file_exists($usersJSON)) {
        echo 1;
        return [];
    }
    $data = file_get_contents($usersJSON);
    return json_decode($data, true);
}

// function get posts from json
function getPostsData() {
    global $postsJSON;
    if (!file_exists($postsJSON)) {
        echo 1;
        return [];
    }

    $data = file_get_contents($postsJSON);
    return json_decode($data, true);
}

// function get comments from json
function getCommentsData() {
    global $commentsJSON;
    if (!file_exists($commentsJSON)) {
        echo 1;
        return [];
    }

    $data = file_get_contents($commentsJSON);
    return json_decode($data, true);
}


function getPosts(){
    
    $users = getUsersData();
    $posts = getPostsData();
    $comments = getCommentsData();
    $postsarr = array();

    $posts = array_reverse($posts);
    
    foreach($posts as $post){
        foreach($users as $user){
            if($user['id'] == $post['uid']){
                $post['uid'] = $user;
                break;
            }
        }
        $post['comments'] = array();
        foreach($comments as $comment){
            if($post['id']==$comment['postId']){
                $post['comments'][] = $comment;
            }
        }
        $postarr[] = $post;
    }
    $str = "";
    foreach($postarr as $parr){
        
    
 $str.='<!-- start of post -->
    <div class="row">
        <div class="col-md-12">
            <div class="post-content">
              <div class="post-container">
                <img src="https://ui-avatars.com/api/?rounded=true&name='.$parr['uid']['name'].'" alt="user" class="profile-photo-md pull-left">
                <div class="post-detail">
                  <div class="user-info">
                    <h5><a href="timeline.html" class="profile-link">'. $parr['uid']['name'] .'</a></h5>
                  </div>
                  <div class="reaction">
                    <!--<a class="btn text-green"><i class="fa fa-thumbs-up"></i> 13</a>
                    <a class="btn text-red"><i class="fa fa-thumbs-down"></i> 0</a>-->
                  </div>
                  <div class="line-divider"></div>
                  <div class="post-text">
                    <h3>'.$parr['title'].'</h3>
                    <p>'.$parr['body'].'</p>
                    <form method = "POST" action ="">
                    <input type = "submit" name = "delete" value = "Delete">
                    <input type="hidden" name="postIDDelete" value="' . $parr['id'] . '">
                    </form>
                  </div>
                  <div class="line-divider"></div>';
        foreach($parr['comments'] as $comm)
        $str .=  '<div class="post-comment">
        <img src="https://ui-avatars.com/api/?rounded=true&name='.$comm['name'].'" alt="" class="profile-photo-sm"><h6>'.$comm['name'].'</h6>'.'<br>'.
        '<p style = "font-size: small";>'.$comm['body'].'</p>
      </div>';

                  $str .= '<div class="comment-box">
    <form action="" method="POST" id="commentForm_' . $parr['id'] . '">
        <input type="text" name="comment" placeholder="Add a comment...">
        <input type="hidden" name="post_id" value="' . $parr['id'] . '">
        <input type="submit" value = "Comment" class="submit-button" data-post-id="' . $parr['id'] . '"></button>
    </form>
</div>';

    $str.='</div>
              </div>
            </div>
       </div>
    </div>';
    }
return $str;
}

function getUsersFromFile() {
    global $usersJSON;

    if (!file_exists($usersJSON)) {
        return [];
    }

    $userData = file_get_contents($usersJSON);
    return json_decode($userData, true);
}


function registerUser($id, $name, $username, $email, $street, $barangay, $city){
    global $usersJSON, $name, $email;
    $users = json_decode(file_get_contents($usersJSON));
    
    $newUser = [
        'id' => $id + 1,
        'name' => $name,
        'username' =>  $username,
        'email' => $email,
        'address' =>[
            'street' => $street,
            'barangay' => $barangay,
            'city' => $city,
        ]
    ];

    $users[] = $newUser;
    file_put_contents($usersJSON, json_encode($users, JSON_PRETTY_PRINT));

    echo "Successful!";
}

function loginUser($username){
    global $userID;

    $users = getUsersFromFile();
    foreach($users as $user){
        if($user['username'] == $username){
            echo"Hello ".$username;
            $_SESSION['userID'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['email'] = $user['email'];
            echo $_SESSION['userID'];
            return $userID;
        }
    }
return false;
}

function createPost($title, $body){
    global $postsJSON;
    $userID =  $_SESSION['userID'];

    if(!$userID){
        echo "ENGK";
        return;
    }
    echo $userID;

    $posts = json_decode(file_get_contents($postsJSON)); //gets all post

    $newPost = [ //create newpost
        'uid' => $userID,
        'id' => count($posts) + 1,
        'title' => $title,
        'body' => $body,
    ];

    $posts[] = $newPost; //append all post to new post
    file_put_contents($postsJSON, json_encode($posts, JSON_PRETTY_PRINT)); //write
}

function createComment($referenceID, $commentBody){
    global $commentsJSON, $name, $email;

    $comments = json_decode(file_get_contents($commentsJSON));

    $newComment = [
        'postId'=> $referenceID,
        'id' => count($comments),
        'name' => $_SESSION['name'],
        'email' => $_SESSION['email'],
        'body' => ': '.$commentBody,
    ];

    $comments[] = $newComment;
    file_put_contents($commentsJSON, json_encode($comments, JSON_PRETTY_PRINT));

}

function deletePost($referencePostID){
    global $postsJSON;
    $data = json_decode(file_get_contents($postsJSON), true);
    //unset($posts); //to prevent memory leak kuno

    $found = false;
    
    foreach($data as $key => $post){
        if($referencePostID == $post['id'] && $_SESSION['userID'] == $post['uid']){
            unset($data[$key]);
            $found = true;
            echo "Successfully deleted!";
            break;
        }
    }
    if($found){
        $data = array_values($data);
        file_put_contents($postsJSON, json_encode($data, JSON_PRETTY_PRINT));
    }else{
        echo "You cannot delete other users' posts unless you are an admin!";
    }

}
?>

<script>
    if ( window.history.replaceState ) {
        window.history.replaceState( null, null, window.location.href );
    }
</script>




