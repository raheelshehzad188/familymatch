<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-API-KEY');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Check API key
$api_key = $_SERVER['HTTP_X_API_KEY'] ?? '';
$valid_api_key = '123456'; // Replace with your actual API key

if ($api_key !== $valid_api_key) {
    http_response_code(401);
    echo json_encode(['error' => 'Invalid API key']);
    exit();
}

// Allow both /questions and /questions.php
$uri = $_SERVER['REQUEST_URI'];
$uri = strtok($uri, '?'); // Remove query string
$uri = rtrim($uri, '/');
$basename = basename($uri);

if (
    $_SERVER['REQUEST_METHOD'] === 'GET' &&
    (
        $basename === 'questions' || $basename === 'questions.php'
    )
) {
    $questions = [
        ["id" => 1, "apiname" => "dob", "question" => "When's your Birth date?", "input" => "date", "validationType" => "birthday", "description" => "your text would be here"],
        ["id" => 2, "apiname" => "full_name", "question" => "What's your name as the main contact for the family?", "input" => "text", "validationType" => "name", "description" => "your text would be here"],
        ["id" => 3, "apiname" => "email", "question" => "What's the best email to reach you?", "input" => "email", "validationType" => "email1", "description" => "your text would be here"],
        ["id" => 4, "apiname" => "password", "question" => "What password would you like for your account?", "input" => "password", "validationType" => "password", "description" => "Select strong password"],
        ["id" => 5, "apiname" => "family_nickname", "question" => "What's a special name or nickname for your family?", "input" => "text", "description" => "Something that represents your family vibe."],
        ["id" => 6, "apiname" => "home_location", "question" => "Where does your family call home?", "input" => "dropdown-text", "description" => "Type or select your location"],
    ];
    
    $interest_reason = array();
    $interest_reason[] = array('id'=>'1','name'=>'New to the area?');
    $interest_reason[] = array('id'=>'2','name'=>'Expand family options?');
    $interest_reason[] = array('id'=>'3','name'=>'Change in family situation (e.g. divorce, remarriage, new blended family)?');
    $interest_reason[] = array('id'=>'4','name'=>'Change in family interests?');
    $interest_reason[] = array('id'=>'5','name'=>'Other (specify)');
    
    $questions[] = ["id" => 7, "apiname" => "interest_reason", "question" => "Why are you interested in Family Match?", "input" => "radio", "options" => $interest_reason];
    $questions[] = ["id" => 8, "apiname" => "family_size", "question" => "How many family members make up your family?", "input" => "radio", "options" => ["1", "2", "3", "4", "5+"], "description" => "Include yourself too"];
    $questions[] = ["id" => 9, "apiname" => "life_stage", "question" => "Which of the following best describes your family's current life stage?", "input" => "radio", "options" => ["Expecting a child","Raising young kids","Parenting teens","Empty nesters","Multigenerational","Newly married"]];
    $questions[] = ["id" => 10, "apiname" => "family_status", "question" => "What's your family status?", "input" => "radio", "options" => ["Married","Single parent","Blended family","Multigenerational","Other"]];
    $questions[] = ["id" => 11, "apiname" => "family_values", "question" => "What values are at the heart of your family?", "input" => "checkbox", "options" => ["Kindness","Honesty","Faith","Adventure","Respect","Creativity","Others"]];
    $questions[] = ["id" => 12, "apiname" => "ethnicity", "question" => "What is your family's ethnicity?", "input" => "checkbox", "options" => ["Asian","Black / African descent","Hispanic / Latino","Middle Eastern","White / Caucasian","Mixed","Prefer not to say","Other"]];
    $questions[] = ["id" => 13, "apiname" => "languages_spoken", "question" => "What languages do you speak at home or with friends?", "input" => "checkbox", "options" => ["English","Urdu","Arabic","Punjabi","Spanish","French","Others"]];
    $questions[] = ["id" => 14, "apiname" => "family_activities", "question" => "What activities does your family love doing together?", "input" => "checkbox", "options" => ["Cooking","Sports","Board games","Traveling","Watching movies","Outdoor adventures","Other"]];
    $questions[] = ["id" => 15, "apiname" => "activities_with_others", "question" => "What activities would your family enjoy doing with other families?", "input" => "checkbox", "options" => ["Picnics","Cultural events","Playdates","Volunteering","Game nights","Group travel","Other"]];
    $questions[] = ["id" => 16, "apiname" => "family_tradition", "question" => "What's a favorite family tradition or celebration you cherish?", "input" => "text"];
    $questions[] = ["id" => 17, "apiname" => "family_story", "question" => "What's a little something about your family's story or traditions?", "input" => "textarea"];
    $questions[] = ["id" => 18, "apiname" => "family_photo", "question" => "Would you like to share a photo or fun avatar?", "input" => "image"];

    echo json_encode([
        'success' => true,
        'data' => $questions,
        'message' => 'Questions fetched successfully'
    ]);
    exit();
}

// If endpoint not found
http_response_code(404);
echo json_encode(['error' => 'Endpoint not found']);
?> 