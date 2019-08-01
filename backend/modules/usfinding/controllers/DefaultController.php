<?php

namespace backend\modules\usfinding\controllers;

use yii\db\Exception;
use yii\db\Query;
use yii\helpers\VarDumper;
use yii\web\Controller;
use yii\web\Response;
use Yii;
use backend\modules\teleradio\classes\QueryData;
use backend\modules\usfinding\classes\QueryUrine;
use backend\modules\usfinding\classes\QuerySummary;

/**
 * THE CONTROLLER ACTION
 */
class DefaultController extends Controller {

    private $sqlCurrentCondition = "";
    private $initUsFinding = [
        'Parenchymal-ECHO' => null,
        'Normal' => [
            [
                'AND', 'f2v2a1', 'LIKE', '0'
            ]
        ],
        'Normal-Liver-mass' => [
            [
                'AND', 'f2v2a1', 'LIKE', '0'
            ], [
                'AND', 'f2v2a2', 'NOT LIKE', '0'
            ], [
                'AND', [
                    [
                        null, 'f2v2a2b5c1', 'LIKE', '1'
                    ], [
                        'OR', 'f2v2a2b6c1', 'LIKE', '1'
                    ], [
                        'OR', 'f2v2a2b7c1', 'LIKE', '1'
                    ]
                ]
            ]
        ],
        'Normal-Liver-mass-None' => [
            [
                'AND', 'f2v2a1', 'LIKE', '0'
            ], [
                'AND', 'f2v2a2', 'LIKE', '0'
            ]
        ],
        'Normal-Liver-mass-Single' => [
            [
                'AND', 'f2v2a1', 'LIKE', '0'
            ], [
                'AND', 'f2v2a2', 'LIKE', '1'
            ], [
                'AND', [
                    [
                        null, 'f2v2a2b5c1', 'LIKE', '1'
                    ], [
                        'OR', 'f2v2a2b6c1', 'LIKE', '1'
                    ], [
                        'OR', 'f2v2a2b7c1', 'LIKE', '1'
                    ]
                ]
            ]
        ],
        'Normal-Liver-mass-Multiple' => [
            [
                'AND', 'f2v2a1', 'LIKE', '0'
            ], [
                'AND', 'f2v2a2', 'LIKE', '2'
            ], [
                'AND', [
                    [
                        null, 'f2v2a2b5c1', 'LIKE', '1'
                    ], [
                        'OR', 'f2v2a2b6c1', 'LIKE', '1'
                    ], [
                        'OR', 'f2v2a2b7c1', 'LIKE', '1'
                    ]
                ]
            ]
        ],
        'Normal-Duct-dilate' => [
            [
                'AND', 'f2v2a1', 'LIKE', '0'
            ], [
                'AND', [
                    [
                        null, 'f2v2a3b1', 'LIKE', '1'
                    ], [
                        'OR', 'f2v2a3b2', 'LIKE', '1'
                    ], [
                        'OR', 'f2v2a3b3', 'LIKE', '1'
                    ]
                ]
            ]
        ],
        'Normal-Duct-dilate-None' => [
            [
                'AND', 'f2v2a1', 'LIKE', '0'
            ], [
                'AND', 'f2v2a3b0', 'LIKE', '1'
            ]
        ],
        'Normal-Duct-dilate-Rt.Lobe' => [
            [
                'AND', 'f2v2a1', 'LIKE', '0'
            ], [
                'AND', 'f2v2a3b1', 'LIKE', '1'
            ]
        ],
        'Normal-Duct-dilate-Lt.Lobe' => [
            [
                'AND', 'f2v2a1', 'LIKE', '0'
            ], [
                'AND', 'f2v2a3b2', 'LIKE', '1'
            ]
        ],
        'Normal-Duct-dilate-Common' => [
            [
                'AND', 'f2v2a1', 'LIKE', '0'
            ], [
                'AND', 'f2v2a3b3', 'LIKE', '1'
            ]
        ],
        'Abnormal' => [
            [
                'AND', 'f2v2a1', 'LIKE', '1'
            ]
        ],
        'Fatty-liver' => [
            [
                'AND', 'f2v2a1', 'LIKE', '1'
            ], [
                'AND', [
                    [
                        null, 'f2v2a1b1', 'LIKE', '1'
                    ], [
                        'OR', 'f2v2a1b1', 'LIKE', '2'
                    ], [
                        'OR', 'f2v2a1b1', 'LIKE', '3'
                    ]
                ]
            ]
        ],
        'Mild' => [
            [
                'AND', 'f2v2a1', 'LIKE', '1'
            ], [
                'AND', 'f2v2a1b1', 'LIKE', '1'
            ]
        ],
        'Mild-Liver-mass' => [
            [
                'AND', 'f2v2a1', 'LIKE', '1'
            ], [
                'AND', 'f2v2a1b1', 'LIKE', '1'
            ], [
                'AND', 'f2v2a2', 'NOT LIKE', '0'
            ], [
                'AND', [
                    [
                        null, 'f2v2a2b5c1', 'LIKE', '1'
                    ], [
                        'OR', 'f2v2a2b6c1', 'LIKE', '1'
                    ], [
                        'OR', 'f2v2a2b7c1', 'LIKE', '1'
                    ]
                ]
            ]
        ],
        'Mild-Liver-mass-None' => [
            [
                'AND', 'f2v2a1', 'LIKE', '1'
            ], [
                'AND', 'f2v2a1b1', 'LIKE', '1'
            ], [
                'AND', 'f2v2a2', 'LIKE', '0'
            ]
        ],
        'Mild-Liver-mass-Single' => [
            [
                'AND', 'f2v2a1', 'LIKE', '1'
            ], [
                'AND', 'f2v2a1b1', 'LIKE', '1'
            ], [
                'AND', 'f2v2a2', 'LIKE', '1'
            ], [
                'AND', [
                    [
                        null, 'f2v2a2b5c1', 'LIKE', '1'
                    ], [
                        'OR', 'f2v2a2b6c1', 'LIKE', '1'
                    ], [
                        'OR', 'f2v2a2b7c1', 'LIKE', '1'
                    ]
                ]
            ]
        ],
        'Mild-Liver-mass-Multiple' => [
            [
                'AND', 'f2v2a1', 'LIKE', '1'
            ], [
                'AND', 'f2v2a1b1', 'LIKE', '1'
            ], [
                'AND', 'f2v2a2', 'LIKE', '2'
            ], [
                'AND', [
                    [
                        null, 'f2v2a2b5c1', 'LIKE', '1'
                    ], [
                        'OR', 'f2v2a2b6c1', 'LIKE', '1'
                    ], [
                        'OR', 'f2v2a2b7c1', 'LIKE', '1'
                    ]
                ]
            ]
        ],
        'Mild-Duct-dilate' => [
            [
                'AND', 'f2v2a1', 'LIKE', '1'
            ], [
                'AND', 'f2v2a1b1', 'LIKE', '1'
            ], [
                'AND', [
                    [
                        null, 'f2v2a3b1', 'LIKE', '1'
                    ], [
                        'OR', 'f2v2a3b2', 'LIKE', '1'
                    ], [
                        'OR', 'f2v2a3b3', 'LIKE', '1'
                    ]
                ]
            ]
        ],
        'Mild-Duct-dilate-None' => [
            [
                'AND', 'f2v2a1', 'LIKE', '1'
            ], [
                'AND', 'f2v2a1b1', 'LIKE', '1'
            ], [
                'AND', 'f2v2a3b0', 'LIKE', '1'
            ]
        ],
        'Mild-Duct-dilate-Rt.Lobe' => [
            [
                'AND', 'f2v2a1', 'LIKE', '1'
            ], [
                'AND', 'f2v2a1b1', 'LIKE', '1'
            ], [
                'AND', 'f2v2a3b1', 'LIKE', '1'
            ]
        ],
        'Mild-Duct-dilate-Lt.Lobe' => [
            [
                'AND', 'f2v2a1', 'LIKE', '1'
            ], [
                'AND', 'f2v2a1b1', 'LIKE', '1'
            ], [
                'AND', 'f2v2a3b2', 'LIKE', '1'
            ]
        ],
        'Mild-Duct-dilate-Common' => [
            [
                'AND', 'f2v2a1', 'LIKE', '1'
            ], [
                'AND', 'f2v2a1b1', 'LIKE', '1'
            ], [
                'AND', 'f2v2a3b3', 'LIKE', '1'
            ]
        ],
        'Moderate' => [
            [
                'AND', 'f2v2a1', 'LIKE', '1'
            ], [
                'AND', 'f2v2a1b1', 'LIKE', '2'
            ]
        ],
        'Moderate-Liver-mass' => [
            [
                'AND', 'f2v2a1', 'LIKE', '1'
            ], [
                'AND', 'f2v2a1b1', 'LIKE', '2'
            ], [
                'AND', 'f2v2a2', 'NOT LIKE', '0'
            ], [
                'AND', [
                    [
                        null, 'f2v2a2b5c1', 'LIKE', '1'
                    ], [
                        'OR', 'f2v2a2b6c1', 'LIKE', '1'
                    ], [
                        'OR', 'f2v2a2b7c1', 'LIKE', '1'
                    ]
                ]
            ]
        ],
        'Moderate-Liver-mass-None' => [
            [
                'AND', 'f2v2a1', 'LIKE', '1'
            ], [
                'AND', 'f2v2a1b1', 'LIKE', '2'
            ], [
                'AND', 'f2v2a2', 'LIKE', '0'
            ]
        ],
        'Moderate-Liver-mass-Single' => [
            [
                'AND', 'f2v2a1', 'LIKE', '1'
            ], [
                'AND', 'f2v2a1b1', 'LIKE', '2'
            ], [
                'AND', 'f2v2a2', 'LIKE', '1'
            ], [
                'AND', [
                    [
                        null, 'f2v2a2b5c1', 'LIKE', '1'
                    ], [
                        'OR', 'f2v2a2b6c1', 'LIKE', '1'
                    ], [
                        'OR', 'f2v2a2b7c1', 'LIKE', '1'
                    ]
                ]
            ]
        ],
        'Moderate-Liver-mass-Multiple' => [
            [
                'AND', 'f2v2a1', 'LIKE', '1'
            ], [
                'AND', 'f2v2a1b1', 'LIKE', '2'
            ], [
                'AND', 'f2v2a2', 'LIKE', '2'
            ], [
                'AND', [
                    [
                        null, 'f2v2a2b5c1', 'LIKE', '1'
                    ], [
                        'OR', 'f2v2a2b6c1', 'LIKE', '1'
                    ], [
                        'OR', 'f2v2a2b7c1', 'LIKE', '1'
                    ]
                ]
            ]
        ],
        'Moderate-Duct-dilate' => [
            [
                'AND', 'f2v2a1', 'LIKE', '1'
            ], [
                'AND', 'f2v2a1b1', 'LIKE', '2'
            ], [
                'AND', [
                    [
                        null, 'f2v2a3b1', 'LIKE', '1'
                    ], [
                        'OR', 'f2v2a3b2', 'LIKE', '1'
                    ], [
                        'OR', 'f2v2a3b3', 'LIKE', '1'
                    ]
                ]
            ]
        ],
        'Moderate-Duct-dilate-None' => [
            [
                'AND', 'f2v2a1', 'LIKE', '1'
            ], [
                'AND', 'f2v2a1b1', 'LIKE', '2'
            ], [
                'AND', 'f2v2a3b0', 'LIKE', '1'
            ]
        ],
        'Moderate-Duct-dilate-Rt.Lobe' => [
            [
                'AND', 'f2v2a1', 'LIKE', '1'
            ], [
                'AND', 'f2v2a1b1', 'LIKE', '2'
            ], [
                'AND', 'f2v2a3b1', 'LIKE', '1'
            ]
        ],
        'Moderate-Duct-dilate-Lt.Lobe' => [
            [
                'AND', 'f2v2a1', 'LIKE', '1'
            ], [
                'AND', 'f2v2a1b1', 'LIKE', '2'
            ], [
                'AND', 'f2v2a3b2', 'LIKE', '1'
            ]
        ],
        'Moderate-Duct-dilate-Common' => [
            [
                'AND', 'f2v2a1', 'LIKE', '1'
            ], [
                'AND', 'f2v2a1b1', 'LIKE', '2'
            ], [
                'AND', 'f2v2a3b3', 'LIKE', '1'
            ]
        ],
        'Severe' => [
            [
                'AND', 'f2v2a1', 'LIKE', '1'
            ], [
                'AND', 'f2v2a1b1', 'LIKE', '3'
            ]
        ],
        'Severe-Liver-mass' => [
            [
                'AND', 'f2v2a1', 'LIKE', '1'
            ], [
                'AND', 'f2v2a1b1', 'LIKE', '3'
            ], [
                'AND', 'f2v2a2', 'NOT LIKE', '0'
            ], [
                'AND', [
                    [
                        null, 'f2v2a2b5c1', 'LIKE', '1'
                    ], [
                        'OR', 'f2v2a2b6c1', 'LIKE', '1'
                    ], [
                        'OR', 'f2v2a2b7c1', 'LIKE', '1'
                    ]
                ]
            ]
        ],
        'Severe-Liver-mass-None' => [
            [
                'AND', 'f2v2a1', 'LIKE', '1'
            ], [
                'AND', 'f2v2a1b1', 'LIKE', '3'
            ], [
                'AND', 'f2v2a2', 'LIKE', '0'
            ]
        ],
        'Severe-Liver-mass-Single' => [
            [
                'AND', 'f2v2a1', 'LIKE', '1'
            ], [
                'AND', 'f2v2a1b1', 'LIKE', '3'
            ], [
                'AND', 'f2v2a2', 'LIKE', '1'
            ], [
                'AND', [
                    [
                        null, 'f2v2a2b5c1', 'LIKE', '1'
                    ], [
                        'OR', 'f2v2a2b6c1', 'LIKE', '1'
                    ], [
                        'OR', 'f2v2a2b7c1', 'LIKE', '1'
                    ]
                ]
            ]
        ],
        'Severe-Liver-mass-Multiple' => [
            [
                'AND', 'f2v2a1', 'LIKE', '1'
            ], [
                'AND', 'f2v2a1b1', 'LIKE', '3'
            ], [
                'AND', 'f2v2a2', 'LIKE', '2'
            ], [
                'AND', [
                    [
                        null, 'f2v2a2b5c1', 'LIKE', '1'
                    ], [
                        'OR', 'f2v2a2b6c1', 'LIKE', '1'
                    ], [
                        'OR', 'f2v2a2b7c1', 'LIKE', '1'
                    ]
                ]
            ]
        ],
        'Severe-Duct-dilate' => [
            [
                'AND', 'f2v2a1', 'LIKE', '1'
            ], [
                'AND', 'f2v2a1b1', 'LIKE', '3'
            ], [
                'AND', [
                    [
                        null, 'f2v2a3b1', 'LIKE', '1'
                    ], [
                        'OR', 'f2v2a3b2', 'LIKE', '1'
                    ], [
                        'OR', 'f2v2a3b3', 'LIKE', '1'
                    ]
                ]
            ]
        ],
        'Severe-Duct-dilate-None' => [
            [
                'AND', 'f2v2a1', 'LIKE', '1'
            ], [
                'AND', 'f2v2a1b1', 'LIKE', '3'
            ], [
                'AND', 'f2v2a3b0', 'LIKE', '1'
            ]
        ],
        'Severe-Duct-dilate-Rt.Lobe' => [
            [
                'AND', 'f2v2a1', 'LIKE', '1'
            ], [
                'AND', 'f2v2a1b1', 'LIKE', '3'
            ], [
                'AND', 'f2v2a3b1', 'LIKE', '1'
            ]
        ],
        'Severe-Duct-dilate-Lt.Lobe' => [
            [
                'AND', 'f2v2a1', 'LIKE', '1'
            ], [
                'AND', 'f2v2a1b1', 'LIKE', '3'
            ], [
                'AND', 'f2v2a3b2', 'LIKE', '1'
            ]
        ],
        'Severe-Duct-dilate-Common' => [
            [
                'AND', 'f2v2a1', 'LIKE', '1'
            ], [
                'AND', 'f2v2a1b1', 'LIKE', '3'
            ], [
                'AND', 'f2v2a3b3', 'LIKE', '1'
            ]
        ],
        'PDF' => [
            [
                'AND', 'f2v2a1', 'LIKE', '1'
            ], [
                'AND', [
                    [
                        null, 'f2v2a1b2', 'LIKE', '1'
                    ], [
                        'OR', 'f2v2a1b2', 'LIKE', '2'
                    ], [
                        'OR', 'f2v2a1b2', 'LIKE', '3'
                    ]
                ]
            ]
        ],
        'PDF1' => [
            [
                'AND', 'f2v2a1', 'LIKE', '1'
            ], [
                'AND', 'f2v2a1b2', 'LIKE', '1'
            ]
        ],
        'PDF1-Liver-mass' => [
            [
                'AND', 'f2v2a1', 'LIKE', '1'
            ], [
                'AND', 'f2v2a1b2', 'LIKE', '1'
            ], [
                'AND', 'f2v2a2', 'NOT LIKE', '0'
            ], [
                'AND', [
                    [
                        null, 'f2v2a2b5c1', 'LIKE', '1'
                    ], [
                        'OR', 'f2v2a2b6c1', 'LIKE', '1'
                    ], [
                        'OR', 'f2v2a2b7c1', 'LIKE', '1'
                    ]
                ]
            ]
        ],
        'PDF1-Liver-mass-None' => [
            [
                'AND', 'f2v2a1', 'LIKE', '1'
            ], [
                'AND', 'f2v2a1b2', 'LIKE', '1'
            ], [
                'AND', 'f2v2a2', 'LIKE', '0'
            ]
        ],
        'PDF1-Liver-mass-Single' => [
            [
                'AND', 'f2v2a1', 'LIKE', '1'
            ], [
                'AND', 'f2v2a1b2', 'LIKE', '1'
            ], [
                'AND', 'f2v2a2', 'LIKE', '1'
            ], [
                'AND', [
                    [
                        null, 'f2v2a2b5c1', 'LIKE', '1'
                    ], [
                        'OR', 'f2v2a2b6c1', 'LIKE', '1'
                    ], [
                        'OR', 'f2v2a2b7c1', 'LIKE', '1'
                    ]
                ]
            ]
        ],
        'PDF1-Liver-mass-Multiple' => [
            [
                'AND', 'f2v2a1', 'LIKE', '1'
            ], [
                'AND', 'f2v2a1b2', 'LIKE', '1'
            ], [
                'AND', 'f2v2a2', 'LIKE', '2'
            ], [
                'AND', [
                    [
                        null, 'f2v2a2b5c1', 'LIKE', '1'
                    ], [
                        'OR', 'f2v2a2b6c1', 'LIKE', '1'
                    ], [
                        'OR', 'f2v2a2b7c1', 'LIKE', '1'
                    ]
                ]
            ]
        ],
        'PDF1-Duct-dilate' => [
            [
                'AND', 'f2v2a1', 'LIKE', '1'
            ], [
                'AND', 'f2v2a1b2', 'LIKE', '1'
            ], [
                'AND', [
                    [
                        null, 'f2v2a3b1', 'LIKE', '1'
                    ], [
                        'OR', 'f2v2a3b2', 'LIKE', '1'
                    ], [
                        'OR', 'f2v2a3b3', 'LIKE', '1'
                    ]
                ]
            ]
        ],
        'PDF1-Duct-dilate-None' => [
            [
                'AND', 'f2v2a1', 'LIKE', '1'
            ], [
                'AND', 'f2v2a1b2', 'LIKE', '1'
            ], [
                'AND', 'f2v2a3b0', 'LIKE', '1'
            ]
        ],
        'PDF1-Duct-dilate-Rt.Lobe' => [
            [
                'AND', 'f2v2a1', 'LIKE', '1'
            ], [
                'AND', 'f2v2a1b2', 'LIKE', '1'
            ], [
                'AND', 'f2v2a3b1', 'LIKE', '1'
            ]
        ],
        'PDF1-Duct-dilate-Lt.Lobe' => [
            [
                'AND', 'f2v2a1', 'LIKE', '1'
            ], [
                'AND', 'f2v2a1b2', 'LIKE', '1'
            ], [
                'AND', 'f2v2a3b2', 'LIKE', '1'
            ]
        ],
        'PDF1-Duct-dilate-Common' => [
            [
                'AND', 'f2v2a1', 'LIKE', '1'
            ], [
                'AND', 'f2v2a1b2', 'LIKE', '1'
            ], [
                'AND', 'f2v2a3b3', 'LIKE', '1'
            ]
        ],
        'PDF2' => [
            [
                'AND', 'f2v2a1', 'LIKE', '1'
            ], [
                'AND', 'f2v2a1b2', 'LIKE', '2'
            ]
        ],
        'PDF2-Liver-mass' => [
            [
                'AND', 'f2v2a1', 'LIKE', '1'
            ], [
                'AND', 'f2v2a1b2', 'LIKE', '2'
            ], [
                'AND', 'f2v2a2', 'NOT LIKE', '0'
            ], [
                'AND', [
                    [
                        null, 'f2v2a2b5c1', 'LIKE', '1'
                    ], [
                        'OR', 'f2v2a2b6c1', 'LIKE', '1'
                    ], [
                        'OR', 'f2v2a2b7c1', 'LIKE', '1'
                    ]
                ]
            ]
        ],
        'PDF2-Liver-mass-None' => [
            [
                'AND', 'f2v2a1', 'LIKE', '1'
            ], [
                'AND', 'f2v2a1b2', 'LIKE', '2'
            ], [
                'AND', 'f2v2a2', 'LIKE', '0'
            ]
        ],
        'PDF2-Liver-mass-Single' => [
            [
                'AND', 'f2v2a1', 'LIKE', '1'
            ], [
                'AND', 'f2v2a1b2', 'LIKE', '2'
            ], [
                'AND', 'f2v2a2', 'LIKE', '1'
            ], [
                'AND', [
                    [
                        null, 'f2v2a2b5c1', 'LIKE', '1'
                    ], [
                        'OR', 'f2v2a2b6c1', 'LIKE', '1'
                    ], [
                        'OR', 'f2v2a2b7c1', 'LIKE', '1'
                    ]
                ]
            ]
        ],
        'PDF2-Liver-mass-Multiple' => [
            [
                'AND', 'f2v2a1', 'LIKE', '1'
            ], [
                'AND', 'f2v2a1b2', 'LIKE', '2'
            ], [
                'AND', 'f2v2a2', 'LIKE', '2'
            ], [
                'AND', [
                    [
                        null, 'f2v2a2b5c1', 'LIKE', '1'
                    ], [
                        'OR', 'f2v2a2b6c1', 'LIKE', '1'
                    ], [
                        'OR', 'f2v2a2b7c1', 'LIKE', '1'
                    ]
                ]
            ]
        ],
        'PDF2-Duct-dilate' => [
            [
                'AND', 'f2v2a1', 'LIKE', '1'
            ], [
                'AND', 'f2v2a1b2', 'LIKE', '2'
            ], [
                'AND', [
                    [
                        null, 'f2v2a3b1', 'LIKE', '1'
                    ], [
                        'OR', 'f2v2a3b2', 'LIKE', '1'
                    ], [
                        'OR', 'f2v2a3b3', 'LIKE', '1'
                    ]
                ]
            ]
        ],
        'PDF2-Duct-dilate-None' => [
            [
                'AND', 'f2v2a1', 'LIKE', '1'
            ], [
                'AND', 'f2v2a1b2', 'LIKE', '2'
            ], [
                'AND', 'f2v2a3b0', 'LIKE', '1'
            ]
        ],
        'PDF2-Duct-dilate-Rt.Lobe' => [
            [
                'AND', 'f2v2a1', 'LIKE', '1'
            ], [
                'AND', 'f2v2a1b2', 'LIKE', '2'
            ], [
                'AND', 'f2v2a3b1', 'LIKE', '1'
            ]
        ],
        'PDF2-Duct-dilate-Lt.Lobe' => [
            [
                'AND', 'f2v2a1', 'LIKE', '1'
            ], [
                'AND', 'f2v2a1b2', 'LIKE', '2'
            ], [
                'AND', 'f2v2a3b2', 'LIKE', '1'
            ]
        ],
        'PDF2-Duct-dilate-Common' => [
            [
                'AND', 'f2v2a1', 'LIKE', '1'
            ], [
                'AND', 'f2v2a1b2', 'LIKE', '2'
            ], [
                'AND', 'f2v2a3b3', 'LIKE', '1'
            ]
        ],
        'PDF3' => [
            [
                'AND', 'f2v2a1', 'LIKE', '1'
            ], [
                'AND', 'f2v2a1b2', 'LIKE', '3'
            ]
        ],
        'PDF3-Liver-mass' => [
            [
                'AND', 'f2v2a1', 'LIKE', '1'
            ], [
                'AND', 'f2v2a1b2', 'LIKE', '3'
            ], [
                'AND', 'f2v2a2', 'NOT LIKE', '0'
            ], [
                'AND', [
                    [
                        null, 'f2v2a2b5c1', 'LIKE', '1'
                    ], [
                        'OR', 'f2v2a2b6c1', 'LIKE', '1'
                    ], [
                        'OR', 'f2v2a2b7c1', 'LIKE', '1'
                    ]
                ]
            ]
        ],
        'PDF3-Liver-mass-None' => [
            [
                'AND', 'f2v2a1', 'LIKE', '1'
            ], [
                'AND', 'f2v2a1b2', 'LIKE', '3'
            ], [
                'AND', 'f2v2a2', 'LIKE', '0'
            ]
        ],
        'PDF3-Liver-mass-Single' => [
            [
                'AND', 'f2v2a1', 'LIKE', '1'
            ], [
                'AND', 'f2v2a1b2', 'LIKE', '3'
            ], [
                'AND', 'f2v2a2', 'LIKE', '1'
            ], [
                'AND', [
                    [
                        null, 'f2v2a2b5c1', 'LIKE', '1'
                    ], [
                        'OR', 'f2v2a2b6c1', 'LIKE', '1'
                    ], [
                        'OR', 'f2v2a2b7c1', 'LIKE', '1'
                    ]
                ]
            ]
        ],
        'PDF3-Liver-mass-Multiple' => [
            [
                'AND', 'f2v2a1', 'LIKE', '1'
            ], [
                'AND', 'f2v2a1b2', 'LIKE', '3'
            ], [
                'AND', 'f2v2a2', 'LIKE', '2'
            ], [
                'AND', [
                    [
                        null, 'f2v2a2b5c1', 'LIKE', '1'
                    ], [
                        'OR', 'f2v2a2b6c1', 'LIKE', '1'
                    ], [
                        'OR', 'f2v2a2b7c1', 'LIKE', '1'
                    ]
                ]
            ]
        ],
        'PDF3-Duct-dilate' => [
            [
                'AND', 'f2v2a1', 'LIKE', '1'
            ], [
                'AND', 'f2v2a1b2', 'LIKE', '3'
            ], [
                'AND', [
                    [
                        null, 'f2v2a3b1', 'LIKE', '1'
                    ], [
                        'OR', 'f2v2a3b2', 'LIKE', '1'
                    ], [
                        'OR', 'f2v2a3b3', 'LIKE', '1'
                    ]
                ]
            ]
        ],
        'PDF3-Duct-dilate-None' => [
            [
                'AND', 'f2v2a1', 'LIKE', '1'
            ], [
                'AND', 'f2v2a1b2', 'LIKE', '3'
            ], [
                'AND', 'f2v2a3b0', 'LIKE', '1'
            ]
        ],
        'PDF3-Duct-dilate-Rt.Lobe' => [
            [
                'AND', 'f2v2a1', 'LIKE', '1'
            ], [
                'AND', 'f2v2a1b2', 'LIKE', '3'
            ], [
                'AND', 'f2v2a3b1', 'LIKE', '1'
            ]
        ],
        'PDF3-Duct-dilate-Lt.Lobe' => [
            [
                'AND', 'f2v2a1', 'LIKE', '1'
            ], [
                'AND', 'f2v2a1b2', 'LIKE', '3'
            ], [
                'AND', 'f2v2a3b2', 'LIKE', '1'
            ]
        ],
        'PDF3-Duct-dilate-Common' => [
            [
                'AND', 'f2v2a1', 'LIKE', '1'
            ], [
                'AND', 'f2v2a1b2', 'LIKE', '3'
            ], [
                'AND', 'f2v2a3b3', 'LIKE', '1'
            ]
        ],
        'OVUPDF' => [
            [
                'AND', 'f2v2a1', 'LIKE', '1'
            ], [
                'AND', [
                    [
                        null, 'f2v2a1b2', 'LIKE', '1'
                    ], [
                        'OR', 'f2v2a1b2', 'LIKE', '2'
                    ], [
                        'OR', 'f2v2a1b2', 'LIKE', '3'
                    ]
                ],
            ],
        ],
        'OVUUrinePDF' => [
            [
                'AND', 'f2v2a1', 'LIKE', '1'
            ], [
                'AND', [
                    [
                        null, 'f2v2a1b2', 'LIKE', '1'
                    ], [
                        'OR', 'f2v2a1b2', 'LIKE', '2'
                    ], [
                        'OR', 'f2v2a1b2', 'LIKE', '3'
                    ]
                ],
            ],
        ],
        'OVUUrinePDFUrPos' => [
            [
                'AND', 'f2v2a1', 'LIKE', '1'
            ], [
                'AND', [
                    [
                        null, 'f2v2a1b2', 'LIKE', '1'
                    ], [
                        'OR', 'f2v2a1b2', 'LIKE', '2'
                    ], [
                        'OR', 'f2v2a1b2', 'LIKE', '3'
                    ]
                ],
            ],
        ],
        'OVUUrinePDFUrNeg' => [
            [
                'AND', 'f2v2a1', 'LIKE', '1'
            ], [
                'AND', [
                    [
                        null, 'f2v2a1b2', 'LIKE', '1'
                    ], [
                        'OR', 'f2v2a1b2', 'LIKE', '2'
                    ], [
                        'OR', 'f2v2a1b2', 'LIKE', '3'
                    ]
                ],
            ],
        ],
        'OVUSusp' => [
            [
                'AND', 'f2v6a3', 'LIKE', '1'
            ], [
                'AND', 'f2v6a3b1', 'LIKE', '1'
            ],
        ],
        // เพิ่มเงื่อนไข ต่อทีหลัง
        'OVUUrineSusp' => [
            [
                'AND', 'f2v6a3', 'LIKE', '1'
            ], [
                'AND', 'f2v6a3b1', 'LIKE', '1'
            ],
        ],
        'OVUUrineSuspUrPos' => [
            [
                'AND', 'f2v6a3', 'LIKE', '1'
            ], [
                'AND', 'f2v6a3b1', 'LIKE', '1'
            ],
        ],
        'OVUUrineSuspUrNeg' => [
            [
                'AND', 'f2v6a3', 'LIKE', '1'
            ], [
                'AND', 'f2v6a3b1', 'LIKE', '1'
            ],
        ],
        'Cirrhosis' => [
            [
                'AND', 'f2v2a1', 'LIKE', '1'
            ], [
                'AND', 'f2v2a1b3', 'LIKE', '1'
            ]
        ],
        'Cirrhosis-Liver-mass' => [
            [
                'AND', 'f2v2a1', 'LIKE', '1'
            ], [
                'AND', 'f2v2a1b3', 'LIKE', '1'
            ], [
                'AND', 'f2v2a2', 'NOT LIKE', '0'
            ], [
                'AND', [
                    [
                        null, 'f2v2a2b5c1', 'LIKE', '1'
                    ], [
                        'OR', 'f2v2a2b6c1', 'LIKE', '1'
                    ], [
                        'OR', 'f2v2a2b7c1', 'LIKE', '1'
                    ]
                ]
            ]
        ],
        'Cirrhosis-Liver-mass-None' => [
            [
                'AND', 'f2v2a1', 'LIKE', '1'
            ], [
                'AND', 'f2v2a1b3', 'LIKE', '1'
            ], [
                'AND', 'f2v2a2', 'LIKE', '0'
            ]
        ],
        'Cirrhosis-Liver-mass-Single' => [
            [
                'AND', 'f2v2a1', 'LIKE', '1'
            ], [
                'AND', 'f2v2a1b3', 'LIKE', '1'
            ], [
                'AND', 'f2v2a2', 'LIKE', '1'
            ], [
                'AND', [
                    [
                        null, 'f2v2a2b5c1', 'LIKE', '1'
                    ], [
                        'OR', 'f2v2a2b6c1', 'LIKE', '1'
                    ], [
                        'OR', 'f2v2a2b7c1', 'LIKE', '1'
                    ]
                ]
            ]
        ],
        'Cirrhosis-Liver-mass-Multiple' => [
            [
                'AND', 'f2v2a1', 'LIKE', '1'
            ], [
                'AND', 'f2v2a1b3', 'LIKE', '1'
            ], [
                'AND', 'f2v2a2', 'LIKE', '2'
            ], [
                'AND', [
                    [
                        null, 'f2v2a2b5c1', 'LIKE', '1'
                    ], [
                        'OR', 'f2v2a2b6c1', 'LIKE', '1'
                    ], [
                        'OR', 'f2v2a2b7c1', 'LIKE', '1'
                    ]
                ]
            ]
        ],
        'Cirrhosis-Duct-dilate' => [
            [
                'AND', 'f2v2a1', 'LIKE', '1'
            ], [
                'AND', 'f2v2a1b3', 'LIKE', '1'
            ], [
                'AND', [
                    [
                        null, 'f2v2a3b1', 'LIKE', '1'
                    ], [
                        'OR', 'f2v2a3b2', 'LIKE', '1'
                    ], [
                        'OR', 'f2v2a3b3', 'LIKE', '1'
                    ]
                ]
            ]
        ],
        'Cirrhosis-Duct-dilate-None' => [
            [
                'AND', 'f2v2a1', 'LIKE', '1'
            ], [
                'AND', 'f2v2a1b3', 'LIKE', '1'
            ], [
                'AND', 'f2v2a3b0', 'LIKE', '1'
            ]
        ],
        'Cirrhosis-Duct-dilate-Rt.Lobe' => [
            [
                'AND', 'f2v2a1', 'LIKE', '1'
            ], [
                'AND', 'f2v2a1b3', 'LIKE', '1'
            ], [
                'AND', 'f2v2a3b1', 'LIKE', '1'
            ]
        ],
        'Cirrhosis-Duct-dilate-Lt.Lobe' => [
            [
                'AND', 'f2v2a1', 'LIKE', '1'
            ], [
                'AND', 'f2v2a1b3', 'LIKE', '1'
            ], [
                'AND', 'f2v2a3b2', 'LIKE', '1'
            ]
        ],
        'Cirrhosis-Duct-dilate-Common' => [
            [
                'AND', 'f2v2a1', 'LIKE', '1'
            ], [
                'AND', 'f2v2a1b3', 'LIKE', '1'
            ], [
                'AND', 'f2v2a3b3', 'LIKE', '1'
            ]
        ]
    ];
    private $allDataUsFindingReport = array();

