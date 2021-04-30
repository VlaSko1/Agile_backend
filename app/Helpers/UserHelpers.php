<?php

    /**
     * Return user id for this Blog or Null.
     *
     * 
     * @return user_id;
     */
    function getUserId()
    {
        $user = auth()->user();
        if (is_null($user)) {
            return null;
        }
        return $user_id = $user->id;
    }

    /**
     * Return user id for this Blog or Null.
     *
     * 
     * @return id or null;
     */
    function getUserIdChecked()
    {
        $user = auth()->user();
        
        return $user_id = $user->id;
    }