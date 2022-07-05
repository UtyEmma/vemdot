<?php

namespace App\Traits;
use App\Models\Site\SiteSettings;

trait ReturnTemplate {

    public $paginate = 12;
    public $active = 'active';
    public $pending = 'pending';
    public $suspended = 'suspended';
    public $confirmed = 'confirmed'; // For KYC
    public $declined = 'declined';

    //method that return the messages template
    public function returnMessageTemplate($status = true, $message = '', $payload = [], $other = []) {
        $appSettings = SiteSettings::first();

        return response()
        ->json([
            'status' => $status ? 'success' : 'error',
            'message' => $message,
            'payload' => $payload,
            'additional_data' => $other,
            'site_details' => $appSettings,
        ]);
    }

    //method that return error messages
    public function returnErrorMessage($keyword, $append = '', $prepend = '') {
        $item = $append ?? 'Item';
        $messageArray = [
            'wrong_crendential'=>'Wrong login credentials, Please check you email and password',
            'account_blocked'=>'Your account is blocked, please contact support via live chat for further details',
            'email_not_found'=>'This email is not registerd with us, plases proceed to register an account',
            'refferral_not_found'=>'refferral code not correct, please confirm it and try again',
            'login_reqiured'=>'Email and Password must be provided ',
            'invalid_token'=>'Invalid Token Supplied',
            'token_reqiured'=>'Token must be provided ',
            'expired_token'=>'Token has expired',
            'user_not_found'=>'The requested User was not found',
            'failed_data_returned'=>'Data failed to return',
            'no_data_returned'=>'No Data was returned',
            'no_user'=>'This user is not registerd with us, please select a valid user',
            'unknown_error'=>'An error occurred, please try again',
            'insufficiant_fund'=>'Insufficiant Fund',
            'not_equal_password'=>'The provided password does not match your current password.',
            'not_found' => "$item was not Found",
            'wrong_code' => "You entered a wrong code.",
            'used_code' => "You entered a code that was previously used",
            'meal_availability' => "this meal is not available",
            'paystack_token' => "The paystack token has expired. Please refresh the page and try again.",
            'unable_to_pay' => "This payment is unavailable at moment, please make use of another.",
            'payment_not_complete' => "An error occured!, payment was incomplete",
            'subscription_exist' => "You have previously subscribed for this plan",
            'wrong_accct_number' => "Provided accout number does not match the Bank",
            'kyc_error' => "Your Account has not been verified",
        ];
        return $messageArray[$keyword];
    }

    public function returnSuccessMessage($keyword, $append = '', $prepend = '') {
        $item = $append ?? 'Item';
        $messageArray = [
            'successful_token_creation'=>'Code was successfully created',
            'successful_creation'=>'You request was successfully created',
            'successful_updated'=>'You request was successfully updated',
            'successful_deleted'=>'You request was successfully deleted',
            'successful_declined'=>'You request was successfully declined',
            'data_returned'=>'Data was successfully returned',
            'successful_logout'=>'You have successfully logged out and the token was successfully deleted',
            'successful_login'=>'Login was successful',
            'activation_token_sent'=>'Hi, an account activation mail have been sent to your email address. Please provide the code in the mail in the box below',
            'valid_token'=>'Valid Token',
            '2fa_code_sent'=>'We sent you code on your mobile number.',
            'account_verified'=>'Your account have been successfully verified, please login to continue',
            'account_registered'=>'Your account was created successfully, please login to continue',
            'user_deleted'=>'Selected User(s) was deleted successfully',
            'user_block'=>'Selected User(s) was blocked successfully',
            'user_unblock'=>'Selected User(s) was unblocked successfully',
            'transaction_confirmed'=>'Payment was confrimed successfully',
            'transaction_unconfirmed'=>'Payment was unconfrimed successfully',
            'fund_sent'=>'You fund transfer was successful',
            'mail_sent'=>'Mail was successfully sent',
            'password_reset'=>'You new password is set, navigate to the login page',
            'created' => "$item was created successfully",
            'fetched_single' => "$item was retrieved",
            'fetched_all' => $item."s were retrieved",
            'updated' => "$item updated successfully",
            'deleted' => "$item was deleted successfully"
        ];
        return $messageArray[$keyword];
    }
}
