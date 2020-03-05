<?php
require_once "./includes/config.php";
/**
 * Global part
 */
// Init date and json
$date = date('Y-m-01');
$json = "";
// Get all activity check and count by days
$query = $db->prepare('SELECT * FROM activity_check WHERE update_date LIKE :date_id');
switch ($date) {
    case date('Y-m-01'):
        $query->execute(array(
            'date_id' => $date." %"
        ));
        $count = 0;
        while ($data = $query->fetch()) {
            $count++;
        }
        $json .= $count.", ";
        $date = date('Y-m-02');
    case date('Y-m-02'):
        $query->execute(array(
            'date_id' => $date." %"
        ));
        $count = 0;
        while ($data = $query->fetch()) {
            $count++;
        }
        $json .= $count.", ";
        $date = date('Y-m-03');
    case date('Y-m-03'):
        $query->execute(array(
            'date_id' => $date." %"
        ));
        $count = 0;
        while ($data = $query->fetch()) {
            $count++;
        }
        $json .= $count.", ";
        $date = date('Y-m-04');
    case date('Y-m-04'):
        $query->execute(array(
            'date_id' => $date." %"
        ));
        $count = 0;
        while ($data = $query->fetch()) {
            $count++;
        }
        $json .= $count.", ";
        $date = date('Y-m-05');
    case date('Y-m-05'):
        $query->execute(array(
            'date_id' => $date." %"
        ));
        $count = 0;
        while ($data = $query->fetch()) {
            $count++;
        }
        $json .= $count.", ";
        $date = date('Y-m-06');
    case date('Y-m-06'):
        $query->execute(array(
            'date_id' => $date." %"
        ));
        $count = 0;
        while ($data = $query->fetch()) {
            $count++;
        }
        $json .= $count.", ";
        $date = date('Y-m-07');
    case date('Y-m-07'):
        $query->execute(array(
            'date_id' => $date." %"
        ));
        $count = 0;
        while ($data = $query->fetch()) {
            $count++;
        }
        $json .= $count.", ";
        $date = date('Y-m-08');
    case date('Y-m-08'):
        $query->execute(array(
            'date_id' => $date." %"
        ));
        $count = 0;
        while ($data = $query->fetch()) {
            $count++;
        }
        $json .= $count.", ";
        $date = date('Y-m-09');
    case date('Y-m-09'):
        $query->execute(array(
            'date_id' => $date." %"
        ));
        $count = 0;
        while ($data = $query->fetch()) {
            $count++;
        }
        $json .= $count.", ";
        $date = date('Y-m-10');
    case date('Y-m-10'):
        $query->execute(array(
            'date_id' => $date." %"
        ));
        $count = 0;
        while ($data = $query->fetch()) {
            $count++;
        }
        $json .= $count.", ";
        $date = date('Y-m-11');
    case date('Y-m-11'):
        $query->execute(array(
            'date_id' => $date." %"
        ));
        $count = 0;
        while ($data = $query->fetch()) {
            $count++;
        }
        $json .= $count.", ";
        $date = date('Y-m-12');
    case date('Y-m-12'):
        $query->execute(array(
            'date_id' => $date." %"
        ));
        $count = 0;
        while ($data = $query->fetch()) {
            $count++;
        }
        $json .= $count.", ";
        $date = date('Y-m-13');
    case date('Y-m-13'):
        $query->execute(array(
            'date_id' => $date." %"
        ));
        $count = 0;
        while ($data = $query->fetch()) {
            $count++;
        }
        $json .= $count.", ";
        $date = date('Y-m-14');
    case date('Y-m-14'):
        $query->execute(array(
            'date_id' => $date." %"
        ));
        $count = 0;
        while ($data = $query->fetch()) {
            $count++;
        }
        $json .= $count.", ";
        $date = date('Y-m-15');
    case date('Y-m-15'):
        $query->execute(array(
            'date_id' => $date." %"
        ));
        $count = 0;
        while ($data = $query->fetch()) {
            $count++;
        }
        $json .= $count;
        break;
}
$query->closeCursor();
// Function stats
$current_date = date('Y-m-d');
if ($current_date == date('Y-m-01') || $current_date > date('Y-m-15')) {
    $percent = '<span class="text-info"><i class="fad fa-arrow-left"></i> OFF %</span>';
} else {
    // Query to count now and previous date
    $query = $db->prepare('SELECT * FROM activity_check WHERE update_date LIKE :date_id');
    $query->execute(array(
        'date_id' => date('Y-m-d') . ' %'
    ));
    $val1 = 0;
    while ($data = $query->fetch()) {
        $val1++;
    }
    $query->execute(array(
        'date_id' => date('Y-m-d', strtotime('-1 day')) . ' %'
    ));
    $val2 = 0;
    while ($data = $query->fetch()) {
        $val2++;
    }
    $query->closeCursor();
    if ($val1 == 0 && $val2 == 0) {
        $val = (double) 0;
    } else {
        $val = (double) (-100 + ($val1 / $val2) * 100);
    }
    if ($val > 1000.00) {
        $percent = '<span class="text-success"><i class="fad fa-arrow-up"></i> '.((double) $val1 * 100).'%</span>';
    } elseif ($val > 0.00) {
        $percent = '<span class="text-success"><i class="fad fa-arrow-up"></i> '.number_format($val, 2, ',', ' ').'%</span>';
    } elseif ($val < 0.00) {
        $percent = '<span class="text-danger"><i class="fad fa-arrow-down"></i> '.number_format($val, 2, ',', ' ').'%</span>';
    } elseif ($val == 0.00) {
        $percent = '<span class="text-warning"><i class="fad fa-arrow-left"></i> '.number_format($val, 2, ',', ' ').'%</span>';
    }
}

