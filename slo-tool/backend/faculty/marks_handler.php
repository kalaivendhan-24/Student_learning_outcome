<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../config/obe.php';
require_role(['faculty']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_mark'])) {
    $markId = (int) ($_POST['mark_id'] ?? 0);
    if ($markId > 0) {
        try {
            $pdo->beginTransaction();
            $markStmt = $pdo->prepare('SELECT id, student_id, course_id, co_id FROM marks WHERE id = ? LIMIT 1');
            $markStmt->execute([$markId]);
            $mark = $markStmt->fetch();
            if (!$mark) {
                $pdo->rollBack();
                header('Location: marks.php?err=not_found');
                exit;
            }

            $delAtt = $pdo->prepare('DELETE FROM attainment_results WHERE mark_id = ?');
            $delAtt->execute([$markId]);

            if ($delAtt->rowCount() === 0) {
                $mapCountStmt = $pdo->prepare('SELECT COUNT(*) FROM co_po_mapping WHERE co_id = ?');
                $mapCountStmt->execute([(int) $mark['co_id']]);
                $mapCount = (int) $mapCountStmt->fetchColumn();
                if ($mapCount > 0) {
                    $oldAttStmt = $pdo->prepare('SELECT id FROM attainment_results WHERE mark_id IS NULL AND student_id = ? AND course_id = ? AND co_id = ? ORDER BY id DESC LIMIT ' . $mapCount);
                    $oldAttStmt->execute([(int) $mark['student_id'], (int) $mark['course_id'], (int) $mark['co_id']]);
                    $oldIds = array_map('intval', array_column($oldAttStmt->fetchAll(), 'id'));
                    if ($oldIds) {
                        $placeholders = implode(',', array_fill(0, count($oldIds), '?'));
                        $purgeStmt = $pdo->prepare('DELETE FROM attainment_results WHERE id IN (' . $placeholders . ')');
                        $purgeStmt->execute($oldIds);
                    }
                }
            }

            $delMark = $pdo->prepare('DELETE FROM marks WHERE id = ?');
            $delMark->execute([$markId]);
            $pdo->commit();
            header('Location: marks.php?del=1');
            exit;
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            header('Location: marks.php?err=delete_failed');
            exit;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_marks'])) {
    $studentId = (int) $_POST['student_id'];
    $courseId = (int) $_POST['course_id'];
    $coId = (int) $_POST['co_id'];
    $internal = (float) $_POST['internal_mark'];
    $assignment = (float) $_POST['assignment_mark'];
    $exam = (float) $_POST['exam_mark'];
    $total = round(($internal * 0.3) + ($assignment * 0.2) + ($exam * 0.5), 2);

    $stmt = $pdo->prepare('INSERT INTO marks (student_id, course_id, co_id, internal_mark, assignment_mark, exam_mark, total_mark) VALUES (?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute([$studentId, $courseId, $coId, $internal, $assignment, $exam, $total]);
    $markId = (int) $pdo->lastInsertId();

    $coAtt = normalize_attainment($total);
    $mapStmt = $pdo->prepare('SELECT po_id, weight FROM co_po_mapping WHERE co_id = ?');
    $mapStmt->execute([$coId]);
    $maps = $mapStmt->fetchAll();
    foreach ($maps as $m) {
        $poAtt = round(($coAtt * $m['weight']) / 3, 2);
        $save = $pdo->prepare('INSERT INTO attainment_results (mark_id, student_id, course_id, co_id, po_id, co_attainment, po_attainment) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $save->execute([$markId, $studentId, $courseId, $coId, (int)$m['po_id'], $coAtt, $poAtt]);
    }

    header('Location: marks.php?ok=1');
    exit;
}

$students = $pdo->query('SELECT id, reg_no, name FROM students ORDER BY reg_no')->fetchAll();
$courses = $pdo->query('SELECT id, code, name FROM courses ORDER BY code')->fetchAll();
$cos = $pdo->query('SELECT id, code FROM course_outcomes ORDER BY code')->fetchAll();
$recent = $pdo->query('SELECT m.id, s.reg_no, s.name, c.code, co.code AS co_code, m.total_mark FROM marks m JOIN students s ON m.student_id=s.id JOIN courses c ON m.course_id=c.id JOIN course_outcomes co ON m.co_id=co.id ORDER BY m.id DESC LIMIT 20')->fetchAll();
?>