    public function actionIndex() {
        $qryProvince = $this->getProvince();
        $qryUsTour = $this->getUsTour();
        $qryZone = $this->getZone();
        $qryUsSite = $this->getUsSite();
        $worklistno = isset($_SESSION['worklistno']) ? $_SESSION['worklistno'] : null;

        $session = \Yii::$app->session;
        $table_us = $session['table_us'];
        $refresh_time = $session['refresh_time'];
        $auto_reload = $session['auto_reload'];

        if ($table_us == '') {
            $table_us = "tb_data_3";
            $_SESSION['table_us'];
        }

        if ($refresh_time == '') {
            $refresh_time = '5';
            $_SESSION['refresh_time'] = $refresh_time;
        }

        $isMonitor = 'false';
        if ($auto_reload == 'true') {
            $isMonitor = 'true';
        }
        $dfUSFinding = DefaultUsfindingSiteValueController::GetDefaultUSFinding();

        return $this->render('index', [
                    'dfUSFinding' => $dfUSFinding,
                    'zone' => $qryZone,
                    'province' => $qryProvince,
                    'usTour' => $qryUsTour,
                    'usSite' => $qryUsSite,
                    'isMonitor' => $isMonitor,
        ]);
    }

    public function actionUsfinding() {
        $qryProvince = $this->getProvince();
        $qryUsTour = $this->getUsTour();
        $qryZone = $this->getZone();
        $qryUsSite = $this->getUsSite();

        $dfUSFinding = DefaultUsfindingSiteValueController::GetDefaultUSFinding();

        return $this->renderAjax('index', [
                    'dfUSFinding' => $dfUSFinding,
                    'zone' => $qryZone,
                    'province' => $qryProvince,
                    'usTour' => $qryUsTour,
                    'usSite' => $qryUsSite
        ]);
    }

    public function actionShowReport() {
        if (0) {
            echo "<pre align='left'>";
            echo "Show Report\n";
            print_r($_GET);
            echo "</pre>";
        }
        //echo Yii::$app->formatter->asDate($_GET[startDate], "php:d/m/Y");

        $startDate = \DateTime::createFromFormat("d/m/Y", $_GET['startDate']); // ::createFromFormat("d/m/Y  H:i:s", '31/01/2015');
        $endDate = \DateTime::createFromFormat("d/m/Y", $_GET['endDate']);
        //echo Yii::$app->formatter->asDate($dateTime, "php:d-m-Y");
        $this->generateSqlShowReport(
                $startDate->format('Y-m-d'), $endDate->format('Y-m-d'), isset($_GET['zoneCode']) ? $_GET['zoneCode'] : null, isset($_GET['provinceCode']) ? $_GET['provinceCode'] : null, isset($_GET['amphurCode']) ? $_GET['amphurCode'] : null, isset($_GET['hospitalCode']) ? $_GET['hospitalCode'] : null
        );
        $this->createRtfFile();
        $this->createBtnExport();
        $this->createSummartGraphic();
        

        $ur = new QueryUrine();
        $ur->checkexdate_start = $startDate->format('Y-m-d');
        $ur->checkexdate_end = $endDate->format('Y-m-d');
        $ur->checkDateImportOV();

        if (strlen($ur->ustime) > 0) {
            echo $this->renderAjax('_urineresult', [
                'ustime' => $ur->ustime,
            ]);
            echo "<br /><br /><br />";
        }
    }

    public function actionShowReportInUsTour() {

        $request = Yii::$app->request;

        if (0) {
            echo "<pre align='left'>";
            print_r($_GET);
            echo "</pre>";
            echo $request->get('times');
        }

        $hsitecodeAndTimes = explode(":", $_GET['hSiteCode']);
        $data = $this->getDateFromUsTour($hsitecodeAndTimes[0], $hsitecodeAndTimes[1]);
        //echo $data[0]['times'];
        if (0) {
            echo "<pre align='left'>";
            var_dump($data);
            echo "</pre>";
        }
        $this->generateSqlShowReport(
                explode(" ", $data[0]['sdate'])[0], explode(" ", $data[0]['edate'])[0], null, $data[0]['provcode'], $data[0]['ampcode'], $hsitecodeAndTimes[0]
        );
//        VarDumper::dump($this->allDataUsFindingReport,10,true);

        $this->createRtfFile();
        $this->createBtnExport();
        $this->createSummartGraphic();

        if (strlen($data[0]['times']) > 0) {
            echo $this->renderAjax('_urineresult', [
                'ustime' => $data[0]['times'],
            ]);
            echo "<br /><br /><br />";
        }
    }

    public function actionShowReportInUsSite() {

        if (0) {
            echo "<pre align='left'>";
            var_dump($_GET);
            echo "</pre>";
        }
        $hsitecodeAndTimes = explode(":", $_GET['hSiteCode']);
        $data = $this->getDateFromUsSite($hsitecodeAndTimes[0]);
        if (0) {
            echo "<pre align='left'>";
            var_dump($data);
            echo "</pre>";
        }
        if (1) {
            $this->generateSqlShowReport(
                    explode(" ", $data[0]['sdate'])[0], explode(" ", $data[0]['edate'])[0], null, $data[0]['provcode'], $data[0]['ampcode'], $hsitecodeAndTimes[0]
            );
            //        VarDumper::dump($this->allDataUsFindingReport,10,true);

            $this->createRtfFile();
            $this->createBtnExport();
            $this->createSummartGraphic();
        }
    }

