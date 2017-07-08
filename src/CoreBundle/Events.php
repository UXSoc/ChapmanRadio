<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 6/6/17
 * Time: 1:34 PM
 */

namespace CoreBundle;


class Events
{
    const IMAGE_SAVE = "image.save";
    const IMAGE_DELETE = "image.delete";
    const IMAGE_RETRIEVE = "image.retrieve";
    const IMAGE_FILTER = "image.filter";

    const USER_CONFIRMATION = "user.confirmation";
    const USER_PASSWORD_RESET = "user.password_reset";

    const SHOW_SCHEDULE_CREATE = "show.schedule.create";
    const SHOW_EVENT_DELETE = "show.event.delete";

    const ON_COMMENT_CREATED = "comment.created";

    const ON_SCHEDULE_RULE = "schedule.rule";
}