<?php

echo backend\modules\ezforms2\classes\TmfWidget::ui()
                    ->target($target)
                    ->ezf_id('1518753299024918000')
                    ->reloadDiv('test')
                    ->pageSize($options['page_size'])
                    ->buildGrid();