    public function actionProvince() {
        $qryProvince = $this->getProvince($_GET['zoneCode']);

        echo '<option value="">เลือกจังหวัด</option>';
        foreach ($qryProvince as $item) {
            echo '<option value="' . $item['PROVINCE_CODE'] . '">' . trim($item['PROVINCE_NAME']) . '</option>';
        }
    }

    public function actionAmphur() {
        $qryAmphur = $this->getAmphur($_GET['provinceCode']);

        echo '<option value="">เลือกอำเภอ</option>';
        foreach ($qryAmphur as $item) {
            echo '<option value="' . $item['PROVINCE_CODE'] . $item['AMPHUR_CODE'] . '">' . trim($item['AMPHUR_NAME']) . '</option>';
        }
    }

    public function actionAllHospitalThai() {
        $qryAllHospitalThai = $this->getHospital($_GET['provinceCode'], $_GET['amphurCode']);

        echo '<option value="">เลือกหน่วยบริการ</option>';
        foreach ($qryAllHospitalThai as $item) {
            echo '<option value="' . $item['hcode'] . '">' . trim($item['hcode']) . ' : ' . $item['name'] . '</option>';
        }
    }

    public function actionShowListPatientReport() {

        # US Finding
        $hsitecoderaw = $_GET['hospital'];
        $usTourraw = explode(':', $hsitecoderaw);
        $hsitecode = $usTourraw[0];
        $usTour = [1];
        $checksite = self::Checksite($hsitecode);
        $checkadmin = self::CheckAdmin();
        $checksitemanager = self::CheckSiteManager();
        $countadmin = count($checkadmin);
        $countdata = count($checksite);
        $countsitemanager = count($checksitemanager);

        if (Yii::$app->user->can('sitemanager') == TRUE) {
            //echo "<br /><br /><br /><br />sitemanager";
        } else {
            //echo "<br /><br /><br /><br />No sitemanager";
        }

        if ($countadmin > 0 || $countsitemanager > 0 || Yii::$app->user->can('doctorcascap') == TRUE || Yii::$app->user->can('sitemanager') == TRUE) {
            $allListPatient = $this->getDataListPtReportResult();
            $this->createButtonExportTable();
            $this->createTable($allListPatient);
        } else {
            if ($countdata > 0) {
                $allListPatient = $this->getDataListPtReportResult();
                $this->createButtonExportTable();
                $this->createTable($allListPatient);
            } else {
                echo "ไม่มีหน่วยงานคุณในสัญจรนี้";
                exit();
            }
        }
    }

    public function actionShowListPatientReportResult() {
        $hsitecoderaw = $_GET[hospital];
        $usTourraw = explode(':', $hsitecoderaw);
        $hsitecode = $usTourraw[0];
        $usTour = [1];
        //$user_id=Yii::$app->user->identity->
        $checksite = self::Checksite($hsitecode);
        $checkadmin = self::CheckAdmin();
        $checksitemanager = self::CheckSiteManager();
        $countadmin = count($checkadmin);
        $countdata = count($checksite);
        $countsitemanager = count($checksitemanager);
        if ($countadmin > 0 || $countsitemanager > 0 || Yii::$app->user->can('doctorcascap') == TRUE || Yii::$app->user->can('sitemanager') == TRUE) {
            $allListPatient = $this->getDataListPtReportResult();
            $this->createButtonExportTable();
            $this->createTable($allListPatient);
        } else {

            if ($countdata > 0) {
                $allListPatient = $this->getDataListPtReportResult();
                $this->createButtonExportTable();
                $this->createTable($allListPatient);
            } else {
                echo "ไม่มีหน่วยงานคุณในสัญจรนี้";
                exit();
            }
        }
    }

    public function actionRecordUsPrivilege() {

        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $sitecode = Yii::$app->user->identity->userProfile->sitecode;
        $id = $request->get('id');
        $f2v1 = $request->get('f2v1');
        // check record privilege
        $data = \backend\modules\usfinding\classes\QueryCCA02::getOwnerPatientCCA02($sitecode, $id);
        // return data
        $data[cca02id] = $id;
        $data[f2v1] = $f2v1;
        return $data;
    }

    public function CheckAdmin() {
        $user_id = Yii::$app->user->identity->userProfile->user_id;
        $sqlControl = "SELECT
user_id
FROM
`rbac_auth_assignment`
WHERE
`item_name`
LIKE '%administrator%'
AND user_id='$user_id'
";
        $dataProvider = Yii::$app->db->createCommand($sqlControl)->queryAll();
        return $dataProvider;
    }

    public function CheckSiteManager() {
        $user_id = Yii::$app->user->identity->userProfile->user_id;
        $sqlControl = "SELECT
user_id
FROM
`rbac_auth_assignment`
WHERE
`item_name`
LIKE '%sitemanager%'
AND user_id='$user_id'
";
        $dataProvider = Yii::$app->db->createCommand($sqlControl)->queryAll();
        return $dataProvider;
    }

    public function Checksite($hsitecode) {
        $sitecode = Yii::$app->user->identity->userProfile->sitecode;
        $sqlControl = "SELECT
sitecode,
hsitecode
FROM
`tb_data_3`
WHERE
sitecode='$sitecode'
or
hsitecode='$hsitecode'
    ";
        $dataProvider = Yii::$app->db->createCommand($sqlControl)->queryAll();
        return $dataProvider;
    }

    public function actionGetDetailsUsFinding() {
        $hsitecodeAndTimes = explode(":", $_GET['hSiteCode']);
        $data = $this->getDateFromUsTour($hsitecodeAndTimes[0], $hsitecodeAndTimes[1]);
        echo $data[0]['sdate'] . ',' . $data[0]['edate'] . ',' . $data[0]['provcode'] . ',' . $data[0]['ampcode'] . ',' . $hsitecodeAndTimes[0];
    }

    private function createRtfFile() {
        $strSearch = array("-", " ", ":", "000000");

        $mindStone = "";
        $sizeInitUsFinding = sizeof($this->allDataUsFindingReport['initUsFinding']);
        $i = 0;
        foreach ($this->allDataUsFindingReport['initUsFinding'] as $key => $value) {
            if ($key == "startDate") {
                $mindStone .= "s";
            } else if ($key == "endDate") {
                $mindStone .= "e";
            } else if ($key == "zoneCode") {
                $mindStone .= "z";
            } else if ($key == "provinceCode") {
                $mindStone .= "p";
            } else if ($key == "amphurCode") {
                $mindStone .= "a";
            } else if ($key == "hospitalCode") {
                $mindStone .= "h";
            }
            $mindStone .= str_replace($strSearch, "", $value);
            if ($i < $sizeInitUsFinding - 1) {
                $mindStone .= "_";
            }
            $i += 1;
        }
        $filename = "cca02sum_" . $mindStone . ".doc";
        $file = file_get_contents("usfindingexport/cca_02_diagram_20140310_02.rtf");
        $this->allDataUsFindingReport['initUsFinding']['filename'] = $filename;

        $file = str_replace("(n=x1)", "(n=" . $this->allDataUsFindingReport['Parenchymal-ECHO']['count'] . ")", $file);
        $file = str_replace("(n=x2)", "(n=" . $this->allDataUsFindingReport['Normal']['count'] . ")", $file);
        $file = str_replace("(n=x3)", "n=" . $this->allDataUsFindingReport['Abnormal']['count'] . "", $file);
        if ($this->allDataUsFindingReport['Parenchymal-ECHO']['count'] != 0) {
            $percent = round((str_replace(',', '', $this->allDataUsFindingReport['Abnormal']['count']) / str_replace(',', '', $this->allDataUsFindingReport['Parenchymal-ECHO']['count'])) * 100, 1);
        } else {
            $percent = 0;
        }

        $file = str_replace("(n=x3p)", "(" . $percent . "%)", $file);
//        $file=str_replace("(n=x4)","(n=".number_format($data['x4'],0,',','.').")",$file);
        $file = str_replace("n=x5", "n=" . $this->allDataUsFindingReport['Fatty-liver']['count'] . "", $file);
        $file = str_replace("n=x6", "n=" . $this->allDataUsFindingReport['PDF']['count'] . "", $file);
        $file = str_replace("n=x7", "n=" . $this->allDataUsFindingReport['Cirrhosis']['count'] . "", $file);
        $file = str_replace("n=x8", "n=" . $this->allDataUsFindingReport['Mild']['count'] . "", $file);
        $file = str_replace("n=x9", "n=" . $this->allDataUsFindingReport['Moderate']['count'] . "", $file);
        $file = str_replace("n=x10", "n=" . $this->allDataUsFindingReport['Severe']['count'] . "", $file);
        $file = str_replace("n=x11", "n=" . $this->allDataUsFindingReport['PDF1']['count'] . "", $file);
        $file = str_replace("n=x12", "n=" . $this->allDataUsFindingReport['PDF2']['count'] . "", $file);
        $file = str_replace("n=x13", "n=" . $this->allDataUsFindingReport['PDF3']['count'] . "", $file);
        $file = str_replace("n=x14", "n=" . $this->allDataUsFindingReport['Normal-Liver-mass']['count'] . "", $file);
        $file = str_replace("n=x15", "n=" . $this->allDataUsFindingReport['Mild-Liver-mass']['count'] . "", $file);
        $file = str_replace("n=x16", "n=" . $this->allDataUsFindingReport['Moderate-Liver-mass']['count'] . "", $file);
        $file = str_replace("n=x17", "n=" . $this->allDataUsFindingReport['Severe-Liver-mass']['count'] . "", $file);
        $file = str_replace("n=x18", "n=" . $this->allDataUsFindingReport['PDF1-Liver-mass']['count'] . "", $file);
        $file = str_replace("n=x19", "n=" . $this->allDataUsFindingReport['PDF2-Liver-mass']['count'] . "", $file);
        $file = str_replace("n=x20", "n=" . $this->allDataUsFindingReport['PDF3-Liver-mass']['count'] . "", $file);
        $file = str_replace("n=x21", "n=" . $this->allDataUsFindingReport['Cirrhosis-Liver-mass']['count'] . "", $file);
        $file = str_replace("n=x22", "n=" . $this->allDataUsFindingReport['Normal-Duct-dilate']['count'] . "", $file);
        $file = str_replace("n=x23", "n=" . $this->allDataUsFindingReport['Mild-Duct-dilate']['count'] . "", $file);
        $file = str_replace("n=x24", "n=" . $this->allDataUsFindingReport['Moderate-Duct-dilate']['count'] . "", $file);
        $file = str_replace("n=x25", "n=" . $this->allDataUsFindingReport['Severe-Duct-dilate']['count'] . "", $file);
        $file = str_replace("n=x26", "n=" . $this->allDataUsFindingReport['PDF1-Duct-dilate']['count'] . "", $file);
        $file = str_replace("n=x27", "n=" . $this->allDataUsFindingReport['PDF2-Duct-dilate']['count'] . "", $file);
        $file = str_replace("n=x28", "n=" . $this->allDataUsFindingReport['PDF3-Duct-dilate']['count'] . "", $file);
        $file = str_replace("n=x29", "n=" . $this->allDataUsFindingReport['Cirrhosis-Duct-dilate']['count'] . "", $file);
        $file = str_replace("x30", "" . $this->allDataUsFindingReport['Normal-Liver-mass-None']['count'] . "", $file);
        $file = str_replace("x31", "" . $this->allDataUsFindingReport['Normal-Liver-mass-Single']['count'] . "", $file);
        $file = str_replace("x32", "" . $this->allDataUsFindingReport['Normal-Liver-mass-Multiple']['count'] . "", $file);
        $file = str_replace("x33", "" . $this->allDataUsFindingReport['Mild-Liver-mass-None']['count'] . "", $file);
        $file = str_replace("x34", "" . $this->allDataUsFindingReport['Mild-Liver-mass-Single']['count'] . "", $file);
        $file = str_replace("x35", "" . $this->allDataUsFindingReport['Mild-Liver-mass-Multiple']['count'] . "", $file);
        $file = str_replace("x36", "" . $this->allDataUsFindingReport['Moderate-Liver-mass-None']['count'] . "", $file);
        $file = str_replace("x37", "" . $this->allDataUsFindingReport['Moderate-Liver-mass-Single']['count'] . "", $file);
        $file = str_replace("x38", "" . $this->allDataUsFindingReport['Moderate-Liver-mass-Multiple']['count'] . "", $file);
        $file = str_replace("x39", "" . $this->allDataUsFindingReport['Severe-Liver-mass-None']['count'] . "", $file);
        $file = str_replace("x40", "" . $this->allDataUsFindingReport['Severe-Liver-mass-Single']['count'] . "", $file);
        $file = str_replace("x41", "" . $this->allDataUsFindingReport['Severe-Liver-mass-Multiple']['count'] . "", $file);
        $file = str_replace("x42", "" . $this->allDataUsFindingReport['PDF1-Liver-mass-None']['count'] . "", $file);
        $file = str_replace("x43", "" . $this->allDataUsFindingReport['PDF1-Liver-mass-Single']['count'] . "", $file);
        $file = str_replace("x44", "" . $this->allDataUsFindingReport['PDF1-Liver-mass-Multiple']['count'] . "", $file);
        $file = str_replace("x45", "" . $this->allDataUsFindingReport['PDF2-Liver-mass-None']['count'] . "", $file);
        $file = str_replace("x46", "" . $this->allDataUsFindingReport['PDF2-Liver-mass-Single']['count'] . "", $file);
        $file = str_replace("x47", "" . $this->allDataUsFindingReport['PDF2-Liver-mass-Multiple']['count'] . "", $file);
        $file = str_replace("x48", "" . $this->allDataUsFindingReport['PDF3-Liver-mass-None']['count'] . "", $file);
        $file = str_replace("x49", "" . $this->allDataUsFindingReport['PDF3-Liver-mass-Single']['count'] . "", $file);
        $file = str_replace("x50", "" . $this->allDataUsFindingReport['PDF3-Liver-mass-Multiple']['count'] . "", $file);
        $file = str_replace("x51", "" . $this->allDataUsFindingReport['Cirrhosis-Liver-mass-None']['count'] . "", $file);
        $file = str_replace("x52", "" . $this->allDataUsFindingReport['Cirrhosis-Liver-mass-Single']['count'] . "", $file);
        $file = str_replace("x53", "" . $this->allDataUsFindingReport['Cirrhosis-Liver-mass-Multiple']['count'] . "", $file);
        $file = str_replace("x54", "" . $this->allDataUsFindingReport['Normal-Duct-dilate-None']['count'] . "", $file);
        $file = str_replace("x55", "" . $this->allDataUsFindingReport['Normal-Duct-dilate-Rt.Lobe']['count'] . "", $file);
        if (0) {
            $file = str_replace("x56", "" . $this->allDataUsFindingReport['Normal-Duct-dilate-Lt.Lobe']['count'] . "", $file);
        }
        $file = str_replace("x57", "" . $this->allDataUsFindingReport['Normal-Duct-dilate-Common']['count'] . "", $file);
        $file = str_replace("x58", "" . $this->allDataUsFindingReport['Mild-Duct-dilate-None']['count'] . "", $file);
        $file = str_replace("x59", "" . $this->allDataUsFindingReport['Mild-Duct-dilate-Rt.Lobe']['count'] . "", $file);
        $file = str_replace("x60", "" . $this->allDataUsFindingReport['Mild-Duct-dilate-Lt.Lobe']['count'] . "", $file);
        $file = str_replace("x61", "" . $this->allDataUsFindingReport['Mild-Duct-dilate-Common']['count'] . "", $file);
        $file = str_replace("x62", "" . $this->allDataUsFindingReport['Moderate-Duct-dilate-None']['count'] . "", $file);
        $file = str_replace("x63", "" . $this->allDataUsFindingReport['Moderate-Duct-dilate-Rt.Lobe']['count'] . "", $file);
        $file = str_replace("x64", "" . $this->allDataUsFindingReport['Moderate-Duct-dilate-Lt.Lobe']['count'] . "", $file);
        $file = str_replace("x65", "" . $this->allDataUsFindingReport['Moderate-Duct-dilate-Common']['count'] . "", $file);
        $file = str_replace("x66", "" . $this->allDataUsFindingReport['Severe-Duct-dilate-None']['count'] . "", $file);
        $file = str_replace("x67", "" . $this->allDataUsFindingReport['Severe-Duct-dilate-Rt.Lobe']['count'] . "", $file);
        $file = str_replace("x68", "" . $this->allDataUsFindingReport['Severe-Duct-dilate-Lt.Lobe']['count'] . "", $file);
        $file = str_replace("x69", "" . $this->allDataUsFindingReport['Severe-Duct-dilate-Common']['count'] . "", $file);
        $file = str_replace("x70", "" . $this->allDataUsFindingReport['PDF1-Duct-dilate-None']['count'] . "", $file);
        $file = str_replace("x71", "" . $this->allDataUsFindingReport['PDF1-Duct-dilate-Rt.Lobe']['count'] . "", $file);
        $file = str_replace("x72", "" . $this->allDataUsFindingReport['PDF1-Duct-dilate-Lt.Lobe']['count'] . "", $file);
        $file = str_replace("x73", "" . $this->allDataUsFindingReport['PDF1-Duct-dilate-Common']['count'] . "", $file);
        $file = str_replace("x74", "" . $this->allDataUsFindingReport['PDF2-Duct-dilate-None']['count'] . "", $file);
        $file = str_replace("x75", "" . $this->allDataUsFindingReport['PDF2-Duct-dilate-Rt.Lobe']['count'] . "", $file);
        $file = str_replace("x76", "" . $this->allDataUsFindingReport['PDF2-Duct-dilate-Lt.Lobe']['count'] . "", $file);
        $file = str_replace("x77", "" . $this->allDataUsFindingReport['PDF2-Duct-dilate-Common']['count'] . "", $file);
        $file = str_replace("x78", "" . $this->allDataUsFindingReport['PDF3-Duct-dilate-None']['count'] . "", $file);
        $file = str_replace("x79", "" . $this->allDataUsFindingReport['PDF3-Duct-dilate-Rt.Lobe']['count'] . "", $file);
        $file = str_replace("x80", "" . $this->allDataUsFindingReport['PDF3-Duct-dilate-Lt.Lobe']['count'] . "", $file);
        $file = str_replace("x81", "" . $this->allDataUsFindingReport['PDF3-Duct-dilate-Common']['count'] . "", $file);
        $file = str_replace("x82", "" . $this->allDataUsFindingReport['Cirrhosis-Duct-dilate-None']['count'] . "", $file);
        $file = str_replace("x83", "" . $this->allDataUsFindingReport['Cirrhosis-Duct-dilate-Rt.Lobe']['count'] . "", $file);
        $file = str_replace("x84", "" . $this->allDataUsFindingReport['Cirrhosis-Duct-dilate-Lt.Lobe']['count'] . "", $file);
        $file = str_replace("x85", "" . $this->allDataUsFindingReport['Cirrhosis-Duct-dilate-Common']['count'] . "", $file);
        $file = str_replace("x86", "" . $this->allDataUsFindingReport['Normal-Duct-dilate-Lt.Lobe']['count'] . "", $file);

        file_put_contents("usfindingexport/resultdocfile/" . $filename, $file);
    }

    private function createBtnExport() {
        echo '<div class="row ExportGraphWord">' .
        '<div class="col-md-9">' .
        '</div>' .
        '<div class="col-md-3">' .
        '<p class="text-right"><a href="' . '/usfindingexport/resultdocfile/' . $this->allDataUsFindingReport['initUsFinding']['filename'] . '">' .
        '<button class="btn btn-info form-control exportToDoc" id="exportToDoc" >' .
        '<i class="glyphicon glyphicon-save-file"></i> Export to word' .
        '</button>' .
        '</a></p>' .
        '</div>' .
        '</div>';
    }

