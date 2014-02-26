<?php
    echo json_encode( [
        'intent' => [
            [
                'creatureid' => 1,
                'action' => 'MOVE',
                'direction' => 'NORTH'
            ],
            [
                'creatureid' => 2,
                'action' => 'MOVE',
                'direction' => 'NORTH'
            ],
            [
                'creatureid' => 3,
                'action' => 'MOVE',
                'direction' => 'NORTH'
            ]
        ]
    ] );
?>