// Init date and json for second part date
$date = date('Y-m-16');
$json_hs = "";
// Get all activity check and count by days
$query = $db->prepare('SELECT * FROM activity_check WHERE update_date LIKE :date_id');
switch ($date) {
    case date('Y-m-16'):
        $query->execute(array(
            'date_id' => $date." %"
        ));
        $count = 0;
        while ($data = $query->fetch()) {
            $count++;
        }
        $json_hs .= $count.", ";
        $date = date('Y-m-17');
    case date('Y-m-17'):
        $query->execute(array(
            'date_id' => $date." %"
        ));
        $count = 0;
        while ($data = $query->fetch()) {
            $count++;
        }
        $json_hs .= $count.", ";
        $date = date('Y-m-18');
    case date('Y-m-18'):
        $query->execute(array(
            'date_id' => $date." %"
        ));
        $count = 0;
        while ($data = $query->fetch()) {
            $count++;
        }
        $json_hs .= $count.", ";
        $date = date('Y-m-19');
    case date('Y-m-19'):
        $query->execute(array(
            'date_id' => $date." %"
        ));
        $count = 0;
        while ($data = $query->fetch()) {
            $count++;
        }
        $json_hs .= $count.", ";
        $date = date('Y-m-20');
    case date('Y-m-20'):
        $query->execute(array(
            'date_id' => $date." %"
        ));
        $count = 0;
        while ($data = $query->fetch()) {
            $count++;
        }
        $json_hs .= $count.", ";
        $date = date('Y-m-21');
    case date('Y-m-21'):
        $query->execute(array(
            'date_id' => $date." %"
        ));
        $count = 0;
        while ($data = $query->fetch()) {
            $count++;
        }
        $json_hs .= $count.", ";
        $date = date('Y-m-22');
    case date('Y-m-22'):
        $query->execute(array(
            'date_id' => $date." %"
        ));
        $count = 0;
        while ($data = $query->fetch()) {
            $count++;
        }
        $json_hs .= $count.", ";
        $date = date('Y-m-23');
    case date('Y-m-23'):
        $query->execute(array(
            'date_id' => $date." %"
        ));
        $count = 0;
        while ($data = $query->fetch()) {
            $count++;
        }
        $json_hs .= $count.", ";
        $date = date('Y-m-24');
    case date('Y-m-24'):
        $query->execute(array(
            'date_id' => $date." %"
        ));
        $count = 0;
        while ($data = $query->fetch()) {
            $count++;
        }
        $json_hs .= $count.", ";
        $date = date('Y-m-25');
    case date('Y-m-25'):
        $query->execute(array(
            'date_id' => $date." %"
        ));
        $count = 0;
        while ($data = $query->fetch()) {
            $count++;
        }
        $json_hs .= $count.", ";
        $date = date('Y-m-26');
    case date('Y-m-26'):
        $query->execute(array(
            'date_id' => $date." %"
        ));
        $count = 0;
        while ($data = $query->fetch()) {
            $count++;
        }
        $json_hs .= $count.", ";
        $date = date('Y-m-27');
    case date('Y-m-27'):
        $query->execute(array(
            'date_id' => $date." %"
        ));
        $count = 0;
        while ($data = $query->fetch()) {
            $count++;
        }
        $json_hs .= $count.", ";
        $date = date('Y-m-28');
    case date('Y-m-28'):
        $query->execute(array(
            'date_id' => $date." %"
        ));
        $count = 0;
        while ($data = $query->fetch()) {
            $count++;
        }
        $json_hs .= $count.", ";
        $date = date('Y-m-29');
    case date('Y-m-29'):
        $query->execute(array(
            'date_id' => $date." %"
        ));
        $count = 0;
        while ($data = $query->fetch()) {
            $count++;
        }
        $json_hs .= $count.", ";
        $date = date('Y-m-30');
        case date('Y-m-3'):
            $query->execute(array(
                'date_id' => $date." %"
            ));
            $count = 0;
            while ($data = $query->fetch()) {
                $count++;
            }
            $json_hs .= $count.", ";
            $date = date('Y-m-31');
    case date('Y-m-31'):
        $query->execute(array(
            'date_id' => $date." %"
        ));
        $count = 0;
        while ($data = $query->fetch()) {
            $count++;
        }
        $json_hs .= $count;
        break;
}
$query->closeCursor();