    private function createSummartGraphic() {
        $imgPath = '/img/CCA_02_Diagram_BT.png';
        echo '<div class="summaryOfUltrasonoGraphicFinding" id="summaryOfUltrasonoGraphicFinding" style="background-image: url(' . $imgPath . '); background-size:initial; background-repeat: no-repeat; height: 1050px;">';
        //echo '<img class="bgUSFinding" id="bgUSFinding" width="1473" height="1042" src="img/CCA_02_Diagram_BT.png">';
        $stageNotShow[] = 'OVUPDF';
        foreach ($this->initUsFinding as $key => $value) {
            if (str_replace('OVU', '', $key) != $key) {
                // ขึ้นต้นด้วย OVU ไม่ต้องแสดงใน graph
            } else if (!in_array($key, $stageNotShow)) {
                $newKey = substr(str_replace("-", "", $key), 0, 20);
                $sql = "SELECT `stage`,`top`,`left` " .
                        "FROM `cascap_data`.`diagram_overall_position` " .
                        "WHERE `stage` LIKE '%$newKey%' ";
                $qryTopLeft = Yii::$app->db->createCommand($sql)->queryAll();

                $hospital = isset($this->allDataUsFindingReport['initUsFinding']['hospitalCode']) ? $this->allDataUsFindingReport['initUsFinding']['hospitalCode'] : "";
                $startDate = $this->allDataUsFindingReport['initUsFinding']['startDate'];
                $endDate = $this->allDataUsFindingReport['initUsFinding']['endDate'];
                $zone = isset($this->allDataUsFindingReport['initUsFinding']['zoneCode']) ? $this->allDataUsFindingReport['initUsFinding']['zoneCode'] : "";
                $province = isset($this->allDataUsFindingReport['initUsFinding']['provinceCode']) ? $this->allDataUsFindingReport['initUsFinding']['provinceCode'] : "";
                $amphur = isset($this->allDataUsFindingReport['initUsFinding']['amphurCode']) ? $this->allDataUsFindingReport['initUsFinding']['amphurCode'] : "";


                echo '<div class="valueReportUSFinding" style="cursor: pointer; left: ' . $qryTopLeft[0]['left'] . 'px;top: ' . $qryTopLeft[0]['top'] . 'px; background: rgba(255, 255, 255, 0.3);" divReportUSFinding keyStore="' . $key . '" zone="' . $zone . '" Hospital="' . $hospital . '" startDate="' . $startDate . '" endDate="' . $endDate . '" province="' . $province . '" amphur="' . $amphur . '">';
                echo '<a id="valueReportUSFindingA" class="blue17bold">';
                echo '<p id="valueReportUSFindingP" class="text-center">';
                if (strpos($key, "None") >= 1 ||
                        strpos($key, "Single") >= 1 ||
                        strpos($key, "Multiple") >= 1 ||
                        strpos($key, "Rt.Lobe") >= 1 ||
                        strpos($key, "Lt.Lobe") >= 1 ||
                        strpos($key, "Common") >= 1) {
                    if (($this->allDataUsFindingReport[$key]['count'] > 0) && !(strpos($key, "None") >= 1)) {
                        echo "<span class='underline danger'>" . $this->allDataUsFindingReport[$key]['count'] . "</span>";
                    } else {
                        echo "" . $this->allDataUsFindingReport[$key]['count'] . "";
                    }
                } else {
                    if ($key == 'Abnormal' || $key == 'Fatty-liver' || $key == 'PDF' || $key == 'PDF1' || $key == 'PDF2' || $key == 'PDF3' || $key == 'Cirrhosis') {
                        $allCount = $this->allDataUsFindingReport["Parenchymal-ECHO"]["count"];
                        $countAbnormal = $this->allDataUsFindingReport[$key]['count'];
                        if ($allCount != 0) {
                            $percentAbNormal = round((str_replace(',', '', $countAbnormal) / str_replace(',', '', $allCount)) * 100, 1);
                        } else {
                            $percentAbNormal = 0;
                        }

                        echo "n=" . $countAbnormal . " <span class='danger'>(" . $percentAbNormal . "%)</span>";
                    } else {
                        echo "(n=" . $this->allDataUsFindingReport[$key]['count'] . ")";
                    }
                }
                echo '</p>';
                echo '</a>';
                echo '</div>';
            }
        }
        echo '</div>';
//        VarDumper::dump($this->allDataUsFindingReport,10,true);
        $this->getResultUsFinging();
    }

    private function createButtonExportTable() {
        $keystore = $_GET['keystore'];
        $startdate = $_GET['startdate'];
        //echo $startdate;
        $enddate = $_GET['enddate'];
        $zone = $_GET['zone'];
        $province = $_GET['province'];
        $amphur = $_GET['amphur'];
        $hospital = $_GET['hospital'];
//        echo $keystore." : ".$startdate." : ".$enddate." : ".$zone." : ".$province." : ".$amphur." : ".$hospital;

        echo '<div class="row ExportExcelListData">' .
        '<div class="col-md-9">' .
        '</div>' .
        '<div class="col-md-3">' .
        '<p class="text-right">' .
        '<a class="btn btn-info form-control exportToExcel" id="exportToExcel" keystore="' . $keystore . '" startdate="' . $startdate . '" enddate="' . $enddate . '" zone="' . $zone . '" province="' . $province . '" amphur="' . $amphur . '" hospital="' . $hospital . '">' .
        '<i class="glyphicon glyphicon-save-file"></i> Export to excel' .
        '</a>' .
        '</p>' .
        '</div>' .
        '</div>';
    }

    private function createTable($allListPatient) {
        $hospital = $_GET['hospital'];
        $keystore = $_GET['keystore'];
        $siteCode = Yii::$app->user->identity->userProfile->sitecode;

        if (str_replace('OVU', '', $keystore) != $keystore) {
            $theaderOV = '<td rowspan="2" valign="top" align="right" style="min-width:60px;">OV</td>';
        }

        echo '<div id="tableClone"></div>';
        echo '<div class="table-responsive table-listPatiant" id="table-listPatiant">';
        echo \yii\helpers\Html::hiddenInput('last-id', '', ['id' => 'last-id', 'last-id' => '', 'data-f2v1' => '', 'data-id' => '']);
        echo '<table class="table table-hover table-bordered table-striped" id="table-striped" width="100%">';
        echo '<thead>
                <tr bgcolor="#94b5ff">' .
//                    '<td rowspan="2" valign="top" align="right">Test</td>'.
        '<td rowspan="2" valign="top" align="right" style="min-width:60px;">No.</td>
                    <td rowspan="2" valign="top" align="right" style="min-width:100px;">HOSPCODE</td>
                    <td rowspan="2" valign="top" align="right" style="min-width:110px;">Paticipant ID</td>' .
        (
        ((Yii::$app->user->can('adminsite') == '1') || (Yii::$app->user->can('sitemanager') == true) || ($hospital == $siteCode)) ?
                '<td rowspan="2" valign="top" align="center" style="min-width:90px;">HN</td>' .
                '<td rowspan="2" valign="top" align="center" style="min-width:160px;">Name</td>' :
                ""
        ) .
        '<td rowspan="2" valign="top" align="right" style="min-width:100px;">Date visit</td>
                    <td rowspan="2" valign="top" align="right" style="min-width:90px;">U/S</td>
                    <td rowspan="2" valign="top" align="right" style="min-width:170px;">Abnormal</td>' . $theaderOV . '
                    <td colspan="8" valign="top" align="center">Mass</td>
                    <td colspan="4" valign="top" align="center">Duct dilate</td>
                    <td rowspan="2" valign="top" align="right" style="min-width:100px;">นัด</td>
                    <td colspan="2" valign="top" align="right">ส่งรักษาต่อ</td>
                    <td rowspan="2" valign="top" align="right" style="min-width:100px;">CCA-02.1</td>
                </tr>
                <tr bgcolor="#94b5ff">
                    <td valign="top" align="center" style="min-width:80px;">    </td>
                    <td valign="top" align="center" style="min-width:60px;">Cyst</td>
                    <td valign="top" align="center" style="min-width:80px;">Hemang</td>
                    <td valign="top" align="center" style="min-width:60px;">Cal</td>
                    <td valign="top" align="center" style="min-width:60px;">Intra</td>
                    <td valign="top" align="center" style="min-width:60px;">High</td>
                    <td valign="top" align="center" style="min-width:60px;">Low</td>
                    <td valign="top" align="center" style="min-width:60px;">Mixed</td>
                    <td valign="top" align="center" style="min-width:60px;">None</td>
                    <td valign="top" align="center" style="min-width:60px;">Rt.</td>
                    <td valign="top" align="center" style="min-width:60px;">Lt.</td>
                    <td valign="top" align="center" style="min-width:80px;">Common</td>
                    <td valign="top" align="center" style="min-width:60px;">ส่งต่อ</td>
                    <td valign="top" align="center" style="min-width:60px;">verify</td>
                </tr>
              </thead></tbody>';
        $countList = 1;
//        $tbodyExport='';
        foreach ($allListPatient as $listPatient) {
            $hn = $listPatient['hn'];
            $hncode = $listPatient['hncode'];
            $title = $listPatient['title'];
            $name = $listPatient['name'];
            $surname = $listPatient['surname'];
            $cid = $listPatient['cid'];
            $mobile = $listPatient['mobile'];

            $rowId = $listPatient['id'];
            $hsitecode = $listPatient['hsitecode'];
            $sitecode = $listPatient['sitecode'];
            $ptcodefull = $listPatient['ptcodefull'];
            $ptid = $listPatient['ptid'];
            $usImage = '<img id="usimage" src="https://tools.cascap.in.th/api/us/imglist.php?ptid=' . $ptid . '&id=' . $rowId . '" height="30" style="vertical-align: text-top;">';
            $hptcode = $listPatient['hptcode'];

            $dateVisitFull = $listPatient['f2v1'];
            $spitDateAndTime = explode(" ", $dateVisitFull);
            $spltDate = explode("-", $spitDateAndTime[0]);
            $dateVisit = $spltDate[2] . "/" . $spltDate[1] . "/" . $spltDate[0];

            $parenchymalECHO = $listPatient['f2v2a1'];
            $fattyliver = $listPatient['f2v2a1b1'];
            $periductalFibrosis = $listPatient['f2v2a1b2'];
            $cirrhosis = $listPatient['f2v2a1b3'];
            $parenchymalChange = $listPatient['f2v2a1b4'];
            $liverMass = $listPatient['f2v2a2'];
            $cyst = $listPatient['f2v2a2b1c1'];
            $hemang = $listPatient['f2v2a2b2c1'];
            $cal = $listPatient['f2v2a2b3c1'];
            $intra = $listPatient['f2v2a2b4c1'];
            $high = $listPatient['f2v2a2b5c1'];
            $low = $listPatient['f2v2a2b6c1'];
            $mixed = $listPatient['f2v2a2b7c1'];
            $none = $listPatient['f2v2a3b0'];
            $rt = $listPatient['f2v2a3b1'];
            $lt = $listPatient['f2v2a3b2'];
            $common = $listPatient['f2v2a3b3'];
            $appointment = $listPatient['f2v6'];
            $send = $listPatient['f2v6a3'];
            $CTMRI = $listPatient['f2p1v2'];
            $finding = $listPatient['f2p1v3'];

            $abNormal = '';
            if (($fattyliver != '') && !(is_null($fattyliver))) {
                if ($fattyliver == '1')
                    $abNormal .= "Mild Fatty";
                else if ($fattyliver == '2')
                    $abNormal .= "Mode Fatty";
                else
                    $abNormal .= "Seve Fatty";
            }

            if (($periductalFibrosis != '') && !(is_null($periductalFibrosis))) {
                if (($fattyliver != '') && !(is_null($fattyliver)))
                    $abNormal .= ", ";

                if ($periductalFibrosis == '1')
                    $abNormal .= "PDF1";
                else if ($periductalFibrosis == '2')
                    $abNormal .= "PDF2";
                else
                    $abNormal .= "PDF3";
            }

            // OV และ PDF
            $tbdataOV = '';
            if (str_replace('OV', '', $keystore) != $keystore) {
                $tbdataOV = '<td align="right" valign="top"></td>';
                if ($listPatient['urine_result'] == '1') {
                    $tbdataOV = '<td align="right" valign="top">Pos(+)</td>';
                } else if ($listPatient['urine_result'] == '0') {
                    $tbdataOV = '<td align="right" valign="top">Neg(-)</td>';
                } else {
                    $tbdataOV = '<td align="right" valign="top"></td>';
                }
            }

            if ($cirrhosis == '1') {
                if (($fattyliver != '') && !(is_null($fattyliver)) ||
                        ($periductalFibrosis != '') && !(is_null($periductalFibrosis)))
                    $abNormal .= ", ";
                $abNormal .= "Cirrhosis";
            }

            if ($parenchymalChange == '1') {
                if (($fattyliver != '') && !(is_null($fattyliver)) ||
                        ($periductalFibrosis != '') && !(is_null($periductalFibrosis)) ||
                        $cirrhosis == '1')
                    $abNormal .= ", ";
                $abNormal .= "Parenchymal Change";
            }


            $aTagOpen = "";
            $aTagClose = "";
            if ((Yii::$app->user->can('adminsite') == '1') || (Yii::$app->user->can('doctorcascap') == true) || ($siteCode == $hsitecode) || ($siteCode == $sitecode)) {
                $urlToCca02 = "/inputdata/redirect-page?dataid=" . $rowId . "&ezf_id=1437619524091524800&rurl=" . base64_encode(Yii::$app->request->url);
                $aTagOpen = "<a id='atag' style=\"text-decoration: none !important;\" href='$urlToCca02' target='_blank'>";
                $aTagClose = "</a>";
            }
            echo '<tr>' . // onclick="javascript:location.href=\''.$urlToCca02.'\'"
//                '<td align="right" valign="top">'.
//                    Yii::$app->user->can('adminsite').'=='.(Yii::$app->user->can('adminsite')=='1').'<br>'.
//                    $siteCode.'==<br>'.
//                    $hsitecode.'=='.($siteCode==$hsitecode).'<br>'.
//                    $sitecode.'=='.($siteCode==$sitecode).'<br>'.
//                '</td>'.
            '<td align="right" valign="top">' . $aTagOpen . $countList . $aTagClose . '</td>' .
            '<td align="right" valign="top">' . $aTagOpen . $hsitecode . $aTagClose . '<br/><span class="bgBludTextWhite">' . $ptcodefull . '</span>' . '</td>' .
            '<td style="mso-number-format:0000#; align="right" valign="top">' . $aTagOpen . $usImage . " " . $hptcode . $aTagClose . '</td>' .
            (((Yii::$app->user->can('adminsite') == '1') || (Yii::$app->user->can('sitemanager') == true) || ($hospital == $siteCode)) ?
                    '<td align="center" valign="top">' . $aTagOpen . $hn . $aTagClose . '</td>' .
                    '<td align="left" valign="top">' . $aTagOpen . ($title . " " . $name . " " . $surname) . $aTagClose . '</td>' :
                    "") .
            '<td align="right" valign="top" id="data-f2v1-' . $rowId . '" data-id="' . $rowId . '" data-f2v1="' . $dateVisit . '">' . $aTagOpen . $dateVisit . $aTagClose . '</td>' .
            '<td align="right" valign="top">' . $aTagOpen . (($parenchymalECHO == '1') ? 'Abnormal' : 'Normal') . $aTagClose . '</td>' .
            '<td align="right" valign="top">' . $aTagOpen . $abNormal . $aTagClose . '</td>' . $tbdataOV .
            '<td align="right" valign="top">' . $aTagOpen . (($liverMass == '0' ? 'None' : (($liverMass == '1') ? 'Single' : 'Multiple'))) . $aTagClose . '</td>' .
            '<td align="center" valign="top">' . $aTagOpen . $cyst . $aTagClose . '</td>' .
            '<td align="center" valign="top">' . $aTagOpen . $hemang . $aTagClose . '</td>' .
            '<td align="center" valign="top">' . $aTagOpen . $cal . $aTagClose . '</td>' .
            '<td align="center" valign="top">' . $aTagOpen . $intra . $aTagClose . '</td>' .
            '<td align="center" valign="top">' . $aTagOpen . $high . $aTagClose . '</td>' .
            '<td align="center" valign="top">' . $aTagOpen . $low . $aTagClose . '</td>' .
            '<td align="center" valign="top">' . $aTagOpen . $mixed . $aTagClose . '</td>' .
            '<td align="center" valign="top">' . $aTagOpen . $none . $aTagClose . '</td>' .
            '<td align="center" valign="top">' . $aTagOpen . $rt . $aTagClose . '</td>' .
            '<td align="center" valign="top">' . $aTagOpen . $lt . $aTagClose . '</td>' .
            '<td align="center" valign="top">' . $aTagOpen . $common . $aTagClose . '</td>' .
            '<td align="right" valign="top">' . $aTagOpen . (($appointment == '1') ? '1 ปี' : '6 เดือน') . $aTagClose . '</td>' .
            '<td align="right" valign="top">' . $aTagOpen . (($send == '1') ? 'ส่งต่อ' : '') . $aTagClose . '</td>' .
            '<td align="center" valign="top">' . $aTagOpen . '-' . $aTagClose . '</td>' .
            '<td align="right">' . $aTagOpen . '<label>'.$CTMRI.'</label> '.$finding.' '. $aTagClose . '</td>' .
            '</tr>';

//            $tbodyExport .= '<tr>'.
//                '<td>'.$countList.'</td>'.
//                '<td>'.$hsitecode.'<br><span class="bgBludTextWhite">'.$ptcodefull.'</span></td>'.
//                '<td>'.$usImage." ".($hptcode).'</td>'.
//                '<td>'.$hn.'</td>'.
//                '<td>'.($title." ".$name." ".$surname).'</td>'.
//                '<td>'.$dateVisit.'</td>'.
//                '</tr>';

            $countList += 1;
        }
        echo '</tbody></table>';
        echo '</div>';

//        echo '<div class="table-exportListPatiantExcel" style="display: none">';
//        echo '<table class="exportListPatiantExcel">'.
//            '<thead>'.
//                '<tr><td>No.</td><td>HOSPCODE</td><td>Paticipant ID</td><td>HN</td><td>Name</td><td>Date visit</td></tr>'.
//            '</thead>'.
//            '<tbody>'.$tbodyExport.'</tbody>'.
//            '</table>';
//        echo '</div>';
    }

