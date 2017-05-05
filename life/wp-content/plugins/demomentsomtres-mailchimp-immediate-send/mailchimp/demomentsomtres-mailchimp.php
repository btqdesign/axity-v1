<?php

/**
 * Mailchimp management based on 3.0 API
 * V3.201703012005
 */
Class DeMomentSomTresMailchimp {

    private $api_key;
    private $api_endpoint = 'https://<dc>.api.mailchimp.com/3.0/';
    private $verify_ssl   = false;

    const MAILCHIMP_SUCCESS = 'success';
    const MAILCHIMP_ERROR   = 'error';

    /**
     * Create a new instance
     * @param string $api_key Your MailChimp API key
     * @since 3.0
     */
    function __construct($api_key) {
        $this->api_key      = $api_key;
        list(, $datacentre) = explode('-', $this->api_key);
        $this->api_endpoint = str_replace('<dc>', $datacentre, $this->api_endpoint);
    }

    /**
     * @since 3.0
     * @param type $action
     * @param type $request_type
     * @param type $data
     * @return type
     */
    function call($action, $request_type, $data = array()) {
        $api_key = $this->api_key;
        $url     = $this->api_endpoint . $action;
        $args    = array(
            'headers' => array(
                'Authorization' => 'Basic ' . base64_encode('user:' . $api_key)
            )
        );
        if ($request_type == "GET"):
            $url      .= '?' . http_build_query($data);
            $response = wp_remote_get($url, $args);
            if (wp_remote_retrieve_response_code($response) == 200):
                $body   = wp_remote_retrieve_body($response);
                $answer = json_decode($body);
            else:
                $answer = $response;
            endif;
            return $answer;
        else:
            $args["method"] = $request_type;
            $args["body"]   = json_encode($data);
            $response       = wp_remote_post($url, $args);
            if (wp_remote_retrieve_response_code($response) == 200):
                $body   = wp_remote_retrieve_body($response);
                $answer = json_decode($body);
            else:
                $answer = $response;
            endif;
            return $answer;
        endif;
    }

    /**
     * Inits a MailChimp API session
     * @param string $api
     * @return \DeMomentSomTresMailChimp|null
     * @since 3.0
     */
    public static function Session($api) {
        if ($api == ''):
            return null;
        endif;
        $s = new DeMomentSomTresMailChimp($api);
        return $s;
    }

    /**
     * Get MailChimp Lists
     * @param mixed $mcSession MailChimp Session
     * @param boolean $groups if true groups will be downloaded if they exist
     * @return array associative array with all List containing id, name, groups
     * @since 3.20170201
     */
    public static function GetLists($mcSession, $groups = false) {
        $result = array();
        if ($mcSession):
            $temp = $mcSession->call('lists', "GET", array(
                'fields' => "lists",
                'count'  => 256,
            ));
            if (isset($temp->lists)):
                foreach ($temp->lists as $t):
                    $resultat = array(
                        'id'          => $t->id,
                        'name'        => $t->name,
                        'subscribers' => $t->stats->member_count,
                    );
                    $tempg    = $mcSession->call('lists/' . $t->id . "/interest-categories", "GET", array());
                    if (count($tempg->categories)):
                        $categories = array();
                        foreach ($tempg->categories as $cat):
                            $categoria = array(
                                "id"   => $cat->id,
                                "name" => $cat->title,
                            );
                            $tempi     = $mcSession->call('lists/' . $t->id . "/interest-categories/" . $cat->id . "/interests/", "GET", array(
                                "count" => 256,
                            ));
                            if (count($tempi->interests)):
                                $interests = array();
                                foreach ($tempi->interests as $int):
                                    $interests[] = array(
                                        "id"          => $int->id,
                                        "name"        => $int->name,
                                        "subscribers" => $int->subscriber_count,
                                    );
                                endforeach;
                            endif;
                            $categoria["interests"] = $interests;
                            $categories[]           = $categoria;
                        endforeach;
                        $resultat["interest-groups"] = $categories;
                    endif;
                    $result[] = $resultat;
                endforeach;
            else:
                error_log(__CLASS__ . "::" . __FUNCTION__ . " - ERROR - " . print_r($temp["body"], true));
            endif;
        endif;
        return $result;
    }

    public static function GetList($mcSession, $listid) {
        if ($mcSession):
            $list = $mcSession->call("lists/" . $listid, "GET");
            if (is_array($list)):
                error_log("List $listid not found in DeMomentSomTresMailChimp::GetList. " . print_r($list, true));
                return;
            endif;
            return $list;
        endif;
        return;
    }

    /**
     * Returns user hash based on email
     * @param type $email
     * @return string
     * @since 3.0
     */
    public static function MailChimpUserID($email) {
        return md5(strtolower($email));
    }

    /**
     * Inits a MailChimp API session
     * @param string $api
     * @return \DeMomentSomTresMailChimp|null
     * @since 3.0
     * @deprecated 3.20170201
     */
    public static function MailChimpSession($api) {
        return self::Session($api);
    }

    /**
     * Get MailChimp Lists
     * @param mixed $mcSession MailChimp Session
     * @param boolean $groups if true groups will be downloaded if they exist
     * @return array associative array with all List containing id, name, groups
     * @since 3.0
     * @deprecated 3.20170201
     */
    public static function MailChimpGetLists($mcSession, $groups = false) {
        return self::GetLists($mcSession, $groups = false);
    }

    /**
     * 
     * @param mixed $mcSession a mailchimp api session
     * @param string $email an email
     * @param string $listid a list id
     * @param boolean $withGroups
     * @return array associative array containing subscribedToList and groupings
     * @since 3.0
     */
    public static function MailChimpGetEmailListSubscription($mcSession, $email, $listid, $withGroups = true) {
        $result = new stdClass();
        if ($mcSession):
            $mcUserid     = self::MailChimpUserID($email);
            $subscription = $mcSession->call("lists/$listid/members/$mcUserid", "GET", array());
            if ($subscription->status == "subscribed"):
                $result->subscribedToList = TRUE;
                if ($withGroups):
                    $groups = array();
                    foreach ($subscription->interests as $k => $v):
                        if ($v):
                            $groups[$k] = $v;
                        endif;
                    endforeach;
                    $result->interests = $groups;
                endif;
            else:
                $result->subscribedToList = FALSE;
            endif;
        endif;
        return $result;
    }

    /**
     * 
     * @param type $mcSession
     * @param type $listid
     * @param type $email
     * @param type $interests as array of interest-id=>boolean
     * @return mixed
     * @since 3.0
     */
    public static function SubscribeToList($mcSession, $listid, $email, $interests = array()) {
        if ($mcSession):
            $subscribe = $mcSession->call("lists/$listid/members/" . self::MailChimpUserID($email), "PUT", array(
                'email_address' => $email,
                'status'        => "subscribed",
                'interests'     => $interests,
            ));
            return $subscribe;
        else:
            error_log("Not mcSession" . print_r($mcSession, true));
        endif;
        return;
    }

    /**
     * @since 3.0
     */
    public static function UnsubscribeFromList($mcSession, $listid, $email) {
        $status=DeMomentSomTresMailchimp::MAILCHIMP_SUCCESS;
        if ($mcSession):
            $unsubscribe = $mcSession->call("lists/$listid/members/" . self::MailChimpUserID($email), "PATCH", array(
                'status'        => "unsubscribed",
            ));
            if(is_array($unsubscribe)):
                $status= DeMomentSomTresMailchimp::MAILCHIMP_ERROR;
            endif;
        endif;
        return $status;
    }

    /**
     * Get MailChimp Templates and sections
     * @param mixed $mcSession MailChimp Session
     * @param string $type filters by type
     * @return array associative array containing id, name for each template
     * @since 3.20170201
     */
    public static function GetTemplates($mcSession, $type = "user") {
        $result = array();
        if ($mcSession):
            $data = array();
            if ($type !== ""):
                $data["type"] = $type;
            endif;
            $data["count"] = 256;
            $temp          = $mcSession->call('templates', "GET", $data);
            if (isset($temp->templates)):
                foreach ($temp->templates as $t):
                    $preresult = array(
                        'id'   => $t->id,
                        'name' => $t->name,
                        "type" => $t->type
                    );
                    $tempsect  = $mcSession->call("templates/$t->id/default-content", "GET");
                    $sections  = array();
                    foreach ($tempsect->sections as $k => $v):
                        $sections[] = $k;
                    endforeach;
                    $preresult["sections"] = $sections;
                    $result[]              = $preresult;
                endforeach;
            endif;
        endif;
        return $result;
    }

    const REGULAR_CAMPAIGN = "regular";

    /**
     * Create a Campaign setting recipients as a list or with an interest group
     * @since 3.20170201
     * @param DeMomentSomTresMailchimp $mcSession
     * @param string $type
     * @param string $listid
     * @param mixed $settings
     * @param int $interestCategory
     * @param int $interest
     * @return boolean/integer 
     */
    public static function CampaignCreate($mcSession, $type, $listid, $settings, $interestCategory = "", $interest = "") {
        if ($mcSession):
            $recipients = array(
                "list_id" => $listid,
            );
            if ($interestCategory !== ""):
                if ($interest === ""):
                    error_log("Campaign creation failed in DeMomentSomTresMailChimp::CreateCampaing. InterestId cannot be null.");
                    return;
                endif;
                $segment_opts               = array(
                    "match"      => "any",
                    "conditions" => array(
                        array(
                            "condition_type" => "Interests",
                            "field"          => "interests-" . $interestCategory,
                            "op"             => "interestcontains",
                            "value"          => array(
                                $interest,
                            )
                        )
                    ),
                );
                $recipients["segment_opts"] = $segment_opts;
            endif;
            $data     = array(
                "type"       => $type,
                "recipients" => $recipients,
                "settings"   => $settings,
            );
            $campaign = $mcSession->call("campaigns", "POST", $data);
            if (is_array($campaign)):
                error_log("Campaign creation failed in DeMomentSomTresMailChimp::CreateCampaing. " . print_r($campaign, true));
                return;
            endif;
            return $campaign->id;
        endif;
        return;
    }

    /**
     * Sets a content to the campaign
     * If template is specified it will set the content to $section
     * Else it sets the content to whole html
     * @param type $mcSession
     * @param type $campaignid
     * @param type $content
     * @param type $template
     * @param type $section
     * @return boolean status
     * @version 3.20170201
     */
    public static function CampaignSetContent($mcSession, $campaignid, $content, $template = "", $section = "") {
        if (!$mcSession):
            return;
        endif;
        if (!$campaignid):
            return;
        endif;
        $data = array();
        if ($template !== ""):
            $datatemplate       = array();
            $datatemplate["id"] = intval($template);
            if ($section !== ""):
                $datatemplate["sections"] = array(
                    $section => $content,
                );
            endif;
            $data["template"] = $datatemplate;
        else:
            $data["html"] = $content;
        endif;
        $answer = $mcSession->call("campaigns/$campaignid/content", "PUT", $data);
        if (is_array($answer)):
            return false;
        endif;
        return true;
    }

    public static function CampaignSend($mcSession, $campaignid) {
        $answer = $mcSession->call("campaigns/$campaignid/actions/send", "POST");
        if (isset($answer["response"]["code"])):
            return ($answer["response"]["code"] == "204");
        else:
            return false;
        endif;
    }
}

?>