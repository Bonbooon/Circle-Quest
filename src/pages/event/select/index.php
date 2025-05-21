<?php
require_once DBCONNECT;
require_once COMPONENTS_PATH . '/ShowMessage.php';
require_once CONTROLLERS_PATH . '/DenyInvitation.php';
require_once CONTROLLERS_PATH . '/AcceptInvitation.php';
require_once CONTROLLERS_PATH . '/UpdateUserRequestAndEvents.php';

$userId = $_SESSION['user']['user_id'];
$message = '';
// Get event ID from GET request (or set it from a session or another source)
$eventId = isset($_GET['id']) ? $_GET['id'] : null;
// Get event name by ID
$stmt = $dbh->prepare("SELECT title FROM events WHERE id = ?");
$stmt->execute([$eventId]);
$eventName = $stmt->fetchColumn();

$stmt = $dbh->prepare("SELECT image_path FROM event_images WHERE event_id = ? AND image_type = 'メインビジュアル'");
$stmt->execute([$eventId]);
$eventImage = $stmt->fetchColumn();

$teamId = isset($_GET['teamId']) ? $_GET['teamId'] : null;
$senderId = isset($_GET['senderId']) ? $_GET['senderId'] : null;
if (!$eventId) {
    echo "No event ID specified.";
    exit();
}

// Get the invitation status for the user
$stmt = $dbh->prepare("
    SELECT ei.id, ei.status, et.name AS team_name
    FROM event_invitations ei
    JOIN event_teams et ON ei.team_id = et.id
    WHERE ei.invitee_id = :user_id AND ei.event_id = :event_id
");
$stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
$stmt->bindParam(':event_id', $eventId, PDO::PARAM_INT);
$stmt->execute();
$invitation = $stmt->fetch(PDO::FETCH_ASSOC);

// If no invitation, redirect to another page (e.g., event list or home)
if (!$invitation) {
    echo "You do not have an invitation for this event.";
    exit();
}

// Handle accept or deny actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $dbh->beginTransaction();
        if ($_POST['selection'] == 'accept') {
            AcceptInvitation($dbh, $teamId, $eventId, $userId, $senderId);
        } elseif ($_POST['selection'] == 'deny') {
            DenyInvitation($dbh, $teamId, $eventId, $userId, $senderId);
        } else {
            throw new Exception('no selection found');
        }
        $dbh->commit();
        UpdateUserRequestAndEvents($dbh);
        header('Location: ' . '/index.php?page=notification');
        exit(); 
    } catch (Exception $e) {
        $dbh->rollBack();
        $message = $e->getMessage();
    }
}
?>

<div class="flex flex-col items-center gap-8">
    <?= ShowMessage($message) ?>
    <h1>イベントの招待</h1>
    
    <?php if ($invitation['status'] === 'pending'): ?>
        <h2><?php echo htmlspecialchars($eventName); ?></h2>
        <?php if ($eventImage) { ?>
            <img src="<?= $eventImage ?>" alt="<?= $eventName ?>" class="w-1/2">
        <?php } ?>
        <h3>あなたはチームに招待されました: <?php echo htmlspecialchars($invitation['team_name']); ?></h3>
    
        <form method="POST" class="flex gap-4 pb-5">
            <button type="submit" class="border p-2 rounded bg-themeYellow" name="selection" value="accept">承諾する</button>
            <button type="submit" class="border p-2 rounded bg-red-400" name="selection" value="deny">拒否する</button>
        </form>
    <?php elseif ($invitation['status'] === 'accepted'): ?>
        <p class="text-xl">あなたはすでにこの招待を承諾し、このチームに入っています: <?php echo htmlspecialchars($invitation['team_name']); ?>.</p>
    <?php elseif ($invitation['status'] === 'rejected'): ?>
        <p class="text-xl">あなたはすでにこの招待を断っています</p>
    <?php endif; ?>
</div>