    private function getDataListPtReportResult() {

        # US Finding

        $keystore = $_GET['keystore'];
        $startdate = $_GET['startdate'];
        if (0) {
            echo "<pre align='left'>";
            echo "\n";
            print_r($_GET);
            echo "</pre>";
        }
        $enddate = $_GET['enddate'];
        $zone = $_GET['zone'];
        $province = $_GET['province'];
        $amphur = $_GET['amphur'];
        $hospital = $_GET['hospital'];
        if (0) {
            echo "<pre align='left'>";
            print_r($_GET);
            echo "</pre>";
            //exit;
        }

        //\appxq\sdii\utils\VarDumper::dump($hospital.' '.$startdate.' '.$enddate);
        //echo $keystore;
        //echo "<br />";

        if (1) {
            foreach ($this->initUsFinding as $key => $value) {
                if ($key == $keystore) {
                    $concatSql = $this->concatSql($value);
                }
            }

            //echo $concatSql;
            //echo "<br />";
        }

        $sqlStarterGetDataReport = "SELECT DISTINCT `tb_data_3`.`id` as id,
                    `tbdata_1`.`hn` as hn, `tbdata_1`.`hncode` as hncode, `tbdata_1`.`title` as title, `tbdata_1`.`name` as name,
                    `tbdata_1`.`surname` as surname, `tbdata_1`.`cid` as cid, `tbdata_1`.`mobile` as mobile,
                    `tb_data_3`.`hsitecode` as hsitecode, `tb_data_3`.`sitecode` as sitecode, `tb_data_3`.`ptcodefull` as ptcodefull,
                    `tb_data_3`.`ptid` as ptid, `tb_data_3`.`hptcode` as hptcode, `tb_data_3`.`f2v1` as f2v1, `tb_data_3`.`f2v2a1` as f2v2a1,
                    `tb_data_3`.`f2v2a1b1` as f2v2a1b1, `tb_data_3`.`f2v2a1b2` as f2v2a1b2, `tb_data_3`.`f2v2a1b3` as f2v2a1b3, `tb_data_3`.`f2v2a1b4` as f2v2a1b4,
                    `tb_data_3`.`f2v2a2` as f2v2a2, `tb_data_3`.`f2v2a2b1c1` as f2v2a2b1c1, `tb_data_3`.`f2v2a2b2c1` as f2v2a2b2c1, `tb_data_3`.`f2v2a2b3c1` as f2v2a2b3c1,
                    `tb_data_3`.`f2v2a2b4c1` as f2v2a2b4c1, `tb_data_3`.`f2v2a2b5c1` as f2v2a2b5c1, `tb_data_3`.`f2v2a2b6c1` as f2v2a2b6c1,
                    `tb_data_3`.`f2v2a2b7c1` as f2v2a2b7c1, `tb_data_3`.`f2v2a3b0` as f2v2a3b0, `tb_data_3`.`f2v2a3b1` as f2v2a3b1,
                    `tb_data_3`.`f2v2a3b2` as f2v2a3b2, `tb_data_3`.`f2v2a3b3` as f2v2a3b3, `tb_data_3`.`f2v6` as f2v6, `tb_data_3`.`f2v6a3` as f2v6a3,
                    IF(`tb_data_4`.`f2p1v2`=1,'CT',IF(`tb_data_4`.`f2p1v2`=2,'MRI',IF(`tb_data_4`.`f2p1v2`=3,'MRCP',''))) as f2p1v2
                    ,IF(`tb_data_4`.`f2p1v3`=1,'Intrahepatic',IF(`tb_data_4`.`f2p1v3`=2,'Perihilar',IF(`tb_data_4`.`f2p1v3`=2,'Distal',''))) as f2p1v3
                FROM
                    `cascapcloud`.`tb_data_3`
                INNER JOIN `cascapcloud`.`tb_data_1` AS tbdata_1 ON (
                    `tb_data_3`.`ptid` = `tbdata_1`.`id`
                )INNER JOIN `cascapcloud`.`all_hospital_thai` AS all_hospital ON (
                    `tb_data_3`.`hsitecode` = `all_hospital`.`hcode`
                )LEFT JOIN `cascapcloud`.`tb_data_4` ON `tb_data_4`.`ptid` = `tbdata_1`.`id` 
                WHERE (`tb_data_3`.`hsitecode`=`all_hospital`.`hcode`) " .
                "AND `tb_data_3`.`f2v1` BETWEEN '$startdate' AND '$enddate' AND `tb_data_3`.`rstat`!='3' AND `tb_data_3`.`rstat`!='0' ";

        if ($hospital != '' || $hospital != null) {
//            $sqlStarterGetDataReport.="AND `all_hospital_thai`.`hcode`='$hospital' ";
            $sqlStarterGetDataReport .= "AND (`tb_data_3`.`hsitecode` LIKE '%$hospital%' OR `tb_data_3`.`sitecode` LIKE '%$hospital%') ";
        } else {
            if ($zone != '' || $zone != null)
                $sqlStarterGetDataReport .= "AND `all_hospital`.`zone_code` LIKE '%$zone%' ";
            if ($province != '' || $province != null)
                $sqlStarterGetDataReport .= "AND `all_hospital`.`provincecode` LIKE '%$province%' ";
            if ($amphur != '' || $amphur != null)
                $sqlStarterGetDataReport .= "AND `all_hospital`.`amphurcode` LIKE '%$amphur%' ";
            $sqlStarterGetDataReport .= "AND (`tb_data_3`.`hsitecode` LIKE '%$hospital%' OR `tb_data_3`.`sitecode` LIKE '%$hospital%') AND `tb_data_3`.hsitecode NOT IN ('91110','91111','91112','91005')";
        }

        if ($keystore == 'Refer') {
            $sqlStarterGetDataReport .= "AND `f2v6a3` LIKE '%1%' ";
        } else if ($keystore == 'ReferSuspectedCCA') {
            $sqlStarterGetDataReport .= "AND `f2v6a3` LIKE '%1%' " .
                    "AND `f2v6a3b1` LIKE '%1%'";
        } else if ($keystore == 'ReferNoSuspectedCCA') {
            $sqlStarterGetDataReport .= "AND `f2v6a3` LIKE '%1%' " .
                    "AND (`f2v6a3b1` IS NULL OR `f2v6a3b1` NOT LIKE '%1%')";
        } else if ($keystore == 'LiverMassAb') {
            $sqlStarterGetDataReport .= "AND `f2v2a2` NOT LIKE '%0%' ";
        } else if ($keystore == 'LiverMass') {
            $sqlStarterGetDataReport .= "AND `f2v2a2` NOT LIKE '%0%' " .
                    "AND (`f2v2a2b5c1` LIKE '%1%' OR `f2v2a2b6c1` LIKE '%1%' OR `f2v2a2b7c1` LIKE '%1%' ) ";
        } else if ($keystore == 'DilatedBileDuct') {
            $sqlStarterGetDataReport .= "AND ( `f2v2a3b1` LIKE '%1%' OR `f2v2a3b2` LIKE '%1%' OR `f2v2a3b3` LIKE '%1%' ) ";

            // SUSPECTED CASE DRILL DOWN ===========================================
        } else if ($keystore == 'normal') {
            $sqlStarterGetDataReport .= " AND f2v2a1 LIKE '%0%' ";
        } else if ($keystore == 'abnormal') {
            $sqlStarterGetDataReport .= " AND f2v2a1 LIKE '%1%' ";
        } else if ($keystore == 'liver-mass') {
            $sqlStarterGetDataReport .= " AND f2v6a3b1 LIKE '%1%' AND (`f2v2a2` NOT LIKE '%0%' "
                    . "AND (`f2v2a2b5c1` LIKE '%1%' OR `f2v2a2b6c1` LIKE '%1%' OR `f2v2a2b7c1` LIKE '%1%' )) ";
        } else if ($keystore == 'fatty-liver') {
            $sqlStarterGetDataReport .= "  AND  (IFNULL(f2v2a1b1,0) LIKE '%1%' OR f2v2a1b1 LIKE '%2%' OR f2v2a1b1 LIKE '%3%') ";
        } else if ($keystore == 'pdf') {
            $sqlStarterGetDataReport .= "  AND (f2v2a1b2 LIKE '%1%' OR f2v2a1b2 LIKE '%2%' OR f2v2a1b2 LIKE '%3%') ";
        } else if ($keystore == 'cirrhosis') {
            $sqlStarterGetDataReport .= "  AND f2v2a1b3 LIKE '%1%' ";
        } else if ($keystore == 'parenchymal-change') {
            $sqlStarterGetDataReport .= "  AND f2v2a1b4 LIKE '%1%' ";
        } else if ($keystore == 'suspected-case') {
            $sqlStarterGetDataReport .= " AND `f2v6a3b1` LIKE '%1%' ";
        } else if ($keystore == 'dilated-duct') {
            $sqlStarterGetDataReport .= " AND `f2v6a3b1` LIKE '%1%' AND (`f2v2a3b1` LIKE '%1%' OR `f2v2a3b2` LIKE '%1%' OR `f2v2a3b3` LIKE '%1%' ) ";
        } else if ($keystore == 'both') {
            $sqlStarterGetDataReport .= " AND f2v6a3b1='1'  AND ((IFNULL(f2v2a1b4,0)!='1' AND  (f2v2a3b1 LIKE '%1%' OR f2v2a3b2 LIKE '%1%' OR f2v2a3b3 LIKE '%1%')) AND (`f2v2a2b5c1` LIKE '%1%' OR `f2v2a2b6c1` LIKE '%1%' OR `f2v2a2b7c1` LIKE '%1%' ))";
        } else if ($keystore == 'suspected-parenchymal') {
            $sqlStarterGetDataReport .= " AND f2v6a3b1='1' ";
        } else if ($keystore == 'suspected-normal') {
            $sqlStarterGetDataReport .= " AND f2v6a3b1='1' AND f2v2a1 LIKE '%0%' ";
        } else if ($keystore == 'suspected-abnormal') {
            $sqlStarterGetDataReport .= " AND f2v6a3b1='1' AND f2v2a1 LIKE '%1%' ";
        } else if ($keystore == 'suspected-fatty-liver') {
            $sqlStarterGetDataReport .= " AND f2v6a3b1='1'  AND  ((f2v2a1b1 LIKE '%1%' OR f2v2a1b1 LIKE '%2%' OR f2v2a1b1 LIKE '%3%') AND (f2v2a3b1 LIKE '%1%' OR f2v2a3b2 LIKE '%1%' OR f2v2a3b3 LIKE '%1%')) ";
        } else if ($keystore == 'suspected-pdf') {
            $sqlStarterGetDataReport .= " AND f2v6a3b1='1' AND (f2v2a1b2 LIKE '%1%' OR f2v2a1b2 LIKE '%2%' OR f2v2a1b2 LIKE '%3%') ";
        } else if ($keystore == 'suspected-cirrhosis') {
            $sqlStarterGetDataReport .= " AND f2v6a3b1='1' AND f2v2a1b3 LIKE '%1%' ";
        } else if ($keystore == 'suspected-parenchymal-change') {
            $sqlStarterGetDataReport .= " AND f2v6a3b1='1' AND f2v2a1b4 LIKE '%1%' ";
        } else if ($keystore == 'refer-case') {
            $sqlStarterGetDataReport .= " AND f2v6a3 LIKE '%1%' ";
        } else if ($keystore == 'refer-suspected-cca') {
            $sqlStarterGetDataReport .= " AND f2v6a3 LIKE '%1%' AND f2v6a3b1 LIKE '%1%' ";
        } else if ($keystore == 'refer-other') {
            $sqlStarterGetDataReport .= " AND f2v6a3 LIKE '%1%' AND (`f2v6a3b1` IS NULL OR `f2v6a3b1` NOT LIKE '%1%') ";
        }


        if (strlen($concatSql) > 0) {
            $sqlStarterGetDataReport .= $concatSql . " ";
        }
        $sqlStarterGetDataReport .= "ORDER BY hptcode ASC";
        
        if (0) {
            echo "<br />";
            echo $sqlStarterGetDataReport;
            echo "<br />";
            //exit;
        }

        if (str_replace('OVUUrine', '', $keystore) != $keystore) {
            $ovuSQL = self::ovuFilter($keystore);
            $ovuSQLHosp = self::ovuHospital();
            $sqlOV = 'select results as urine_result,usfinding.* from tbdata_24 urine left join (';
            $sqlOV .= $sqlStarterGetDataReport;
            $sqlOV .= ') usfinding ';
            $sqlOV .= 'on usfinding.ptid=urine.ptid where usfinding.ptid is not null ';
            $sqlOV .= $ovuSQL;
            $sqlOV .= $ovuSQLHosp;
            $sqlStarterGetDataReport = $sqlOV;
        } else if (str_replace('OVU', '', $keystore) != $keystore) {
            $ovuSQLHosp = self::ovuHospital();
            $sqlOV = 'select results as urine_result,usfinding.* from (';
            $sqlOV .= $sqlStarterGetDataReport;
            $sqlOV .= ') usfinding ';
            $sqlOV .= 'left join tbdata_24 urine ';
            $sqlOV .= 'on usfinding.ptid=urine.ptid where usfinding.ptid is not null ';
            $sqlOV .= $ovuSQLHosp;
            $sqlStarterGetDataReport = $sqlOV;
        }

        if (0) {
            echo "<br />";
            echo $sqlStarterGetDataReport;
        }
        $qryNumDataUsFindingReport = Yii::$app->db->createCommand($sqlStarterGetDataReport)->queryAll();

        return $qryNumDataUsFindingReport;
    }

    private function ovuFilter($ovu) {
        if ($ovu === 'OVUUrinePDFUrPos') {
            $out = 'and urine.results="1" ';
        } else if ($ovu === 'OVUUrinePDFUrNeg') {
            $out = 'and urine.results="0" ';
        } else if ($ovu === 'OVUUrineSuspUrPos') {
            $out = 'and urine.results="1" ';
        } else if ($ovu === 'OVUUrineSuspUrNeg') {
            $out = 'and urine.results="0" ';
        }
        return $out;
    }

    private function ovuHospital() {
        $request = Yii::$app->request;
        if (strlen($request->get('ovuhospital')) > 0) {
            $out = 'and urine.hsitecode="' . $request->get('ovuhospital') . '" ';
        }
        return $out;
    }

    private function getDataListPtReport() {
        $keystore = $_GET['keystore'];
        $startdate = $_GET['startdate'];
        $enddate = $_GET['enddate'];
        //echo $startdate;
        if (0) {
            echo "<pre align='left'>";
            print_r($_GET);
            echo "</pre>";
        }
        $zone = $_GET['zone'];
        $province = $_GET['province'];
        $amphur = $_GET['amphur'];
        $hospital = $_GET['hospital'];

        $sqlStarterGetDataReport = "SELECT DISTINCT `tb_data_3`.`id` as id,
                    `tbdata_1`.`hn` as hn, `tbdata_1`.`hncode` as hncode, `tbdata_1`.`title` as title, `tbdata_1`.`name` as name,
                    `tbdata_1`.`surname` as surname, `tbdata_1`.`cid` as cid, `tbdata_1`.`mobile` as mobile,
                    `tb_data_3`.`hsitecode` as hsitecode, `tb_data_3`.`sitecode` as sitecode, `tb_data_3`.`ptcodefull` as ptcodefull,
                    `tb_data_3`.`ptid` as ptid, `tb_data_3`.`hptcode` as hptcode, `tb_data_3`.`f2v1` as f2v1, `tb_data_3`.`f2v2a1` as f2v2a1,
                    `tb_data_3`.`f2v2a1b1` as f2v2a1b1, `tb_data_3`.`f2v2a1b2` as f2v2a1b2, `tb_data_3`.`f2v2a1b3` as f2v2a1b3, `tb_data_3`.`f2v2a1b4` as f2v2a1b4,
                    `tb_data_3`.`f2v2a2` as f2v2a2, `tb_data_3`.`f2v2a2b1c1` as f2v2a2b1c1, `tb_data_3`.`f2v2a2b2c1` as f2v2a2b2c1, `tb_data_3`.`f2v2a2b3c1` as f2v2a2b3c1,
                    `tb_data_3`.`f2v2a2b4c1` as f2v2a2b4c1, `tb_data_3`.`f2v2a2b5c1` as f2v2a2b5c1, `tb_data_3`.`f2v2a2b6c1` as f2v2a2b6c1,
                    `tb_data_3`.`f2v2a2b7c1` as f2v2a2b7c1, `tb_data_3`.`f2v2a3b0` as f2v2a3b0, `tb_data_3`.`f2v2a3b1` as f2v2a3b1,
                    `tb_data_3`.`f2v2a3b2` as f2v2a3b2, `tb_data_3`.`f2v2a3b3` as f2v2a3b3, `tb_data_3`.`f2v6` as f2v6, `tb_data_3`.`f2v6a3` as f2v6a3
                FROM
                    `cascapcloud`.`tb_data_3`
                INNER JOIN `cascapcloud`.`tb_data_1` AS tbdata_1 ON (
                    `tb_data_3`.`ptid` = `tbdata_1`.`id`
                )INNER JOIN `cascapcloud`.`all_hospital_thai` AS all_hospital ON (
                    `tb_data_3`.`hsitecode` = `all_hospital`.`hcode`
                )
                WHERE (`tb_data_3`.`hsitecode`=`all_hospital`.`hcode`) " .
                "AND `tb_data_3`.`f2v1` BETWEEN '$startdate' AND '$enddate' AND `tb_data_3`.`rstat`!='3' AND `tb_data_3`.`rstat`!='0' ";


        if ($hospital != '' || $hospital != null) {
            $sqlStarterGetDataReport .= "AND (`tb_data_3`.`hsitecode` LIKE '%$hospital%' OR `tb_data_3`.`sitecode` LIKE '%$hospital%') ";
        } else {
            if ($zone != '' || $zone != null)
                $sqlStarterGetDataReport .= "AND `all_hospital`.`zone_code` LIKE '%$zone%' ";
            if ($province != '' || $province != null)
                $sqlStarterGetDataReport .= "AND `all_hospital`.`provincecode` LIKE '%$province%' ";
            if ($amphur != '' || $amphur != null)
                $sqlStarterGetDataReport .= "AND `all_hospital`.`amphurcode`LIKE '%$amphur%' ";
            $sqlStarterGetDataReport .= "AND (`tb_data_3`.`hsitecode` LIKE '%$hospital%' OR `tb_data_3`.`sitecode` LIKE '%$hospital%') AND `tb_data_3`.hsitecode NOT IN ('91110','91111','91112','91005') ";
        }

        $concatSql = $this->concatSql($this->initUsFinding[$keystore]);
        $sqlStarterGetDataReport .= ($concatSql . "ORDER BY hptcode ASC");
//        echo $sqlStarterGetDataReport;
        $qryNumDataUsFindingReport = Yii::$app->db->createCommand($sqlStarterGetDataReport)->queryAll();
        return $qryNumDataUsFindingReport;
    }

    private function getDateFromUsTour($hsitecode, $times) {
        $sqlGetDate = "SELECT * FROM `history_us_tour` WHERE `hcode` LIKE '%$hsitecode%' AND `times` = $times";
        $qryAllHospitalThai = Yii::$app->dbcascap->createCommand($sqlGetDate)->queryAll();
        return $qryAllHospitalThai;
    }

    private function getDateFromUsSite($hsitecode) {
        $sqlUsTour = "SELECT ussite.No, ussite.dateatsite, ussite.hcode, ussite.hospitalname ";
        $sqlUsTour .= ",hospital.zone_code as zonecode ";
        $sqlUsTour .= ",hospital.provincecode as provcode ";
        $sqlUsTour .= ",concat(hospital.provincecode,hospital.amphurcode) as ampcode ";
        $sqlUsTour .= ",ussite.dateatsite as sdate ";
        $sqlUsTour .= ",substr(NOW(),1,10) as edate ";
        $sqlUsTour .= "from history_us_site ussite ";
        $sqlUsTour .= "left join all_hospital_thai hospital ";
        $sqlUsTour .= "on hospital.hcode=ussite.hcode ";
        $sqlUsTour .= "WHERE ussite.`hcode` LIKE '%$hsitecode%' ";
        $sqlUsTour .= "order by ussite.dateatsite ";
        //echo $sqlUsTour;
        $qryAllHospitalThai = Yii::$app->db->createCommand($sqlUsTour)->queryAll();
        return $qryAllHospitalThai;
    }

