<?php

namespace App\Factories;

use App\Respondents\InvalidKeywordRespondent;

class WHORespondentFactory
{
    protected $respondent;

    public function __construct($phonenumber, $message)
    {
        //save phonenumber
        // $this->saveContact($phonenumber);
        //resolve respondent
        $this->respondent = $this->resolveRespondent($message);
    }

    public static function create()
    {
        $self =  new static(
            request()->input('From'),
            request()->input('Body')
        );

        return $self->respondent;
    }

    public function saveContact($phonenumber)
    {
        //save user's phoneumber
        return User::firstOrCreate([
            "phonenumber" => $phonenumber
        ]);
    }

    protected function normalizeMessage($message)
    {
        //trim and covert message to lowercase
        return trim(strtolower($message));
    }

    public function resolveRespondent($message)
    {
        $message =  $this->normalizeMessage($message);

        $respondents = $this->getRepondents();
        foreach ($respondents as $respondent) {
            if ($respondent::shouldRespond($message)) {
                return new $respondent($message);
            }
        }
        return new InvalidKeywordRespondent($message);
    }

    public function getRepondents()
    {
        return config('who.respondents');
    }
}