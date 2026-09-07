<?php

namespace Saggre\WordPress\Repository\Model;

/**
 * Change types reported for a path in an SVN log entry.
 */
enum LogPathAction: string
{
    case Added = 'A';
    case Modified = 'M';
    case Deleted = 'D';
    case Replaced = 'R';
}