    private function generateSqlShowReport($startDate = null, $endDate = null, $zoneCode = null, $provinceCode = null, $amphurCode = null, $hospitalCode = null) {
        if (0) {
            $sqlStarterGetDataReport = "SELECT COUNT(DISTINCT id) as count FROM `cascapcloud`.`tb_data_3` " . (
                    ($hospitalCode == null) ?
                    ", `cascapcloud`.`all_hospital_thai` " :
                    ""
                    ) . " WHERE `tb_data_3`.`f2v1` BETWEEN '$startDate' AND '$endDate' AND `tb_data_3`.`rstat`!='3' AND `tb_data_3`.`rstat`!='0' ";
            $this->allDataUsFindingReport['initUsFinding'] = [
                'startDate' => $startDate,
                'endDate' => $endDate
            ];

            if ($hospitalCode != null) {
                $sqlStarterGetDataReport .= "AND (`tb_data_3`.`hsitecode` = '$hospitalCode' OR `tb_data_3`.`sitecode` = '$hospitalCode') ";
                $this->allDataUsFindingReport['initUsFinding']['hospitalCode'] = $hospitalCode;
            } else {
                if ($zoneCode != null) {
                    $sqlStarterGetDataReport .= "AND `all_hospital_thai`.`zone_code`='$zoneCode' ";
                    $this->allDataUsFindingReport['initUsFinding']['zoneCode'] = $zoneCode;
                }
                if ($provinceCode != null) {
                    $sqlStarterGetDataReport .= "AND `all_hospital_thai`.`provincecode`='$provinceCode' ";
                    $this->allDataUsFindingReport['initUsFinding']['provinceCode'] = $provinceCode;
                }
                if ($amphurCode != null) {
                    $amphurCode = substr($amphurCode, 2, 2);
                    $sqlStarterGetDataReport .= "AND `all_hospital_thai`.`amphurcode`='$amphurCode' ";
                    $this->allDataUsFindingReport['initUsFinding']['amphurCode'] = $amphurCode;
                }
                $sqlStarterGetDataReport .= "AND (`tb_data_3`.`hsitecode` = `all_hospital_thai`.`hcode` OR `tb_data_3`.`sitecode` = `all_hospital_thai`.`hcode`) ";
            }
            $this->getAllDataUsFindingReport($sqlStarterGetDataReport);
        } else {
            //เปลี่ยนมาใช้การ Join data
            if ($hospitalCode != null) {
                $sqlStarterGetDataReport = " FROM `cascapcloud`.`tb_data_3`";
                $sqlStarterGetDataReport .= " WHERE `tb_data_3`.`f2v1` BETWEEN '$startDate' AND '$endDate' AND `tb_data_3`.`rstat`!='3' AND `tb_data_3`.`rstat`!='0' ";
                $this->allDataUsFindingReport['initUsFinding'] = [
                    'startDate' => $startDate,
                    'endDate' => $endDate
                ];
                $sqlStarterGetDataReport .= "AND (`tb_data_3`.`hsitecode` = '$hospitalCode' OR `tb_data_3`.`sitecode` = '$hospitalCode') ";
                $this->allDataUsFindingReport['initUsFinding']['hospitalCode'] = $hospitalCode;
            } else {
                // กรณีที่ เลือกในระดับ พื้นที่  //
                $sqlStarterGetDataReport = " FROM `cascapcloud`.`tb_data_3`";
                $sqlStarterGetDataReport .= " INNER JOIN `all_hospital_thai` on `tb_data_3`.`hsitecode` = `all_hospital_thai`.`hcode` ";
                $sqlStarterGetDataReport .= " WHERE `tb_data_3`.`f2v1` BETWEEN '$startDate' AND '$endDate' AND `tb_data_3`.`rstat`!='3' AND `tb_data_3`.`rstat`!='0' ";
                $this->allDataUsFindingReport['initUsFinding'] = [
                    'startDate' => $startDate,
                    'endDate' => $endDate
                ];
                if (strlen($zoneCode) == 2) {
                    $sqlStarterGetDataReport .= "AND `all_hospital_thai`.`zone_code`='$zoneCode' ";
                    $this->allDataUsFindingReport['initUsFinding']['zoneCode'] = $zoneCode;
                } else if ($zoneCode == 0) {
                    $sqlStarterGetDataReport .= "AND `all_hospital_thai`.`zone_code` is not null ";
                    $this->allDataUsFindingReport['initUsFinding']['zoneCode'] = $zoneCode;
                }
                if ($provinceCode != null) {
                    $sqlStarterGetDataReport .= "AND `all_hospital_thai`.`provincecode`='$provinceCode' ";
                    $this->allDataUsFindingReport['initUsFinding']['provinceCode'] = $provinceCode;
                }
                if ($amphurCode != null) {
                    $amphurCode = substr($amphurCode, 2, 2);
                    $sqlStarterGetDataReport .= "AND `all_hospital_thai`.`amphurcode`='$amphurCode' ";
                    $this->allDataUsFindingReport['initUsFinding']['amphurCode'] = $amphurCode;
                }
            }
            $this->sqlCurrentCondition = $sqlStarterGetDataReport;

            $this->getAllDataUsFindingReport($sqlStarterGetDataReport);
        }
    }

    private function concatSql($optionalCommand) {
        $str = "";
        if (0) {
            echo "<pre align='left'>";
            print_r($optionalCommand);
            echo "</pre>";
            echo "<br />";
        }
        if (!is_null($optionalCommand)) {
            foreach ($optionalCommand as $value) {
                if (is_array($value[0])) {
                    $str .= "( ";
                    $str .= $this->concatSql($value[0]);
                    $str .= ") ";
                } else if (is_null($value[0])) {
                    $str .= "$value[1] $value[2] '%$value[3]%' ";
                } else {
                    if (($value[0] == 'AND' || $value[0] == 'OR') && is_array($value[1])) {
                        $str .= $value[0] . " (" . $this->concatSql($value[1]) . ")";
                    } else if (($value[0] == 'AND' || $value[0] == 'OR') && !is_array($value[1])) {
                        $str .= "$value[0] $value[1] $value[2] '%$value[3]%' ";
                    }
                }
            }
        }
        //echo $str;
        return $str;
    }

    private function getAllDataUsFindingReport($sqlStarterGetDataReport) {
        $new_sql = "";
        foreach ($this->initUsFinding as $key => $value) {
            $concatSql = $this->concatSql($value);
            //echo "condition ";
            //echo $concatSql;
            //echo "<br />";
            $new_sql .= ",count(distinct if( 1 " . $concatSql . ",id, null )) as '" . $key . "' ";
            $new_sql .= "\n";
            //
            $newSql = $sqlStarterGetDataReport . "" . $concatSql;
            //$qryNumDataUsFindingReport= Yii::$app->dbcascap->createCommand($newSql)->queryAll();
            //VarDumper::dump(Yii::$app->dbcascap->createCommand($newSql)->rawSql);
            //echo "<br />";
            //echo $key;
            //echo "<br />";
            //echo "<br />";
//            exit();
            $this->allDataUsFindingReport[$key] = [
                'count' => $qryNumDataUsFindingReport[0]['count'],
//                'optional' => $this->initUsFinding[$key]
//                'sql' => $newSql,
            ];
        }
        if (0) {
            echo "<br />";
            echo $sqlStarterGetDataReport;
            echo "<br />";
            echo "SQL: ";
            echo "<br />";
            echo "<pre align='left'>";
            echo $new_sql;
            echo "</pre>";
            echo "<br />";
        }
        if (1) {
            $sql = "select count(id) as count ";
            $sql .= $new_sql;
            $sql .= str_replace("SELECT COUNT(DISTINCT id) as count", "", $sqlStarterGetDataReport);
            if (0) {
                echo "<br />";
                echo "<pre align='left'>";
                echo $sql;
                echo "</pre>";
                echo "<br />";
            }

            $result = Yii::$app->db->createCommand($sql)->queryAll();
            if (count($result[0]) > 0) {
                /*
                  echo "<pre align='left'>";
                  print_r($result[0]);
                  echo "</pre>";
                 * 
                 */
                foreach ($result[0] as $kr => $vr) {
                    /*
                      echo "<pre align='left'>";
                      print_r($kr);
                      echo "</pre>";
                     * 
                     */
                    $this->allDataUsFindingReport[$kr] = [
                        'count' => number_format($result[0][$kr], 0, '.', ','),
                            //                'optional' => $this->initUsFinding[$key]
                            //                'sql' => $newSql,
                    ];
                }
            }
        }
    }

    private function getResultUsFinging() {
        $startDate = $this->allDataUsFindingReport['initUsFinding']['startDate'];
        //echo $startDate;
        if (0) {
            echo "<pre align='left'>";
            echo "user from allDateUsFindingReport\n";
            print_r($_GET);
            echo "</pre>";
        }
        //echo Yii::$app->formatter->asDatetime($dateTime, "php:d-m-Y  H:i:s");
        $endDate = $this->allDataUsFindingReport['initUsFinding']['endDate'];

        if (0) {
            $sqlRefer = "SELECT COUNT(DISTINCT id) as COUNT ";
            $sqlRefer .= "FROM `cascapcloud`.`tb_data_3` ";
            $sqlRefer .= "INNER JOIN `cascapcloud`.`all_hospital_thai` " .
                    "WHERE (`tb_data_3`.`hsitecode`=`all_hospital_thai`.`hcode`) " .
                    "AND `f2v6a3` LIKE '%1%' " .
                    "AND `tb_data_3`.`f2v1` BETWEEN '$startDate' AND '$endDate' AND `tb_data_3`.`rstat`!='3' AND `tb_data_3`.`rstat`!='0' ";

            $sqlReferSuspectedCCA = "SELECT COUNT(DISTINCT id) as COUNT ";
            $sqlReferSuspectedCCA .= "FROM `cascapcloud`.`tb_data_3`, `cascapcloud`.`all_hospital_thai` " .
                    " WHERE (`tb_data_3`.`hsitecode`=`all_hospital_thai`.`hcode`) " .
                    "AND `f2v6a3` LIKE '%1%' " .
                    "AND `f2v6a3b1` LIKE '%1%'" .
                    "AND `tb_data_3`.`f2v1` BETWEEN '$startDate' AND '$endDate' AND `tb_data_3`.`rstat`!='3' AND `tb_data_3`.`rstat`!='0' ";

            $sqlReferNoSuspectedCCA = "SELECT COUNT(DISTINCT id) as COUNT ";
            $sqlReferNoSuspectedCCA .= "FROM `cascapcloud`.`tb_data_3`, `cascapcloud`.`all_hospital_thai` " .
                    " WHERE (`tb_data_3`.`hsitecode`=`all_hospital_thai`.`hcode`) " .
                    "AND `f2v6a3` LIKE '%1%' " .
                    "AND (`f2v6a3b1` IS NULL OR `f2v6a3b1` NOT LIKE '%1%')" .
                    "AND `tb_data_3`.`f2v1` BETWEEN '$startDate' AND '$endDate' AND `tb_data_3`.`rstat`!='3' AND `tb_data_3`.`rstat`!='0' ";

            $sqlLiverMass = "SELECT COUNT(DISTINCT id) as COUNT FROM `cascapcloud`.`tb_data_3`, `cascapcloud`.`all_hospital_thai` " .
                    " WHERE (`tb_data_3`.`hsitecode`=`all_hospital_thai`.`hcode`) " .
                    "AND `f2v2a2` NOT LIKE '%0%' " .
                    "AND (`f2v2a2b5c1` LIKE '%1%' OR `f2v2a2b6c1` LIKE '%1%' OR `f2v2a2b7c1` LIKE '%1%' ) " .
                    "AND `tb_data_3`.`f2v1` BETWEEN '$startDate' AND '$endDate' AND `tb_data_3`.`rstat`!='3' AND `tb_data_3`.`rstat`!='0' ";

            $sqlLiverMassAbNormal = "SELECT COUNT(DISTINCT id) as COUNT FROM `cascapcloud`.`tb_data_3`, `cascapcloud`.`all_hospital_thai` " .
                    " WHERE (`tb_data_3`.`hsitecode`=`all_hospital_thai`.`hcode`) " .
                    "AND `f2v2a2` NOT LIKE '%0%' " .
                    "AND `tb_data_3`.`f2v1` BETWEEN '$startDate' AND '$endDate' AND `tb_data_3`.`rstat`!='3' AND `tb_data_3`.`rstat`!='0' ";

            $sqlDilatedBileDuct = "SELECT COUNT(DISTINCT id) as COUNT FROM `cascapcloud`.`tb_data_3`, `cascapcloud`.`all_hospital_thai` " .
                    " WHERE (`tb_data_3`.`hsitecode`=`all_hospital_thai`.`hcode`) " .
                    "AND ( `f2v2a3b1` LIKE '%1%' OR `f2v2a3b2` LIKE '%1%' OR `f2v2a3b3` LIKE '%1%' ) " .
                    "AND `tb_data_3`.`f2v1` BETWEEN '$startDate' AND '$endDate' AND `tb_data_3`.`rstat`!='3' AND `tb_data_3`.`rstat`!='0' ";


            $zoneCode = isset($this->allDataUsFindingReport['initUsFinding']['zoneCode']) ? $this->allDataUsFindingReport['initUsFinding']['zoneCode'] : null;
            $provinceCode = isset($this->allDataUsFindingReport['initUsFinding']['provinceCode']) ? $this->allDataUsFindingReport['initUsFinding']['provinceCode'] : null;
            $amphurCode = isset($this->allDataUsFindingReport['initUsFinding']['amphurCode']) ? $this->allDataUsFindingReport['initUsFinding']['amphurCode'] : null;
            $hospitalCode = isset($this->allDataUsFindingReport['initUsFinding']['hospitalCode']) ? $this->allDataUsFindingReport['initUsFinding']['hospitalCode'] : null;
            if ($zoneCode != null) {
                $strAndZone = "AND `zone_code` LIKE '%" . $zoneCode . "%' ";
                $sqlRefer .= $strAndZone;
                $sqlReferSuspectedCCA .= $strAndZone;
                $sqlReferNoSuspectedCCA .= $strAndZone;
                $sqlLiverMass .= $strAndZone;
                $sqlLiverMassAbNormal .= $strAndZone;
                $sqlDilatedBileDuct .= $strAndZone;
            }
            if ($provinceCode != null) {
                $strAndProvince = "AND `provincecode` LIKE '%" . $provinceCode . "%' ";
                $sqlRefer .= $strAndProvince;
                $sqlReferSuspectedCCA .= $strAndProvince;
                $sqlReferNoSuspectedCCA .= $strAndProvince;
                $sqlLiverMass .= $strAndProvince;
                $sqlLiverMassAbNormal .= $strAndProvince;
                $sqlDilatedBileDuct .= $strAndProvince;
            }
            if ($amphurCode != null) {
                $strAndAmphur = "AND `amphurcode` LIKE '%" . $amphurCode . "%' ";
                $sqlRefer .= $strAndAmphur;
                $sqlReferSuspectedCCA .= $strAndAmphur;
                $sqlReferNoSuspectedCCA .= $strAndAmphur;
                $sqlLiverMass .= $strAndAmphur;
                $sqlLiverMassAbNormal .= $strAndAmphur;
                $sqlDilatedBileDuct .= $strAndAmphur;
            }
            if ($hospitalCode != null) {
                $strAndHospital = "AND `all_hospital_thai`.`hcode` LIKE '%" . $hospitalCode . "%' ";
                $sqlRefer .= $strAndHospital;
                $sqlReferSuspectedCCA .= $strAndHospital;
                $sqlReferNoSuspectedCCA .= $strAndHospital;
                $sqlLiverMass .= $strAndHospital;
                $sqlLiverMassAbNormal .= $strAndHospital;
                $sqlDilatedBileDuct .= $strAndHospital;
            }


            $qryRefer = Yii::$app->dbcascap->createCommand($sqlRefer)->queryAll();
            $qryReferSuspectedCCA = Yii::$app->dbcascap->createCommand($sqlReferSuspectedCCA)->queryAll();
            $qryReferNoSuspectedCCA = Yii::$app->dbcascap->createCommand($sqlReferNoSuspectedCCA)->queryAll();
            $qryLiverMass = Yii::$app->dbcascap->createCommand($sqlLiverMass)->queryAll();
            $qryLiverMassAbNormal = Yii::$app->dbcascap->createCommand($sqlLiverMassAbNormal)->queryAll();
            $qryDilatedBileDuct = Yii::$app->dbcascap->createCommand($sqlDilatedBileDuct)->queryAll();

            $allCount = $this->allDataUsFindingReport["Parenchymal-ECHO"]["count"];
            $countRefer = $qryRefer[0]['COUNT'];
            $countReferSuspectedCCA = $qryReferSuspectedCCA[0]['COUNT'];
            if ($allCount != 0) {
                $percentReferSuspectedCCA = round(($countReferSuspectedCCA / $allCount) * 100, 1);
            } else {
                $percentReferSuspectedCCA = 0;
            }

            $countPerOneHundredThousand = number_format(($percentReferSuspectedCCA * 1000), 0, '.', ',');
            $countReferNoSuspectedCCA = $qryReferNoSuspectedCCA[0]['COUNT'];
            $countLiverMassAb = $qryLiverMassAbNormal[0]['COUNT'];
            $countLiverMass = $qryLiverMass[0]['COUNT'];
            $countDilatedBileDuct = $qryDilatedBileDuct[0]['COUNT'];
        } else {

            $sql = "select ";
            $sql .= "count(distinct if(`f2v6a3` LIKE '%1%',id,NULL)) "
                    . "as 'Refer' ";
            $sql .= ",count(distinct if(`f2v6a3` LIKE '%1%' "
                    . "AND `f2v6a3b1` LIKE '%1%',id,NULL)) "
                    . "as 'ReferSuspectedCCA' ";
            $sql .= ",count(distinct if(`f2v6a3` LIKE '%1%' "
                    . "AND (`f2v6a3b1` IS NULL OR `f2v6a3b1` NOT LIKE '%1%'),id,NULL)) "
                    . "as 'ReferNoSuspectedCCA' ";
            $sql .= ",count(distinct if(`f2v2a2` NOT LIKE '%0%' "
                    . "AND (`f2v2a2b5c1` LIKE '%1%' OR `f2v2a2b6c1` LIKE '%1%' OR `f2v2a2b7c1` LIKE '%1%' ),id,NULL)) "
                    . "as 'LiverMass' ";
            $sql .= ",count(distinct if(`f2v2a2` NOT LIKE '%0%',id,NULL)) "
                    . "as 'LiverMassAbNormal' ";
            $sql .= ",count(distinct if(( `f2v2a3b1` LIKE '%1%' OR `f2v2a3b2` LIKE '%1%' OR `f2v2a3b3` LIKE '%1%' ),id,NULL)) "
                    . "as 'DilatedBileDuct' ";
            $sql .= ",count(distinct if(( `f2v2a1b4` LIKE '%1%'),id,NULL)) "
                    . "as 'ParenchymalChange' ";
            $sql .= ",count(distinct if( `f2v6a3b1` LIKE '%1%' AND (`f2v2a3b1` LIKE '%1%' OR `f2v2a3b2` LIKE '%1%' OR `f2v2a3b3` LIKE '%1%' ),id,NULL)) "
                    . "as 'dilated_bileDuct' ";
            $sql .= ",count(distinct if(f2v6a3b1 LIKE '%1%' AND (`f2v2a2` NOT LIKE '%0%' "
                    . "AND (`f2v2a2b5c1` LIKE '%1%' OR `f2v2a2b6c1` LIKE '%1%' OR `f2v2a2b7c1` LIKE '%1%' )),id,NULL)) "
                    . "as 'liver_mass' ";
            $sql .= ",count(distinct if(f2v6a3b1='1' AND (`f2v6a3b1` LIKE '%1%' AND IFNULL(f2v2a1b4,0)!='1' AND (`f2v2a3b1`='1' OR `f2v2a3b2`='1' OR `f2v2a3b3`='1') AND (`f2v2a2` IN('1','2') AND (`f2v2a2b5c1`='1' OR `f2v2a2b6c1`='1' OR `f2v2a2b7c1`='1' ))),id,NULL)) "
                    . "as 'Both' ";
            $sql .= ",count(distinct if(f2v6a3b1='1',id,NULL)) "
                    . "as 'suspected_case' ";
            $sql .= ",count(distinct if(f2v6a3b1='1' AND (f2v2a1='0' OR f2v2a1='1'),id,NULL)) "
                    . "as 'suspected_parenchymal' ";
            $sql .= ",count(distinct if(f2v6a3b1='1' AND f2v2a1 LIKE '%0%',id,NULL)) "
                    . "as 'suspected_normal' ";
            $sql .= ",count(distinct if(f2v6a3b1='1' AND f2v2a1 LIKE '%1%',id,NULL)) "
                    . "as 'suspected_abnormal' ";
            $sql .= ",count(distinct if(f2v6a3b1='1'  AND  ((f2v2a1b1 LIKE '%1%' OR f2v2a1b1 LIKE '%2%' OR f2v2a1b1 LIKE '%3%') AND (f2v2a3b1 LIKE '%1%' OR f2v2a3b2 LIKE '%1%' OR f2v2a3b3 LIKE '%1%')),id,NULL)) "
                    . "as 'suspected_fatty_liver' ";
            $sql .= ",count(distinct if(f2v6a3b1='1' AND (f2v2a1b2 LIKE '%1%' OR f2v2a1b2 LIKE '%2%' OR f2v2a1b2 LIKE '%3%') ,id,NULL)) "
                    . "as 'suspected_pdf' ";
            $sql .= ",count(distinct if(f2v6a3b1='1' AND f2v2a1b3 LIKE '%1%',id,NULL)) "
                    . "as 'suspected_cirrhosis' ";
            $sql .= ",count(distinct if(f2v6a3b1='1' AND f2v2a1b4='1',id,NULL)) "
                    . "as 'suspected_parenchymal_change' ";
            $sql .= $this->sqlCurrentCondition;
            if (0) {
                echo "<br />";
                echo $sql;
                echo "<br />";
            }

            $result = Yii::$app->db->createCommand($sql)->queryOne();
            if (0) {
                echo "<br />";
                echo "<pre align='left'>";
                print_r($result);
                echo "</pre>";
                echo "<br />";
            }

            $qryRefer = $result['Refer'];
            $qryReferSuspectedCCA = $result['ReferSuspectedCCA']; //Yii::$app->dbcascap->createCommand($sqlReferSuspectedCCA)->queryAll();
            $qryReferNoSuspectedCCA = $result['ReferNoSuspectedCCA']; //Yii::$app->dbcascap->createCommand($sqlReferNoSuspectedCCA)->queryAll();
            $qryLiverMass = $result['LiverMass']; //Yii::$app->dbcascap->createCommand($sqlLiverMass)->queryAll();
            $qryLiverMassAbNormal = $result['LiverMassAbNormal']; //Yii::$app->dbcascap->createCommand($sqlLiverMassAbNormal)->queryAll();
            $qryDilatedBileDuct = $result['DilatedBileDuct']; //Yii::$app->dbcascap->createCommand($sqlDilatedBileDuct)->queryAll();

            $allCount = $this->allDataUsFindingReport["Parenchymal-ECHO"]["count"];
            $countRefer = $result['Refer'];
            $countReferSuspectedCCA = $result['ReferSuspectedCCA'] * 1;

            //var_dump($allCount);

            if ($allCount > 0) {
                $percentReferSuspectedCCA = ($countReferSuspectedCCA / str_replace(',', '', $allCount)) * 100;
            } else {
                $percentReferSuspectedCCA = 0;
            }

            $countPerOneHundredThousand = number_format(($percentReferSuspectedCCA * 1000), 0, '.', ',');
            $countReferNoSuspectedCCA = $result['ReferNoSuspectedCCA'];
            $countLiverMassAb = $result['LiverMassAbNormal'];
            $countLiverMass = $result['LiverMass'];
            $countDilatedBileDuct = $result['DilatedBileDuct'];
            $parenchymalChange = $result['ParenchymalChange'];
            $parenchymalEcho = $result['ParenchymalEcho'];
            $both = $result['Both'];
            $suspected_case = $result['suspected_case'];
            $suspected_parenchymal = $result['suspected_parenchymal'];
            $suspected_normal = $result['suspected_normal'];
            $suspected_abnormal = $result['suspected_abnormal'];
            $suspected_fatty_liver = $result['suspected_fatty_liver'];
            $suspected_pdf = $result['suspected_pdf'];
            $suspected_cirrhosis = $result['suspected_cirrhosis'];
            $dilated_bileDuct = $result['dilated_bileDuct'];
            $liver_mass = $result['liver_mass'];
            $suspected_parenchymal_change = $result['suspected_parenchymal_change'];
        }


        $hospital = isset($this->allDataUsFindingReport['initUsFinding']['hospitalCode']) ? $this->allDataUsFindingReport['initUsFinding']['hospitalCode'] : "";
        $startDate = $this->allDataUsFindingReport['initUsFinding']['startDate'];
        $endDate = $this->allDataUsFindingReport['initUsFinding']['endDate'];
        $zone = isset($this->allDataUsFindingReport['initUsFinding']['zoneCode']) ? $this->allDataUsFindingReport['initUsFinding']['zoneCode'] : "";
        $province = isset($this->allDataUsFindingReport['initUsFinding']['provinceCode']) ? $this->allDataUsFindingReport['initUsFinding']['provinceCode'] : "";
        $amphur = isset($this->allDataUsFindingReport['initUsFinding']['amphurCode']) ? $this->allDataUsFindingReport['initUsFinding']['amphurCode'] : "";

//        echo '<div class="resultOfUltrasonoGraphicFinding">';
//
//        echo '<p class="">กลุ่มส่งตรวจรักษาต่อแล้ว ' .
//        '<span style="cursor: pointer;" class="valueResultUSFinding" keystore="Refer" hospital="' . $hospital . '" startdate="' . $startDate . '" enddate="' . $endDate . '" zone="' . $zone . '" province="' . $province . '" amphur="' . $amphur . '" spanReportResultUSFinding>' .
//        '<a class="danger">' . $countRefer . '</a>' .
//        '</span>	ราย' .
//        '</p>';
//
//        echo '<p class="refer">กลุ่มสงสัยมะเร็งท่อน้ำดี และได้รับการส่งตรวจรักษาต่อ ' .
//        '<span style="cursor: pointer;" class="valueResultUSFinding" keystore="ReferSuspectedCCA" hospital="' . $hospital . '" startdate="' . $startDate . '" enddate="' . $endDate . '" zone="' . $zone . '" province="' . $province . '" amphur="' . $amphur . '" spanReportResultUSFinding>' .
//        '<a class="danger">' . number_format($countReferSuspectedCCA, 0, '.', ',') . '</a>' .
//        '</span>	ราย ' .
//        '<span class="danger">(' . number_format($percentReferSuspectedCCA, 1, '.', ',') . '%) (' . $countPerOneHundredThousand . '/100,000 ประชากร)</span>' .
//        '</p>';
//
//        echo '<p class="refer">กลุ่มได้รับการส่งตรวจรักษาต่อ จากสาเหตุอื่น ' .
//        '<span style="cursor: pointer;" class="valueResultUSFinding" keystore="ReferNoSuspectedCCA" hospital="' . $hospital . '" startdate="' . $startDate . '" enddate="' . $endDate . '" zone="' . $zone . '" province="' . $province . '" amphur="' . $amphur . '" spanReportResultUSFinding>' .
//        '<a class="danger">' . number_format($countReferNoSuspectedCCA, 0, '.', ',') . '</a>' .
//        '</span>	ราย' .
//        '</p>';
//
//        echo '<p class="">Liver Abnormal ' .
//        '<span style="cursor: pointer;" class="valueResultUSFinding" keystore="LiverMassAb" hospital="' . $hospital . '" startdate="' . $startDate . '" enddate="' . $endDate . '" zone="' . $zone . '" province="' . $province . '" amphur="' . $amphur . '" spanReportResultUSFinding>' .
//        '<a class="danger">' . number_format($countLiverMassAb, 0, '.', ',') . '</a>' .
//        '</span>	ราย' .
//        '</p>';
//
//        echo '<p class="">Cyst or Hemang or Cal' .
//        '<span style="cursor: pointer;" class="valueResultUSFinding" keystore="LiverMassx" hospital="' . $hospital . '" startdate="' . $startDate . '" enddate="' . $endDate . '" zone="' . $zone . '" province="' . $province . '" amphur="' . $amphur . '" spanReportResultUSFinding>' .
//        '<a class="danger">' . number_format($countLiverMassAb - ($countLiverMass + $countLiverMass), 0, '.', ',') . '</a>' .
//        '</span>	ราย' .
//        '</p>';
//
//        echo '<p class="">Liver Mass ' .
//        '<span style="cursor: pointer;" class="valueResultUSFinding" keystore="LiverMass" hospital="' . $hospital . '" startdate="' . $startDate . '" enddate="' . $endDate . '" zone="' . $zone . '" province="' . $province . '" amphur="' . $amphur . '" spanReportResultUSFinding>' .
//        '<a class="danger">' . number_format($countLiverMass, 0, '.', ',') . '</a>' .
//        '</span>	ราย' .
//        '</p>';
//
//        echo '<p class="">Duct dilate ' .
//        '<span style="cursor: pointer;" class="valueResultUSFinding" keystore="DilatedBileDuct" hospital="' . $hospital . '" startdate="' . $startDate . '" enddate="' . $endDate . '" zone="' . $zone . '" province="' . $province . '" amphur="' . $amphur . '" spanReportResultUSFinding>' .
//        '<a class="danger">' . number_format($countDilatedBileDuct, 0, '.', ',') . '</a>' .
//        '</span>	ราย' .
//        '</p>';
//        echo '</div>';
        
        echo $this->renderAjax('suspected-case', [
            'allDataUsFindingReport' => $this->allDataUsFindingReport,
            'parenchymalChange' => $parenchymalChange,
            'both' => $both,
            'liver_mass' => $liver_mass,
            'DilatedBileDuct' => $dilated_bileDuct,
            'parenchymalEcho' => $parenchymalEcho,
            'suspected_case' => $suspected_case,
            'suspected_parenchymal' => $suspected_parenchymal,
            'suspected_normal' => $suspected_normal,
            'suspected_abnormal' => $suspected_abnormal,
            'suspected_fatty_liver' => $suspected_fatty_liver,
            'suspected_pdf' => $suspected_pdf,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'hospital' => $hospital,
            'province' => $province,
            'zone' => $zone,
            'amphur' => $amphur,
            'suspected_cirrhosis' => $suspected_cirrhosis,
            'suspected_parenchymal_change' => $suspected_parenchymal_change,
            'refer_case' => $countRefer,
            'refer_suspected_case' => $countReferSuspectedCCA,
            'refer_other' => $countReferNoSuspectedCCA,
        ]);
    }

