<?php

// проверяем авторизацию
function login_check() {
    if ( !isset($_SESSION['user_id']) ) {
        die('ACCESS DENIED');
    }
}

// flash сообщение post redirect get
function flash() {
    if ( isset($_SESSION['success']) ) {
        echo('<p style="color: green;">'.$_SESSION['success'].'</p>');
        unset($_SESSION['success']);
    } elseif ( isset($_SESSION['failure']) ) {
        echo('<p style="color: red;">'.$_SESSION['failure'].'</p>');
        unset($_SESSION['failure']);
    }
}

// авторизация
function login($pdo) {
    if ( strlen($_POST['email']) > 0 && strlen($_POST['pass']) > 0 ) {
        if ( str_contains($_POST['email'], '@') ) {
            $check = hash('md5',  'XyZzy12*_'.$_POST['pass']);
            $stmt = $pdo->prepare('SELECT user_id FROM users WHERE email = :em AND password = :pw');
            $stmt->execute(array(':em' => $_POST['email'], ':pw' => $check));
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ( $row !== false ) {
                $_SESSION['user_id'] = $row['user_id'];
                return true;
            } else {
                $_SESSION['failure'] = 'Incorrect email or password';
                return false;
            }
        } else {
            $_SESSION['failure'] = 'Email must have an at-sign (@)';
            return false;
        }
    } else {
        $_SESSION['failure'] = 'User name and password are required';
        return false;
    }
} 


// валидируем содержимое post запроса Profile
function profile_validation() {
    if ( strlen($_POST['first_name']) > 0 && 
         strlen($_POST['last_name']) > 0 && 
         strlen($_POST['email']) > 0 && 
         strlen($_POST['headline']) && 
         strlen($_POST['summary']) > 0 ) {
        if ( str_contains($_POST['email'], '@') ) {
            return true;
        } else {
            $_SESSION['failure'] = 'Email address must contain @';
            return false;
        }
    } else {
        $_SESSION['failure'] = 'All fields are required';
        return false;
    }
}

// валидируем содержимое post запроса Position
function position_validation() {
    for($i=1; $i<=9; $i++) {
        if ( !isset($_POST['year'.$i]) ) continue;
        if ( !isset($_POST['desc'.$i]) ) continue;
        $year = $_POST['year'.$i];
        $desc = $_POST['desc'.$i];
        if ( strlen($year) == 0 || strlen($desc) == 0) {
            $_SESSION['failure'] = 'All fields are required';
            return false;
        } elseif ( !is_numeric($year) ) {
            $_SESSION['failure'] = 'Position year must be numeric';
            return false;
        }
    }
    return true;
}

// валидируем содержимое post запроса Education
function education_validation() {
    for($i=1; $i<=9; $i++) {
        if ( !isset($_POST['edu_year'.$i]) ) continue;
        if ( !isset($_POST['edu_school'.$i]) ) continue;
        $year = $_POST['edu_year'.$i];
        $desc = $_POST['edu_school'.$i];
        if ( strlen($year) == 0 || strlen($desc) == 0) {
            $_SESSION['failure'] = 'All fields are required';
            return false;
        } elseif ( !is_numeric($year) ) {
            $_SESSION['failure'] = 'Position year must be numeric';
            return false;
        }
    }
    return true;
}

// добавляем в БД: таблица Profile
function insert_profile($pdo) {
        $stmt = $pdo->prepare('INSERT INTO Profile (user_id, first_name, last_name, email, headline, summary) VALUES (:uid, :fn, :ln, :em, :he, :su)');
        $stmt->execute(array(
            ':uid' => $_SESSION['user_id'],
            ':fn' => $_POST['first_name'],
            ':ln' => $_POST['last_name'],
            ':em' => $_POST['email'],
            ':he' => $_POST['headline'],
            ':su' => $_POST['summary'])
        );
        $_SESSION['profile_id'] = $pdo->lastInsertId();
}

// добавляем в БД: таблица Position
function insert_position($pdo, $profile_id) {
    $rank = 1;
    for( $i=1; $i<=9; $i++ ) {
        if ( !isset($_POST['year'.$i]) ) continue;
        if ( !isset($_POST['desc'.$i]) ) continue;
        $year = $_POST['year'.$i];
        $desc = $_POST['desc'.$i];

        $stmt = $pdo->prepare('INSERT INTO Position (profile_id, `rank`, `year`, `description`) VALUES (:pid, :rk, :yr, :dc)');
        $stmt->execute(array(
        ':pid' => $profile_id,
        ':rk' => $rank,
        ':yr' => $year,
        ':dc' => $desc)
        );
        $rank++;
    }
}

