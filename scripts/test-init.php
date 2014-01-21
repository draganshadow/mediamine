<?php
echo exec('php create-db.php') . PHP_EOL;
system('php ../public/index.php install') . PHP_EOL;
system('php ../public/index.php scan /test/videos/') . PHP_EOL;
system('php ../public/index.php searchSeries') . PHP_EOL;
system('php ../public/index.php searchMovies') . PHP_EOL;