    public function actionGetSuspectedCase() {
        $request = \Yii::$app->request;
        $case_type = $request->post('case_type');
        $startdate = $request->post('startdate');
        $enddate = $request->post('enddate');
        $zone = $request->post('zone');
        $province = $request->post('province');
        $amphur = $request->post('amphur');
        $startDate = \backend\classes\DateForQuery::FormatDateForMysql($startdate);
        $endDate = \backend\classes\DateForQuery::FormatDateForMysql($enddate);
        $sitecode = \Yii::$app->user->identity->userProfile->sitecode;
        \appxq\sdii\utils\VarDumper::dump($request->post());
        $hospital = $request->post('hospital');
        if ($hospital == $sitecode || \Yii::$app->user->can('administrator')) {
            $sql = " SELECT tb3.`id`,tb3.hsitecode, tb3.ptcodefull, `title`,tb1.`name`,`surname`,";
        } else {
            $sql = " SELECT tb3.`id`,tb3.hsitecode, tb3.ptcodefull, '' as title,MD5(tb1.`name`) as 'name','' as surname,";
        }

        $sql .= "  tb1.hncode, tb3.f2v1
            FROM `cascapcloud`.`tb_data_3` tb3 INNER JOIN `cascapcloud`.`tb_data_1` AS tb1 ON ( `tb3`.`ptid` = `tb1`.`id` )
            INNER JOIN `cascapcloud`.`all_hospital_thai` AS all_hospital ON ( `tb3`.`hsitecode` = `all_hospital`.`hcode` ) WHERE (`tb3`.`hsitecode`=`all_hospital`.`hcode`)
            ";
        if ($case_type == 'normal') {
            $sql .= " AND tb3.f2v2a1 LIKE '%0%' ";
        } else if ($case_type == 'abnormal') {
            $sql .= " AND tb3.f2v2a1 LIKE '%1%' ";
        } else if ($case_type == 'liver-mass') {
            $sql .= " AND f2v6a3b1 LIKE '%1%' AND (`f2v2a2` NOT LIKE '%0%' "
                    . "AND (`f2v2a2b5c1` LIKE '%1%' OR `f2v2a2b6c1` LIKE '%1%' OR `f2v2a2b7c1` LIKE '%1%' )) ";
        } else if ($case_type == 'fatty-liver') {
            $sql .= "  AND  (IFNULL(tb3.f2v2a1b1,0) LIKE '%1%' OR tb3.f2v2a1b1 LIKE '%2%' OR tb3.f2v2a1b1 LIKE '%3%') ";
        } else if ($case_type == 'pdf') {
            $sql .= "  AND (tb3.f2v2a1b2 LIKE '%1%' OR tb3.f2v2a1b2 LIKE '%2%' OR tb3.f2v2a1b2 LIKE '%3%') ";
        } else if ($case_type == 'cirrhosis') {
            $sql .= "  AND tb3.f2v2a1b3 LIKE '%1%' ";
        } else if ($case_type == 'parenchymal-change') {
            $sql .= "  AND tb3.f2v2a1b4 LIKE '%1%' ";
        } else if ($case_type == 'suspected-case') {
            $sql .= " AND `f2v6a3b1` LIKE '%1%' ";
        } else if ($case_type == 'dilated-duct') {
            $sql .= " AND `f2v6a3b1` LIKE '%1%' AND (`f2v2a3b1` LIKE '%1%' OR `f2v2a3b2` LIKE '%1%' OR `f2v2a3b3` LIKE '%1%' ) ";
        } else if ($case_type == 'both') {
            $sql .= " AND tb3.f2v6a3b1='1'  AND ((IFNULL(f2v2a1b4,0)!='1' AND  (tb3.f2v2a3b1 LIKE '%1%' OR tb3.f2v2a3b2 LIKE '%1%' OR tb3.f2v2a3b3 LIKE '%1%')) AND (`f2v2a2b5c1` LIKE '%1%' OR `f2v2a2b6c1` LIKE '%1%' OR `f2v2a2b7c1` LIKE '%1%' ))";
        } else if ($case_type == 'suspected-parenchymal') {
            $sql .= " AND tb3.f2v6a3b1='1' ";
        } else if ($case_type == 'suspected-normal') {
            $sql .= " AND tb3.f2v6a3b1='1' AND tb3.f2v2a1 LIKE '%0%' ";
        } else if ($case_type == 'suspected-abnormal') {
            $sql .= " AND tb3.f2v6a3b1='1' AND tb3.f2v2a1 LIKE '%1%' ";
        } else if ($case_type == 'suspected-fatty-liver') {
            $sql .= " AND tb3.f2v6a3b1='1'  AND  ((tb3.f2v2a1b1 LIKE '%1%' OR tb3.f2v2a1b1 LIKE '%2%' OR tb3.f2v2a1b1 LIKE '%3%') AND (tb3.f2v2a3b1 LIKE '%1%' OR tb3.f2v2a3b2 LIKE '%1%' OR tb3.f2v2a3b3 LIKE '%1%')) ";
        } else if ($case_type == 'suspected-pdf') {
            $sql .= " AND tb3.f2v6a3b1='1' AND (tb3.f2v2a1b2 LIKE '%1%' OR tb3.f2v2a1b2 LIKE '%2%' OR tb3.f2v2a1b2 LIKE '%3%') ";
        } else if ($case_type == 'suspected-cirrhosis') {
            $sql .= " AND tb3.f2v6a3b1='1' AND tb3.f2v2a1b3 LIKE '%1%' ";
        } else if ($case_type == 'suspected-parenchymal-change') {
            $sql .= " AND tb3.f2v6a3b1='1' AND tb3.f2v2a1b4 LIKE '%1%' ";
        } else if ($case_type == 'refer-case') {
            $sql .= " AND tb3.f2v6a3 LIKE '%1%' ";
        } else if ($case_type == 'refer-suspected-cca') {
            $sql .= " AND tb3.f2v6a3b1 LIKE '%1%' ";
        } else if ($case_type == 'refer-other') {
            $sql .= " AND tb3.f2v6a3b1 IS NULL OR tb3.f2v6a3b1 NOT LIKE '%1%' ";
        }
        if ($hospital != '' || $hospital != null) {
            $sql .= "AND (`tb_data_3`.`hsitecode` LIKE '%$hospital%' OR `tb_data_3`.`sitecode` LIKE '%$hospital%') ";
        } else {
            if ($zone != '' || $zone != null)
                $sql .= "AND `all_hospital`.`zone_code` LIKE '%$zone%' ";
            if ($province != '' || $province != null)
                $sql .= "AND `all_hospital`.`provincecode` LIKE '%$province%' ";
            if ($amphur != '' || $amphur != null)
                $sql .= "AND `all_hospital`.`amphurcode`LIKE '%$amphur%' ";
            $sql .= "AND (`tb_data_3`.`hsitecode` LIKE '%$hospital%' OR `tb_data_3`.`sitecode` LIKE '%$hospital%') ";
        }
        $result = Yii::$app->db->createCommand($sql, [':sitecode' => $hospital])->queryAll();
        $dataProvider = $result;
        return $this->renderAjax('suspected-case-view', ['dataProvider' => $dataProvider, 'case_type' => $case_type, 'hospital' => $hospital]);
    }

    private function getUsTour() {
        $sqlUsTour = "SELECT * FROM `history_us_tour` ORDER BY `times` DESC";

        $qryUsTour = Yii::$app->dbcascap->createCommand($sqlUsTour)->queryAll();

        return $qryUsTour;
    }

    private function getUsSite() {
        $sqlUsTour = "SELECT ussite.No, ussite.dateatsite, ussite.hcode, ussite.hospitalname ";
        $sqlUsTour .= ",hospital.zone_code as zonecode ";
        $sqlUsTour .= ",hospital.provincecode as provcode ";
        $sqlUsTour .= ",concat(hospital.provincecode,hospital.amphurcode) as ampcode ";
        $sqlUsTour .= ",NOW() as edate ";
        $sqlUsTour .= "from history_us_site ussite ";
        $sqlUsTour .= "left join all_hospital_thai hospital ";
        $sqlUsTour .= "on hospital.hcode=ussite.hcode ";
        $sqlUsTour .= "order by ussite.dateatsite ";

        $qryUsTour = Yii::$app->db->createCommand($sqlUsTour)->queryAll();

        return $qryUsTour;
    }

    private function getZone() {
        $sqlZone = "SELECT zone_code, zone_name FROM `cascapcloud`.`all_hospital_thai` WHERE zone_code IS NOT NULL " .
                "GROUP BY zone_code ORDER BY zone_code";

        $qryZone = Yii::$app->db->createCommand($sqlZone)->queryAll();

        return $qryZone;
    }

    private function getProvince($zoneCode = null) {
        if (is_null($zoneCode) || $zoneCode == 0) {
            $sqlProvince = "SELECT provincecode as PROVINCE_CODE, province as PROVINCE_NAME, zone_code " .
                    "FROM `cascapcloud`.`all_hospital_thai` " .
                    "WHERE provincecode IS NOT NULL " .
                    "GROUP BY provincecode " .
                    "ORDER BY province";
        } else {
            $sqlProvince = "SELECT provincecode as PROVINCE_CODE, province as PROVINCE_NAME, zone_code " .
                    "FROM `cascapcloud`.`all_hospital_thai` " .
                    "WHERE provincecode IS NOT NULL " .
                    "AND zone_code LIKE '%$zoneCode%' " .
                    "GROUP BY PROVINCE_CODE " .
                    "ORDER BY PROVINCE_NAME ";
        }

        $qryProvince = Yii::$app->db->createCommand($sqlProvince)->queryAll();

        return $qryProvince;
    }

    private function getAmphur($provinceCode) {
        $sqlAmphur = "SELECT `code6`, provincecode as PROVINCE_CODE, province as PROVINCE_NAME, amphurcode as AMPHUR_CODE, amphur as AMPHUR_NAME, zone_code " .
                "FROM `cascapcloud`.`all_hospital_thai` " .
                "WHERE provincecode LIKE '%$provinceCode%' " .
                "AND all_hospital_thai.amphurcode NOT LIKE '' " .
                "GROUP BY amphurcode " .
                "ORDER BY amphur";

        $qryAmphur = Yii::$app->db->createCommand($sqlAmphur)->queryAll();

        return $qryAmphur;
    }

    private function getHospital($provinceCode, $amphurCode) {
        $amphurCode = substr($amphurCode, 2, 2);
        $sqlAllHospitalThai = "SELECT hcode, name, code6, provincecode, province, amphurcode, amphur, zone_code " .
                "FROM `cascapcloud`.`all_hospital_thai` " .
                "WHERE provincecode LIKE '%$provinceCode%' " .
                "AND amphurcode LIKE '%$amphurCode%' " .
                "GROUP BY hcode ORDER BY name";

        $qryAllHospitalThai = Yii::$app->db->createCommand($sqlAllHospitalThai)->queryAll();

        return $qryAllHospitalThai;
    }

    public function actionImages($id) {
        $url = "https://www1.cascap.in.th/console/viewimages.php?id=" . $id;

        $json = file_get_contents($url);

        $usimages = json_decode($json, true);
        if (count($usimages) > 0) {
            echo "<h1>Ultrasound Images</h1>";
            foreach ($usimages as $key => $img) {
                echo "<img class='img-responsive' src='{$img}'><br><br>";
            }
        }
        $sql = "select file_name from file_upload where tbid='{$id}'";
        $images = \Yii::$app->db->createCommand($sql)->queryAll();
        if (count($images) > 0) {
            echo "<h1>File upload Images</h1>";
            foreach ($images as $key => $img) {
                echo "<img class='img-responsive' src='{$img}'><br><br>";
            }
        }
    }

