<?php

namespace App\Concerns;

trait UseQuickBindings
{
    /**
     * @return static
     */
    public static function i() {
        return app(get_called_class());
    }
}
