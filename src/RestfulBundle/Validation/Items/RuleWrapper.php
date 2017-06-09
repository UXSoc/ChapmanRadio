<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 6/7/17
 * Time: 7:58 PM
 */

namespace RestfulBundle\Validation;


use Carbon\Carbon;
use Recurr\DateExclusion;
use Recurr\Exception;
use Recurr\Rule;
use Symfony\Component\Validator\Constraints as Assert;

class RuleWrapper
{
    /**
     * @Assert\Type(type="Recurr\Rule")
     * @Assert\Valid()
     */
    private $rule;

    /**
     * @Assert\NotBlank()
     * @Assert\Date()
     */
    private $startDate;

    /**
     * @Assert\NotBlank()
     * @Assert\Date()
     */
    private $endDate;

    /**
     * @Assert\NotBlank()
     * @Assert\Time()
     */
    private $startTime;

    /**
     * @Assert\NotBlank()
     * @Assert\Time()
     */
    private $endTime;



    function __construct()
    {
        $this->rule = new Rule();

    }

    public function setRule($rule)
    {
        $this->rule = $rule;
    }

}