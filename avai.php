
<?php
session_start();
include 'avi.php';

$doctor_id = isset($_SESSION['doctor_id']) ? (int)$_SESSION['doctor_id'] : 1;

$message      = '';
$message_type = '';

// ----- Handle form actions -----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action    = $_POST['action']    ?? '';
    $day       = $_POST['day']       ?? '';
    $time_slot = $_POST['time_slot'] ?? '';

    if ($action === 'add' && $day && $time_slot) {
        // Check duplicate
        $stmt = $conn->prepare("SELECT id FROM availability WHERE doctor_id = ? AND day = ? AND time_slot = ?");
        $stmt->bind_param("iss", $doctor_id, $day, $time_slot);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $message      = 'This slot is already added.';
            $message_type = 'error';
        } else {
            $ins = $conn->prepare("INSERT INTO availability (doctor_id, day, time_slot) VALUES (?, ?, ?)");
            $ins->bind_param("iss", $doctor_id, $day, $time_slot);
            if ($ins->execute()) {
                $message      = 'Time slot added successfully.';
                $message_type = 'success';
            } else {
                $message      = 'Error adding slot: ' . $conn->error;
                $message_type = 'error';
            }
        }

    } elseif ($action === 'remove' && $day && $time_slot) {
        $del = $conn->prepare("DELETE FROM availability WHERE doctor_id = ? AND day = ? AND time_slot = ?");
        $del->bind_param("iss", $doctor_id, $day, $time_slot);
        if ($del->execute()) {
            $message      = 'Time slot removed.';
            $message_type = 'success';
        } else {
            $message      = 'Error removing slot: ' . $conn->error;
            $message_type = 'error';
        }
    }
}

// ----- Load current availability -----
$dayOrder = ['monday','tuesday','wednesday','thursday','friday','saturday','sunday'];

$dayLabels = [
    'monday'    => 'Monday',    'tuesday'  => 'Tuesday',
    'wednesday' => 'Wednesday', 'thursday' => 'Thursday',
    'friday'    => 'Friday',    'saturday' => 'Saturday',
    'sunday'    => 'Sunday'
];

$timeSlotLabels = [
    'morning'   => 'Morning (8 AM – 12 PM)',
    'afternoon' => 'Afternoon (12 PM – 4 PM)',
    'evening'   => 'Evening (4 PM – 8 PM)'
];

$stmt = $conn->prepare("SELECT day, time_slot FROM availability WHERE doctor_id = ? ORDER BY FIELD(day,'monday','tuesday','wednesday','thursday','friday','saturday','sunday')");
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();

