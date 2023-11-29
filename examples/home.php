<html>
    <form method = "GET" action = "">
        <label for = "registration">Register</label>
        <br>
        <input type = "text" name = "nameR" placeholder="Enter name"></input>
        <br>
        <input type = "text" name = "username" placeholder="Enter username"></input>
        <br>
        <input type = "text" name = "email" placeholder="Enter email"></input>
        <br>
        <!-- <input type = "text" name = "address" placeholder="Enter address"></input>
        <br> -->
        <input type = "text" name = "street" placeholder="Enter street"></input>
        <br>
        <input type = "text" name = "barangay" placeholder="Enter barangay"></input>
        <br>
        <input type = "text" name = "city" placeholder="Enter city"></input>
        <br>
        <input type = "submit" value = "Register"></input>
    </form>
            
    <form method = "GET" action = "">
        <label for = "Log-In">Log-in</label>
        <br>
        <input type = "text" name = "nameL"></input>
        <br>
        <input type = "submit" value = "Log-In"></input>
    </form>

    <form method = "POST" action = "">
        <label for = "Create Post">Create Post</label>
        <input type = "text" name = "title" placeholder="Title"></input>
        <input type = "text" name = "body" placeholder="Body"></input>
        <input type = "submit" value = "Post"></input>
    </form>

    <?php
    include_once 'api.php';
    if($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['nameR'])){
        global $name;
        global $email;

        $users = json_decode(file_get_contents($usersJSON));
        $id = count($users);
        $name = $_GET['nameR'];
        $username = $_GET['username'];
        $email = $_GET['email'];
        //$address = $_GET['address'];
        $street = $_GET['street'];
        $barangay = $_GET['barangay'];
        $city = $_GET['city'];
        registerUser($id, $name, $username, $email, $street, $barangay, $city);
    }

    if($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['nameL'])){
        $username = $_GET['nameL'];
        loginUser($username);
    }

    if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['title']) && isset($_POST['body']) ){
        $title = $_POST['title'];
        $body = $_POST['body'];
        createPost($title, $body);
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['comment'])) {
        $referenceID = $_POST['post_id'];
        $commentBody = $_POST['comment'];

        if(!$referenceID || !$commentBody){
            echo "WHATTTT";
            return;
        }
        createComment($referenceID, $commentBody);
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete'])) {
        $referencePostID = $_POST['postIDDelete'];

        if(!$referencePostID){
            echo "An error occured, please try again.";
            return;
        }
        deletePost($referencePostID);
    }

?>
</html>