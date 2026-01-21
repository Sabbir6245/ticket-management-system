<?php
session_start();

$conn = new mysqli("localhost", "root", "", "ticket_management");

$userResult = $conn->query("SELECT id, username, email, role, status, created_at FROM users");
$users = [];
$counts = ['total'=>0,'active'=>0,'blocked'=>0];

while ($row = $userResult->fetch_assoc()) {
    $users[] = $row;
    $counts['total']++;
    ($row['status'] ?? 'active') === 'active' ? $counts['active']++ : $counts['blocked']++;
}

$eventResult = $conn->query("
    SELECT e.id, e.title, e.event_date, e.venue, e.status, u.username AS organiser
    FROM events e
    JOIN users u ON e.organiser_id = u.id
");
$events = $eventResult->fetch_all(MYSQLI_ASSOC);

$usersHtml = "<table>
<tr>
<th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Registered</th><th>Action</th>
</tr>";

foreach($users as $u) {
    $status = $u['status'] ?? 'active';
    $buttonText = ($status === 'active') ? 'Block' : 'Unblock';
    $buttonClass = ($status === 'active') ? 'block-btn' : 'unblock-btn';
    
    $usersHtml .= "<tr>
    <td>{$u['id']}</td>
    <td>" . htmlspecialchars($u['username']) . "</td>
    <td>" . htmlspecialchars($u['email']) . "</td>
    <td>" . htmlspecialchars($u['role']) . "</td>
    <td class='status-{$status}'>" . ucfirst($status) . "</td>
    <td>" . ($u['created_at'] ?? '-') . "</td>
    <td>
        <button class='{$buttonClass}' onclick='blockUser({$u['id']}, \"{$status}\")'>{$buttonText}</button>
    </td>
    </tr>";
}
$usersHtml .= "</table>";

$eventsHtml = "<table>
<tr>
<th>ID</th><th>Title</th><th>Organizer</th><th>Date</th><th>Venue</th><th>Status</th><th>Action</th>
</tr>";

foreach($events as $e) {
    $status = $e['status'] ?? 'active';
    $buttonText = ($status === 'active') ? 'Mute' : 'Unmute';
    $buttonClass = ($status === 'active') ? 'mute-btn' : 'unmute-btn';
    
    $eventsHtml .= "<tr>
    <td>{$e['id']}</td>
    <td>" . htmlspecialchars($e['title']) . "</td>
    <td>" . htmlspecialchars($e['organiser']) . "</td>
    <td>{$e['event_date']}</td>
    <td>" . htmlspecialchars($e['venue']) . "</td>
    <td class='status-{$status}'>" . ucfirst($status) . "</td>
    <td>
        <button class='{$buttonClass}' onclick='muteEvent({$e['id']}, \"{$status}\")'>{$buttonText}</button>
    </td>
    </tr>";
}
$eventsHtml .= "</table>";

$summaryHtml = "<table>
<tr><th>Total</th><th>Active</th><th>Blocked</th></tr>
<tr>
<td>{$counts['total']}</td>
<td>{$counts['active']}</td>
<td>{$counts['blocked']}</td>
</tr>
</table>";

$conn->close();

echo json_encode([
    'users' => $usersHtml,
    'events' => $eventsHtml,
    'summary' => $summaryHtml
]);
?>