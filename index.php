<?php
session_start();

if (isset($_GET['lang'])) {
    $allowedLangs = ['fr', 'en', 'vi', 'al'];
    if (in_array($_GET['lang'], $allowedLangs)) {
        $_SESSION['lang'] = $_GET['lang'];
    }
    $page = $_GET['page'] ?? 'landing';
    header("Location: index.php?page=" . $page);
    exit();
}

error_reporting(E_ALL & ~E_DEPRECATED);
ini_set("display_errors", 1);

define("CHARGE_AUTOLOAD", true);
require_once("inc/poo.inc.php"); 

$errorMessageLogin = "";
$errorMessageSignup = "";

$page = isset($_GET["page"]) ? $_GET["page"] : "landing";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = new Database(); 

    if ($page === 'login' && isset($_POST['email']) && isset($_POST['password'])) {
        if ($db->authenticateUser(trim($_POST['email']), $_POST['password'])) {
            header("Location: index.php?page=dashboard");
            exit();
        } else {
            $errorMessageLogin = "Adresse email ou mot de passe incorrect.";
        }
    }

    if ($page === 'signup' && isset($_POST['email']) && isset($_POST['password']) && isset($_POST['birth_date'])) {
        $password = $_POST['password'];
        $passwordConfirm = $_POST['password_confirm'] ?? '';
        $acceptCgu = isset($_POST['accept_cgu']) && $_POST['accept_cgu'] === '1';
        $birthDate = $_POST['birth_date'];

        $birthDateTimestamp = strtotime($birthDate);
        $minimumBirthDate = strtotime('-14 years');

        if (!$acceptCgu) {
            $errorMessageSignup = "Vous devez accepter les conditions generales d'utilisation.";
        } elseif ($birthDateTimestamp === false || $birthDateTimestamp > $minimumBirthDate) {
            $errorMessageSignup = "Vous devez avoir au moins 14 ans pour vous inscrire.";
        } elseif ($password !== $passwordConfirm) {
            $errorMessageSignup = "La confirmation du mot de passe ne correspond pas.";
        } elseif (
            strlen($password) < 8 ||
            !preg_match('/[A-Z]/', $password) ||
            !preg_match('/[a-z]/', $password) ||
            !preg_match('/\d/', $password)
        ) {
            $errorMessageSignup = "Le mot de passe doit contenir au minimum 8 caracteres, 1 majuscule, 1 minuscule et 1 chiffre.";
        } else {
            $resultatInscription = $db->registerUser(trim($_POST['email']), $password, $birthDate);

            if ($resultatInscription === true) {
                $db->authenticateUser(trim($_POST['email']), $password);
                $_SESSION['needs_onboarding'] = 1;
                header("Location: index.php?page=dashboard");
                exit();
            } else {
                $errorMessageSignup = $resultatInscription;
            }
        }
    }
}

