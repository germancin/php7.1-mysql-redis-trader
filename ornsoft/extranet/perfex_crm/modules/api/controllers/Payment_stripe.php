<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/** @noinspection PhpIncludeInspection */
require __DIR__.'/REST_Controller.php';

class Payment_stripe extends REST_Controller {

    function __construct()
    {
        parent::__construct();
        $this->load->model('Api_model');
    }

    /**
     * @api {get} api/payment_stripe
     * @apiName listen_actions
     *
     * @apiHeader {String} Authorization Basic Access Authentication token.
     *
     * @apiParam {Number} id Task unique ID.
     *
     * @apiSuccess {Object} Tasks information.
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *         "id": "10",
     *         "name": "Khảo sát chi tiết hiện trạng",
     *         "description": "",
     *         "priority": "2",
     *         "dateadded": "2019-02-25 12:26:37",
     *         "startdate": "2019-01-02 00:00:00",
     *         "duedate": "2019-01-04 00:00:00",
     *         "datefinished": null,
     *         "addedfrom": "9",
     *         "is_added_from_contact": "0",
     *         "status": "4",
     *         "recurring_type": null,
     *         "repeat_every": "0",
     *         "recurring": "0",
     *         "is_recurring_from": null,
     *         ...
     *     }
     *
     * @apiError {Boolean} status Request status.
     * @apiError {String} message No data were found.
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 404 Not Found
     *     {
     *       "status": false,
     *       "message": "No data were found"
     *     }
     */
    public function data_post()
    {
        log_activity(':::STRIPE:::');
        if(empty($_POST)){
            log_activity(':::STRIPE::: ' . ' The request is empty');
            $_POST = json_decode($this->security->xss_clean(file_get_contents("php://input")), true);
            log_activity(':::STRIPE::: ' . 'The POST is' . $_POST);

        }else{
            log_activity(':::STRIPE::: Post is not empty ' . json_encode($_POST, true));
        }

        if($_POST['action'] === 'create_subscription_increment' || $_POST['action'] === 'create_subscription_delete_staff') {
            // create with step the subscription increment.

            $response = $this->createQueueRecord($_POST['action'], $_POST);

            if($response){
                $this->response([
                    'status' => true,
                    'message' => 'Success',
                    'payload' => $_POST,
                    'code' => REST_Controller::HTTP_OK
                ], REST_Controller::HTTP_OK);
            }

            $this->response([
                'status' => false,
                'message' => 'Record was not created at queue.',
                'payload' => $_POST,
                'code' => 500
            ], 500);

        }

        if($_POST['action'] === 'add_stripe_usage') {
            log_activity(':::STRIPE::: ' . ' Got into add_Stripe_usage');
            $CI = &get_instance();
            $rowsTableQueue = $CI->db->where('active', 1)->get('tblqueue_actions');
            $date = new DateTime();

            if ($CI->db->field_exists('action', 'tblqueue_actions')) {
                log_activity(':::STRIPE::: ' . ' Got into tblqueue_actions');

                if (!empty($rowsTableQueue) && $rowsTableQueue->num_rows() == 0) {
                    $this->response(['status' => true, 'message' => 'No records were found to process.', 'payload' => '', 'code' => REST_Controller::HTTP_OK], REST_Controller::HTTP_OK);
                }
                else {
                    $rowsTableQueue = $rowsTableQueue->result_array();
                    log_activity(':::STRIPE::: ' . ' Got into $rowsTableQueue it has records to process ' . json_encode($rowsTableQueue));

                    if(!empty($rowsTableQueue) && is_array($rowsTableQueue) && count($rowsTableQueue) > 0 ){
                        log_activity(':::STRIPE::: ' . ' Got into $rowsTableQueue because is not empty');
                        // here lets do the thing
                        $results = [];
                        foreach ($rowsTableQueue as $queueItem) {
                            log_activity(':::STRIPE::: ' . ' Processing record ' . json_encode($queueItem) );
                            $payload = json_decode($queueItem['payload']);
                            $extranetClientId = (!empty($payload->extranet_client_id))? $payload->extranet_client_id : '';
                            log_activity(':::STRIPE::: ' . ' Extranet Client Id ' . $extranetClientId );
                            $currentStaffAmount = (!empty($payload->current_staff_amount))? $payload->current_staff_amount : 0;
                            log_activity(':::STRIPE::: ' . ' Current Staff Amount ' . $currentStaffAmount );
                            $stripeSubscriptionItemId = null;
                            $subscriptionResponse = null;

                            $extranetClientId = (int) $extranetClientId;
                            if(!empty($extranetClientId) && is_int($extranetClientId) && $extranetClientId > 0 ) {
                                log_activity(':::STRIPE::: ' . ' NOT EMPTY information to continue the process ' . $extranetClientId . ' and ' . $currentStaffAmount );
                                // here need to take subscription response from the custom value table
                                $customFieldSubscriptionResponse = getCustomFieldIdBySlug('customers_subscription_response');
                                log_activity(':::STRIPE::: ' . ' Custom Field Id Response ' . json_encode($customFieldSubscriptionResponse) );

                                if(!empty($customFieldSubscriptionResponse->id)){
                                    $subscriptionResponse = getValueFromCustomField($extranetClientId, $customFieldSubscriptionResponse->id);
                                    log_activity(':::STRIPE::: ' . ' Value from customer subscription response ' . json_encode($subscriptionResponse) );
                                }else{
                                    log_activity(':::STRIPE::: ' . ' IS EMPTY Value from customer subscription response ' );
                                }

                                if(!empty($subscriptionResponse)) {
                                    $subscriptionResponse = json_decode($subscriptionResponse, true);

                                    if(!empty($subscriptionResponse['stripe_subscription_item_id'])){
                                        log_activity(':::STRIPE::: ' . ' GOT Into stripe_subscription_item_id ' . $subscriptionResponse['stripe_subscription_item_id'] );

                                        $stripeSubscriptionItemId = $subscriptionResponse['stripe_subscription_item_id'];
                                        $stripeSubscriptionId = (!empty($subscriptionResponse['stripe_subscription_items']['data'][0]['subscription']))?$subscriptionResponse['stripe_subscription_items']['data'][0]['subscription']:null;

                                        $apiKey = STRIPE_SECRET_KEY;

                                        \Stripe\Stripe::setApiKey($apiKey);

                                        try {
                                            // getting the current usage
                                            $dataToSendSummary = ['subscription_item' => $stripeSubscriptionItemId , 'limit' => 100];
                                            $usageSummary = \Stripe\UsageRecordSummary::get_records($dataToSendSummary);
                                            log_activity(':::STRIPE::: ' . ' Getting usage from user ' . $usageSummary );
                                            $usageSummaryBody = (!empty($usageSummary->body))?json_decode($usageSummary->body):'';
                                            log_activity(':::STRIPE::: ' . ' Getting usage body ' . json_encode($usageSummaryBody) );
                                            $currentUsageNumber = (!empty($usageSummaryBody->data[0]->total_usage))?$usageSummaryBody->data[0]->total_usage:0;
                                            log_activity(':::STRIPE::: ' . ' Current Usage Number ' . $currentUsageNumber);

                                            // Here I have to calculate the amount of the deleted
                                            // have to get the strip subscription date to compare
                                            $deletedAmountStaff = getDeletedStaffAmount($stripeSubscriptionId, $payload);
                                            $currentStaffAmount = $currentStaffAmount + $deletedAmountStaff;

                                            if($currentStaffAmount > $currentUsageNumber){
                                                log_activity(':::STRIPE::: ' . ' CUrrent Staff is greater than stripe usage TRUE');
                                                $dataToSend = [
                                                    'subscription_item' => $stripeSubscriptionItemId ,
                                                    'quantity' => (int) $currentStaffAmount,
                                                    'timestamp' => $date->getTimestamp()
                                                ];

                                                $StripeResponse = \Stripe\UsageRecord::create($dataToSend);
                                                log_activity(':::STRIPE::: ' . ' Stripe response ' . json_encode($StripeResponse));
                                                $r = $CI->db->where('id',$queueItem['id']);
                                                $rows = $r->update('tblqueue_actions', [
                                                    'active' => 0, 'stripe_response' => json_encode($StripeResponse)
                                                ]);

                                                $results[] = "Results: " . json_encode($StripeResponse, true);

                                            }else{
                                                log_activity(':::STRIPE::: ' . ' Current Staff is greater than stripe usage FALSE');
                                                $r = $CI->db->where('id',$queueItem['id']);
                                                $message =  "User maybe deleted users. The amount of users in stripe are $currentUsageNumber and in the system are $currentStaffAmount. We did not create a new usage since the amount of users in stripe are greater than the amount of users in the system";
                                                $r->update('tblqueue_actions', [
                                                    'active' => 3, 'stripe_response' => $message
                                                ]);

                                                $results[] = $message;
                                            }

                                        } catch (Exception $e) {
                                            log_activity(':::STRIPE::: ' . ' Error ' . json_encode($e));
                                            $r = $CI->db->where('id',$queueItem['id']);
                                            $r->update('tblqueue_actions', [
                                                'active' => 2, 'stripe_response' => $e->getMessage()
                                            ]);

                                            $results[] = $e->getCode() . ' ' . $e->getMessage();

                                        }
                                    }
                                }

                            }else{
                                // update the record with error message
                                log_activity(':::STRIPE::: ' . ' Update with error Missing extranet_client_id');
                                $r = $CI->db->where('id',$queueItem['id']);
                                $r->update('tblqueue_actions', [
                                    'active' => 2, 'stripe_response' => 'Missing extranet_client_id'
                                ]);

                            }
                        }

                        $this->response([
                            'status' => true,
                            'message' => 'Success',
                            'payload' => $results,
                            'code' => REST_Controller::HTTP_OK
                        ], REST_Controller::HTTP_OK);

                    }
                }

                $this->response([
                    'status' => true,
                    'message' => 'Success',
                    'payload' => 'tblqueue_actions exist',
                    'code' => REST_Controller::HTTP_OK
                ], REST_Controller::HTTP_OK);

            }
            else{
                $this->response([
                    'status' => false,
                    'message' => 'no field found',
                    'code' => REST_Controller::HTTP_NOT_FOUND
                ], REST_Controller::HTTP_NOT_FOUND);
            }
        }

        if($_POST['action'] === 'list_stripe_usage') {
            $CI = &get_instance();
            $rows = $CI->db->where('active', 1)->get('tblqueue_actions');

            if (!empty($rows) && $rows->num_rows() == 0) {
                $this->response(['status' => true, 'message' => 'No records found to process.', 'payload' => '', 'code' => REST_Controller::HTTP_OK], REST_Controller::HTTP_OK);

            } else {
                $results = $rows->result_array();

                $this->response(['status' => true, 'message' => 'Success', 'payload' => $results, 'code' => REST_Controller::HTTP_OK], REST_Controller::HTTP_OK);
            }
        }

        $this->response([
            'status' => false,
            'message' => 'No action was found.',
            'code' => REST_Controller::HTTP_NOT_FOUND
        ], REST_Controller::HTTP_NOT_FOUND);

    }

    public function createQueueRecord($action, $payload)
    {
        try {
            $CI = &get_instance();
            $payload = (is_array($payload))?json_encode($payload): $payload;

            $CI->db->insert('tblqueue_actions', [
                'action' => $action,
                'payload' => $payload,
                'active' => 1,
                'created_at' => date("Y-m-d H:i:s"),
            ]);

            return true;
        } catch (Exception $e) {
            return false;
        }

        return false;
    }

}