/**
 * Individual part
 */
if (!isAdmin()) {
    // Init date and json
    $date = date('Y-m-01');
    $json_me = "";
    // Get all activity check and count by days
    $query = $db->prepare('SELECT * FROM activity_check WHERE update_date LIKE :date_id AND advisor_id = :advisor');
    switch ($date) {
        case date('Y-m-01'):
            $query->execute(array(
                'date_id' => $date." %",
                'advisor' => $_COOKIE['advisor']
            ));
            $count = 0;
            while ($data = $query->fetch()) {
                $count++;
            }
            $json_me .= $count.", ";
            $date = date('Y-m-02');
        case date('Y-m-02'):
            $query->execute(array(
                'date_id' => $date." %",
                'advisor' => $_COOKIE['advisor']
            ));
            $count = 0;
            while ($data = $query->fetch()) {
                $count++;
            }
            $json_me .= $count.", ";
            $date = date('Y-m-03');
        case date('Y-m-03'):
            $query->execute(array(
                'date_id' => $date." %",
                'advisor' => $_COOKIE['advisor']
            ));
            $count = 0;
            while ($data = $query->fetch()) {
                $count++;
            }
            $json_me .= $count.", ";
            $date = date('Y-m-04');
        case date('Y-m-04'):
            $query->execute(array(
                'date_id' => $date." %",
                'advisor' => $_COOKIE['advisor']
            ));
            $count = 0;
            while ($data = $query->fetch()) {
                $count++;
            }
            $json_me .= $count.", ";
            $date = date('Y-m-05');
        case date('Y-m-05'):
            $query->execute(array(
                'date_id' => $date." %",
                'advisor' => $_COOKIE['advisor']
            ));
            $count = 0;
            while ($data = $query->fetch()) {
                $count++;
            }
            $json_me .= $count.", ";
            $date = date('Y-m-06');
        case date('Y-m-06'):
            $query->execute(array(
                'date_id' => $date." %",
                'advisor' => $_COOKIE['advisor']
            ));
            $count = 0;
            while ($data = $query->fetch()) {
                $count++;
            }
            $json_me .= $count.", ";
            $date = date('Y-m-07');
        case date('Y-m-07'):
            $query->execute(array(
                'date_id' => $date." %",
                'advisor' => $_COOKIE['advisor']
            ));
            $count = 0;
            while ($data = $query->fetch()) {
                $count++;
            }
            $json_me .= $count.", ";
            $date = date('Y-m-08');
        case date('Y-m-08'):
            $query->execute(array(
                'date_id' => $date." %",
                'advisor' => $_COOKIE['advisor']
            ));
            $count = 0;
            while ($data = $query->fetch()) {
                $count++;
            }
            $json_me .= $count.", ";
            $date = date('Y-m-09');
        case date('Y-m-09'):
            $query->execute(array(
                'date_id' => $date." %",
                'advisor' => $_COOKIE['advisor']
            ));
            $count = 0;
            while ($data = $query->fetch()) {
                $count++;
            }
            $json_me .= $count.", ";
            $date = date('Y-m-10');
        case date('Y-m-10'):
            $query->execute(array(
                'date_id' => $date." %",
                'advisor' => $_COOKIE['advisor']
            ));
            $count = 0;
            while ($data = $query->fetch()) {
                $count++;
            }
            $json_me .= $count.", ";
            $date = date('Y-m-11');
        case date('Y-m-11'):
            $query->execute(array(
                'date_id' => $date." %",
                'advisor' => $_COOKIE['advisor']
            ));
            $count = 0;
            while ($data = $query->fetch()) {
                $count++;
            }
            $json_me .= $count.", ";
            $date = date('Y-m-12');
        case date('Y-m-12'):
            $query->execute(array(
                'date_id' => $date." %",
                'advisor' => $_COOKIE['advisor']
            ));
            $count = 0;
            while ($data = $query->fetch()) {
                $count++;
            }
            $json_me .= $count.", ";
            $date = date('Y-m-13');
        case date('Y-m-13'):
            $query->execute(array(
                'date_id' => $date." %",
                'advisor' => $_COOKIE['advisor']
            ));
            $count = 0;
            while ($data = $query->fetch()) {
                $count++;
            }
            $json_me .= $count.", ";
            $date = date('Y-m-14');
        case date('Y-m-14'):
            $query->execute(array(
                'date_id' => $date." %",
                'advisor' => $_COOKIE['advisor']
            ));
            $count = 0;
            while ($data = $query->fetch()) {
                $count++;
            }
            $json_me .= $count.", ";
            $date = date('Y-m-15');
        case date('Y-m-15'):
            $query->execute(array(
                'date_id' => $date." %",
                'advisor' => $_COOKIE['advisor']
            ));
            $count = 0;
            while ($data = $query->fetch()) {
                $count++;
            }
            $json_me .= $count;
            break;
    }
    $query->closeCursor();
    // Function stats
    if ($current_date == date('Y-m-01') || $current_date > date('Y-m-15')) {
        $percent_me = '<span class="text-info"><i class="fad fa-arrow-left"></i> OFF %</span>';
    } else {
        // Query to count now and previous date
        $query = $db->prepare('SELECT * FROM activity_check WHERE (update_date LIKE :date_id) AND advisor_id = :advisor');
        $query->execute(array(
            'date_id' => date('Y-m-d') . ' %',
            'advisor' => $_COOKIE['advisor']
        ));
        $val_me1 = 0;
        while ($data = $query->fetch()) {
            $val_me1++;
        }
        $query->execute(array(
            'date_id' => date('Y-m-d', strtotime('-1 day')) . ' %',
            'advisor' => $_COOKIE['advisor']
        ));
        $val_me2 = 0;
        while ($data = $query->fetch()) {
            $val_me2++;
        }
        $query->closeCursor();
        if ($val_me2 != 0) {
            $val_me = (double) (-100 + ($val_me1 / $val_me2) * 100);
        } else {
            $val_me = 100.00;
        }
        if ($val_me > 0.00) {
            $percent_me = '<span class="text-success"><i class="fad fa-arrow-up"></i> '.number_format($val_me, 2, ',', ' ').'%</span>';
        } elseif ($val_me < 0.00) {
            $percent_me = '<span class="text-danger"><i class="fad fa-arrow-down"></i> '.number_format($val_me, 2, ',', ' ').'%</span>';
        } else {
            $percent_me = '<span class="text-warning"><i class="fad fa-arrow-left"></i> '.number_format($val_me, 2, ',', ' ').'%</span>';
        }
    }

    // Init date and json for second part date
    $date = date('Y-m-16');
    $json_me_hs = "";
    // Get all activity check and count by days
    $query = $db->prepare('SELECT * FROM activity_check WHERE update_date LIKE :date_id AND advisor_id = :advisor');
    switch ($date) {
        case date('Y-m-16'):
            $query->execute(array(
                'date_id' => $date." %",
                'advisor' => $_COOKIE['advisor']
            ));
            $count = 0;
            while ($data = $query->fetch()) {
                $count++;
            }
            $json_me_hs .= $count.", ";
            $date = date('Y-m-17');
        case date('Y-m-17'):
            $query->execute(array(
                'date_id' => $date." %",
                'advisor' => $_COOKIE['advisor']
            ));
            $count = 0;
            while ($data = $query->fetch()) {
                $count++;
            }
            $json_me_hs .= $count.", ";
            $date = date('Y-m-18');
        case date('Y-m-18'):
            $query->execute(array(
                'date_id' => $date." %",
                'advisor' => $_COOKIE['advisor']
            ));
            $count = 0;
            while ($data = $query->fetch()) {
                $count++;
            }
            $json_me_hs .= $count.", ";
            $date = date('Y-m-19');
        case date('Y-m-19'):
            $query->execute(array(
                'date_id' => $date." %",
                'advisor' => $_COOKIE['advisor']
            ));
            $count = 0;
            while ($data = $query->fetch()) {
                $count++;
            }
            $json_me_hs .= $count.", ";
            $date = date('Y-m-20');
        case date('Y-m-20'):
            $query->execute(array(
                'date_id' => $date." %",
                'advisor' => $_COOKIE['advisor']
            ));
            $count = 0;
            while ($data = $query->fetch()) {
                $count++;
            }
            $json_me_hs .= $count.", ";
            $date = date('Y-m-21');
        case date('Y-m-21'):
            $query->execute(array(
                'date_id' => $date." %",
                'advisor' => $_COOKIE['advisor']
            ));
            $count = 0;
            while ($data = $query->fetch()) {
                $count++;
            }
            $json_me_hs .= $count.", ";
            $date = date('Y-m-22');
        case date('Y-m-22'):
            $query->execute(array(
                'date_id' => $date." %",
                'advisor' => $_COOKIE['advisor']
            ));
            $count = 0;
            while ($data = $query->fetch()) {
                $count++;
            }
            $json_me_hs .= $count.", ";
            $date = date('Y-m-23');
        case date('Y-m-23'):
            $query->execute(array(
                'date_id' => $date." %",
                'advisor' => $_COOKIE['advisor']
            ));
            $count = 0;
            while ($data = $query->fetch()) {
                $count++;
            }
            $json_me_hs .= $count.", ";
            $date = date('Y-m-24');
        case date('Y-m-24'):
            $query->execute(array(
                'date_id' => $date." %",
                'advisor' => $_COOKIE['advisor']
            ));
            $count = 0;
            while ($data = $query->fetch()) {
                $count++;
            }
            $json_me_hs .= $count.", ";
            $date = date('Y-m-25');
        case date('Y-m-25'):
            $query->execute(array(
                'date_id' => $date." %",
                'advisor' => $_COOKIE['advisor']
            ));
            $count = 0;
            while ($data = $query->fetch()) {
                $count++;
            }
            $json_me_hs .= $count.", ";
            $date = date('Y-m-26');
        case date('Y-m-26'):
            $query->execute(array(
                'date_id' => $date." %",
                'advisor' => $_COOKIE['advisor']
            ));
            $count = 0;
            while ($data = $query->fetch()) {
                $count++;
            }
            $json_me_hs .= $count.", ";
            $date = date('Y-m-27');
        case date('Y-m-27'):
            $query->execute(array(
                'date_id' => $date." %",
                'advisor' => $_COOKIE['advisor']
            ));
            $count = 0;
            while ($data = $query->fetch()) {
                $count++;
            }
            $json_me_hs .= $count.", ";
            $date = date('Y-m-28');
        case date('Y-m-28'):
            $query->execute(array(
                'date_id' => $date." %",
                'advisor' => $_COOKIE['advisor']
            ));
            $count = 0;
            while ($data = $query->fetch()) {
                $count++;
            }
            $json_me_hs .= $count.", ";
            $date = date('Y-m-29');
        case date('Y-m-29'):
            $query->execute(array(
                'date_id' => $date." %",
                'advisor' => $_COOKIE['advisor']
            ));
            $count = 0;
            while ($data = $query->fetch()) {
                $count++;
            }
            $json_me_hs .= $count.", ";
            $date = date('Y-m-30');
        case date('Y-m-30'):
            $query->execute(array(
                'date_id' => $date." %",
                'advisor' => $_COOKIE['advisor']
            ));
            $count = 0;
            while ($data = $query->fetch()) {
                $count++;
            }
            $json_me_hs .= $count.", ";
            $date = date('Y-m-31');
        case date('Y-m-31'):
            $query->execute(array(
                'date_id' => $date." %",
                'advisor' => $_COOKIE['advisor']
            ));
            $count = 0;
            while ($data = $query->fetch()) {
                $count++;
            }
            $json_me_hs .= $count;
            break;
    }
    $query->closeCursor();
}