    public function actionSummaryReport($startDate = null, $endDate = null, $zoneCode = null, $provinceCode = null, $amphurCode = null, $hospitalCode = null) {
        return $this->renderAjax('summary-report', [
                    'hospitalCode' => $hospitalCode,
                    'doctorAll' => QuerySummary::getDoctor($startDate, $endDate, $hospitalCode, 0),
                    'genderAll' => QuerySummary::getDoctor($startDate, $endDate, $hospitalCode, 1),
                    'header' => "รายงานสรุปการตรวจอัลตราซาวน์ : " . \backend\classes\MonitorReport::getSiteName($hospitalCode),
                    'parenchymal' => QuerySummary::getCca02($startDate, $endDate, $hospitalCode, "f2v2a1", "1", ""),
                    'nonpdf' => QuerySummary::getCca02($startDate, $endDate, $hospitalCode, "f2v2a1b2", "", ""),
                    'pdf1' => QuerySummary::getCca02($startDate, $endDate, $hospitalCode, "f2v2a1b2", "1", ""),
                    'pdf2' => QuerySummary::getCca02($startDate, $endDate, $hospitalCode, "f2v2a1b2", "2", ""),
                    'pdf3' => QuerySummary::getCca02($startDate, $endDate, $hospitalCode, "f2v2a1b2", "3", ""),
                    'fattyLiverMild' => QuerySummary::getCca02($startDate, $endDate, $hospitalCode, "f2v2a1b1", "1", ""),
                    'fattyLiverModerate' => QuerySummary::getCca02($startDate, $endDate, $hospitalCode, "f2v2a1b1", "2", ""),
                    'fattyLiverSevere' => QuerySummary::getCca02($startDate, $endDate, $hospitalCode, "f2v2a1b1", "3", ""),
                    'cirrhosis' => QuerySummary::getCca02($startDate, $endDate, $hospitalCode, "f2v2a1b3", "1", ""),
                    'liverMassSingle' => QuerySummary::getCca02($startDate, $endDate, $hospitalCode, "f2v2a2", "1", ""),
                    'liverMassMultiple' => QuerySummary::getCca02($startDate, $endDate, $hospitalCode, "f2v2a2", "2", ""),
                    'gallWall' => QuerySummary::getCca02($startDate, $endDate, $hospitalCode, "f2v3a2", "1", ""),
                    'gallStone' => QuerySummary::getCca02($startDate, $endDate, $hospitalCode, "f2v3a3", "1", ""),
                    'gallPost' => QuerySummary::getCca02($startDate, $endDate, $hospitalCode, "f2v3a4", "1", ""),
                    'dilatedRight' => QuerySummary::getCca02($startDate, $endDate, $hospitalCode, "f2v2a3b1", "1", ""),
                    'dilatedLeft' => QuerySummary::getCca02($startDate, $endDate, $hospitalCode, "f2v2a3b2", "1", ""),
                    'dilatedCbd' => QuerySummary::getCca02($startDate, $endDate, $hospitalCode, "f2v2a3b3", "1", ""),
                    'ascites' => QuerySummary::getCca02($startDate, $endDate, $hospitalCode, "f2v5a1", "1", ""),
                    'splenomegaly' => QuerySummary::getCca02($startDate, $endDate, $hospitalCode, "f2v5a2", "1", ""),
                    'other' => QuerySummary::getCca02($startDate, $endDate, $hospitalCode, "f2v5a3", "1", ""),
                    'oneYear' => QuerySummary::getCca02($startDate, $endDate, $hospitalCode, "f2v6", "1", ""),
                    'sixMonth' => QuerySummary::getCca02($startDate, $endDate, $hospitalCode, "f2v6", "2", ""),
                    'send' => QuerySummary::getCca02($startDate, $endDate, $hospitalCode, "f2v6a3", "1", ""),
                    'suspectedCca' => QuerySummary::getCca02($startDate, $endDate, $hospitalCode, "f2v6a3b1", "1", ""),
                    'sendOther' => QuerySummary::getCca02($startDate, $endDate, $hospitalCode, "f2v6a3b2", "1", ""),
                    'dilatedTotal' => QuerySummary::getCca02($startDate, $endDate, $hospitalCode, "(f2v2a3b1 OR f2v2a3b2 OR f2v2a3b3)", "1", ""),
        ]);
    }

    public function actionCca01Report($startDate = null, $endDate = null, $zoneCode = null, $provinceCode = null, $amphurCode = null, $hospitalCode = null) {

        return $this->renderAjax('cca01-report', [
                    'hospitalCode' => $hospitalCode,
                    'hospitalName' => \backend\classes\MonitorReport::getSiteName($hospitalCode),
                    'startDate' => $startDate,
                    'endDate' => $endDate,
                    'total' => QuerySummary::getCca01($startDate, $endDate, $hospitalCode, 0),
                    'total_hcode' => QuerySummary::getCca01($startDate, $endDate, $hospitalCode, 1),
                    'total_male' => QuerySummary::getCca01($startDate, $endDate, $hospitalCode, 2),
                    'total_female' => QuerySummary::getCca01($startDate, $endDate, $hospitalCode, 3),
                    'total_age' => QuerySummary::getCca01Age($startDate, $endDate, $hospitalCode, '0'),
                    'total_age_hcode' => QuerySummary::getCca01Age($startDate, $endDate, $hospitalCode, '1'),
                    'total_age_male' => QuerySummary::getCca01Age($startDate, $endDate, $hospitalCode, '2'),
                    'total_age_female' => QuerySummary::getCca01Age($startDate, $endDate, $hospitalCode, '3'),
                    'total_median' => QuerySummary::getCca01Age($startDate, $endDate, "13777", '4'),
                    'total_median_hcode' => QuerySummary::getCca01Age($startDate, $endDate, $hospitalCode, 'median1'),
                    'total_median_male' => QuerySummary::getCca01Age($startDate, $endDate, $hospitalCode, 'median2'),
                    'total_median_female' => QuerySummary::getCca01Age($startDate, $endDate, $hospitalCode, 'median3'),
                    'relation' => QuerySummary::getRelationDrilldown($startDate, $endDate, null, "1,2"),
                    'relationHos' => QuerySummary::getRelationDrilldown($startDate, $endDate, $hospitalCode, "1,2"),
                    'relationMale' => QuerySummary::getRelationDrilldown($startDate, $endDate, $hospitalCode, "1"),
                    'relationFemale' => QuerySummary::getRelationDrilldown($startDate, $endDate, $hospitalCode, "2"),
                    'f1v14' => QuerySummary::getCca01Diagnose($startDate, $endDate, null, "1,2", null),
                    'f1v14hos' => QuerySummary::getCca01Diagnose($startDate, $endDate, $hospitalCode, "1,2", "number"),
                    'f1v14hosmale' => QuerySummary::getCca01Diagnose($startDate, $endDate, $hospitalCode, "1", "number"),
                    'f1v14hosfemale' => QuerySummary::getCca01Diagnose($startDate, $endDate, $hospitalCode, "2", "number"),
        ]);
    }

    public function actionSummaryDrilldown($startDate = null, $endDate = null, $hospitalcode = null, $doctorcode = null, $doctorname = null, $state = null, $gender = null, $data = null, $cca02 = null, $zonedata = null) {
        if ($state == 'gender')
            $patientDrilldown = QuerySummary::getSummaryDrilldown($startDate, $endDate, $hospitalcode, $doctorcode, $gender, $state);
        else if ($state == 'cca02')
            $patientDrilldown = QuerySummary::getCca02Drilldown($startDate, $endDate, $hospitalcode, $data, $cca02);
        else if ($state == 'cca01' && $data == 'f1v14')
            $patientDrilldown = QuerySummary::getCca01Diagnose($startDate, $endDate, $hospitalcode, $gender, "drilldown");
        else if ($state == 'cca01' && $data == 'f1v9a1')
            $patientDrilldown = QuerySummary::getRelationDrilldown($startDate, $endDate, $hospitalcode, $gender);
        else if ($state == 'cca01' && $data == 'age')
            $patientDrilldown = QuerySummary::getAgeDrilldown($startDate, $endDate, $hospitalcode, $gender, $cca02);
        else if ($state == 'cca01')
            $patientDrilldown = QuerySummary::getCca01Drilldown($startDate, $endDate, $hospitalcode, $data, $gender, $cca02);
        else if ($state == 'doctor')
            $patientDrilldown = QuerySummary::getSummaryDrilldown($startDate, $endDate, $hospitalcode, $doctorcode, $gender, $state, $zonedata);
        else if ($state == 'doctorzone') {
            $patientDrilldown = QuerySummary::getSummaryDrilldown($startDate, $endDate, $hospitalcode, $doctorcode, $gender, $state, $zonedata);
            $state = substr("doctorzone", 0, -4);
        } else if ($state == 'genderzone') {
            $patientDrilldown = QuerySummary::getSummaryDrilldown($startDate, $endDate, $hospitalcode, $doctorcode, $gender, $state, $zonedata);
            $state = substr("genderzone", 0, -4);
        } else if ($state == 'cca02zone') {
            $patientDrilldown = QuerySummary::getCca02DrilldownZone($startDate, $endDate, $zonedata, $data, $cca02);
            $state = substr("cca02zone", 0, -4);
        } else if ($state == 'cca01zone') {
            $patientDrilldown = QuerySummary::getCca01DrilldownZone($startDate, $endDate, $zonedata, $data, $gender, $cca02);
            $state = substr($state, 0, -4);
        }
        $provider = new \yii\data\ArrayDataProvider([
            'allModels' => $patientDrilldown,
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => [
                'attributes' => ['hptcode', 'f2v1'],
            ],
        ]);
        return $this->renderAjax('summary-drilldown', [
                    'provider' => $provider,
                    'doctorname' => $doctorname,
                    'state' => $state,
                    'cca02List' => $cca02List,
                    'cca01List' => $cca01List,
                    'data' => $data,
        ]);
    }

    public function actionDoctorZone($startDate = null, $endDate = null, $zoneCode = null, $provinceCode = null, $amphurCode = null, $summaryZone = null) {
        $zone = "$zoneCode,$provinceCode,$amphurCode";
        //\appxq\sdii\utils\VarDumper::dump($aa);
        if ($provinceCode != null && $amphurCode != null)
            $header = "รายงานสรุปการตรวจอัลตราซาวน์อำเภอ : " . QuerySummary::getZoneName(null, $provinceCode, $amphurCode)[amphur];
        else if ($provinceCode != null && $amphurCode == null)
            $header = "รายงานสรุปการตรวจอัลตราซาวน์จังหวัด : " . QuerySummary::getZoneName(null, $provinceCode, null)[province];
        else
            $header = "รายงานสรุปการตรวจอัลตราซาวน์เขต " . $zoneCode . " : " . QuerySummary::getZoneName($zoneCode, null, null)[zone_name];
        return $this->renderAjax('summary-report', [
                    'doctorAll' => QuerySummary::getDoctorZone($startDate, $endDate, $zoneCode, $provinceCode, $amphurCode, 0),
                    'genderAll' => QuerySummary::getDoctorZone($startDate, $endDate, $zoneCode, $provinceCode, $amphurCode, 1),
                    'header' => $header,
                    'summaryZone' => $summaryZone,
                    'zone' => $zone,
                    'parenchymal' => QuerySummary::getCca02Zone($startDate, $endDate, $zoneCode, $provinceCode, $amphurCode, "f2v2a1", "1"),
                    'nonpdf' => QuerySummary::getCca02Zone($startDate, $endDate, $zoneCode, $provinceCode, $amphurCode, "f2v2a1b2", ""),
                    'pdf1' => QuerySummary::getCca02Zone($startDate, $endDate, $zoneCode, $provinceCode, $amphurCode, "f2v2a1b2", "1"),
                    'pdf2' => QuerySummary::getCca02Zone($startDate, $endDate, $zoneCode, $provinceCode, $amphurCode, "f2v2a1b2", "2"),
                    'pdf3' => QuerySummary::getCca02Zone($startDate, $endDate, $zoneCode, $provinceCode, $amphurCode, "f2v2a1b2", "3"),
                    'fattyLiverMild' => QuerySummary::getCca02Zone($startDate, $endDate, $zoneCode, $provinceCode, $amphurCode, "f2v2a1b1", "1"),
                    'fattyLiverModerate' => QuerySummary::getCca02Zone($startDate, $endDate, $zoneCode, $provinceCode, $amphurCode, "f2v2a1b1", "2"),
                    'fattyLiverSevere' => QuerySummary::getCca02Zone($startDate, $endDate, $zoneCode, $provinceCode, $amphurCode, "f2v2a1b1", "3"),
                    'cirrhosis' => QuerySummary::getCca02Zone($startDate, $endDate, $zoneCode, $provinceCode, $amphurCode, "f2v2a1b3", "1"),
                    'liverMassSingle' => QuerySummary::getCca02Zone($startDate, $endDate, $zoneCode, $provinceCode, $amphurCode, "f2v2a2", "1"),
                    'liverMassMultiple' => QuerySummary::getCca02Zone($startDate, $endDate, $zoneCode, $provinceCode, $amphurCode, "f2v2a2", "2"),
                    'gallWall' => QuerySummary::getCca02Zone($startDate, $endDate, $zoneCode, $provinceCode, $amphurCode, "f2v3a2", "1"),
                    'gallStone' => QuerySummary::getCca02Zone($startDate, $endDate, $zoneCode, $provinceCode, $amphurCode, "f2v3a3", "1"),
                    'gallPost' => QuerySummary::getCca02Zone($startDate, $endDate, $zoneCode, $provinceCode, $amphurCode, "f2v3a4", "1"),
                    'dilatedRight' => QuerySummary::getCca02Zone($startDate, $endDate, $zoneCode, $provinceCode, $amphurCode, "f2v2a3b1", "1"),
                    'dilatedLeft' => QuerySummary::getCca02Zone($startDate, $endDate, $zoneCode, $provinceCode, $amphurCode, "f2v2a3b2", "1"),
                    'dilatedCbd' => QuerySummary::getCca02Zone($startDate, $endDate, $zoneCode, $provinceCode, $amphurCode, "f2v2a3b3", "1"),
                    'ascites' => QuerySummary::getCca02Zone($startDate, $endDate, $zoneCode, $provinceCode, $amphurCode, "f2v5a1", "1"),
                    'splenomegaly' => QuerySummary::getCca02Zone($startDate, $endDate, $zoneCode, $provinceCode, $amphurCode, "f2v5a2", "1"),
                    'other' => QuerySummary::getCca02Zone($startDate, $endDate, $zoneCode, $provinceCode, $amphurCode, "f2v5a3", "1"),
                    'oneYear' => QuerySummary::getCca02Zone($startDate, $endDate, $zoneCode, $provinceCode, $amphurCode, "f2v6", "1"),
                    'sixMonth' => QuerySummary::getCca02Zone($startDate, $endDate, $zoneCode, $provinceCode, $amphurCode, "f2v6", "2"),
                    'send' => QuerySummary::getCca02Zone($startDate, $endDate, $zoneCode, $provinceCode, $amphurCode, "f2v6a3", "1"),
                    'suspectedCca' => QuerySummary::getCca02Zone($startDate, $endDate, $zoneCode, $provinceCode, $amphurCode, "f2v6a3b1", "1"),
                    'sendOther' => QuerySummary::getCca02Zone($startDate, $endDate, $zoneCode, $provinceCode, $amphurCode, "f2v6a3b2", "1"),
                    'dilatedTotal' => QuerySummary::getCca02Zone($startDate, $endDate, $zoneCode, $provinceCode, $amphurCode, "(f2v2a3b1 OR f2v2a3b2 OR f2v2a3b3)", "1"),
        ]);
    }

    public function actionCca01Zone($startDate = null, $endDate = null, $zoneCode = null, $provinceCode = null, $amphurCode = null, $summaryZone = null) {
        $zone = "$zoneCode,$provinceCode,$amphurCode";
        // \appxq\sdii\utils\VarDumper::dump($zoneCode);
        if ($provinceCode != null && $amphurCode != null) {
            $hospitalName = "อำเภอ" . QuerySummary::getZoneName(null, $provinceCode, $amphurCode)[amphur];
            $state = "amphur";
        } else if ($provinceCode != null && $amphurCode == null) {
            $hospitalName = "จังหวัด" . QuerySummary::getZoneName(null, $provinceCode, null)[province];
            $state = "province";
        } else {
            $hospitalName = "เขต " . $zoneCode . " : " . QuerySummary::getZoneName($zoneCode, null, null)[zone_name];
            $state = "zone";
        }

        return $this->renderAjax('cca01-report', [
                    'hospitalName' => $hospitalName,
                    'summaryZone' => $summaryZone,
                    'zone' => $zone,
                    'total' => QuerySummary::getCca01($startDate, $endDate, "13777", 0),
                    'total_hcode' => QuerySummary::getCca01Zone($startDate, $endDate, $zoneCode, $provinceCode, $amphurCode, "1,2", null),
                    'total_male' => QuerySummary::getCca01Zone($startDate, $endDate, $zoneCode, $provinceCode, $amphurCode, "1", null),
                    'total_female' => QuerySummary::getCca01Zone($startDate, $endDate, $zoneCode, $provinceCode, $amphurCode, "2", null),
                    'total_age' => QuerySummary::getCca01Age($startDate, $endDate, "13777", '0'),
                    'total_age_hcode' => QuerySummary::getCca01Zone($startDate, $endDate, $zoneCode, $provinceCode, $amphurCode, "1,2", "age"),
                    'total_age_male' => QuerySummary::getCca01Zone($startDate, $endDate, $zoneCode, $provinceCode, $amphurCode, "1", "age"),
                    'total_age_female' => QuerySummary::getCca01Zone($startDate, $endDate, $zoneCode, $provinceCode, $amphurCode, "2", "age"),
                    'total_median' => QuerySummary::getCca01Age($startDate, $endDate, "13777", '4'),
                    'total_median_hcode' => QuerySummary::getCca01Zone($startDate, $endDate, $zoneCode, $provinceCode, $amphurCode, "1,2", "median"),
                    'total_median_male' => QuerySummary::getCca01Zone($startDate, $endDate, $zoneCode, $provinceCode, $amphurCode, "1", "median"),
                    'total_median_female' => QuerySummary::getCca01Zone($startDate, $endDate, $zoneCode, $provinceCode, $amphurCode, "2", "median"),
                    'relation' => QuerySummary::getRelationDrilldown($startDate, $endDate, null, "1,2"),
                    'relationHos' => QuerySummary::getCca01Zone($startDate, $endDate, $zoneCode, $provinceCode, $amphurCode, "1,2", "relation"),
                    'relationMale' => QuerySummary::getCca01Zone($startDate, $endDate, $zoneCode, $provinceCode, $amphurCode, "1", "relation"),
                    'relationFemale' => QuerySummary::getCca01Zone($startDate, $endDate, $zoneCode, $provinceCode, $amphurCode, "2", "relation"),
                    'f1v14' => QuerySummary::getCca01Diagnose($startDate, $endDate, null, "1,2", null),
                    'f1v14hos' => QuerySummary::getCca01Zone($startDate, $endDate, $zoneCode, $provinceCode, $amphurCode, "1,2", "diagnose"),
                    'f1v14hosmale' => QuerySummary::getCca01Zone($startDate, $endDate, $zoneCode, $provinceCode, $amphurCode, "1", "diagnose"),
                    'f1v14hosfemale' => QuerySummary::getCca01Zone($startDate, $endDate, $zoneCode, $provinceCode, $amphurCode, "2", "diagnose"),
        ]);
    }

    public function actionFinduser($search = null) {
        $dataUser = \Yii::$app->db->createCommand("SELECT * FROM user_profile WHERE CONCAT(firstname,' ', lastname) LIKE '%$search%' OR user_id LIKE '%$search%'");
        $dataUser = $dataUser->queryAll();

        unset($out);
        foreach ($dataUser as $value) {
            $out["results"][] = ['id' => $value['user_id'], 'text' => $value["firstname"] . ' ' . $value["lastname"]];
        }
        if (!$dataUser) {
            $out = ['results' => []];
        }
        return json_encode($out);
    }

}
