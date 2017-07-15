<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 6/7/17
 * Time: 11:42 PM
 */

namespace CoreBundle;


class Caches
{
    const SCHEDULE_EVENTS = "scheduler.events.";
    const SCHEDULE_RULE_CACHE = "schedule.rule.cache.";
    const SCHEDULE_RULE_CACHE_BETWEEN = "schedule.rule.cache.between.";

    const SCHEDULE_RULE_CACHE_CHAIN_ENTRY = "schedule.rule.cache.chain.";
    const SCHEDULE_RULE_CACHE_CHAIN_ENTRY_START = "schedule.rule.cache.chain.start";
}