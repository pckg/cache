<?php

namespace Pckg\Cache\Handler;

use Pckg\Database\Record;

class InvalidateCacheByRecord
{

    /**
     * @T00D00 - Disable caching when multiple records are inserted at once.
     *
     * @param Record $record
     *
     * @throws \Exception
     */
    public function handle(Record $record)
    {
        /**
         * Language $record -> updated, inserted, deleted
         */
        $caches = config('pckg.cache.invalidate.record.' . get_class($record), []);

        /**
         * Skip when nothing gets invalidated.
         */
        if (!$caches) {
            return;
        }

        /**
         * Invalidate all defined keys and rebuild cache.
         */
        $cache = cache();
        foreach ($caches as $key => $rebuild) {
            /**
             * Replace key when rebuild process is not defined.
             */
            if (is_numeric($key)) {
                $key = $rebuild;
            }

            /**
             * Delete cached value.
             */
            $cache->delete($key);

            /**
             * Continue when rebuild is not needed.
             */
            if (!is_only_callable($rebuild)) {
                continue;
            }

            /**
             * Rebuild cache.
             */
            $rebuild();
        }
    }
}