// добавляем в БД: таблица Education
function insert_education($pdo, $profile_id) {
    $rank = 1;
    for( $i=1; $i<=9; $i++ ) {
        if ( !isset($_POST['edu_year'.$i]) ) continue;
        if ( !isset($_POST['edu_school'.$i]) ) continue;
        $year = $_POST['edu_year'.$i];
        $edu = $_POST['edu_school'.$i];

        $institution_id = false;
        $stmt = $pdo->prepare('SELECT institution_id FROM Institution WHERE name = :name');
        $stmt->execute(array(':name' => $edu));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ( $row !== false ) $institution_id = $row['institution_id'];

        if ( $institution_id === false ) {
            $stmt = $pdo->prepare('INSERT INTO Institution (name) VALUES (:name)');
            $stmt->execute(array(':name' => $edu));
            $institution_id = $pdo->lastInsertId();
        }
        
        $stmt = $pdo->prepare('INSERT INTO Education (profile_id, `rank`, `year`, institution_id) VALUES (:pid, :rk, :yr, :iid)');
        $stmt->execute(array(
        ':pid' => $profile_id,
        ':rk' => $rank,
        ':yr' => $year,
        ':iid' => $institution_id)
        );
        $rank++;
    }
}

// проверяем наличие profile_id в БД по get запросу
function get_profile($pdo) {
    $stmt = $pdo->prepare('SELECT * FROM Profile WHERE profile_id = :pid');
    $stmt->execute(array(':pid' => $_GET['profile_id']));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ( !isset($_GET['profile_id']) ) {
        $_SESSION['failure'] = 'Missing profile_id';
        return false;
    } elseif ( $row === false ) {
        $_SESSION['failure'] = 'Bad value for profile_id';
        return false;
    } else {
        $row['first_name'] = htmlentities($row['first_name']);
        $row['last_name'] = htmlentities($row['last_name']);
        $row['email'] = htmlentities($row['email']);
        $row['headline'] = htmlentities($row['headline']);
        $row['summary'] = htmlentities($row['summary']);
        return $row;
    }
}

// обновляем данные в БД: таблица Profile
function update_profile($pdo) {
    $stmt = $pdo->prepare('UPDATE Profile SET user_id = :uid, first_name = :fn, last_name = :ln, email = :em, headline = :he, summary = :su WHERE user_id = :uid && profile_id = :pid');
    $stmt->execute(array(
    ':uid' => $_SESSION['user_id'],
    ':pid' => $_GET['profile_id'],
    ':fn' => $_POST['first_name'],
    ':ln' => $_POST['last_name'],
    ':em' => $_POST['email'],
    ':he' => $_POST['headline'],
    ':su' => $_POST['summary'])
    );
}

// обновляем данные в БД: таблица Profile
function update_position($pdo) {
    $stmt = $pdo->prepare('DELETE FROM Position WHERE profile_id=:pid');
    $stmt->execute(array(':pid' => $_GET['profile_id']));

    $rank = 1;
    for($i=1; $i<=9; $i++) {
    if ( !isset($_POST['year'.$i]) ) continue;
    if ( !isset($_POST['desc'.$i]) ) continue;
    $year = $_POST['year'.$i];
    $desc = $_POST['desc'.$i];

    $stmt = $pdo->prepare('INSERT INTO Position (profile_id, `rank`, `year`, `description`) VALUES (:pid, :rank, :yr, :dc)');
    $stmt->execute(array(
    ':pid' => $_GET['profile_id'],
    ':rank' => $rank,
    ':yr' => $year,
    ':dc' => $desc)
    );
    $rank++;
    }
}

// обновляем данные в БД: таблица Education
function update_education($pdo) {
    $stmt = $pdo->prepare('DELETE FROM Education WHERE profile_id=:pid');
    $stmt->execute(array(':pid' => $_GET['profile_id']));

    $rank = 1;
    for( $i=1; $i<=9; $i++ ) {
        if ( !isset($_POST['edu_year'.$i]) ) continue;
        if ( !isset($_POST['edu_school'.$i]) ) continue;
        $year = $_POST['edu_year'.$i];
        $edu = $_POST['edu_school'.$i];

        $institution_id = false;
        $stmt = $pdo->prepare('SELECT institution_id FROM Institution WHERE name = :name');
        $stmt->execute(array(':name' => $edu));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ( $row !== false ) $institution_id = $row['institution_id'];

        if ( $institution_id === false ) {
            $stmt = $pdo->prepare('INSERT INTO Institution (name) VALUES (:name)');
            $stmt->execute(array(':name' => $edu));
            $institution_id = $pdo->lastInsertId();
        }
        
        $stmt = $pdo->prepare('INSERT INTO Education (profile_id, `rank`, `year`, institution_id) VALUES (:pid, :rk, :yr, :iid)');
        $stmt->execute(array(
        ':pid' => $_GET['profile_id'],
        ':rk' => $rank,
        ':yr' => $year,
        ':iid' => $institution_id)
        );
        $rank++;
    }
}