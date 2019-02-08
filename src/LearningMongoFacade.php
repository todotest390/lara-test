<?php

namespace Learning\LearningMongo;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Learning\LearningMongo\Skeleton\SkeletonClass
 */
class LearningMongoFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'learning-mongo';
    }
}
