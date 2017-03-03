<?php


namespace CieloCheckout;

class RecurrentPayment extends Commons
{
    public $Interval;
    public $EndDate;

    public static $Interval_validate = [
        'Monthly' => 'Transações mensais.',
        'Bimonthly' => 'Transações bimestrais.',
        'Quarterly' => 'Transações trimestrais.',
        'SemiAnnual' => 'Transações semestrais.',
        'Annual' => 'Transações anuais.',
    ];

    protected function validate()
    {
        $this->Interval_validate();
        $this->EndDate_validate();
    }

    protected $property_requirements = [
        'Interval' => [
            'empty' => ['negate' => FALSE],
        ],
    ];

    private function Interval_validate()
    {
        if (!isset(self::$Interval_validate[$this->Interval])) {
            throw new \Exception("'Interval == {$this->Interval}' is invalid.");
        }
    }

    private function EndDate_validate()
    {
        if (!empty($this->EndDate)) {
            $matches = [];
            if (!preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $this->EndDate, $matches)) {
                throw new \Exception('Invalid date format. Expected: yyyy-mm-dd');
            }
            $year = $matches[1];
            $month = $matches[2];
            $day = $matches[3];

            if (!checkdate($month, $day, $year)) {
                throw new \Exception('Invalid date');
            }
        }
    }
}
