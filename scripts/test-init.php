<?php
echo exec('php create-db.php') . PHP_EOL;
system('php ../public/index.php install') . PHP_EOL;
system('php ../public/index.php scan /vagrant/data/test') . PHP_EOL;
system('php ../public/index.php searchSeries') . PHP_EOL;