$availability = [];
while ($row = $result->fetch_assoc()) {
    $availability[$row['day']][] = $row['time_slot'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set Your Availability · Health Without Distance</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --primary:        #2b5f70;
            --primary-light:  #3f7d91;
            --primary-dark:   #1d4552;
            --secondary:      #50a4c0;
            --secondary-dark: #3b8aa3;
            --dark:           #1e293b;
            --gray:           #64748b;
            --light:          #f0f7fc;
            --white:          #ffffff;
            --danger:         #ef4444;
            --danger-dark:    #b91c1c;
            --success-bg:     #f0fdf9;
            --success-text:   #0b5e42;
            --shadow-sm:      0 4px 10px rgba(0,0,0,0.06);
            --shadow-md:      0 8px 24px rgba(0,0,0,0.10);
            --shadow-lg:      0 20px 40px rgba(0,0,0,0.13);
            --radius:         20px;
            --transition:     all 0.22s ease;
        }

        /* ── Layout ── */
        body {
            font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(145deg, #c8dfe9 0%, #e8f4f9 100%);
            min-height: 100vh;
            padding: 3rem 1.5rem;
            color: var(--dark);
            line-height: 1.6;
        }

        .container {
            max-width: 820px;
            width: 100%;
            margin: 0 auto;
        }

        /* ── Page title ── */
        .page-title {
            font-size: 2rem;
            font-weight: 700;
            color: white;
            margin-bottom: 1.8rem;
            display: flex;
            align-items: center;
            gap: 0.9rem;
            text-shadow: 0 2px 8px rgba(0,40,50,0.35);
            letter-spacing: -0.02em;
        }

        .page-title i {
            background: rgba(255,255,255,0.22);
            padding: 0.75rem;
            border-radius: 50%;
            font-size: 1.5rem;
            flex-shrink: 0;
        }

        /* ── Main card ── */
        .availability-container {
            background: var(--white);
            padding: 2.5rem 2.8rem;
            border-radius: 28px;
            box-shadow: var(--shadow-lg);
            border: 1px solid rgba(255,255,255,0.7);
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(18px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ── Notification banner ── */
        .notification {
            display: flex;
            align-items: center;
            gap: 0.9rem;
            padding: 0.9rem 1.4rem;
            border-radius: 14px;
            margin-bottom: 1.8rem;
            font-weight: 500;
            font-size: 0.97rem;
            border-left: 5px solid transparent;
        }

        .notification.success {
            background: var(--success-bg);
            border-left-color: var(--success-text);
            color: var(--success-text);
        }

        .notification.error {
            background: #fef2f2;
            border-left-color: var(--danger);
            color: var(--danger-dark);
        }

        .notification i { font-size: 1.2rem; flex-shrink: 0; }

        /* ── Info header ── */
        .availability-header {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            padding: 1.3rem 1.6rem;
            margin-bottom: 2rem;
            background: linear-gradient(115deg, rgba(43,95,112,0.07) 0%, rgba(80,164,192,0.07) 100%);
            border-radius: var(--radius);
            border-left: 5px solid var(--primary);
        }

        .availability-header .header-icon {
            font-size: 1.4rem;
            color: var(--primary);
            background: rgba(43,95,112,0.12);
            padding: 0.65rem;
            border-radius: 50%;
            flex-shrink: 0;
            margin-top: 0.1rem;
        }

        .availability-header p {
            font-size: 1rem;
            color: var(--dark);
            margin: 0;
        }

        /* ── Section divider ── */
        .section-divider {
            border: none;
            border-top: 2px solid var(--light);
            margin: 2rem 0;
        }

        /* ── Form grid ── */
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.6rem;
            margin-bottom: 1.6rem;
        }

        .form-col {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .form-col label {
            font-weight: 600;
            color: var(--dark);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.95rem;
        }

        .form-col label i {
            color: var(--primary);
            font-size: 1rem;
            width: 1.2rem;
            text-align: center;
        }

        /* ── Select ── */
        select {
            width: 100%;
            padding: 0.85rem 1rem;
            border: 2px solid #dde8f0;
            border-radius: 14px;
            font-size: 0.97rem;
            font-weight: 500;
            color: var(--dark);
            background-color: var(--white);
            transition: var(--transition);
            cursor: pointer;
            appearance: none;
            background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="%232b5f70" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>');
            background-repeat: no-repeat;
            background-position: right 1rem center;
            background-size: 1rem;
        }

        select:hover { border-color: var(--primary-light); background-color: #f8fbfd; }
        select:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(43,95,112,0.15); }

        /* ── Add button row ── */
        .add-btn-row {
            display: flex;
            justify-content: flex-start;
        }

        /* ── Slots section ── */
        .slots-section { margin-top: 0.5rem; }

        .slots-label {
            font-weight: 600;
            color: var(--dark);
            display: flex;
            align-items: center;
            gap: 0.6rem;
            font-size: 1rem;
            margin-bottom: 1rem;
        }

        .slots-label i { color: var(--primary); font-size: 1.1rem; }

        .selected-slots {
            min-height: 140px;
            padding: 1.6rem;
            background: var(--light);
            border-radius: var(--radius);
            border: 2px dashed var(--primary-light);
        }

        .selected-slots .empty-msg {
            text-align: center;
            padding: 2rem 1rem;
            color: var(--gray);
            font-style: italic;
            font-size: 0.97rem;
        }

        /* ── Day group ── */
        .day-slot-group {
            margin-bottom: 1.2rem;
            padding: 1rem 1.3rem;
            background: var(--white);
            border-radius: 16px;
            box-shadow: var(--shadow-sm);
            border: 1px solid rgba(43,95,112,0.1);
        }

        .day-slot-group:last-child { margin-bottom: 0; }

        .day-slot-group h4 {
            color: var(--primary-dark);
            font-size: 1rem;
            font-weight: 700;
            margin-bottom: 0.8rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            border-bottom: 1.5px solid var(--light);
            padding-bottom: 0.6rem;
        }

        .day-slot-group h4 i { color: var(--primary); }

        .time-slots { display: flex; flex-wrap: wrap; gap: 0.6rem; }

        /* ── Slot badge ── */
        .slot-badge {
            background: linear-gradient(135deg, #eaf4f8 0%, #f4f9fc 100%);
            padding: 0.5rem 1rem 0.5rem 1.1rem;
            border-radius: 50px;
            border: 1.5px solid var(--primary-light);
            color: var(--primary-dark);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 600;
            font-size: 0.88rem;
            box-shadow: 0 1px 4px rgba(43,95,112,0.1);
            transition: var(--transition);
        }

        .slot-badge:hover {
            border-color: var(--primary);
            box-shadow: 0 3px 10px rgba(43,95,112,0.18);
        }

        .slot-badge > i { font-size: 0.78rem; color: var(--primary); }

        /* ── Remove button ── */
        .remove-slot-btn {
            background: #fee2e2;
            color: var(--danger);
            border: none;
            border-radius: 50%;
            width: 22px;
            height: 22px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: var(--transition);
            margin-left: 0.2rem;
            font-size: 0.7rem;
            padding: 0;
            flex-shrink: 0;
        }

        .remove-slot-btn:hover {
            background: var(--danger);
            color: white;
            transform: scale(1.15);
        }

        /* ── Buttons ── */
        .btn {
            padding: 0.85rem 2rem;
            border: none;
            border-radius: 50px;
            font-size: 0.97rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            white-space: nowrap;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
            box-shadow: 0 4px 14px rgba(43,95,112,0.3);
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            box-shadow: 0 8px 22px rgba(43,95,112,0.4);
            transform: translateY(-2px);
        }

        .btn-primary:active { transform: translateY(0); box-shadow: var(--shadow-sm); }

        /* ── Responsive ── */
        @media (max-width: 640px) {
            body { padding: 1.5rem 1rem; }
            .availability-container { padding: 1.8rem 1.5rem; }
            .form-row { grid-template-columns: 1fr; gap: 1.2rem; }
            .page-title { font-size: 1.6rem; }
            .availability-header { flex-direction: column; }
            .btn { width: 100%; justify-content: center; }
        }
    </style>
</head>
<body>
<div class="container">

    <h1 class="page-title">
        <i class="fas fa-calendar-check"></i> Set Your Availability
    </h1>

    <div class="availability-container">

        <?php if ($message): ?>
        <div class="notification <?= htmlspecialchars($message_type) ?>">
            <i class="fas <?= $message_type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle' ?>"></i>
            <?= htmlspecialchars($message) ?>
        </div>
        <?php endif; ?>

        <div class="availability-header">
            <i class="fas fa-hourglass-half header-icon"></i>
            <p>Choose a day and a time block, then click <strong>Add time slot</strong>. Click <strong>×</strong> on any badge to remove it.</p>
        </div>

        <!-- ADD SLOT FORM -->
        <form method="POST" action="">
            <input type="hidden" name="action" value="add">

            <div class="form-row">
                <div class="form-col">
                    <label for="day"><i class="fas fa-calendar-day"></i> Weekday</label>
                    <select name="day" id="day">
                        <?php foreach ($dayLabels as $val => $label): ?>
                        <option value="<?= $val ?>"<?= (isset($_POST['day']) && $_POST['day'] === $val && ($_POST['action'] ?? '') === 'add') ? ' selected' : '' ?>>
                            <?= $label ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-col">
                    <label for="time_slot"><i class="fas fa-clock"></i> Time block</label>
                    <select name="time_slot" id="time_slot">
                        <?php foreach ($timeSlotLabels as $val => $label): ?>
                        <option value="<?= $val ?>"<?= (isset($_POST['time_slot']) && $_POST['time_slot'] === $val && ($_POST['action'] ?? '') === 'add') ? ' selected' : '' ?>>
                            <?= $label ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="add-btn-row">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-plus-circle"></i> Add time slot
                </button>
            </div>
        </form>

        <hr class="section-divider">

        <!-- CURRENT SLOTS -->
        <div class="slots-section">
            <div class="slots-label">
                <i class="fas fa-list-ul"></i> Your current weekly slots
            </div>

            <div class="selected-slots">
                <?php if (empty($availability)): ?>
                    <p class="empty-msg">✨ No time slots added yet</p>
                <?php else: ?>
                    <?php foreach ($dayOrder as $day): ?>
                        <?php if (!empty($availability[$day])): ?>
                        <div class="day-slot-group">
                            <h4><i class="fas fa-calendar-alt"></i> <?= $dayLabels[$day] ?></h4>
                            <div class="time-slots">
                                <?php foreach ($availability[$day] as $slot): ?>
                                <div class="slot-badge">
                                    <i class="fas fa-clock"></i>
                                    <?= htmlspecialchars($timeSlotLabels[$slot] ?? $slot) ?>
                                    <form method="POST" action="" style="display:contents;">
                                        <input type="hidden" name="action"    value="remove">
                                        <input type="hidden" name="day"       value="<?= htmlspecialchars($day) ?>">
                                        <input type="hidden" name="time_slot" value="<?= htmlspecialchars($slot) ?>">
                                        <button type="submit" class="remove-slot-btn" title="Remove">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

    </div><!-- /.availability-container -->
</div>
</body>
</html>