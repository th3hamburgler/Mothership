<?php

return array(
    'alerts' => [
        'create' => [
            'success' => ':singular <b>:resource</b> updated succesfully.',
            'error'   => 'There was a problem saving the :singular <b>:resource</b>.
                          Please correct errors in the form.',
        ],
        'delete' => [
            'success' => ':singular succesfully <b>deleted</b>.',
            'error'   => 'There was a problem <b>deleting</b> the :singular <b>:resource</b>.
                          Please correct errors in the form.',
        ],
        'massDelete' => [
            'success' => 'Selected :plural succesfully <b>deleted</b>.',
            'empty'   => 'No <b>:plural</b> were selected for deletion.
                          Tick the items you wish to delete and try again.',
            'error'   => 'There was a problem <b>deleting</b> selected :plural.
                          Please try again.',
        ],
        'edit' => [
            'success' => ':singular <b>:resource</b> updated succesfully.',
            'error'   => 'There was a problem saving the :singular <b>:resource</b>.
                          Please correct errors in the form.',
        ],
        'password' => [
            'success' => ':singular <b>:resource</b> password has been changed succesfully.',
            'error'   => 'There was a changing the password of :singular <b>:resource</b>.
                          Please correct errors in the form.',
        ],
    ],
    'title' => [
        'index'    => 'All :plural',
        'upload'   => 'Upload a new :singular.',
        'create'   => 'Create a new :singular.',
        'show'     => 'View :singular: :resource',
        'edit'     => 'Edit :singular: :resource',
        'password' => 'Update password: :resource',
        // related titles
        'rindex'   => 'All :rplural :plural',
        'redit'    => 'Edit :singular: :resource',
    ],
    'caption' => [
        'index' => 'Displaying all :plural',
    ]
);
