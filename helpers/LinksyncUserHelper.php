<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<?php

class LinksyncUserHelper
{
    public function __construct()
    {
        $hasReachedCappingLimit = LinksynceparcelValidator::validateConsignmentLimit();
        if (self::isTerminated()) {
            add_action('admin_notices', array($this, 'setUpLaidInfoMessage'));
        } else if (self::isFreeTrial()) {
            if($hasReachedCappingLimit) {
                if(! self::cappingPreventDisplay()) {
                    add_action('admin_notices', array($this, 'setCappingMessage'));
                }
            } else {
                add_action('admin_notices', array($this, 'setUpLaidInfoMessage'));
            }
        }
    }

    public static function cappingPreventDisplay()
    {
        $pages_except = array(
            'massCreateConsignment'
        );
        return $_GET['page'] == 'linksynceparcel' && in_array($_GET['action'], $pages_except);
    }

    public static function generateCappingMessage($dialog = null)
    {
        $message = get_option('linksync_capping_limit_message', '');

        $kb = get_option('linksync_capping_limit_kb', '');

        if($kb != '') {
            $message .= " <a href='" . $kb . "' target='_blank'>Learn More</a>.";
        }

        if($dialog != null) {
            $message .= "<br/><br/>";
        }

        $update_button = ' <a  href="https://my.linksync.com/index.php?m=dashboard"
                              target="_blank"
                              class="button button-primary" style="margin-top: -4px;">Click here to upgrade now</a>';
        $message .= $update_button;

        return $message;

    }

    public static function setCappingMessage($admin_notice = true)
    {
        ?>
            <div class="error linksynceparcel_capping<?php echo $admin_notice == true ? " notice" : ''; ?>">
                <p><?php echo self::generateCappingMessage(); ?></p>
            </div>
        <?php
    }

    public static function setUpLaidInfoMessage()
    {
        $message = LinksyncApiController::get_current_laid_info();
        $isFreeTrial = false;
        $service_status = '';
        $message_data = '';
        $user_message = '';
        $api_time = isset($message['time']) ? new DateTime($message['time']) : new DateTime();

        if (isset($message['message'])) {
            $message_data = explode(',', $message['message']);
        } elseif (isset($message['userMessage'])) {
            $message_data = explode(',', $message['userMessage']);
        }

        if (is_array($message_data) && !empty($message_data[2]) && !empty($message_data[1]) && !empty($message_data[0])) {
            $isFreeTrial = self::isFreeTrial($message_data[2]);
            $service_status = $message_data[1];
            $registrationDate = $message_data[0];
        } else if (count($message_data) <= 1 && isset($message['userMessage']) && is_string($message['userMessage'])) {
            $user_message = ucfirst($message['userMessage']);
        }

        $update_button = '<a  href="https://my.linksync.com/index.php?m=dashboard"
                              target="_blank"
                              class="button button-primary" style="margin-top: -4px;">Click here to upgrade now</a>';

        if (true == $isFreeTrial && isset($registrationDate)) {
            $registrationDate = trim($registrationDate);
            $service_status = trim($service_status);
            $remaining_days = self::getRemainingDaysOfTrial($registrationDate, $api_time);

            if ('Terminated' == $service_status) {
                $user_message = 'Hey, sorry to say but your linksync free trial has ended! '.$update_button;
            } elseif ('Suspended' == $service_status) {
                $user_message = 'Hey, sorry to say but your linksync account was Suspended!'.$update_button;
            } elseif ('Cancelled' == $service_status) {
                $user_message = 'Hey, sorry to say but your linksync account was Cancelled!'.$update_button;
            } elseif ('1' == $remaining_days && 'Terminated' != $service_status) {
                $user_message = 'Your linksync FREE  trial ends tomorrow! '.$update_button;
            } elseif ('0' == $remaining_days && 'Terminated' != $service_status) {
                $user_message = 'Your linksync FREE trial ends today! '.$update_button;
            } else {
                $user_message = 'Your linksync FREE trial ends in ' . $remaining_days . ' days! '.$update_button;
            }

        } else if ('Terminated' == $service_status) {
            $user_message = 'Hey, sorry to say but your linksync account was Terminated!';
        } else if ('Suspended' == $service_status) {
            $user_message = 'Hey, sorry to say but your linksync account was Suspended!';
        }


        if (!empty($user_message)) {
            ?>
            <div class="error notice">
                <h3><?php echo $user_message; ?></h3>
            </div>
            <?php

        }

    }

    public static function getRemainingDaysOfTrial($productRegistrationDate, DateTime $current_api_time)
    {

        // $next_due_date = date('Y-m-d', strtotime($productRegistrationDate . "+13 days"));
        $duedate = new DateTime($productRegistrationDate);
        $next_due_date = $duedate->add(new DateInterval('P14D'));

        $dueDateEnds = new DateTime($next_due_date->format('Y-m-d'));

        $today = new DateTime($current_api_time->format('Y-m-d'));

        $trialDaysRemaining = $today->diff($dueDateEnds);

        return $trialDaysRemaining->format("%d");

    }

    public static function isFreeTrial($package_id = '')
    {
        $laid_info = LinksyncApiController::get_current_laid_info();
        if(!empty($laid_info) && $laid_info != '') {
            if(empty($package_id)) {
                $message_data = explode(',', $laid_info['message']);
                $package_id =  $message_data[2];
            }

            $package_id = trim($package_id);
            $package = self::getLinksyncPlansForLaid($package_id);
            if ('14 Days Free Trial' == $package) {
                return true;
            }
        }

        return false;
    }

    public static function isTerminated()
    {
        $laid_info = LinksyncApiController::get_current_laid_info();
        if(!empty($laid_info) && $laid_info != '') {
            if(!empty($laid_info['message'])) {
                $message_data = explode(',', $laid_info['message']);
            } else {
                $message_data = explode(',', $laid_info['userMessage']);
            }

            $status =  $message_data[1];

            if (trim($status) == 'Terminated') {
                return true;
            }
        }
        return false;
    }

    public static function getLinksyncPlansForLaid($planId)
    {
        $plans = array(
            1 => 'Basic',
            2 => 'Business',
            3 => 'Premium',
            4 => '14 Days Free Trial',
            5 => 'Unlimited',
        );

        if (!empty($planId) && isset($plans[$planId])) {
            return $plans[$planId];
        }

        return $planId;

    }

}

new LinksyncUserHelper();
