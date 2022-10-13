<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use App\Models\Question;
use App\Models\QuestionResponse;
use Twilio\Rest\Client;
use Twilio\TwiML\MessagingResponse;

class TwilioSMSController extends Controller
{
    //
    public function index(Request $request)
    {
        $receiverNumber = "+17473771245";
        // $receiverNumber = "+18585001661";
        $message = "This is testing from ItSolutionStuff.com";

        try {

            $account_sid = getenv("TWILIO_SID");
            $auth_token = getenv("TWILIO_TOKEN");
            $twilio_number = getenv("TWILIO_FROM");

            $client = new Client($account_sid, $auth_token);
            $client->messages->create($receiverNumber, [
                'from' => $twilio_number,
                'body' => $message,
                // 'mediaUrl' => ''
            ]);

            dd('SMS Sent Successfully.');
        } catch (Exception $e) {
            dd("Error: " . $e->getMessage());
        }
    }

    public function responses(Request $request){
        $response = new MessagingResponse();
        $question_id = $request->cookie('question_id');

        if ($question_id == 'deleted') {
            $question_id = null;
        }

        if($question_id !== null) {
            QuestionResponse::create([
                'answer' => $request->input(['Body']),
                'question_id' => $question_id,
                'messages_id'=>$request->input(['MessageSid'])
            ]);

            $next_question = Question::find($question_id + 1);

            if($next_question){
                return $this->nextQuestion($response, $next_question);
            }else{
                $response->message("Thank you for taking the time to complete this survey!");
                return $this->destroy($response);

            }
        } else {
            $response->message(" Thank you for being a customer. Please help us improve our product and our service to you by completing this survey.");

            return $this->firstQuestion($response);
        }
    }

    private function firstQuestion($response){
        $question = Question::orderBy('id', 'ASC')->get()->first();
        $response->message($question->body);
        return response($response)->withCookie(cookie('question_id',$question->id, 60));
    }

    private function nextQuestion($response,$question){
        $response->message($question->body);
        return response($response)->withCookie(cookie('question_id',$question->id, 60));
    }

    private function destroy($response){
        return response($response)->withCookie(\Cookie::forget('question_id'));
    }
}