switch($page) {
    case "login": $view = new ViewLogin($errorMessageLogin); echo $view; break;
    case "signup": $view = new ViewSignUp($errorMessageSignup); echo $view; break;
    case "legal": $view = new ViewLegal(); echo $view; break;
    case "privacy": $view = new ViewPrivacy(); echo $view; break;
    case "faq": $view = new ViewFAQ(); echo $view; break;
    case "about": $view = new ViewAbout(); echo $view; break;
    case "contact": $view = new ViewContact(); echo $view; break;
    case "gcu": $view = new ViewCGU(); echo $view; break;

    case "dashboard":
        if (!isset($_SESSION['id_user'])) { header("Location: index.php?page=login"); exit(); }
        $db = new Database();

        $onboardingError = '';
        $needsOnboarding = isset($_SESSION['needs_onboarding']) && $_SESSION['needs_onboarding'] == 1;

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'complete_onboarding') {
            $username = strtolower(trim($_POST['username'] ?? ''));
            $displayName = trim($_POST['display_name'] ?? '');
            $origins = isset($_POST['origins']) && is_array($_POST['origins']) ? $_POST['origins'] : [];

            if (!preg_match('/^[a-z0-9._]{3,30}$/', $username)) {
                $onboardingError = "Le nom d'utilisateur doit contenir 3 a 30 caracteres (lettres, chiffres, point, underscore).";
            } elseif (count($origins) < 1 || count($origins) > 4) {
                $onboardingError = "Veuillez selectionner entre 1 et 4 origines.";
            } else {
                if ($displayName === '') {
                    $displayName = $username;
                }

                $result = $db->completeOnboarding($_SESSION['id_user'], $username, $displayName, $origins);
                if ($result === true) {
                    unset($_SESSION['needs_onboarding']);
                    header("Location: index.php?page=dashboard");
                    exit();
                }
                $onboardingError = $result;
            }

            $needsOnboarding = true;
        }

        $userData = $db->getUser($_SESSION['id_user']);
        $recommendations = $db->getRecommendations($_SESSION['id_user']);
        $view = new ViewDashboard($userData, $recommendations, $needsOnboarding, $onboardingError);
        echo $view;
        break;

    case "explorer":
        if (!isset($_SESSION['id_user'])) { header("Location: index.php?page=login"); exit(); }
        $db = new Database();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'new_post') {
            $content = $_POST['content'] ?? '';
            $fileInfo = (isset($_FILES['post_image']) && $_FILES['post_image']['error'] === UPLOAD_ERR_OK) ? $_FILES['post_image'] : null;
            if ($content !== '' || $fileInfo) {
                $db->createPost($_SESSION['id_user'], $content, $fileInfo);
            }
            header("Location: index.php?page=explorer");
            exit();
        }

        $userData = $db->getUser($_SESSION['id_user']);
        $recommendations = $db->getRecommendations($_SESSION['id_user']);
        $posts = $db->getFeedPosts('all', $_SESSION['id_user']); 
        $view = new ViewExplorer($userData, $recommendations, $posts);
        echo $view;
        break;

    case "events":
        if (!isset($_SESSION['id_user'])) { header("Location: index.php?page=login"); exit(); }
        $db = new Database();
        $myId = $_SESSION['id_user'];
        $userData = $db->getUser($myId);
        $todayPublicEvents = $db->getTodayPublicEvents();
        $myOrganizedEvents = $db->getMyOrganizedEvents($myId);
        $myConnections = $db->getConnections($myId);
        $pendingEventInvites = $db->getPendingEventInvitations($myId);
        
        $view = new ViewEvents($userData, $todayPublicEvents, $myOrganizedEvents, $myConnections, $pendingEventInvites);
        echo $view;
        break;

    case "profile":
        if (!isset($_SESSION['id_user'])) { header("Location: index.php?page=login"); exit(); }
        $db = new Database();
        $myId = $_SESSION['id_user'];
        
        if (isset($_GET['id']) && $_GET['id'] != $myId) {
            $targetId = intval($_GET['id']);
            $targetUser = $db->getUser($targetId);
            
            if (!$targetUser) { header("Location: index.php?page=dashboard"); exit(); }
            
            $currentUser = $db->getUser($myId);
            $connectionStatus = $db->getConnectionStatus($myId, $targetId);
            $userPosts = $db->getUserPosts($targetId, $myId);
            
            $view = new ViewUserProfile($currentUser, $targetUser, $connectionStatus, $userPosts);
            echo $view;
            break;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
            $origins = isset($_POST['origins']) && is_array($_POST['origins']) ? $_POST['origins'] : [];

            $parseCrop = function($prefix) {
                $readyKey = $prefix . '_crop_ready';
                $keys = ['x', 'y', 'w', 'h'];

                if (!isset($_POST[$readyKey]) || $_POST[$readyKey] !== '1') {
                    return null;
                }

                $crop = [];
                foreach ($keys as $k) {
                    $field = $prefix . '_crop_' . $k;
                    if (!isset($_POST[$field]) || !is_numeric($_POST[$field])) {
                        return null;
                    }
                    $crop[$k] = (float) $_POST[$field];
                }

                return $crop;
            };

            $avatarCrop = $parseCrop('avatar');
            $bannerCrop = $parseCrop('banner');
            
            $db->updateProfile(
                $myId, 
                $_POST['display_name'], 
                $_POST['bio_free'],
                $origins,
                $_POST['contact_email'] ?? '',
                $_POST['website'] ?? '',
                $_POST['social_link'] ?? '',
                $_POST['skills'] ?? '',
                $_POST['interests'] ?? ''
            );
            
            if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
                $db->updateAvatar($myId, $_FILES['avatar'], $avatarCrop);
            }
            if (isset($_FILES['banner']) && $_FILES['banner']['error'] === UPLOAD_ERR_OK) {
                $db->updateBanner($myId, $_FILES['banner'], $bannerCrop);
            }
            
            header("Location: index.php?page=profile");
            exit();
        }
        $userData = $db->getUser($myId);
        $recommendations = $db->getRecommendations($myId);
        $userPosts = $db->getUserPosts($myId, $myId);
        $view = new ViewProfile($userData, $recommendations, $userPosts);
        echo $view;
        break;

    case "network":
        if (!isset($_SESSION['id_user'])) { header("Location: index.php?page=login"); exit(); }
        $db = new Database();
        $userData = $db->getUser($_SESSION['id_user']);
        $view = new ViewNetwork($userData);
        echo $view;
        break;

    case "messages":
        if (!isset($_SESSION['id_user'])) { header("Location: index.php?page=login"); exit(); }
        $db = new Database();
        $userData = $db->getUser($_SESSION['id_user']);
        $view = new ViewMessages($userData);
        echo $view;
        break;

    case "logout":
        session_destroy();
        header("Location: index.php");
        exit();
        break;

    // --- CORRECTION : Traduction des codes pays dans les posts ---
    case "api_posts":
        ob_clean(); header('Content-Type: application/json');
        $db = new Database();
        $country = isset($_GET['country']) ? $_GET['country'] : 'all';
        
        $posts = $db->getFeedPosts($country, $_SESSION['id_user']);
        
        $countriesJson = @file_get_contents('data/countries.json');
        $countries = $countriesJson ? json_decode($countriesJson, true) : [];
        
        foreach ($posts as &$post) {
            if (!empty($post['origins'])) {
                $codes = explode(',', $post['origins']);
                $names = array_map(function($c) use ($countries) {
                    $c = strtoupper(trim((string)$c));
                    return $countries[$c] ?? $c;
                }, $codes);

                $locationParts = [];
                foreach ($codes as $codeRaw) {
                    $code = strtoupper(trim((string)$codeRaw));
                    $name = htmlspecialchars($countries[$code] ?? $code, ENT_QUOTES, 'UTF-8');
                    if (preg_match('/^[A-Z]{2}$/', $code)) {
                        $flagUrl = 'https://flagcdn.com/20x15/' . strtolower($code) . '.png';
                        $locationParts[] = '<span class="origin-flag-item"><img class="country-flag-img" src="' . $flagUrl . '" alt="' . htmlspecialchars($code, ENT_QUOTES, 'UTF-8') . '" loading="lazy" decoding="async" referrerpolicy="no-referrer"> ' . $name . '</span>';
                    } else {
                        $locationParts[] = '<span class="origin-flag-item"><span class="origin-flag-fallback">🌍</span> ' . $name . '</span>';
                    }
                }

                $post['location'] = implode(', ', $names);
                $post['location_html'] = implode(', ', $locationParts);
            } else {
                $post['location'] = 'Origine inconnue';
                $post['location_html'] = 'Origine inconnue';
            }
        }
        
        echo json_encode($posts);
        exit();
        break;

    case "api_translate":
        ob_clean(); header('Content-Type: application/json');
        if (isset($_POST['text']) && isset($_POST['target_lang'])) {
            $text = $_POST['text'];
            $targetLang = $_POST['target_lang'];
            $url = 'https://translate.googleapis.com/translate_a/single?client=gtx&sl=auto&tl=' . urlencode($targetLang) . '&dt=t&q=' . urlencode($text);
            $response = @file_get_contents($url);
            if ($response) {
                $data = json_decode($response, true);
                if (isset($data[0])) {
                    $translatedText = '';
                    foreach ($data[0] as $segment) {
                        $translatedText .= $segment[0];
                    }
                    echo json_encode(['success' => true, 'translatedText' => $translatedText]);
                } else {
                    echo json_encode(['success' => false, 'error' => 'Invalid response format']);
                }
            } else {
                echo json_encode(['success' => false, 'error' => 'API call failed']);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Missing parameters']);
        }
        exit();
        break;

    case "api_like_post":
        ob_clean(); header('Content-Type: application/json');
        if(isset($_SESSION['id_user']) && isset($_POST['id_post'])) {
            $db = new Database();
            echo json_encode($db->toggleLike($_POST['id_post'], $_SESSION['id_user']));
        } else { echo json_encode(['status' => 'error']); }
        exit();
        break;

    case "api_send_request":
        ob_clean(); header('Content-Type: application/json');
        if(isset($_SESSION['id_user']) && isset($_POST['id_followed'])) {
            $db = new Database();
            $success = $db->sendConnectionRequest($_SESSION['id_user'], $_POST['id_followed']);
            echo json_encode(['success' => $success]);
        } else { echo json_encode(['success' => false]); }
        exit();
        break;

    case "api_accept":
        ob_clean(); header('Content-Type: application/json');
        if(isset($_SESSION['id_user']) && isset($_POST['id_follower'])) {
            $db = new Database();
            $success = $db->acceptConnection($_POST['id_follower'], $_SESSION['id_user']);
            echo json_encode(['success' => $success]);
        } else { echo json_encode(['success' => false]); }
        exit();
        break;

    case "api_reject":
        ob_clean(); header('Content-Type: application/json');
        if(isset($_SESSION['id_user']) && isset($_POST['id_follower'])) {
            $db = new Database();
            $success = $db->rejectConnection($_POST['id_follower'], $_SESSION['id_user']);
            echo json_encode(['success' => $success]);
        } else { echo json_encode(['success' => false]); }
        exit();
        break;

    case "api_remove_connection":
        ob_clean(); header('Content-Type: application/json');
        if(isset($_SESSION['id_user']) && isset($_POST['id_contact'])) {
            $db = new Database();
            $success = $db->deleteConnection($_SESSION['id_user'], $_POST['id_contact']);
            echo json_encode(['success' => $success]);
        } else { echo json_encode(['success' => false]); }
        exit();
        break;

    case "api_get_comments":
        ob_clean(); header('Content-Type: application/json');
        if(isset($_GET['id_post'])) {
            $db = new Database();
            echo json_encode($db->getComments($_GET['id_post']));
        } else { echo json_encode([]); }
        exit();
        break;

    case "api_add_comment":
        ob_clean(); header('Content-Type: application/json');
        if(isset($_SESSION['id_user']) && isset($_POST['id_post']) && isset($_POST['content'])) {
            $db = new Database();
            $success = $db->addComment($_POST['id_post'], $_SESSION['id_user'], $_POST['content']);
            echo json_encode(['success' => $success]);
        } else { echo json_encode(['success' => false]); }
        exit();
        break;

    case "api_message":
        ob_clean(); header('Content-Type: application/json');
        if(isset($_SESSION['id_user']) && isset($_POST['id_receiver'])) {
            $db = new Database();
            $content = isset($_POST['content']) ? $_POST['content'] : '';
            $fileInfo = (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) ? $_FILES['image'] : null;
            if ($content === '' && !$fileInfo) { echo json_encode(['success' => false]); exit(); }
            $success = $db->sendMessage($_SESSION['id_user'], $_POST['id_receiver'], $content, $fileInfo);
            echo json_encode(['success' => $success]);
        } else { echo json_encode(['success' => false]); }
        exit();
        break;

    case "api_get_chat":
        ob_clean(); header('Content-Type: application/json');
        if(isset($_SESSION['id_user']) && isset($_GET['contact'])) {
            $db = new Database();
            echo json_encode($db->getChatHistory($_SESSION['id_user'], $_GET['contact']));
        } else { echo json_encode([]); }
        exit();
        break;

    // --- CORRECTION : Traduction des codes pays dans les recherches ---
    case "api_search":
        ob_clean(); header('Content-Type: application/json');
        if(isset($_SESSION['id_user']) && isset($_GET['q'])) {
            $db = new Database();
            $results = $db->searchUsers($_GET['q'], $_SESSION['id_user']);
            
            $countriesJson = @file_get_contents('data/countries.json');
            $countries = $countriesJson ? json_decode($countriesJson, true) : [];
            
            foreach ($results as &$user) {
                if (!empty($user['origins'])) {
                    $codes = explode(',', $user['origins']);
                    $names = array_map(function($c) use ($countries) { return $countries[$c] ?? $c; }, $codes);
                    $user['location'] = implode(', ', $names);
                } else {
                    $user['location'] = 'Origine inconnue';
                }
            }
            
            echo json_encode($results);
        } else { echo json_encode([]); }
        exit();
        break;

    case "api_create_event":
        ob_clean(); header('Content-Type: application/json');
        if(isset($_SESSION['id_user']) && isset($_POST['title']) && isset($_POST['event_date'])) {
            $db = new Database();
            $fileInfo = (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) ? $_FILES['cover_image'] : null;
            $success = $db->createEvent($_SESSION['id_user'], $_POST['title'], $_POST['event_date'], $_POST['start_time'] ?? null, $_POST['end_time'] ?? null, $_POST['description'] ?? '', $_POST['visibility'] ?? 'shared', $_POST['meeting_url'] ?? null, $fileInfo);
            echo json_encode(['success' => $success]);
        } else { echo json_encode(['success' => false]); }
        exit();
        break;

    case "api_invite_event":
        ob_clean(); header('Content-Type: application/json');
        if(isset($_SESSION['id_user']) && isset($_POST['id_event']) && isset($_POST['id_guest'])) {
            $db = new Database();
            $success = $db->inviteUserToEvent($_POST['id_event'], $_POST['id_guest']);
            echo json_encode(['success' => $success]);
        } else { echo json_encode(['success' => false]); }
        exit();
        break;

    case "api_respond_event_invite":
        ob_clean(); header('Content-Type: application/json');
        if(isset($_SESSION['id_user']) && isset($_POST['id_event']) && isset($_POST['status'])) {
            $db = new Database();
            $success = $db->respondToEventInvite($_POST['id_event'], $_SESSION['id_user'], $_POST['status']);
            echo json_encode(['success' => $success]);
        } else { echo json_encode(['success' => false]); }
        exit();
        break;

    case "api_events":
        ob_clean(); header('Content-Type: application/json');
        if(isset($_SESSION['id_user'])) {
            $db = new Database();
            echo json_encode($db->getEventsForUser($_SESSION['id_user']));
        } else { echo json_encode([]); }
        exit();
        break;

    case "api_notifications":
        ob_clean(); header('Content-Type: application/json');
        if(isset($_SESSION['id_user'])) {
            $db = new Database();
            $myId = $_SESSION['id_user'];
            
            $connReqs = $db->getPendingRequests($myId);
            $eventInvites = $db->getPendingEventInvitations($myId);
            
            $notifications = [];
            foreach($connReqs as $req) {
                $notifications[] = ['message' => htmlspecialchars($req['display_name']) . ' souhaite se connecter.', 'avatar' => $req['avatar_url'] ? htmlspecialchars($req['avatar_url']) : null, 'initial' => strtoupper(substr($req['display_name'], 0, 1)), 'link' => 'index.php?page=network'];
            }
            foreach($eventInvites as $inv) {
                $notifications[] = ['message' => htmlspecialchars($inv['organizer_name']) . ' vous a invité à ' . htmlspecialchars($inv['title']) . '.', 'avatar' => $inv['avatar_url'] ? htmlspecialchars($inv['avatar_url']) : null, 'initial' => strtoupper(substr($inv['organizer_name'], 0, 1)), 'link' => 'index.php?page=events'];
            }
            echo json_encode($notifications);
        } else { echo json_encode([]); }
        exit();
        break;

    default:
        $view = new ViewLandingPage();
        echo $view;
        break;
}
?>