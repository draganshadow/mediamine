<?php
echo exec('php create-db.php') . PHP_EOL;
system('php ../public/index.php install') . PHP_EOL;
//system('php ../public/index.php scan /opt/data/03\ -\ SERIES\ TV/') . PHP_EOL;
//system('php ../public/index.php searchSeries') . PHP_EOL;
system('php ../public/index.php scan /opt/data/02\ -\ FILMS/MCM_DIVX_OK/') . PHP_EOL;
system('php ../public/index.php searchMovies') . PHP_EOL;
