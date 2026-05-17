<?php

return [
    'role_structure' => [
        'super_admin' => [
            'doctors' => 'c,r,u,d',
            'parents' => 'c,r,u,d',
            'students' => 'c,r,u,d',
            'subjects' => 'c,r,u,d',
            'levels' => 'c,r,u,d',
            'departments' => 'c,r,u,d',
            'lessons' => 'r',
            'assignments' => 'r',
            'stdassign' => 'r',
            'regist' => 'c,r,u,d',
            'admins' => 'c,r,u,d',
            'users' => 'c,r,u,d',
            'complains' => 'r,u,d',
            'student-problem' => 'r,u,d',
            'doctor-problem' => 'r,u,d',
        ],
        'admin' => [
            'doctors' => 'c,r,u,d',
            'students' => 'c,r,u,d',
            'parents' => 'c,r,u,d',
            'subjects' => 'c,r,u,d',
            'levels' => 'c,r,u,d',
            'departments' => 'c,r,u,d',
            'lessons' => 'r',
            'assignments' => 'r',
            'stdassign' => 'r',
            'regist' => 'c,r,u,d',
            'admins' => 'c,r,u,d',
            'users' => 'c,r,u,d',
            'complains' => 'r,u,d',
            'student-problem' => 'r,u,d',
            'doctor-problem' => 'r,u,d',

        ],
        'doctor' => [
            'subjects' => 'r',
            'lessons' => 'c,r,u,d',
            'assignments' => 'c,r,u,d',
            'stdassign' => 'r',
            'regist' => 'r',
        ],
        'student' => [
            'subjects' => 'r',
            'lessons' => 'r',
            'assignments' => 'r',
            'stdassign' => 'c',
            'regist' => 'r',
        ],
        'parent' => [
            'subjects' => 'r',
            'lessons' => 'r',
            'assignments' => 'r',
            'stdassign' => 'r',
            'regist' => 'r',
        ],

    ],

    'permissions_map' => [
        'c' => 'create',
        'r' => 'read',
        'u' => 'update',
        'd' => 'delete'
    ]
];
