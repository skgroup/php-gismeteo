<?php
/**
 * Result.php
 * ----------------------------------------------
 *
 *
 * @author      Stanislav Kiryukhin <korsar.zn@gmail.com>
 * @copyright   Copyright (c) 2014, CKGroup.ru
 *
 * @version    0.0.1
 * ----------------------------------------------
 * All Rights Reserved.
 * ----------------------------------------------
 */
namespace SKGroup\Gismeteo\Result;

/**
 * Class Result
 * @package SKGroup\Gismeteo\Result
 */
class Result
{
    /**
     * @var
     */
    public $exp_time;

    /**
     * @var Location
     */
    public $location;


    /**
     * @var Day[]
     */
    public $day;
} 